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
use App\Utilities\Converters\Objects;
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
use Epi\Model\Entity\Group;
use Epi\Model\Entity\Item;

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
     * Whether to check field types after marshalling and merge JSON data
     *
     * @var bool
     */
    public $mergeJson = true;

    /**
     * Request parameter config
     *
     * @var string[]
     */
    public $parameters = [
        'id' => 'list-integer',
        'itemtypes' => 'list',
        'propertytypes' => 'list',
        'published' => 'list-integer',
        'template' => 'raw',
        'columns' => 'list-or-false',
        'tile' => 'string',
        'zoom' => 'integer',
        'articles' => [
            'projects' => 'list',
            'articletypes' => 'list',
            'field' => 'string',
            'term' => 'string',
            'properties' => 'merge',
            'published' => 'list-integer'
        ],
        'properties' => 'nested-list',
        'images' => 'list-or-false'
    ];

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

        $this->hasMany(
            'ArticleItems',
            [
                'className' => 'Epi.Items',
                'foreignKey' => 'articles_id',
                'bindingKey' => 'articles_id'
            ]
        );

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

        parent::afterMarshal($event, $entity, $data, $options);
    }

    /**
     * Set the articles ID
     *
     * @param EventInterface $event
     * @param Item $entity
     * @param array $options
     */
    public function beforeSave(EventInterface $event, $entity, $options = [])
    {
        if ($entity->root) {
            $entity->articles_id = $entity->root->id;
        }

        // Split file name into path, name and extension.
        $entity->updateFile(true);

        // Update date
        $entity->updateDate(true);

        // Create new property
        if (!empty($entity->newproperty)) {
            $property = $entity->newproperty;
            $this->Properties->save($property);
            $entity->property = $property;
            $entity->properties_id = $property->id;
        }
    }

    /**
     * Extract search parameters from query parameters
     *
     * @param array $requestParameters
     * @param string $requestPath
     * @param string $requestAction
     * @return array
     */
    public function parseRequestParameters(array $requestParameters = [], $requestPath = '', $requestAction = ''): array
    {
        $params = Attributes::parseQueryParams($requestParameters, $this->parameters, 'items');
        return $params;
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

        // TODO: Why update default order?
        $byDistance = ($params['sort'] ?? '') === 'distance';
        if ($byDistance) {
            $defaultOrder = ['distance' => 'ASC'];
        } else {
            $defaultOrder = [FIELD_ARTICLES_SIGNATURE => 'ASC'];
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
     *  ### Options
     *  - type (string) Filter by type
     *  - join (boolean) Join the columns to the query
     *
     * @param array $selected The selected columns
     * @param array $default The default columns
     * @param array $options
     *
     * @return array
     */
    public function getColumns($selected = [], $default = [], $options = [])
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

        return parent::getColumns($selected, $default, $options);
    }


    /**
     * Set the article as item root
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findPrepareRoot(Query $query, array $options)
    {
        if ($query->isHydrationEnabled()) {
            $query = $query->formatResults(
                function (CollectionInterface $results) use (&$query) {
                    return $results->map(
                        function ($row) {
                            return $row->prepareRoot($row->article, $row->article, true);
                        }
                    );
                }
            );
        }
        return $query;
    }

    /**
     * Constructs a database query from request parameters
     *
     * @param Query $query
     * @param $options
     * @return Query
     */
    public function findHasParams(Query $query, array $options): Query
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

        $query = $query->find('hasArticleOptions', $params);

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
     * Find items that are contained in matching articles
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasArticleOptions(Query $query, array $options)
    {
        unset($options['articletypes']);
        unset($options['sectiontypes']);
        unset($options['itemtypes']);
        unset($options['propertytypes']);

        // TODO: Why here? Ist this solved by merging the options below?
        if (empty($options['articles']['term'])) {
            unset($options['articles']['field']);
        }

        if (!empty($options)) {
            $options = array_merge($options, $options['articles'] ?? []);
            unset($options['articles']);
            $articles_query = $this->Articles->find('hasParams', $options)->select('Articles.id');
            $query = $query->where(['Items.articles_id IN' => $articles_query]);
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
     * Restrict results to a property type
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasPropertyType(Query $query, array $options)
    {
        $types = Attributes::commaListToStringArray($options['propertytypes'] ?? []);
        if (!empty($types)) {
            $query = $query
                ->contain('Properties')
                ->where([
                    'Properties.propertytype IN' => $types,
                ]);
        }

        return $query;
    }

    /**
     * Contain data necessary for table columns
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findContainColumns(Query $query, array $options)
    {
        $contain = [];
        $snippets = $options['snippets'] ?? [];

        if (in_array('section', $snippets)) {
            $contain[] = 'Sections';
        }

        if (in_array('project', $snippets)) {
            $contain[] = 'Articles';
            $contain[] = 'Articles.Projects';
        }

        if (in_array('article', $snippets)) {
            $contain[] = 'Articles';
        }

        if (in_array('properties', $snippets)) {
            $contain[] = 'Properties';
            $contain[] = 'Articles';

            $contain['Articles.Items'] =  function ($q) use ($options) {
                return $q
                    ->select(['id', 'articles_id', 'properties_id'])
                    ->where(['Items.properties_id IS NOT' => null]);
            };

            $contain['Articles.Links'] =  function ($q) use ($options) {
                return $q
                    ->select(['id', 'root_id', 'root_tab', 'to_id', 'to_tab'])
                    ->where(['Links.to_id IS NOT' => null, 'Links.to_tab' => 'properties']);
            };

//            $contain[] = 'Articles.Items';
//            $contain[] = 'Articles.Links';
        }

        // Remove duplicates
        $seen = [];
        $contain = array_filter($contain, function ($item) use (&$seen) {
            return !is_string($item) || !in_array($item, $seen, true) && $seen[] = $item;
        });
        //$contain = array_unique($contain);

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
            ->find('cached', ['cachekey' => 'searchIndexes'])
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
     * Get a list of search indexes from the types configuration
     *
     * @return string[] Labels indexed by search keys
     */
    public function getSearchIndexes()
    {
        $types = $this->getDatabase()->types[$this->getTable()] ?? [];
        $indexKeys = [];
        foreach ($types as $typeName => $typeData) {
            if (!empty($typeData['merged']['fulltext'])) {
                $indexKeys = array_merge(
                    $indexKeys,
                    array_map(fn($x) => is_array($x) ? ($x['index'] ?? __('Content')) : $x ,Objects::extract($typeData, 'merged.fields.*.fulltext') ?? [])
                );
            }
        }

        $indexValues = [];
        foreach ($indexKeys as $indexValue) {
            $indexKey = 'text.' . $indexValue;
            if (!isset($indexValues[$indexKey])) {
                $indexValues[$indexKey] = '- ' . I18n::getTranslator()->translate(Inflector::humanize($indexValue));
            }
        }

        return $indexValues;
    }

    /**
     * Query for aggregated timeline data
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findTimeline(Query $query, array $options)
    {
        $z = isset($options['zoom']) ? (int)$options['zoom'] : 100;
        $bucketExpr = sprintf("FLOOR(Items.date_start / %d) * %d", $z, $z);

        // Get the selected properties for stacked timelines
        $propertyGroups = array_filter($options['properties'] ?? [], fn($x) => in_array('grp', $x['flags'] ?? []));
        $propertyGroups = array_values(array_map(fn($x) => $x['selected'] ?? [], $propertyGroups));
        $propertyGroups = array_merge(...$propertyGroups);

        if (empty($propertyGroups)) {

            $query = $query
                ->find('hasParams')
                ->select([
                    'x' => $query->newExpr($bucketExpr),
                    'z' => $z,
                    'totals' => $query->func()->count('Items.id'),
                    'grouptype' => '"period"'
                ])
                ->group([$query->newExpr($bucketExpr)])
                ->order(['x' => 'ASC']);

        } else {

            $query = $query
                ->find('hasParams')
                ->join([
                    'ArticleItems' => [
                        'table' => 'items',
                        'type' => 'INNER',
                        'conditions' => [
                            'ArticleItems.articles_id = Items.articles_id',
                            'ArticleItems.properties_id IN' => $propertyGroups
                        ]
                    ],
                    'ArticleProperties' => [
                        'table' => 'properties',
                        'type' => 'INNER',
                        'conditions' => [
                            'ArticleProperties.id = ArticleItems.properties_id'
                        ]
                    ]
                ])
                ->select([
                    'x' => $query->newExpr($bucketExpr),
                    'y_id' => 'ArticleItems.properties_id',
                    'y_label' => 'ArticleProperties.lemma',
                    'y_type' => "'properties'",
                    'z' => $z,

                    'totals' => $query->func()->count('Items.id'),
                    'grouptype' => 'ArticleProperties.propertytype',
                ])
                ->group(['ArticleItems.properties_id', $query->newExpr($bucketExpr)])
                ->order(['x' => 'ASC']);
        }

        $query
            ->disableHydration()
            ->formatResults(function (\Cake\Collection\CollectionInterface $results) {
                return $results->map(function ($row) {
                    if (!empty($row['y_id']) && !empty($row['y_type'])) {
                        $row['y'] = $row['y_type'] . '-' . $row['y_id'];
                    }
                    return new Group($row);
                });
            });
        return $query;
    }

    /**
     * Query for aggregated map data
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findTiles(Query $query, array $options)
    {
        $zoom = 5;
        if (isset($options['tile'])) {
            $tile = explode('/', $options['tile']);
            $zoom = ($tile[0] ?? $zoom) + 4;
        }
        else if (isset($options['zoom'])) {
            $zoom = (int)$options['zoom'];
        }


        $geoAlias = 'Properties';
        $valueField = 'content';
        $n = pow(2, $zoom);

        $tileXExpr = sprintf(
            'FLOOR(( CAST(JSON_VALUE(%1$s.%2$s,\'$.lng\') AS DOUBLE) + 180) / 360 * %3$d)',
            $geoAlias, $valueField, $n
        );
        $tileYExpr = sprintf(
            'FLOOR((1 - LOG(TAN(RADIANS(CAST(JSON_VALUE(%1$s.%2$s,\'$.lat\') AS DOUBLE))) + 1 / COS(RADIANS(CAST(JSON_VALUE(%1$s.%2$s,\'$.lat\') AS DOUBLE)))) / PI()) / 2 * %3$d)',
            $geoAlias, $valueField, $n
        );

// // Alternative for virtual fields in the database:
//        $geoAlias = 'Properties';
//        $valueField = 'geo';
//        $n = pow(2, $zoom);
//
//        $tileXExpr = sprintf(
//            'FLOOR((%1$s.%2$s_lng + 180) / 360 * %3$d)',
//            $geoAlias, $valueField, $n
//        );
//        $tileYExpr = sprintf(
//            'FLOOR((1 - LOG(TAN(RADIANS(%1$s.%2$s_lat)) + 1 / COS(RADIANS(%1$s.%2$s_lat))) / PI()) / 2 * %3$d)',
//            $geoAlias, $valueField, $n
//        );


        $query = $query
            ->find('hasParams', $options)
            ->find('containColumns', $options)
            ->contain('Properties')

            ->select([
                'x' => $query->newExpr($tileXExpr),
                'y' => $query->newExpr($tileYExpr),
                'z' => $zoom,
                'totals' => $query->func()->count('Items.id'),
                'grouptype' => '"tile"'
            ])
            ->group([$query->newExpr($tileXExpr), $query->newExpr($tileYExpr)]);

        $query
            ->disableHydration()
            ->formatResults(function (\Cake\Collection\CollectionInterface $results) {
                return $results->map(function ($row) {
                    return new Group($row);
                });
            });

        return $query;
    }

    /**
     * Query for relations between properties
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findGraph(Query $query, array $options)
    {
        $propertyGroups = array_filter($options['properties'] ?? [], fn($x) => in_array('grp', $x['flags'] ?? []));
        if (!empty($propertyGroups)) {
            $detailPropertytype = array_key_first($propertyGroups);
            $options['propertytypes'] = $detailPropertytype;
        }

        if (empty($options['propertytypes'])) {
            return $query->where(['1 = 0']);
        }

        // Step 1: Find article IDs that connect to more than one property and vice versa
        $twoProp = $this->find()
            ->find('hasParams', $options)
            ->select(['Items.articles_id'])
            ->group(['Items.articles_id'])
            ->having(['COUNT(DISTINCT Items.properties_id) >' => 1]);

        $twoArt = $this->find()
            ->find('hasParams', $options)
            ->select(['Items.properties_id'])
            ->group(['Items.properties_id'])
            ->having(['COUNT(DISTINCT Items.articles_id) >' => 1]);

        $query = $query
            ->find('hasParams', $options)
            ->contain(['Properties', 'Articles']);

        $columns = [
            'x_id' => 'Items.articles_id',
            'x_label' => 'Articles.name',
            'x_type' => "'articles'",
            'y_id' => 'Items.properties_id',
            'y_label' => 'Properties.lemma',
            'y_type' => "'properties'",
            'z' => $query->func()->count('Items.id'),
            'grouptype' => 'Properties.propertytype',
        ];

        if (!empty($options['images'])) {
            $query = $query->join([
                'ArticleItems' => [
                    'table' => 'items',
                    'type' => 'INNER',
                    'conditions' => [
                        'ArticleItems.articles_id = Items.articles_id',
                        'ArticleItems.sortno = 1',
                        'ArticleItems.itemtype IN' => $options['images']
                    ]
                ]
            ]);
            $columns['x_image'] = "CONCAT(ArticleItems.file_path, '/', ArticleItems.file_name)";
        }
        $query = $query->select($columns)
        ->where(['Items.articles_id IN' => $twoProp])
        ->where(['Items.properties_id IN' => $twoArt])
        ->group(['Items.articles_id', 'Items.properties_id']);

        $query
            ->disableHydration()
            ->formatResults(function (\Cake\Collection\CollectionInterface $results) {
                return $results->map(function ($row) {
                    $row['x'] = $row['x_type'] . '-' . $row['x_id'];
                    $row['y'] = $row['y_type'] . '-' . $row['y_id'];
                    return new Group($row);
                });
            });
        return $query;
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
