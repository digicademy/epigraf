<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Utilities\Converters;

class Numbers
{

    /**
     * Convert a number to different numbering systems
     *
     * @param integer $number
     * @param string $format alphabetic | alphabetic-lower | alphabetic-alpha |
     *                       roman | roman-lower | roman-upper
     *                       greek | greek-lower | greek-upper
     * @return string
     */
    static function numberToString($number, $format)
    {
        if ($format === 'alphabetic') {
            return Numbers::numberToLetters($number, true);
        }
        elseif ($format === 'alphabetic-lower') {
            return Numbers::numberToLetters($number, true);
        }
        elseif ($format === 'alphabetic-upper') {
            return Numbers::numberToLetters($number, false);
        }
        elseif ($format === 'roman') {
            return Numbers::numberToRoman($number, true);
        }
        elseif ($format === 'roman-lower') {
            return Numbers::numberToRoman($number, true);
        }
        elseif ($format === 'roman-upper') {
            return Numbers::numberToRoman($number, false);
        }
        elseif ($format === 'greek') {
            //Note: the start is an uppercase alpha, not an a
            return Numbers::numberToLetters($number, true, 'Α', 25);
        }
        elseif ($format === 'greek-lower') {
            //Note: the start is an uppercase alpha, not an a
            return Numbers::numberToLetters($number, true, 'Α', 25);
        }
        elseif ($format === 'greek-upper') {
            //Note: the start is an uppercase alpha, not an a
            return Numbers::numberToLetters($number, false, 'Α', 25);
        }

        else {
            return (string)$number;
        }
    }

    /**
     * Convert a number to letters from a predefined alphabet
     *
     * @param int $number Input number
     * @param array $alphabet The alphabet, e.g. ['*','x','+'] or range('A', 'Z')
     * @param boolean $lower Lower or upper case?
     * @return string
     */
    static function numberToAlpha($number, $lower = false, $alphabet = [])
    {
        $length = count($alphabet);
        $str = '';
        while ($number > 0) {
            $str = $alphabet[$number % $length] . $str;
            $number = (int)($number / $length);
        }
        return $lower ? strtolower($str) : $str;
    }

    /**
     * Convert a number to letters
     *
     * Example: 5 becomes E, 26 becomes Z, 27 becomes AA
     *
     * @param int $number Input number
     * @param int $start The starting index character, e.g. 65 for 'A'
     * @param int $length The length of the used code points, e.g. 26 for A-Z
     * @param boolean $lower Lower or upper case?
     * @return string
     */
    static function numberToLetters($number, $lower = false, $start = 65, $length = 26)
    {
        $start = is_string($start) ? mb_ord($start) : $start;
        $str = '';
        while ($number > 0) {
            $str = mb_chr((($number - 1) % $length) + $start) . $str;
            $number = (int)(($number - 1) / $length);
        }
        return $lower ? mb_strtolower($str) : $str;
    }

    /**
     * Convert a number to a roman number
     *
     * Example: 5 becomes V
     *
     * @param integer $number
     * @param boolean $lower Lower or upper case?
     * @return string
     */
    static function numberToRoman($number, $lower = false)
    {
        $map = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        );
        $str = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $str .= $roman;
                    break;
                }
            }
        }

        return $lower ? strtolower($str) : $str;
    }

}

