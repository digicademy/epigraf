<?php
declare(strict_types=1);

namespace Epi\Test\TestCase\Controller;

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Epi\Test\TestCase\EpiTestCase;

/**
 * App\Controller\IrisController Test Case
 *
 */
class IrisControllerTest extends EpiTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
        'app.Pipelines',
        'app.Permissions',
        'app.TwoDatabanks',

        'plugin.Epi.Users',
        'plugin.Epi.Token',
        'plugin.Epi.Meta',

        'plugin.Epi.PublicUsers',
        'plugin.Epi.PublicToken',
        'plugin.Epi.PublicMeta',

        'plugin.Epi.TransferPropertiesPublic',
        'plugin.Epi.PublicTypes'
    ];

    protected RouteBuilder $routeBuilder;

    /**
     * Test redirect with content type negotiation
     *
     * @return void
     */
    public function testViewProperties()
    {

        // Redirect route to test database (test_public instead of epi_public)
        // @deprecated: Not necessary anymore, prefixes are added based on the test / production mode
        //        $this->routeBuilder = Router::createRouteBuilder('/');
        //        $this->routeBuilder->scope('/', function (RouteBuilder $routes) {
        //            $routes->connect('/iri/*', ['plugin'=>'Epi','database'=> DATABASE_PUBLIC, 'controller' => 'Iris', 'action' => 'show']);
        //            $routes->fallbacks(DashedRoute::class);
        //        });

        $this->loginUser('admin');
        $this->get('iri/properties/materials/knownmaterial');

        $this->assertResponseCode(303);
        $this->assertRedirect(
            [
                'plugin' => 'Epi',
                'database' => DATABASE_PUBLIC,
                'controller' => 'Properties',
                'action' => 'view',
                3
            ]
        );

        // Get the JSON document
        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->get('epi/' . DATABASE_PUBLIC . '/properties/view/3');
        $this->assertJsonResponseEqualsComparison();
    }
}
