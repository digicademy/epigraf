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

use App\Datasource\Paging\TotalPaginator;
use App\Datasource\Services\ServiceFactory;
use App\Model\Interfaces\ExportTableInterface;
use App\Model\Table\BaseTable as AppBaseTable;
use App\Model\Table\SaveManyException;
use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use App\Utilities\Converters\Objects;
use App\Utilities\Text\TextParser;
use App\View\MarkdownView;
use ArrayObject;
use Cake\Collection\CollectionInterface;
use Cake\Database\Expression\AggregateExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use Epi\Model\Behavior\IndexBehavior;
use Epi\Model\Entity\Article;
use Epi\Model\Entity\IndexProperty;
use Epi\Model\Entity\IndexSection;
use Epi\Model\Entity\SectionPath;

/**
 * Articles table
 *
 * # Relations
 * @property ItemsTable $Items
 * @property SectionsTable $Sections
 * @property ProjectsTable $Projects
 *
 * # Behaviors
 * @mixin IndexBehavior
 */
class ArticlesTable extends BaseTable implements ExportTableInterface
{

    /**
     * The field containing a default caption
     *
     * @var string
     */
    public $captionField = 'name';

    public $parameters = [
        'id' => 'list-integer', // For updating stale rows in paginator.js
        'articles' => 'list-integer', // Replace by id?

        'deleted' => 'string',
        'published' => 'list-integer',
        'snippets' => 'list',

        'articletypes' => 'list',
        'sectiontypes' => 'list',
        'itemtypes' => 'list',
        'propertytypes' => 'list',

        'targets' => 'json',

        'items' => [
            'published' => 'list-integer'
        ],

        'field' => 'string',
        'term' => 'string',
        'highlight' => 'string',
        'projects' => 'list-integer',
        'properties' => 'nested-list', // Example: properties.objecttypes.selected=1,2,3.

        'references' => 'list-integer',

        'lat' => 'float',
        'lng' => 'float',
        'zoom' => 'raw',

        'selected' => 'list',
        'columns' => 'list',
        'sort' => 'list',
        'direction' => 'list',

        'template' => 'string',
        'mode' => 'constant-mode',
        'shape' => 'string',
        'idents' => 'string',

        'lanes' => 'string',
        'lane' => 'list-integer',

        'tile' => 'string',
        'children' => 'string',

        'load' => 'list',
        'save' => 'list'
    ];

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'articletype';

    /**
     * Property to collect index items (see findContainAll method)
     *
     * @var array $index
     */
    public array $index = [];

