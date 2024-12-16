<?php
declare(strict_types=1);

namespace Widgets\Test\TestCase\View\Helper;

use Widgets\View\Helper\TableHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * Widgets\View\Helper\TableHelper Test Case
 */
class TableHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Widgets\View\Helper\TableHelper
     */
    protected $Table;

    /**
     * Setup method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new View();
        $this->Table = new TableHelper($view);
    }

    /**
     * Teardown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Table);

        parent::tearDown();
    }
}
