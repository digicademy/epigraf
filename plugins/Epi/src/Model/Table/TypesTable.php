<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace Epi\Model\Table;

use App\Model\Interfaces\ExportTableInterface;
use App\Model\Interfaces\ScopedTableInterface;
use App\Utilities\Converters\Attributes;
use ArrayObject;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use Epi\Model\Traits\TransferTrait;

/**
 * Types table
 *
 * # Relations
 * @property ItemsTable $Items
 * @property SectionsTable $Sections
 * @property ArticlesTable $Articles
 * @property ProjectsTable $Projects
 */
class TypesTable extends BaseTable implements ScopedTableInterface, ExportTableInterface
{
    use TransferTrait;

    /**
     * @var int Default export limit used in TransferTrait
     */
    protected $exportLimit = 100;

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'scope';

    /**
     * Scope field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $scopeField = 'scope';

    /**
     * Current scope
     *
     * @var null
     */
    public $scopeValue = null;

    /**
     * Default scope options
     *
     * @var string[]
     */
    public $scopeOptions = ['projects', 'articles', 'sections', 'items', 'links', 'footnotes', 'properties'];

    /**
     * Request parameter config
     *
     * @var string[]
     */
    public $parameters = [
        'id' => 'list',
        'idents' => 'string',
        'selected' => 'list',
        'published' => 'list-integer',
        'term' => 'string',
        'scopes' => 'list',
        'modes' => 'list',
        'categories' => 'list',
        'columns' => 'list',
        'name' => 'list',
        'iri' => 'list',

        'load' => 'list',
        'save' => 'list'
    ];

