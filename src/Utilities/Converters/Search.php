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
 * Parse search strings
 */
class Search
{
    /**
     * Generate query conditions from a search expression
     *
     * Supports OR using the pipe |.
     * Supports AND using whitespace as separator.
     * Supports the comparison operators >, >=, <, <= with optional whitespace after: ">= 2025".
     * Use quotes to include whitespace, pipe or comparison characters in the search term.
     *
     * For timestamp fields, partial dates span the whole period:
     *   '2025'       → whole year
     *   '2025-06'    → whole month
     *   '2025-06-15' → whole day
     *
     * Comparison operators on partial dates use the appropriate boundary:
     *   >=2025  → modified >= 2025-01-01  (start of period)
     *   >2025   → modified >= 2026-01-01  (after the whole period)
     *   <=2025  → modified <  2026-01-01  (up to and including whole period)
     *   <2025   → modified <  2025-01-01  (before the period)
     *
     * @param array|string $term A search term or an array of search terms.
     * @param array $fields A list of fields (must already be present in the query).
     * @param string $operator One of 'LIKE' or '='.
     * @param string $type One of 'string', 'integer', or 'timestamp'. Necessary to select the correct operator on numbers and timestamps.
     * @param array $filter Conditions added to each token of the search term (e.g. used to select the appropriate full text index)
     * @return array
     */
    public static function termConditions($term, $fields, $operator, $type, $filter = [])
    {
        if (is_array($term)) {
            return ['AND' => array_map(
                fn($t) => self::termConditions($t, $fields, $operator, $type, $filter),
                $term
            )];
        }

        return ['OR' => array_map(
            fn($branch) => self::buildBranch($branch, $fields, $operator, $type, $filter),
            self::parse($term)
        )];
    }

    /**
     * Parse input into OR branches of AND token arrays.
     *
     * Token: ['value' => string, 'op' => string|null]
     * Handles: space=AND, |=OR, "..."=quoted, \=escape next char
     * Whitespace between a comparison operator and its value is allowed.
     *
     * @param string $input
     * @return array
     */
    public static function parse(string $input): array
    {
        $branches = [];
        $current  = [];
        $token    = '';
        $escaped  = false;
        $inQuotes = false;
        $quoted   = false;

        $branchSeparator = '|';
        $tokenSeparator  = ' ';
        $escaper         = '\\';
        $quoteStartChar  = '"';
        $quoteEndChar    = '"';
        $reserved        = ' \\|"';

        $flush = function () use (&$current, &$token, &$quoted) {
            if ($token !== '') {
                $tokenArray = ['value' => $token, 'op' => null];
                if (!$quoted) {
                    foreach (['>=', '<=', '>', '<'] as $op) {
                        if (str_starts_with($token, $op)) {
                            $tokenArray = ['value' => substr($token, strlen($op)), 'op' => $op];
                            break;
                        }
                    }
                }
                $current[] = $tokenArray;
                $token  = '';
                $quoted = false;
            }
        };

        for ($i = 0, $len = strlen($input); $i < $len; $i++) {
            $ch = $input[$i];

            if ($escaped) {
                $token   .= $ch;
                $escaped  = false;
            } elseif ($ch === $escaper && strpbrk($input[$i + 1] ?? '', $reserved)) {
                $escaped = true;
            } elseif ($ch === $quoteStartChar) {
                $inQuotes ? ($inQuotes = false) : ($inQuotes = $quoted = true);
            } elseif ($ch === $quoteEndChar) {
                $inQuotes ? ($inQuotes = false) : ($inQuotes = $quoted = true);
            } elseif (!$inQuotes && $ch === $tokenSeparator) {
                $flush();
            } elseif (!$inQuotes && $ch === $branchSeparator) {
                $flush();
                if (!empty($current)) {
                    $branches[] = self::mergeOperatorTokens($current);
                    $current = [];
                }
            } else {
                $token .= $ch;
            }
        }

        $flush();
        if (!empty($current)) $branches[] = self::mergeOperatorTokens($current);

        return $branches;
    }

    /**
     * Merge operator-only tokens with their following value token.
     *
     * Turns [op:'>=' value:''] + [op:null value:'2025']
     * into  [op:'>=' value:'2025']
     *
     * Operator tokens with no following value are discarded.
     *
     * @param array $tokens
     * @return array
     */
    private static function mergeOperatorTokens(array $tokens): array
    {
        $result = [];
        $i = 0;
        while ($i < count($tokens)) {
            $token = $tokens[$i];
            if ($token['op'] !== null && $token['value'] === '') {
                if (isset($tokens[$i + 1])) {
                    $result[] = ['op' => $token['op'], 'value' => $tokens[$i + 1]['value']];
                    $i += 2;
                } else {
                    $i++; // operator with no value — discard
                }
            } else {
                $result[] = $token;
                $i++;
            }
        }
        return $result;
    }

    /**
     * Build conditions from a parsed AND branch
     *
     * @param array $tokens First level contains OR conditions, second the AND conditions
     * @param array $fields
     * @param string $operator
     * @param string $type
     * @param array $filter
     * @return array
     */
    private static function buildBranch(array $tokens, array $fields, string $operator, string $type, array $filter): array
    {
        $conditions = array_map(
            fn($token) => ['OR' => array_values(array_filter(array_map(
                fn($field, $key) => self::fieldCondition($token, $field, $key, $operator, $type),
                $fields, array_keys($fields)
            )))],
            $tokens
        );

        if (!empty($filter)) $conditions[] = $filter;
        return $conditions;
    }

