<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use App\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Database\TypeFactory;
use Cake\Datasource\ConnectionManager;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;
use Cake\Utility\Security;
use Cake\Routing\Router;
use Cake\Error\ErrorTrap;
use Cake\Error\ExceptionTrap;

/*
 * Configure paths required to find CakePHP + general filepath constants
 */

if (env('PHPUNIT', false) || env('CODECEPTION', false) || Configure::read('test')) {
    require __DIR__ . '/paths_test.php';
} else {
    require __DIR__ . '/paths.php';
}

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

/**
 * Uncomment block of code below if you want to use `.env` file during development.
 * You should copy `config/.env.default to `config/.env` and set/modify the
 * variables as required.
 *
 * It is HIGHLY discouraged to use a .env file in production, due to security risks
 * and decreased performance on each request. The purpose of the .env file is to emulate
 * the presence of the environment variables like they would be present in production.
 */
// if (!env('APP_NAME') && file_exists(CONFIG . '.env')) {
//     $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
//     $dotenv->parse()
//         ->putenv()
//         ->toEnv()
//         ->toServer();
// }

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);

    // Load codeception settings
    if (env('PHPUNIT', false) || env('CODECEPTION', false) || Configure::read('test')) {
        $_SERVER['PHP_SELF'] = '/';
        Configure::load('app_test', 'default', true);

        // Will evaluate to true in the local test environment and to false in the GitLab pipeline
        // (prevents c3.php to be included in the pipeline, otherwise test would fail for still unknown reasons)
        if (env('CODECEPTION', false)) {
            //Optional (if not set the default c3 output dir will be used)
            define('C3_CODECOVERAGE_ERROR_LOG_FILE', ROOT . DS . 'logs/c3_error.log');
            include ROOT . DS . 'c3.php';
        }
    }

} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

/*
 * Load an environment local configuration file.
 * You can use a file like app_local.php to provide local overrides to your
 * shared configuration.
 */
//Configure::load('app_local', 'default');

/*
 * When debug = true the caches should only last for a short time.
 */
if (Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
    Configure::write('Cache._cake_routes_.duration', '+2 seconds');
}

/*
 * Set the default server timezone. Using UTC makes time calculations / conversions easier.
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
 */
date_default_timezone_set(Configure::read('App.defaultTimezone'));

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/**
 * Register application error and exception handlers.
 */
(new ErrorTrap(Configure::read('Error')))->register();
(new ExceptionTrap(Configure::read('Error')))->register();

/*
 * Include the CLI bootstrap overrides.
 */
if (PHP_SAPI === 'cli') {
    require CONFIG . 'bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 */
$fullBaseUrl = Configure::read('App.fullBaseUrl');

if (!$fullBaseUrl) {

    /*
     * When using proxies or load balancers, SSL/TLS connections might
     * get terminated before reaching the server. If you trust the proxy,
     * you can enable `$trustProxy` to rely on the `X-Forwarded-Proto`
     * header to determine whether to generate URLs using `https`.
     *
     * See also https://book.cakephp.org/4/en/controllers/request-response.html#trusting-proxy-headers
     */
    $trustProxy = false;

    $s = null;
    if (env('HTTPS') || ($trustProxy && env('HTTP_X_FORWARDED_PROTO') === 'https')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        $fullBaseUrl = 'http' . $s . '://' . $httpHost;
    }
    unset($httpHost, $s);
    Configure::write('App.fullBaseUrl', $fullBaseUrl);
}

if ($fullBaseUrl) {
    Router::fullBaseUrl($fullBaseUrl);
}
unset($fullBaseUrl);

Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
TransportFactory::setConfig(Configure::consume('EmailTransport'));
Mailer::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

/*
 * The default crypto extension in 3.0 is OpenSSL.
 * If you are migrating from 2.x uncomment this code to
 * use a more compatible Mcrypt based implementation
 */
//Security::engine(new \Cake\Utility\Crypto\Mcrypt());

/*
 * Setup detectors for mobile and tablet.
 */
ServerRequest::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();

    return $detector->isTablet();
});


