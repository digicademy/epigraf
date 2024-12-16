<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace Epi\Test\TestCase\Model\Table;

use Epi\Model\Table\TypesTable;
use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Model\Table\TypesTable Test Case
 */
class TypesTableTest extends EpiTestCase
{
    /**
     * Test subject
     *
     * @var \Epi\Model\Table\TypesTable
     */
    public $Types;

    /**
     * Fixtures
     *
     * @var array
     */
	public $fixtures = [
		'plugin.Epi.Types'
	];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Types') ? [] : ['className' => TypesTable::class];
        $this->Types = $this->fetchTable('Types', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Types);
        parent::tearDown();
    }


	/**
	 * Test getScopes method
	 *
	 * @return void
	 */
	public function testGetScopes()
	{
		$scopes = $this->Types->getScopes();

		$compare = $this->saveComparisonJson($scopes);
		$this->assertJsonStringEqualsComparison($compare);
	}

}