    public $searchFields = [
        'identifiers' => [
            'caption' => 'Identifiers',
            'scopes' => [
                'Types.name',
                'Types.caption',
                'Types.norm_iri',
                'Types.id' => ['type' => 'integer', 'operator' => '=']
            ]
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

        $this->setTable('types');
        $this->setDisplayField('caption');
        $this->setPrimaryKey('id');
        $this->setEntityClass('Epi.Type');

        $this->hasMany(
            'Subtypes',
            [
                'className' => 'Epi.Types',
                'bindingKey' => ['name', 'scope'],
                'foreignKey' => ['name', 'scope'],
                'conditions' => [
                    [
                        'OR' => [
                            'Subtypes.mode <>' => 'default',
                            'Subtypes.preset <>' => 'default'
                        ]
                    ]
                ],
                'order' => ['Subtypes.sortno' => 'ASC']
            ]
        );

        $this->hasMany(
            'Projects',
            [
                'className' => 'Epi.Projects',
                'bindingKey' => 'name',
                'foreignKey' => 'projecttype',
                'conditions' => ['Types.scope' => 'projects']
            ]
        );

        $this->hasMany(
            'Articles',
            [
                'className' => 'Epi.Articles',
                'bindingKey' => 'name',
                'foreignKey' => 'articletype',
                'conditions' => ['Types.scope' => 'articles']
            ]
        );

        $this->hasMany(
            'Sections',
            [
                'className' => 'Epi.Sections',
                'bindingKey' => 'name',
                'foreignKey' => 'sectiontype',
                'conditions' => ['Types.scope' => 'sections']
            ]
        );

        $this->hasMany(
            'Items',
            [
                'className' => 'Epi.Items',
                'bindingKey' => 'name',
                'foreignKey' => 'itemtype',
                'conditions' => ['Types.scope' => 'items']
            ]
        );

        $this->hasMany(
            'Properties',
            [
                'className' => 'Epi.Properties',
                'bindingKey' => 'name',
                'foreignKey' => 'propertytype',
                'conditions' => ['Types.scope' => 'properties']
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
        $schema->setColumnType('config', 'json');
        return $schema;
    }

    /**
     * beforeMarshal callback
     *
     * Prepare the config data
     *
     * @param EventInterface $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        // TODO: Is this the way to go? Add the same logic to Users->settings
        //      -> better implement JsonType->marshal()
        // TODO: Does this work after implementing JsonType?
//        if (isset($data['config']) && !is_array($data['config'])) {
//            $value = json_decode($data['config'], true);
//            if ($value !== null) {
//                $data['config'] = $value;
//            }
//        }

        parent::beforeMarshal($event, $data, $options);
    }

    /**
     * Before save method
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     *
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        // TODO: make suggestions in the frontend using JS
        if (empty($entity->norm_iri)) {
            $entity->norm_iri = $entity->iriFragment;
        }

        if (empty($entity->caption)) {
            $entity->caption = Attributes::cleanCaption($entity->name);
        }
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

        $this->Articles->clearResultCache();
        $this->clearViewCache('epi_views_Epi_Articles');
    }

    /**
     * Default validation rules
     *
     * @param \Cake\Validation\Validator $validator Validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
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
            ->scalar('name')
            ->maxLength('name', 100)
            ->add('name', 'validFormat', [
                'rule' => ['custom', '/^[a-z0-9_-]+$/'],
                'message' => 'Only lowercase alphanumeric characters, underscore and hyphen are allowed.'
            ]);

        $validator
            ->scalar('norm_iri')
            ->maxLength('norm_iri', 500)
            ->add('norm_iri', 'validFormat', [
                'rule' => ['custom', '/^[a-z0-9_~-]+$/'],
                'message' => 'Only lowercase alphanumeric characters, underscore, hyphen and tilde are allowed.'
            ])
            ->allowEmptyString('norm_iri');

        $validator
            ->scalar('category')
            ->maxLength('category', 100)
            ->allowEmptyString('category');

        $validator
            ->scalar('caption')
            ->maxLength('caption', 200)
            ->allowEmptyString('caption');

        $validator
            ->scalar('description')
            ->maxLength('description', 500)
            ->allowEmptyString('description');

        $validator
            ->allowEmptyString('config')
            ->add('config', 'isJson', [
                'rule' => 'isJson',
                'message' => __('Could not parse JSON data, please check you syntax'),
                'provider' => 'table'
            ]);

        return $validator;
    }

    /**
     * Rules after the validation but before saving
     *
     * // TODO: replace isArray by Validator::array
     *
     * @param RulesChecker $rules
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        // Check JSON to array conversion for create and update operations
        $rules->add(function ($entity, $options) {
            if (isset($entity['config'])) {
                return empty($entity['config']) || is_array($entity['config']);
            }
            return true;
        }, 'isArray');

        return $rules;
    }

    /**
     * beforeFind callback
     *
     * @param EventInterface $event
     * @param Query $query
     * @param ArrayObject $options
     * @param $primary
     * @return void
     */
    public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, $primary)
    {
        //$true = true;
    }

    /**
     * Get all scopes
     *
     * implements ScopedTableInterface
     */
    public function getScopes(): array
    {
        return $this->scopeOptions;
    }

    /**
     * Get current scope
     *
     * implements ScopedTableInterface
     *
     * @return string
     */
    public function getScope(): string
    {
        return $this->scopeValue ?? '';
    }

    /**
     * Set current scope
     *
     * @implements ScopedTableInterface
     * @param string $scope
     * @return Table
     */
    public function setScope($scope): \Cake\ORM\Table
    {
        if ($scope instanceof EntityInterface) {
            // Quick fix: Only if the scope field was set in the entity
            if (!$this->hasField($this->scopeField)) {
                return $this;
            }

            $scope = $scope->{$this->scopeField};
        }

        $this->scopeValue = $scope;
        return $this;
    }


    /**
     * Remove current scope
     *
     * @implements ScopedTableInterface
     * @return Table
     */
    public function removeScope(): Table
    {
        $this->scopeValue = null;
        return $this;
    }

    /**
     * Find types that are not listed in the types table
     *
     * @return array
     */
    public function getProblems()
    {
        $dependent = [
            'Projects' => 'projecttype',
            'Articles' => 'articletype',
            'Sections' => 'sectiontype',
            'Items' => 'itemtype',
            'Properties' => 'propertytype'
        ];

        $problems = [];

        foreach ($dependent as $modelName => $field) {
            $model = $this->fetchTable('Epi.' . $modelName);

            $scope = Inflector::underscore($modelName);
            $types = $this->find()->select('name')->where(['scope' => $scope])->distinct();

            $missing = $model->find()
                ->select([$field])
                ->distinct([$field])
                ->where([$field . ' NOT IN' => $types, 'deleted' => 0])
                ->all()
                ->extract($field)
                ->toArray();

            foreach ($missing as $missingType) {
                $problems[] = __('For {0}, the {1} configuration is missing.', $scope, $missingType);
            }
        }

        return $problems;
    }

    /**
     * Get the scope number
     *
     * @param $query
     * @param $options
     *
     * @return mixed
     */
    public function _scopeNumberField($query, $options)
    {
        $order = ['projects', 'articles', 'sections', 'items', 'links', 'footnotes', 'properties'];

        $scopeNumberExpression = $query->newExpr()->case();
        foreach ($order as $no => $scope) {
            $scopeNumberExpression = $scopeNumberExpression
                ->when(['scope' => $scope])
                ->then($no, 'integer');
        }

        return $scopeNumberExpression;
    }

    /**
     * Find types for links bound to an article
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findLinkTypes(Query $query, array $options)
    {
        return $query->find('all')
            ->order(['category' => 'asc', 'name' => 'asc'])
            ->where(['scope' => 'links', 'deleted' => 0, 'mode' => MODE_DEFAULT], [], true);
    }

    /**
     * Find types for footnotes bound to an article
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findFootnoteTypes(Query $query, array $options)
    {
        return $query->find('all')
            ->order(['category' => 'asc', 'name' => 'asc'])
            ->where(['scope' => 'footnotes', 'deleted' => 0, 'mode' => MODE_DEFAULT], [], true);
    }

    /**
     * Get column definitions from the config field
     * Definitions from multiple type entities will be merged
     *
     * @param Query $query
     * @param array $options Provide the scope key in the $options.
     * @return Query
     */
    public function findColumns(Query $query, array $options)
    {

        // TODO: use $row->getValueNested instead of Hash:extract (implement test first)
        $mapper = function ($row, $key, $mapReduce) {
            $columns = Hash::extract($row, 'config.columns.{*}');
            foreach ($columns as $column) {
                $column['name'] = $column['name'] ?? $column['key'];
                $mapReduce->emitIntermediate($column, $column['name']);
            }
        };

        $reducer = function ($columns, $columnName, $mapReduce) {
            $mapReduce->emit($columns[0], $columnName);
        };

        $query = $query
            ->where(['scope' => $options['scope'] ?? ''])
            ->mapReduce($mapper, $reducer);

        return $query;
    }

    /**
     * Extract search parameters from request parameters
     *
     * @param array $requestParameters The query parameters
     * @param string $requestPath
     * @param string $requestAction
     * @return array
     */
    public function parseRequestParameters(array $requestParameters = [], $requestPath = '', $requestAction = ''): array
    {
        $params = Attributes::parseQueryParams($requestParameters, $this->parameters, 'types');
        $params['action'] = $requestAction;
        return $params;
    }

    /**
     * Constructs a database query from request parameters
     *
     * //TODO: split into finders, see the implementation in ArticlesTable.php
     *
     * @param \Cake\ORM\Query $query
     * @param array $params Request parameters
     * @return \Cake\Database\Query
     */
    public function findHasParams(Query $query, array $params): Query
    {

        $default = [
            'id' => [],
            'scopes' => [],
            'term' => '',
            'published' => null
        ];

        $params = array_merge($default, $params);

        // ID, scopes, categories, name
        $query = $query->find('values', [
            'values' => [
                'id' => $params['id'] ?? null,
                'name' => $params['name'] ?? null,
                'norm_iri' => $params['iri'] ?? null,
                'scope' => $params['scopes'] ?? null,
                'mode' => $params['modes'] ?? null,
                'category' => $params['categories'] ?? null
            ]
        ]);


        // Term
        $term = $params['term'] ?? false;
        if ($term) {
            $searchConfig = $this->searchFields['identifiers'] ?? [];
            $query = $query->find('term', [
                'term' => $term,
                'searchFields' => $searchConfig['scopes'] ?? [],
                'operator' => $searchConfig['operator'] ?? 'LIKE',
                'type' => $searchConfig['type'] ?? 'string'
            ]);

        }

        // Published
        $query = $query->find('hasPublicationState', ['published' => $params['published']]);

        // Sort field
        $query
            ->select($this)
            ->select(['scopenumber' => $this->_scopeNumberField($query, $params)], false);

        return $query;
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
            'id' => [
                'caption' => __('ID'),
                'sort' => true,
                'default' => $this::$requestFormat !== 'html',
                'width' => 50
            ],
            'scope' => [
                'caption' => __('Scope'),
                'sort' => 'scopenumber',
                'default' => true,
                'width' => 150
            ],
            'name' => [
                'caption' => __('Name'),
                'sort' => true,
                'default' => true
            ],
            'mode' => [
                'caption' => __('Mode'),
                'sort' => 'mode',
                'default' => true,
                'width' => 100
            ],
            'preset' => [
                'caption' => __('Preset'),
                'sort' => 'preset',
                'default' => true,
                'width' => 100
            ],
            'sortno' => [
                'caption' => __('Sort'),
                'sort' => true,
                'default' => true,
                'width' => 75
            ],
            'category' => [
                'caption' => __('Category'),
                'sort' => true,
                'default' => true,
                'width' => 150
            ],
            'caption' => [
                'caption' => __('Caption'),
                'sort' => true,
                'default' => true
            ],
            'description' => [
                'caption' => __('Description'),
                'sort' => true,
                'default' => true
            ],
            'config' => [
                'caption' => __('Config'),
                'key' => 'config',
                'sort' => false,
                'aggregate' => false,
                'default' => $this::$requestFormat !== 'html',
                'width' => 250
            ],
            'shortcut' => [
                'caption' => __('Shortcut'),
                'key' => 'config.shortcut',
                'sort' => false,
                'default' => false,
                'width' => 100
            ],
            'tag_type' => [
                'caption' => __('Tag type'),
                'key' => 'config.tag_type',
                'sort' => false,
                'default' => false,
                'width' => 100
            ],
            // TODO: use binary publication state
            'published_label' => [
                'caption' => __('Published'),
                'sort' => 'published',
                'default' => $this::$requestFormat === 'html',
                'width' => 100
            ],
            'norm_iri' => [
                'caption' => __('IRI fragment'),
                'sort' => 'norm_iri',
                'default' => $this::$requestFormat !== 'html',
                'width' => 150
            ],
            'iri_path' => [
                'caption' => __('IRI path'),
                'sort' => 'norm_iri',
                'default' => $this::$requestFormat === 'html',
                'width' => 150
            ],
            'created' => [
                'caption' => __('Created'),
                'sort' => true,
                'default' => $this::$requestFormat !== 'html',
                'width' => 50
            ],
            'modified' => [
                'caption' => __('Modified'),
                'sort' => true,
                'default' => $this::$requestFormat !== 'html',
                'width' => 50
            ]
        ];

        return parent::getColumns($selected, $default, $type);
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

        $categories = $this->find('all')->select(['category'])->distinct()
            ->all()->extract('category')->toArray();

        $filter['categories'] = array_combine($categories, $categories);

        $scopes = $this->getScopes();
        $filter['scopes'] = array_combine($scopes, $scopes);

        return $filter;
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

        return [
                'order' => ['scopenumber' => 'ASC', 'category' => 'ASC', 'sortno' => 'ASC', 'name' => 'ASC'],
                'sortableFields' => $this->getSortableFields($columns),
                'limit' => 100,
                'maxLimit' => 500
            ] + $pagination;
    }

}
