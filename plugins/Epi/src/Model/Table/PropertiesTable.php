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

use App\Model\Behavior\VersionedTreeBehavior;
use App\Model\Entity\Jobs\JobMutate;
use App\Model\Interfaces\ExportTableInterface;
use App\Model\Interfaces\MutateTableInterface;
use App\Model\Interfaces\ScopedTableInterface;
use App\Model\Table\BaseTable as AppBaseTable;
use App\Model\Table\SaveManyException;
use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use Cake\Collection\Collection;
use Cake\Collection\CollectionInterface;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Epi\Model\Entity\Property;
use Epi\Model\Traits\TransferTrait;
use Exception;
use const SORT_ASC;

/**
 * Properties table
 *
 * # Relations
 * @property HasMany|ItemsTable $Items
 *
 * # Behaviors
 * @mixin \Epi\Model\Behavior\PositionBehavior
 * @mixin VersionedTreeBehavior
 */
class PropertiesTable extends BaseTable implements ScopedTableInterface, MutateTableInterface, ExportTableInterface
{

    use TransferTrait;

    /**
     * @var int Default export limit used in TransferTrait
     */
    protected $exportLimit = 500;

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'propertytype';

    /**
     * Scope field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $scopeField = 'propertytype';

    /**
     * Current scope
     *
     * @var null
     */
    public $scopeValue = null;

    /**
     * Whether to check field types after marshalling and merge JSON data
     *
     * @var bool
     */
    public $mergeJson = true;

    /**
     * Store recover operations for the VersionedTreeBehavior
     *
     * @var array
     */
    public $_recoverQueue = [];

    /**
     * Store move operations for the VersionedTreeBehavior
     *
     * @var array
     */
    public $_moveQueue = [];

    /**
     * Request parameter config
     *
     * @var string[]
     */
    public $parameters = [
        'id' => 'list-integer',
        'properties' => 'list-integer',

        'append' => 'raw',
        'empty' => 'raw',
        'manage' => 'raw',
        'published' => 'list-integer',
        'references' => 'list',
        'ancestors' => 'raw',
        'field' => 'string',
        'term' => 'string',

        'find' => 'string',

        'seek' => 'raw',
        'cursor' => 'raw',
        'direction' => 'raw',
        'collapsed' => 'raw',
//        'level' => 'raw',

        'selected' => 'list',
        'selection' => 'raw',
        'columns' => 'list-or-false',
        'load' => 'list',
        'save' => 'list',
        'mode' => 'constant-mode',

        'template' => 'raw',
        'articles' => [
            'projects' => 'list',
            'articletypes' => 'list',
            'field' => 'raw',
            'term' => 'raw',
            'date' => 'string'
        ]
    ];

    /**
     * Search fields, overrides BaseTable->searchFields
     *
     * @var array[]
     */
    public $searchFields = [
        'all' => [
            'caption' => 'Alle',
            'scopes' => [
                'Properties.lemma',
                'Properties.name',
                'Properties.keywords',
                'Properties.norm_iri',
                'Properties.norm_data',
                'Properties.id' => ['type' => 'integer', 'operator' => '=']
            ],
            'default' => true
        ],
        'lemma' => [
            'caption' => '- Bezeichnung',
            'scopes' => ['Properties.lemma', 'Properties.name'],
            'default' => true
        ],
        'keywords' => [
            'caption' => '- Keywords',
            'scopes' => ['Properties.keywords'],
            'configurable' => 'true'
        ],
        'norm_iri' => [
            'caption' => '- IRI Fragment',
            'scopes' => ['Properties.norm_iri'],
            'default' => true
        ],
        'norm_data' => [
            'caption' => '- Norm data',
            'scopes' => ['Properties.norm_data']
        ],
        'id' => [
            'caption' => '- ID',
            'scopes' => ['Properties.id'],
            'default' => true,
            'type' => 'integer',
            'operator' => '='
        ]
    ];

