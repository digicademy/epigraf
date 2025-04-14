<?php

use Cake\Cache\Engine\FileEngine;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

return [
    'debug' =>false,
    'production' => true,
    'test' => true,

    'enforceHttps' => false,

    'App' => [
        'defaultLocale' => env('APP_DEFAULT_LOCALE', 'en_EN.UTF-8'),
        'defaultTimezone' => env('APP_DEFAULT_TIMEZONE', 'Europe/Berlin')
    ],

    'Pages' => [
        'contexthelp' => 'I. Kontexthilfe'
    ],

    'Asset' => [
        'timestamp' => true,
    ],

    /**
     * No delayed jobs in test system
     *
     */
    'Jobs' => [
        'delay' => false,
        'scheme' => 'tcp',
        'host'   => 'redis',
        'port'   => 6379,
        'queue_name' => 'jobs_queue',
        'status_name' => 'jobs_status'
    ],

    /**
     * Connection information used by the ORM to connect
     * to your application's datastores.
     * Do not use periods in database name - it may lead to error.
     * See https://github.com/cakephp/cakephp/issues/6471 for details.
     * Drivers include Mysql Postgres Sqlite Sqlserver
     * See vendor\cakephp\cakephp\src\Database\Driver for complete list
     */
    'Datasources' => [
        'default' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'persistent' => false,
            'host' => 'test_sql',
            'port' => '3306', //Standard mySQL port: 3306
            'username' => 'root',
            'password' => 'root',
            'database' => 'test_epigraf',
            'encoding' => 'utf8mb4',
            'timezone' => 'Europe/Berlin',
            'flags' => [],
            'cacheMetadata' => true,
            'log' => false,

            /**
             * Set identifier quoting to true if you are using reserved words or
             * special characters in your table or column names. Enabling this
             * setting will result in queries built using the Query Builder having
             * identifiers quoted when creating SQL. It should be noted that this
             * decreases performance because each query needs to be traversed and
             * manipulated before being executed.
             */
            'quoteIdentifiers' => false,

            /**
             * During development, if using MySQL < 5.6, uncommenting the
             * following line could boost the speed at which schema metadata is
             * fetched from the database. It can also be set directly with the
             * mysql configuration directive 'innodb_stats_on_metadata = 0'
             * which is the recommended value in production environments
             */
            //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],

            'url' => env('DATABASE_URL', null),
        ],

        /**
         *  Set connection to Epigraf databases.
         *  Database name will be automatically set in runtime.
         */
        'projects' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'persistent' => false,
            'host' => 'test_sql',
            'port' => '3306',
            'username' => 'root',
            'password' => 'root',
            'database' => 'test_projects',
            'encoding' => 'utf8mb4',
            'timezone' => 'Europe/Berlin',
            'cacheMetadata' => true,
            'log' => false
        ],

        /**
         * The test connection is used during the test suite.
         */
        'test' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'persistent' => false,
            'host' => 'test_sql',
            'port' => '3306',
            'username' => 'root',
            'password' => 'root',
            'database' => 'test_epigraf',
            'encoding' => 'utf8mb4',
            'timezone' => 'Europe/Berlin',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
            'log' => false,
            //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],
            'url' => env('DATABASE_TEST_URL', null),
        ],

        'test_projects' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'persistent' => false,
            'host' => 'test_sql',
            'port' => '3306',
            'username' => 'root',
            'password' => 'root',
            'database' => 'test_projects',
            'encoding' => 'utf8mb4',
            'timezone' => 'Europe/Berlin',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
            'log' => false,
            //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],
            'url' => env('DATABASE_TEST_URL', null),
        ],

        'test_public' => [
            'className' => Connection::class,
            'driver' => Mysql::class,
            'persistent' => false,
            'host' => 'test_sql',
            'port' => '3306',
            'username' => 'root',
            'password' => 'root',
            'database' => 'test_public',
            'encoding' => 'utf8mb4',
            'timezone' => 'Europe/Berlin',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
            'log' => false,
            //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],
            'url' => env('DATABASE_TEST_URL', null),
        ]
    ],

    /**
     *  Path of files in test system
     *
     *  All paths should end with the appropriate directory separator
     */
    'Data' => [
        //Root directory of export and databases directory.
        //Will be used as root for file administration in the web interface and should contain export and databases directory
        'root'=> ROOT . DS . 'tests' . DS . 'Files' . DS, //default: ROOT . DS . 'data' . DS

        //Files shared between all users (e.g. wiki)
        'shared'=> ROOT . DS . 'tests' . DS . 'Files'. DS . 'shared'. DS, //default: ROOT . DS . 'data' . DS.'shared'.DS

        //Every database needs its own directory for storing files like "Hilfsmittel" and pictures.
        //Subdirectories are automatically created for every database
        'databases' => ROOT . DS . 'tests' . DS . 'Files' . DS . 'databases'. DS, //default: ROOT . DS . 'data' . DS . 'databases'.DS,

        // Test files for comparisons
        'comparisons' => ROOT . DS . 'tests' . DS . 'Comparisons' . DS,

        // Test files for posts data
        'testdata' => ROOT . DS . 'tests' . DS . 'Testdata' . DS
    ],

    /**
     * Configure the cache adapters.
     */
    'Cache' => [
        'default' => [
            'className' => FileEngine::class,
            'path' => CACHE,
            'prefix' => '',
            'url' => env('CACHE_DEFAULT_URL', null),
        ],


        /**
         * Configure the cache used for general framework caching.
         * Translation cache files are stored with this configuration.
         * Duration will be set to '+1 year' in bootstrap.php when debug = false
         * If you set 'className' => 'Null' core cache will be disabled.
         */
        '_cake_core_' => [
            'className' => FileEngine::class,
            'prefix' => 'myapp_cake_core_',
            'path' => CACHE . 'persistent' . DS,
            'serialize' => true,
            'duration' => '+2 minutes',
            'url' => env('CACHE_CAKECORE_URL', null),
        ],

        /**
         * Configure the cache for model and datasource caches. This cache
         * configuration is used to store schema descriptions, and table listings
         * in connections.
         * Duration will be set to '+1 year' in bootstrap.php when debug = false
         */
        '_cake_model_' => [
            'className' => FileEngine::class,
            'prefix' => 'myapp_cake_model_',
            'path' => CACHE . 'models' . DS,
            'serialize' => true,
            'duration' => '+2 minutes',
            'url' => env('CACHE_CAKEMODEL_URL', null),
        ],

        /**
         * Configure the cache for routes. The cached routes collection is built the
         * first time the routes are processed via `config/routes.php`.
         * Duration will be set to '+2 seconds' in bootstrap.php when debug = true
         */
        '_cake_routes_' => [
            'className' => FileEngine::class,
            'prefix' => 'myapp_cake_routes_',
            'path' => CACHE,
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKEROUTES_URL', null),
        ],

        'index' => [
            'className' => FileEngine::class,
            'prefix' => 'myapp_cake_index_',
            'path' => CACHE . 'index' . DS,
            'serialize' => true,
            'duration' => '+30 minutes',
            //'url' => env('CACHE_CAKEMODEL_URL', null),
        ],

        'views' => [
            'className' => 'File',
            'path' => CACHE . 'views' . DS,
            'prefix' => 'epi_views_',
            'duration' => '+1 day'
        ],

        'results' => [
            'className' => 'File',
            'path' => CACHE . 'results' . DS,
            'prefix' => 'epi_results_',
            'duration' => '+1 day'
        ]
    ]
];
