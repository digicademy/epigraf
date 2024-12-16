<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Table;

use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;
use ArrayObject;
use Cake\Collection\CollectionInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\I18n\I18n;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use Cake\Database\Schema\TableSchemaInterface;

/**
 * Items Model
 *
 * # Relations
 * @property ArticlesTable $Articles
 * @property SectionsTable $Sections
 * @property LinksTable $Links
 * @property PropertiesTable $Properties
 */
class ItemsTable extends BaseTable
{

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'itemtype';

    /**
     * Initialize hook
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('items');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->setEntityClass('Epi.Item');
        $this->addBehavior('Epi.XmlStyles', [
            'fields' => [
                'content',
                'translation',
                'file_copyright',
                'date_add',
                'source_from',
                'source_addition',
                'value'
            ]
        ]);

        $this->belongsTo(
            'Properties',
            [
                'foreignKey' => 'properties_id',
                'className' => 'Epi.Properties',
                'propertyName' => 'property'
            ]
        );

        $this->belongsTo(
            'PropertiesWithAncestors',
            [
                'foreignKey' => 'properties_id',
                'className' => 'Epi.Properties',
                'propertyName' => 'property',
                'finder' => 'containAncestors'
            ]
        );

        $this->belongsTo(
            'Articles',
            [
                'foreignKey' => 'articles_id',
                'className' => 'Epi.Articles',
                'propertyName' => 'article'
            ]
        );

        $this->belongsTo(
            'Sections',
            [
                'foreignKey' => 'sections_id',
                'className' => 'Epi.Sections',
                'propertyName' => 'section'
            ]
        );

        $this->belongsTo(
            'Files',
            [
                'foreignKey' => ['file_name'],
                'bindingKey' => ['name'],
                'className' => 'Epi.Files',
                'propertyName' => 'file',
                'joinType' => Query::JOIN_TYPE_LEFT,
                'conditions' => ['Files.path = CONCAT("articles/", Items.file_path)']
            ]
        );

        $this->belongsTo(
            'LinkedArticle',
            [
                'foreignKey' => 'links_id',
                'conditions' => ['Items.links_tab' => 'articles'],
                'className' => 'Epi.Articles',
                'propertyName' => 'links_article'
            ]
        );

        $this->belongsTo(
            'LinkedSection',
            [
                'foreignKey' => 'links_id',
                'conditions' => ['Items.links_tab' => 'sections'],
                'className' => 'Epi.Sections',
                'propertyName' => 'links_section'
            ]
        );


        // linked by links table
        $this->hasMany(
            'ToLinks',
            [
                'className' => 'Epi.Links',
                'foreignKey' => 'to_id',
                'conditions' => ['Links.to_tab' => 'items']
            ]
        );

        // linked by links table
        $this->hasMany(
            'FromLinks',
            [
                'className' => 'Epi.Links',
                'foreignKey' => 'from_id',
                'conditions' => ['FromLinks.from_tab' => 'items'],
                'dependent' => true,
                'cascadeCallbacks' => true
            ]
        );

        $this->hasMany('Footnotes', [
            'className' => 'Epi.Footnotes',
            'foreignKey' => 'from_id',
            'conditions' => ['Footnotes.from_tab' => 'items', 'Footnotes.deleted' => 0],
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        $this->belongsTo(
            'Types',
            [
                'className' => 'Epi.Types',
                'strategy' => BelongsTo::STRATEGY_SELECT,
                'joinType' => Query::JOIN_TYPE_LEFT,
                'foreignKey' => 'itemtype',
                'bindingKey' => 'name',
                'conditions' => ['Types.scope' => 'items', 'Types.mode' => 'default', 'Types.preset' => 'default']
            ]
        );
    }

    /**
     * Returns the schema table object describing this table's properties.
     *
     * @return \Cake\Database\Schema\TableSchemaInterface
     */
    public function getSchema(): TableSchemaInterface
    {
        $schema = parent::getSchema();
        $schema->setColumnType('file_online', 'negbool');
        return $schema;
    }

