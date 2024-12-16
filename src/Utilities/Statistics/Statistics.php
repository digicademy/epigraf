<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 * Added methods to https://github.com/app-explorer/php-statistics
 * Original licence: MIT License
 */

/**
 * PHP Statistics Library contains following statistical functions
 * - Sum: The sum of all observations of sample from population or the sum of all elements of population
 * - Mean (µ): The average of all observations of sample or population which tells the common behaviour of the group.
 * - Mode: The most repeated value of the obervations in a sample or population
 * - Median: The central element of the group after arranging the observations in either ascending or descending order. For even numbers the median is the average of two middle numbers.
 * - Sample Variance (s²): An inferential statistics function describes the variablitiy among the sample data. It tells how uniformly the data in the sample set distributed across.
 * - Population Variance (s²): An inferential statistics function describes the variablitiy among the population data. It tells how uniformly the data in the population distributed across.
 * - Sample Standard Deviation (s): Standard Deviation is an inferencial statistics function to estimate how the common characteristics of data deviates from mean or the common behavior of the sample data.
 * - Population Standard Deviation (s): It is an descriptive statistics function to find how the common characteristics of data deviates from the the mean or common behavior of the population data.
 * - Standard Error of Mean and Proportion (SE): Standard Error is an popular inferential statistics function used to estimate how effective the selected sample size influence the results of statistical experiments.
 * - Factorial (n!): A popular mathematical function used in the context of probability & statistics to find the possible outcomes for a sample space.
 * - Permutations (nPr): A probability function which describes how many permutations (nPr) are possible in a sample space where the the order is very important (for example XY & YX are not same and are considered two different events)
 * - Combinations (nCr or n Choose k): A probability function which describes how many combinations (nCr or n Choose k) are possible in a sample space where the the order is not important (for example XY & YX are same events and are considered as a single event)
 * - Z-score to P-value: Z-score is the measure of deviation of dataset in a sample or population which describes how many standard deviation the complete set of sample or population data deviates from its mean. P-value is the measure of probability which estimates the data distribution in the expected region of bell curve.
 *
 * Added methods:
 * - Range: Difference between maximum and minimum
 * - Geometric Mean: nth root of product of (all) n array elements
 * - Harmonic Mean: n divided by sum of (all) n inverse array elements
 * - p-Quantil: Generalized median, adapted from continuous data and applied to distinct data
 */

namespace App\Utilities\Statistics;

class Statistics
{

    /**
     *  test input array
     *
     * @param $data array
     * @return bool
     */
    private static function isValidDataArray($data)
    {
        return ($data != null && count($data) > 0);
    }

    /**
     * calculate sum for the given input numbers
     *
     * @param $data array
     * @return numeric|null
     */
    private static function sum($data)
    {
        if (Statistics::isValidDataArray($data)) {
            /*
            $sum = 0;
            for ($i = 0; $i < count($data); $i++) {
                $sum += $data[$i];
            }
            */
            $sum = array_sum($data);
        }
        else {
            $sum = null;
        }
        return $sum;
    }

    /**
     * calculate sum of inverses for the given input numbers
     *
     * @param $data array
     * @return numeric|null
     */
    private static function suminv($data)
    {
        if (Statistics::isValidDataArray($data) && !in_array(0, $data)) {
            $invdata = [];
            for ($i = 0; $i < count($data); $i++) {
                $invdata[] = 1 / $data[$i];
            }
            $suminv = Statistics::sum($invdata);
        }
        else {
            $suminv = null;
        }
        return $suminv;
    }

    /**
     * calculate product for the given input numbers
     *
     * @param $data array
     * @return numeric|null
     */
    private static function prod($data)
    {
        if (Statistics::isValidDataArray($data)) {
            /*
            $prod = 0;
            for ($i = 0; $i < count($data); $i++) {
                $prod *= $data[$i];
            }
            */
            $prod = array_product($data);
        }
        else {
            $prod = null;
        }
        return $prod;
    }

    /**
     * calculate range for the given input array
     *
     * @param $data array
     * @return float|null
     */
    public static function range($data)
    {
        if (Statistics::isValidDataArray($data)) {
            $range = max($data) - min($data);
        }
        else {
            $range = null;
        }
        return $range;
    }

    /**
     * calculate arithmetic mean for the given input array
     *
     * @param $data array
     * @return float|null
     */
    public static function mean($data)
    {
        if (Statistics::isValidDataArray($data)) {
            $mean = (Statistics::sum($data) / count($data));
        }
        else {
            $mean = null;
        }
        return $mean;
    }

    /**
     * calculate geometric mean for the given input array
     *
     * @param $data array
     * @return float|null
     */
    public static function meangeom($data)
    {
        if (Statistics::isValidDataArray($data)) {
            $meangeom = exp(log(Statistics::prod($data)) / count($data));
        }
        else {
            $meangeom = null;
        }
        return $meangeom;
    }

    /**
     * calculate harmonic mean for the given input array
     *
     * @param $data array
     * @return float|null
     */
    public static function meanharm($data)
    {
        if (Statistics::isValidDataArray($data)) {
            $meanharm = count($data) / Statistics::suminv($data);
        }
        else {
            $meanharm = null;
        }
        return $meanharm;
    }

