<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Table;

use App\Model\Entity\Doc;
use App\Model\Interfaces\ScopedTableInterface;
use App\Utilities\Text\TextParser;
use ArrayObject;
use Cake\Collection\CollectionInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Epi\Model\Behavior\PositionBehavior;

/**
 * Docs table
 *
 * Handles the help and the pages segment of the docs table.
 */
class DocsTable extends BaseTable implements ScopedTableInterface
{

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'segment';

    /**
     * Scope field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $scopeField = 'segment';

    /**
     * Current scope
     *
     * @var null
     */
    public $scopeValue = null;

    /**
     * Initial scope options
     *
     * @var string[]
     */
    public $scopeOptions = ['help', 'wiki', 'pages'];

    /**
     * Model table configuration
     *
     * @var array
     */
    public $config = [
        'table' => 'docs',
        'norm_iri' => true
    ];

    /**
     * Request parameter config
     *
     * @var string[]
     */
    public $parameters = [
        'id' => 'list',
        'term' => 'string',
        'category' => 'string',
        'iri' => 'string',

        'selected' => 'list',
        'columns' => 'list-or-false',

        'load' => 'list',
        'save' => 'list'
    ];

    /**
     * The field containing a default caption
     *
     * @var string
     */
    public $captionField = 'name';

    /**
     * Initialize hook
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable($this->config['table']);
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        // $this->hasMany('Permissions',[
        // 'dependent'=>true,
        // 'property'=>'locks',
        // 'foreignKey'=>'entity_id',
        // 'conditions'=>['Permissions.permission_type'=>'lock','entity_type'=>'docs']
        // ]);
    }


    /**
     * Before find method
     *
     * @param EventInterface $event
     * @param Query $query
     * @param ArrayObject $options
     * @param $primary
     *
     * @return void
     */
    public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, $primary)
    {
        if (!empty($this->scopeValue)) {
            $query->where([$this->scopeField => $this->scopeValue]);
        }
    }

    /**
     * Before save method
     *
     * @param EventInterface $event
     * @param Doc $entity
     * @param array $options
     */
    public function beforeSave(EventInterface $event, $entity, $options = [])
    {
        if (!empty($this->scopeValue) && !empty($this->scopeField)) {
            $entity->{$this->scopeField} = $this->scopeValue;
        }
    }

    /**
     * Get distinct categories
     *
     * @return mixed
     */
    public function getCategories()
    {
        $query = $this->find();

        if (!empty($this->scopeValue) && !empty($this->scopeField)) {
            $query = $query->where([$this->scopeField => $this->scopeValue]);
        }

        $query = $query
            ->select(['category'])
            ->distinct()
            ->order(['category' => 'ASC'])
            ->all()
            ->extract('category');

        return $query->toArray();
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
        $query = $this->find('all')->where(['menu' => 1]);

        if (!empty($this->scopeValue) && !empty($this->scopeField)) {
            $query = $query->where([$this->scopeField => $this->scopeValue]);
        }

        if ($published) {
            $query = $query->where(['published' => PUBLICATION_BINARY_PUBLISHED]);
        }

        $query = $query
            ->select(['category'])
            ->distinct()
            ->order(['category' => 'ASC'])
            ->all()
            ->map(
                function ($row) {
                    $cat = $row['category'];
                    $items = explode('/', $cat);
                    $label = trim(end($items));
                    $class = empty($label) ? 'category-meta' : '';
                    $url = ['action' => empty($label) ? 'index' : 'show', '?' => ['category' => $cat]];
                    $label = empty($label) ? __('Without category') : $label;

                    return [
                        'label' => $label,
                        'url' => $url,
                        'level' => count($items) - 1,
                        'tree-collapsed' => true, //count($items) > 0,
                        'tree-hidden' => count($items) > 1,//count($items) > 2,
                        'class' => $class
                    ];
                },
            );

        $cats = PositionBehavior::addTreePositions($query->toArray(), true);

        $menu = [
            'caption' => __('Navigation'),
            'activate' => true,
            'scrollbox' => true,
            'search' => true,
            'tree' => 'foldable'
        ];

        $first = [
            'label' => __('List all'),
            'url' => ['action' => 'index'],
            'class' => 'category-meta'
        ];
        $menu[] = $first;

        return array_merge($menu, $cats);
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
                'order' => [
                    'Docs.category' => 'asc',
                    'Docs.sortkey' => 'asc',
                    'Docs.name' => 'asc'
                ],
                'limit' => 100
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
            'name' => [
                'caption' => __('Name'),
                'sort' => true,
                'default' => true
            ],

            'category' => [
                'caption' => __('Category'),
                'sort' => true,
                'default' => true
            ],
            'norm_iri' => [
                'caption' => __('IRI-Fragment'),
                'sort' => 'norm_iri',
                'width' => 150,
                'default' => true
            ],
            'sortkey' => [
                'caption' => __('Order'),
                'sort' => true,
                'width' => 50,
                'default' => true
            ],
            'modified' => [
                'caption' => __('Last modified'),
                'sort' => true,
                'default' => true
            ],
            'created' => [
                'caption' => __('Created on'),
                'sort' => true,
                'default' => true
            ]
        ];

