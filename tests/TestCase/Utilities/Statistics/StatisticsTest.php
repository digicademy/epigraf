<?php

/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Utilities\Statistics;

use App\Utilities\Statistics\Statistics;
use Cake\TestSuite\TestCase;

/**
 * Statistics Test Case
 */
class StatisticsTest extends TestCase
{
    /**
     * Valid test data
     *
     * @var array
     */
    protected array $validData = [];

    /**
     * Invalid test data
     *
     * @var array
     */
    protected array $invalidData = [];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->validData = [1,4,9,16,25,36,49,64,81,100,100,100];
        $this->invalidData = [];
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset ($this->validData);
        unset ($this->invalidData);
    }

    /**
     * Test range
     *
     * @return void
     */
    public function testRange(): void
    {
        $range = Statistics::range($this->validData);
        self::assertEquals(99, $range);

        $range = Statistics::range($this->invalidData);
        self::assertNull($range);
    }

    /**
     * Test arithmetic mean
     *
     * @return void
     */
    public function testMean(): void
    {
        $mean = Statistics::mean($this->validData);
        self::assertEquals(48.75, $mean);

        $mean = Statistics::mean($this->invalidData);
        self::assertNull($mean);
    }

    /**
     * Test geometric mean
     *
     * @return void
     */
    public function testMeanGeom(): void
    {
        $mean = Statistics::meangeom($this->validData);
        self::assertEquals(26.707, round($mean,3));

        $mean = Statistics::meangeom($this->invalidData);
        self::assertNull($mean);
    }

    /**
     * Test harmonic mean
     *
     * @return void
     */
    public function testMeanHarm(): void
    {
        $mean = Statistics::meanharm($this->validData);
        self::assertEquals(7.644, round ($mean,3));

        $mean = Statistics::meanharm($this->invalidData);
        self::assertNull($mean);
    }

    /**
     * Test modal value
     *
     * @return void
     */
    public function testMode(): void
    {
        $mode = Statistics::mode($this->validData);
        self::assertEquals(100, $mode);

        $mode = Statistics::mode($this->invalidData);
        self::assertNull($mode);
    }

    /**
     * Test (third- and quarter-) quantils
     *
     * @return void
     */
    public function testQuantil(): void
    {
        // $this->validData = [1,4,9, 16,25,36, 49,64,81, 100,100,100]; // sum = 585
        $quantil = Statistics::quantil($this->validData,0.25);
        self::assertEquals(10.75, round($quantil,3));

        $quantil = Statistics::quantil($this->validData,1/3);
        self::assertEquals(19, round($quantil,3));

        $quantil = Statistics::quantil($this->validData,0.5);
        self::assertEquals(42.5, round($quantil,3));

        $quantil = Statistics::quantil($this->validData,2/3);
        self::assertEquals(75.333, round($quantil,3));

        $quantil = Statistics::quantil($this->validData,0.75);
        self::assertEquals(95.25, round($quantil,3));

        $quantil = Statistics::quantil($this->invalidData);
        self::assertNull($quantil);
    }

    /**
     * Test median
     *
     * @return void
     */
    public function testMedian(): void
    {
        $median = Statistics::median($this->validData);
        self::assertEquals(42.5, $median);

        $median = Statistics::median($this->invalidData);
        self::assertNull($median);
    }

    /**
     * Test variance
     *
     * @return void
     */
    public function testPopulationVariance(): void
    {
        $populationVar = Statistics::populationVariance($this->validData);
        self::assertEquals(1401.188, round($populationVar,3));

        $populationVar = Statistics::populationVariance($this->invalidData);
        self::assertNull($populationVar);
    }

    /**
     * Test standard deviation
     *
     * @return void
     */
    public function testPopulationStandardDeviation(): void
    {
        $populationStdDev = Statistics::populationStandardDeviation($this->validData);
        self::assertEquals(37.432, round($populationStdDev,3));

        $populationStdDev = Statistics::populationStandardDeviation($this->invalidData);
        self::assertNull($populationStdDev);
    }

    /**
     * Test sampleVariance method
     *
     * @return void
     */
    public function testSampleVariance(): void
    {
        $sampleVar = Statistics::sampleVariance($this->validData);
        self::assertEquals(1528.568, round($sampleVar,3));

        $sampleVar = Statistics::sampleVariance($this->invalidData);
        self::assertNull($sampleVar);
    }

    /**
     * Test sampleStandardDeviation method
     *
     * @return void
     */
    public function testSampleStandardDeviation(): void
    {
        $sampleStdDev = Statistics::sampleStandardDeviation($this->validData);
        self::assertEquals(39.097, round($sampleStdDev,3));

        $sampleStdDev = Statistics::standardError($this->invalidData);
        self::assertNull($sampleStdDev);
    }

    /**
     * Test standardError method
     *
     * @return void
     */
    public function testStandardError(): void
    {
        $sampleStdErr = Statistics::standardError($this->validData);
        self::assertEquals(11.286, round($sampleStdErr,3));

        $sampleStdErr = Statistics::standardError($this->invalidData);
        self::assertNull($sampleStdErr);
    }

    /**
     * Test zScore method
     *
     * @return void
     */
    public function testZScore(): void
    {
        $zScore = Statistics::zScore(-4);
        self::assertEquals(0, $zScore);

        $zScore = Statistics::zScore(4);
        self::assertEquals(1, $zScore);

        $zScore = Statistics::zScore(1.5);
        self::assertEquals(0.933, round($zScore,3));
    }

}