    /**
     * calculate mode for the given input numbers in array
     *
     * @param $data array
     * @return string|null
     */
    public static function mode($data)
    {
        if (Statistics::isValidDataArray($data)) {
            $mode = '';
            $countArr = [];
            for ($i = 0; $i < count($data); $i++) {
                if (!isset($countArr[$data[$i]])) {
                    $countArr[$data[$i]] = 1;
                }
                else {
                    $countArr[$data[$i]]++;
                }
            }
            $maxs = array_keys($countArr, max($countArr));
            for ($i = 0; $i < count($maxs); $i++) {
                $times = $countArr[$maxs[$i]];
                if ($times > 1) {
                    if ($mode != '') {
                        $mode .= ', ';
                    }
                    $mode .= $maxs[$i];
                }
                else {
                    $mode = '';
                }
            }
        }
        else {
            $mode = null;
        }
        return $mode;
    }

    /**
     * calculate p-quantil for the given input numbers in array
     * quantil p = 0.5 calculates the median
     *
     * @param $data array
     * @return float|null
     */
    public static function quantil($data, $p = 0.5)
    {
        if (Statistics::isValidDataArray($data)) {
            sort($data);
            $spot = count($data) * $p - 0.5;
            if (abs($spot - round($spot)) < 0.001) {
                $quantil = $data[round($spot)];
            }
            else {
                $quantil = (1 - $p) * $data[floor($spot)] + $p * $data[ceil($spot)];
            }
        }
        else {
            $quantil = null;
        }
        return $quantil;
    }

    /**
     * calculate median for the given input numbers in array
     *
     * @param $data array
     * @return float|null
     */
    public static function median($data)
    {
        /*
        if (Statistics::isValidDataArray ($data)) {
            sort($data);
            $middle = count($data) / 2;
            if (count($data) % 2 == 1) {
                $median = $data[$middle];
            }
            else {
                $median = ($data[$middle-1] + $data[$middle]) / 2;
            }
        }
        else {
            $median = null;
        }
        return $median;
        */
        return Statistics::quantil($data, 0.5);
    }

    /**
     * calculate factorial for the given number
     *
     * @param $n int
     * @return int|null
     */
    public static function factorial($n)
    {
        $factorial = 1;
        if ($n == 0 || $n == 1) {
            $factorial = 1;
        }
        elseif ($n > 1) {
            for ($i = 1; $i <= $n; $i++) {
                $factorial = $factorial * $i;
            }
        }
        else {
            $factorial = null;
        }
        return $factorial;
    }

    /**
     * calculate permutation (nPr) for the n and r
     *
     * @param $n int
     * @param $r int
     * @return float|null
     */
    public static function permutation($n, $r)
    {
        if ($r > 0 && $n > $r) {
            $permutation = (Statistics::factorial($n) / Statistics::factorial($n - $r));
        }
        else {
            $permutation = null;
        }
        return $permutation;
    }

    /**
     * calculate permutation (nCr) for the n and r
     *
     * @param $n int
     * @param $r int
     * @return float|null
     */
    public static function combinations($n, $r)
    {
        if ($r > 0 && $n > $r) {
            $npr = Statistics::permutation($n, $r);
            $combinations = ($npr / Statistics::factorial($r));
        }
        else {
            $combinations = null;
        }
        return $combinations;
    }

    /**
     * calculate generic variance
     *
     * @param $data array
     * @param $isSample bool
     * @return float|null
     */
    private static function variance($data, $isSample = true)
    {
        if (Statistics::isValidDataArray($data)) {
            $count = count($data);
            $mean = Statistics::mean($data);
            $x2Mean = 0;
            foreach ($data as $inp) {
                $x2Mean += pow(($inp - $mean), 2);
            }
            $variance = (($isSample) ? $x2Mean / ($count - 1) : $x2Mean / $count);
        }
        else {
            $variance = null;
        }
        return $variance;
    }

    /**
     * find sample variance (s^2)
     *
     * @param $data array
     * @return float|bool
     */
    public static function sampleVariance($data)
    {
        return Statistics::variance($data);
    }

    /**
     * find population variance (s^2)
     *
     * @param $data array
     * @return float|bool
     */
    public static function populationVariance($data)
    {
        return Statistics::variance($data, false);
    }

    /**
     * find sample standard deviation (s)
     *
     * @param $data array
     * @return float|null
     */
    public static function sampleStandardDeviation($data)
    {
        $variance = Statistics::variance($data);
        return ($variance) ? sqrt($variance) : null;
    }

    /**
     * find population standard deviation (s)
     *
     * @param $data array
     * @return float|null
     */
    public static function populationStandardDeviation($data)
    {
        $variance = Statistics::variance($data, false);
        return ($variance) ? sqrt($variance) : null;
    }

    /**
     * find standard error
     *
     * @param $data array
     * @return float|bool
     */
    public static function standardError($data)
    {
        $stdDev = Statistics::sampleStandardDeviation($data);
        return ($stdDev) ? $stdDev / sqrt(count($data)) : $stdDev;
    }

    /**
     * calculate Z Score to P Value (two tailed), where the Z score is ranged from -3.5 to 3.5
     *
     * @param $z float
     * @return float
     */
    public static function zScore($z)
    {
        if ($z < -3.5) {
            $result = 0;
        }
        elseif ($z > 3.5) {
            $result = 1;
        }
        else {
            $factK = 1;
            $sum = 0;
            $term = 1;
            $k = 0;
            $loopStop = exp(-23);
            while (abs($term) > $loopStop) {
                $term = 0.3989422804 * pow(-1, $k) * pow($z, $k)
                    / (2 * $k + 1) / pow(2, $k) * pow($z, $k + 1) / $factK;
                $sum += $term;
                $k++;
                $factK *= $k;
            }
            $sum += 0.5;
            $result = $sum;
        }
        return $result;
    }
}

