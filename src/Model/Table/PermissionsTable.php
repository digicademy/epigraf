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

use App\Model\Entity\Permission;
use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;
use ArrayObject;
use App\Cache\Cache;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use Cake\I18n\FrozenTime;

/**
 * Permissions table
 *
 * # Relations
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class PermissionsTable extends BaseTable
{

    /**
     * Predefined user roles
     *
     * @var string[]
     */
    static $userRoles = [
        'guest' => 'Gast',
        'bot' => 'Bot',
        'reader' => 'Leser',
        'desktop' => 'Desktopnutzer',
        'coder' => 'Kodierer',
//        'tester' => 'Tester',
        'author' => 'Bearbeiter',
        'editor' => 'Redakteur',
        'admin' => 'Administrator',
        'devel' => 'Entwickler'
    ];


    static $permissionTypes = ['access' => 'Access', 'lock' => 'Lock'];
    static $requestTypes = ['web' => 'Web-Zugriff', 'api' => 'API-Zugriff'];
    static $requestModes = ['default' => 'Default', 'code' => 'Code', 'preview' => 'View'];
    static $entityTypes = ['databank' => 'Databank', 'record' => 'Record'];

    static $_endpoints = null;
    public $captionField = 'id';

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

        $this->setTable('permissions');
        $this->setEntityClass('Permission');
        $this->setPrimaryKey('id');
        $this->belongsTo('Users');
    }

    /**
     * Before marshall method
     *
     * @param EventInterface $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     *
     * @return void
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        if (isset($data['user_role']) && ($data['user_role'] === '')) {
            $data['user_role'] = null;
        }

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

        return $validator;
    }

    /**
     * Extract search parameters from query parameters
     *
     * The result is passed to the finder functions.
     * Parses
     *
     * @param array $requestParameters
     * @param string $requestPath
     * @param string $requestAction
     * @return array
     */
    public function parseRequestParameters(array $requestParameters = [], $requestPath = '', $requestAction = ''): array
    {
        $params = [];
        $params['action'] = $requestAction;
        $params['columns'] = Attributes::commaListToStringArray($requestParameters['columns'] ?? '');

        foreach ($this->getColumns() as $fieldName => $fieldConfig) {

            if (!empty($fieldConfig['filter'])) {

                $type = $fieldConfig['filter'] ?? 'text';
                if ($type === 'text') {
                    $params[$fieldName] = $requestParameters[$fieldName] ?? '';
                }
                elseif ($type === 'select') {
                    $params[$fieldName] = Attributes::commaListToStringArray($requestParameters[$fieldName] ?? '');
                }


            }
        }

        if (isset($requestParameters['id'])) {
            $params['id'] = $requestParameters['id'];
        }

        return Arrays::array_remove_empty($params);
    }

    /**
     * Get a list of controller classes
     *
     * @param array $plugins A list of plugins that should be scanned. The app folder is always scanned.
     *
     * @return string[]
     */
    static public function getControllers($plugins = [])
    {
        $results = Files::getClassesInPath(ROOT . DS . 'src/Controller' . DS, 'App\Controller');
        foreach ($plugins as $plugin) {
            $results = array_merge(
                $results,
                Files::getClassesInPath(
                    ROOT . DS . 'plugins' . DS . $plugin . DS . 'src/Controller' . DS,
                    $plugin . '\Controller'
                )
            );
        }

        return $results;
    }

    /**
     * Get the actions of a class
     *
     * @param $className
     * @param $ignoreList
     * @return array
     */
    static public function getActions($className, $ignoreActions = null, $ignoreClasses = null)
    {

        // TODO: all public methods not derived from Cake\Controller\Controller are actions.
        //       get them and remove / hide custom methods not gedacht als actions from the controllers
        if ($ignoreActions === null) {
            $ignoreActions = [
                'beforeFilter',
                'afterFilter',
                'beforeRender',
                'initialize'
            ];
        }

        if ($ignoreClasses === null) {
            $ignoreClasses = [
                'Cake\Controller\Controller',
                'App\Controller\AppController',
                'Epi\Controller\AppController'
            ];
        }

        $class = new \ReflectionClass($className);
        $defaultRequests = ['web' => [], 'api' => []];
        $defaultRoles =
            [
                'web' => array_combine(
                    array_keys(PermissionsTable::$userRoles),
                    array_map(
                        fn($role) => in_array($role, ['admin', 'devel']) ? ['*'] : [],
                        array_keys(PermissionsTable::$userRoles)
                    )
                ),
                'api' => array_fill_keys(PermissionsTable::$userRoles, [])
            ];

        $allowed = $class->getDefaultProperties()['authorized'] ?? [];
        $allowed = array_merge($defaultRequests, $allowed);

        $grouped = [];
        foreach ($allowed as $request => $roles) {
            $roles = array_merge($defaultRoles[$request], $roles);

            foreach ($roles as $role => $actions) {
                foreach ($actions as $action) {
                    $grouped[$action][$role][] = $request;
                }
            }
        }

        $actions = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $actions = array_filter($actions, fn($action) => !in_array($action->name, $ignoreActions));
        $actions = array_filter($actions, fn($action) => !in_array($action->class, $ignoreClasses));
        //$actions = array_filter($actions, fn($action) => $action->class === $className);
        $actionsNames = array_map(fn($action) => $action->name, $actions);
        $actionsAuthorized = array_map(
            fn($action) => array_merge_recursive(
                $grouped['*'] ?? [],
                $grouped[$action->name] ?? []
            )
            ,
            $actions
        );
        $actionsPermissions = array_combine($actionsNames, $actionsAuthorized);

        return $actionsPermissions;
    }

    /**
     * Get a nested list of all controllers and actions (endpoints)
     *
     * The app namespace and the epi plugin are searched.
     *
     * @return array
     */
    static public function getEndpoints()
    {
        if (empty(PermissionsTable::$_endpoints)) {
            PermissionsTable::$_endpoints = Cache::remember(
                'endpoints',
                function () {
                    $controllers = PermissionsTable::getControllers(['Epi']);
                    $resources = [];
                    foreach ($controllers as $className) {
                        $actions = PermissionsTable::getActions($className);
                        if (!empty($actions)) {
                            $classParts = explode('\\', $className);
                            $plugin = strtolower($classParts[0] ?? '');
                            $controller = str_replace('controller', '', strtolower($classParts[2] ?? ''));

                            $resources[$plugin][$controller] = $actions;
                        }
                    }
                    return $resources;
                },
                '_cake_model_'
            );
        }

        return PermissionsTable::$_endpoints;
    }

    /**
     * Check whether a user role has been authorized to access an endpoint
     *
     * @param array $endpoint An array containing the endpoint components scope, controller, action
     * @param string $user_role The user role (e.g. guest, author or admin)
     * @param string $user_request Either web or api.
     * @return boolean
     */
    static public function getEndpointHasRole($endpoint, $user_role, $user_request)
    {
        $endpoints = PermissionsTable::getEndpoints();
        $scope = Inflector::underscore($endpoint['scope'] ?? '');
        $controller = Inflector::underscore($endpoint['controller'] ?? '');
        $action = Inflector::underscore($endpoint['action'] ?? '');
        $permission = $endpoints[$scope][$controller][$action] ?? [];
        return in_array($user_request, $permission[$user_role] ?? []);
    }

    /**
     * Get the list of endpoints
     *
     * @return array
     */
    static public function getEndpointOptions()
    {
        $options = [];

        $resources = PermissionsTable::getEndpoints();
        foreach ($resources as $plugin => $controllers) {
            foreach ($controllers as $controller => $actions) {
                foreach ($actions as $action => $roles) {
                    $options[] = implode('/', [$plugin, $controller, $action]);
                }
            }
        }

        return $options;
    }

    /**
     * Get a permission table for endpoints by role
     *
     * @param string|integer $level Filter by level
     * @return array
     */
    public function getEndpointTable($level = 'all')
    {
        $resources = PermissionsTable::getEndpoints();

        // Convert tree to table
        $rows = [];
        $columns = ['id' => 'Endpoint', 'scope' => 'Scope', 'controller' => 'Controller', 'action' => 'Action'];
        $columns = array_merge($columns, PermissionsTable::$userRoles);

        foreach ($resources as $plugin => $controllers) {
            $id = $plugin;
            $rows[] = ['level' => 0, 'id' => $id, 'scope' => $plugin];

            foreach ($controllers as $controller => $actions) {
                $parent = $id;
                $id = $plugin . '/' . $controller;
                $rows[] = [
                    'level' => 1,
                    'id' => $id,
                    'parent' => $parent,
                    'scope' => $plugin,
                    'controller' => $controller
                ];

                foreach ($actions as $action => $roles) {
                    $parent = $plugin . '/' . $controller;
                    $id = $plugin . '/' . $controller . '/' . $action;
                    $row = [
                        'level' => 2,
                        'id' => $id,
                        'parent' => $parent,
                        'scope' => $plugin,
                        'controller' => $controller,
                        'action' => $action
                    ];

                    foreach ($roles as $role => $requests) {
                        $row[$role] = implode(', ', $requests);
                        $columns[$role] = $columns[$role] ?? $role;
                    }
                    $rows[] = $row;
                }
            }
        }

        if (is_numeric($level)) {
            $rows = array_filter($rows, fn($row) => $row['level'] >= $level);
        }

        return ['rows' => $rows, 'columns' => $columns];
    }

    /**
     * Remove all expired locks
     *
     * @return mixed
     */
    public function pruneLocks()
    {
        return $this->deleteAll(['permission_type' => 'lock', 'permission_expires <' => FrozenTime::now()]);
    }

    /**
     * Check lock status of an entity
     *
     * An entity is locked, if lock entries exit in the permissions table
     * that do not match the given permission ID.
     *
     * @param string $database
     * @param string $tablename
     * @param int $entityId
     * @param int|null $permissionId The permission ID of previous lock operations or null
     *
     * @return int Number of locks excluding locks identified by the permissionId
     */
    public function isLocked($database, $tablename, $entityId, $permissionId = null)
    {
        $conditions = [
            'permission_type' => 'lock',
            'entity_type' => 'record',
            'entity_name' => $database . '.' . $tablename,
            'entity_id' => $entityId
        ];

        if (!is_null($permissionId)) {
            $conditions['id <>'] = intval($permissionId);
        }

        $this->pruneLocks();
        return $this->find('all')
            ->where($conditions)
            ->count();
    }

    /**
     * Check user lock status of an entity
     *
     * An entity is user locked, if lock entries exit in the permissions table
     * that do belong to the given user.
     *
     * @param string $database
     * @param string $tablename
     * @param int $entityId
     * @param int|null $userId User ID
     *
     * @return int Number of locks (except with the ID given permission ID)
     */
    public function isLockedByUser($database, $tablename, $entityId, $userId = null)
    {
        $conditions = [
            'permission_type' => 'lock',
            'entity_type' => 'record',
            'entity_name' => $database . '.' . $tablename,
            'entity_id' => $entityId,
            'user_id' => $userId
        ];

        $this->pruneLocks();
        return $this->find('all')
            ->where($conditions)
            ->count();
    }

    /**
     * Find the user's lock record for an entity
     *
     * @param $database
     * @param $tableName
     * @param $entityId
     * @param $userId
     * @return Permission|null
     */
    public function getLock($database, $tableName, $entityId, $userId)
    {
        $conditions = [
            'permission_type' => 'lock',
            'user_id' => $userId,
            'entity_type' => 'record',
            'entity_name' => $database . '.' . $tableName,
            'entity_id' => $entityId
        ];

        $this->pruneLocks();
        return $this->find('all')
            ->where($conditions)
            ->first();
    }

    /**
     * Lock records
     *
     * @param int|null $permissionId The permission ID of previous lock operations or null
     * @param int $duration
     * @param string $database
     * @param string $tableName
     * @param int|null $entityId
     * @param int $userId
     *
     * @return int|null The permission ID of the lock operation
     */
    public function createLock(
        $permissionId,
        $duration = 60,
        $database = null,
        $tableName = null,
        $entityId = null,
        $userId = null
    ) {
        //Check for lock
        // TODO: what if a concurrent user locks the entity in the meantime?
        //       Prevent concurrent locks in the save operation by validation rules.
        if ($this->isLocked($database, $tableName, $entityId, $permissionId)) {
            return null;
        }

        // Define lock data
        $expires = FrozenTime::now()->addSeconds($duration);
        $entityName = $database . '.' . $tableName;
        $permissionData = [
            'permission_type' => 'lock',
            'permission_expires' => $expires,
            'entity_type' => 'record',
            'entity_name' => $entityName,
            'entity_id' => $entityId,
            'user_id' => $userId
        ];

        // If a permission ID is provided, the user already has locked the entity
        // In this case, update the lock. Otherwise, create a new lock.
        $this->pruneLocks();
        if (!is_null($permissionId)) {
            $permissionEntity = $this->find('all')->where(['id' => $permissionId])->first();
        }

        if (empty($permissionEntity)) {
            $permissionEntity = $this->newEntity([]);
        }

        $permissionEntity = $this->patchEntity($permissionEntity, $permissionData);

        $result = $this->save($permissionEntity);
        return $result ? $permissionEntity->id : null;
    }

    /**
     * Release locks
     *
     * @param int|null $permissionId The permission ID of previous lock operations or null
     * @return bool|int
     */
    public function releaseLock($permissionId)
    {
        if (is_null($permissionId)) {
            return false;
        }
        return $this->deleteAll(['id' => intval($permissionId)]);
    }

    /**
     * Check permissions
     *
     * @param array $permission = [
     *      //Permission condition array
     *     'user_id'=>'',           // User ID (either provide user_id or user_role)
     *     'user_role'=>'',         // User role (either provide user_id or user_role)
     *     'user_request'=>'',      // api or web
     *     'entity_type'=>'',       // database or record
     *     'entity_name'=>'',       // Database name or * as a  wildcard
     *     'entity_id'=>'',         // For record permissions, add the ID of the record
     *     'permission_type'=>'',   // access or lock
     *     'permission_name'=>''    // The endpoint name or * as a wildcard
     * ]
     *
     * @return int Number of matched permissions
     */
    public function hasPermission($permission)
    {
        $conditions = $permission;

        // Allow both, specific users or user roles.
        // But ignore combinations of the user ID with user roles that were not requested
        unset($conditions['user_id']);
        unset($conditions['user_role']);

        $user_conditions = [];
        if (isset($permission['user_id'])) {
            $user_conditions[] = [
                'user_id' => $permission['user_id'],
                'user_role IS' => null
            ];
        }

        if (isset($permission['user_role'])) {
            $user_conditions[] = [
                'user_id IS' => null,
                'user_role' => $permission['user_role']
            ];
        }

        if (isset($permission['user_role']) && isset($permission['user_id'])) {
            $user_conditions[] = [
                'user_id' => $permission['user_id'],
                'user_role' => $permission['user_role']
            ];
        }

        $conditions[] = ['OR' => $user_conditions];

        // Allow wildcards for endpoints
        unset($conditions['permission_name']);
        $endpoint_conditions = [];

        $endpoint_conditions[] = ['permission_name' => '*'];
        if (isset($permission['permission_name'])) {
            $endpoint_conditions[] = ['permission_name' => $permission['permission_name']];
        }
        $conditions[] = ['OR' => $endpoint_conditions];

        // Allow wildcards for databases
        unset($conditions['entity_name']);
        $entity_conditions = [];

        $entity_conditions[] = ['entity_name' => '*'];
        if (isset($permission['entity_name'])) {
            $entity_conditions[] = ['entity_name' => $permission['entity_name']];
        }
        $conditions[] = ['OR' => $entity_conditions];

        return $this->find('all')->where($conditions)->count();
    }

    /**
     * Add a permission
     *
     * @param array $permission Permission condition array with the following keys:
     *                           user_id, user_role, user_request,
     *                           entity_type, entity_name, entity_id,
     *                           permission_type, permission_name
     * @return bool|\Cake\Datasource\EntityInterface
     */
    public function addPermission($permission)
    {
        //Check for permission
        if ($this->hasPermission($permission)) {
            return true;
        }

        // Create new permission
        $perm = $this->newEntity($permission);
        return ($this->save($perm));
    }

    /**
     * Remove the given permission
     *
     * @param array $permission Permission condition array with the following keys:
     *                           user_id, user_role, user_request,
     *                           entity_type, entity_name, entity_id,
     *                           permission_type, permission_name
     * @return true
     */
    public function removePermission($permission)
    {
        $this->deleteAll($permission);
        return true;
    }

    /**
     * Allow endpoint access to the currently logged in user
     *
     * @param string $endpoint The controller name, followed by a slash, and the action name
     * @param string $database The database name. For app level endpoints leave blank
     * @param array $requestedBy Access by 'web' or 'api'.
     * @return bool|\Cake\Datasource\EntityInterface
     */
    public function allowEndpoint($endpoint, $database = '', $requestedBy = 'web')
    {
        $userId = BaseTable::$userId;

        $permission = [
            'user_id' => $userId,
            'user_request' => $requestedBy,
            'entity_type' => $database !== '' ? 'databank' : '',
            'entity_name' => $database,
            // 'entity_id' => null,
            'permission_type' => 'access',
            'permission_name' => $endpoint
        ];

        return $this->addPermission($permission);
    }

    /**
     * Remove all permissions of the currently logged in user to a specific endpoint
     *
     * @param string $endpoint The controller name, followed by a slash, and the action name.
     * @param string $database Leave blank for app level actions, otherwise set to the project database name.
     * @param string|null $requestedBy Leave empty to remove all permission.
     *                                 Set to 'web' or 'api' to only remove permissions related
     *                                 to the specific request option.
     * @return int
     */
    public function denyEndpoint($endpoint, $database = '', $requestedBy = null)
    {
        $userId = BaseTable::$userId;

        $permission = [
            'user_id' => $userId,
            'entity_type' => $database !== '' ? 'databank' : '',
            'entity_name' => $database,
            'permission_type' => 'access',
            'permission_name' => $endpoint
        ];

        if ($requestedBy !== null) {
            $permission['user_request'] = $requestedBy;
        }

        return $this->removePermission($permission);
    }

    /**
     * Contain table data
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findContainFields(Query $query, array $options)
    {
        $query = $query->contain(['Users']);
        return $query;
    }

    /**
     * Contain view data
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findContainAll(Query $query, array $options): Query
    {
        $contain = ['Users'];
        $query = $query->contain($contain);
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
        $users = $this
            ->find('list')
            ->orderAsc('username');

        $default = [
            'user_id' => [
                'caption' => __('User'),
                'key' => 'username', // Show username, not ID
                'type' => 'select',
                'options' => $users,
                'empty' => true,
                'default' => false,
                'action' => ['view', 'edit', 'add'] //TODO: irrelevant?
            ],

            'username' => [
                'caption' => __('User'),
                'field' => 'Users.username',
                'type' => 'text',
                'empty' => true,
                'default' => true,
                'sort' => true,
                'action' => ['index'],
                'filter' => 'text'
            ],

            'user_role' => [
                'caption' => __('Role'),
                'type' => 'select',
                'empty' => true,
                'default' => true,
                'sort' => true,
                'options' => PermissionsTable::$userRoles,
                'filter' => 'select'
            ],

            'user_request' => [
                'caption' => __('Request'),
                'type' => 'select',
                'empty' => true,
                'default' => true,
                'sort' => true,
                'options' => PermissionsTable::$requestTypes,
                'filter' => 'select'
            ],

            'entity_type' => [
                'caption' => __('Entity Type'),
                'type' => 'select',
                'empty' => true,
                'options' => PermissionsTable::$entityTypes,
                'filter' => 'select',
                'sort' => true,
                'default' => true
            ],

            'entity_name' => [
                'caption' => __('Entity name'),
                'filter' => 'text',
                'sort' => true,
                'default' => true
            ],

            'entity_id' => [
                'caption' => __('Entity ID'),
                'type' => 'text',
                'sort' => true,
                'default' => true
            ],
            'permission_type' => [
                'caption' => __('Permission Type'),
                'type' => 'select',
                'options' => PermissionsTable::$permissionTypes,
                'filter' => 'select',
                'sort' => true,
                'default' => true
            ],
            'permission_name' => [
                'caption' => __('Endpoint name'),
                'filter' => 'text',
                'sort' => true,
                'default' => true
            ],

            'permission_expires' => [
                'caption' => __('Expires'),
                'sort' => true,
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
                'order' => ['user_role' => 'ASC'],
                'sortableFields' => $this->getSortableFields($columns),
                'limit' => 100,
                'maxLimit' => 1000
            ] + $pagination;
    }

}
