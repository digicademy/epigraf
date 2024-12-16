<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @since         3.7.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Test\Utilities\Constraint\Response;

use DOMXPath;
use IvoPetkov\HTML5DOMDocument;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use DOMDocument;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * BodyContains
 *
 * @internal
 */
class HtmlContainsHtmlElement extends Constraint
{
    /**
     * @var bool
     */
    protected $ignoreCase;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $xpath;

    /**
     * Constructor
     *
     * @param string $value
     * @param string $xpath XPath expression for extracting the HTML element
     * @param bool $ignoreCase Ignore case
     */
    public function __construct(string $value, string $xpath, bool $ignoreCase = false)
    {
        $this->xpath = $xpath;
        $this->value = $value;
    }

    /**
     * Check assertion
     *
     * @param mixed $other Expected type
     * @return bool
     */
    public function matches($other): bool
    {
        $element_other = $this->extractHtml5Element($other, $this->xpath);
        $element_response = $this->extractHtml5Element($this->value, $this->xpath);

        return ($element_other !== '') &&
            ($element_response !== '') &&
            ($element_response === $element_other);
            //(mb_strpos($element_other, $element_response) !== false);
    }

    /**
     * Parse HTML, extract body element, serialize back to HTML
     *
     * @param string $html The HTML text
     * @param string $xpath A Xpath expression
     * @return string
     */
    public static function extractHtmlElement($html, $xpath)
    {
        if (empty($html)) {
            return '';
        }

        // Workaround: DomDocument doesn't know all HTML tags (nav, footer) and produces warnings
        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;

        // Workaround: remove whitespace (preserveWhiteSpace does not work)
        //$html = preg_replace('~>\s+<~', '><', $html);
        $html = preg_replace('~>\s+~', '>', $html);
        $html = preg_replace('~\s+<~', '<', $html);

        // Remove comments
        $html = preg_replace('~\<!--[a-zA-Z ]+-->~', '', $html);

        $doc->loadHTML($html);
        libxml_clear_errors();

        $domxpath = new DOMXPath($doc);
        $entries = $domxpath->query($xpath);

        $doc->formatOutput = true;
        $out = '';
        foreach ($entries as $entry) {
            $out .= $doc->savehtml($entry);
        }


        return $out;
    }

    /**
     * Parse HTML, extract body element, serialize back to HTML
     *
     * @param string $html
     * @param string $selector A css selector or a XPath expression
     * @return string
     */
    public static function extractHtml5Element($html, $selector)
    {

        if (empty($html)) {
            return '';
        }

        // XPath
        if (str_starts_with($selector, '/')) {
            return HtmlContainsHtmlElement::extractHtmlElement($html, $selector);
        }

        $doc = new HTML5DOMDocument();

        // Workaround: remove whitespace (preserveWhiteSpace does not work)
        $doc->preserveWhiteSpace = false;
        $html = preg_replace('~>\s+~', '>', $html);
        $html = preg_replace('~\s+<~', '<', $html);

        // Remove comments
        $html = preg_replace('~\<!--[a-zA-Z ]+-->~', '', $html);

        if ($selector !== "") {
            $out = '';

            $doc->loadHTML($html);
            $doc->formatOutput = true;

            $elements = $doc->querySelectorAll($selector);
            foreach ($elements as $element) {
                $out .= $element->outerHTML;
            }
        }
        else {
            $out = $html;
        }

        // Simple pretty print (formatOutput does not work)
        $out = preg_replace('~\</[a-z]+>~', "$0\n", $out);
        $out = preg_replace('~\/>~', "$0\n", $out);

        return $out;
    }

    /**
     * Throws an exception for the given compared value and test description.
     *
     * @param mixed $other evaluated value or object
     * @param string $description Additional information about the test
     * @param ComparisonFailure $comparisonFailure
     *
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @psalm-return never-return
     */
    protected function fail($other, $description, ComparisonFailure $comparisonFailure = null): void
    {
        if ($comparisonFailure === null) {
            $recodedOther = $this->extractHtml5Element($other, $this->xpath);
            $recodedValue = $this->extractHtml5Element($this->value, $this->xpath);


            $comparisonFailure = new ComparisonFailure(
                $this->value,
                $other,
                $recodedValue,
                $recodedOther,
                false,
                'Failed asserting that two HTML values are equal.'
            );
        }

        parent::fail($other, $description, $comparisonFailure);
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most cases.
     * This method should return the second part of that sentence.
     * To provide additional failure information additionalFailureDescription can be used.
     *
     * @param mixed $other evaluated value or object
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function failureDescription($other): string
    {
        return 'two HTML string are equal';
    }

    /**
     * Get default failure description
     *
     * Only implemented because parent class has abstract method toString.
     * Superseded by failureDescription.
     *
     * @return string
     */
    public function toString(): string
    {
        return sprintf(
            'matches HTML string "%s"',
            $this->value
        );
    }

}
