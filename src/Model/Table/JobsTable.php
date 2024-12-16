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
            ->requirePresence('typ', 'create')
            ->notEmptyString('typ');

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
     * @param EntityInterface $entity
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity)
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
        if (!in_array($requestAction, ['add', 'download'])) {
            return $requestParameters;
        }

        $pipeline_id = $requestParameters['pipeline'] ?? null;

        // Pipeline from user settings
        if (is_null($pipeline_id)) {
            $user = BaseTable::$user;
            if (($requestParameters['scope'] ?? 'article') == 'book') {
                $pipeline_id = $user['pipeline_book_id'] ?? null;
            }
            else {
                $pipeline_id = $user['pipeline_article_id'] ?? null;
            }
            $requestParameters['pipeline'] = $pipeline_id;
            unset($requestParameters['scope']);
        }

        // Pipeline from norm_iri
        elseif (!is_numeric($pipeline_id)) {
            $pipelinesTable = $this->fetchTable('Pipelines');
            $pipeline_id = $pipelinesTable
                ->find('all')
                ->where(['norm_iri' => $pipeline_id])
                ->first();

            $requestParameters['pipeline'] = $pipeline_id['id'] ?? null;
        }

        return $requestParameters;
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
                'default' => true
            ],
            'typ' => [
                'caption' => __('Type'),
                'default' => true
            ],
            'status' => [
                'caption' => __('Status'),
                'default' => true
            ],
            'progress' => [
                'caption' => __('Progress'),
                'default' => true
            ],
            'progressmax' => [
                'caption' => __('Max progress'),
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

        return parent::getColumns($selected, $default, $type);
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
}
