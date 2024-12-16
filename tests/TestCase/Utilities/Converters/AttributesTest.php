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

use App\Utilities\Converters\Attributes;
use App\Utilities\Converters\Numbers;
use Cake\TestSuite\TestCase;

/**
 * Attributes Test Case
 */
class AttributesTest extends TestCase
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
    public function testParseQueryParams(): void
    {
        $testQuery = [
            'strings' => '0,1,2,3',
            'ids' => '0,1,2,3',
            'nothing' => '',
            'emptylist' => '',
            'emptyintegerlist' => '',
            'prefixed_type1' => '1,2',
            'prefixed_type2' => '3,4',
            'prefixed_type3' => '5,6',
            'prefixed-bool_type1' => 'true',
            'prefixed-bool_type2' => 'false',
            'prefixed-bool_type3' => '1',
            'prefixed-bool_type4' => '0',
            'merged_type1' => '1,2',
            'merged_type2' => '3,4',
            'merged_type3' => '5,6',
            'nested_bool_type1' => 'true',
            'nested_bool_type2' => 'false',
            'nested_bool_type3' => '1',
            'nested_bool_type4' => '0'
        ];

        $testConfig = [
            'strings' => 'list',
            'ids' => 'list-integer',
            'prefixed' => 'hybrid-list-integer',
            'prefixed-bool' => 'hybrid-list-boolean',
            'merged' => 'merge',
            'nested' => 'nested-boolean',
            'emptylist' => 'list',
            'emptyintegerlist' => 'list-integer'

        ];

        $testResult = [
            'strings' => ['0','1','2','3'],
            'ids' => [0,1,2,3],
            'prefixed' => ['type1'=>[1,2], 'type2' => [3,4], 'type3' => [5,6]],
            'prefixed-bool' => ['type1'=>true, 'type2'=>false, 'type3'=>true, 'type4'=>false],
            'merged' => ['1','2','3','4','5','6'],
            'nested' => ['bool' => ['type1'=>true, 'type2'=>false, 'type3'=>true, 'type4'=>false]]
        ];

        $this->assertSame($testResult, Attributes::parseQueryParams($testQuery, $testConfig));
    }
}
