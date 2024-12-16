<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.3.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\TestCase;

use App\Application;
use Cake\Core\Configure;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Http\Middleware\HttpsEnforcerMiddleware;
use Cake\Error\Middleware\ErrorHandlerMiddleware;

use InvalidArgumentException;



/**
 * ApplicationTest class
 */
class ApplicationTest extends AppTestCase
{

    /**
     * Test bootstrap
     *
     * Assert existence of plugin features.
     *
     * @return void
     */
    public function testBootstrap()
    {
        Configure::write('debug', false);
        $app = new Application(dirname(dirname(__DIR__)) . '/config');
        $app->bootstrap();
        $plugins = $app->getPlugins();

        $this->assertCount(6, $plugins);
        $this->assertTrue($plugins->has('Rest'), 'plugins has Rest');
        $this->assertTrue($plugins->has('Epi'), 'plugins has Epi');
        $this->assertTrue($plugins->has('Files'), 'plugins has Files');
        $this->assertTrue($plugins->has('Widgets'), 'plugins has Widgets');
    }

    /**
     * Test bootstrap plugin without halt
     *
     * @return void
     */
    public function testBootstrapPluginWithoutHalt()
    {
        $this->expectException(InvalidArgumentException::class);

        $app = $this->getMockBuilder(Application::class)
            ->setConstructorArgs([dirname(dirname(__DIR__)) . '/config'])
            ->onlyMethods(['addPlugin'])
            ->getMock();

        $app->method('addPlugin')
            ->will($this->throwException(new InvalidArgumentException('test exception.')));

        $app->bootstrap();
    }

    /**
     * Test middleware
     *
     * Assert instances of middleware classes
     *
     * @return void
     */
    public function testMiddleware()
    {
        $app = new Application(dirname(dirname(__DIR__)) . '/config');
        Configure::write('enforceHttps', true);
        $middleware = new MiddlewareQueue();

        $middleware = $app->middleware($middleware);

        $middleware->seek(0);
        $this->assertInstanceOf(ErrorHandlerMiddleware::class, $middleware->current());

        $middleware->seek(1);
        $this->assertInstanceOf(AssetMiddleware::class, $middleware->current());

        $middleware->seek(2);
        $this->assertInstanceOf(HttpsEnforcerMiddleware::class, $middleware->current());

        $middleware->seek(3);
        $this->assertInstanceOf(RoutingMiddleware::class, $middleware->current());
    }

}
