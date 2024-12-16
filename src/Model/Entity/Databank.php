<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity;

use App\Model\Table\BaseTable;
use App\Model\Table\DatabanksTable;
use App\Model\Table\UsersTable;
use App\Cache\Cache;
use App\Utilities\Converters\Objects;
use App\Utilities\Files\Files;
use Cake\Chronos\Chronos;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Epi\Model\Table\BaseTable as EpiBaseTable;
use Cake\Database\Exception\MissingConnectionException;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Hash;
use Epi\Model\Table\MetaTable;
use Exception;
use PDOException;

/**
 * Databank Entity
 *
 * # Database fields (without inherited fields)
 * @property string $name
 * @property string $version
 * @property string $category
 * @property string $description
 * @property string $iriprefix
 *
 * # Virtual fields (without inherited fields)
 * @property string|null $plugin
 *
 * @property bool $available
 * @property bool $isempty
 * @property bool $versionok
 * @property bool $isready
 * @property array $status
 *
 * @property int|null $projectsCount
 * @property int|null $articlesCount
 * @property int|null $propertiesCount
 * @property int|null $typesCount
 * @property int|null $filesCount
 * @property int|null $usersCount
 * @property int|null $notesCount
 *
 * @property string $route
 * @property array $backups
 *
 * # Relations
 * @property false|array $grants
 * @property array $types Array of types, grouped by scope and name.
 *
 * # Relations
 * @property DatabanksTable $table
 */
