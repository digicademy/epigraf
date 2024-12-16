<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */

declare(strict_types=1);

namespace Epi\Test\TestCase\Model\Table;

use Epi\Model\Table\SectionsTable;
use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;

/**
 * App\Model\Table\SectionsTable Test Case
 */
class SectionsTableTest extends EpiTestCase
{
    /**
     * Test subject
     *
     * @var \Epi\Model\Table\SectionsTable
     */
    protected $Sections;

    public $dumps = [
        'test_projects' => 'test_projects.sql'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Sections') ? [] : ['className' => SectionsTable::class];
        $this->Sections = $this->fetchTable('Sections', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Sections);

        parent::tearDown();
    }


	/**
	 * test getScopes method
	 *
	 * @return void
	 */
	public function testGetScopes(): void
	{
		$scopes = $this->Sections->getScopes();
		$compare = $this->saveComparisonJson($scopes);
		$this->assertJsonStringEqualsComparison($compare);
	}

	/**
	 * test setScope method
	 *
	 * @return void
	 */
	public function testSetScope(): void
	{
//     * Warning: the findThreaded method uses the children key for a list
//     * of children. The findTreeList helper internally uses findThreaded.
//     * Therefore, these methods ar not compatible with $this->path method.

		$this->Sections->setScope(1);
		$testData1 = $this->Sections->find('treeList');
		$section1Json = $this->saveComparisonJson($testData1, '.section1');
		$this->assertJsonStringEqualsComparison($section1Json,'.section1');

		$this->Sections->setScope(2);
		$testData2 = $this->Sections->find('treeList');
		$section2Json = $this->saveComparisonJson($testData2, '.section2');
		$this->assertJsonStringEqualsComparison($section2Json, '.section2');
	}


	/**
	 * test removeScope method
	 *
	 * @return void
	 */
	public function testRemoveScope(): void
	{
        // Check behavior
        $result = $this->Sections->hasBehavior('VersionedTree');
        $this->assertEquals(true,$result);

        // Scope 1
        $this->Sections->setScope(1);
        $data = $this->Sections->find('treeList');
        $compare = $this->saveComparisonJson($data,'.scope1');
        $this->assertJsonStringEqualsComparison($compare,'.scope1');

        // Remove scope
        $this->Sections->removeScope();
        $data = $this->Sections->find('treeList');
        $compare = $this->saveComparisonJson($data);
        $this->assertJsonStringEqualsComparison($compare);

        // Scope 3
        $this->Sections->setScope(3);
        $data = $this->Sections->find('treeList');
        $compare = $this->saveComparisonJson($data,'.scope3');
        $this->assertJsonStringEqualsComparison($compare,'.scope3');
	}

}
