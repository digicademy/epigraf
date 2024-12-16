<?php
namespace Epi\Test\TestCase\Model\Entity;

use App\Utilities\Converters\Arrays;
use Cake\Collection\CollectionInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Epi\Model\Table\ArticlesTable;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Model\Table\ArticlesTable Test Case
 */
class ArticleTest extends EpiTestCase
{
    /**
     * Test subject
     *
     * @var \Epi\Model\Table\ArticlesTable
     */
    public $Articles;

    /**
     * Use auto fixtures
     *
     * @var bool
     */
    public $autoFixtures = true;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
        'app.Permissions',
        'app.Pipelines',
        'app.Databanks',
	];


    public $dumps = [
        'test_projects' => 'test_projects.sql'
    ];

    public RouteBuilder $routeBuilder;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Articles') ? [] : ['className' => ArticlesTable::class];
        $this->Articles = $this->fetchTable('Articles', $config);
        $this->Articles::$userRole = 'devel';
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Articles);

        parent::tearDown();
    }


    /**
	 * Test getValueNested method
     *
	 * @return void
	 */
	public function testGetValueNested()
	{
		$id = 1;

		$article = $this->Articles
            ->find('containFields')
			->where(['Articles.id'=>$id])
			->first();

        $signature = $article->getValueFormatted('signature');
        $this->assertEquals('Signatur 1',$signature);

        $signature = $article->getValueNested('signature',['aggregate'=>'collapse']);
        $this->assertEquals('Signatur 1',$signature);

        $signature = $article->getValueFormatted('project.signature');
        $this->assertEquals('TP1', $signature);

        $signature = $article->getValueNested('project.signature',['aggregate' =>'collapse']);
        $this->assertEquals('TP1',$signature);

        $date = $article->getValueFormatted('items.{*}[itemtype=conditions].date_value');
        $this->assertEquals(['1234'],$date);

        $date = $article->getValueNested('items.{*}[itemtype=conditions].date_value',['aggregate'=>'collapse']);
        $this->assertEquals('1234',$date);
    }

    /**
     * Test getting file properties
     *
     * @return void
     */
    public function testFileProperties() {

        $this->routeBuilder = Router::createRouteBuilder('/');

        $this->routeBuilder->plugin(
            'Epi',
            ['path' => '/epi/{database}'],
            function (RouteBuilder $routes) {
                $routes->fallbacks(DashedRoute::class);
            }
        );

        $article = $this->Articles
            ->find('containFields')
            ->where(['Articles.id' => 1])
            ->first();

        $images = $article->getValueNested('items.{*}[itemtype=images].file_properties', ['aggregate' => false]);
        $images = Arrays::array_remove_keys($images, ['root']);

        $compare = $this->saveComparisonJson($images);
        $this->assertJsonStringEqualsComparison($compare);
    }
}
