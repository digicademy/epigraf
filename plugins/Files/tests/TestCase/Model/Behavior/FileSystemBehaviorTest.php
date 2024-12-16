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

namespace Files\Test\TestCase\Model\Behavior;

use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use Files\Model\Behavior\FileSystemBehavior;

/**
 * Files\Model\Behavior\FileSystemBehavior Test Case
 */
class FileSystemBehaviorTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Files\Model\Behavior\FileSystemBehavior
     */
    protected $FileSystem;

    /**
     * Setup method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $table = new Table();
        $this->FileSystem = new FileSystemBehavior($table);
    }

    /**
     * teardown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FileSystem);

        parent::tearDown();
    }

}