    /**
     * @var array Options for patching entities in the edit action
     */
    public $patchOptions = [
        'associated' => ['LinksFrom'],
        'import' => true,
        'recover' => true,
        'fulltext' => true
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
        $this->setTable('properties');

        $this->addBehavior('Epi.XmlStyles', ['fields' => ['content', 'elements', 'source_from']]);
        $this->addBehavior('VersionedTree', [
            'level' => 'level',
            'recoverOrder' => 'lft',
            'scope' => [$this->scopeField => $this->scopeValue, $this->getAlias() . '.deleted' => 0],
        ]);
        $this->addBehavior('Epi.Position');

        $this->setEntityClass('Epi.Property');

        $this->belongsTo(
            'Parent',
            [
                'className' => 'Epi.Properties',
                'strategy' => BelongsTo::STRATEGY_JOIN,
                'joinType' => Query::JOIN_TYPE_LEFT,
                'foreignKey' => 'parent_id',
            ]
        );

        $this->belongsTo(
            'Preceding',
            [
                'className' => 'Epi.Properties',
                'strategy' => BelongsTo::STRATEGY_JOIN,
                'joinType' => Query::JOIN_TYPE_LEFT,
                'foreignKey' => 'propertytype',
                'bindingKey' => 'propertytype',
                'propertyName' => 'preceding',
                'conditions' => ['Preceding.rght + 1 = Properties.lft']
            ]
        );

        $this->belongsTo(
            'Types',
            [
                'className' => 'Epi.Types',
                'strategy' => BelongsTo::STRATEGY_SELECT,
                'joinType' => Query::JOIN_TYPE_LEFT,
                'foreignKey' => 'propertytype',
                'bindingKey' => 'name',
                'conditions' => ['Types.scope' => 'properties', 'Types.mode' => 'default', 'Types.preset' => 'default']
            ]
        );

        $this->hasMany(
            'Items',
            [
                'className' => 'Epi.Items',
                'foreignKey' => 'properties_id',
                'Items.deleted' => 0
            ]
        );

        // Intext links
        $this->hasMany(
            'LinksTo',
            [
                'className' => 'Epi.Links',
                'foreignKey' => 'to_id',
                'conditions' => ['LinksTo.to_tab' => 'properties']
            ]
        );

        $this->hasMany(
            'LinksFrom',
            [
                'className' => 'Epi.Links',
                'foreignKey' => 'from_id',
                'conditions' => ['LinksFrom.from_tab' => 'properties'],
                'propertyName' => 'links'
            ]
        );

        // There are two types of nodes in the properties table:
        // - lemmas: the related_id is empty.
        // - references: the node connects
        //               a source node (parent_id) and
        //               a target node (related_id)
        //
        // The Reference relations will fetch reference nodes that link two lemmas.
        // In order to get the linked nodes, use the Lookup relations:
        // Verweis unter: contain(['ReferencesFrom','ReferencesFrom.LookupFrom'])
        // Siehe auch: contain(['ReferencesTo','ReferencesTo.LookupTo'])

        // "Verweis unter"
        $this->hasMany(
            'ReferencesFrom',
            [
                'className' => 'Epi.Properties',
                'strategy' => HasOne::STRATEGY_SUBQUERY,
                'foreignKey' => 'related_id',
                'sort' => 'ReferencesFrom.lft'
            ]
        );

        // "Verweis unter"
        $this->belongsTo(
            'LookupFrom',
            [
                'className' => 'Epi.Properties',
                'strategy' => BelongsTo::STRATEGY_JOIN,
                'joinType' => Query::JOIN_TYPE_LEFT,
                'foreignKey' => 'parent_id',
                'propertyName' => 'lookup_from'
            ]
        );


        $this->belongsTo(
            'LookupFromWithAncestors',
            [
                'className' => 'Epi.Properties',
                'foreignKey' => 'parent_id',
                'propertyName' => 'lookup_from',
                'finder' => 'containAncestors'
            ]
        );


        // "Siehe auch"
        $this->hasMany(
            'ReferencesTo',
            [
                'className' => 'Epi.Properties',
                'strategy' => HasOne::STRATEGY_SUBQUERY,
                'foreignKey' => 'parent_id',
                'conditions' => ['ReferencesTo.related_id IS NOT' => null],
                'sort' => 'ReferencesTo.lft'
            ]
        );

        // "Siehe auch"
        $this->belongsTo(
            'LookupTo',
            [
                'className' => 'Epi.Properties',
                'strategy' => BelongsTo::STRATEGY_JOIN,
                'joinType' => Query::JOIN_TYPE_LEFT,
                'foreignKey' => 'related_id',
                'propertyName' => 'lookup_to'
            ]
        );

        $this->belongsTo(
            'LookupToWithAncestors',
            [
                'className' => 'Epi.Properties',
                'foreignKey' => 'related_id',
                'finder' => 'containAncestors',
                'propertyName' => 'lookup_to'

            ]
        );

        // Properties (called base property) can have a property (called meta property)
        $this->belongsTo(
            'MetaProperty',
            [
                'foreignKey' => 'properties_id',
                'className' => 'Epi.Properties',
                'propertyName' => 'property'
            ]
        );

        $this->belongsTo(
            'MetaPropertyWithAncestors',
            [
                'foreignKey' => 'properties_id',
                'className' => 'Epi.Properties',
                'strategy' => BelongsTo::STRATEGY_JOIN,
                'joinType' => Query::JOIN_TYPE_LEFT,
                'propertyName' => 'property',
                'finder' => 'containAncestors'
            ]
        );

        $this->hasMany(
            'BaseProperties',
            [
                'className' => 'Epi.Properties',
                'foreignKey' => 'properties_id'
            ]
        );

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
            ->scalar('propertytype')
            ->maxLength('propertytype', 100);

        $validator
            ->scalar('lemma')
            ->maxLength('lemma', 1500)
            ->allowEmptyString('lemma');

        $validator
            ->scalar('name')
            ->maxLength('name', 1500)
            ->allowEmptyString('name');

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
     * Returns a rules checker object that will be used for validating application integrity
     *
     * @param RulesChecker $rules
     *
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['parent_id'], new PropertiesTable(), ['allowNullableNulls' => true]));

        $rules->addUpdate(function ($entity, $options) {
            return $entity->id !== $entity->parent_id;
        }, ['errorField' => 'parent_id', 'message' => __("Self-links will collapse the universe. Don't.")]);

        return $rules;
    }

    /**
     * Clear the caches after saving
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param $options
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, $options = [])
    {
        parent::afterSave($event, $entity, $options);

        $this->Items->Articles->clearCache();
    }

    /**
     * Get the name of the default property type from types definitions.
     * Defaults to the first property type found in the types, or an empty string.
     *
     * @return mixed|string
     */
    public function getDefaultScope()
    {
        $key = 'default';  //The config key. Set to 'lane' if you want to get the default lane.
        $propertytypes = collection($this->getDatabase()->types['properties'] ?? []);
        $defaultproperty = $propertytypes
            ->filter(function ($type) use ($key) {
                return $type['merged'][$key] ?? false;
            })
            ->first();

        $defaultproperty = $defaultproperty ?? $propertytypes->first();

        return $defaultproperty['name'] ?? '';
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

        $dir = strtolower(($params['direction'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC';

        return [
                'seek' => $params['seek'] ?? null,
                'cursor' => $params['cursor'] ?? null,
                'children' => $params['children'] ?? false,
                'collapsed' => $params['collapsed'] ?? false,
                'order' => ['Properties.lft' => $dir],
                'direction' => $dir,
                'sort' => 'lft',
                'sortableFields' => ['lft'],
                'limit' => 100,
                'maxLimit' => 100
            ] + $pagination;
    }

    /**
     * Extract search parameters from request parameters
     *
     * @param array $requestParameters Request parameters
     * @param string|null $requestPath The property type
     * @param string $requestAction
     * @return array
     */
    public function parseRequestParameters(array $requestParameters = [], $requestPath = '', $requestAction = ''): array
    {
        $params = Attributes::parseQueryParams($requestParameters, $this->parameters, 'properties');
        $params['propertytype'] = empty($requestPath) ? ($requestParameters['propertytype'] ?? $requestParameters['scope'] ?? '') : $requestPath;

        // Article parameters: selected properties
        $other = Attributes::extractPrefixedNestedList($requestParameters, 'properties_');
        // $other usually has no key $params['propertytype'] because it is filtered by JS; clean up?
        $params['selected'] = $other[$params['propertytype']] ?? $params['selected'] ?? []; // To show ticks in checkboxes
        unset($other[$params['propertytype']]);


        $params['articles']['properties'] = $other;
        $params['articles'] = Arrays::array_remove_empty($params['articles']);

        // Update seek parameter from find parameter
        // Redirect to findHasTerm in case the find string starts with **
        if (($params['find'] ?? false) && empty($params['id'])) {
            if (substr($params['find'] ?? '', 0, 2) === '**') {
                $params['term'] = substr($params['find'] ?? '', 2);
            }
            else {
                $params['seek'] = $this->getCursorId($params);
            }
        }

        // Find collapsed option
        // TODO: rename type key in config to 'tree' ?
        if (!isset($requestParameters['collapsed'])) {
            if ((empty($params['seek']) && empty($params['term'])) && empty($params['id']) && empty($params['find']) && empty($params['append'])) {
                $treeType = $this
                    ->getDatabase()
                    ->types['properties'][$params['propertytype']]['merged']['type'] ?? false;

                $params['collapsed'] = $treeType === 'collapsed';
            }
            else {
                $params['collapsed'] = false;
            }
        }

        $params['action'] = $requestAction;
        return Arrays::array_remove_empty($params);
    }

    /**
     * Find a property by a tree find expression
     *
     * Tree find expressions are composed from terms
     * that are concatenated by `>`. Example: un > alb.
     * Alternative separators are `-` or `~`.
     *
     * The lemmata beginning with a term on the correspondig level
     * are matched. You can use * as placeholder to match lemmata
     * beyond their starting characters
     *
     * @param array $params The find expression in 'find'
     * @return int
     */
    protected function getCursorId(array $params)
    {
        $treeTerms = $params['find'] ?? '';
        $treeTerms = str_replace('*', '%', $treeTerms);
        $treeTerms = str_replace(['›', '-', '~'], '>', $treeTerms);
        $treeTerms = array_filter(explode('>', $treeTerms));

        $treeField = $this->getDatabase()->types['properties'][$params['propertytype']]['merged']['displayfield'] ?? '';
        if (empty($treeField)) {
            $hasLemma = !empty($this->getDatabase()->types['properties'][$params['propertytype']]['merged']['fields']['lemma'] ?? false);
            $hasName = !empty($this->getDatabase()->types['properties'][$params['propertytype']]['merged']['fields']['name'] ?? false);
            $treeField = (!$hasLemma && $hasName) ? 'name' : 'lemma';
        }
        $treeField = Attributes::cleanOption($treeField, ['lemma', 'name'], 'lemma');

        $cursorQuery = null;

        while (!empty($treeTerms)) {
            $term = trim(array_shift($treeTerms));

            $parentOperator = is_null($cursorQuery) ? 'parent_id IS' : 'parent_id IN';
            $cursorConditions = [
                'propertytype' => $params['propertytype'],
                $parentOperator => $cursorQuery,
                $treeField . ' LIKE' => $term . '%'
            ];

            $cursorQuery = $this
                ->find('all')
                ->select(['id'])
                ->orderAsc('lft')
                ->where($cursorConditions);
        }

        if ($cursorQuery === null) {
            return null;
        }

        $cursorQuery = $cursorQuery->find('hasArticleOptions', $params);
        $cursor = $cursorQuery->first();
        return $cursor ? $cursor->id : null;
    }

    /**
     * Build sidemenu from categories
     *
     * @param $published
     *
     * @return array
     * @throws \Exception
     */
    public function getMenu($published = false)
    {

        $propertyGroups = $this->getDatabase()->getPropertyConfig();
        $cats = [];

        foreach ($propertyGroups as $groupCaption => $propertyTypes) {
            $cats[] = [
                'spacer' => $groupCaption
            ];
            foreach ($propertyTypes as $propertyType => $propertyTypeCaption) {
                $cats[] = [
                    'label' => $propertyTypeCaption,
                    'url' => ['action' => 'index', $propertyType, '?' => ['load' => true]],
                    'data' => [
                        'data-list-itemof' => "menu-left",
                        'data-id' => $propertyType
                    ]
                ];
            }
        }
        $menu = [
            'caption' => __('Navigation'),
            'activate' => true,
            'scrollbox' => true,
//            'search' => true,
            'grouped' => true,
//            'data-list-name' => 'propertytypes'
//            'tree' => 'foldable'
        ];

        return array_merge($menu, $cats);
    }

    /**
     * Find properties by query parameters
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasParams(Query $query, array $options): Query
    {
        $default = [
            'references' => ['to'],
            'propertytype' => '',
            'term' => '',
            'id' => null,
            'ancestors' => true,
            'published' => null,
            'articles' => []
        ];
        $params = array_merge($default, $options);

        // Contain
        // TODO: Why here? Move to findContain*
        $query = $query->contain(['Types', 'LinksFrom', 'LinksFrom.Properties']);

        //Property type
        $query = $query->where(['Properties.propertytype' => $params['propertytype']]);
        $query = $query->find('withRelated', $params);
        $query = $query->find('hasTerm', $params);
        $query = $query->find('hasArticleOptions', $params);
        $query = $query->find('hasIds', $params);
        $query = $query->find('hasPublicationState', $params);
        $query = $query->find('withAncestors', $params);

        if ($options['articleCount'] ?? true) {
            $query = $query->find('articleCount', $params);
        }
        if ($options['treePositions'] ?? true) {
            $query = $query->find('treePositions');
        }

        return $query;
    }

    /**
     * Find by project ID.
     *
     * Can be chained into the query builder to restrict to projects
     */
    public function findHasProject(Query $query, array $options)
    {
        $projects = $options['projects'] ?? [];

        if (!empty($projects)) {

            $query = $query->distinct();

            $query = $query->innerJoinWith(
                'Items.Articles',
                function (Query $query) use ($projects) {
                    $query = $query->where(['Articles.projects_id IN' => $projects]);
                    return $query;
                }
            );
        }

        return $query;
    }

    /**
     * Find by search term in lemma, name and norm_iri
     *
     * // TODO: implement findSelected instead of mixing it into findTerm
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findHasTerm(Query $query, array $options)
    {

        $term = $options['term'] ?? false;
        $field = $options['field'] ?? 'all';

        if (!empty($term) && !empty($field)) {

            $searchConfig = $this->getConfiguredSearchFields($options)[$field] ?? [];

            $query = $query->find('term', [
                'term' => $term,
                'searchFields' => $searchConfig['scopes'] ?? [],
                'operator' => $searchConfig['operator'] ?? 'LIKE',
                'type' => $searchConfig['type'] ?? 'string',
                'selected' => $options['selected'] ?? []
            ]);
        }

        return $query;
    }


    /**
     * Narrows down the properties to  properties used in items or links by
     * project, term, article type, and other properties
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasArticleOptions(Query $query, array $options)
    {
        $articles_options = $options['articles'] ?? [];

        if ($articles_options) {
            $itemsQuery = $this->Items->Articles
                ->find('hasProject', $articles_options)
                ->find('hasTerm', $articles_options)
                ->find('hasDate', $articles_options)
                ->find('hasArticleType', $articles_options)
                ->find('hasProperties', $articles_options)
                ->innerJoinWith('Items')
                ->select(['Items.properties_id']);

            $linksQuery = $this->Items->Articles
                ->find('hasProject', $articles_options)
                ->find('hasTerm', $articles_options)
                ->find('hasDate', $articles_options)
                ->find('hasArticleType', $articles_options)
                ->find('hasProperties', $articles_options)
                ->innerJoinWith('Links',
                    function ($q) {
                        return $q->where(['Links.to_tab' => 'properties']);
                    })
                ->select(['Links.to_id']);

            $or = [
                ['Properties.id IN' => $itemsQuery],
                ['Properties.id IN' => $linksQuery]
            ];

            // Keep selected properties
            $selected = $options['selected'] ?? [];
            if (!empty($selected)) {
                $or[] = ['Properties.id IN' => $selected];
            }

            $query = $query->where(['OR' => $or]);
        }

        return $query;
    }

    /**
     * Find by ID
     *
     * @param Query $query
     * @param array $options Set 'id' to the property ID.
     * @return Query
     */
    public function findHasIds(Query $query, array $options)
    {
        $propertyId = $options['id'] ?? $options['properties'] ?? null;
        if ($propertyId !== null) {
            $query = $query
                ->where(['Properties.id IN' => $propertyId])
                ->contain(['Preceding']);
        }

        return $query;
    }

    /**
     * Get numbers of articles grouped by property
     *
     * @param $properties_ids
     * @param $articles_options
     * @return array
     */
    protected function _queryArticleCounts($properties_ids, $articles_options)
    {

        $itemsQuery = $this->Items->Articles
            ->find('hasParams', $articles_options)
            ->matching('Items', function ($q) use ($properties_ids) {
                return $q
                    ->where(['Items.properties_id IN' => $properties_ids]);
            })
            ->select([
                'prop_id' => 'Items.properties_id',
                'group_id' => 'Items.articles_id',
                'Articles.id'
            ])
            ->disableHydration();


        $linksQuery = $this->Items->Articles
            ->find('hasProject', $articles_options)
            ->find('hasTerm', $articles_options)
            ->find('hasArticleType', $articles_options)
            ->find('hasProperties', $articles_options)
            ->matching('Links', function ($q) use ($properties_ids) {
                return $q
                    ->where([
                        'Links.to_id IN' => $properties_ids,
                        'Links.to_tab' => 'properties'
                    ]);
            })
            ->select([
                'prop_id' => 'Links.to_id',
                'group_id' => 'Links.root_id'
            ])
            ->disableHydration();

        $articlesQuery = $itemsQuery->all()->append($linksQuery);

        // Aggregate
        return $articlesQuery
            ->groupBy('prop_id')
            ->map(
                function ($value) {
                    //TODO: use this data instead of separate _queryItemsCounts & _queryLinksCounts
                    //      nearly everything should be there.
                    return count(array_unique(array_column($value, 'group_id')));
                }
            )
            ->toArray();
    }

    /**
     * Get number of items grouped by property
     *
     * @param $properties_ids
     * @param $articles_options
     * @return array
     */
    protected function _queryItemCounts($properties_ids, $articles_options)
    {
        // TODO: document the exact scope of resulting items count values
        // (given the articles are filtered on article level)
        $articlesQuery = $this->Items->Articles
            ->find('hasProject', $articles_options)
            ->find('hasTerm', $articles_options)
            ->find('hasArticleType', $articles_options)
            ->find('hasProperties', $articles_options);

        $itemsQuery = $this->Items->Articles->Sections->Items
            ->find('all')
            ->where(['Items.articles_id IN' => $articlesQuery->select('id')])
            ->where(['Items.properties_id IN' => $properties_ids])
            ->select([
                'prop_id' => 'Items.properties_id',
                'group_id' => 'Items.id'
            ])
            ->disableHydration();

        // Aggregate
        return $itemsQuery
            ->all()
            ->groupBy('prop_id')
            ->map(
                function ($value) {
                    return count(array_unique(array_column($value, 'group_id')));
                }
            )
            ->toArray();
    }

    /**
     * Get number of links grouped by property
     *
     * @param $properties_ids
     * @param $articles_options
     * @return array
     */
    protected function _queryLinksCounts($properties_ids, $articles_options)
    {

        // TODO: document the exact scope of resulting links count values
        // (given the articles are filtered on article level)
        $articlesQuery = $this->Items->Articles
            ->find('hasProject', $articles_options)
            ->find('hasTerm', $articles_options)
            ->find('hasArticleType', $articles_options)
            ->find('hasProperties', $articles_options);

        $linksQuery = $this->Items->Articles->Links
            ->find('all')
            ->where(['Links.root_id IN' => $articlesQuery->select('id'), 'Links.root_tab' => 'articles'])
            ->where(['Links.to_id IN' => $properties_ids, 'Links.to_tab' => 'properties'])
            ->select([
                'prop_id' => 'Links.to_id',
                'group_id' => 'Links.id'
            ])
            ->disableHydration();

        // Aggregate
        return $linksQuery
            ->all()
            ->groupBy('prop_id')
            ->map(
                function ($value) {
                    return count(array_unique(array_column($value, 'group_id')));
                }
            )
            ->toArray();
    }


    /**
     * Add article count to query
     *
     * @param Query $query
     * @param array $options
     *
     */
    public function findArticleCount(Query $query, array $options)
    {
        $query = $query->formatResults(
            function (CollectionInterface $results) use ($options) {
                // Get article count for each result row
                $properties_ids = $results->extract('id')->toArray();
                if (empty($properties_ids)) {
                    return $results;
                }

                $articles_options = $options['articles'] ?? [];

                $counts = [
                    'articles' => $this->_queryArticleCounts($properties_ids, $articles_options),
                    'items' => $this->_queryItemCounts($properties_ids, $articles_options),
                    'links' => $this->_queryLinksCounts($properties_ids, $articles_options)
                ];

                // Merge
                return $results->map(
                    function ($row) use ($counts) {
                        $row['articles_count'] = $counts['articles'][$row['id']] ?? 0;
                        $row['items_count'] = $counts['items'][$row['id']] ?? 0;
                        $row['links_count'] = $counts['links'][$row['id']] ?? 0;
                        return $row;
                    }
                );
            }
        );

        return $query;
    }

    /**
     * Find page before and after a given item
     *
     * @param array $options Array containing the key anchor_id.
     *
     * @return Query
     */
    public function findAnchored(Query $query, array $options)
    {
        if ($options['anchor_id'] !== null) {
            $anchor = $this->get($options['anchor_id']);
            //$query = $query->where(['rght >=' =>$anchor->rght])->order(['lft'=>'ASC']);
            $query = $query->where(['lft <=' => $anchor->lft])->order(['lft' => 'DESC']);

        }

        return $query;
    }

    /**
     * Finds all selected properties, regardless of other filter parameters
     *
     * //TODO: implement
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findWithSelected(Query $query, array $options)
    {
//        $query = $query->where(
//            ['Properties.id IN' => $query->select(['Properties.id'])]
//        );
        return $query;
    }

    /**
     * Get self and descendants
     *
     * Can be chained into the query builder. A new query is constructed containing
     * the descendants. The result of this query is returned.
     *
     * @param Query $query
     * @param array $options
     * @return Query The query will expand the collection of properties.
     */
    public function findDescendants(Query $query, array $options)
    {
        // TODO: hydration not necessary for IDs, right?

        $parents = $query
            ->select(['id', 'lft', 'rght', $this->scopeField])
            ->order(['lft' => 'ASC'])
            ->where(['id IN' => $options['ids']]);

        $conditions = [];
        foreach ($parents as $node) {
            if (empty($node['lft'])) {
                continue;
            }
            if (empty($node['rght'])) {
                continue;
            }
            if (empty($node[$this->scopeField])) {
                continue;
            }

            $conditions[] = [
                $this->getAlias() . '.lft >=' => $node['lft'],
                $this->getAlias() . '.rght <=' => $node['rght'],
                $this->getAlias() . '.' . $this->scopeField => $node[$this->scopeField]
            ];

        }

        // TODO: collapse lft/rght conditions where no gaps are in between
        $conditions = array_filter($conditions, fn($x) => !empty($x));

        $nodesQuery = $this->find()
            ->select($this->getAlias() . '.id')
            ->where(['or' => $conditions]);

        return $nodesQuery;
    }

    /**
     * Find properties with related_id
     *
     * By default, only nodes are returned.
     * Include 'to' in the reference option array to retrieve edges as well.
     *
     * ### Options
     * - references (array) An array with the values 'to' or 'from' or an empty array.
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findWithRelated(Query $query, array $options)
    {
        $refs = $options['references'] ?? [];
        if (!in_array('to', $refs)) {
            $query = $query->where(['Properties.related_id IS' => null]);
        } else {
            $query = $query->contain(['LookupToWithAncestors','MetaProperty']);
        }
        return $query;
    }

    /**
     * Get ancestors and self
     *
     * Can be chained into the query builder. A new query is constructed containing
     * the ancestors. The result of this query is returned.
     *
     * @param Query $query
     * @param array $options
     * @return Query The query will expand the collection of properties.
     */
    public function findWithAncestors(Query $query, array $options)
    {
        if (empty($options['ancestors'])) {
            return $query;
        }

        // Signal to the paginator that the query will be replaced later
        // Prevents level filtering.
        $query = $query->applyOptions(['expand' => true]);

        return $query->formatResults(
            function (CollectionInterface $results) use (&$query, $options) {
                $hydrate = $query->isHydrationEnabled();
                $scopeField = $this->scopeField;

                $conditions = $results->map(
                    function ($node) use ($scopeField) {
                        if (empty($node['lft'])) {
                            return [];
                        }
                        if (empty($node['rght'])) {
                            return [];
                        }
                        if (empty($node[$scopeField])) {
                            return [];
                        }

                        return [
                            $this->getAlias() . '.lft <=' => $node['lft'],
                            $this->getAlias() . '.rght >=' => $node['rght'],
                            $this->getAlias() . '.' . $scopeField => $node[$scopeField]
                        ];
                    }
                )->toList();

                // TODO: collapse lft/rght conditions where no gaps are in between
                $conditions = array_filter(
                    $conditions,
                    function ($x) {
                        return !empty($x);
                    }
                );

                $ancestors = new Collection([]);
                if (!empty($conditions)) {
                    $ancestorsQuery = $this->find();

                    $collapsedLevel = $query->getOptions()['collapsedLevel'] ?? null;
                    if (isset($collapsedLevel)) {
                        $ancestorsQuery = $ancestorsQuery->where([$this->getAlias() . '.level <=' => $collapsedLevel]);
                    }

                    // TODO: replace by contain option
                    $refs = $options['references'] ?? [];
//                    if (in_array('to', $refs)) {
//                        $ancestorsQuery = $ancestorsQuery
//                            ->contain(['ReferencesTo', 'ReferencesTo.LookupToWithAncestors','ReferencesTo.MetaProperty']);
//                    }
                    if (in_array('from', $refs)) {
                        $ancestorsQuery = $ancestorsQuery
                            ->contain(['ReferencesFrom', 'ReferencesFrom.LookupFromWithAncestors','ReferencesFrom.MetaProperty']);
                    }

                    $contain = $query->getContain();
                    if (!empty($contain)) {
                        $ancestorsQuery = $ancestorsQuery->contain($contain);
                    }

                    $ancestorsQuery = $ancestorsQuery->where(['or' => $conditions])
                        ->order($this->getAlias() . '.lft')
                        ->enableHydration($hydrate)
                        ->all();

                    $ancestors = $ancestors->append($ancestorsQuery);
                }

                $ancestors = $ancestors->indexBy('id');
                return $ancestors;
            }
        );
    }

    /**
     * Expand the result set to include ancestors and self.
     *
     * Similar to findWithAncestors, but pagination is not
     * applied to the original dataset, but to the ancestors.
     *
     * @deprecated Use findWithAncestors instead.
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findExpandAncestors(Query $query, array $options)
    {
        $hydrate = $query->isHydrationEnabled();
        $scopeField = $this->scopeField;

        $conditions = $query->map(
            function ($node) use ($scopeField) {
                if (!isset($node['lft'])) {
                    return [];
                }
                if (!isset($node['rght'])) {
                    return [];
                }
                if (empty($node[$scopeField])) {
                    return [];
                }

                return [
                    $this->getAlias() . '.lft <=' => $node['lft'],
                    $this->getAlias() . '.rght >=' => $node['rght']
//                    $this->getAlias() . '.' . $scopeField => $node[$scopeField]
                ];
            }
        )->toList();

        // TODO: collapse lft/rght conditions where no gaps are in between
        $conditions = array_filter(
            $conditions,
            function ($x) {
                return !empty($x);
            }
        );

        if (empty($conditions)) {
            return $query;
        }

        $ancestorsQuery = $this
            ->find()
            ->where([$this->getAlias() . '.related_id IS' => null])
            ->where([$this->getAlias() . '.' . $scopeField => $options['propertytype'] ?? '']);

        // TODO: replace by contain option
        $refs = $options['references'] ?? [];
        if (in_array('to', $refs)) {
            $ancestorsQuery = $ancestorsQuery
                ->contain(['ReferencesTo', 'ReferencesTo.LookupToWithAncestors']);
        }
        if (in_array('from', $refs)) {
            $ancestorsQuery = $ancestorsQuery
                ->contain(['ReferencesFrom', 'ReferencesFrom.LookupFromWithAncestors']);
        }

        $contain = $query->getContain();
        if (!empty($contain)) {
            $ancestorsQuery = $ancestorsQuery->contain($contain);
        }

        $ancestorsQuery = $ancestorsQuery->where(['or' => $conditions]);

        $ancestorsQuery = $ancestorsQuery
            ->order($this->getAlias() . '.lft')
            ->enableHydration($hydrate);

        return $ancestorsQuery;
    }


    /**
     * Get ancestors from lft/rght values for a set of given nodes
     *
     * Add a list of properties to $options['nodes'].
     * Each property needs level, lft, rght and propertytype values
     *
     * @param array $options
     */
    public function findAncestorsFor(Query $query, array $options)
    {
        $nodes = $options['nodes'] ?? [];

        $conditions = [];
        foreach ($nodes as $node) {
            if (empty($node['lft'])) {
                continue;
            }
            if (empty($node['rght'])) {
                continue;
            }
            if (empty($node['propertytype'])) {
                continue;
            }

            $conditions[] = [
                'Properties.lft <' => $node['lft'],
                'Properties.rght >' => $node['rght'],
                'Properties.propertytype' => $node['propertytype']
            ];
        }

        $query = $query->where(['or' => $conditions]);

        return $query;
    }

    /**
     * Remove list of nodes from result (avoids duplicates with ancestors)
     *
     * Add a list of properties to $options['nodes']
     *
     * @param array $options
     */
    public function findWithout(Query $query, array $options)
    {
        $ids = $options['ids'] ?? [];
        if (empty($ids)) {
            return $query;
        }
        else {
            return $query->where(['Properties.id NOT IN' => $ids]);
        }
    }


    /**
     * Get reference nodes that link from other nodes to given nodes (verweis unter)
     *
     * Add a list of property IDs to $options['nodes'].
     *
     * @param array $options
     */
    public function findReferencesFrom(Query $query, array $options)
    {
        $ids = $options['nodes'] ?? [];
        $query = $query
            ->where([$this->getAlias() . '.related_id IN' => $ids])
            ->contain(['LookupFromWithAncestors']);

        return $query;
    }

    /**
     * Get reference nodes that link from given nodes to other nodes (siehe auch)
     *
     * Add a list of property IDs to $options['nodes'].
     *
     * @param array $options
     */
    public function findReferencesTo(Query $query, array $options)
    {
        $ids = $options['nodes'] ?? [];
        $query = $query
            ->where(
                [
                    $this->getAlias() . '.parent_id IN' => $ids,
                    $this->getAlias() . '.related_id IS NOT' => null
                ]
            )
            ->contain(['LookupToWithAncestors']);

        return $query;
    }

    /**
     * Get articles that are linked to the given property
     *
     * ### Options
     * - reference (int) The property ID
     *
     * @param Query $query
     * @param array $options
     * @return Query
     * @throws Exception
     */
    public function findArticles(Query $query, array $options)
    {
        $reference = $options['reference'] ?? null;
        if (empty($reference)) {
            throw new Exception('Missing reference node');
        }

        $articlesTable = $this->fetchTable('Epi.Articles');
        $articlesQuery = $articlesTable
            ->find('hasProperties', ['properties' => $reference])
            ->contain(['Projects'])
            ->limit(10);
        return $articlesQuery;
    }

    /**
     * Get properties with the same lemma (or name if configured as displayfield)
     *
     * Pass the reference property in the reference key of the $options array.
     *
     * ### Options
     * - reference The reference property entity
     * - samelevel (bool) Whether to find only properties on the same level (default false)
     *
     * @param array $options
     */
    public function findHomonyms(Query $query, array $options)
    {
        $reference = $options['reference'] ?? null;
        if (empty($reference)) {
            throw new Exception('Missing reference node');
        }

        if (is_null($reference->level)) {
            throw new Exception('Missing level, repair the tree!');
        }

        if (is_null($reference->propertytype)) {
            throw new Exception('Missing property type, repair the property!');
        }

        $displayfield = Attributes::cleanOption(
            $reference->type->config['mergefield'] ??
            $reference->type->config['displayfield'] ?? 'lemma',
            ['lemma', 'name'],
            'lemma'
        );

        $conditions = [
            'id <>' => $reference->id,
            'deleted' => 0,
            'level' => $reference->level,
            'propertytype' => $reference->propertytype,
            $displayfield . ' <>' => '',
            $displayfield . ' IS NOT' => null,
            'LOWER(' . $displayfield . ')' => mb_strtolower($reference->{$displayfield})
        ];

        // TODO: check ancestors

        if (!empty($options['samelevel'])) {
            if (empty($reference->parent_id)) {
                $conditions['parent_id IS'] = null;
            }
            else {
                $conditions['parent_id'] = $reference->parent_id;
            }
        }


        return $query->find('containAncestors')
            ->where($conditions);
    }

    public function findMerged(Query $query, array $options)
    {
        $reference = $options['mergedto_id'] ?? null;
        if (empty($reference)) {
            throw new Exception('Missing reference node');
        }

        return $query
            ->find('deleted', ['deleted' => 1])
            ->find('containAncestors')
            ->where([
                'Properties.mergedto_id' => $reference,
            ]);
    }

    /**
     * Find the selected properties
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findLanes(Query $query, array $options): Query
    {
        $query = $this->find('hasArticleOptions', $options)
            ->orderAsc('Properties.lft')
            ->where(['Properties.related_id IS' => null]);


        // Get from properties
        $lanes = $options['lanes'] ?? false;
        $propertyType = empty($lanes) ? $this->getDefaultScope() : $lanes;
        $selectedProperties = $options['properties'][$propertyType]['selected'] ?? $options['properties'][$propertyType] ?? [];

        $query = $query->where(['Properties.propertytype' => $propertyType]);


        if (empty($selectedProperties)) {
            $query = $query->where(['1=0']);
        }
        else {
            $query = $query->where(['Properties.id IN' => $selectedProperties]);
        }

        return $query;
    }

    /**
     * Find the complete property
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findContainAll(Query $query, array $options): Query
    {
        $query = $query
            ->find('containAncestors')
            ->contain([
                'LookupToWithAncestors',
                'MetaProperty',
                'Parent',
                'LinksFrom',
                'LinksFrom.Properties',
                'Types'
            ]);
        return $query;
    }

    /**
     * Returns a (paginatable) query that retrieves properties and all
     * their children.
     *
     * @deprecated Use findAncestors instead
     *
     * TODO In case the target DBMS supports it, consider using common table expressions after upgrading to CakePHP 4.1+
     *
     * @param string $propertytype The propertytype for which to retrieve the properties.
     * @return Query
     */
    function getPropertiesWithChildren($propertytype)
    {
        // select all root nodes (of a specific property type) first,
        // so that the paginator can apply an offset and a limit
        // on the query.
        $query = $this
            ->find()
            ->select(['Properties.id'])
            ->where(
                [
                    'Properties.propertytype' => $propertytype,
                    'Properties.parent_id IS' => null,
                    'Properties.lft IS NOT' => null,
                    'Properties.rght IS NOT' => null,
                ]
            )
            ->orderAsc('Properties.lft')
            ->formatResults(
                function (CollectionInterface $results) use ($propertytype, &$query) {
                    // use the (possibly paginated) root nodes from the previous
                    // query to select all those nodes with all their children,
                    // so that proper partial-trees are being retrieved where the
                    // children aren't being separated from their parents.
                    if (!$results->count()) {
                        return $results;
                    }

                    $rootNodesQuery = $this
                        ->find()
                        ->select(['RootNodes.id'])
                        ->from(['RootNodes' => (clone $query)->select(['id' => 'Properties.id'], true)]);

                    $formatQuery = $this
                        ->find()
                        ->select(['articles_count' => 0])
                        ->enableAutoFields()
                        ->contain('Reference')
                        ->contain('Lookup')
                        ->from(['RootNodes' => 'properties'])
                        ->innerJoin(
                            ['Properties' => 'properties'],
                            function (QueryExpression $exp, Query $query) use ($propertytype) {
                                return $exp->and(
                                    [
                                        $exp->between(
                                            'Properties.lft',
                                            $query->identifier('RootNodes.lft'),
                                            $query->identifier('RootNodes.rght')
                                        ),
                                        'Properties.propertytype' => $propertytype
                                    ]
                                );
                            }
                        )
                        ->where(
                            [
                                'RootNodes.id IN' => $rootNodesQuery,
                                'RootNodes.propertytype' => $propertytype,
                                'RootNodes.parent_id IS' => null,
                                'RootNodes.lft IS NOT' => null,
                                'RootNodes.rght IS NOT' => null,
                            ]
                        )
                        ->orderAsc('Properties.sortno')
                        ->orderAsc('Properties.lft');

                    if (!$query->isHydrationEnabled()) {
                        $formatQuery->disableHydration();
                    }

                    /*
                    SELECT properties.id, items.id, items.sections_id, sections.id, sections.name, articles.id
                    FROM properties
                    INNER JOIN items ON items.properties_id = properties.id
                    INNER JOIN sections ON sections.id = items.sections_id
                    INNER JOIN articles ON articles.id = items.articles_id AND articles.projects_id = 3
                    WHERE properties.id = 329
                    */
                    // TODO should this functionality be in a separate method, as it is currently only used in the project view's properties selector?
                    $projectId = $query->getOptions()['projectId'] ?? null;

                    $articlesCountQuery = clone $formatQuery;
                    $articlesCountQuery
                        ->select(
                            function (Query $query) {
                                return [
                                    'Properties.id',
                                    // TODO is this the correct way to avoid duplicates? or is a different join required?
                                    // TODO there seem to be duplicate articles where two or more different items do reference the same article, but the items have different section IDs
                                    'articles_count' => $query->func()->count(
                                        'Articles.id'
                                    //$query->func()->distinct(['Articles.id' => 'identifier'])
                                    ),
                                ];
                            },
                            true
                        )
                        ->disableAutoFields()
                        ->innerJoinWith(
                            'Items.Articles',
                            function (Query $query) use ($projectId) {
                                if ($projectId) {
                                    // TODO this kills the performance, a compound index on id and projects_id seems to help sometimes
                                    // TODO paginating is an extra performance killer on top... for whatever reason
                                    // @maxikopp: guess this is fixed?
                                    $query->where(
                                        [
                                            'Articles.projects_id' => $projectId,
                                        ]
                                    );
                                }

                                return $query;
                            }
                        )
                        /*->innerJoin([
                            'Items' => 'items',
                        ], [
                            'Items.properties_id = Properties.id',
                        ])
                        ->innerJoin([
                            // TODO this is not compatible with automatic identifier quoting
                            'Articles FORCE INDEX FOR JOIN (id_projects_id)' => 'articles',
                        ], function () use ($projectId) {
                            $conditions = [
                                'Articles.id = Items.articles_id',
                            ];

                            if ($projectId) {
                                $conditions['Articles.projects_id'] = $projectId;
                            }

                            return $conditions;
                        })*/
                        ->group('Properties.id')
                        ->disableHydration();

                    $properties = $formatQuery->indexBy('id')->toArray();
                    foreach ($articlesCountQuery as $articlesCount) {
                        if (isset($properties[$articlesCount['id']])) {
                            $properties[$articlesCount['id']]['articles_count'] = $articlesCount['articles_count'];
                        }
                    }

                    return collection($properties);
                }
            );

        return $query;
    }

    /**
     * Returns a (non-paginatable) query that retrieves properties
     * matching the given search term, including their parents
     * and children.
     *
     * @deprecated Use findAncestors instead
     * TODO In case the target DBMS supports it, consider using common table expressions after upgrading to CakePHP 4.1+
     *
     * The result will also include the properties (and their parents)
     * that are referenced via the `verweis_id` column.
     *
     * @param string $propertytype The property type from which to retrieve the properties.
     * @param string $searchTerm The search term.
     * @return Query
     *
     */
    function searchPropertiesWithChildren($propertytype, $searchTerm)
    {
        // select the properties matching the search term, and include
        // all of their parents, up to and including the root nodes.
        $ancestorIdsQuery = $this
            ->find()
            ->select(['id' => 'Properties.id'])
            ->from(['AnchorNodes' => 'properties'])
            ->innerJoin(
                ['Properties' => 'properties'],
                function (QueryExpression $exp, Query $query) use ($propertytype) {
                    return $exp->and(
                        [
                            $exp->between(
                                'AnchorNodes.lft',
                                $query->identifier('Properties.lft'),
                                $query->identifier('Properties.rght')
                            ),
                            'Properties.propertytype' => $propertytype
                        ]
                    );
                }
            )
            ->where(
                [
                    'AnchorNodes.lemma LIKE ' => '%' . $searchTerm . '%',
                    'AnchorNodes.propertytype' => $propertytype,
                ]
            )
            ->group('Properties.id');

        // find the properties matching the search term, and select
        // all their children, ie excluding the nodes matching the
        // search term, as they are already being selected in the
        // ancestor query.
        $descendantIdsQuery = $this
            ->find()
            ->select(['id' => 'Properties.id'])
            ->from(['AnchorNodes' => 'properties'])
            ->innerJoin(
                ['Properties' => 'properties'],
                function (QueryExpression $exp, Query $query) use ($propertytype) {
                    return $exp->and(
                        [
                            'Properties.lft >' => $query->identifier('AnchorNodes.lft'),
                            'Properties.lft <=' => $query->identifier('AnchorNodes.rght'),
                            'Properties.propertytype' => $propertytype
                        ]
                    );
                }
            )
            ->where(
                [
                    'AnchorNodes.lemma LIKE ' => '%' . $searchTerm . '%',
                    'AnchorNodes.propertytype' => $propertytype
                ]
            )
            ->group('Properties.id');

        // combine both queries in a UNION to retrieve a merged result
        // set that includes parents and children without duplicates (
        // which could occur as the selected ancestor and descendant
        // paths can overlap when the search term in the ancestor query
        // matches a property that will also be selected in the
        // descendant query, and vice versa when the search term in the
        // descendant query matches a property that will also be selected
        // in the ancestor query).
        $allIdsUnionQuery = $ancestorIdsQuery->union($descendantIdsQuery);

        //debug($allIdsUnionQuery->toArray());
        // obtain all ids in a subquery compatible select for use in the
        // main query, where finally all properties columns are fetched
        // in a `ONLY_FULL_GROUP_BY` compliant manner.
        //
        // TODO Our initial queries are functional dependency compatible, but MariaDB doesn't support that yet https://jira.mariadb.org/browse/MDEV-11588
        $allIdsQuery = $this
            ->find()
            ->select(['Properties.id'])
            ->from(['Properties' => $allIdsUnionQuery]);
        // debug($allIdsQuery->toArray());
        return $this
            ->find()
            ->contain('Reference')
            ->contain('Lookup')
            ->where(
                [
                    'Properties.id IN' => $allIdsQuery
                ]
            )
            ->formatResults(
                function (CollectionInterface $results) use ($propertytype) {
                    // the union query will only select the parents and children of
                    // the properties matching the search term, but we also need to
                    // include the properties (and their parents) that match the
                    // `verweis_id`s that exist in our results.
                    //
                    // some of them may already exist in the result set, but most of
                    // the time they live in root nodes (or are root nodes themselves)
                    // that do not belong to the properties matching the search term.

                    $verweisIds = $results
                        ->extract('related_id')
                        ->filter(
                            function ($value) {
                                return $value !== null;
                            }
                        )
                        ->toArray();
                    $verweisIds = array_unique($verweisIds);

                    if (empty($verweisIds)) {
                        return $results;
                    }

                    $existingIds = $results
                        ->extract('id')
                        ->toArray();

                    // select all the not already selected properties matching
                    // the `verweis_id`s found in the results of the union query,
                    // and include all of their parents, up to and including the
                    // root node.
                    $ancestorIdsQuery = $this
                        ->find()
                        ->contain('Reference')
                        ->contain('Lookup')
                        ->select(['Properties.id'])
                        ->from(['AnchorNodes' => 'properties'])
                        ->innerJoin(
                            ['Properties' => 'properties'],
                            function (QueryExpression $exp, Query $query) use ($propertytype) {
                                return $exp->and(
                                    [
                                        $exp->between(
                                            'AnchorNodes.lft',
                                            $query->identifier('Properties.lft'),
                                            $query->identifier('Properties.rght')
                                        ),
                                        'Properties.propertytype' => $propertytype
                                    ]
                                );
                            }
                        )
                        ->where(
                            [
                                'AnchorNodes.id IN' => $verweisIds,
                                'AnchorNodes.id NOT IN' => $existingIds,
                                'AnchorNodes.propertytype' => $propertytype,
                            ]
                        )
                        ->group('Properties.id');

                    // obtain all properties with all columns based on the id only
                    // query, in order to comply with the `ONLY_FULL_GROUP_BY`
                    // restrictions.
                    //
                    // TODO Our initial queries are functional dependency compatible, but MariaDB doesn't support that yet https://jira.mariadb.org/browse/MDEV-11588
                    $ancestorQuery = $this
                        ->find()
                        ->where(
                            [
                                'Properties.id IN' => $ancestorIdsQuery,
                            ]
                        );

                    return $results
                        ->append($ancestorQuery->all())
                        ->sortBy('sortno', SORT_ASC);
                }
            );
    }


    /**
     * Find record by propertytype and norm_iri,
     * set ID. Used for CSV import.
     *
     * @param $properties
     * @param $segment
     * @return mixed
     *
     */
    public function lookupIds($properties, $segment)
    {
        foreach ($properties as $key => $property) {
            if (empty($property['norm_iri'])) {
                continue;
            }

            $item = $this->find('all')->where(
                [
                    'propertytype' => $segment,
                    'norm_iri' => $property['norm_iri']
                ]
            )->first();
            if (!empty($item)) {
                $properties[$key]['id'] = $item->id;
            }
        }

        return $properties;
    }

    /**
     * Get filter options
     *
     * @param array $params
     * @return array
     */
    public function getFilter($params)
    {
        $filter = parent::getFilter($params);

        $projectsTable = TableRegistry::getTableLocator()->get('Epi.Projects');
        $projects = $projectsTable-> find('list')->toArray();
        $filter['projects'] = $projects;

        return $filter;
    }

    protected function getConfiguredSearchFields($params = []) {

        if (!($params['propertytype'] ?? false)) {
            return $this->searchFields;
        }

        $fieldsConfig = $this->getDatabase()->types['properties'][$params['propertytype']]['merged']['fields'] ?? [];
        if (empty($fieldsConfig)) {
            return $this->searchFields;
        }

        // default search fields
        $searchFields = [];
        foreach ($this->searchFields as $field => $spec) {
            if ($spec['default'] ?? false) {
                $candidate = array_slice($spec, 0);
                if ($fieldsConfig[$field]['caption'] ?? false) {
                    $candidate['caption'] = '- ' . $fieldsConfig[$field]['caption'];
                    unset($fieldsConfig[$field]);
                }
                else {
                    if ($fieldsConfig[$field] ?? false) {
                        $candidate['caption'] = '- '. $fieldsConfig[$field];
                        unset($fieldsConfig[$field]);
                    }
                }
                $searchFields[$field] = $candidate;
            }
        }

        // configured search fields
        $baseScope = ucfirst($this->getTable());
        $contentSearchFields = [];
        foreach($fieldsConfig as $field => $config) {
            if (($config['caption'] ?? false) && ($config['searchfield'] ?? false)) {
                $caption = $config['caption'];
                $contentSearchFields[$field] = [
                    'caption' => '- '. $caption,
                    'scopes' => [$baseScope . '.' . $field]
                ];
            }
        }

        //  group configured search fields
        if (!empty($contentSearchFields)) {
            $scopeElements = array_column($contentSearchFields, 'scopes');
            $mergedScopes = array_merge(...$scopeElements);
            $searchFields['groupedContent'] = [
                'caption' => 'Inhalt', // __('Content'),
                'scopes' => Arrays::array_unique_mixed($mergedScopes)
            ];
        }
        $searchFields = array_merge($searchFields, $contentSearchFields);

        // set scopes for 'all'
        if ($searchFields['all'] ?? false) {
            $scopeElements = array_column($searchFields, 'scopes');
            $mergedScopes = array_merge(...$scopeElements);
            $searchFields['all']['scopes'] = Arrays::array_unique_mixed($mergedScopes);
        }

        return $searchFields;

    }

    /**
     * Get all scopes
     *
     * implements ScopedTableInterface
     *
     * @return array|false
     */
    public function getScopes(): array
    {
        $this->removeScope();
        $scopes = $this->find()
            ->select(['Properties.propertytype'])
            ->where(
                [
                    'Properties.deleted' => 0,
                    'Properties.propertytype IS NOT' => null,
                ]
            );

        $scopes = $scopes
            ->distinct('Properties.propertytype')
            ->all()
            ->extract('propertytype')
            ->toArray();

        return $scopes;
    }

    /**
     * Get scope
     *
     * implements ScopedTableInterface
     *
     * @return string
     */
    public function getScope(): string
    {
        return $this->scopeValue;
    }

    /**
     * Enable tree behavior and set scope
     *
     * @implements ScopedTableInterface
     * @param $scope
     * @return \Cake\ORM\Table
     */
    public function setScope($scope = null): \Cake\ORM\Table
    {
        if ($scope instanceof EntityInterface) {
            // Quick fix: Only if the scope field was set in the entity
            if (!$scope->hasValue($this->scopeField)) {
                return $this;
            }

            $this->setScope($scope->{$this->scopeField});
            return $this;
        }

        if ($this->behaviors()->has('VersionedTree')) {

            if (is_null($scope)) {
                $scopeCondition = [$this->getAlias() . '.propertytype IS' => null, $this->getAlias() . '.deleted' => 0];
            }
            else {
                $scopeCondition = [$this->getAlias() . '.propertytype' => $scope, $this->getAlias() . '.deleted' => 0];
            }

            $this->behaviors()->VersionedTree->setConfig('scope', $scopeCondition, false);
        }

        $this->scopeValue = $scope;
        return $this;
    }


    /**
     *  Disable tree behavior and, thus, remove scope
     *
     *  implements ScopedTableInterface
     * @return Table
     */
    public function removeScope(): Table
    {
        if ($this->behaviors()->has('VersionedTree')) {
            $this->behaviors()->VersionedTree->setConfig('scope', null);
        }
        $this->scopeValue = null;
        return $this;
    }

    /**
     * Update all references to the source properties to the target property
     *
     * @param mixed $propertyTargetId
     * @param array $propertySourceIds
     * @return int The number of affected rows
    */
    protected function rewire(mixed $propertyTargetId, array $propertySourceIds): int
    {
        // Change references of the items
        $this->Items->updateAllWithVersion(
            ['properties_id' => $propertyTargetId],
            ['properties_id IN' => $propertySourceIds, 'deleted' => 0]
        );

        // Change references of the links
        $this->LinksTo->updateAllWithVersion(
            ['to_id' => $propertyTargetId],
            ['to_id IN' => $propertySourceIds, 'to_tab' => 'properties', 'deleted' => 0]
        );

        $this->LinksFrom->updateAllWithVersion(
            ['from_id' => $propertyTargetId],
            ['from_id IN' => $propertySourceIds, 'from_tab' => 'properties', 'deleted' => 0]
        );

        // Change references of the merged property
        // TODO: update lft/rght
        $this->updateAllWithVersion(
            ['parent_id' => $propertyTargetId],
            ['parent_id IN' => $propertySourceIds, 'deleted' => 0]
        );

        $this->updateAllWithVersion(
            ['related_id' => $propertyTargetId],
            ['related_id IN' => $propertySourceIds, 'deleted' => 0]
        );

        $this->updateAllWithVersion(
            ['properties_id' => $propertyTargetId],
            ['properties_id IN' => $propertySourceIds, 'deleted' => 0]
        );

        // Delete merged property
        // TODO: update lft/rght
        return $this->updateAllWithVersion(
            ['deleted' => 1, 'mergedto_id' => $propertyTargetId],
            ['id IN' => $propertySourceIds, 'deleted' => 0]
        );
    }

    /**
     * Rewire all references to child properties to the parent property
     *
     * @param Property $property
     * @return bool Whether the operation was successful
     */
    public function resolve($property) : bool {
        $connection = $this->getConnection();
        $connection->begin();
        try {
            $result =  $this->rewire($property->parent_id, [$property->id]);
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            throw new Exception(__('Could not merge properties: {0}', [$e->getMessage()]));
        }
        return $result > 0;
    }

    /**
     * Merge properties
     *
     * ### Options:
     * - preview: (bool) Whether to preview or actually perform the merge (default false).
     * - concat: (bool) Whether to combine the content of all properties (default false).
     *
     * @param integer $propertyTargetId The ID of the target property.
     * @param integer[] $propertySourceIds A list of source properties.
     * @param array $options
     * @return Property|null Returns the preview, if concat or preview are true.
     * @throws Exception If the merge fails.
     */
    public function merge($propertyTargetId, $propertySourceIds, $options)
    {
        if (($options['concat'] ?? false) || ($options['preview'] ?? false)) {
            /** @var Property $propertyTarget */
            $propertyTarget = $this->get($propertyTargetId);
            $propertyTarget->merged_ids = $propertySourceIds;
        }
        else {
            $propertyTarget = null;
        }

        // Merge field content
        if ($options['concat'] ?? false) {
            $propertySources = $this->find('all')
                ->where(['Properties.id IN' => $propertySourceIds]);

            // Merge fields
            $concatFields = [
                'lemma',
                'name',
                'sortkey',
                'signature',
                'file_name',
                'unit',
                'comment',
                'content',
                'elements',
                'keywords',
                'source_from',
                'norm_data' => "\n"
            ];

            foreach ($concatFields as $fieldName => $separator) {
                if (is_numeric($fieldName)) {
                    $fieldName = $separator;
                    $separator = ' / ';
                }

                $mergedValues = [trim($propertyTarget[$fieldName] ?? '')];
                foreach ($propertySources as $propertySource) {
                    $sourceValue = trim($propertySource[$fieldName] ?? '');
                    if (!in_array(mb_strtolower($sourceValue), array_map('mb_strtolower', $mergedValues))) {
                        $mergedValues[] = $propertySource[$fieldName];
                    }
                }
                $mergedValues = implode($separator, array_unique(array_filter($mergedValues)));
                $propertyTarget[$fieldName] = $mergedValues;
            }

            // Don't merge IRIs if they are different
            foreach ($propertySources as $propertySource) {
                if ($propertySource->norm_iri !== $propertyTarget->norm_iri) {
                    $propertyTarget->norm_iri = null;
                    break;
                }
            }
        }

        // Perform merge
        if (!($options['preview'] ?? false)) {
            $connection = $this->getConnection();
            $connection->begin();
            try {

                // Create a new property with the merged content
                if ($options['concat'] ?? false) {

                    $propertySourceIds[] = $propertyTargetId;
                    $propertyTarget = $this->createCopy($propertyTarget);
                    if (empty($propertyTarget)) {
                        throw new Exception(__('Could not create merged property'));
                    }
                    $propertyTargetId = $propertyTarget->id;
                }

                // Rewire the references to the old properties
                $this->rewire($propertyTargetId, $propertySourceIds);

                $connection->commit();

            } catch (Exception $e) {
                $connection->rollback();
                throw new Exception(__('Could not merge properties: {0}', [$e->getMessage()]));
            }
        }

        return $propertyTarget;
    }

    /**
     * Get columns
     *
     * TODO: keep all definitions, indexed by: articletype, default, query parameter
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
            'lemma' => ['caption' => __('Lemma'), 'width' => 200, 'default' => true, 'public' => true],
            'name' => ['caption' => __('Name'), 'width' => 200, 'default' => false],
            'sortkey' => ['caption' => __('Sort key'), 'width' => 200, 'default' => false],
            'ishidden' => ['caption' => __('Hide name'), 'width' => 50, 'default' => false],
            'iscategory' => ['caption' => __('Group'), 'width' => 50, 'default' => false],
            'comment' => ['caption' => __('Comment'), 'width' => 200, 'default' => false],
            'keywords' => ['caption' => __('Keywords'), 'width' => 200, 'default' => false],
            'articles_count' => [
                'caption' => __('Articles'),
                'link' => [
                    'controller' => 'Articles',
                    'action' => 'index',
                    '?' => ['properties.{propertytype}.selected' => '{id}', 'load' => true]
                ],
                'width' => 150,
                'default' => true
            ],
            'items_count' => ['caption' => __('Items'), 'width' => 150, 'default' => false],
            'links_count' => ['caption' => __('Links'), 'width' => 150, 'default' => false],
            'norm_data' => ['caption' => __('Norm data'), 'width' => 200, 'default' => false],
            'norm_iri' => [
                'caption' => __('IRI fragment'),
                'width' => 200,
                'link' => 'iri',
                'default' => true,
                'public' => true
            ],
            'iri_path' => [
                'caption' => __('IRI path'),
                'width' => 200,
                'link' => 'iri',
                'default' => false,
                'public' => true
            ],
            'id' => ['caption' => 'ID', 'width' => 100, 'default' => false],
            'level' => ['caption' => __('Level'), 'width' => 100, 'default' => false],
            'related_id' => ['caption' => __('Related'), 'width' => 100, 'default' => false]
        ];

        return parent::getColumns($selected, $default, $options);
    }

    /**
     * Get the list of supported tasks
     *
     * @return string[]
     */
    public function mutateGetTasks(): array
    {
        $tasks = [
//            'delete' => 'Delete properties',
//            'fix_iris' => 'Clean IRIs',
            'rebuild_sortkeys' => 'Rebuild sort keys',
            'batch_sort' => 'Sort properties',
            'batch_merge' => 'Merge properties',
            'batch_reconcile' => 'Reconcile properties'
        ];

        if (in_array(AppBaseTable::$userRole, ['author', 'editor'])) {
            $tasks = array_intersect_key($tasks, ['batch_sort' => true,'rebuild_sortkeys' => true]);
        }
        elseif (!in_array(AppBaseTable::$userRole, ['admin', 'devel'])) {
            $tasks = [];
        }

        return $tasks;
    }

    /**
     * Called from mutate job
     *
     * @param array $params
     * @param JobMutate $job
     * @return int Number of records to process.
     *             For operations that work on the whole dataset (e.g. sort), the number of property types.
     */
    public function mutateGetCount($params, $job): int
    {
        if (($job->config['task'] ?? '') === 'batch_sort') {
            $params = ['propertytype' => $params['propertytype'] ?? $params['scope'] ?? ''];
//            return 1;
        }

        $params['ancestors'] = false;
        $params['treePositions'] = false;
        return parent::mutateGetCount($params, $job);
    }

    /**
     * Sort all properties of a type
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Not used
     * @return array The mutated entities
     */
    public function mutateEntitiesBatchSort($taskParams, $dataParams, $paging): array
    {
        $dataParams = $this->parseRequestParameters($dataParams);
        $propertytype = $dataParams['propertytype'] ?? $dataParams['scope'] ?? '';

        // Sort / recover a single propertytype
        $sortBy = $taskParams['sortby'] ?? 'sortkey';

        if (!in_array(AppBaseTable::$userRole, ['admin', 'devel']) && !in_array($sortBy, ['sortkey', 'lemma'])) {
            throw new MethodNotAllowedException('You have no permission for using the selected sort field.');
        }

        $this->setSortField($sortBy);
        $this->setScope($propertytype);
        $this->recover();

        return [$propertytype];
    }

    /**
     * Merge all properties with the same lemma path
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the key 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesBatchMerge($taskParams, $dataParams, $paging): array
    {
        if (($taskParams['cursor'] ?? 0) < 0) {
            throw new BadRequestException('Invalid cursor for merge task');
        }
        $limit = $paging['limit'] ?? 1;

        $dataParams = $this->parseRequestParameters($dataParams);

        // Use cursor based pagination instead of offset
        if (($taskParams['cursor'] ?? 0) > 0) {
            $cursorNode = $this->find('all', ['deleted'=>[0,1]])
                ->where(['id' => $taskParams['cursor']])
                ->firstOrFail();
//            $cursorNode = $this->get($taskParams['cursor']);
            $cursorConditions = [
                'OR' => [
                    ['Properties.level' => $cursorNode->level, 'Properties.id >' => $taskParams['cursor'] ?? 0],
                    'Properties.level >' => $cursorNode->level
                ]
            ];
        } else {
            $cursorConditions = ['1=1'];
        }

        $dataParams['articleCount'] = false;
        $dataParams['treePositions'] = false;
        $dataParams['ancestors'] = false;
        $dataParams['treePositions'] = false;

        $entities = $this
            ->find('hasParams', $dataParams)
            ->where($cursorConditions)
            ->orderAsc('Properties.level')
            ->orderAsc('Properties.id')
            ->limit($limit)
            ->toArray();

        /** @var Property $entity */
        foreach ($entities as $entity) {

            // Skip already merged entities
            if (!$this->exists(['id' => $entity->id, 'deleted' => 0])) {
                continue;
            }

            $sourceEntities = $entity->duplicates;
            $sourceIds = $sourceEntities->all()->extract('id')->toArray();
            if (!empty($sourceIds)) {
                $this->merge($entity->id, $sourceIds, ['concat' => true]);
            }
        }

        return $entities;
    }

    /**
     * Reconcile all properties using external services configured in the property type
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the key 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesBatchReconcile($taskParams, $dataParams, $paging): array
    {
        if (($taskParams['cursor'] ?? 0) < 0) {
            throw new BadRequestException('Invalid cursor for task');
        }
        $limit = $paging['limit'] ?? 1;
        $dataParams = $this->parseRequestParameters($dataParams);
        $dataParams['ancestors'] = false;
        $dataParams['treePositions'] = false;

        // Use cursor based pagination instead of offset
        if (($taskParams['cursor'] ?? 0) > 0) {
            $cursorConditions = ['Properties.id >' => $taskParams['cursor'] ?? 0];
        } else {
            $cursorConditions = ['1=1'];
        }

        $entities = $this
            ->find('hasParams', $dataParams)
            ->contain(['Types'])
            ->where($cursorConditions)
            ->orderAsc('Properties.id')
            ->limit($limit)
            ->toArray();

        $targetField = $taskParams['targetfield'] ?? 'norm_data';
        $reconcileOptions = ['onlyempty' => !empty($taskParams['onlyempty'])];

        /** @var Property $entity */
        foreach ($entities as $entity) {
            $entity->reconcile($targetField, $reconcileOptions);
        }

        if (!$this->saveMany($entities, [])) {
            throw new SaveManyException('Could save entities.');
        }

        return $entities;
    }

    /**
     * Mutate: Rebuild date fields
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param array $paging Array with the keys 'offset' and 'limit'
     * @return array The mutated entities
     */
    public function mutateEntitiesRebuildSortkeys($taskParams, $dataParams, $paging): array
    {
        $dataParams = $this->parseRequestParameters($dataParams);
        $dataParams['ancestors'] = false;
        $dataParams['treePositions'] = false;

        $entities = $this
            ->find('hasParams', $dataParams)
            //->find('containAll', $dataParams)
            ->contain(['Types'])
            ->limit( $paging['limit'] ?? 100)
            ->offset($paging['offset'] ?? 0)
            ->toArray();

        foreach ($entities as $entity) {
            /** @var Property $entity */
            $entity->updateSortKey();
        }
        $this->saveMany($entities);

        return $entities;
    }
}
