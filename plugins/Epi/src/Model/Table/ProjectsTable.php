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


use App\Model\Interfaces\ExportTableInterface;
use App\Utilities\Converters\Attributes;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Query;
use Cake\Validation\Validator;
use Epi\Model\Entity\Project;
use Epi\Model\Traits\TransferTrait;

/**
 * Projects table
 */
class ProjectsTable extends BaseTable implements ExportTableInterface
{

    use TransferTrait;

    /**
     * @var int Default export limit used in TransferTrait
     */
    protected $exportLimit = 1;

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'projecttype';

    /**
     * The field containing a default caption
     *
     * @var string
     */
    public $captionField = 'name';

    /**
     * Request parameter config
     *
     * @var string[]
     */
    public $parameters = [
        'id' => 'list-integer',
        'projects' => 'list-integer',
        // Replace by id? No, it's used by the export pipeline (query parameter projects passed to getExportData)
        'projecttypes' => 'list',
        'term' => 'string',
        'published' => 'list-integer',
        'template' => 'raw',
        'columns' => 'list-or-false',
        'selected' => 'list',
        'articles' => [
            'projects' => 'list',
            'articletypes' => 'list',
            'field' => 'string',
            'term' => 'string',
            'properties' => 'merge',
            'published' => 'list-integer'
        ],
        'properties' => 'nested-list',
        'load' => 'list',
        'save' => 'list'
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

        $this->setTable('projects');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Epi.XmlStyles', []);

        $this->hasMany(
            'Articles',
            [
                'className' => 'Epi.Articles',
                'foreignKey' => 'projects_id',
                'conditions' => ['Articles.deleted' => 0],
                'joinType' => Query::JOIN_TYPE_INNER,
                'dependent' => true,
                'cascadeCallbacks' => true
            ]
        );


        $this->belongsTo(
            'Types',
            [
                'className' => 'Epi.Types',
                'strategy' => BelongsTo::STRATEGY_SELECT,
                'joinType' => Query::JOIN_TYPE_LEFT,
                'foreignKey' => 'projecttype',
                'bindingKey' => 'name',
                'conditions' => ['Types.scope' => 'projects', 'Types.mode' => 'default', 'Types.preset' => 'default']
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
            ->scalar('projecttype')
            ->maxLength('projecttype', 100);

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
        $this->Articles->clearCache();
    }

    /**
     * Get columns to be rendered in table views
     *
     * ### Options
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
            'sortno' => [
                'caption' => __('Sorting'),
                'sort' => true,
                'width' => 50,
                'default' => false
            ],

            FIELD_PROJECTS_SIGNATURE => [
                'caption' => __('Short name'),
                'sort' => true,
                'width' => 150,
                'default' => true
            ],

            'name' => [
                'caption' => __('Name'),
                'sort' => true,
                'default' => true
            ],

            'articles' => [
                'caption' => __('Articles'),
                'align' => 'right',
                // The placeholder {id} will be replaced by LinkHelper->fillPlaceholders()
                'link' => [
                    'controller' => 'Articles',
                    'action' => 'index',
                    '?' => ['projects' => '{id}', 'load' => true]
                ],
                'sort' => 'articles',
                'width' => 50,
                'default' => true,
            ],

            'published_label' => [
                'caption' => __('Progress'),
                'sort' => 'published',
                'width' => 100,
                'default' => true
            ],

            'iri_path' => [
                'caption' => __('IRI'),
                'sort' => 'norm_iri',
                'width' => 150,
                'default' => true
            ],

            'description' => [
                'caption' => __('Description'),
                'sort ' => false,
                'width' => 200,
                'default' => false
            ]
        ];

        return parent::getColumns($selected, $default, $options);
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

        // TODO: implement $fields
        return [
                'order' => [$this->captionField => 'ASC'],
                'sortableFields' => $this->getSortableFields($columns),
                'limit' => 100
            ] + $pagination;
    }

    /**
     * Find projects by ID
     *
     * @param \Cake\ORM\Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findHasProjectIds(Query $query, array $options)
    {
        $ids = $options['projects'] ?? [];

        if (is_string($ids)) {
            $ids = Attributes::commaListToIntegerArray($ids);
        }

        if ($ids) {
            $query = $query->where(['Projects.id IN' => $ids]);
        }

        return $query;
    }

    /**
     * Narrows down the projects to  projects used in articles matching the
     * parameters in the articles key.
     *
     * @param \Cake\ORM\Query $query
     * @param array $options
     * @return Query
     */
    public function findHasArticleOptions(Query $query, array $options)
    {
        $articlesOptions = $options['articles'] ?? [];
        $articlesOptions['properties'] = $options['properties'] ?? [];

        $articlesOptions = array_filter($articlesOptions);

        if (!empty($articlesOptions)) {
            $articlesQuery = $this->Articles
                ->find('hasParams', $articlesOptions)
                ->select(['Articles.projects_id']);

            $query = $query->where(['Projects.id IN' => $articlesQuery]);
        }

        return $query;
    }

    /**
     * Contain data necessary for table columns
     *
     * @param \Cake\ORM\Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findContainColumns(Query $query, array $options)
    {
        if (
            empty($options['columns']) ||
            isset($options['columns']['articles']) ||
            in_array('articles', $options['columns'] ?? [])
        ) {
            $query = $query->find('articleCount', $options);
        }

        return $query;
    }

    /**
     * Add article count to query
     *
     * @param \Cake\ORM\Query $query
     * @param array $options
     *
     */
    public function findArticleCount(Query $query, array $options)
    {

        // obtain project ids and article count in a separate query first,
        // in order to comply with the `ONLY_FULL_GROUP_BY` restrictions.
        $withArticlesCountQuery =
            $this->find()
                ->select(
                    function (\Cake\ORM\Query $query) {
                        return [
                            'id' => 'Projects.id',
                            'articles' => $query->func()->count('Articles.id')
                        ];
                    }
                )
                //->where(['Projects.deleted'=> 0])
                ->leftJoinWith('Articles')
                ->group(['Projects.id']);

        $query = $query
            ->select(['articles' => 'ArticleCounts.articles'])
            ->select($this)
            ->innerJoin(
                ['ArticleCounts' => $withArticlesCountQuery],
                function (QueryExpression $exp, Query $query) {
                    return $exp->equalFields('ArticleCounts.id', 'Projects.id');
                }
            );

        return $query;
    }

    /**
     * Constructs a database query from request parameters
     *
     * //TODO: split into finders, see the implementation in ArticlesTable.php
     *
     * @param \Cake\ORM\Query $query
     * @param array $params Request parameters
     * @return Query
     */
    public function findHasParams(Query $query, array $params): Query
    {

        $default = [
            'id' => [],
            'term' => '',
            'projecttypes' => '',
            'published' => null,
            'articles' => [],
            'projects' => []
        ];

        $params = array_merge($default, $params);

        // Term
        $term = $params['term'] ?? false;
        if ($term) {
            $query = $query->where(
                [
                    'OR' => [
                        'name LIKE' => '%' . $term . '%',
                        'signature LIKE' => '%' . $term . '%',
                        'description LIKE' => '%' . $term . '%',
                        'norm_iri LIKE' => '%' . $term . '%'
                    ]
                ]
            );
        }

        // Project types
        $types = $params['projecttypes'] ?? false;
        if (!empty($types)) {
            $types = Attributes::commaListToStringArray($types);
            $query = $query->where([
                'Projects.projecttype IN' => $types,
            ]);
        }

        $query = $query->find('hasProjectIds', ['projects' => $params['id']]);
        $query = $query->find('hasProjectIds', ['projects' => $params['projects']]);

        // Only projects used in matched articles
        $query = $query->find('hasArticleOptions', $params['articles']);

        // Published
        $query = $query->find('hasPublicationState', ['published' => $params['published']]);

        return $query;
    }

    /**
     * Find a project list to be used in selectors
     * The list is grouped  by project type if $options['grouped'] is set to true
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findSelect($query, $options)
    {
        $listOptions = [
            'keyField' => 'id',
            'valueField' => function ($project) {
                return $project->captionPath;
            }
        ];

        if ($options['grouped'] ?? false) {
            $listOptions['groupField'] = 'Type.caption';
        }

        return $query
            ->find('list', $listOptions)
            ->contain('Types')
            ->order(['projecttype' => 'ASC', 'name' => 'ASC', FIELD_PROJECTS_SIGNATURE => 'ASC']);
    }

}
