<?php
declare(strict_types=1);

namespace Widgets\Test\TestCase\View\Helper;

use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Widgets\View\Helper\LinkHelper;

/**
 * Files\View\Helper\LinkHelper Test Case
 */
class LinkHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Widgets\View\Helper\LinkHelper
     */
    protected $Link;

    protected RouteBuilder $routeBuilder;

    /**
     * Setup method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->routeBuilder = Router::createRouteBuilder('/');
        $this->routeBuilder->connect('/{controller}/{action}/*');

        $view = new View();
        $this->Link = new LinkHelper($view);
    }

    /**
     * Teardown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Link);

        parent::tearDown();
    }

    /**
     * Test sort link
     *
     * @return void
     */
    public function testSortLink(): void
    {
        $request = new ServerRequest([
            'params' => [
                'plugin' => null,
                'controller' => 'Test',
                'action' => 'index',
            ],
            'url' => '',
            'base' => '',
            'webroot' => '/',
            'query' => []
        ]);
        Router::setRequest($request);
        $this->Link->getView()->setRequest($request);

        $expected = '<a href="/Test/index?sort=field&amp;direction=asc">Title</a>';
        $result = $this->Link->sortLink('field', 'Title');
        $this->assertSame($expected, $result);

        $request = new ServerRequest([
            'params' => [
                'plugin' => null,
                'controller' => 'Test',
                'action' => 'index',
            ],
            'url' => '',
            'base' => '',
            'webroot' => '/',
            'query' => [
                'sort' => 'field',
                'direction' => 'asc',
            ]
        ]);
        Router::setRequest($request);
        $this->Link->getView()->setRequest($request);

        $expected = '<a href="/Test/index?sort=field&amp;direction=desc" class="asc">Title</a>';
        $result = $this->Link->sortLink('field', 'Title');
        $this->assertSame($expected, $result);

        $request = new ServerRequest([
            'params' => [
                'plugin' => null,
                'controller' => 'Test',
                'action' => 'index',
            ],
            'url' => '',
            'base' => '',
            'webroot' => '/',
            'query' => [
                'sort' => 'field',
                'direction' => 'desc',
            ]
        ]);
        Router::setRequest($request);
        $this->Link->getView()->setRequest($request);

        $expected = '<a href="/Test/index?sort=field&amp;direction=asc" class="desc">Title</a>';
        $result = $this->Link->sortLink('field', 'Title');
        $this->assertSame($expected, $result);
    }
}
