<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */

declare(strict_types=1);

namespace Epi\Test\TestCase\Model\Analytics;

use Epi\Model\Analytics\Analytics;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Model\Analytics\Analytics Test Case
 */
class AnalyticsTest extends EpiTestCase
{

    public $Analytics = null;

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
        $this->Analytics = new Analytics();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Analytics);
        parent::tearDown();
    }


	/**
	 * test getHealthQuery method
	 *
	 * @return void
	 */
	public function testGetHealthQuery(): void
	{
		$healthQuery = $this->Analytics->getHealthQuery('0353d6b8ac374f0fc5790beeeda32eea');

		$compare = $this->saveComparisonJson($healthQuery);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * test getHealthQuery method (check if id is empty/non-existent)
	 *
	 * @return void
	 */
	public function testGetHealthQueryEmpty(): void
	{
		$healthQuery = $this->Analytics->getHealthQuery('notExistentId');
		$this->assertEquals([], $healthQuery);
	}


	/**
	 * test getHealthQueries method
	 *
	 * @return void
	 */
	public function testGetHealthQueries(): void
	{
		$healthQueries = $this->Analytics->getHealthQueries();
		$compare = $this->saveComparisonJson($healthQueries);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * test getOrphans method
	 *
	 * @return void
	 */
	public function testGetOrphans(): void
	{
		$orphans = $this->Analytics->getOrphans();
		$compare = $this->saveComparisonJson($orphans);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * test findHealthRecords method
	 *
	 * @return void
	 */
	public function testFindHealthRecords(): void
	{
//		Get health query of a type which contains records
//		(here: "Null items to properties by properties id (itemtype=conditions)")
		$healthQuery = $this->Analytics->getHealthQuery('11e18b22c218cca0a0d1d02663effcd1');

//		Find Records
		$records = $this->Analytics->findHealthRecords($healthQuery);
		$compare = $this->saveComparisonJson($records);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * test clearOrphans method
	 *
	 * @return void
	 */
	public function testClearOrphans(): void
	{
		//TODO: hier müsste
		// 1. ein Fixture mit Orphans konstruiert werden
		// 2. Geprüft werden, ob die Orphans nach clearOrphans in der Datenbank verschwunden sind
		$records = $this->Analytics->clearOrphans();
		$compare = $this->saveComparisonJson($records);
		$this->assertJsonStringEqualsComparison($compare);

	}

}
