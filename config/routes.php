<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */


use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;
use Cake\Http\ServerRequest;

/*
 * This file is loaded in the context of the `Application` class.
  * So you can use  `$this` to reference the application class instance
  * if required.
 */
return function (RouteBuilder $routes): void {
    /**
     * The default class to use for all routes
     *
     * The following route classes are supplied with CakePHP and are appropriate
     * to set as the default:
     *
     * - Route
     * - InflectedRoute
     * - DashedRoute
     *
     * If no call is made to `Router::defaultRouteClass()`, the class used is
     * `Route` (`Cake\Routing\Route\Route`)
     *
     * Note that `Route` does not do any inflections on URLs which will result in
     * inconsistently cased URLs when used with `:plugin`, `:controller` and
     * `:action` markers.
     *
     */
    Router::defaultRouteClass(DashedRoute::class);

    $routes->setExtensions(API_EXTENSIONS);

    $routes->scope('/', function (RouteBuilder $builder) {

        /**
         * Register middleware
         */
        //    $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        //        'httpOnly' => true
        //    ]));

        /**
         * Apply a middleware to the current route scope.
         * Requires middleware to be registered via `Application::routes()` with `registerMiddleware()`
         */
        //$routes->applyMiddleware('csrf');

        /* Pages */
        $builder->connect('/', ['controller' => 'Pages', 'action' => 'show', 'start']);

        /* Pages */
        $builder->connect('/docs/{action}/pages/*', ['controller' => 'Pages']);
        $builder->connect('/pages/', ['controller' => 'Pages', 'action' => 'show', 'start']);
        $builder->connect('/pages/{iri}', ['controller' => 'Pages', 'action' => 'show'])
            ->setPass(['iri'])
            ->setPatterns(['iri' => '[a-z0-9_-]+']);

        /* Help */
        $builder->connect('/docs/{action}/help/*', ['controller' => 'Help']);
        $builder->connect('/help/', ['controller' => 'Help', 'action' => 'show', 'start']);
        $builder->connect('/help/{iri}', ['controller' => 'Help', 'action' => 'show'])
            ->setPass(['iri'])
            ->setPatterns(['iri' => '[a-z0-9_-]+']);

        /* Wiki */
        $builder->connect('/docs/{action}/wiki/*', ['controller' => 'Wiki']);
        $builder->connect('/wiki/', ['controller' => 'Wiki', 'action' => 'show', 'start']);
        $builder->connect('/wiki/{iri}', ['controller' => 'Wiki', 'action' => 'show'])
            ->setPass(['iri'])
            ->setPatterns(['iri' => '[a-z0-9_-]+']);

        /* Export and article view */
        //TODO: implement Articles/export instead of Jobs/add
        $builder->connect('/export', ['controller' => 'Jobs', 'action' => 'add']);
        $builder->connect('/show', ['controller' => 'Articles', 'action' => 'show']);

        /* IRIs */
        $builder->connect('/iri/*',
            ['plugin' => 'Epi', 'database' => DATABASE_PUBLIC, 'controller' => 'Iris', 'action' => 'show']
        );

        /**
         * Connect catchall routes for all controllers.
         *
         * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
         *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
         *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
         *
         * Any route class can be used with this method, such as:
         * - DashedRoute
         * - InflectedRoute
         * - Route
         * - Or your own route class
         *
         * You can remove these routes once you've connected the
         * routes you want in your application.
         */
        $builder->fallbacks(DashedRoute::class);
    });


    /**
     * Add token to links
     *
     */
    Router::addUrlFilter(function (array $params, ServerRequest $request) {

        // For users controller, remove token
        if ((strtolower($params['controller'] ?? '')) == 'users') {
            unset($params['?']['token']);
        }

        //  For other controllers, carry token
        elseif ($request->getQuery('token') && !isset($params['?']['token'])) {
            $params['?']['token'] = $request->getQuery('token');
        }

        // If set to false, remove token
        elseif (isset($params['?']['token']) && ($params['?']['token'] === false)) {
            unset($params['?']['token']);
        }

        return $params;
    });


    /**
     * Persist active database parameter
     *
     * Based on the current request and the target URL,
     * the parameter is passed as routing parameter or as query parameter
     */
    Router::addUrlFilter(function (array $params, ServerRequest $request) {
        $database = $request->getParam('database') ?? $request->getQuery('database');
        if (isset($params['database']) && ($params['database'] === false)) {
            unset($params['?']['database']);
            unset($params['database']);
        }
        else if ($database) {

            // URLs completely outside the plugin
            if (!$request->getParam('plugin') && !isset($params['database'])) {
                $params['?']['database'] = $database;
            }

            // URLs pointing outwards
            elseif ($request->getParam('plugin') && ($params['plugin'] ?? true) === false) {
                $params['?']['database'] = $database;
            }

            //  URLs completely inside the plugin
            elseif ($request->getParam('plugin') && !isset($params['database'])) {
                $params['database'] = $database;
            }

        }

        // Remove database parameter for login & logout
        if (
            (strtolower($params['controller'] ?? '') == 'users') &&
            (in_array($params['action'] ?? '', ['login', 'logout']))
        ) {
            unset($params['?']['database']);
        }

        return $params;
    });

    /**
     * Persist active theme parameter
     *
     */
    Router::addUrlFilter(function (array $params, ServerRequest $request) {
        $theme = $request->getQuery('theme', 'default');
        if ($theme !== 'default') {
            $params['?']['theme'] = $theme;
        }

        return $params;
    });
};
