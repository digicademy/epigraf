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

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use DOMDocument;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * BodyContains
 *
 * @internal
 */
class BodyContainsHtmlBody extends Constraint
{
    /**
     * Ignore case
     *
     * @var bool
     */
    protected $ignoreCase;

    /**
     * Current value
     *
     * @var string
     */
    private $value;

    /**
     * Constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response A response instance.
     * @param bool $ignoreCase Ignore case
     */
    public function __construct(string $value, bool $ignoreCase = false)
    {
        $this->value = $value;
    }

    /**
     * Checks assertion
     *
     * @param mixed $other Expected type
     * @return bool
     */
    public function matches($other): bool
    {

        $body_other = $this->extractBodyElement($other);
        $body_response = $this->extractBodyElement($this->value);

        return  mb_strpos($body_other, $body_response) !== false;
    }

    /**
     * Parse HTML, extract body element, serialize back to HTML
     *
     * @param $html
     * @return string
     */
    public static function extractBodyElement($html) {

        if (empty($html)) {
            return '';
        }

        // Workaround: DomDocument doesn't know all HTML tags (nav, footer) and produces warnings
        libxml_use_internal_errors(TRUE);

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

        $body = $doc->getElementsByTagName('body');

        $body = $body->item(0);
        $doc->formatOutput = true;
        $body = $doc->savehtml($body);

        return $body;
    }

    /**
     * Throws an exception for the given compared value and test description.
     *
     * @param mixed             $other             evaluated value or object
     * @param string            $description       Additional information about the test
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
            $recodedOther = $this->extractBodyElement($other);
            $recodedValue = $this->extractBodyElement($this->value);


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
     * Get failure description
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
