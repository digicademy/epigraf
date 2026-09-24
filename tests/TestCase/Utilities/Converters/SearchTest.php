<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Utilities\Converters;

use App\Utilities\Converters\Search;
use Cake\TestSuite\TestCase;

/**
 * Tests for search term parsing and condition generation
 */
class SearchTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test parse
     *
     * Token structure: ['value' => string, 'op' => string|null]
     * Result structure: OR branches (array) of AND token lists (array)
     *
     * @return void
     */
    public function testParse(): void
    {
        // Single token
        $this->assertEquals(
            [[['value' => 'hello', 'op' => null]]],
            Search::parse('hello')
        );

        // AND: space-separated tokens in one branch
        $this->assertEquals(
            [[
                ['value' => 'hello', 'op' => null],
                ['value' => 'world', 'op' => null],
            ]],
            Search::parse('hello world')
        );

        // OR: pipe creates a second branch
        $this->assertEquals(
            [
                [['value' => 'hello', 'op' => null]],
                [['value' => 'world', 'op' => null]],
            ],
            Search::parse('hello|world')
        );

        // Quoted space — one token, not split as AND
        $this->assertEquals(
            [[['value' => 'hello world', 'op' => null]]],
            Search::parse('"hello world"')
        );

        // Quoted pipe — one token, not split as OR
        $this->assertEquals(
            [[['value' => 'hello|world', 'op' => null]]],
            Search::parse('"hello|world"')
        );

        // Escaped pipe — literal pipe character
        $this->assertEquals(
            [[['value' => 'hello|world', 'op' => null]]],
            Search::parse('hello\|world')
        );

        // Comparison operators: longest match first (>= before >)
        $this->assertEquals(
            [[['value' => 'm', 'op' => '>=']]],
            Search::parse('>=m')
        );

        $this->assertEquals(
            [[['value' => 'm', 'op' => '>']]],
            Search::parse('>m')
        );

        $this->assertEquals(
            [[['value' => 'm', 'op' => '<=']]],
            Search::parse('<=m')
        );

        $this->assertEquals(
            [[['value' => 'm', 'op' => '<']]],
            Search::parse('<m')
        );

        // Whitespace between operator and value — merged into one token
        $this->assertEquals(
            [[['value' => '2025', 'op' => '>=']]],
            Search::parse('>= 2025')
        );

        $this->assertEquals(
            [[['value' => '2025', 'op' => '<=']]],
            Search::parse('<= 2025')
        );

        // Quoted comparison characters — literal, no operator detected
        $this->assertEquals(
            [[['value' => '>=m', 'op' => null]]],
            Search::parse('">=m"')
        );

        $this->assertEquals(
            [[['value' => 'a>b', 'op' => null]]],
            Search::parse('"a>b"')
        );

        // Comparison + normal token in one AND branch
        $this->assertEquals(
            [[
                ['value' => 'm', 'op' => '>='],
                ['value' => 'hello', 'op' => null],
            ]],
            Search::parse('>=m hello')
        );

        // Quoted pipe inside branch + unquoted OR separator
        $this->assertEquals(
            [
                [
                    ['value' => 'a|b', 'op' => null],
                    ['value' => 'c', 'op' => null],
                ],
                [['value' => 'd', 'op' => null]],
            ],
            Search::parse('"a|b" c|d')
        );

        // Operators with spaces across OR branches
        $this->assertEquals(
            [
                [['value' => '2025', 'op' => '>=']],
                [['value' => '2020', 'op' => '<']],
            ],
            Search::parse('>= 2025|< 2020')
        );
    }

    /**
     * Test termConditions with string fields
     *
     * @return void
     */
    public function testTermConditionsString(): void
    {
        $fields = ['Items.name' => ['operator' => 'LIKE', 'type' => 'string']];

        // Simple LIKE search
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.name LIKE' => '%hello%']]]]]],
            Search::termConditions('hello', $fields, 'LIKE', 'string')
        );

        // OR branches — either term matches
        $this->assertEquals(
            ['OR' => [
                [['OR' => [['Items.name LIKE' => '%hello%']]]],
                [['OR' => [['Items.name LIKE' => '%world%']]]],
            ]],
            Search::termConditions('hello|world', $fields, 'LIKE', 'string')
        );

        // AND tokens — all terms must match
        $this->assertEquals(
            ['OR' => [[
                ['OR' => [['Items.name LIKE' => '%hello%']]],
                ['OR' => [['Items.name LIKE' => '%world%']]],
            ]]],
            Search::termConditions('hello world', $fields, 'LIKE', 'string')
        );

        // Alphabetical comparison — greater than or equal
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.name >=' => 'm']]]]]],
            Search::termConditions('>=m', $fields, 'LIKE', 'string')
        );

        // Alphabetical range — browse one letter of the alphabet
        $this->assertEquals(
            ['OR' => [[
                ['OR' => [['Items.name >=' => 'm']]],
                ['OR' => [['Items.name <'  => 'n']]],
            ]]],
            Search::termConditions('>=m <n', $fields, 'LIKE', 'string')
        );

        // Quoted comparison characters — treated as literal LIKE
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.name LIKE' => '%>=m%']]]]]],
            Search::termConditions('">=m"', $fields, 'LIKE', 'string')
        );

        // Array of terms — terms joined with AND
        $this->assertEquals(
            ['AND' => [
                ['OR' => [[['OR' => [['Items.name LIKE' => '%hello%']]]]]],
                ['OR' => [[['OR' => [['Items.name LIKE' => '%world%']]]]]],
            ]],
            Search::termConditions(['hello', 'world'], $fields, 'LIKE', 'string')
        );

        // Filter appended to each AND branch
        $this->assertEquals(
            ['OR' => [[
                ['OR' => [['Items.name LIKE' => '%hello%']]],
                ['Items.deleted' => null],
            ]]],
            Search::termConditions('hello', $fields, 'LIKE', 'string', ['Items.deleted' => null])
        );
    }

    /**
     * Test termConditions with integer fields
     *
     * @return void
     */
    public function testTermConditionsInteger(): void
    {
        $fields = ['Items.count' => ['operator' => '=', 'type' => 'integer']];

        // Exact match
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.count' => 42]]]]]],
            Search::termConditions('42', $fields, '=', 'integer')
        );

        // Non-numeric value — no condition generated
        $this->assertEquals(
            ['OR' => [[['OR' => []]]]],
            Search::termConditions('hello', $fields, '=', 'integer')
        );

        // Comparison operator
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.count >=' => 10]]]]]],
            Search::termConditions('>=10', $fields, '=', 'integer')
        );

        // Comparison with whitespace after operator
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.count >=' => 10]]]]]],
            Search::termConditions('>= 10', $fields, '=', 'integer')
        );

        // Range: >= AND <=
        $this->assertEquals(
            ['OR' => [[
                ['OR' => [['Items.count >=' => 10]]],
                ['OR' => [['Items.count <=' => 100]]],
            ]]],
            Search::termConditions('>=10 <=100', $fields, '=', 'integer')
        );
    }

    /**
     * Test termConditions with timestamp fields
     *
     * Partial dates expand to a half-open interval [start, end).
     * Operators map to the appropriate boundary:
     *
     *   =   whole period:  field >= start AND field < end
     *   >=  start:         field >= start
     *   <   start:         field <  start
     *   >   end:           field >= end
     *   <=  end:           field <  end
     *
     * @return void
     */
    public function testTermConditionsTimestamp(): void
    {
        $fields = ['Items.modified' => ['operator' => '=', 'type' => 'timestamp']];

        // Year — expands to whole year
        $this->assertEquals(
            ['OR' => [[['OR' => [['AND' => [
                'Items.modified >=' => new \DateTime('2025-01-01 00:00:00'),
                'Items.modified <'  => new \DateTime('2026-01-01 00:00:00'),
            ]]]]]]],
            Search::termConditions('2025', $fields, '=', 'timestamp')
        );

        // Month — expands to whole month
        $this->assertEquals(
            ['OR' => [[['OR' => [['AND' => [
                'Items.modified >=' => new \DateTime('2025-06-01 00:00:00'),
                'Items.modified <'  => new \DateTime('2025-07-01 00:00:00'),
            ]]]]]]],
            Search::termConditions('2025-06', $fields, '=', 'timestamp')
        );

        // Day — expands to whole day
        $this->assertEquals(
            ['OR' => [[['OR' => [['AND' => [
                'Items.modified >=' => new \DateTime('2025-06-15 00:00:00'),
                'Items.modified <'  => new \DateTime('2025-06-16 00:00:00'),
            ]]]]]]],
            Search::termConditions('2025-06-15', $fields, '=', 'timestamp')
        );

        // Hour — must be quoted (contains space), expands to whole hour
        $this->assertEquals(
            ['OR' => [[['OR' => [['AND' => [
                'Items.modified >=' => new \DateTime('2025-06-15 10:00:00'),
                'Items.modified <'  => new \DateTime('2025-06-15 11:00:00'),
            ]]]]]]],
            Search::termConditions('"2025-06-15 10"', $fields, '=', 'timestamp')
        );

        // Minute — must be quoted (contains space), expands to whole minute
        $this->assertEquals(
            ['OR' => [[['OR' => [['AND' => [
                'Items.modified >=' => new \DateTime('2025-06-15 10:30:00'),
                'Items.modified <'  => new \DateTime('2025-06-15 10:31:00'),
            ]]]]]]],
            Search::termConditions('"2025-06-15 10:30"', $fields, '=', 'timestamp')
        );

        // >= year — from start of year
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.modified >=' => new \DateTime('2025-01-01 00:00:00')]]]]]],
            Search::termConditions('>=2025', $fields, '=', 'timestamp')
        );

        // > year — after the whole year (start of next year)
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.modified >=' => new \DateTime('2026-01-01 00:00:00')]]]]]],
            Search::termConditions('>2025', $fields, '=', 'timestamp')
        );

        // <= year — up to and including the whole year
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.modified <' => new \DateTime('2026-01-01 00:00:00')]]]]]],
            Search::termConditions('<=2025', $fields, '=', 'timestamp')
        );

        // < year — strictly before the year
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.modified <' => new \DateTime('2025-01-01 00:00:00')]]]]]],
            Search::termConditions('<2025', $fields, '=', 'timestamp')
        );

        // Whitespace between operator and value
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.modified >=' => new \DateTime('2025-01-01 00:00:00')]]]]]],
            Search::termConditions('>= 2025', $fields, '=', 'timestamp')
        );

        // Range: >=2025 <=2025 — start to end of 2025
        $this->assertEquals(
            ['OR' => [[
                ['OR' => [['Items.modified >=' => new \DateTime('2025-01-01 00:00:00')]]],
                ['OR' => [['Items.modified <'  => new \DateTime('2026-01-01 00:00:00')]]],
            ]]],
            Search::termConditions('>=2025 <=2025', $fields, '=', 'timestamp')
        );

        // >= month — from start of month
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.modified >=' => new \DateTime('2025-06-01 00:00:00')]]]]]],
            Search::termConditions('>=2025-06', $fields, '=', 'timestamp')
        );

        // > day — from start of next day
        $this->assertEquals(
            ['OR' => [[['OR' => [['Items.modified >=' => new \DateTime('2025-06-16 00:00:00')]]]]]],
            Search::termConditions('>2025-06-15', $fields, '=', 'timestamp')
        );
    }

}
