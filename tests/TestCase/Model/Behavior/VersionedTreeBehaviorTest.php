<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Model\Behavior;

use App\Test\TestCase\AppTestCase;
use Cake\ORM\Table;

/**
 * App\Model\Behavior\VersionBehavior Test Case
 */
class VersionedTreeBehaviorTest extends AppTestCase
{

    public $dumps = [
        'test_epigraf' => 'test_epigraf_tree.sql'
    ];

	/**
	 * Test subject
	 *
	 * @var Table
	 */
	public $Tree;


	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp(): void
	{
		parent::setUp();
		$this->Tree = $this->fetchTable('Tree');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown(): void
	{
		unset($this->Tree);
		parent::tearDown();
	}


	/**
	 * Test moving node ta a new position
	 *
	 * @return void
	 */
	public function testMoveTo()
	{
		$this->Tree->addBehavior('VersionedTree');

		$this->Tree->moveTo(5,1,2);

        $newTree = $this->Tree->find('all')->select(['id','parent_id','lft','rght'])->toArray();

        $compare = $this->saveComparisonJson($newTree);
        $this->assertJsonStringEqualsComparison($compare);
	}

}