class Databank extends BaseEntity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity()
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * @var string[]
     */
    public $_virtual = ['plugin'];

    /**
     * Current database version
     *
     * @var string[]
     */
    public $plugins = [DATABASE_CURRENT_VERSION => 'Epi'];

    /**
     * Is database connected?
     *
     * @var bool
     */
    protected $_available = false;

    /**
     * Is database version valid?
     *
     * @var bool
     */
    protected $_versionok = false;

    /**
     * Is database empty?
     *
     * @var bool
     */
    protected $_isempty = true;

    /**
     * Content of the types table, grouped by scope and name
     *
     * @var array
     */
    protected $_types = [];

    /**
     * Add the database prefix
     *
     * Depending on test or production mode,
     * the prefix 'test_' or 'epi_' is added if not present.
     *
     * @param string|boolean $name If false, returns false. Otherwise the name with the prefix.
     * @return mixed|string
     */
    public static function addPrefix($name)
    {
        $db_prefix = Configure::read('test') ? 'test_' : 'epi_';
        if ($name && !str_starts_with($name, $db_prefix)) {
            $name = $db_prefix . $name;
        }
        return $name;
    }

    /**
     * Add the database prefix
     *
     * Depending on test or production mode,
     * the prefix 'test_' or 'epi_' is added if not present.
     *
     * @param false|string $name If false, returns false. Otherwise the name without prefix.
     * @return false|string
     */
    public static function removePrefix($name)
    {
        $db_prefix = Configure::read('test') ? 'test_' : 'epi_';
        if ($name && str_starts_with($name, $db_prefix)) {
            $name = substr($name, strlen($db_prefix));
        }

        return $name;
    }


    /**
     * Get plugin version
     *
     * Magic fields
     *
     * @return string|null
     */
    protected function _getPlugin()
    {
        return $this->plugins[$this->version] ?? null;
    }

    /**
     * Check database connectivity
     *
     * Only checked once and then saved in $this->>available.
     * Changes the database for subsequent queries.
     *
     * @return bool
     */
    protected function _getAvailable()
    {
        if (!empty($this->_available)) {
            return $this->_available;
        }

        $old = EpiBaseTable::getDatabaseName();
        $this->_available = false;
        try {
            $db = EpiBaseTable::setDatabase($this->name);
            $dbDriver = $db->getDriver();
            $dbDriver->connect();
            $this->_available = $dbDriver->isConnected();

        } catch (MissingConnectionException $e) {
            $this->_available = false;
        } finally {
            EpiBaseTable::setDatabase($old);
        }

        return ($this->_available);
    }

    /**
     * Check whether the database is empty
     *
     * Only checked once and then saved in $this->_isempty.
     *
     * @return bool
     */
    protected function _getIsempty()
    {
        $old = EpiBaseTable::getDatabaseName();
        try {
            EpiBaseTable::setDatabase($this->name);
            $tables = $this->getStructure();
            $this->_isempty = empty($tables);
        } finally {
            EpiBaseTable::setDatabase($old);
            TableRegistry::getTableLocator()->clear();
        }

        return ($this->_isempty);
    }

    /**
     * Check database version
     *
     * Changes the database for subsequent queries
     * TODO: don't change the active database, use activateDatabase() instead
     *
     * @return bool
     */
    protected function _getVersionok()
    {
        return $this->isready;
    }

    /**
     * Check whether the database is ready
     *
     * Can we connect to the database and
     * does it have a correct version record in the meta table?
     * Only checked once and then saved in $this->_versionok
     *
     * Changes the database for subsequent queries
     *
     * TODO: don't change the active database, use activateDatabase() instead
     *
     * @return bool
     */
    protected function _getIsready()
    {
        if (empty($this->plugin)) {
            return false;
        }

        if (!empty($this->_versionok)) {
            return $this->_versionok;
        }

        $this->_versionok = false;

        //Check connection
        if (!$this->available) {
            return false;
        }

        //Check if meta table exists
        $old = EpiBaseTable::getDatabaseName();
        try {
            $db = EpiBaseTable::setDatabase($this->name);
            //TableRegistry::getTableLocator()->clear();
            //Cache::clear('_cake_model_');

            // Has meta table?
            $collection = $db->getSchemaCollection();
            $tables = $collection->listTables();
            if (!in_array('meta', $tables)) {
                return false;
            }

            //Compare version
            $meta_table = $this->fetchTable($this->plugin . '.Meta');
            if (!$meta_table->hasField('name')) {
                return false;
            }

            if (in_array($this->version, [DATABASE_CURRENT_VERSION])) {
                if (!$meta_table->hasField('value')) {
                    return false;
                }

                /** @var MetaTable $meta */
                $meta = $meta_table
                    ->find()
                    ->select('value')
                    ->where(['name' => 'db_version'])
                    ->first();

                $this->_versionok = (!empty($meta) && ($meta->value === $this->version));
            }
            else {
                $this->_versionok = false;
            }
        } finally {
            EpiBaseTable::setDatabase($old);
        }

        return $this->_versionok;
    }

    /**
     * Get the database name without prefix
     *
     * @return string
     */
    protected function _getCaption()
    {
        return self::removePrefix($this->name);
    }

    /**
     * Get database status
     *
     * @return array
     */
    protected function _getStatus()
    {
        $ready = false;

        if (!$this->available) {
            $msg = __('The database is not available.');
        }

        elseif ($this->isempty) {
            $msg = __('The database is empty.');
        }

        elseif (!$this->versionok) {
            $msg = __('The version of the database is not supported.');
        }

        else {
            $msg = __('The database is ready to use.');
            $ready = true;
        }

        return ['msg' => $msg, 'ready' => $ready];
    }

    /**
     * Get number of records in a taböe
     *
     * Query data from the associated project database.
     *
     * @param $modelname
     *
     * @return int|null
     */
    public function getTableCount($modelname)
    {
        if (!$this->activateDatabase()) {
            return null;
        }

        try {
            $table = $this->fetchTable($this->plugin . '.' . $modelname);
            return $table->find()->count();
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * Get number of projects
     *
     * @return int|null
     */
    protected function _getProjectsCount()
    {
        return $this->getTableCount('Projects');
    }

    /**
     * Get number of articles
     *
     * @return int|null
     */
    protected function _getArticlesCount()
    {
        return $this->getTableCount('Articles');
    }

    /**
     * Get number of properties
     *
     * @return int|null
     */
    protected function _getPropertiesCount()
    {
        return $this->getTableCount('Properties');
    }

    /**
     * Get number of types
     *
     * @return int|null
     */
    protected function _getTypesCount()
    {
        return $this->getTableCount('Types');
    }

    /**
     * Get number of files
     *
     * @return int|null
     */
    protected function _getFilesCount()
    {
        return $this->getTableCount('Files');
    }

    /**
     * Get number of files
     *
     * @return int|null
     */
    protected function _getUsersCount()
    {
        return $this->getTableCount('Users');
    }

    /**
     * Get number of notes
     *
     * @return int|null
     */
    protected function _getNotesCount()
    {
        return $this->getTableCount('Notes');
    }

    /**
     * Activate database
     *
     * @return bool
     */
    public function activateDatabase()
    {
        if (EpiBaseTable::getDatabaseName() === $this->name) {
            return true;
        }
        elseif (!$this->isready) {
            return false;
        }
        else {
            EpiBaseTable::setDatabase($this->name);
            TableRegistry::getTableLocator()->clear();
            return true;
        }
    }


    /**
     * Get table list
     *
     * @return array
     */
    public function getStructure()
    {
        if (!$this->available) {
            return [];
        }

        try {
            $db = EpiBaseTable::setDatabase($this->name);

            $collection = $db->getSchemaCollection();
            $tables = $collection->listTables();

            return $tables;

        } catch (MissingConnectionException $e) {
            return [];
        }
    }

    /**
     * Get plugin route for different database versions
     *
     * @return string
     */
    protected function _getRoute()
    {
        return $this->plugins[$this->version] == 'Epi' ? 'epi' : 'epi';
    }

    /**
     * Check whether a username exists
     *
     * @param $username
     *
     * @return bool
     */
    public function userExists($username)
    {
        if (!$this->available) {
            return false;
        }

        $oldDatabase = EpiBaseTable::getDatabaseName();
        $db = EpiBaseTable::setDatabase($this->name);
        $user = $db->execute('SELECT * from mysql.user WHERE user="' . $username . '"')->fetchAll('assoc');
        EpiBaseTable::setDatabase($oldDatabase);

        return (!empty($user));
    }

    /**
     * Create user
     *
     * @param $username
     * @param $password
     *
     * @return false|mixed|string|null
     */
    public function createUser($username, $password = null)
    {
        if (!$this->available) {
            return false;
        }

        if ($this->userExists($username)) {
            return false;
        }

        if (empty($password)) {
            $password = UsersTable::generateAccesstoken(10);
        }

        $oldDatabase = EpiBaseTable::getDatabaseName();
        $db = EpiBaseTable::setDatabase($this->name);
        $result = $db->execute('CREATE USER "' . $username . '" IDENTIFIED BY "' . $password . '"');
        EpiBaseTable::setDatabase($oldDatabase);

        return $result ? $password : false;
    }

    /**
     * Delete user
     *
     * @param $username
     *
     * @return false
     */
    public function deleteUser($username)
    {
        if (!$this->available) {
            return false;
        }
        if (!$this->userExists($username)) {
            return false;
        }

        $db = EpiBaseTable::setDatabase($this->name);
        return $db->execute('DROP USER "' . $username . '"');
    }

    /**
     * Set user password
     *
     * @param $username
     * @param $password
     *
     * @return false|mixed|string|null
     */
    public function setPassword($username, $password = null)
    {
        if (!$this->available) {
            return false;
        }

        if (!$this->userExists($username)) {
            return $this->createUser($username, $password);
        }

        if (empty($password)) {
            $password = UsersTable::generateAccesstoken(10);
        }

        $db = EpiBaseTable::setDatabase($this->name);
        $result = $db->execute('SET PASSWORD FOR "' . $username . '" = PASSWORD("' . $password . '")');
        return $result ? $password : false;
    }

    /**
     * Grant SQL database access
     *
     * @param $username
     *
     * @return false
     */
    public function grantDesktopAccess($username)
    {
        if (!$this->available) {
            return false;
        }

        if (!$this->userExists($username)) {
            $this->createUser($username);
        }

        $db = EpiBaseTable::setDatabase($this->name);
        return $db->execute('GRANT SELECT,INSERT,UPDATE,DELETE,LOCK TABLES ON ' . $this->name . '.* TO "' . $username . '" REQUIRE SSL');
        //return $db->execute('GRANT SELECT,INSERT,UPDATE,DELETE,LOCK TABLES ON '.$this->name.'.* TO "'.$username.'"');
    }

    /**
     * Revoke SQL database access
     *
     * @param string $username A SQL username, usually prefixed with 'epi_'
     * @return boolean Whether the access was revoked or did not exist, which is fine.
     */
    public function revokeDesktopAccess($username)
    {
        if (!$this->available) {
            return false;
        }

        if (!$this->userExists($username)) {
            return false;
        }

        $db = EpiBaseTable::setDatabase($this->name);
        try {
            return $db->execute('REVOKE SELECT,INSERT,UPDATE,DELETE,LOCK TABLES ON ' . $this->name . '.* FROM "' . $username . '"');
        } catch (PDOException $e) {
            // Catch SQLSTATE[42000]: Syntax error or access violation: 1141 There is no such grant defined for user 'x' on host '%'
            // which indicates the grant does not exist, that's fine.
            return (strpos($e->getMessage(), 'SQLSTATE[42000]') !== false);
        }
    }

    /**
     * Add permission to access the database view the web frontend
     *
     * @param integer $user_id The user ID
     * @param string $user_role The user role.
     * @return mixed
     */
    public function grantWebAccess($user_id, $user_role = null)
    {
        $permissionTable = $this->fetchTable('Permissions');
        $permission = [
            'user_id' => $user_id,
            'user_role' => $user_role,
            'user_request' => 'web',
            'entity_type' => 'databank',
            'entity_name' => $this->name,
            'entity_id' => $this->id,
            'permission_type' => 'access'
        ];
        return ($permissionTable->addPermission($permission));
    }

    /**
     * Remove all web permissions for a database
     *
     * TODO: handle $user_role
     *
     * @param int $userId The user ID
     * @param string $userRole Either null to revoke all access or a valid user role
     * @return int Number of affected permission rows
     */
    public function revokeWebAccess($userId, $userRole = null)
    {
        $permissionTable = $this->fetchTable('Permissions');
        $conditions = [
            'entity_type' => 'databank',
            'user_id' => $userId,
            [
                'OR' => [
                    ['user_request' => 'web'],
                    ['user_request IS' => null],
                ]
            ],
            [
                'OR' => [
                    'entity_name' => $this->name,
                    'entity_id' => $this->id
                ]
            ]
        ];
        if (!empty($userRole)) {
            $conditions['user_role'] = $userRole;
        }

        return $permissionTable->removePermission($conditions);
    }

    /**
     * Add permission to access the database via the API
     *
     * @param integer $user_id The user ID
     * @param string $user_role The user role.
     * @return mixed
     */
    public function grantApiAccess($user_id, $user_role = null)
    {
        $permissionTable = $this->fetchTable('Permissions');
        $permission = [
            'user_id' => $user_id,
            'user_role' => $user_role,
            'user_request' => 'api',
            'entity_type' => 'databank',
            'entity_name' => $this->name,
            'entity_id' => $this->id,
            'permission_type' => 'access'
        ];
        return ($permissionTable->addPermission($permission));
    }

    /**
     * Remove all API permissions for a database
     *
     * @param int $userId The user ID
     * @param string $userRole Either null to revoke all access or a valid user role
     * @return int Number of affected permission rows
     */
    public function revokeApiAccess($userId, $userRole = null)
    {
        $permissionTable = $this->fetchTable('Permissions');
        $conditions = [
            'user_id' => $userId,
            'entity_type' => 'databank',
            [
                'OR' => [
                    ['user_request' => 'api'],
                    ['user_request IS' => null],
                ]
            ],
            [
                'OR' => [
                    'entity_name' => $this->name,
                    'entity_id' => $this->id
                ]
            ]
        ];
        if (!empty($userRole)) {
            $conditions['user_role'] = $userRole;
        }

        return $permissionTable->removePermission($conditions);
    }


    /**
     * Get list of usernames of users with grants on the database
     *
     * @return false|array
     */
    protected function _getGrants()
    {
        if (!$this->available) {
            return false;
        }

        $db = EpiBaseTable::setDatabase($this->name);
        $grants = $db->execute('SELECT * FROM mysql.db WHERE Db = "' . $this->name . '"')->fetchAll('num');
        $userNames = Objects::extract($grants, '*.2');
        return $userNames;
    }

    /**
     * Initialize database
     *
     * If the preset property is set, the sql dump from the preset folder will be imported.
     * Otherwise, an empty database is created from the schema file matching the version property.
     *
     * @return bool
     */
    public function initDatabase()
    {
        if (!$this->isempty) {
            return false;
        }

        $preset = $this->preset ?? '';
        if (!empty($preset)) {
            $presetFolder = Plugin::path('Epi') . 'config' . DS . 'presets' . DS;
            $initsql = $presetFolder . $preset . DS . $preset . '.sql.gz';

            $presetData = $this->getPresetData();
            if (!empty(($presetData))) {
                $this->category = $presetData['category'] ?? $this->category;
                $this->description = $presetData['description'] ?? $this->description;
                $this->table->save($this);
            }

        }
        else {
            $version = str_replace('.', '', $this->version);
            $initsql = Plugin::path($this->plugin) . 'config' . DS . 'schema' . DS . 'dbempty_' . $version . '.sql';
        }

        return $this->import($initsql);
    }

    /**
     * Load preset settings from the preset json file
     *
     * Set the preset property to the preset name to load the settings.
     *
     * @return array
     */
    public function getPresetData()
    {
        $presetName = $this->preset ?? '';
        $presetData = [];

        if (!empty($presetName)) {
            $presetFile = Plugin::path('Epi') . 'config' . DS . 'presets' . DS . $presetName . DS . $presetName . '.json';
            if (file_exists($presetFile)) {
                try {
                    $jsonContent = file_get_contents($presetFile);
                    $presetData = json_decode($jsonContent, true);
                } catch (Exception $e) {
                    // Ignore
                }
            }
        }

        return $presetData;
    }

    /**
     * Create database
     *
     * @return bool
     */
    public function createDatabase()
    {
        try {
            $conn = EpiBaseTable::setDatabase(false);
            return $conn->execute('CREATE DATABASE IF NOT EXISTS ' . $this->name . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Drop database
     *
     * @return bool
     */
    public function dropDatabase()
    {
        try {
            $conn = EpiBaseTable::setDatabase(false);
            return $conn->execute('DROP DATABASE ' . $this->name);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create folders and copy preset files
     *
     * Preset files will be copied if the preset property is set.
     *
     * @return array
     */
    public function createFolders()
    {
        $root = Configure::read('Data.databases') . $this->name . DS;
        $preset = $this->preset ?? '';
        $presetFolder = Plugin::path('Epi') . 'config' . DS . 'presets' . DS . $preset . DS;

        $created = 0;
        $missing = 0;

        if (!is_dir($root)) {
            $created += mkdir($root, 0777, true);
            $missing += 1;
        }

        foreach (['articles', 'properties', 'notes', 'backup'] as $folder) {
            if (!is_dir($root . $folder . DS)) {
                $created += mkdir($root . $folder . DS, 0777, true);
                $missing += 1;
            }

            if (!empty($preset) && is_dir($presetFolder . DS . $folder)) {
                Files::copyFiles($folder, null, $presetFolder, $root . $folder . DS);
            }

        }


        return ['missing' => $missing, 'created' => $created];
    }

    /**
     * Import an sql dump
     *
     * @param $filename
     *
     * @return bool
     */
    public function import($filename)
    {
        return EpiBaseTable::loadSql($filename, $this->name, 'projects');
    }

    /**
     * Backup database
     *
     * @return bool
     */
    public function backupDatabase()
    {
        //Check folders
        $this->createFolders();
        $path = Configure::read('Data.databases') . $this->name . DS . 'backup' . DS;
        if (!is_dir($path)) {
            throw new NotFoundException('The backup path does not exist. Please check your configuration.');
        }

        //Check exec function
        if (!function_exists('exec')) {
            throw new NotFoundException('The exec function is disabled. Please check your PHP configuration.');
        }

        //Database connection
        $db = EpiBaseTable::setDatabase($this->name);
        $dsc = $db->config();
        $database = '--user=' . $dsc['username'] . ' --password=' . $dsc['password'] . ' --host=' . $dsc['host'] . ' ' . Databank::addPrefix($dsc['database']);

        //Backup
        $mysqldump = 'mysqldump';

        $time = Chronos::now();
        $timestring = $time->format("Y-m-d_h_i_s_") . $time->getTimestamp();

        $filename = 'backup_' . $this->name . '_' . $timestring;
        $output = '> "' . $path . $filename . '.sql"';
        $errors = '2> "' . $path . $filename . '.err"';
        $where = '';
        $options = '--skip-ssl --extended-insert --net-buffer-length=100000';
        $tablename = '';

        $command = implode(' ', array($mysqldump, $where, $options, $database, $tablename, $errors, $output));
        exec($command, $out, $status);
        exec("gzip " . $path . $filename . '.sql');

        //delete files
        //if (file_exists($path.$filename.'.sql.gz'))
        //  unlink($path.$filename.'.sql');
        if (filesize($path . $filename . '.err') == 0) {
            unlink($path . $filename . '.err');
        }

        return empty($status);
    }

    /**
     * Get backup files
     *
     * @return array
     */
    protected function _getBackups()
    {
        $path = Configure::read('Data.databases') . $this->name . DS . 'backup' . DS;
        $files = glob($path . "*.gz");
        $files = array_map('basename', $files);

        return $files;
    }

    /**
     * Return fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        $baseUrl = '/epi/' . $this->name . '/files/download?path=backup&filename=';

        $fields = [
            'name' => [
                'caption' => __('Name'),
                'type' => 'choose',
                'itemtype' => 'databank',
                'options' => ['controller' => 'Databanks', 'action' => 'select'],
                'action' => ['add', 'edit', 'create']
            ],
            'caption' => [
                'caption' => __('Name'),
                'action' => ['view']
            ],
            'category' => [
                'caption' => __('Category')
            ],
            'description' => [
                'caption' => __('Description')
            ],

            'version' => [
                'caption' => __('Version')
            ],

            'published' => [
                'caption' => __('Progress'),
                'type' => 'select',
                'options' => $this->publishedOptions,
                'action' => ['edit', 'view']
            ],

            'iriprefix' => [
                'caption' => __('IRI Prefix')
            ],

            'status' => [
                'caption' => __('Status'),
                'action' => 'view',
                'extract' => 'status.msg'
            ],

            'users' => [
                'caption' => __('Associated Users'),
                'action' => 'view',
                'extract' => 'users.{n}.username'
            ],

            'grants' => [
                'caption' => __('Granted SQL-users'),
                'action' => 'view'
            ],

            'permissions' => [
                'caption' => __('Permissions'),
                'action' => 'view',
                'extract' => 'permissions.{n}.username'
            ],

            'backups' => [
                'caption' => __('Backup files'),
                'action' => 'view',
                'format' => 'url',
                'baseUrl' => $baseUrl
            ],
        ];

        return $fields;
    }

    /**
     * Get the types, grouped by scope and name
     *
     * @return array
     * @see Databank::$types
     */
    protected function _getTypes()
    {
        if (empty($this->_types)) {

            if (!$this->activateDatabase()) {
                $this->_types = [];
            }

            try {
                $table = $this->fetchTable($this->plugin . '.Types');
                $cacheConfig = $table->initResultCache();
                $cacheKey = (BaseTable::$userId ?? 'public') . '_' . (BaseTable::$requestMode ?? 'default') . '_grouped';

                $this->_types = Cache::remember(
                    $cacheKey,
                    function () use ($table) {
                        return $this->_types = $table
                            ->find('all')
                            ->where(['mode' => 'default', 'preset' => 'default'])
                            ->contain('Subtypes')
                            ->select([
                                'scope',
                                'sortno',
                                'category',
                                'name',
                                'caption',
                                'config',
                                'norm_iri',
                                'published'
                            ])
                            ->order(['scope', 'sortno', 'category', 'name'])
                            ->limit(TYPES_LIMIT)
                            ->all()
                            ->groupBy('scope')
                            ->map(function ($group, $key) {
                                $group = collection($group)->indexBy('name');
                                return Hash::sort($group->toArray(), '{*}.sortno');
                            })
                            ->toArray();
                    },
                    $cacheConfig
                );

            } catch (Exception $ex) {
                $this->_types = [];
            }
        }

        return $this->_types;
    }

    /**
     * Get a grouped name-value list of types to be used in filter widgets
     *
     * @param string $scope E.g. 'properties'
     * @param string $keyField E.g. 'name'
     * @param string $valueField E.g. 'caption'
     * @param string $groupField E.g. 'category'
     * @return array
     */
    public function getGroupedTypes($scope, $keyField, $valueField, $groupField)
    {
        $userRole = $this->currentUserRole ?? 'guest';
        $showAll = ($userRole !== 'guest');

        $groups = [];
        foreach ($this->types[$scope] ?? [] as $type) {
            $role = $type->merged['export']['role'] ?? '';
            if (!$showAll && (in_array($role, ['index', 'search']))) {
                continue;
            }
            $groups[$type[$groupField] ?? ''][$type[$keyField] ?? ''] = $type[$valueField] ?? '';
        }
        return $groups;
    }
}
