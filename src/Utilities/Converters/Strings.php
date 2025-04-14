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

/**
 * Static string functions
 *
 */
class Strings
{

    /**
     * Replace umlauts with their long form.
     *
     * @param string $value The input string that may contain umlauts.
     * @return string The string with umlauts replaced.
     */
    public static function replaceUmlauts($value)
    {
        return str_replace(
            ['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'],
            ['ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'sz'],
            $value
        );
    }

    /**
     * Remove characters other than small letters (a-z), numbers (0-9),
     * hyphen (-), underscore (_) and tilde (~) from a string.
     *
     * @param string $value The input string that may contain characters to be removed.
     * @return string The string with invalid characters removed.
     */
    public static function removeSpecialCharacters($value)
    {
        return preg_replace('/[^ ~a-z0-9\-_]/', '', $value);
    }

    /**
     * Replace multiple whitespaces with a single whitespace.
     *
     * @param string $value The input string that may contain whitespaces to be collapsed.
     * @return string The string with whitespaces collapsed.
     */
    public static function collapseWhitespace($value)
    {
        return preg_replace('/ +/', ' ', $value);
    }

    /**
     * Prefix all numbers inside a string with a "0".
     *
     * Example:  "no. 23" becomes "no. 023"
     *
     * @param string $value Input string.
     * @param int $width Minimum width of numbers (default: 1).
     * @return string Output string with prefixed numbers.
     */
    public static function prefixNumbersWithZero($value, $width = 1)
    {
        return preg_replace_callback('/\d+/', function ($matches) use ($width) {
            return str_pad($matches[0], $width, '0', STR_PAD_LEFT);
        }, $value);
    }
}
