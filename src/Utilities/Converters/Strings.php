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

use App\Utilities\XmlParser\HtmlHeader;
use Epi\Model\Behavior\PositionBehavior;
use Exception;
use Masterminds\HTML5;
use Masterminds\HTML5\Parser\Scanner;
use Masterminds\HTML5\Parser\Tokenizer;

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

    /**
     * Remove forward slashes
     *
     * @param string $value
     * @return string
     */
    public static function removeSlashes($value)
    {
        $value = str_replace('/', '', $value);
        return $value;
    }

    /**
     * Remove square and round brackets from a string, keep the content inside.
     *
     * @param string $value
     * @return string
     */
    public static function removeBrackets($value)
    {
        // Remove square brackets and keep the content inside
        $value = preg_replace('/\[(.*?)\]/', '$1', $value);
        // Remove round brackets and keep the content inside
        $value = preg_replace('/\((.*?)\)/', '$1', $value);
        return $value;
    }

    public static function processStrings($value, $process = [])
    {
        foreach ($process as $step) {
            if ($step === 'stripBrackets') {
                $value = self::removeBrackets($value);
            }
            elseif ($step === 'stripSlashes') {
                $value = self::removeSlashes($value);
            }
            elseif ($step === 'collapseWhitespace') {
                $value = self::collapseWhitespace($value);
            }
            elseif ($step === 'replaceUmlauts') {
                $value = self::replaceUmlauts($value);
            }
            elseif ($step === 'removeSpecialCharacters') {
                $value = self::removeSpecialCharacters($value);
            }
            elseif ($step === 'prefixNumbersWithZero') {
                $value = self::prefixNumbersWithZero($value);
            }
        }

        return $value;
    }

    /**
     * Tokenize string with escaping mechanism
     *
     * @param string $value
     * @param string $separator The separator used to split the string into tokens (default: `|`).
     * @param string $escaper The character used to escape reserved characters (default: `\`).
     * @return string[] An array of tokens
     */
    public static function tokenize($value, $separator = '|', $escaper = '\\')
    {
        $resultTokens = [];
        $token = '';
        $escaped = false;
        $reserved = $separator . $escaper;

        for ($i = 0; $i < strlen($value); $i++) {
            $char = $value[$i];

            // If the current char is escaped, add it to the current token
            if ($escaped) {
                $token .= $char;
                $escaped = false;
            }
            // Set escaped flag (look ahead, only if reserved characters are escaped)
            elseif (($char === $escaper) && (strpbrk($value[$i+1] ?? '', $reserved))) {
                $escaped = true;
            }
            elseif ($char === $separator) {
                $resultTokens[] = $token;
                $token = '';
            }
            else {
                $token .= $char;
            }
        }

        $resultTokens[] = $token;

        return $resultTokens;
    }

    /**
     * Get TOC from HTML header tags
     *
     * @param string $content
     * @param array $options
     * @return array
     */
    public static function getToc($content, $options = []) {

        try {
            $events = new HtmlHeader(false);
            $scanner = new Scanner($content, !empty($options['encoding']) ? $options['encoding'] : 'UTF-8');
            $parser = new Tokenizer(
                $scanner,
                $events,
                !empty($options['xmlNamespaces']) ? Tokenizer::CONFORMANT_XML : Tokenizer::CONFORMANT_HTML
            );

            $parser->parse();

            $html5 = new HTML5();
            $value = $html5->saveHTML($events->document());
            $value = preg_replace('/^<!DOCTYPE html>\n/', '', $value);
            $value = preg_replace('/^<html>/', '', $value);
            $value = preg_replace('/<\/html>$/', '', $value);

            $toc = $events->toc;

            // Add tree structure
            if (!empty($toc)) {
                $toc = PositionBehavior::addTreePositions($toc);
            }


        } catch (Exception $e) {
            $toc = [];
            $value = $content;
        }

        return ['toc' => $toc, 'html' => $value];
    }
}
