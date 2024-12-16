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

use App\Utilities\Converters\Attributes;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\I18n\FrozenTime;
use Cake\Utility\Hash;

/**
 * Users Model
 *
 * # Relations
 * @property PermissionsTable $PermissionsById
 * @property PermissionsTable $PermissionsByRole
 * @property DatabanksTable $Databanks
 * @property PipelinesTable $ArticlePipelines
 * @property PipelinesTable $BookPipelines
 */
class UsersTable extends BaseTable
{

    public $parameters = [
        'id' => 'list',
        'columns' => 'list',
        'order' => 'list',
        'sort' => 'list',
        'selected' => 'list',
        'direction' => 'list',
        'iri' => 'list',
        'uname' => 'string',
        'role' => 'list',
        'load' => 'list',
        'save' => 'list'
    ];

    public $captionField = 'username';

    public static $locales = ['de_DE.UTF-8' => 'Deutsch', 'en_EN.UTF-8' => 'English'];

    public static $themes = [
        'light' => 'Light',
        'dark' => 'Dark',
        'terracotta' => 'Terracotta',
        'serif' => 'Serif'
//        'sapphire' => 'Sapphire',
//        'leave' => 'Leave'
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

        $this->setTable('users');
        $this->setDisplayField('username');
        $this->setPrimaryKey('id');

        $this->belongsTo('Databanks');

        $this->belongsTo('ArticlePipelines', [
            'className' => 'Pipelines',
            'foreignKey' => 'pipeline_article_id'
        ]);

        $this->belongsTo('BookPipelines', [
            'className' => 'Pipelines',
            'foreignKey' => 'pipeline_book_id'
        ]);

        $this->hasMany('PermissionsById', [
            'className' => 'Permissions',
            'propertyName' => 'permissions',
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        $this->hasMany('PermissionsByRole', [
            'className' => 'Permissions',
            'foreignKey' => 'user_role',
            'bindingKey' => 'role',
            'propertyName' => 'rolepermissions',
            'conditions' => ['PermissionsByRole.user_id IS' => null],
            'dependent' => false,
            'cascadeCallbacks' => false
        ]);
    }

    /**
     * Default validation rules.
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
            ->notEmptyString('username', 'A username is required')
            ->add('username', [
                'length' => [
                    'rule' => ['maxLength', 50],
                    'message' => 'Usernames should be 50 characters long at maximum.',
                ]
            ])
            ->add('username', 'validFormat', [
                'rule' => ['custom', '/^[0-9a-zA-Z]+$/'],
                'message' => 'Only alphanumeric characters are allowed.'
            ])
            ->notEmptyString('password', 'A password is required')
            ->add('role', 'inList', [
                'rule' => ['inList', array_keys(PermissionsTable::$userRoles)],
                'message' => 'Please enter a valid role'
            ])
            ->notEmptyString('name', 'A full name is required')
            ->notEmptyString('acronym', 'An acronym is required')
            ->notEmptyString('norm_iri', 'A unique IRI fragment is required')
            ->email('email');

        return $validator;
    }

    /**
     * Authentication finder
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findAuth(Query $query, array $options)
    {
        $query->contain(['Databanks', 'PermissionsById', 'PermissionsByRole']);
        return $query;
    }


    /**
     * Find entities by request parameters
     *
     * @param Query $query
     * @param array $options request parameters
     *
     * @return Query
     */
    public function findHasParams(Query $query, array $options): Query
    {
        $query = parent::findHasParams($query, $options);

        // Add activity fields and sort active users to the top
        $query = $query->formatResults(function ($entities) {

            $connected = $this->sqlConnectedUsers();
            foreach ($entities as $no => $user) {
                $sqluser = 'epi_' . $user->username;
                $user->number = $no;
                $user->sqlconnections = !empty($connected[$sqluser]) ? $connected[$sqluser] : '';
                $user->active = !empty($user->sqlconnections) || (!empty($user->lastaction) && $user->lastaction->wasWithinLast('10 minutes'));
                unset($connected[$sqluser]);
            }

            // Sort active users to the top
            $entities = $entities
                ->compile()
                ->sortBy(function ($user) {
                    return !$user->active . '-'
                        . !$user->sqlconnections . '-'
                        . str_pad($user->number, 10, '0', STR_PAD_LEFT);
                }, SORT_ASC, SORT_STRING);

            return $entities;
        });

        return $query;
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
        // TODO: automatically select contained associations
        $query = $query
            ->contain(['Databanks', 'ArticlePipelines', 'BookPipelines'])
            ->select($this)
            ->select($this->Databanks)
            ->select($this->ArticlePipelines)
            ->select($this->BookPipelines);

        $query = $query->find('sortFields', $options);

        return $query;
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
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->isUnique(['norm_iri']));

        return $rules;
    }

    /**
     * Returns the schema table object describing this table's properties.
     *
     * @return \Cake\Database\Schema\TableSchemaInterface
     */
    public function getSchema(): TableSchemaInterface
    {
        $schema = parent::getSchema();
        $schema->setColumnType('settings', 'json');
        return $schema;
    }

    /**
     * Handle user settings, call parent
     *
     * @param EntityInterface $entity
     * @param array $data
     * @param array $options
     * @return EntityInterface
     */
    public function patchEntity(EntityInterface $entity, array $data, array $options = []): EntityInterface
    {
        // merge settings
        if (isset($data['settings']) && isset($entity['settings'])) {
            $data['settings'] = array_replace_recursive($entity['settings'], $data['settings']);
        }
        // see ActionsComponent $settings = array_merge($settingsAnte, $settings);
        return parent::patchEntity($entity, $data, $options);
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
        if (empty($options['keeppasswords'])) {
            if (empty($entity->accesstoken)) {
                $entity->accesstoken = $this->generateAccesstoken();
            }
            if (empty($entity->password)) {
                $entity->password = $this->generateAccesstoken();
            }
        }

        if (empty($entity['norm_iri'])) {
            $entity['norm_iri'] = Attributes::cleanIdentifier($entity->username);
        }
    }

    /**
     * Generate access token
     *
     * @param $length
     *
     * @return string
     */
    public static function generateAccesstoken($length = 20)
    {
        //use Cake\Utility\Text;
        //sha1(Text::uuid());

        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        if ($length > 0) {
            $rand_id = "";
            for ($i = 1; $i <= $length; $i++) {
                mt_srand(round((double)microtime() * 1000000));
                $num = mt_rand(0, strlen($chars));
                $rand_id .= substr($chars, $num, 1);
            }
        }
        return $rand_id;
    }

    /**
     * Update last action and retrieve a new user record every 30 seconds
     *
     * @param array $user A user entity retrieved with the Auth component
     * @return array
     */
    public function updateActive($user)
    {
        if (empty($user['id'] ?? false)) {
            return $user;
        }

        if (isset($user['lastaction']) && ($user['lastaction']->wasWithinLast('30 seconds'))) {
            return $user;
        }

        $this->updateQuery()
            ->update()
            ->set(['lastaction' => FrozenTime::now()])
            ->where(['id' => $user['id']])
            ->execute();

        // Get new user object
        $user = $this->get($user['id'], ['finder' => 'auth'])->toArray();

        return $user;
    }

    /**
     * Update user settings
     *
     * @param array $user A user entity retrieved with the Auth component.
     * @param string $scope For example 'ui' or 'paths'
     * @param string $key
     * @param mixed $value
     *
     * @return array
     */
    public function updateSettings($user, $scope = null, $key = null, $value = null)
    {
        if (empty($user['id'] ?? false)) {
            return $user;
        }

        if (empty($scope)) {
            return $user;
        }

        // Update activity field and settings
        $now = FrozenTime::now();
        $settings = $user['settings'] ?? [];

        if (!is_null($key)) {
            $settings[$scope][$key] = $value;
        }
        else {
            $settings = array_replace_recursive($settings, [$scope => $value]);
        }

        $this->updateQuery()
            ->update()
            ->set(['lastaction' => $now, 'settings' => $settings])
            ->where(['id' => $user['id']])
            ->execute();

        // Update session token
        if (!empty($user['accesstoken'])) {
            $token = $this->fetchTable('Epi.Token');
            $token->updateSessionToken($user['accesstoken']);
        }

        // Get new user object
        $user = $this->get($user['id'], ['finder' => 'auth'])->toArray();

        return $user;
    }

    /**
     * Get currently active users
     *
     * @return array
     */
    public function sqlConnectedUsers()
    {
        $conn = $this->getConnection();
        $processes = $conn->execute('SHOW PROCESSLIST;')->fetchAll('assoc');
        $users = array_count_values(Hash::extract($processes, '{n}.User'));
        return $users;
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
            'order' => ['username' => 'ASC'],
            'sortableFields' => $this->getSortableFields($columns),
            'limit' => 1000,
            'maxLimit' => 1000
        ];
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
            //To prevent autofill, use uname instead of username and map it in parseRequestParameters()
            'uname' => [
                'key' => 'username',
                'field' => 'username',
                'caption' => __('User name'),
                'sort' => true,
                'filter' => 'text',
                'default' => true
            ],

            'role' => [
                'caption' => __('Role'),
                'type' => 'select',
                'empty' => true,
                'default' => true,
                'sort' => true,
                'options' => PermissionsTable::$userRoles,
                'filter' => 'select'
            ],

            'contact' => [
                'caption' => __('Contact'),
                'filter' => false,
                'sort' => true,
                'default' => true
            ],

            'email' => [
                'caption' => __('Email'),
                'filter' => false,
                'sort' => true,
                'default' => true
            ],

            'databank' => [
                'caption' => __('Database'),
                'key' => 'databank.caption',
                'type' => 'text',
                'empty' => true,
                'default' => true,
                'sort' => 'databank.name',
                'filter' => false
            ],

            'pipeline_article' => [
                'caption' => __('Article pipeline'),
                'key' => 'article_pipeline.name',
                'type' => 'text',
                'empty' => true,
                'default' => true,
                'sort' => true,
                'action' => ['index'],
                'filter' => false
            ],

            'pipeline_book' => [
                'caption' => __('Book pipeline'),
                'key' => 'book_pipeline.name',
                'type' => 'text',
                'empty' => true,
                'default' => true,
                'sort' => true,
                'action' => ['index'],
                'filter' => false
            ],

            'active' => [
                'caption' => __('Active'),
                'align' => 'center',
                'type' => 'badge',
                'badge' => ['', '⬤'],
                'sort' => true,
                'default' => true,
                'width' => 100,
            ],

            'lastaction' => [
                'caption' => __('Last action'),
                'sort' => true,
                'default' => true
            ],

            'sqlconnections' => [
                'caption' => __('SQL connections'),
                'default' => true
            ],

            'norm_iri' => [
                'caption' => __('IRI'),
                'sort' => true,
                'default' => true,
                'filter' => false
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

}
