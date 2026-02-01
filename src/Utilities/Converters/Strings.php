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
     *  Highlight search terms
     *
     * @param $text
     * @param $terms
     * @param int $buffer
     * @param string $shorten
     * @return string
     */
    public static function highlightTerms($text, $terms, $buffer = 100, $shorten = '<span class="shorten">...</span>')
    {
        if (mb_strlen($text) == 0) {
            return '';
        }
        array_filter($terms);

        // Escape HTML etc.
        $text = h($text);

        //Positionen der Terme
        $marker = array();
        foreach ($terms as $term) {
            if ($term == '') {
                continue;
            }
            $term = mb_strtolower($term);


            $pos = -1;
            while (($pos = mb_strpos(mb_strtolower($text), $term, $pos + 1)) !== false) {
                if (isset($marker[$pos])) {
                    $marker[$pos] = max($marker[$pos], mb_strlen($term));
                }
                else {
                    $marker[$pos] = mb_strlen($term);
                }
            }
        }
        ksort($marker);

        //Grenzen der Snippets
        $ranges = array();
        foreach ($marker as $pos => $len) {
            if (empty($ranges) || ($ranges[count($ranges) - 1]['end'] < ($pos - $buffer))) {
                $ranges[] = array(
                    'start' => max($pos - $buffer, 0),
                    'end' => min($pos + $len + $buffer - 1, mb_strlen($text) - 1),
                    'marker' => array(array('start' => $pos, 'end' => $pos + $len - 1)),
                );
            }
            else {
                $lastrange = array_pop($ranges);
                $lastrange['end'] = min(max($lastrange['end'], $pos + $len + $buffer - 1), mb_strlen($text) - 1);
                $lastmarker = array_pop($lastrange['marker']);
                if ($lastmarker['end'] >= $pos) {
                    $lastmarker['end'] = max($lastmarker['end'], $pos + $len - 1);
                    $lastrange['marker'][] = $lastmarker;
                }
                else {
                    $lastrange['marker'][] = $lastmarker;
                    $lastrange['marker'][] = array('start' => $pos, 'end' => $pos + $len - 1);
                }

                $ranges[] = $lastrange;
            }
        }

        //Extrahieren der Snippets
        $snippets = array();
        if (empty($ranges)) {
            $snippet = substr($text, 0, $buffer);
            if (mb_strlen($snippet) < mb_strlen($text)) {
                $snippet .= $shorten;
            }
            $snippets[] = $snippet;

        }
        else {
            foreach ($ranges as $range) {
                $snippet = ($range['start'] == 0) ? '' : $shorten;
                $snippet .= mb_substr($text, $range['start'], $range['marker'][0]['start'] - $range['start']);
                foreach ($range['marker'] as $key => $marker) {
                    $snippet .= '<mark>' . mb_substr($text, $marker['start'],
                            $marker['end'] - $marker['start'] + 1) . '</mark>';
                    if (!empty($range['marker'][$key + 1])) {
                        $snippet .= mb_substr($text, $marker['end'] + 1,
                            $range['marker'][$key + 1]['start'] - $marker['end'] - 1);
                    }
                }
                $snippet .= mb_substr($text, $range['marker'][count($range['marker']) - 1]['end'] + 1,
                    $range['end'] - ($range['marker'][count($range['marker']) - 1]['end']));
                if ($range['end'] < (mb_strlen($text) - 1)) {
                    $snippet .= $shorten;
                }

                $snippets[] = $snippet;
            }
        }

        return implode('', $snippets);
    }

    /**
     * Strip all HTML tags from a string, but add whitespace after block level elements, and collapse multiple newlines.
     *
     * @param string $html
     * @return string
     */
    public static function stripTagsWithWhitespace($html)
    {
        // Define block-level tags that should be replaced with a newline
        $blockTags = [
            'address',
            'article',
            'aside',
            'blockquote',
            'canvas',
            'dd',
            'div',
            'dl',
            'dt',
            'fieldset',
            'figcaption',
            'figure',
            'footer',
            'form',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'header',
            'hr',
            'li',
            'main',
            'nav',
            'noscript',
            'ol',
            'p',
            'pre',
            'section',
            'table',
            'tfoot',
            'ul',
            'video',
            'td',
            'th'
        ];

        // Add a newline after each closing block tag
        $pattern = '#</(' . implode('|', $blockTags) . ')>#i';
        $html = preg_replace($pattern, "</$1>\n", $html);

        // Strip all HTML tags
        $text = strip_tags($html);

        // Convert HTML entities to their corresponding characters
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Collapse multiple newlines into a single one and trim whitespace
        $text = preg_replace("/\n+/", "\n", $text);
        return trim($text);
    }

    /**
     * Tokenize string with escaping mechanism. Keep together words that are enclosed in quotation marks.
     *
     * @param string $value
     * @param string $separator The separator used to split the string into tokens (default: ` `).
     * @param string $escaper The character used to escape reserved characters (default: `\`).
     * @param string $startGroupChar
     * @param string $endGroupChar
     * @return string[] An array of tokens
     */
    public static function tokenize($value, $separator = ' ', $escaper = '\\', $startGroupChar = '"', $endGroupChar = '"')
    {
        $resultTokens = [];
        $token = '';
        $escaped = false;
        $groupMode = false;
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
            // Set groupmode
            elseif ($char == $endGroupChar && $groupMode) {
                $groupMode = false;
            } elseif ($char == $startGroupChar && !$groupMode) {
                $groupMode = true;
            }
            elseif ($char === $separator && !$groupMode) {
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
