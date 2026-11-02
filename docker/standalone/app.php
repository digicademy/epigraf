<?php
/**
 * Epigraf configuration for a local standalone image
 */

use Cake\Cache\Engine\FileEngine;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Log\Engine\ConsoleLog;
use Cake\Log\Engine\FileLog;
use Cake\Mailer\Transport\MailTransport;

$cache = fn(?string $sub, string $prefix, string $duration) => [
    'className' => FileEngine::class,
    'path'      => $sub === null ? CACHE : CACHE . $sub . DS,
    'prefix'    => $prefix,
    'serialize' => true,
    'duration'  => $duration,
];

$db = [
    'className'        => Connection::class,
    'driver'           => Mysql::class,
    'persistent'       => false,
    'host'             => env('MYSQL_HOST', 'sql'),
    'port'             => env('MYSQL_PORT', '3306'),
    'username'         => env('MYSQL_USER', 'epigraf'),
    'password'         => env('MYSQL_PASS', ''),
    'encoding'         => 'utf8mb4',
    'timezone'         => 'UTC',
    'cacheMetadata'    => true,
    'quoteIdentifiers' => false,
    'log'              => false,
];

return [
    'debug'        => filter_var(env('DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'production'   => !filter_var(env('DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'test'         => false,
    'enforceHttps' => false,

    'Data' => [
        'root'      => '/var/www/data' . DS,
        'shared'    => '/var/www/data' . DS . 'shared' . DS,
        'databases' => '/var/www/data' . DS . 'databases' . DS,
    ],

    'Pages' => ['contexthelp' => 'I. Kontexthilfe'],

    'App' => [
        'namespace'       => 'App',
        'icon'            => 'img/favicon_lightblue.png',
        'encoding'        => 'UTF-8',
        'defaultLocale'   => env('APP_DEFAULT_LOCALE', 'en_EN.UTF-8'),
        'defaultTimezone' => env('APP_DEFAULT_TIMEZONE', 'Europe/Berlin'),
        'RateLimit'       => ['limit' => 600, 'interval' => 60],
        'cors'            => json_decode(env('APP_CORS', '[]'), true),
        'base'            => false,
        'dir'             => 'src',
        'webroot'         => 'htdocs',
        'wwwRoot'         => WWW_ROOT,
        'fullBaseUrl'     => env('APP_FULL_BASE_URL', 'http://localhost:8080'),
        'imageBaseUrl'    => 'img/',
        'cssBaseUrl'      => 'css/',
        'jsBaseUrl'       => 'js/',
        'paths' => [
            'plugins'   => [ROOT . DS . 'plugins' . DS],
            'templates' => [ROOT . DS . 'templates' . DS],
            'locales'   => [ROOT . DS . 'resources' . DS . 'locales' . DS],
        ],
        'epidesktop' => false,
    ],

    'Logins'   => ['form' => true, 'token' => true, 'remote' => false],
    'Security' => ['salt' => env('SECURITY_SALT')],
    'Asset'    => ['timestamp' => 'force'],

    'Jobs' => [
        'delay'       => false,
        'scheme'      => 'tcp',
        'host'        => '127.0.0.1',
        'port'        => 6379,
        'queue_name'  => 'jobs_queue',
        'status_name' => 'jobs_status',
    ],

    'Cache' => [
        'default'       => $cache(null,         'epi_default_',   '+1 day'),
        '_cake_core_'   => $cache('persistent', 'epi_cake_core_', '+1 year'),
        '_cake_model_'  => $cache('models',     'epi_model_',     '+1 year'),
        '_cake_routes_' => $cache(null,         'epi_routes_',    '+1 year'),
        'index'         => $cache('index',      'epi_index_',     '+30 minutes'),
        'views'         => $cache('views',      'epi_views_',     '+1 day'),
        'results'       => $cache('results',    'epi_results_',   '+1 day'),
        'services'      => $cache('services',   'epi_services_',  '+1 year'),
    ],

    'Error' => [
        'errorLevel' => E_ERROR,
        'skipLog'    => [],
        'log'        => true,
        'trace'      => true,
        'logger'     => \App\Error\ErrorLogger::class,
    ],

    'EmailTransport' => ['default' => ['className' => MailTransport::class]],
    'Email' => ['default' => ['transport' => 'default', 'from' => 'epigraf@localhost']],

    'Services' => [
        'llm' => [
            'base_url'     => env('DATABOARD_URL', 'https://databoard.uni-muenster.de/'),
            'access_token' => env('DATABOARD_ACCESSTOKEN', null),
        ],
        'geo' => [
            'base_url'  => 'https://nominatim.openstreetmap.org/',
            'useragent' => 'Epigraf/5.0 (github.com/digicademy/epigraf)',
        ],
    ],

    'Datasources' => [
        'default'       => $db + ['database' => 'epigraf'],
        'projects'      => $db + ['database' => 'epi_playground'],
        'test'          => $db + ['database' => 'test_epigraf'],
        'test_projects' => $db + ['database' => 'test_projects'],
        'test_public'   => $db + ['database' => 'test_public'],
    ],

    'Log' => [
        'debug' => [
            'className' => FileLog::class, 'path' => LOGS, 'file' => 'debug',
            'scopes' => false, 'levels' => ['notice', 'info', 'debug'],
        ],
        'error' => [
            'className' => FileLog::class, 'path' => LOGS, 'file' => 'error',
            'scopes' => false, 'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        ],
        'queries' => [
            'className' => FileLog::class, 'path' => LOGS, 'file' => 'queries',
            'scopes' => ['queriesLog'],
        ],
        // log to stderr so that logs show up in "docker compose logs"
        'stderr' => [
            'className' => ConsoleLog::class, 'stream' => 'php://stderr',
            'scopes' => false, 'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        ],
    ],

    'Session' => ['defaults' => 'cache', 'timeout' => 120, 'cookie' => 'EPIGRAF5'],
];