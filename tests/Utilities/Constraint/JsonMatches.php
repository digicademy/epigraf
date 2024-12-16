<?php declare(strict_types=1);
/*
 * Overwrite the original Class in order to shorten the output
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Test\Utilities\Constraint;


use function json_decode;
use function sprintf;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\Json;
use SebastianBergmann\Comparator\ComparisonFailure;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * Asserts whether or not two JSON objects are equal.
 */
final class JsonMatches extends Constraint
{
    /**
     * @var string
     */
    private $value;

    /**
     * Constructor
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Evaluate the constraint for parameter $other
     *
     * Returns true if the constraint is met, false otherwise.
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param mixed $other value or object to evaluate
     *
     * @return bool
     */
    protected function matches($other): bool
    {
        [$error, $recodedOther] = Json::canonicalize($other);

        if ($error) {
            return false;
        }

        [$error, $recodedValue] = Json::canonicalize($this->value);

        if ($error) {
            return false;
        }

        return $recodedOther == $recodedValue;
    }

    /**
     * Handle comparison result failure
     *
     * Throws an exception for the given compared value and test description.
     *
     * @param mixed             $other             evaluated value or object
     * @param string            $description       Additional information about the test
     * @param ComparisonFailure $comparisonFailure
     *
     * @return void
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @psalm-return never-return
     */
    protected function fail($other, $description, ComparisonFailure $comparisonFailure = null): void
    {
        if ($comparisonFailure === null) {
            [$error, $recodedOther] = Json::canonicalize($other);

            if ($error) {
                parent::fail($other, $description);
            }

            [$error, $recodedValue] = Json::canonicalize($this->value);

            if ($error) {
                parent::fail($other, $description);
            }

            $recodedValue = Json::prettify($recodedValue);
            $recodedOther = Json::prettify($recodedOther);

            $comparisonFailure = new ComparisonFailure(
                json_decode($this->value),
                json_decode($other),
                $recodedValue,
                $recodedOther,
                false,
                'Failed asserting that two json values are equal.'
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
        return 'the two JSON strings are equal';
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
            'matches JSON string "%s"',
            $this->value
        );
    }

}