/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
//Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
//Inflector::rules('irregular', ['red' => 'redlings']);
//Inflector::rules('uninflected', ['dontinflectme']);
//Inflector::rules('transliteration', ['/å/' => 'aa']);


/**
 * Custom data types
 */

//TypeFactory::map('json', 'Cake\Database\Type\JsonType');
TypeFactory::map('json', 'App\Database\Type\JsonType');
TypeFactory::map('negbool', 'App\Database\Type\NegBoolType');

/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
//Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
//Inflector::rules('irregular', ['datenbank' => 'datenbanken']);
//Inflector::rules('uninflected', ['datenbanken']);
//Inflector::rules('transliteration', ['/å/' => 'aa']);

/*
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on Plugin to use more
 * advanced ways of loading plugins
 *
 * Plugin::loadAll(); // Loads all plugins at once
 * Plugin::load('Migrations'); //Loads a single plugin named Migrations
 *
 */

/**
 * Default database names and version
 */

define('DATABASE_STAGE', 'stage');
define('DATABASE_PUBLIC', 'public');
define('DATABASE_CURRENT_VERSION', '4.5');

/**
 * Publication constants
 */
define('PUBLICATION_DRAFTED',0);
define('PUBLICATION_INPROGRESS',1);
define('PUBLICATION_COMPLETE',2);
define('PUBLICATION_PUBLISHED',3);
define('PUBLICATION_SEARCHABLE',4);

define('PUBLICATION_BINARY_UNPUBLISHED',0);
define('PUBLICATION_BINARY_PUBLISHED',1);

/**
 * View modes
 */

// TODO: replace 'code' by 'revise' in modes
define('MODE_DEFAULT', 'default');
define('MODE_PREVIEW', 'preview');
define('MODE_REVISE', 'code');
define('MODE_STAGE', 'stage');


/**
 * Project user roles
 */

define('USER_AUTHOR', 0);
define('USER_ADMIN', 1);
define('USER_HIDDEN', 2);

/**
 * Field definitions
 */
define('FIELD_SECTIONS_SIGNATURE','alias');
define('FIELD_ARTICLES_SIGNATURE','signature');
define('FIELD_PROJECTS_SIGNATURE','signature');
define('VALUE_INVALID_LFT', -1);
define('VALUE_INVALID_ID', -1);

/**
 * Type definitions
 */
define('ITEMTYPE_FULLTEXT', 'search');
define('ITEMTYPE_COLLECTION', 'collections');
define('ITEMTYPE_SIGNATURES', 'signatures');
define('SECTIONTYPE_COLLECTION', 'collections');
define('SECTIONTYPE_SIGNATURES', 'signatures');
define('SECTIONNAME_SIGNATURES', 'Signaturen');
define('PROPERTYTYPE_LITERATURE', 'literature');

/**
 * Limits
 */

define('TYPES_LIMIT', 1000);

/**
 * Lock modes
 */

// Equals lm_update, the only used lock mode in EpiDesktop
define('LOCKMODE_EPIDESKTOP', 1702146);

/**
 * Serialization formats
 */

define('RENDERED_FORMATS', ['html', 'txt', 'md', 'jsonld', 'rdf', 'ttl','geojson']);
define('VIEW_FORMATS', ['html', 'md','txt']);
define('TRIPLE_FORMATS', ['jsonld', 'rdf', 'ttl']);
define('PLAINTEXT_FORMATS', ['txt', 'md', 'jsonld', 'rdf', 'ttl', 'geojson']);

define('API_FORMATS', ['json', 'xml', 'csv']);

define('API_EXTENSIONS', ['json', 'xml', 'csv', 'jsonld', 'rdf', 'ttl', 'md', 'geojson']);
define('API_CONTENTTYPES', ['application/json', 'application/xml', 'text/csv', 'application/ld+json', 'application/rdf+xml', 'text/turtle', 'text/markdown', 'application/geo+json']);

/**
 * Namespaces
 */

define(
    'SERIALIZE_NAMESPACES',
    [
        'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
        'hydra' => 'http://www.w3.org/ns/hydra/core#'
    ]
);
