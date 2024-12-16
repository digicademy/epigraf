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

use App\Model\Entity\Databank;
use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use Cake\Core\Plugin;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Query;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Databanks table
 *
 * # Relations
 * @property \Cake\ORM\Association\HasMany $Permissions
 */
class DatabanksTable extends BaseTable
{

    /**
     * Save last database for activate/deactivateDatabase method
     *
     * @var bool
     */
    public $previousdatabase = false;

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
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('databanks');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->hasMany('Users');

        $this->hasMany('Permissions', [
            'className' => 'Permissions',
            'foreignKey' => 'entity_id',
            'conditions' => ['Permissions.entity_type' => 'databank']
        ]);

        $this->addBehavior('Timestamp');
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
            ->notBlank('name')
            ->add('name', 'validFormat', [
                'rule' => ['custom', '/^[0-9a-zA-Z_]+$/'],
                'message' => 'Only alphanumeric characters and underscore are allowed.'
            ]);

        return $validator;
    }

    /**
     * Get database connections
     *
     * @return array
     */
    public function getConnections()
    {
        $connection = ConnectionManager::get('default');
        $results = $connection->execute('SHOW DATABASES')->fetchAll('num');
        $results = array_map(
            function ($x) {
                return $x[0];
            },
            $results
        );

        return $results;
    }

    public function getPresets()
    {
        $presetFolder = Plugin::path('Epi') . 'config' . DS . 'presets' . DS;
        $presets = ['' => 'Empty database'];

        if (is_dir($presetFolder)) {
            $subfolders = array_filter(glob($presetFolder . '/*'), 'is_dir');

            foreach ($subfolders as $subfolder) {
                $folderName = basename($subfolder);
                $jsonFilePath = $subfolder . '/' . $folderName . '.json';


                if (file_exists($jsonFilePath)) {
                    try {
                        $jsonContent = file_get_contents($jsonFilePath);
                        $jsonData = json_decode($jsonContent, true);
                        $presetName = $jsonData['name'] ?? $folderName;
                        $presets[$folderName] = $presetName;
                    } catch (Exception $e) {
                        // Ignore
                    }
                }
            }
        }

        return $presets;
    }

    /**
     * Get permissions of a user
     *
     * ## Options
     * - user An array with the keys role and id
     *
     * @param Query $query
     * @param array $options Pass the user
     *
     * @return mixed
     */
    public function findAllowedBy(Query $query, array $options)
    {
        $user = $options['user'] ?? [];

        // All but admins and devels need specific permissions
        if (!in_array($user['role'] ?? '', ['devel', 'admin'])) {

            // Access permission
            $conditions = ['permission_type' => 'access'];

            // By user role and/or user ID
            if (empty ($user)) {
                $conditions['Permissions.user_id IS'] = null;
                $conditions['Permissions.user_role'] = 'guest';
            }
            else {
                $conditions[] = [
                    'OR' => [
                        [
                            'Permissions.user_id' => $user['id'] ?? ''
                        ],
                        [
                            'Permissions.user_id IS' => null,
                            'Permissions.user_role' => $user['role'] ?? 'guest'
                        ]
                    ]
                ];
            }

            $query = $query->find('all')
                ->matching('Permissions', function ($q) use ($conditions) {
                    return $q->where($conditions);
                });
        }

        if ($options['aslist'] ?? false) {
            $query = $query->formatResults(function ($results) {
                return $results->combine('name', 'name');
            });
        }

        return $query->order(['name' => 'ASC']);
    }

    /**
     * Activates the given project database for further queries.
     *
     * Returns the databank entity.
     * Throws a BadRequestException if the database is not available.
     *
     * @param $dbname
     *
     * @return array|\Cake\Datasource\EntityInterface
     */
    public function activateDatabase($dbname)
    {
        // Check target database
        if (empty($dbname)) {
            throw new RecordNotFoundException('Missing target database.');
        }

        $targetdb = $this->find('all')->where(['name' => Databank::addPrefix($dbname)])->first();

        if (empty($targetdb)) {
            throw new RecordNotFoundException('Target database not found.');
        }

        // Save database name for deactivateDatabase
        $this->previousdatabase = BaseTable::getDatabaseName();

        // Open target database (isReady initializes the new database connection)
        if (!$targetdb->activateDatabase()) {
            throw new RecordNotFoundException('Target database not ready.');
        }

        return $targetdb;
    }

    /**
     * Deactivate current database by activating previous database
     *
     * @return array|\Cake\Datasource\EntityInterface
     */
    public function deactivateDatabase()
    {
        return $this->activateDatabase($this->previousdatabase);
    }


    /**
     * Extract search parameters from request parameters
     *
     * @param array $requestParameters
     * @param string $requestPath
     * @param string $requestAction
     * @return array
     */
    public function parseRequestParameters(array $requestParameters = [], $requestPath = '', $requestAction = ''): array
    {
        $params = ['action' => $requestAction];

        $params['id'] = $requestParameters['id'] ?? null;

        //@deprecated: Remove fields parameter
        $params['columns'] = Attributes::commaListToStringArray($requestParameters['columns'] ?? $requestParameters['fields'] ?? '');

        return Arrays::array_remove_empty($params);
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
                'order' => ['Databanks.name' => 'asc'],
                'limit' => 100
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
            'name' => [
                'caption' => __('Name'),
                'sort' => 'name',
                'width' => 150,
                'default' => true,
                'name' => 'name',
                'key' => 'caption',
                'selected' => true
            ],
            'category' => [
                'caption' => __('Category'),
                'width' => 150,
                'default' => true,
                'selected' => true
            ],
            'projects' => [
                'caption' => __('Projects'),
                'sort ' => false,
                'width' => 80,
                'default' => true,
                'link' => [
                    'plugin' => 'epi',
                    'controller' => 'Projects',
                    'database' => '{caption}',
                    'action' => 'index',
                    '?' => ['load' => true]
                ],
                'name' => 'projects',
                'key' => 'projects_count',
                'selected' => true
            ],
            'articles' => [
                'caption' => __('Articles'),
                'sort ' => false,
                'width' => 80,
                'link' => [
                    'plugin' => 'epi',
                    'controller' => 'Articles',
                    'database' => '{caption}',
                    'action' => 'index',
                    '?' => ['load' => true]
                ],
                'default' => true,
                'key' => 'articles_count'
            ],
            'properties' => [
                'caption' => __('Categories'),
                'sort ' => false,
                'width' => 80,
                'link' => [
                    'plugin' => 'epi',
                    'controller' => 'Properties',
                    'database' => '{caption}',
                    'action' => 'index',
                    '?' => ['load' => true]
                ],
                'default' => true,
                'key' => 'properties_count'
            ],
            'files' => [
                'caption' => __('Files'),
                'sort ' => false,
                'width' => 80,
                'link' => [
                    'plugin' => 'epi',
                    'controller' => 'Files',
                    'database' => '{caption}',
                    'action' => 'index',
                    '?' => ['load' => true]
                ],
                'default' => true,
                'key' => 'files_count'
            ],
            'notes' => [
                'caption' => __('Notes'),
                'sort ' => false,
                'width' => 80,
                'link' => [
                    'plugin' => 'epi',
                    'controller' => 'Notes',
                    'database' => '{caption}',
                    'action' => 'index',
                    '?' => ['load' => true]
                ],
                'default' => true,
                'key' => 'notes_count'
            ],
            'users' => [
                'caption' => __('Users'),
                'sort ' => false,
                'width' => 80,
                'link' => [
                    'plugin' => 'epi',
                    'controller' => 'Users',
                    'database' => '{caption}',
                    'action' => 'index',
                    '?' => ['load' => true]
                ],
                'default' => true,
                'key' => 'users_count'
            ],
            'types' => [
                'caption' => __('Types'),
                'sort ' => false,
                'width' => 80,
                'link' => [
                    'plugin' => 'epi',
                    'controller' => 'Types',
                    'database' => '{caption}',
                    'action' => 'index',
                    '?' => ['load' => true]
                ],
                'default' => true,
                'key' => 'types_count'
            ],
            'version' => [
                'caption' => __('Version'),
                'sort ' => true,
                'width' => 50,
                'default' => true
            ],
            'available' => [
                'caption' => __('Available'),
                'width' => 50,
                'default' => true,
                'align' => 'center',
                'type' => 'test'
            ],
            'versionok' => [
                'caption' => __('Valid'),
                'width' => 50,
                'default' => true,
                'align' => 'center',
                'type' => 'test'
            ],
            'published' => [
                'caption' => __('Published'),
                'sort ' => 'published',
                'width' => 50,
                'default' => true,
                'align' => 'center',
                'type' => 'badge'
            ],

            'created' => [
                'caption' => __('Created'),
                'sort ' => true,
                'width' => 100,
                'default' => true
            ],
            'modified' => [
                'caption' => __('Modified'),
                'sort ' => true,
                'width' => 100,
                'default' => true
            ],

            'description' => [
                'caption' => __('Description'),
                'sort ' => false,
                'width' => 200,
                'default' => false
            ],

            'id' => [
                'caption' => __('ID'),
                'sort ' => true,
                'width' => 50,
                'default' => true
            ],
        ];

        return parent::getColumns($selected, $default, $type);
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
            'id' => false
        ];

        $params = array_merge($default, $params);

        // ID
        $id = $params['id'] ?? false;
        if ($id) {
            $query = $query->where(['Databanks.id' => $id]);
        }

        return $query;
    }

}
