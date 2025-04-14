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

use Epi\Model\Table\ProjectsTable;
use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Model\Table\ProjectsTable Test Case
 */
class ProjectsTableTest extends EpiTestCase
{
    /**
     * Test subject
     *
     * @var \Epi\Model\Table\ProjectsTable
     */
    protected $Projects;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Databanks'
	];

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
        $config = TableRegistry::getTableLocator()->exists('Projects') ? [] : ['className' => ProjectsTable::class];
        $this->Projects = $this->fetchTable('Projects', $config);
    }


    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Projects);

        parent::tearDown();
    }


	/**
	 * test getExportCount method
	 *
	 * @return void
	 */
	public function testGetExportCount(): void
	{
		$options = [
			'projects' => 1
		];

		$exportCount = $this->Projects
			->getExportCount($options);

		$compare = $this->saveComparisonJson($exportCount);
		$this->assertJsonStringEqualsComparison($compare);
		$this->assertEquals(1, $compare);
	}


	/**
	 * test getExportData method
	 *
	 * @return void
	 */
	public function testGetExportData(): void
	{
		$options = [
			'projects' => 1
		];
		$project = $this->Projects
			->getExportData($options);

		$this->assertJsonStringEqualsComparison($project);
	}


	/**
	 * test getExportData method
	 *
	 * @return void
	 */
	public function testGetExportDataEmpty(): void
	{
		$options = [];
		$project = $this->Projects
			->getExportData($options);

		$this->assertJsonStringEqualsComparison($project);
	}

    /**
     * test findSelect
     *
     * @return void
     */
    public function testFindSelect(): void
    {
        $projects = $this->Projects->find('select')->toArray();
        $this->assertJsonStringEqualsComparison($projects);
    }


    /**
     * test findSelect
     *
     * @return void
     */
    public function testFindSelectGrouped(): void
    {
        $projects = $this->Projects->find('select',['grouped'=>true])->toArray();
        $this->assertJsonStringEqualsComparison($projects);
    }

    /**
     * Test that empty projects are listed
     *
     * @return void
     */
    public function testFindEmptyProjects(): void
    {
        $entity = $this->Projects->newEntity(['name' => 'Empty Project']);
        $this->Projects->save($entity);

        $params = [];
        $projects = $this->Projects
            ->find('hasParams', $params)
            ->find('containFields', $params);

        $this->assertJsonStringEqualsComparison($projects);
    }

}