    /**
     * Default validation rules
     *
     * @param Validator $validator Validator instance
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            //->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('deleted')
            ->allowEmptyString('deleted');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        $validator
            ->scalar('itemtype')
            ->maxLength('itemtype', 100)
            ->allowEmptyString('itemtype');

        $validator
            ->scalar('norm_iri')
            ->maxLength('norm_iri', 500)
            ->add('norm_iri', 'validFormat', [
                'rule' => ['custom', '/^[a-z0-9_~-]+$/'],
                'message' => 'Only lowercase alphanumeric characters, underscore, hyphen and tilde are allowed.'
            ])
            ->allowEmptyString('norm_iri');

        $validator
            ->maxLength('value', 1500, null, fn($context) => is_string($context['data']['value']))
            ->allowEmptyString('value');

        $validator
            ->scalar('content')
            ->allowEmptyString('content');

        $validator
            ->integer('sortno')
            ->allowEmptyString('sortno');

        $validator
            ->scalar('link_tab')
            ->maxLength('link_tab', 500)
            ->allowEmptyString('link_tab');

        $validator
            ->scalar('link_field')
            ->maxLength('link_field', 500)
            ->allowEmptyString('link_field');

        $validator
            ->scalar('link_tagid')
            ->maxLength('link_tagid', 500)
            ->allowEmptyString('link_tagid');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating application integrity
     *
     * @param RulesChecker $rules
     *
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['properties_id'], 'Properties'));
        $rules->add($rules->existsIn(['articles_id'], 'Articles'));
        $rules->add($rules->existsIn(['sections_id'], 'Sections'));

        return $rules;
    }


    /**
     * Prepare a new property if requested & merge JSON data
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void
     */
    public function afterMarshal(
        EventInterface $event,
        EntityInterface $entity,
        ArrayObject $data,
        ArrayObject $options
    ) {
        if (empty($entity->properties_id) && !empty($data['newproperty']['name'])) {
            $property = $data['newproperty'];
            $property['lemma'] = $property['lemma'] ?? $property['name'];
            $property['sortkey'] = $property['sortkey'] ?? $property['name'];

            $entity->newproperty = $this->Properties->newEntity($property);
        }

        // Merge JSON arrays
        foreach (($entity->type->merged['fields'] ?? []) as $field => $config) {
            if (($config['format'] ?? '') === 'json') {
                $entity->mergeJson($field, $data, false);
            }
        }
//        $entity->mergeJson('value', $data, false);

        parent::afterMarshal($event, $entity, $data, $options);
    }

    /**
     * Set the articles ID
     *
     * @param EventInterface $event
     * @param $entity
     * @param $options
     */
    public function beforeSave(EventInterface $event, $entity, $options)
    {
        if ($entity->root) {
            $entity->articles_id = $entity->root->id;
        }

        // Split file name into path, name and extension.
        if ($entity->file_name) {
            $path = pathinfo($entity->file_name);
            $path['dirname'] = $path['dirname'] === '.' ? '' : $path['dirname'];

            $entity['file_path'] = Files::prependPath($entity['file_path'] ?? '', $path['dirname']);
            $entity['file_name'] = $path['basename'];
            $entity['file_type'] = $path['extension'];
        }
        // Clear path
        else {
            $entity['file_name'] = '';
            $entity['file_path'] = '';
            $entity['file_type'] = null;
        }

        // Update date
        $entity->updateDate();

        // Create new property
        if (!empty($entity->newproperty)) {
            $property = $entity->newproperty;
            $this->Properties->save($property);
            $entity->property = $property;
            $entity->properties_id = $property->id;
        }
    }

    /**
     * Get pagination parameters
     *
     * @param array $params Parsed request parameters
     * @param array $columns
     * @return array
     */
    public function getPaginationParams(array $params = [], array $columns = [])
    {
        $pagination = parent::getPaginationParams($params, $columns);

        $byDistance = ($params['sort'] ?? '') === 'distance';

        // TODO: Why update default order?
        if ($byDistance) {
            $defaultOrder = ['distance' => 'ASC'];
        }

        else {
            $defaultOrder = [
                FIELD_ARTICLES_SIGNATURE => 'ASC'
            ];
        }

        $limit = 1000;

        return [
                'order' => $defaultOrder,
                'sortableFields' => $this->getSortableFields($columns),
                'limit' => $limit,
                'maxLimit' => 1000
            ] + $pagination;
    }

