<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Test\TestCase\Model\Behavior;

use App\Model\Table\BaseTable;
use App\View\XmlView;
use Epi\Model\Table\ArticlesTable;
use Epi\Test\TestCase\EpiTestCase;
use App\Cache\Cache;

/**
 * App\Model\Behavior\IndexBehavior Test Case
 */

class IndexBehaviorTest extends EpiTestCase
{
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

    public ArticlesTable|null $Articles = null;

    /**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp(): void
	{
		parent::setUp();
        /** @var ArticlesTable Articles */
		$this->Articles = $this->fetchTable('Epi.Articles');
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
     * test saveIndex and loadIndex
     *
     * @return void
     */
    public function testSaveLoadIndex(): void
    {

        // Set user role because otherwise the _isVisible() method will prevent rendering the data
        BaseTable::$userRole = 'admin';

//        $this->loginUser('admin');
        $cachekey = 'testindex';

        $contain = [
            'Sections' => ['strategy'=>'subquery'],
            'Items' => ['strategy'=>'subquery'],
            'Items.PropertiesWithAncestors',
        ];

        $this->Articles->clearIndex($cachekey, true);

        $this->Articles
            ->find('collectItems',['snippets'=>['indexes']])
            ->contain($contain)
            ->all();

        $index = Cache::read($cachekey, 'index');
        $this->assertEmpty($index);

        $this->Articles->saveIndex($cachekey);
        $this->Articles->clearIndex($cachekey, false);

        $index = Cache::read($cachekey, 'index');
        $this->assertNotEmpty($index);

        $index = $this->saveComparisonJson($index);
        $this->assertJsonStringEqualsComparison($index);

        $index_prepared = $this->Articles->getIndexes($cachekey);
        $view = new XmlView();
        $index_xml = $view->renderContent($index_prepared);

        $index_xml = $this->saveComparisonXml($index_xml,'.prepared');
        $this->assertXmlEqualsComparison($index_xml,'.prepared');
    }

}
