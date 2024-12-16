<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\DashedRoute;


/*
 * This file is loaded in the context of the `Application` class.
  * So you can use  `$this` to reference the application class instance
  * if required.
 */
return function (RouteBuilder $routes): void {


    $routes->setExtensions(API_EXTENSIONS);

    $routes->plugin(
        'Epi',
        ['path' => '/epi/{database}'],

        function (RouteBuilder $routes) {
            $routes->connect('/iri/*', ['controller' => 'Iris', 'action' => 'show']);

            $routes->fallbacks(DashedRoute::class);
        }
    );
};
