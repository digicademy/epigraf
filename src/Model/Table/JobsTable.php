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

use App\Model\Entity\Job;
use App\Utilities\Converters\Attributes;
use Cake\Database\Query;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Cake\Database\Schema\TableSchemaInterface;

/**
 * Jobs table
 *
 * # Relations
 * @property \Cake\ORM\Association\BelongsTo $Pipelines
 */
class JobsTable extends BaseTable
{

    public $captionField = 'id';

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'jobtype';

    /**
     * Request parameter config
     *
     * @var string[]
     */
    public $parameters = [
        'id' => 'list',
        'name' => 'string',
        'norm_iri' => 'string',
        'pipeline' => 'raw',
        'scope' => 'string',
        'selection' => 'raw',
        'created_by' => 'list',
        'columns' => 'list-or-false',
        'jobtype' => 'list',
        'status' => 'list'
    ];

    /**
     * Predefined job types
     *
     * @var string[]
     */
    public static $jobTypes = [
        'export' => 'Export',
        'import' => 'Import',
        'transfer' => 'Transfer',
        'mutate' => 'Mutate'
    ];

    /**
     * Predefined job status values
     *
     * @var string[]
     */
    static $jobStates = [
        'init' => 'Waiting',
        'work' => 'Pending',
        'finish' => 'Done',
        'error' => 'Failed'
    ];

    /**
     * Initialize hook
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('jobs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Pipelines', [
            'foreignKey' => 'pipelines_id',
            'joinType' => Query::JOIN_TYPE_INNER
        ]);

        $this->belongsTo('Creator', [
            'className' => 'Users',
            'foreignKey' => 'created_by',
            'joinType' => \Cake\ORM\Query::JOIN_TYPE_LEFT,
            'propertyName' => 'creator'
        ]);

        $this->belongsTo('Modifier', [
            'className' => 'Users',
            'foreignKey' => 'modified_by',
            'joinType' => Query::JOIN_TYPE_LEFT,
            'propertyName' => 'modifier'
        ]);
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
        $schema->setColumnType('result', 'json');
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->requirePresence('jobtype', 'create')
            ->notEmptyString('jobtype');

        $validator
            ->allowEmptyString('config');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param RulesChecker $rules The rules object to be modified.
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['pipelines_id'], 'Pipelines'));

        return $rules;
    }


    /**
     * Automatically fill name and sortkey
     *
     * @param EventInterface $event
     * @param Job $entity
     * @param array $options
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, $options = [])
    {
        if (empty($entity->config['user_role'])) {
            $entity->config['user_role'] = $this::$userRole;
        }

        // TODO: use created_by field
        if (empty($entity->config['user_id'])) {
            $entity->config['user_id'] = $this::$userId;
        }
    }

    /**
     * Put delayed jobs into the queue after saving
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param array $options
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, $options = [])
    {
        parent::afterSave($event, $entity, $options);

        if ($entity->isNew() && !empty($entity->delay)) {
            $entity->toQueue();
        }
    }


    /**
     * Extract search parameters from request parameters
     *
     * Sets the pipeline ID based on pipeline query parameter or user settings.
     *
     * @param array $requestParameters Request parameters
     * @param string|null $requestPath The property type
     * @param string $requestAction
     * @return array
     */
    public function parseRequestParameters(array $requestParameters = [], $requestPath = '', $requestAction = ''): array
    {
        unset($requestParameters['page']);

        // @deprecated: use TransferComponent
        if (in_array($requestAction, ['add', 'download'])) {
            $params = $requestParameters;

            $pipeline_id = $params['pipeline'] ?? null;

            // Pipeline from user settings
            if (is_null($pipeline_id)) {
                $user = BaseTable::$user;
                if (($params['scope'] ?? 'article') == 'book') {
                    $pipeline_id = $user['pipeline_book_id'] ?? null;
                }
                else {
                    $pipeline_id = $user['pipeline_article_id'] ?? null;
                }
                $params['pipeline'] = $pipeline_id;
                unset($params['scope']);
            }

            // Pipeline from norm_iri
            elseif (!is_numeric($pipeline_id)) {
                $pipelinesTable = $this->fetchTable('Pipelines');
                $pipeline_id = $pipelinesTable
                    ->find('all')
                    ->where(['norm_iri' => $pipeline_id])
                    ->first();

                $params['pipeline'] = $pipeline_id['id'] ?? null;
            }

            return $params;
        }

        $params = Attributes::parseQueryParams($requestParameters, $this->parameters, 'articles');
        return $params;
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
            'id' => [
                'caption' => __('ID'),
                'default' => true
            ],
            'iri_path' => [
                'key' => 'iri_path',
                'field' => 'norm_iri',
                'caption' => __('IRI'),
                'default' => true
            ],
            'name' => [
                'caption' => __('Name'),
                'filter' => 'text',
                'default' => true
            ],
            'jobtype' => [
                'caption' => __('Type'),
                'type' => 'select',
                'empty' => true,
                'sort' => true,
                'options' => JobsTable::$jobTypes,
                'filter' => 'select',
                'default' => true,
            ],
            'status' => [
                'caption' => __('Status'),
                'type' => 'select',
                'empty' => true,
                'sort' => true,
                'options' => JobsTable::$jobStates,
                'filter' => 'select',
                'default' => true
            ],
            'progress_label' => [
                'caption' => __('Progress'),
                'default' => true
            ],
            'delay' => [
                'caption' => __('Delay'),
                'default' => true
            ],
            'pipeline' => [
                'caption' => __('Pipeline'),
                'extract' => 'config.pipeline_name',
                'default' => true
            ],
            'database' => [
                'caption' => __('Database'),
                'extract' => 'config.database',
                'default' => true
            ],
            'created_by' => [
                'key' => 'creator.username',
                'caption' => __('Creator'),
                'sort' => false,
                'options' => $this->Creator->find('list')->toArray(),
                'filter' => 'select',
                'default' => true
            ],
            'modified' => [
                'caption' => __('Last modified'),
                'action' => 'view',
                'sort' => true,
                'default' => true
            ],
            'created' => [
                'caption' => __('Created on'),
                'action' => 'view',
                'sort' => true,
                'default' => true
            ]
        ];

        return parent::getColumns($selected, $default, $options);
    }

    /**
     * Get pagination parameters
     *
     * @param array $params Parsed request parameters
     * @param array $columns
     *
     * @return array
     */
    public function getPaginationParams(array $params = [], array $columns = [])
    {
        $pagination = parent::getPaginationParams($params, $columns);

        return [
                'order' => ['id' => 'DESC'],
                'sortableFields' => $this->getSortableFields($columns),
                'limit' => 100,
                'maxLimit' => 1000
            ] + $pagination;
    }

    /**
     * Contain data necessary for table columns
     *
     * @param \Cake\ORM\Query $query
     * @param array $options
     * @return Query
     */
    public function findContainColumns(Query $query, array $options)
    {
        $query = $query
            ->contain(['Creator'])
            ->select($this)
            ->select($this->Creator);

        return $query;
    }
}
