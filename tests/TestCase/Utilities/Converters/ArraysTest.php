<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Utilities\Converters;

use App\Utilities\Converters\Arrays;
use Cake\TestSuite\TestCase;

/**
 * Tests for array manipulation functions
 */
class ArraysTest extends TestCase
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
     * Test parseFieldKey
     *
     * @return void
     */
    public function testGroupTriples(): void
    {

        // Completely different triples
        $triples = [
            ['subject' => 's1', 'predicate' => 'p1', 'object' => 'o1'],
            ['subject' => 's2', 'predicate' => 'p2', 'object' => 'o2'],
            ['subject' => 's3', 'predicate' => 'p3', 'object' => 'o3'],
        ];

        $this->assertEquals(
            [
                's1' => ['p1' => 'o1'],
                's2' => ['p2' => 'o2'],
                's3' => ['p3' => 'o3']

            ],
            Arrays::array_group_values($triples, ['subject', 'predicate', 'object'])
        );

        // Same subject, different predicates and objects
        $triples = [
            ['subject' => 's1', 'predicate' => 'p1', 'object' => 'o1'],
            ['subject' => 's1', 'predicate' => 'p2', 'object' => 'o2'],
            ['subject' => 's1', 'predicate' => 'p3', 'object' => 'o3'],
        ];

        $this->assertEquals(
            [
                's1' => [
                    'p1' => 'o1',
                    'p2' => 'o2',
                    'p3' => 'o3'
                ],
            ],
            Arrays::array_group_values($triples, ['subject', 'predicate', 'object'])
        );

        // Same subject, mixed predicates and same objects
        $triples = [
            ['subject' => 's1', 'predicate' => 'p1', 'object' => 'o1'],
            ['subject' => 's1', 'predicate' => 'p1', 'object' => 'o1'],
            ['subject' => 's1', 'predicate' => 'p3', 'object' => 'o1'],
            ['subject' => 's4', 'predicate' => 'p3', 'object' => 'o1'],
        ];

        $this->assertEquals(
            [
                's1' => [
                    'p1' => ['o1', 'o1'],
                    'p3' => 'o1'
                ],
                's4' => [
                    'p3' => 'o1'
                ]
            ],
            Arrays::array_group_values($triples, ['subject', 'predicate', 'object'])
        );

        // Missing values
        $triples = [
            ['subject' => 's1', 'predicate' => 'p1', 'object' => 'o1'],
            ['subject' => 's1', 'predicate' => 'p1'],
            ['predicate' => 'p1', 'object' => 'o1'],
            ['subject' => 's1', 'object' => 'o1'],
            ['subject' => 's2'],
            ['predicate' => 'p3'],
            ['object' => 'o4'],
        ];

        $this->assertEquals(
            [
                's1' => ['p1' => 'o1']
            ],
            Arrays::array_group_values($triples, ['subject', 'predicate', 'object'])
        );
    }


}
