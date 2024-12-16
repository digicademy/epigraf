<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersTable
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
//        'app.Users',
//        'app.Databanks',
//        'app.Pipelines',
//        'app.Permissions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
//        $config = TableRegistry::getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
//        $this->Users = $this->fetchTable('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
//        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test findAuth method
     *
     * @return void
     */
    public function testFindAuth()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test generateAccesstoken method
     *
     * @return void
     */
    public function testGenerateAccesstoken()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test updateActive method
     *
     * @return void
     */
    public function testUpdateActive()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test sqlConnectedUsers method
     *
     * @return void
     */
    public function testSqlConnectedUsers()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