    /**
     * Get columns to be rendered in table views
     *
     * @param array $selected The selected columns
     * @param array $default The default columns
     * @param string|null $type Filter by type
     *
     * @return array
     */
    public function getColumns($selected = [], $default = [], $type = null)
    {
        $default = [
            'id' => ['caption' => 'item.id', 'default' => true],
            'version_id' => ['caption' => 'item.version_id', 'default' => true],
            'deleted' => ['caption' => 'item.deleted', 'default' => true],
            'itemtype' => ['caption' => 'item.itemtype', 'default' => true],
            'sortno' => ['caption' => 'item.sortno', 'default' => true],
            'value' => ['caption' => 'item.value', 'default' => true],
            'content' => ['caption' => 'item.content', 'default' => true],

            'property.id',
            'property.propertytype',
            'property.iri_path',
            'property.lemma',

            'article.project.id',
            'article.project.projecttype',
            'article.project.iri_path',
            'article.project.name',
            'article.project.signature',

            'article.id',
            'article.articletype',
            'article.iri_path',
            'article.signature',
            'article.name',

            'section.id',
            'section.sectiontype',
            'section.iri_path'
        ];

        return parent::getColumns($selected, $default, $type);
    }


    /**
     * Constructs a database query from request parameters
     *
     * @param Query $query
     * @param $options
     * @return Query
     */
    public function findHasArticleParams(Query $query, array $options): Query
    {
        // Merge empty default parameters
        $default = [
            'projects' => '',
            'articletypes' => '',
            'sectiontypes' => '',
            'itemtypes' => '',
            'propertytypes' => '',
            'quality' => '',
            'published' => null,
        ];
        $params = array_merge($default, $options);

        // Generate query
        $query = $query->find('hasArticleType', ['articletypes' => $params['articletypes']]);
        $query = $query->find('hasSectionType', ['sectiontypes' => $params['sectiontypes']]);
        $query = $query->find('hasItemType', ['itemtypes' => $params['itemtypes'], 'quality' => $params['quality']]);
        $query = $query->find('hasPropertyType', ['propertytypes' => $params['propertytypes']]);

        $articles_query = $this->Articles->find('hasParams', $params)->select('Articles.id');
        $query = $query->where(['Items.articles_id IN' => $articles_query]);

        return $query;
    }

    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasProject(Query $query, array $options)
    {
        $projects = Attributes::commaListToIntegerArray($options['projects'] ?? []);

        if (!empty($projects)) {
            $query = $query
                ->where([
                    'Projects.id IN' => $projects,
                ]);
        }

        return $query;
    }

    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasArticleType(Query $query, array $options)
    {
        $types = Attributes::commaListToStringArray($options['articletypes'] ?? []);
        if (!empty($types)) {
            $query = $query
                ->where([
                    'Articles.articletype IN' => $types,
                ]);
        }

        return $query;
    }

    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasSectionType(Query $query, array $options)
    {
        $types = Attributes::commaListToStringArray($options['sectiontypes'] ?? []);
        if (!empty($types)) {
            $query = $query
                ->contain('Sections')
                ->where([
                    'Sections.sectiontype IN' => $types,
                ]);
        }

        return $query;
    }


    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasItemType(Query $query, array $options)
    {
        $types = Attributes::commaListToStringArray($options['itemtypes'] ?? []);
        if (!empty($types)) {
            $query = $query
                ->where([
                    'Items.itemtype IN' => $types,
                ]);
        }

        if (!empty($options['quality'])) {
            $query = $query
                ->where([
                    'CAST(JSON_VALUE(Items.`value`,\'$.val\') AS INTEGER) >= ' . $options['quality'],
                ]);
        }

        return $query;
    }


    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasPropertyType(Query $query, array $options)
    {
        $types = Attributes::commaListToStringArray($options['propertytypes'] ?? []);
        if (!empty($types)) {
            $query = $query
                ->where([
                    'Properties.propertytype IN' => $types,
                ]);
        }

        return $query;
    }

