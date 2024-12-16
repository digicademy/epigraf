<?php
declare(strict_types=1);

namespace Epi\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;

/**
 * App\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends EpiTestCase
{
    /**
     * Test subject
     *
     * @var \Epi\Model\Table\UsersTable
     */
    protected $Users;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
        $this->Users = $this->fetchTable('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Users);

        parent::tearDown();
    }

}
