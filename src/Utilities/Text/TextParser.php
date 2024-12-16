<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Utilities\Text;

class TextParser
{

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
}

