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

use App\Test\TestCase\AppTestCase;
use App\Utilities\Converters\HistoricDates;

/**
 * Numbers Test Case
 */
class HistoricDatesTest extends AppTestCase
{

    public $testData;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // Ordered list of dates
        $testDataFile = __DIR__ . '/../../../Testdata/HistoricDates/dates_v5.json';
        $json = file_get_contents($testDataFile);
        $this->testData = json_decode($json, true);

        // JSON does not know order, order by order key
        uasort($this->testData, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });
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
     * Test normalization of historic dates
     *
     * @return void
     */
    public function testNormalization()
    {
        $testData = [
            '1520 - 22' => '1520–1522',
            '1520 - 1522' => '1520–1522',
            '12.-13.Jh.' => '12.–13.Jh.',
            '1. Hälfte 14.Jh' => '1.H.14.Jh.',
            '1923-09-17 bis 1923-10-12' => '1923-09-17/1923-10-12'
        ];

        $result = array_map(
            function ($x) {
                $normalized = HistoricDates::normalize($x);
                return preg_replace("/\s+/", "", $normalized);
            },
            array_keys($testData)
        );

        $this->assertEquals(
            array_values($testData),
            array_values($result)
        );
    }

    /**
     * Test that parsing invalid dates returns null
     *
     * @return void
     */
    public function testInvalidDates(): void
    {
        $invalidDates = [
            'abcd'
        ];

        foreach ($invalidDates as $date) {
            $parsed = HistoricDates::minyear($date);
            $this->assertNull($parsed);

            $parsed = HistoricDates::maxyear($date);
            $this->assertNull($parsed);

            $parsed = HistoricDates::years($date);
            $this->assertEmpty($parsed);

            $parsed = HistoricDates::encode($date);
            $this->assertNull($parsed);

            $parsed = HistoricDates::normalize($date);
            $this->assertNull($parsed);
        }
    }

    public function testParseHistoricDateList(): void
    {

        $testData = [
            '1949-05-23T15:13/1952-07-03T17:18 und 1960-07-15, 1965-03-04',
            '513 und 514-516 / 712, vor 1613'
        ];

        $result = array_map([HistoricDates::class, 'parseHistoricDateList'], $testData);
        $this->assertJsonStringEqualsComparison($result);

    }

    /**
     * Test date parsing on the full set of test data
     *
     * @return void
     */
    public function testParsing(): void
    {
        $result = array_map(
            fn($x) => [
                'value' => HistoricDates::normalize($x),
                'sort' => HistoricDates::encode($x),
                'from' => HistoricDates::minyear($x),
                'to' => HistoricDates::maxyear($x)
            ],
            array_keys($this->testData)
        );
        $comparison = array_map(
            fn($x) => [
                'value' => $x['value'],
                'sort' => $x['sort'],
                'from' => $x['from'],
                'to' => $x['to'],
            ],
            array_values($this->testData)
        );

        $this->assertEquals(
            $comparison,
            $result
        );
    }

    /**
     * Test that the date order is correct,
     * based on the date key
     *
     * @return void
     */
    public function testOrder(): void
    {
        $result = array_map(
            fn($x, $no) => HistoricDates::encode($x),
            array_keys($this->testData),
            range(0, count($this->testData) -1)
        );

        // For debugging purposes
        $order = array_combine(
            array_column($this->testData, 'sort'),
            array_values($result)
        );
        asort($order);

        // Assert correct order
        asort($result);
        $this->assertEquals(
            range(0, count($result) -1),
            array_keys($result)
        );
    }
}