    /**
     * Generate the condition for one field
     *
     * @param array $token
     * @param string $field
     * @param mixed $key
     * @param string $operator
     * @param string $type
     * @return array
     */
    private static function fieldCondition(array $token, $field, $key, string $operator, string $type): array
    {
        if (!is_numeric($key)) {
            $operator = $field['operator'] ?? $operator;
            $type     = $field['type']     ?? $type;
            $field    = $key;
        }

        ['value' => $value, 'op' => $op] = $token;

        if ($op !== null) {
            return match($type) {
                'string'    => [$field . ' ' . $op => $value],
                'integer'   => is_numeric($value) ? [$field . ' ' . $op => (int)$value]   : [],
                'float'     => is_numeric($value) ? [$field . ' ' . $op => (float)$value] : [],
                'timestamp' => self::dateCondition($field, $op, $value),
                default     => [],
            };
        }

        return match(true) {
            $operator === 'LIKE' && $type === 'string'                         => [$field . ' LIKE' => '%' . $value . '%'],
            $operator === '='    && $type === 'string'                         => [$field => $value],
            $operator === '='    && $type === 'integer' && ctype_digit($value) => [$field => (int)$value],
            $operator === '='    && $type === 'timestamp'                      => self::dateCondition($field, '=', $value),
            default                                                             => [],
        };
    }

    /**
     * Construct a date/time condition
     *
     * Thanks to the versatile DateTime class,
     * possible values include:
     *
     * 'yesterday'
     * '-7 days',
     * '-1 hour',
     * '2024-01-15',
     * '2024-01-15 10:00'
     *
     * For partial dates the operator is mapped to the correct period boundary:
     *
     *   =   whole period:  field >= start AND field < end
     *   >=  start:         field >= start
     *   <   start:         field <  start
     *   >   end:           field >= end
     *   <=  end:           field <  end
     *
     * Non-partial values (full datetimes, relative strings) use the operator as-is.
     * Values containing spaces need quoting in the search interface, e.g. "-7 days".
     *
     * @param string $field
     * @param string $op  One of '=', '>', '>=', '<', '<='
     * @param string $value
     * @return array
     */
    private static function dateCondition(string $field, string $op, string $value): array
    {
        try {
            $parsed = self::parseDatePrecision($value);
            if ($parsed === null) return [];

            ['start' => $start, 'end' => $end, 'partial' => $partial] = $parsed;

            if (!$partial) {
                return $op === '=' ? [$field => $start] : [$field . ' ' . $op => $start];
            }

            return match($op) {
                '='  => ['AND' => [$field . ' >=' => $start, $field . ' <' => $end]],
                '>=' => [$field . ' >=' => $start],
                '<'  => [$field . ' <'  => $start],
                '>'  => [$field . ' >=' => $end],
                '<=' => [$field . ' <'  => $end],
                default => [],
            };
        } catch (\Exception) {
            return [];
        }
    }

    /**
     * Parse a date string and return its precision as a half-open interval [start, end).
     *
     * Recognized partial formats:
     *   '2025'       → year  [2025-01-01 00:00:00, 2026-01-01 00:00:00)
     *   '2025-06'    → month [2025-06-01 00:00:00, 2025-07-01 00:00:00)
     *   '2025-06-15' → day   [2025-06-15 00:00:00, 2025-06-16 00:00:00)
     *   '2025-06-15 10' → hour  [2025-06-15 10:00:00, 2025-06-15 11:00:00)
     *   '2025-06-15 10:30' → minute [2025-06-15 10:30:00, 2025-06-15 10:31:00)
     *
     * All other values ('yesterday', '-7 days', '2024-01-15 10:00', ...)
     * are passed directly to DateTime (partial: false).
     *
     * @param string $value
     * @return array{start: \DateTime, end: \DateTime, partial: bool}|null
     */
    private static function parseDatePrecision(string $value): ?array
    {
        if (trim($value) === '') return null;

        // Year: "2025"
        if (preg_match('/^\d{4}$/', $value)) {
            $start = new \DateTime($value . '-01-01 00:00:00');
            $end   = (clone $start)->modify('+1 year');
            return ['start' => $start, 'end' => $end, 'partial' => true];
        }

        // Month: "2025-06"
        if (preg_match('/^\d{4}-\d{2}$/', $value)) {
            $start = new \DateTime($value . '-01 00:00:00');
            $end   = (clone $start)->modify('+1 month');
            return ['start' => $start, 'end' => $end, 'partial' => true];
        }

        // Day: "2025-06-15"
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $start = new \DateTime($value . ' 00:00:00');
            $end   = (clone $start)->modify('+1 day');
            return ['start' => $start, 'end' => $end, 'partial' => true];
        }

        // Hour: "2025-06-15 10"
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}$/', $value)) {
            $start = new \DateTime($value . ':00:00');
            $end   = (clone $start)->modify('+1 hour');
            return ['start' => $start, 'end' => $end, 'partial' => true];
        }

        // Minute: "2025-06-15 10:30"
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $value)) {
            $start = new \DateTime($value . ':00');
            $end   = (clone $start)->modify('+1 minute');
            return ['start' => $start, 'end' => $end, 'partial' => true];
        }

        // Exact datetime or relative expression: "2024-01-15 10:00:00", "yesterday", "-7 days"
        $date = new \DateTime($value);
        return ['start' => $date, 'end' => $date, 'partial' => false];
    }
}