    /**
     * Search fields, overrides BaseTable->searchFields
     *
     * // TODO: Configure in types
     *
     * @var array[]
     */
    public $searchFields = [
        'captions' => [
            'caption' => 'Bezeichner',
            'scopes' => [
                'Articles.signature',
                'Articles.id' => ['type' => 'integer', 'operator' => '='],
                'Articles.name',
                'Articles.norm_iri',
                'Articles.norm_data'
            ]
        ],
        FIELD_ARTICLES_SIGNATURE => [
            'caption' => '- Signatur',
            'scopes' => ['Articles.signature']
        ],
        'ID' => [
            'caption' => '- ID',
            'scopes' => ['Articles.id'],
            'type' => 'integer',
            'operator' => '='
        ],
        'name' => [
            'caption' => '- Titel',
            'scopes' => ['Articles.name']
        ],
        'norm_iri' => [
            'caption' => '- IRI',
            'scopes' => ['Articles.norm_iri']
        ],
        'norm_data' => [
            'caption' => '- Normdaten',
            'scopes' => ['Articles.norm_data']
        ],
        'status' => [
            'caption' => 'Status',
            'scopes' => ['Articles.status']
        ],
        'text' => [
            'caption' => 'Text',
            'scopes' => []
        ]
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

        $this->setTable('articles');
        $this->setDisplayField(FIELD_ARTICLES_SIGNATURE);
        $this->setPrimaryKey('id');

        $this->addBehavior('Epi.Index', []);
        $this->addBehavior('Epi.XmlStyles', []);

        $this->belongsTo('Projects', [
            'className' => 'Epi.Projects',
            'foreignKey' => 'projects_id',
            'joinType' => Query::JOIN_TYPE_LEFT,
            'propertyName' => 'project'
        ]);

        $this->belongsTo('Creator', [
            'className' => 'Epi.Users',
            'foreignKey' => 'created_by',
            'joinType' => Query::JOIN_TYPE_LEFT,
            'propertyName' => 'creator'
        ]);

        $this->belongsTo('Modifier', [
            'className' => 'Epi.Users',
            'foreignKey' => 'modified_by',
            'joinType' => Query::JOIN_TYPE_LEFT,
            'propertyName' => 'modifier'
        ]);

        $this->hasMany('Sections', [
            'className' => 'Epi.Sections',
            'foreignKey' => 'articles_id',
            'joinType' => Query::JOIN_TYPE_INNER,
            'sort' => ['Sections.articles_id', 'Sections.lft'],
            'propertyName' => 'sections',
            'conditions' => ['Sections.deleted' => 0],
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        $this->hasMany('Items', [
            'className' => 'Epi.Items',
            'foreignKey' => 'articles_id',
            'sort' => ['Items.articles_id', 'Items.sortno'],
            'propertyName' => 'items',
            'conditions' => ['Items.deleted' => 0],
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        $this->hasMany('ItemsSearch', [
            'className' => 'Epi.Items',
            'foreignKey' => 'articles_id',
            'joinType' => Query::JOIN_TYPE_LEFT,
            'sort' => ['ItemsSearch.articles_id', 'ItemsSearch.sortno'],
            'propertyName' => ITEMTYPE_FULLTEXT,
            'conditions' => ['ItemsSearch.itemtype' => ITEMTYPE_FULLTEXT, 'ItemsSearch.deleted' => 0]
        ]);

        $this->hasMany('Links', [
            'className' => 'Epi.Links',
            'foreignKey' => 'root_id',
            'conditions' => ['Links.root_tab' => 'articles', 'Links.deleted' => 0],
            'joinType' => Query::JOIN_TYPE_LEFT,
            'sort' => 'Links.id',
            'propertyName' => 'links',
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        $this->hasMany('ToLinks', [
            'className' => 'Epi.Links',
            'foreignKey' => 'to_id',
            'conditions' => ['Links.to_tab' => 'articles', 'Links.deleted' => 0]
        ]);

        $this->hasMany('FromLinks', [
            'className' => 'Epi.Links',
            'foreignKey' => 'from_id',
            'conditions' => ['Links.from_tab' => 'articles', 'Links.deleted' => 0]
        ]);

        $this->hasMany('Footnotes', [
            'className' => 'Epi.Footnotes',
            'foreignKey' => 'root_id',
            'conditions' => ['Footnotes.root_tab' => 'articles', 'Footnotes.deleted' => 0],
            'joinType' => Query::JOIN_TYPE_LEFT,
            'sort' => ['Footnotes.sortno', 'Footnotes.name'],
            'propertyName' => 'footnotes',
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        // using "deleted" as foreignKey and bindingKey tricks Cake to
        // fetch all records
        $this->hasMany(
            'LinkTypes',
            [
                'className' => 'Epi.Types',
                'finder' => 'linkTypes',
                'foreignKey' => 'deleted',
                'bindingKey' => 'deleted'
            ]
        );

        // using "deleted" as foreignKey and bindingKey tricks Cake to
        // fetch all records
        $this->hasMany(
            'FootnoteTypes',
            [
                'className' => 'Epi.Types',
                'finder' => 'footnoteTypes',
                'foreignKey' => 'deleted',
                'bindingKey' => 'deleted'
            ]
        );

        $this->belongsTo(
            'Types',
            [
                'className' => 'Epi.Types',
                'strategy' => BelongsTo::STRATEGY_SELECT,
                'joinType' => Query::JOIN_TYPE_LEFT,
                'foreignKey' => 'articletype',
                'bindingKey' => 'name',
                'conditions' => ['Types.scope' => 'articles', 'Types.mode' => 'default', 'Types.preset' => 'default']
            ]
        );
    }

    /**
     * Default validation rules
     *
     * @param Validator $validator Validator instance
     *
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('articletype')
            ->maxLength('articletype', 100);

        $validator
            ->scalar('name')
            ->maxLength('name', 1500);

        $validator
            ->scalar('norm_iri')
            ->maxLength('norm_iri', 500)
            ->add('norm_iri', 'validFormat', [
                'rule' => ['custom', '/^[a-z0-9_~-]+$/'],
                'message' => 'Only lowercase alphanumeric characters, underscore, hyphen and tilde are allowed.'
            ])
            ->allowEmptyString('norm_iri');

        return $validator;
    }

    /**
     * Remove temporary ID
     *
     * @param EventInterface $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     *
     * @return void
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        //TODO: merge JSON in afterMarshal, see https://github.com/liqueurdetoile/cakephp-orm-json/blob/6364490a410b9244429b06e72bfa62087cf58b9f/src/DatField/DatFieldParserTrait.php#L300
        parent::beforeMarshal($event, $data, $options);
    }

    /**
     * Set root and container property of the items
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param array $options
     *
     * @return void
     */
    public function beforeSave(EventInterface $event, $entity, $options = []): void
    {
        if (!empty($entity->sections)) {
            foreach ($entity->sections as $section) {
                $section->prepareRoot($entity, $entity);
            }
        }

        if ($options['addsections'] ?? false) {
            $entity->addDefaultSections(true);
        }

    }

    /**
     * Set lft/rght values of sections and clear the cache
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param array $options
     *
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, $options = [])
    {
        //TODO: automatically create IRI fragment (define behavior?)
        if ($options['recover'] ?? false) {
            $this->Sections->setScope($entity->id);
            $this->Sections->recover();
        }

        parent::afterSave($event, $entity, $options);
        $this->clearViewCache('epi_views_Epi_Articles');
    }

    /**
     * Find articles by search term
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findHasTerm(Query $query, array $options)
    {
        $term = $options['term'] ?? false;
        $field = $options['field'] ?? false;

        //Fulltext
        if (!empty($term) && (str_starts_with($field, 'text'))) {
            $query = $query->find('hasText', $options);
        }

        // Other fields
        elseif (!empty($term) && !empty($field)) {
            $searchConfig = $this->searchFields[$field] ?? [];

            $query = $query->find('term', [
                'term' => $term,
                'searchFields' => $searchConfig['scopes'] ?? [],
                'operator' => $searchConfig['operator'] ?? 'LIKE',
                'type' => $searchConfig['type'] ?? 'string'
            ]);
        }

        return $query;
    }

    /**
     * Find articles by geolocation
     *
     * ### Options
     * - tile (string) The tile coordinates in the format "zoom/y/x"
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findHasGeolocation(Query $query, array $options)
    {

        if (!empty($options['tile'])) {

            $filter = $this->getFilter([]);
            $geoConditions = [];
            foreach ($filter['geodata'] ?? [] as $itemType => $itemField) {

                $geoQuery = $this
                    ->find()
                    ->select(['Articles.id'])
                    ->join($this->_tilesJoin(
                        $options['tile'],
                        $itemType,
                        $itemField,
                        $options['items']['published'] ?? []
                    ));

                $geoConditions[] = ['Articles.id IN' => $geoQuery];
            }
            $query = $query->where(['OR' => $geoConditions]);
        }

        return $query;
    }

    /**
     * Find articles without geolocation distance
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findHasNoGeolocation(Query $query, array $options): Query
    {
        $filter = $this->getFilter($options);
        foreach ($filter['geodata'] ?? [] as $itemType => $itemField) {

            $geoQuery = $this
                ->find()
                ->select(['Articles.id'])
                ->join($this->_geoJoin($itemType, $itemField));

            $query = $query->where(['Articles.id NOT IN' => $geoQuery]);
        }

        return $query;
    }

    /**
     * Find articles by project
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findHasProject(Query $query, array $options)
    {
        //TODO: can this be removed (parsed in parseRequestParameters)?
        $projects = Attributes::commaListToIntegerArray($options['projects'] ?? []);

        if (!empty($projects)) {
            $query = $query
                ->where([
                    'Articles.projects_id IN' => $projects,
                ]);
        }

        return $query;
    }

    /**
     * Find articles by article type
     *
     * @param Query $query
     * @param array $options Provide the articletypes in the corresponding key
     *
     * @return Query
     */
    public function findHasArticleType(Query $query, array $options)
    {
        //TODO: can this be removed (parsed in parseRequestParameters)?
        $articletypes = Attributes::commaListToStringArray($options['articletypes'] ?? []);

        if (!empty($articletypes)) {
            $query = $query
                ->where([
                    'Articles.articletype IN' => $articletypes,
                ]);
        }
        return $query;
    }

    /**
     * Find articles by section type
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findHasSectionType(Query $query, array $options)
    {
        //TODO: can this be removed (parsed in parseRequestParameters)?
        $types = Attributes::commaListToStringArray($options['sectiontypes'] ?? []);

        if (!empty($types)) {

            $subQuery = $this
                ->find()
                ->select(['Articles.id'])
                ->innerJoinWith('Sections', function (Query $query) use ($types) {
                    $query = $query->where(['Sections.sectiontype IN' => $types]);
                    return $query;
                });

            // Filter
            $query = $query
                ->where(['Articles.id IN' => $subQuery]);
        }

        return $query;
    }

    /**
     * Find articles by item type
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findHasItemType(Query $query, array $options)
    {
        //TODO: can this be removed (parsed in parseRequestParameters)?
        $types = Attributes::commaListToStringArray($options['itemtypes'] ?? []);

        if (!empty($types)) {
            $subQuery = $this
                ->find()
                ->select(['Articles.id'])
                ->innerJoinWith('Items', function (Query $query) use ($types) {
                    $query = $query->where(['Items.itemtype IN' => $types]);
                    return $query;
                });

            // Filter
            $query = $query
                ->where(['Articles.id IN' => $subQuery]);
        }

        return $query;
    }

    /**
     * Find other articles that refer to given articles by items or links
     *
     * Ignores internal links
     *
     * ### Options
     * - references (array) A list of article IDs
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasReferences(Query $query, array $options)
    {
        $references = $options['references'] ?? null;

        if (empty($references)) {
            return $query;
        }

        $referenceSectionsQuery = $this->Sections->find()
            ->select(['Sections.id'])
            ->where(['Sections.articles_id IN' => $references]);

        $referenceItemsQuery = $this->Items->find()
            ->select(['Items.id'])
            ->where(['Items.articles_id IN' => $references]);

        $referenceLinksQuery = $this->Links->find()
            ->select(['Links.id'])
            ->where(['Links.root_id IN' => $references, 'Links.root_tab' => 'articles']);

        $referenceFootnotesQuery = $this->Footnotes->find()
            ->select(['Footnotes.id'])
            ->where(['Footnotes.root_id IN' => $references, 'Footnotes.root_tab' => 'articles']);

        // Items
        $itemsQuery = $this
            ->find()
            ->select(['Articles.id'])
            ->innerJoinWith('Items', function (Query $query) use (
                $references,
                $referenceSectionsQuery,
                $referenceItemsQuery,
                $referenceLinksQuery,
                $referenceFootnotesQuery
            ) {
                $query = $query->where([
                    // TODO: keep links between the reference articles
                    'Items.articles_id NOT IN' => $references,
                    'OR' => [
                        ['Items.links_id IN' => $references, 'Items.links_tab' => 'articles'],
                        ['Items.links_id IN' => $referenceSectionsQuery, 'Items.links_tab' => 'sections'],
                        ['Items.links_id IN' => $referenceItemsQuery, 'Items.links_tab' => 'items'],
                        ['Items.links_id IN' => $referenceLinksQuery, 'Items.links_tab' => 'links'],
                        ['Items.links_id IN' => $referenceFootnotesQuery, 'Items.links_tab' => 'footnotes'],
                    ]
                ]);
                return $query;
            });

        // Links - to the reference article
        $linksQuery = $this->Links
            ->find()
            ->select(['Links.root_id'])
            ->where([
                // TODO: keep links between the reference articles
                'Links.root_id NOT IN' => $references,
                'Links.root_tab' => 'articles',
                'OR' => [
                    ['Links.to_id IN' => $references, 'Links.to_tab' => 'articles'],
                    ['Links.to_id IN' => $referenceSectionsQuery, 'Links.to_tab' => 'sections'],
                    ['Links.to_id IN' => $referenceItemsQuery, 'Links.to_tab' => 'items'],
                    ['Links.to_id IN' => $referenceLinksQuery, 'Links.to_tab' => 'links'],
                    ['Links.to_id IN' => $referenceFootnotesQuery, 'Links.to_tab' => 'footnotes'],
                ]
            ]);

        // Filter
        $query = $query
            ->where([
                'or' => [
                    ['Articles.id IN' => $itemsQuery],
                    ['Articles.id IN' => $linksQuery],
                ]
            ]);

        return $query;
    }

    /**
     * Find articles by properties
     *
     * ### Options
     * - properties (array) Either an array with property IDs or a nested list.
     *                      The nested list contains the propertytype on the first levem,
     *                      and the following keys on the second:
     *                      - selected with a list of the IDs
     *                      - flags: an enum list with the values
     *                               inv (=inverse selection),
     *                               all (=select all),
     *                               des (=descendent properties)
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasProperties(Query $query, array $options)
    {
        // Expand flat array to multidemensional array
        $properties = $options['properties'] ?? [];
        if (empty($properties)) {
            $properties = [];
        }
        elseif (!is_array($properties)) {
            $properties = ['' => $properties];
        }
        elseif (!empty($properties) && !is_array(reset($properties))) {
            $properties = ['' => $properties];
        }

        // Selected IDs
        foreach ($properties as $propertyType => $propertyParams) {
            $propertyIds = $propertyParams['selected'] ?? $propertyParams;

            if (in_array('all', $propertyParams['flags'] ?? [])) {
                $hasItemsQuery = $this
                    ->find()
                    ->select(['Articles.id'])
                    ->innerJoinWith('Items.Properties', function (Query $query) use ($propertyType) {
                        $query = $query->where(['Properties.propertytype' => $propertyType]);
                        return $query;
                    });

                $hasLinksQuery = $this
                    ->find()
                    ->select(['Articles.id'])
                    ->innerJoinWith('Links.Properties', function (Query $query) use ($propertyType) {
                        $query = $query->where(['Properties.propertytype' => $propertyType]);
                        return $query;
                    });

                if (in_array('inv', $propertyParams['flags'] ?? [])) {
                    $query = $query
                        ->where([
                            'AND' => [
                                ['Articles.id NOT IN' => $hasItemsQuery],
                                ['Articles.id NOT IN' => $hasLinksQuery]
                            ]
                        ]);
                }
                else {
                    $query = $query
                        ->where([
                            'OR' => [
                                ['Articles.id IN' => $hasItemsQuery],
                                ['Articles.id IN' => $hasLinksQuery]
                            ]
                        ]);
                }
                continue;
            }

            // show all (no filter) or nothing
            if (!$propertyIds) {
                if (in_array('inv', $propertyParams['flags'] ?? [])) {
                    $query = $query
                        ->where(['0 = 1']);
                }
                continue;
            }

            // Collect child IDs
            if (in_array('des', $propertyParams['flags'] ?? [])) {
                $propertyIds = $this->Items->Properties->find('descendants', ['ids' => $propertyIds]);
            }

            // Items
            $itemsQuery = $this
                ->find()
                ->select(['Articles.id'])
                ->innerJoinWith('Items.Properties', function (Query $query) use ($propertyIds) {
                    $query = $query->where(['Properties.id IN' => $propertyIds]);
                    return $query;
                });

            // Links
            $linksQuery = $this
                ->find()
                ->select(['Articles.id'])
                ->innerJoinWith('Links.Properties', function (Query $query) use ($propertyIds) {
                    $query = $query->where(['Properties.id IN' => $propertyIds]);
                    return $query;
                });

            // Filter
            if (in_array('inv', $propertyParams['flags'] ?? [])) {
                $query = $query
                    ->where([
                        'AND' => [
                            ['Articles.id NOT IN' => $itemsQuery],
                            ['Articles.id NOT IN' => $linksQuery],
                        ]
                    ]);
            }
            else {
                $query = $query
                    ->where([
                        'OR' => [
                            ['Articles.id IN' => $itemsQuery],
                            ['Articles.id IN' => $linksQuery],
                        ]
                    ]);
            }
        }

        return $query;
    }

    /**
     * Find articles by term
     *
     * Full text search with snippet extraction and results highlighter.
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findHasText(Query $query, array $options)
    {

        //Term
        $term = $options['term'] ?? '';
        $fields = ['ItemsSearch.content'];
        $operator = 'LIKE';
        $type = 'string';

        $field = explode('.', $options['field'] ?? 'text');
        if (!empty($field[1])) {
            $filter = ['ItemsSearch.value IN' => $field[1]];
        } else {
            $filter = [];
        }

        $conditions = Arrays::termConditions($term, $fields, $operator, $type, $filter);
        $terms =  preg_split('/[ |]/', $term, -1, PREG_SPLIT_NO_EMPTY);

        $query = $query
            ->distinct()

            //Highlight
            ->contain('ItemsSearch',
                function ($q) use ($conditions, $terms) {
                    return $q
                        ->where(['AND' => $conditions])
                        ->formatResults(function (CollectionInterface $results) use ($terms) {
                            return $results->map(function ($item) use ($terms) {
                                $item['content'] = TextParser::highlightTerms($item['content'], $terms);
                                $item['highlight'] = $terms;
                                return $item;
                            });
                        });
                })

            //Search
            ->innerJoinWith('ItemsSearch',
                function ($q) use ($conditions) {
                    return $q->where(['AND' => $conditions]);
                });

        return $query;
    }

    /**
     * Find articles by request parameters
     *
     * @param Query $query
     * @param array $options request parameters
     *
     * @return Query
     */
    public function findHasParams(Query $query, array $options): Query
    {

        // Merge empty default parameters
        $default = [
            'articles' => null,
            'id' => null,
            'term' => '',
            'field' => '',
            'projects' => '',
            'articletypes' => '',
            'published' => null,
            'properties' => [],
            'lane' => []
        ];
        $params = array_merge($default, $options);

        // Generate query
        $query = $query->find('hasIds', $params);
        $query = $query->find('hasTerm', ['field' => $params['field'], 'term' => $params['term']]);
        $query = $query->find('hasProject', ['projects' => $params['projects']]);
        $query = $query->find('hasReferences', $params);

        $query = $query->find('hasArticleType', ['articletypes' => $params['articletypes']]);
        $query = $query->find('hasSectionType', ['sectiontypes' => $params['sectiontypes'] ?? []]);
        $query = $query->find('hasItemType', ['itemtypes' => $params['itemtypes'] ?? []]);

        $query = $query->find('hasProperties', ['properties' => $params['lane']]);
        $query = $query->find('hasProperties', ['properties' => $params['properties']]);
        $query = $query->find('hasGeolocation', $params);

        //TODO: join project publication state
        $query = $query->find('hasPublicationState', ['published' => $params['published']]);

        return $query;
    }

    /**
     * Find items and sections by type
     *
     *  // TODO: only contain necessary data, based on fields parameter
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findContainFields(Query $query, array $options)
    {
        // Contain sections and items
        $containSections = function ($q) use ($options) {
            $q = $q
                ->select(['id', 'parent_id', 'articles_id', 'sectiontype', 'name']);

            $types = $options['sectiontypes'] ?? [];
            if (!empty($types)) {
                $q = $q->where(['Sections.sectiontype IN' => $types]);
            }
            return $q;
        };

        $containItems = function ($q) use ($options, $query) {

            $itemConditions = [];
            $itemTypes = $options['itemtypes'] ?? [];
            if (!empty($itemTypes)) {
                $itemConditions['Items.itemtype IN'] = $itemTypes;
            }

            $sectionTypes = $options['sectiontypes'] ?? [];
            if (!empty($sectionTypes)) {
                $itemConditions['Items.sections_id IN'] = $query
                    ->cleanCopy()
                    ->select('id', true)
                    ->innerJoinWith('Sections', function (Query $sectionsQuery) use ($options) {
                        return $sectionsQuery->where(['Sections.sectiontype IN' =>  $options['sectiontypes']]);
                    });
            }

            if (!empty($itemConditions)) {
                $q = $q->where($itemConditions);
            }
            return $q;
        };

        $contain = [
            'Creator',
            'Modifier',

            'Projects',

            'Sections' => $containSections,

            'Items' => $containItems,
            'Items.Properties',
            'Items.Types',

            'Links',
            'Footnotes'
        ];

        $query = $query->contain($contain);

        // Distance field for geolocated data
        if (in_array('distance', $options['columns'] ?? [])) {
            $query = $query->find('containDistance', $options);
        }
        // Items for item filters
        else {
            // TODO: remove Creator and Modifier from select, use joins only?
            // TODO: automatically select contained associations
            $query = $query
                ->select($this)
                ->select($this->Projects)
                ->select($this->Creator)
                ->select($this->Modifier);

            $query = $query->find('sortFields', $options);
        }

        return $query;
    }

    /**
     * Get distance field
     *
     * The calculated distance to the position provided in the options,
     * which defaults to Mainz.
     *
     * ### Options
     * - lat The reference location
     * - lng The reference location
     *
     * @param Query $query
     * @param array $options
     * @param string $tableName The table or join name with geodata records.
     * @param string $fieldName The field containing geodata in a JSON value (with lat & lng keys) .
     *
     * @return AggregateExpression An expression to be used in select statements of find operations
     */
    protected function _distanceField($query, $options, $tableName, $fieldName)
    {

        // Default position Mainz
        if ((($options['lat'] ?? null) === null) || (($options['lng'] ?? null) === null)) {
            $options['lat'] = 52.147040492349;
            $options['lng'] = 13.612060546875;
        }

        return $query->func()->min(
            '
                ( 6371 * ACOS(
                  (
                    cos( radians(' . $options['lat'] . ') ) *
                    cos( radians(CAST(JSON_VALUE(' . $tableName. '.`' . $fieldName . '`,\'$.lat\') AS DOUBLE)) )  *
                    cos( radians(CAST(JSON_VALUE(' . $tableName. '.`' . $fieldName . '`,\'$.lng\') AS DOUBLE)) - radians(' . $options['lng'] . ') )
                  )
                  +
                   (
                      sin( radians(' . $options['lat'] . ') )  *
                      sin( radians(CAST(JSON_VALUE(' . $tableName. '.`' . $fieldName . '`,\'$.lat\') AS DOUBLE)))
                    )
                  )
                )'
        );
    }

    /**
     * Get distance join
     *
     * @param string $itemtype The alias will be the item type prefixed by 'geo_dist_'.
     * @param boolean $property Whether to join the property table
     * @return array
     */
    protected function _distanceJoin($itemtype, $property = false)
    {
        $itemAlias = 'geo_dist_' . $itemtype;
        $itemJoin = [
            'table' => 'items',
            'alias' => $itemAlias,
            'type' => 'LEFT',
            'conditions' =>
                $itemAlias . ".articles_id = Articles.id AND " .
                $itemAlias . ".itemtype = \"{$itemtype}\" AND " .
                $itemAlias . ".deleted = 0"
        ];

        if ($property) {
            $propertyAlias = $itemAlias . '_properties';
            $propertyJoin = [
                'table' => 'properties',
                'alias' => $propertyAlias,
                'type' => 'LEFT',
                'conditions' =>
                    $itemAlias . '.properties_id = ' . $propertyAlias . '.id AND ' .
                    $propertyAlias . ".deleted = 0"
            ];

            return [$itemJoin, $propertyJoin];
        }

        return $itemJoin;
    }

    /**
     * Join geo data
     *
     * @param string $itemtype The alias will be the item type prefixed by 'geo_dist_'.
     * @param string $fieldName The field containing geodata in a JSON value (with lat & lng keys).
     *                          The field name can be prefixed with 'property.' to join the property table.
     *
     * @return array
     */
    protected function _geoJoin($itemtype, $fieldName)
    {
        $fieldName = explode('.', $fieldName);
        $propertyJoin = ($fieldName[0] ?? '') === 'property';
        $valueField = count($fieldName) > 1 ? $fieldName[1] : $fieldName[0];

        $itemAlias = 'geo_' . $itemtype;

        if ($propertyJoin) {
            $itemJoin = [
                'table' => 'items',
                'alias' => $itemAlias,
                'type' => 'INNER',
                'conditions' =>
                    $itemAlias . ".articles_id = Articles.id AND " .
                    $itemAlias . ".itemtype = \"{$itemtype}\" AND " .
                    $itemAlias . ".deleted = 0"
            ];


            $propertyAlias = $itemAlias . '_property';
            $propertyJoin = [
                'table' => 'properties',
                'alias' => $propertyAlias,
                'type' => 'INNER',
                'conditions' =>
                    $itemAlias . '.properties_id = ' . $propertyAlias . '.id AND ' .
                    $propertyAlias . ".deleted = 0 AND " .
                    'JSON_VALUE(' . $propertyAlias . '.`' . $valueField . '`,\'$.lat\') IS NOT NULL AND ' .
                    'JSON_VALUE(' . $propertyAlias . '.`' . $valueField . '`,\'$.lng\') IS NOT NULL'
            ];

            return [$itemJoin, $propertyJoin];
        }
        else {
            $itemJoin = [
                'table' => 'items',
                'alias' => $itemAlias,
                'type' => 'INNER',
                'conditions' =>
                    $itemAlias . ".articles_id = Articles.id AND " .
                    $itemAlias . ".itemtype = \"{$itemtype}\" AND " .
                    $itemAlias . ".deleted = 0 AND " .
                    'JSON_VALUE(' . $itemAlias . '.`' . $valueField . '`,\'$.lat\') IS NOT NULL AND ' .
                    'JSON_VALUE(' . $itemAlias . '.`' . $valueField . '`,\'$.lng\') IS NOT NULL'
            ];
            return $itemJoin;
        }
    }

    /**
     * Get tiles join
     *
     * @param string $tile The tile coordinates in the format "zoom/y/x".
     * @param string $itemType The item type containing the geo data or linking to the property with geo data.
     * @param string $fieldName The field containing geo data as JSON value with lat & lng keys.
     *                          The field name can be prefixed with 'property.' to join the properties table.
     * @param integer[] $published An array of publication values, or an empty array.
     * @return array An array that defines inner joins
     */
    protected function _tilesJoin($tile, $itemType, $fieldName, $published = [])
    {
        // Get fields and join aliases
        $fieldName = explode('.', $fieldName);
        $valueField = count($fieldName) > 1 ? $fieldName[1] : $fieldName[0];
        $propertyJoin = ($fieldName[0] ?? '') === 'property';

        $itemAlias = 'geo_tile_' . $itemType;
        $propertyAlias = $itemAlias . '_property';
        $geoAlias = $propertyJoin ? $propertyAlias : $itemAlias;

        // Tile conditions
        $tile = explode('/', $tile);

        // See https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames#ECMAScript_.28JavaScript.2FActionScript.2C_etc..29
        $zoom = (int)$tile[0] ?? 0;
        $ytile = (int)$tile[1] ?? 0;
        $xtile = (int)$tile[2] ?? 0;

        $n = pow(2, $zoom);
        $lon_deg_west = $xtile / $n * 360.0 - 180.0;
        $lon_deg_east = ($xtile + 1) / $n * 360.0 - 180.0;
        $lat_deg_north = rad2deg(atan(sinh(pi() * (1 - 2 * $ytile / $n))));
        $lat_deg_south = rad2deg(atan(sinh(pi() * (1 - 2 * ($ytile + 1) / $n))));

        $geoConditions = [
            'CAST(JSON_VALUE(' . $geoAlias . '.`' . $valueField . '`,\'$.lat\') AS DOUBLE) > ' . $lat_deg_south,
            'CAST(JSON_VALUE(' . $geoAlias . '.`' . $valueField . '`,\'$.lat\') AS DOUBLE) <= ' . $lat_deg_north,
            'CAST(JSON_VALUE(' . $geoAlias . '.`' . $valueField . '`,\'$.lng\') AS DOUBLE) > ' . $lon_deg_west,
            'CAST(JSON_VALUE(' . $geoAlias . '.`' . $valueField . '`,\'$.lng\') AS DOUBLE) <= ' . $lon_deg_east
        ];

        if (!empty($published)) {
            $geoConditions[] = $geoAlias . '.published IN (' . implode(',', $published) . ')';
        }

        // Property joins
        if ($propertyJoin) {

            $itemConditions = [
                $itemAlias . ".articles_id = Articles.id",
                $itemAlias . ".itemtype = \"{$itemType}\"",
                $itemAlias . ".deleted = 0"
            ];
            if (!empty($published)) {
                $itemConditions[] = $itemAlias . '.published IN (' . implode(',', $published) . ')';
            }

            $itemJoin = [
                'table' => 'items',
                'alias' => $itemAlias,
                'type' => 'INNER',
                'conditions' => $itemConditions
            ];

            $geoConditions = [
                $itemAlias . '.properties_id = ' . $propertyAlias . '.id',
                $propertyAlias . ".deleted = 0"
            ] + $geoConditions;

            $propertyJoin = [
                'table' => 'properties',
                'alias' => $propertyAlias,
                'type' => 'INNER',
                'conditions' => $geoConditions
            ];

            return [$itemJoin, $propertyJoin];
        }

        // Item join
        else {
            $geoConditions = [
                $itemAlias . ".articles_id = Articles.id",
                $itemAlias . ".itemtype = \"{$itemType}\"",
                $itemAlias . ".deleted = 0"
            ] + $geoConditions;

            $itemJoin = [
                'table' => 'items',
                'alias' => $itemAlias,
                'type' => 'INNER',
                'conditions' => $geoConditions
            ];
            return $itemJoin;
        }
    }

    /**
     * Order by geolocation distance
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findContainDistance(Query $query, array $options): Query
    {
        $filter = $this->getFilter([]);
        foreach ($filter['geodata'] ?? [] as $itemType => $itemField) {
            $query = $query
                ->select($this)
                ->select(['distance' => $this->_distanceField($query, $options, 'geo_dist_' . $itemType, $itemField)])
                ->join($this->_distanceJoin($itemType))
                ->group(['Articles.id'])
                ->having(['distance IS NOT' => null]);

            // TODO: Allow multiple item types
            return $query;
        }

        return $query;
    }

    /**
     * Get article data with all items
     *
     * Use the snippets key of $options to get:
     * - search Items with text for full text search
     * - indexes An index will be build from all references to properties in items and links
     * - paths The section path will be added to each section and each item
     *
     * Restructure the data with the following option keys:
     * - regroup
     *
     * TODO: merge findContainAll and findContainFields
     * TODO: find links within properties and footnotes?
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findContainAll(Query $query, array $options): Query
    {
        $contain = [
            'Creator',
            'Modifier',

            'Projects',
            'Projects.Types',

            'Sections' => ['strategy' => 'select'],
            'Sections.Types',

            'Items' => ['strategy' => 'select'],
            'Items.Types',
            'Items.LinkedArticle',
            'Items.LinkedArticle.Projects',
            'Items.LinkedSection',

            'Items.PropertiesWithAncestors',
            'Items.PropertiesWithAncestors.Types',

            'Items.PropertiesWithAncestors.MetaPropertyWithAncestors',
            'Items.PropertiesWithAncestors.MetaPropertyWithAncestors.Types',

            'Items.PropertiesWithAncestors.LinksFrom',
            'Items.PropertiesWithAncestors.LinksFrom.Properties',
            'Items.PropertiesWithAncestors.LinksFrom.Properties.MetaPropertyWithAncestors',
            'Items.PropertiesWithAncestors.LinksFrom.Properties.Types',

            'Footnotes',
            'Links',

            'Links.PropertiesWithAncestors',
            'Links.PropertiesWithAncestors.Types',

            'Links.PropertiesWithAncestors.MetaPropertyWithAncestors',
            'Links.PropertiesWithAncestors.MetaPropertyWithAncestors.Types',

            'Links.Articles',
            'Links.Articles.Projects',

            'Links.SectionsWithAncestors',
            'Links.SectionsWithAncestors.SectionArticles',
            'Links.SectionsWithAncestors.SectionArticles.Projects',

            'Links.Footnotes',
            'Links.Footnotes.FootnoteArticles',
            'Links.Footnotes.FootnoteArticles.Projects',
            'Types'
        ];

        $itemConditions = [];
        $sectionConditions = [];

        // Snippets
        if (!in_array('search', $options['snippets'] ?? [])) {
            $itemConditions[] = ['Items.itemtype !=' => ITEMTYPE_FULLTEXT];
        }

        // Section types
        if (!empty($options['sectiontypes'] ?? [])) {
            $sectionConditions[] = ['Sections.sectiontype IN' => $options['sectiontypes']];
        }

        if (!empty($sectionConditions)) {
            $contain['Sections']['queryBuilder'] = function ($q) use ($sectionConditions) {
                return $q->where(['AND' => $sectionConditions]);
            };

            // Remove items in sections
            $contain['Items']['queryBuilder'] = function ($q) use ($sectionConditions) {
                if (!empty($itemConditions)) {
                    $q = $q->where(['AND' => $itemConditions]);
                }
                $q = $q->innerJoinWith('Sections', function (Query $query) use ($sectionConditions) {
                    $query = $query->where($sectionConditions);
                    return $query;
                });
                return $q;
            };
        }

        elseif (!empty($itemConditions)) {
            $contain['Items']['queryBuilder'] = function ($q) use ($itemConditions) {
                return $q->where(['AND' => $itemConditions]); // >limit(50000)
            };
        }

        // Bind Items
        $query = $query->contain($contain);

        $query = $this->findCollectItems($query, $options);
        $query = $this->findRegroupSections($query, $options);

        return $query;
    }


    /**
     * Regroup the sections (change order, put in groups, add tree position values)
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findRegroupSections(Query $query, array $options): Query
    {
        if ($options['regroup'] ?? false) {
            $query = $query->formatResults(function (CollectionInterface $results) use ($options) {
                return $results->each(function ($article) use ($options) {
                    /** @var Article $article */
                    return $article->regroupSections($options);
                });
            });
        };
        return $query;
    }

    /**
     * Transform article
     *
     * - move items to sections
     * - create path for items and sections
     * - set parent on sections
     * - create index: collect properties from items, footnotes and links and add them to the indexes
     * - add sections to serializable fields
     *
     * TODO: See BaseEntity::prepareTree() and decide whether to merge the function here
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findCollectItems(Query $query, array $options): Query
    {

        /** @var ArticlesTable $articles */
        $articles = $this;
        $query = $query->formatResults(function (CollectionInterface $results) use (&$query, $articles, $options) {

            /** @var Article $article */
            return $results->each(function ($article) use ($articles, &$options) {

                // See Footnote::getEntityIsVisible() and Link::getEntityIsVisible()
                // TODO: Better place for this? Put it here because this gets called for each article
                //       from ArticlesTable::findContainAll()
                if (!empty($options['sectiontypes'])) {
                    $article->_filterAnnos = true;
                }

                // Handle sections
                // - Initialize stack with empty root item
                // - Found items will be moved from the article to the section
                $pathStack = [new SectionPath()];
                $sectionStack = [];
                $foundItems = [];
                foreach ($article->sections as $section) {

                    // Collect parents
                    while (!empty($sectionStack) && $sectionStack[count($sectionStack) - 1]['id'] != $section['parent_id']) {
                        array_pop($sectionStack);
                    }
                    $section->parent = empty($sectionStack) ? null : end($sectionStack);
                    $sectionStack[] = $section;

                    // - truncate stack to include only ancestors
                    while (!empty($pathStack) && $pathStack[count($pathStack) - 1]['id'] != $section['parent_id']) {
                        array_pop($pathStack);
                    }

                    // - increase child count
                    if (!empty($pathStack)) {
                        $pathStack[count($pathStack) - 1]['children'] += 1;
                    }

                    // - put path entity on stack
                    $pathStack[] = new SectionPath($section, $pathStack);

                    // Set path (without empty root element)
                    $section['path'] = array_slice($pathStack, 1);

                    $movedItems = [];
                    foreach ($section->items as $item) {
                        $movedItems[] = $item;
                        $foundItems[] = $item->id;

                        $item->prepareRoot($section, $article, true, true);
                        $item['sectionpath'] = $section['path'] ?? [];

                        if (in_array('indexes', $options['snippets'] ?? [])) {

                            // Collect properties for index (item -> property)
                            $indexsection = new IndexSection($section);

                            if (!empty($item['property'])) {
                                $indexproperty = new IndexProperty($item['property']);
                                $articles->addToIndex($indexproperty, $indexsection);
                            }

                            // Collect properties for index (links -> property)
                            $links = $article->getLinksFrom('items', $item->id);
                            foreach ($links as $link) {
                                if ($link->property) {
                                    $indexproperty = new IndexProperty($link->property);
                                    $articles->addToIndex($indexproperty, $indexsection);
                                }
                            }

                            // Collect properties for index (footnotes -> property)
                            $footnotes = $article->getFootnotesFrom('items', $item->id);
                            foreach ($footnotes as $footnote) {
                                $links = $article->getLinksFrom('footnotes', $footnote->id);
                                foreach ($links as $link) {
                                    if ($link->property) {
                                        $indexproperty = new IndexProperty($link->property);
                                        $articles->addToIndex($indexproperty, $indexsection);
                                    }
                                }
                            }
                        }
                        $item->clean();
                    }
                    $section['items'] = $movedItems;
                    $section->clean();
                }

                // Remove found items
                $article->items = array_filter($article->items, fn($item) => !in_array($item->id, $foundItems));
                $article->_nestedItems = true;
                $article->setHidden(['items']);

                $article->clean();
                return $article;
            });
        });

        return $query;
    }

    /**
     * Called from JobExport
     *
     * @implements ExportTableInterface
     *
     * @param $params
     *
     * @return int Number of articles for calculating the progress bar
     */
    public function getExportCount($params): int
    {
        $params = $this->parseRequestParameters($params);

        return $this
            ->find('hasParams', $params)
            ->count();
    }

    /**
     * Get the configured pipelines
     *
     * // TODO: define in ExportTableInterface?
     * // @implements ExportTableInterface
     *
     * @param array $params Query parameters
     * @return array An array of pipelines in the format ['pipelines' => ['iri1', 'iri2']]
     */
    public function getExportOptions($params): array
    {
        $params = $this->parseRequestParameters($params);

        $articles = $this
            ->find('hasParams', $params)
            ->distinct(['Articles.articletype'])
            ->contain('Types');

        $pipelines = [];
        foreach ($articles as $article) {
            $morePipelines = array_keys($article->type['config']['pipelines'] ?? []);
            $pipelines = array_unique(array_merge($pipelines, $morePipelines));
        }
        return ['pipelines' => $pipelines];
    }

    /**
     * Called from JobExport
     *
     * @implements ExportTableInterface
     * @param array $params
     * @param array $paging
     * @param string $indexkey
     * @return Article[]
     */
    public function getExportData($params, $paging = [], $indexkey = ''): array
    {
        $offset = $paging['offset'] ?? 0;
        $limit = $paging['limit'] ?? 1;

        // TODO: harmonize defaults between ArticlesController->_getSearchParameters, getExportData, findComplete
        [$params, $columns, $paging, $filter] = $this->prepareParameters($params);

        // Load index cache
        // TODO: Should this be called in the Job class?
        $this->loadIndex($indexkey);

        // Filter data
        $query = $this
            ->find('hasParams', $params);

        // Add sort criteria
         if (!empty($params['sort'])) {

            $paginator = new TotalPaginator();
            $paginatorParams = $params;
            $paginatorParams['sortableFields'] = $this->getSortableFields($columns);
            $paginatorParams = $paginator->validateSort($this, $paginatorParams);
            if (!empty($paginatorParams['order'])) {
                $query = $query
//                    ->select($this)
                    ->find('sortFields', $params)
                    ->order($paginatorParams['order']);
            }
        }
        else if (!empty($paging['order'])) {
            $pagingFields = Arrays::array_add_prefix($paging['order'], $this->getAlias() . '.', true);
            $query = $query->order($pagingFields);
        }


        // TODO: remove toArray()
        $entities = $query
            ->find('containAll', $params)
            ->limit($limit)
            ->offset($offset)
            ->all()
            ->toArray();

        // Save index cache
        // TODO: Should this be called in the Job class?
        $this->saveIndex($indexkey);

        return $entities;
    }

    /**
     * Get columns
     *
     * TODO: keep all definitions, indexed by: articletype, default, query parameter
     *
     * @param array $selected The selected columns
     * @param array $default The default columns
     * @param string $type Filter by type
     *
     * @return array
     */
    public function getColumns($selected = [], $default = [], $type = null)
    {
        $default = [
            'name' => ['caption' => __('Title'), 'default' => true, 'public' => true],
            'modified' => ['caption' => __('Modified'), 'default' => true, 'public' => false],
            'norm_iri' => ['caption' => __('IRI fragment'), 'width' => 200, 'default' => false, 'public' => true],
            'iri_path' => ['caption' => __('IRI path'), 'width' => 200, 'default' => false, 'public' => true],
            'id' => ['caption' => 'ID', 'width' => 100, 'default' => false]
        ];

        // Merge with columns from the types configuration
        return parent::getColumns($selected, $default, $type);
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
        $params = Attributes::parseQueryParams($requestParameters, $this->parameters, 'articles');

        // Remove conditional parameters
        if (!isset($params['term'])) {
            unset($params['field']);
        }

        // Geodata
        if (!isset($requestParameters['lat']) || !isset($requestParameters['lng'])) {
            unset($params['zoom']);
            unset($params['lat']);
            unset($params['lng']);
        }

        if ((($params['template'] ?? '') !== 'lanes')) {
            unset($params['lanes']);
        }

        // Default modes
        if ($this::$userRole === 'coder') {
            $params['mode'] = MODE_REVISE;
        }
        elseif (($requestAction === 'edit') && !empty($params['published'])) {
            $params['mode'] = MODE_STAGE;
        }

        $params['action'] = $requestAction;
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

        $byTile = !empty($params['tile'] ?? '');

        $defaultOrder = [FIELD_ARTICLES_SIGNATURE => 'ASC'];
        $sortableFields = $this->getSortableFields($columns);

        $limit = $byTile ? 1000 : 100;

        return [
                'order' => $defaultOrder,
                'sortableFields' => $sortableFields,
                'limit' => $limit,
                'maxLimit' => $byTile ? 1000 : 500
            ] + $pagination;
    }

    /**
     * Get filter options including lanes and geodata configuration
     *
     * The geodata configuration in the article type is a dict with
     * itemtypes as keys and extraction paths as values, example:
     *
     *   "geodata" : {
     *     "geolocations" : "value",
     *     "locations" : "property.content"
     *    }
     *
     * @param array $params
     * @return array
     */
    public function getFilter($params)
    {
        $filter = parent::getFilter($params);

        // Add full text search indexes
        $searchIndexes = $this->Items->find('searchIndexes')->toArray();
        $filter['search'] = array_merge($filter['search'], $searchIndexes);

        // Add lanes
        // TODO: only when requested by snippets?
        // TODO: Paginate
        $filter['lanes'] = $this->Items->Properties->find('lanes', $params);

        // Get geodata configuration
        // TODO: What if different article types have different geodata configurations
        //       for the same itemtype?
        if (($params['template'] ?? '') == 'map') {
            $filter['geodata'] = [];

            // Get config from database
            $types = $this->getDatabase()->types[$this->getTable()] ?? [];
            foreach ($types as $typeName => $typeData) {
                $typeConfig = Objects::extract($typeData, 'merged.geodata') ?? [];
                $filter['geodata'] = array_merge($filter['geodata'], $typeConfig);
            }
        }

        return $filter;
    }

    /**
     * Get a summary of the result set
     *
     * Displays information about missing geo data.
     *
     * @param $params
     * @param $template
     * @param $paging
     * @return array
     */
    public function getSummary($params)
    {
        $summary = [];
        if (($params['template'] ?? '') == 'map') {
            $missing = $this
                ->find('hasParams', $params)
                ->find('containFields', $params)
                ->find('hasNoGeolocation', $params)
                ->all()->count();

            if ($missing) {
                $summary[] = __('Skipped {0} not geocoded records.', $missing);
            }

            $summary[] = __('Please note that the data may contain non-validated locations guessed by naive algorithms.');

        }
        return $summary;
    }

    /**
     * Get the list of supported tasks
     *
     * @return string[]
     */
    public function mutateGetTasks(): array
    {

        $tasks = [
            'assign_project' => 'Assign project',
            'assign_property' => 'Assign category',
            'assign_number' => 'Assign article number',
            'assign_iris' => 'Assign IRIs',
            'generate_items' => 'Generate items',
//            'release_collection' => 'Remove from collection',
            'rebuild_tree' => 'Rebuild section order',
            'rebuild_fulltext' => 'Rebuild fulltext index',
            'rebuild_dates' => 'Rebuild dates index',
            'batch_delete' => 'Delete articles',
            'batch_copy' => 'Copy articles'
//            'publish' => 'Set publication state',
        ];

        if (in_array(AppBaseTable::$userRole, ['author', 'editor'])) {
            $tasks = array_intersect_key($tasks, ['assign_project' => true, 'assign_property' => true]);
        }
        elseif (!in_array(AppBaseTable::$userRole, ['admin', 'devel'])) {
            $tasks = [];
        }

        return $tasks;
    }

    /**
     * Mutate entities: Assign entities to another project
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesAssignProject($taskParams, $dataParams, $paging): array
    {
        $offset = $paging['offset'] ?? 0;
        $limit = $paging['limit'] ?? 1;

        $dataParams = $this->parseRequestParameters($dataParams);
        $articles = $this
            ->find('hasParams', $dataParams)
            //->find('containAll', $dataParams)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        /** @var Article $article */
        foreach ($articles as $article) {
            $article->projects_id = (int)$taskParams['target'];
        }

        if (!$this->saveMany($articles)) {
            throw new SaveManyException('Could not save articles.');
        }

        return $articles;
    }

    /**
     * Mutate entities: Save virtual IRIs to the database
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesAssignIris($taskParams, $dataParams, $paging): array
    {
        $offset = $paging['offset'] ?? 0;
        $limit = $paging['limit'] ?? 1;

        $dataParams = $this->parseRequestParameters($dataParams);
        $articles = $this
            ->find('hasParams', $dataParams)
            ->find('containAll', $dataParams)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        /** @var Article $article */
        foreach ($articles as $article) {
            $article->setIri();
            $article->callRecursively('setIri');
        }

        $options = ['associated' => ['Sections', 'Sections.Items', 'Links', 'Footnotes']];
        if (!$this->saveMany($articles, $options)) {
            throw new SaveManyException('Could save articles.');
        }

        return $articles;
    }

    /**
     * Mutate entities: Assign the article to a collection.
     *
     * There must be a single collection section, if missing it will be created.
     * For each property, a single item will be created.
     *
     * // TODO: don't update timestamps?
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesAssignProperty($taskParams, $dataParams, $paging): array
    {
        $offset = (int)$paging['offset'] ?? 0;
        $limit = (int)$paging['limit'] ?? 1;

        $property = $this->Sections->Items->Properties->get($taskParams['target']);
        $sectionType = $taskParams['sectiontype'] ?? SECTIONTYPE_COLLECTION;
        $itemType = $taskParams['itemtype'] ?? ITEMTYPE_COLLECTION;
        $sectionName = $taskParams['sectionname'] ?? __('Collections');

        $setCounter = $taskParams['counter'] ?? false;
        $itemPosition = $taskParams['position'] ?? false;

        $articles = $this->getExportData($dataParams, ['limit' => $limit, 'offset' => $offset]);

        $entities = [];

        /** @var Article $article */
        foreach ($articles as $idx => $article) {

            $articlesId = 'articles-' . $article->id;

            // Get or create section by type
            $targetSection = array_values($article->getSections($sectionType))[0] ?? [];
            if (empty($targetSection)) {
                $sectionId = 'sections/' . $sectionType . '/' . $article->iriFragment;
                $entities[] = [
                    'id' => $sectionId,
                    'articles_id' => $articlesId,
                    'name' => $sectionName
                ];
            }
            else {
                $sectionId = 'sections-' . $targetSection['id'];
            }

            // Get or create item by type
            $propertiesId = 'properties-' . $property->id;

            $targetItems = !empty($targetSection) ? $targetSection->getItemsByType($itemType) : [];
            $targetItems = array_filter($targetItems, fn($item) => $item['properties_id'] === $property->id);
            if (!empty($targetItems)) {
                $targetItem = array_values($targetItems)[0];
                $itemId = 'items-' . $targetItem->id;
                $targetId = $targetItem->id;
            }
            else {
                $iriItemFragment = $taskParams['irifragment'] ?? ($property->propertytype . '~' . $property->iriFragment);
                $itemId = 'items/' . $itemType . '/' . $article->iriFragment . '~' . $iriItemFragment;
                $targetId = null;
            }

            $itemEntity = [
                'id' => $itemId,
                'sections_id' => $sectionId,
                'articles_id' => $articlesId,
                'properties_id' => $propertiesId,
            ];

            // Set article number and item value to the counter value
            // TODO: make length configurable
            $counterNumber = $offset + $idx + 1;
            $counterValue = empty($setCounter) ? false : str_pad($counterNumber, 3, '0', STR_PAD_LEFT);
            if (!empty($setCounter)) {

                // Update item
                $itemEntity['value'] = $counterValue;

                // Update article
                $articleEntity = [
                    'id' => $articlesId,
                    'sortno' => $counterNumber,
                    'signature' => $counterValue
                ];
                $entities[] = $articleEntity;
            }

            // Reorder items
            if (!empty($itemPosition) && ($itemPosition === 'first')) {
                $itemEntity['sortno'] = 1;

                if (!empty($targetSection)) {
                    $otherItems = $targetSection->getItemsByType($itemType);
                    $otherItems = Hash::sort($otherItems, '{n}.sortno');
                    $itemNo = 2;
                    foreach ($otherItems as $itemIdx => $otherItem) {
                        if (empty($targetId) || ($otherItem['id'] !== $targetId)) {
                            $entities[] = ['id' => 'items-' . $otherItem->id, 'sortno' => $itemNo];
                            $itemNo += 1;
                        }
                    }
                }
            }

            $entities[] = $itemEntity;
        }

        $entities = $this->toEntities($entities);
        $result = $this->saveEntities($entities);

        return $articles;
        //TODO show errors
        //$this->addTaskErrors($importBehavior->getErrors());
    }

    /**
     * Mutate entities: Assign article number
     *
     * Add an article number to the signatures section and to the article itself.
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesAssignNumber($taskParams, $dataParams, $paging): array
    {
        $taskParams['irifragment'] = 'articlenumber';
        return $this->mutateEntitiesAssignProperty($taskParams, $dataParams, $paging);
    }

    /**
     * Mutate entities: Generate items
     *
     * TODO: refactor by implementing section and item creation in a separate patchItemValue() function
     *       with a callback to get the new field value
     *
     * ### Task params
     * - sectionname
     * - sectiontype
     * - itemtype
     * - resultfield
     * - statefield
     * - irifragment
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesGenerateItems($taskParams, $dataParams, $paging): array
    {
        $offset = $paging['offset'] ?? 0;
        $limit = $paging['limit'] ?? 1;

        // Prepare service
        $view = new MarkdownView();
        $view->set('database', $this->getDatabase());
        $apiService = ServiceFactory::create('llm');

        $articles = $this->getExportData($dataParams, ['limit' => $limit, 'offset' => $offset]);
        $entities = [];

        // Submit tasks
        /** @var Article $article */
        $tasks = [];
        foreach ($articles as $idx => $article) {

            // Get summary
            $sectionType = $taskParams['sectiontype'];
            $inputText = $view->renderDocument([$article], ['format' => 'md','ignore' => ['sections' => [$sectionType]]]);

            $task = [
                'input' => $inputText,
                'task' => 'summarize'
            ];
            if (!empty($taskParams['prompts'])) {
                $task['prompts'] = $taskParams['prompts'] ?? 'default';
            }
            $tasks[$article->id] = $task;
        }
        $tasks = $apiService->awaitQueries($tasks, 60);

        // Save result
        /** @var Article $article */
        foreach ($articles as $idx => $article) {
            $task = $tasks[$article->id] ?? [];

            if (($task['state'] ?? 'ERROR') !== 'SUCCESS') {
                $error = 'Task not completed (' . ($task['state'] ?? '') . ')';
                $error .= !empty($task['message']) ? (': ' . $task['message']) : '';
                $result = $error;
                // $article->setError('mutate', $error);
            } else {
                $result = $task['result']['answers'][0]['llm_result'] ?? '';
            }

            $resultData =  [
                $taskParams['resultfield'] => $result,
                $taskParams['statefield'] => $task['state'] ?? 'ERROR'
            ];

            // Find property ID
            if (!empty($taskParams['propertytype'])) {
                $property = $this->Items->Properties->find('all')
                    ->select(['id'])
                    ->where([
                        'propertytype' => $taskParams['propertytype'],
                        'lemma' => trim($result)
                    ])
                    ->order(['lft' => 'ASC'])
                    ->first();
                if (!empty($property)) {
                    $resultData['properties_id'] = 'properties-' . $property->id;
                }
            }

            $newEntities = $article->getItemPatch($taskParams, $resultData);
            $entities = array_merge($entities, $newEntities);
        }

        $entities = $this->toEntities($entities);
        $result = $this->saveEntities($entities);

        return $articles;
    }

    /**
     * Mutate entities: Recover the section tree
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesRebuildTree($taskParams, $dataParams, $paging): array
    {
        $offset = $paging['offset'] ?? 0;
        $limit = $paging['limit'] ?? 1;

        $dataParams = $this->parseRequestParameters($dataParams);
        $articles = $this
            ->find('hasParams', $dataParams)
            //->find('containAll', $dataParams)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        /** @var Article $article */
        foreach ($articles as $article) {
            $article->recoverTree();
        }

        return $articles;
    }

    /**
     * Mutate entities: Rebuild fulltext index
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesRebuildFulltext($taskParams, $dataParams, $paging): array
    {
        $offset = $paging['offset'] ?? 0;
        $limit = $paging['limit'] ?? 1;

        $dataParams = $this->parseRequestParameters($dataParams);
        $dataParams['snippets'][] = 'search';
        $dataParams['snippets'][] = 'comments';
        $articles = $this
            ->find('hasParams', $dataParams)
            ->find('containAll', $dataParams)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        $items = [];
        foreach ($articles as $article) {
            /** @var Article $article */
            $items = array_merge($items, $article->updateSearchItems());
        }
        $this->Items->saveMany($items);
        return $articles;
    }

    /**
     * Mutate: Rebuild date fields
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesRebuildDates($taskParams, $dataParams, $paging): array
    {
        $offset = $paging['offset'] ?? 0;
        $limit = $paging['limit'] ?? 1;

        $dataParams = $this->parseRequestParameters($dataParams);

        // Retrieve relevant item types
        $dataParams['itemtypes'] = [];
        $itemTypes = $this->Items->Types->find('all');
        foreach ($itemTypes as $itemType) {
            if (!empty($itemType->config['fields']['date'] ?? false)) {
                $dataParams['itemtypes'][] = $itemType->name;
            }
        }

        $dataParams['itemtypes'] = array_unique($dataParams['itemtypes']);

        if (empty($dataParams['itemtypes'])) {
            return [];
        }

        $articles = $this
            ->find('hasParams', $dataParams)
            ->find('containFields', $dataParams)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        $items = [];
        foreach ($articles as $article) {
            foreach ($article->items as $item) {
                /** @var \Epi\Model\Entity\Item $item */
                if ($item->type->merged['fields']['date'] ?? false) {
                    $item->updateDate();
                    $items[] = $item;
                }
            }
        }

        $this->Items->saveManyFast($items);
        return $articles;
    }

    /**
     * Mutate entities: Delete the selected entities
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesBatchDelete($taskParams, $dataParams, $paging): array
    {
        $offset = $paging['offset'] ?? 0;
        $limit = $paging['limit'] ?? 1;

        $dataParams = $this->parseRequestParameters($dataParams);
        $articles = $this
            ->find('hasParams', $dataParams)
            //->find('containAll', $dataParams)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        foreach ($articles as $article) {
            $this->delete($article);
        }

        return $articles;
    }


    /**
     * Mutate entities: Create copies of the selected entities
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesBatchCopy($taskParams, $dataParams, $paging): array
    {
        $dataParams = $this->parseRequestParameters($dataParams);
        $entities = $this->getExportData($dataParams, $paging, null);

        foreach ($entities as $entity) {

            $transferOptions = ['copy' => true];
            $newData = $entity->getDataForTransfer($transferOptions);
            $newEntities = $this->toEntities($newData);

            $importConfig = [
                'tree' => true,
                'versions' => true,
                'timestamps' => true
            ];

            $result = $this->saveEntities($newEntities, $importConfig);
//            $this->addTaskErrors($importBehavior->getErrors());
        }

        return $entities;
    }

}
