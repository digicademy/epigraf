<?php

/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Utilities\Files;

use App\Utilities\Files\Files;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * Numbers Test Case
 */
class FilesTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test range
     *
     * @return void
     */
    public function testClean(): void
    {
        $testData = [
            'AAA' => 'aaa',
            'AAA.BBB CCC' => 'aaa.bbb-ccc',
            'aa  bb' => 'aa-bb',
            'a/b' => 'a-b',
            'a.-.-b' => 'a.b',
            'a.--.b' => 'a.b',
            'a-.-.b' => 'a.b',
            'hellö' => 'helloe',
            'hellä' => 'hellae',
            'hellß' => 'hellss'
        ];

        $result = array_map(
            fn($x) => Files::cleanFilename($x),
            array_keys($testData)
        );

        $this->assertEquals(
          array_values($testData),
            $result
        );
    }

    /**
     * Test creating, filling, clearing and removing a folder
     *
     * @return void
     */
    public function testClear(): void
    {
        // 1. Create folder
        $folder = Configure::read('Data.databases') . 'test_projects' . DS . 'jobs' . DS . 'testfolder' . DS;
        $proceed = Files::createFolder($folder);

        $this->assertEquals(true, $proceed);
        $this->assertDirectoryExists($folder);

        // 2. Fill folder
        $file = $folder . 'testfile.txt';
        $this->assertFileDoesNotExist($file);
        Files::appendToFile( $file, 'TEST CONTENT');
        $this->assertFileExists($file);

        // 3. Clean folder
        Files::clearFolder($folder);
        $this->assertFileDoesNotExist($file);
        $this->assertDirectoryExists($folder);

        // 4. Remove folder
        Files::delete($folder);
        $this->assertDirectoryDoesNotExist($folder);
    }
}
