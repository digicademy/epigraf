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

use App\Model\Behavior\ModifierBehavior;
use App\Model\Behavior\VersionBehavior;
use App\Model\Entity\Databank;
use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use App\Utilities\Converters\Objects;
use ArrayObject;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\FactoryLocator;
use Cake\Event\EventInterface;
use Cake\ORM\Association;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Datasource\ConnectionManager;
use App\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use PDOException;

/**
 * Base table
 *
 * # Behaviors
 * @mixin ModifierBehavior
 * @mixin VersionBehavior
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class BaseTable extends Table
{

    use LocatorAwareTrait;

    /**
     * The field containing a default caption
     *
     * @var string
     */
    public $captionField = 'id';

    /**
     * Scope field: For trees the scope of the tree, else the segment of the table or null for non-scoped tables
     *
     * @var null|string
     */
    public $scopeField = null;

    /**
     * Whether to check field types after marshalling and merge JSON data
     *
     * @var bool
     */
    public $mergeJson = false;

    /**
     * Default database
     *
     * @var string
     */
    public static $defaultConnection = 'default';

    /**
     * Current cache status
     *
     * @var bool
     */
    public static $cacheMetadata = true;


    /**
     * Current request scope (api or web)
     *
     * * TODO: pack scope, mode and format into an array
     *
     * @var null|string
     */
    public static $requestScope = null;

    /**
     * Current request mode (default, code, present)
     *
     * @var null|string
     */
    public static $requestMode = null;

    /**
     * Current request action (add, edit, view, index...)
     *
     * @var null|string
     */
    public static $requestAction = null;

    /**
     * Current request target (the URI path)
     *
     * USed for deriving cache keas
     *
     * @var null|string
     */
    public static $requestTarget = null;

    /**
     * Current request format
     *
     * @var string csv, json, xml, html
     */
    public static $requestFormat = null;

    /**
     * Current request preset
     *
     * @var string A custom name to lookup the merged types configuration
     */
    public static $requestPreset = null;

    /**
     * A list of publication status values visible entities must match
     *
     * @var integer[]
     */
    public static $requestPublished = null;

    /**
     * Current user data: id, iri, role, name, acronym
     *
     * @var null|array
     */
    public static $user = null;

    /**
     * Current user role
     *
     * TODO: pack role, id and iri into an array
     *
     * @var null|string
     */
    public static $userRole = null;

    /**
     * Current user ID
     *
     * @var null|int
     */
    public static $userId = null;

    /**
     * Current user IRI
     *
     * @var null|int
     */
    public static $userIri = null;

    /**
     * Current user settings
     *
     * @var array
     */
    public static $userSettings = [];


    /**
     * Current cache name
     *
     * @var null
     */
    protected $_cacheConfigName = null;

    protected $_pluginName = '';

    /**
     * Current database
     *
     * @var Databank
     */
    protected $_database = null;

    /**
     * Request parameter config
     *
     * @var string[]
     */
    public $parameters = [];

    /**
     * Search field configuration
     *
     * An array of field bundles. Each bundle has a caption, a list of searched fields, and
     * optionally the operator options 'type' and 'operator'.
     *
     * The searched fields are provided in the scopes key, either as a list of fields (e.g. Articles.signature)
     * or as a keyed array where the field name is the key and the values contain operator options.
     *
     * Operator options contain the operator ('=' or 'LIKE', default is 'LIKE') and, optionally,
     * a type. For integer fields, set type to 'integer'.
     *
     * Type configuration takes precedence, key: 'fields' > 'searchable'.
     *
     * @var array
     */
    public $searchFields = [];

    /**
     * @var array Options for patching entities in the edit action
     */
    public $patchOptions = [];

    /**
     * Initialize hook
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('Modifier', ['user' => 'userId']);
        $this->addBehavior('Version');
    }

    /**
     * Default validation rules
     *
     * @param Validator $validator Validator instance
     *
     * @return Validator
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
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        return $validator;
    }

    /**
     * Merge formatted field data
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $data
     * @param ArrayObject $options
     */
    public function afterMarshal(
        EventInterface $event,
        EntityInterface $entity,
        ArrayObject $data,
        ArrayObject $options
    ) {

        // Merge JSON arrays
        if (!empty($this->mergeJson)) {
            foreach (($entity->type->merged['fields'] ?? []) as $field => $config) {
                if (in_array($config['format'] ?? '', ['json', 'geodata'])) {
                    $entity->mergeJson($field, $data, false);
                }
            }
        }
    }

    /**
     * Clear the caches after saving
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param $options
     *
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, $options = [])
    {
        $this->clearCache();
    }

    /**
     * Check whether a field is virtual or in the database
     *
     * @param string $fieldName
     * @return bool
     */
    public function hasDatabaseField($fieldName)
    {
        if ($fieldName === false) {
            return false;
        }
        return $this->getSchema()->hasColumn($fieldName);
    }

    /**
     * Changes the database in the projects configuration
     *
     * All following queries are made on this database.
     *
     * @param string $name Database name
     * @param string $conn Connection config name
     * @param bool $prefix Whether to automatically add the prefix 'epi_' or 'test_' to the database name
     * @return \Cake\Datasource\ConnectionInterface
     */
    public static function setDatabase($name, $conn = 'projects', $prefix = true)
    {
        // Add prefix
        if ($prefix) {
            $name = Databank::addPrefix($name);
        }

        // Configure connection
        $old = ConnectionManager::get($conn);
        $old->getDriver()->disconnect();

        $config = $old->config();
        $conn_aliased = $config['name'];

        $config['className'] = get_class($old);
        $config['database'] = $name;

        //Configure cache
        if (BaseTable::$cacheMetadata) {
            $cachekey = 'epi_model_' . $conn . '_' . $name;
            Cache::initCache($cachekey, '_cake_model_');
            $config['cacheMetadata'] = $cachekey;
        }
        else {
            $config['cacheMetadata'] = false;
        }

        // Get connection alias name (necessary for tests)
        ConnectionManager::drop($conn_aliased);
        ConnectionManager::setConfig($conn_aliased, $config);

        TableRegistry::getTableLocator()->clear();

        return ConnectionManager::get($conn)
            ->enableQueryLogging($old->isQueryLoggingEnabled())
            ->setLogger($old->getLogger());

        // Alternative approach, not working: Alias the active account's shard to our 'default' connection.
        //ConnectionManager::alias('shard' . $mapping->shard_id, 'projects');
    }

    /**
     * Load a SQL dump
     *
     * The dump has to be a text file containing SQL statements.
     * Make sure not to include USE database statements.
     *
     * @param string $filename The filename.
     * @param string $dbname The database name
     * @param string $conname projects or default
     * @return bool
     */
    public static function loadSql($filename, $dbname, $conname = 'projects')
    {
        //Check folders
        if (!file_exists($filename)) {
            throw new \Cake\Core\Exception\CakeException('The file could not be found.');
        }

        //Unzip file if necessary
        // $zip = new \ZipArchive() ;
        // $zipstatus = $zip->open($filename);

        // if ($zipstatus === TRUE) {
        // //$destination = TMP.basename($filename).'.sql';
        // $destination = $filename.'.sql';
        // if (!$zip->extractTo($destination))
        // throw new BadRequestException('The file could not be unzipped.');
        // $zip->close();
        // $filename = $destination;
        // } else
        // throw new BadRequestException('The file could not be unzipped: '.$zipstatus);

        try {
            $conn = BaseTable::setDatabase($dbname, $conname);
            $conn->getDriver()->connect();

            //$lines = file($filename);
            $filehandle = gzopen($filename, 'rb');
            try {
                $query = '';

                // Loop through each line
                while (!gzeof($filehandle)) //foreach ($lines as $line)
                {
                    $line = gzgets($filehandle);
                    // Skip it if it's a comment
                    if (substr($line, 0, 2) == '--' || $line == '') {
                        continue;
                    }

                    // Add this line to the current segment
                    $query .= $line;

                    // If it has a semicolon at the end, it's the end of the query,
                    // perform the query. Skip use database statements.
                    if (substr(trim($line), -1, 1) == ';') {

                        if (
                            (substr(strtoupper(trim($line)), 0, 4) !== 'USE ') &&
                            (substr(strtoupper(trim($line)), 0, 16) !== 'CREATE DATABASE ')
                        ) {
                            $status = $conn->execute($query);
                        }

                        if (!$status) {
                            return false;
                        }
                        $query = '';
                    }

                }
            } finally {
                gzclose($filehandle);
            }

        } catch (PDOException $e) {
            return false;
        }

        return true;
    }

    /**
     * Get the iri of the active project database
     *
     * Removes the epi_ prefix from the database name.
     *
     * @param $conn
     *
     * @return array|string
     */
    public static function getDatabaseIri($conn = 'projects')
    {
        $db = ConnectionManager::get($conn);
        $dbName = $db->config()['database'];

        return Databank::removePrefix($dbName);
    }

    /**
     * Get the name of the active project database, including the prefix
     *
     * @param $conn
     *
     * @return mixed
     */
    public static function getDatabaseName($conn = 'projects')
    {
        $db = ConnectionManager::get($conn);
        return $db->config()['database'];
    }


    /**
     * Get the model name from the table name
     *
     * @param string $tableName
     * @param string|boolean $plugin
     *
     * @return string
     */
    public function getModelName($tableName, $plugin = false)
    {
        $modelName = Inflector::camelize($tableName);
        if ($plugin !== false) {
            $modelName = $plugin . '.' . $modelName;
        }
        return $modelName;
    }

    /**
     * Get the model table from the table name
     *
     * @param string $tableName
     * @param string $plugin
     *
     * @return Table
     */
    public function getModel($tableName, $plugin)
    {
        $modelName = $this->getModelName($tableName, $plugin);
        /** @var Table $model */
        $model = FactoryLocator::get('Table')->get($modelName);
        return $model;
    }

    public function getEntityName()
    {
        return Inflector::singularize($this->getTable());
    }

    public function getDefaultScope()
    {
        return '';
    }

    /**
     * Initialize the cache config and return its name.
     *
     * @param string $conn The database connection name, default is 'projects'.
     * @return string Name of the cache configuration
     */
    public function initResultCache($conn = 'projects')
    {
        if (empty($this->_cacheConfigName)) {
            $configName = 'epi_results_' . Databank::addPrefix($this->getDatabaseName($conn)) . '_' . $this->_table;
            $this->_cacheConfigName = $configName;
            Cache::initCache($configName, 'results');
        }

        if (Configure::read('debug')) {
            Cache::clear($this->_cacheConfigName);
        }

        return $this->_cacheConfigName;
    }

    /**
     * Clear cache
     *
     * @param string $conn
     * @return void
     */
    public function clearCache($conn = 'projects')
    {
        // Result cache
        $cacheConfig = $this->initResultCache($conn);
        Cache::clear($cacheConfig);

        // View cache
        // @var string $configName The configuration name used in the controller ($this->plugin . '_' . $this->name);
        $configName = 'epi_views_Epi_Articles';
        Cache::initCache($configName, 'views');
        Cache::clear($configName);
    }

    /**
     * Cache results
     *
     * The cache key is derived from the request target if not explicitly provided.
     *
     * ### Options
     * - cachekey: Optional, a custom cache key
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findCached(Query $query, array $options)
    {
        $cacheKey = $options['cachekey'] ?? md5($this::$requestTarget);
        $query = $query->cache($cacheKey, $this->initResultCache());
        return $query;
    }

    /**
     * Get database of the current table
     *
     * @return Databank
     */
    public function getDatabase()
    {
        if (empty($this->_database)) {
            $table = $this->fetchTable('Databanks');
            $this->_database = $table->findByName(Databank::addPrefix($this->getDatabaseName()))->first();
        }

        return $this->_database;
    }

    /**
     * Get a list of fields from the request parameters
     *
     * @param array $queryparams
     *
     * @return array
     */
    public function parseRequestFields(array $queryparams): array
    {
        $fields = array_filter(explode(",", $queryparams['fields'] ?? ''));
        return $fields;
    }

    /**
     * Find entities by search terms
     *
     * Multiple search terms that are separated by whitespace are treated as AND condition.
     * Each term has to occur in at least one of the fields in the scope.
     * Multiple AND conditions can be separated by the pipe |.
     *
     * Examples:
     * - "123 | 666" searches all articles that contain 123 or 666.
     * - "123 666" searches all articles that contain 123 and 666.
     * - "123 | abc 666" searches all articles that contain either 123 or both of abc and 666.
     *
     * @param Query $query
     * @param array $options An array with the keys 'term' (the search term) and 'fields' (a field list).
     * @return Query
     */
    public function findTerm(Query $query, array $options)
    {
        $term = $options['term'] ?? '';
        $fields = $options['searchFields'] ?? [];
        $operator = $options['operator'] ?? 'LIKE';
        $type = $options['type'] ?? 'string';
        $selected = $options['selected'] ?? [];

        if (empty($term) || empty($fields) || !is_array($fields) || !is_string($term)) {
            return $query;
        }

        $conditions = Arrays::termConditions($term, $fields, $operator, $type);

        // Keep selected properties
        if (!empty($selected)) {
            $conditions['OR'][] = ['Properties.id IN' => $selected];
        }

        return $query->where($conditions);
    }

    /**
     * Find entities with specific values
     *
     * @param Query $query
     * @param array $options An array with the key 'values'
     *                       containing a nested array with field-values conditions
     * @return Query
     */
    public function findValues(Query $query, array $options)
    {
        $fields = $options['values'] ?? [];
        foreach ($fields as $fieldName => $fieldValues) {
            if (!empty($fieldValues)) {
                $query = $query
                    ->where([$this->getAlias() . '.' . $fieldName . ' IN' => $fieldValues]);
            }
        }
        return $query;
    }

    /**
     * Find articles by ID
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findHasIds(Query $query, array $options)
    {
        $alias = $this->getAlias();
        $ids = Attributes::commaListToIntegerArray($options[strtolower($alias)] ?? $options['id'] ?? []);

        if (!empty($ids)) {
            $query = $query
                ->where([
                    $alias . '.id IN' => $ids,
                ]);
        }

        return $query;
    }

    /**
     * Constructs a database query from request parameters
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasParams(Query $query, array $params): Query
    {

        $conditions = [];
        foreach ($this->getColumns() as $fieldKey => $fieldConfig) {

            if (empty($fieldConfig['filter']) || (empty($params[$fieldKey]))) {
                continue;
            }

            $fieldName = $fieldConfig['field'] ?? ($this->getAlias() . '.' . $fieldKey);
            $type = $fieldConfig['filter'] ?? $fieldConfig['type'] ?? 'text';
            if ($type === 'text') {
                $conditions[] = [
                    $fieldName . ' LIKE ' => '%' . $params[$fieldKey] . '%',
                ];
            }
            elseif ($type === 'select') {
                $conditions[] = [
                    $fieldName . ' IN' => $params[$fieldKey],
                ];
            }
        }

        if (!empty($conditions)) {
            $query = $query->where($conditions);
        }

        $query = $query->find('hasIds', $params);

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
        return $query;
    }

    /**
     * Find the complete entity
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findContainAll(Query $query, array $options): Query
    {
        return $query;
    }

    /**
     * Join tables to get nested values for selected columns
     *
     * The join configuration is determined from the sort key of full column configurations.
     * Column configurations are generated by calling getColumns(), which calls augmentSortSetup(),
     * on all columns names.
     *
     * ### Options
     * - search:  Search terms, indexed by column names. Each search term is either a string or an array of strings.
     * - sort:    Array of column names (as defined in the types config) or field extractions keys.
     *            Each item must be compatible with getColumns().
     * - columns: Array of column names.
     *            Functionally equivalent to the sort option if joined is true.
     *            Takes precedence over the sort option.
     * - joined: Whether the columns parameter is considered or not. Default is false.
     *           Joining the columns means that column values are retrieved using SQL
     *           and not by extracting nested values from the entities.
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findColumnFields(Query $query, array $options): Query
    {

        // Add term-field search with 'columns.' prefix to columns filter
        $term = $options['term'] ?? false;
        $field = $options['field'] ?? false;
        if (!empty($term) && !empty($field) && (str_starts_with($field, 'columns.'))) {
            $field = preg_replace('/^columns./', '', $field);
            if (!empty($field)) {
                $options['search'] = [$field => $term];
            }
        }

        // Get selected columns
        $selectedColumns = array_unique(array_merge($options['sort'] ?? [], array_keys($options['search'] ?? [])));

        // Whether to get column values from joins
        $joined = $options['joined'] ?? false;
        if ($joined) {
            $selectedColumns = array_unique(array_merge($options['columns'] ?? [], $options['sort'] ?? []));
        }

        // Join columns
        $columns = $this->getColumns($selectedColumns, [], ['joined' => $joined]);
        $options['columns'] = $columns;
        return $query->find('joinColumns', $options);
    }

    /**
     * Join tables to get nested values for columns
     *
     * The join configuration is determined from the columns key of full column configurations.
     * Column configurations are generated by calling getColumns(), which calls augmentSortSetup(),
     * on all columns names.
     *
     * ### Options
     * - columns: Array of full column configurations
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findJoinColumns(Query $query, array $options): Query
    {
        $columns = $options['columns'] ?? [];
        $search = $options['search'] ?? [];
        $alias = $this->getAlias();

        foreach ($columns as $columnKey => $columnConfig) {

            // TODO: MAybe remove, should be handled by getColumns() above? Not yet filtered in getColumns().
            if (!$columnConfig['selected']) {
                continue;
            }

            $sortConfig = $columnConfig['sort'] ?? [];
            if (empty($sortConfig)) {
                continue;
            }

            // Add the join to the array
            if (is_array($sortConfig) && !empty($sortConfig['relation'] ?? false)) {
                $joinThrough = $sortConfig['through'] ?? [];
                $joinOrder = [];

                $joinThrough[] = $sortConfig;
                $sourceAlias = $alias;

                foreach ($joinThrough as $joinNo => $joinConfig) {
                    $joinRelation = $joinConfig['relation'] ?? false;
                    $joinTable = $joinConfig['table'] ?? '';
                    $foreignKey = $joinConfig['foreign'] ?? null;

                    // The last join has the column key as alias, all others are numbered
                    if (($joinNo + 1) < count($joinThrough)) {
                        $targetAlias = $columnKey . '_' . $joinNo;
                    }
                    else {
                        $targetAlias = $columnKey;
                    }

                    // Join conditions
                    $joinConditions = [];
                    if (!empty($joinConfig['conditions']) && is_array($joinConfig['conditions'])) {
                        foreach ($joinConfig['conditions'] as $condName => $condValue) {
                            if ($condName === 'deleted') {
                                $joinConditions[] = "{$targetAlias}.deleted = 0";
                            }
                            else {
                                $condValue = Attributes::commaListToStringArray($condValue);
                                $condValue = implode(",", array_map(fn($x) => '"' . $x . '"', $condValue));
                                $joinConditions[] = "{$targetAlias}.{$condName} IN (" . $condValue . ")";
                            }
                        }
                    }

                    // Foreign key conditions
                    if (($joinRelation === 'hasmany')) {
                        $joinConditions[] = "{$targetAlias}.{$foreignKey} = {$sourceAlias}.id";
                        if (!empty($joinConfig['order'])) {
                            $joinOrder = [];
                            foreach ($joinConfig['order'] as $orderField) {
                                $joinOrder[] = $targetAlias . '.' . $orderField . ' ASC';
                            }
                        }
                    }
                    // belongsTo associations (users and projects)
                    elseif (($joinRelation === 'belongsto')) {
                        $joinConditions[] = "{$targetAlias}.id = {$sourceAlias}.{$foreignKey}";
                    }

                    // The join
                    $query = $query
                        ->join([
                            'table' => $joinTable,
                            'alias' => $targetAlias,
                            'type' => 'LEFT',
                            'conditions' => $joinConditions
                        ]);

                    $sourceAlias = $targetAlias;
                }

                // Field
                $joinField = $sortConfig['field'] ?? '';
                $typecast = $sortConfig['cast'] ?? false;
                $aggregate = explode('|', $sortConfig['aggregate'] ?? '');
                $aggregate = empty($aggregate) ? '' : end($aggregate);

                $targetAlias = $columnKey;
                $aggField = $targetAlias . '.' . $joinField;
                //$aggField = $query->func()->natural_sort_key([$aggField => 'identifier']);

                // Type cast
                if ($typecast) {
                    $aggField = $query->func()->cast($aggField, $typecast);
                }

                // Aggregate
                if ($aggregate == 'min') {
                    $query = $query->select([$columnKey => $query->func()->min($aggField)]);
                }
                elseif ($aggregate == 'max') {
                    $query = $query->select([$columnKey => $query->func()->max($aggField)]);
                }
                elseif ($aggregate == 'count') {
                    $query = $query->select([$columnKey => $query->func()->count($aggField)]);
                }
                // TODO: handle type casts
                elseif (($aggregate == 'collapse') && !$typecast) {
                    $aggOrder = empty($joinOrder) ? '' : ' ORDER BY ' . implode(',', $joinOrder);
                    $aggSeparator = ' SEPARATOR ", "';
                    $query = $query->select([$columnKey => 'GROUP_CONCAT(' . $aggField . $aggOrder . $aggSeparator . ')']);
                }
                else {
                    $query = $query->select([$columnKey => $aggField]);
                }
            }

            // Add search term conditions
            $searchTerm = $search[$columnKey] ?? [];
            $searchConfig = $columnConfig['search'] ?? [];
            if (!empty($searchTerm) && !empty($searchConfig)) {
                $searchConditions = Arrays::termConditions(
                    $searchTerm,
                    [$searchConfig['field']],
                    $searchConfig['operator'],
                    $searchConfig['type']
                );
                $query = $query->where($searchConditions);
            }
        }

        $query = $query->group([$alias . '.id']);

        return $query;
    }

    /**
     * Get pagination parameters
     *
     * Overwrite in subclasses
     *
     * @param array $params Parsed request parameters
     * @param array $columns
     * @return array
     */
    public function getPaginationParams(array $params = [], array $columns = [])
    {
        return [
            'className' => 'Total',
            'order' => [$this->captionField => 'ASC'],
            'limit' => 100,
            'maxLimit' => 500
        ];
    }

    /**
     * Transforms sort settings to a full sort array
     *
     * @param string $key The field extraction key (may include a pipe for aggregation)
     * @param array|string|boolean $sort The sort configuration
     * @return array|false
     */
    protected function augmentSortSetup($key, $sort)
    {
        // Get the sort key if provided in the sort settings
        if (is_string($sort)) {
            $key = $sort;
        }

        // Parse and prepare the sort key
        $sortKeyConfig = null;
        if (!is_null($key)) {
            $colPipe = explode('|', $key, 2);
            $colSort = explode('.', $colPipe[0] ?? '');

            // TODO: is it a good idea to test for * here?
            $sortAggregate = $colPipe[1] ?? (str_contains(end($colSort), '*') ? 'count' : 'collapse');

            // A simple field
            // TODO: Always return an array, never a simple field name
            if ((count($colSort) === 1) && $this->hasDatabaseField($colSort[0])) {
                $sortKeyConfig = $colSort[0];
            }

            // Sort by associations
            elseif (count($colSort) > 1) {

                $assocKeyIndex = 0;
                $sortKeyConfig = [];
                $source = $this;

                while ($source) {

                    $association = $source->getAssociationByProperty($colSort[$assocKeyIndex]);
                    $target = $association ? $association->getTarget() : null;

                    // TODO: get type from the association
                    $relationType = $association instanceof Association\HasMany ? 'hasmany' : 'belongsto';

                    if ($target) {

                        if ($relationType === 'belongsto') {
                            $targetField = $colSort[$assocKeyIndex + 1] ?? 'id';
                        }
                        else {
                            $targetField = $colSort[$assocKeyIndex + 2] ?? 'id';
                        }

                        $joinConfig = [
                            'table' => $target->getTable(),
                            'foreign' => $association->getForeignKey(),
                            'field' => $targetField,
                            'relation' => $relationType,
                            'conditions' => []
                        ];

                        if (($relationType === 'hasmany') && isset($colSort[$assocKeyIndex + 1])) {
                            // Add square bracket conditions
                            if (preg_match('/\[.*\]/', $colSort[$assocKeyIndex + 1], $relationConditions)) {
                                $relationConditions = trim($relationConditions[0], "[]");
                                $relationConditions = explode('=', $relationConditions);
                                if (count($relationConditions) === 2) {
                                    $joinConfig['conditions'][$relationConditions[0]] = $relationConditions[1];
                                }
                            }

                            // Add group concat order
                            $joinConfig['order'] = [];
                            $assocPrefix = $association->getName() . '.';
                            $sortOrder = $association->getSort();
                            $sortOrder = is_array($sortOrder) ? $sortOrder : [$sortOrder];
                            foreach ($sortOrder as $sortField) {
                                if (str_starts_with($sortField, $assocPrefix)) {
                                    $joinConfig['order'][] = substr($sortField, strlen($assocPrefix));;
                                }
                            }
                        }

                        if ($target->hasDatabaseField('deleted')) {
                            $joinConfig['conditions']['deleted'] = 0;
                        }

                        $sortKeyConfig[] = $joinConfig;

                        // Last association
                        if ($target->hasDatabaseField($targetField)) {
                            break;
                        }
                    }

                    // Next association
                    if ($relationType === 'belongsto') {
                        $assocKeyIndex += 1;
                    }
                    else {
                        $assocKeyIndex += 2;
                    }

                    $source = $target;
                }

                // Add through joins
                if (!empty($sortKeyConfig)) {
                    $through = array_slice($sortKeyConfig, 0, count($sortKeyConfig) - 1);
                    $sortKeyConfig = end($sortKeyConfig);
                    $sortKeyConfig['aggregate'] = $sortAggregate;
                    if (!empty($through)) {
                        $sortKeyConfig['through'] = $through;
                    }
                }
            }
        }

        // Merge augmented settings with passed default settings
        if (is_array($sort)) {
            if (is_array($sortKeyConfig)) {
                $sortKeyConfig['aggregate'] = $sortAggregate;
                $sortKeyConfig = array_merge($sortKeyConfig, $sort);
            }
            else {
                $sortKeyConfig = $sort;
            }
        }

        return $sortKeyConfig;
    }

    /**
     * Standardizes column settings.
     *
     * The column setup may come in different flavors:
     * - String keys are treated as column names, the value
     *   either contains the caption or the settings.
     * - With numeric keys, the value either contains the column name
     *   or the settings (which provide the column name in their name key)
     *
     * The function converts all variations to a name-value-list where
     * keys indicate the column name and values contain the settings.
     *
     * In the settings, the following keys are supported:
     * - key
     * - caption
     * - selectable true|false (default true)
     * - default true|false (default false)
     * - aggregate collapse|count|min|max
     * - sort Array with the keys:
     *   - table
     *   - type
     *   - field
     *   - cast
     *   - aggregate
     *
     * @param array $config Elliptic column settings
     * @param boolean $joined Whether the column is part of a join
     * @param array $default Values to merge into the result
     * @return array Standardized column settings
     */
    public function augmentColumnSetup($config, $joined = false, $default = [])
    {
        // TODO: cache settings

        $columns = [];
        foreach ($config as $key => $item) {
            // Name
            if (is_string($key)) {
                $colKey = $key;
            }
            elseif (is_array($item)) {
                $colKey = strval($item['name'] ?? $item['key'] ?? $key);
            }
            else {
                $colKey = strval($item);
            }

            $colName = Attributes::cleanFieldname($colKey, 'field');

            // Caption
            if (is_string($item)) {
                $colCaption = $item;
            }
            elseif (is_array($item)) {
                $colCaption = $item['caption'] ?? $colName;
            }
            else {
                $colCaption = $colName;
            }

            // Settings
            if (!is_array($item)) {
                $item = [];
            }

            $item = array_merge($item, $default);
            $item['name'] = $colName;
            $item['caption'] = $colCaption;

            $keyExtract = $item['key'] ?? $colKey;
            $keyParsed = Objects::parseFieldKey($keyExtract, [], $item);

            $item['key'] = $keyParsed['key'];
            if (!empty($keyParsed['aggregate'])) {
                $item['aggregate'] = $keyParsed['aggregate'];
            }

            // Sort and search settings
            // Example key: items.*[itemtype=topics].value
            $sortConfig = $this->augmentSortSetup($keyExtract, $item['sort'] ?? null);
            if (!is_null($sortConfig) && (is_string($sortConfig) || !empty($sortConfig['sort'] ?? true))) {
                // Sort config
                $item['sort'] = $sortConfig;

                // Search config
                $searchConfig = [];
                // TODO: augmentSortSetup should always return an array
                if (is_array($sortConfig)) {
                    $searchConfig['field'] = $item['name'] . '.' . ($sortConfig['field'] ?? '');
                    $searchConfig['type'] = 'string';
                    $searchConfig['operator'] = 'LIKE';
                } else {
                    $searchConfig['field'] = $this->getAlias() . '.' . $sortConfig;
                    $searchConfig['type'] = $this->getSchema()->getColumnType($sortConfig);
                    $searchConfig['operator'] = $searchConfig['type'] === 'string' ? 'LIKE' : '=';
                }
                $item['search'] = $searchConfig;

                // Join the field
                if ($joined) {
                    $item['key'] = $colName;
                }
            }

            $columns[$item['name']] = $item;
        }

        return $columns;
    }

    /**
     * Recursively merges the default values into the config.
     *
     * Missing settings in the config are overwritten with the defaults
     *
     * @param array $config
     * @param array $default
     * @param boolean $add Whether to add missing keys
     * @return array
     */
    protected function mergeColumnSetup($config, $default, $add = false)
    {
        // Shortcut
        if (empty($config)) {
            return ($default);
        }

        // Recursively add default values
        foreach ($default as $key => $value) {
            if (!isset($config[$key]) && $add) {
                $config[$key] = $value;
            }
            elseif (isset($config[$key]) && is_array($config[$key]) && is_array($value)) {
                $config[$key] = $this->mergeColumnSetup($config[$key], $value, true);
            }
        }

        return $config;
    }


    /**
     * Get columns to be rendered in table views
     *
     * Each returned array item (= each full column config) has the following keys:
     *
     * - name The internal name of the column
     * - caption The table column caption
     * - key The key to extract from the data in dot notation
     * - value Optionally, a placeholder string for rendering the column value
     * - action The field will be displayed in the provided actions, either a string or an array of strings.
     *          If the action key is missing, the field is displayed in all actions.
     * - type Whether a select or a text input is generated in edit forms
     * - options Select input options
     * - empty Whether an empty option can be selected in select inputs
     * - filter Whether to create a 'select' or a 'text' filter. Leave empty if no filter widget should be created.
     * - sort Boolean or a field name that is used for sorting
     * - link An array containing the URL array, strings in curly brackets are treated as placeholders
     * - selected Whether the field is selected. At least all fields provided in the $selected parameter are marked as true.
     *
     *  ### Options
     *  - type (string) Filter by type
     *  - joined (boolean) Join the columns to the query
     *
     * @param array $selected The list of selected fields (defined in the types config or direct database fields),
     *                        may include extraction strings for ad hoc columns.
     * @param array $default A default configuration of the fields that will be combined with the selected fields.
     * @param array $options
     * @return array Array of full column configurations
     */
    public function getColumns($selected = [], $default = [], $options = [])
    {
        if ($selected === false) {
            return [];
        }

        $joined = $options['joined'] ?? false;
        $default = $this->augmentColumnSetup($default, $joined);

        // Filter out non public columns
        if ($this::$userRole === 'guest') {
            $default = array_filter($default, fn($x) => $x['public'] ?? false);
        }

        // Mark selected columns
        foreach ($default as $key => $value) {
            $default[$key]['selected'] =
                (empty($selected) && ($value['default'] ?? false)) ||
                (in_array($key, $selected));
        }

        // Special features (custom columns and personalized column widths) are not available for guests
        if ($this::$userRole === 'guest') {
            return $default;
        }

        // Special feature: Custom ad hoc columns
        $custom = [];
        foreach ($selected as $field) {
            $columnConfig = $default;
            if (is_array($field)) {
                if (!isset($field['key'])) {
                    continue;
                }
                $columnConfig[$field['key']] = $field;
                $field = $field['key'];
            }
            $customField = Objects::parseFieldKey($field, $columnConfig,
                ['selected' => true, 'aggregate' => 'collapse']);
//            $custom[$customField['name']] = $customField;
            $customField = $this->augmentColumnSetup([$customField], $joined);
            $custom = array_merge($custom, $customField);
        }

        $columns = array_merge($custom, array_diff_key($default, $custom));

        // Special feature: Load column widths from the user settings and the configuration
        $modelIdentifier = strtolower($this->getAlias());
        $modelIdentifier = $this->_pluginName ? $this->_pluginName . '.' . $modelIdentifier : $modelIdentifier;
        $columnWidths = $this::$userSettings;
        $columnWidths = $columnWidths['columns'][$modelIdentifier] ?? [];
        $columns = array_map(
            function ($col) use ($columnWidths) {
                $col['width'] = $columnWidths[$col['name']] ?? $col['width'] ?? null;
                return $col;
            },
            $columns
        );

        return $columns;
    }

    protected function getConfiguredSearchFields($params) {
        return $this->searchFields;
    }

    /**
     * Get filter for table views
     *
     * ### Return keys
     * - search: Search fields and captions. Set $searchFields in each Table class.
     *
     * @param array $params
     * @return array
     */
    public function getFilter($params)
    {
        $searchFields = $this->getConfiguredSearchFields($params);
        $captions = Hash::extract($searchFields, '{*}.caption');
        $searchFields = array_combine(array_keys($searchFields), $captions);
        return ['search' => $searchFields];
    }

    /**
     * Get problems on the table level
     *
     * @return array
     */
    public function getProblems()
    {
        return [];
    }

    /**
     * Get a summary of the result set
     *
     * @param array $params
     * @return array
     */
    public function getSummary($params) {
        return [];
    }

    /**
     * Get sortable fields in the columns list
     *
     * @param $columns
     *
     * @return array
     */
    protected function getSortableFields($columns)
    {
//        $sortableFields = array_filter(
//            $columns,
//            fn($x) => (is_array($x['sort'] ?? false) || $this->hasDatabaseField($x['sort'] ?? false))
//        );
//
        $sortableFields = array_filter($columns, fn($x) => (($x['sort'] ?? false) !== false));

        $sortableFields = array_map(
            function ($col, $key) {
                if (is_string($col['sort'] ?? false)) {
                    return $col['sort'];
                }
                else {
                    return $col['sort']['key'] ?? $col['name'] ?? $key;
                }
            },
            $sortableFields,
            array_keys($sortableFields)
        );

        return $sortableFields;
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
        $params = Attributes::parseQueryParams($requestParameters, $this->parameters ?? []);
        $params['action'] = $requestAction;
        return $params;
    }

    /**
     * Prepare find parameters
     *
     * Calls
     *  - parseRequestParameters()
     *  - getColumns()
     *  - getPaginationsParams()
     *  - getFilter()
     *
     * @param array $requestParams
     * @param string $requestPath
     * @param string $requestAction
     * @return array An array with the values of $params, $columns, $paging, $filter
     */
    public function prepareParameters($requestParams, $requestPath = null, $requestAction = null, $joined = false)
    {
        $params = $this->parseRequestParameters($requestParams, $requestPath, $requestAction);
        $columns = $this->getColumns($params['columns'] ?? [], [], ['type' => $requestPath, 'joined' => $joined]);
        $paging = $this->getPaginationParams($params, $columns);
        $filter = $this->getFilter($params);

        if ($joined) {
            $params['joined'] = $joined;
        }

        return [$params, $columns, $paging, $filter];
    }

    /**
     * Find an association by the property it fills or by the association name
     *
     * @param string $property The property or association name
     * @return Association|null
     */
    public function getAssociationByProperty(string $property)
    {
        foreach (parent::associations() as $association) {
            if ($association->getProperty() === $property) {
                return $association;
            }
            elseif ($association->getName() === $property) {
                return $association;
            }
        }
        return null;
    }

    /**
     * Get default database name
     *
     * @return string
     */
    public static function defaultConnectionName(): string
    {
        return static::$defaultConnection;
    }

}

class SaveManyException extends \Cake\Core\Exception\CakeException
{
}