//        return $default;
        return parent::getColumns($selected, $default, $options);
    }

    /**
     * Constructs a database query from request parameters
     *
     * @param \Cake\ORM\Query $query
     * @param array $params Request parameters
     * @return \Cake\Database\Query
     */
    public function findHasParams(Query $query, array $params): Query
    {

        $default = [
            'id' => [],
            'term' => '',
            'category' => null,
            'published' => ''
        ];

        $params = array_merge($default, $params);

        // ID
        $id = $params['id'] ?? [];
        if (!empty($id)) {
            $query = $query->where(['id IN' => $id]);
        }

        // Search and category
        $query = $query->find('text', $params);

        // Filter out nonpublic entities for guests
        if ($this::$userRole === 'guest') {
            $query = $query->where(['published' => PUBLICATION_BINARY_PUBLISHED]);
        }
        return $query;
    }

    /**
     * Convert the doc to HTML
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findContainAll(Query $query, array $options): Query
    {
        $query = $query->formatResults(function ($results) {
            /** @var Doc $row */
            return $results->map(function ($row) {
                $row->prepareHtml();
                return $row;
            });
        });

        return $query;
    }

    /**
     * Custom finder
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findText(Query $query, array $options)
    {
        // Scope
        if (!empty($this->scopeValue) && !empty($this->scopeField)) {
            $query = $query->where([$this->scopeField => $this->scopeValue]);
        }

        //Category
        if ($options['category'] !== null) {
            $query = $query->where(['category' => $options['category']]);
        }

        //Term
        if (!empty($options['term'])) {
            $term = $options['term'];
            $terms = explode(' ', $term);
            $conditions = array_map(function ($term) {
                return (['content LIKE' => '%' . $term . '%']);
            }, $terms);
            $query = $query->where(['AND' => $conditions]);

            //Highlight
            // TODO: replace by magic property
            $query = $query->formatResults(function (CollectionInterface $results) use ($terms) {
                return $results->map(function ($row) use ($terms) {
                    $row['search'] = [];
                    $row['search'][] = new Doc(
                        [
                            'id' => $row->id,
                            'caption' => __('Content'),
                            'content' => TextParser::highlightTerms(TextParser::stripTagsWithWhitespace($row->content),
                                $terms),
                            'highlight' => $terms
                        ],
                        [
                            'source' => 'Docs',
                            'useSetters' => false,
                            'markClean' => true,
                            'markNew' => true
                        ]
                    );
                    return $row;
                });
            });
        }

        return $query;
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->allowEmptyString('key');

        $validator
            ->allowEmptyString('name');

        $validator
            ->allowEmptyString('category');

        $validator
            ->allowEmptyString('content');

        return $validator;
    }

    /**
     * Get all scopes
     *
     * @implements ScopedTableInterface
     * @param array $options
     * @return array
     */
    public function getScopes(array $options = []): array
    {
        return $this->scopeOptions;
    }

    /**
     * Set scope
     *
     * @implements ScopedTableInterface.
     * @param string $scope (e.g. "wiki" or "help")
     * @return Table
     */
    public function setScope($scope): Table
    {
        if ($scope instanceof EntityInterface) {
            $this->scopeValue = $scope->{$this->scopeField};
        }
        else {
            $this->scopeValue = $scope;
        }
        return $this;
    }

    /**
     * Get scope
     *
     * @implements ScopedTableInterface
     * @return string|null
     */
    public function getScope(): string
    {
        return $this->scopeValue ?? '';
    }

    /**
     * Remove scope
     *
     * @implements ScopedTableInterface
     * @return Table
     */
    public function removeScope(): Table
    {
        $this->scopeValue = null;
        return $this;
    }

}
