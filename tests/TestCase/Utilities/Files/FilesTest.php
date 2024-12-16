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

}