    /**
     * Query data from sections and items for aggregated fields
     *
     *  // TODO: only contain necessary data, based on fields or snippets parameter
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findContainFields(Query $query, array $options)
    {

        $contain = [
            'Properties'
        ];

        $snippets = $options['snippets'] ?? [];
        if (in_array('section', $snippets)) {
            $contain[] = 'Sections';
        }

        if (in_array('project', $snippets)) {
            $contain[] = 'Articles';
            $contain[] = 'Articles.Projects';
        }

        elseif (in_array('article', $snippets)) {
            $contain[] = 'Articles';
        }

        $query = $query->contain($contain);

        return $query;
    }

    /**
     * Find the search indexes in the value field of items with itemtype text
     *
     * @param Query $query
     * @param array $options
     * @return Query A query object with results as a key value list
     */
    public function findSearchIndexes(Query $query, array $options)
    {

        return $query->find('list', ['keyField' => 'value', 'valueField' => 'value'])
            ->distinct()
            ->where(['itemtype' => 'search', 'value IS NOT' => null, 'value <>' => ''])
            ->cache('searchIndexes', $this->initResultCache())
            ->formatResults(function ($results) {
                $results = $results->toArray();
                $results = Arrays::array_add_prefix($results, 'text.', true);
                $results = array_map(
                    fn($row) => '- ' . I18n::getTranslator()->translate(Inflector::humanize($row)),
                    $results
                );
                return $results;
            });
    }

    /**
     * Get list of items in an article,
     * flattens article, section, and property data
     * ...ready for tabular serialization
     *
     * TODO: implement snippets parameter
     *
     * @param Query $query
     * @param array $options
     */
    public function findTable(Query $query, array $options)
    {
        return $query
            ->contain(['Sections', 'Articles', 'Properties'])
            ->formatResults(
                function (CollectionInterface $results) use (&$query, $options) {
                    return $results->map(
                        function ($row) use (&$options) {

                            // Flatten section data
                            if ($row->section) {
                                foreach ($row->section->_serialize_fields as $field) {
                                    $row['section_' . $field] = $row->section[$field];
                                    $row->_serialize_fields[] = 'section_' . $field;
                                }
                                unset($row->section);
                            }

                            // Flatten article data
                            if ($row->article) {
                                foreach ($row->article->_serialize_fields as $field) {
                                    $row['article_' . $field] = $row->article[$field];
                                    $row->_serialize_fields[] = 'article_' . $field;
                                }
                                unset($row->article);
                            }

                            // Flatten property data
                            if ($row->property) {
                                foreach ($row->property->_serialize_fields as $field) {
                                    $row['property_' . $field] = $row->property[$field];
                                    $row->_serialize_fields[] = 'property_' . $field;
                                }

                                unset($row->property);
                            }

                            return $row;
                        }
                    );
                }
            );
    }


    /**
     * Checks the number of images from the items-table available in the fields-table.
     *
     * As long as not implemented yet: Needs database query to set file type prior to execution:
     * UPDATE items SET items.file_type = right(file_name, 3) WHERE items.file_name <> '' AND items.file_name IS NOT null
     *
     * @return array|Query $files Contains the following fields:
     *                      itemtype, datatype, online, whether desired datatype,
     *                      number of available items, number of missing items
     */
    public function findCompleteness()
    {
        $query = $this->find();

        $files = $query
            ->select([
                'itemtype',
                'file_type',
                'file_online',
                'n_missing' => $query->expr('SUM(case when Files.name IS NULL THEN 1 ELSE 0 END)'),
                'n_available' => $query->func()->count('Files.name'),
                'wanted' => $query->expr("case when Items.file_type NOT IN ('cr2', 'crw', 'mrw', 'nef', 'xmp', 'thm') THEN 1 ELSE 0 END")
            ])
            ->contain(['Files'])
            ->where([
                'Items.file_name IS NOT' => null,
                'file_name <>' => ''
            ])
            ->group(['itemtype', 'file_type', 'file_online'])
            ->order([
                'file_online' => 'ASC',
                'wanted' => 'DESC',
                'n_missing' => 'DESC'
            ]);

        return $files;
    }


    /**
     * Get list of missing items (see function checkCompleteness)
     * @return Query $query for further processing (to iterate over object to create list)
     */
    public function findIncomplete(Query $query, array $options)
    {

        $conditions = [
            'file_name IS NOT' => null,
            'file_name <>' => ''
        ];

        if (isset($options['online'])) {
            $conditions[] = ['file_online' => $options['online']];
        }

        if (isset($options['type'])) {
            $conditions[] = ['file_type' => $options['type']];
        }

        if (isset($options['missing'])) {
            if ($options['missing']) {
                $conditions[] = ['Files.id IS' => null];
            }
            else {
                $conditions[] = ['Files.id IS NOT' => null];
            }
        }

        return $query
            ->contain(['Articles', 'Articles.Projects', 'Files'])
            ->where($conditions);
    }


}
