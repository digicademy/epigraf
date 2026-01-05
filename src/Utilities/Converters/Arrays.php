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

use App\Utilities\Text\TextParser;
use Cake\Collection\Collection;
use Cake\Collection\CollectionInterface;
use Cake\Utility\Hash;

/**
 * Static array functions
 *
 */
class Arrays
{

    /**
     * Group items in an associate array by field
     *
     * @param array $items
     * @param string $field Field to group by
     * @param bool $single Whether to return a single item or an array of items in each group
     * @param array $order An array of field values. The result is ordered by this array.
     * @return array
     */
    public static function array_group($items, $field, $single = false, $order = [])
    {
        $grouped = [];

        foreach ($items as $item) {
            if ($single) {
                $grouped[$item[$field] ?? ''] = $item;
            } else {
                $grouped[$item[$field] ?? ''][] = $item;
            }
        }

        if (!empty($order)) {
            $order = array_flip($order);
            uksort(
                $grouped,
                function ($a, $b) use ($order) {
                    $posA = $order[$a] ?? INF;
                    $posB = $order[$b] ?? INF;
                    return $posA - $posB;
                }
            );
        }

        return $grouped;
    }

    /**
     * Group an array of items by keys with values from the last key on the last level
     * On the last level can be single values or arrays of values.
     *
     * @param array $data
     * @param array|string $keys A single key or an array of keys
     * @param bool $empty Whether empty values are allowed (default false)
     * @return array
     */
    public static function array_group_values(array $data, mixed $keys, bool $empty = false)
    {
        $keys = is_string($keys) ? [$keys] : $keys;
        $keys = array_values($keys);
        $maxLevel = count($keys) - 1;
        $grouped = [];

        foreach ($data as $item) {

            // Skip item if any of the required keys are missing
            if (!$empty && !empty(array_diff_key(array_flip($keys), $item))) {
                continue;
            }

            // Reference to the current level in the grouping array
            $currentLevel = &$grouped;

            // Traverse through the keys, creating nested arrays as needed
            foreach ($keys as $level => $key) {

                // Create intermediate levels
                $levelValue = $item[$key] ?? '';
                if ($level < $maxLevel) {
                    if (!isset($currentLevel[$levelValue])) {
                        $currentLevel[$levelValue] = null;
                    }

                    // Move the reference deeper into the nested array
                    $currentLevel = &$currentLevel[$levelValue];
                }

                // Store the value in the final level
                else {
                    if (is_null($currentLevel)) {
                        $currentLevel = $levelValue;
                    }
                    elseif (!is_array($currentLevel)) {
                        $currentLevel = [$currentLevel, $levelValue];
                    }
                    else {
                        $currentLevel[] = $levelValue;
                    }

                    break;
                }
            }
        }

        return $grouped;
    }

    /**
     * Remove null values from array
     *
     * @param $haystack
     * @return mixed
     */
    public static function array_remove_null($haystack)
    {
        if (is_object($haystack) && method_exists($haystack, 'toArray') && is_callable([$haystack, 'toArray'])) {
            $haystack = $haystack->toArray();
        }

        foreach ($haystack as $key => $value) {
            if (is_object($value) && method_exists($value, 'toArray') && is_callable([$value, 'toArray'])) {
                $value = $value->toArray();
            }

            if (is_array($value)) {
                $haystack[$key] = Arrays::array_remove_null($value);
            }

            if ($haystack[$key] === null) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }

    /**
     * Remove null values, empty strings and empty arrays
     *
     * @param array $data
     * @param array $keep Keys to keep even if they are empty
     * @param array $remove Key to check. If empty, all keys are checked
     * @return array
     */
    public static function array_remove_empty(array $data, array $keep = [], array $remove = [])
    {
        return array_filter(
            $data,
            fn($value, $key) =>
                (
                    in_array($key, $keep) ||
                    (!empty($remove) && !in_array($key, $remove))
                ) ||
                (
                    !is_null($value) && ($value !== '') &&
                    (!is_array($value) || !empty($value))
                ),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Remove null values, empty strings and empty arrays
     *
     * @param array $data
     * @param array $keep Keys to keep even if they are empty
     * @param array $remove Key to check. If empty, all keys are checked
     * @return array
     */
    public static function array_remove_empty_recursive(array $data, array $keep = [], array $remove = [])
    {
        if (is_object($data) && method_exists($data, 'toArray') && is_callable([$data, 'toArray'])) {
            $data = $data->toArray();
        }

        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => &$value) {
//            if (is_object($value) && method_exists($value, 'toArray') && is_callable([$value, 'toArray'])) {
//                $value = $value->toArray();
//            }

            if (in_array($key, $keep)) {
                continue;
            }

            if  (empty($remove) || in_array($key, $remove)) {

                if (!(
                    !is_null($value) && ($value !== '') &&
                    (!is_array($value) || !empty($value))
                )) {
                    unset($data[$key]);
                    continue;
                }
            }

            if (is_array($value)) {
                $data[$key] = Arrays::array_remove_empty_recursive($value, $keep, $remove);
            }

        }
        return $data;
    }

    /**
     * Recursively remove entries from a nested array
     *
     * @param array $haystack A nested array
     * @param array $keys All matching keys will be removed
     * @param boolean $nested All keys will match on all levels of the array by default.
     *                        Alternatively, set nested to true to use dot separated keys,
     *                        numeric keys can be matched by `*`.
     * @return mixed
     */
    public static function array_remove_keys($haystack, $keys, $nested = false)
    {
        if (is_object($haystack) && method_exists($haystack, 'toArray') && is_callable([$haystack, 'toArray'])) {
            $haystack = $haystack->toArray();
        }

        // Get first level of keys
        $keysParsed = array_reduce(
            $keys,
            function ($carry, $key) {
                $parsed = explode('.', $key, 2);
                $carry[$parsed[0]][] = $parsed[1] ?? false;
                $carry[$parsed[0]] = array_filter($carry[$parsed[0]]);
                return $carry;
            },
            []
        );

        foreach ($haystack as $key => $value) {
            // Numeric keys matched by `*`
            if (is_numeric($key) && isset($keysParsed['*']) && empty($keysParsed['*'])) {
                unset($haystack[$key]);
            }

            // Nonnumeric keys
            elseif (isset($keysParsed[$key]) && empty($keysParsed[$key])) {
                unset($haystack[$key]);
            }

            // Nested array or keys
            else {
                if ($nested) {
                    $subKeys = is_numeric($key) ? ($keysParsed['*'] ?? []) : ($keysParsed[$key] ?? []);
                    if (empty($subKeys)) {
                        continue;
                    }
                }
                else {
                    $subKeys = $keys;
                }

                if (is_object($value) && method_exists($value, 'toArray') && is_callable([$value, 'toArray'])) {
                    $value = $value->toArray();
                }

                if (is_array($value)) {
                    $haystack[$key] = Arrays::array_remove_keys($value, $subKeys, $nested);
                }
            }
        }

        return $haystack;
    }

    /**
     * Compare two arrays and return the differences
     *
     * @inspiredby https://www.php.net/manual/en/function.array-diff.php#91756
     *
     * @param $array1
     * @param $array2
     * @return array An array with all keys and values that differ.
     */
    public static function array_recursive_diff($array1, $array2, $keep = 'both')
    {
        $diff = array();

        $keys = array_unique(array_merge(array_keys($array1), array_keys($array2)));
        foreach ($keys as $key) {

            $val1 = $array1[$key] ?? null;
            $val2 = $array2[$key] ?? null;
            if (array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
                if (is_array($val1) && is_array($val2)) {
                    $diff_recursive = self::array_recursive_diff($val1, $val2);
                    if (count($diff_recursive)) {
                        $diff[$key] = $diff_recursive;
                    }
                }
                else {
                    // No type comparison because time stamps come in different formats
                    if ($val1 != $val2) {
                        $diffVal = $keep === 'both' ? [$val1, $val2] : $val2;
                        $diff[$key] = $diffVal;
                    }
                }
            }
            else {
                $diffVal = $keep === 'both' ? [$val1, $val2] : $val2;
                $diff[$key] = $diffVal;
            }
        }
        return $diff;
    }

    /**
     * Makes all array elements the same length.
     *
     * If an item has fewer elements than the longest item, the elements are repeated starting with the first element.
     * If an item is empty, the $emptyValue is used.
     *
     * @param array $data An array of arrays
     * @return array
     */
    public static function array_recycle($data, $emptyValue = '')
    {
        // Find the maximum count of elements across all keys
        $count = 0;
        foreach ($data as $values) {
            $count = max($count, is_array($values) ? count($values) : 1);
        }

        // Fill the values
        $result = [];
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $item = [];
                foreach ($data as $key => $values) {

                    if (is_array($values) && empty($values)) {
                        $item[$key] = $emptyValue;
                    }
                    elseif (is_array($values)) {
                        $item[$key] = $values[$i % count($values)];
                    }
                    else {
                        $item[$key] = $values;
                    }
                }
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Concat values from multiple arrays, key by key.
     *
     * If an item has fewer elements than the longest item, the elements are repeated, starting with the first element.
     * If an item is empty, the $emptyValue is used.
     *
     * @param array $data An array of arrays
     * @return array
     */
    public static function array_concat($data, $emptyValue = '')
    {
        // Find the maximum count of elements across all keys
        $count = 0;
        foreach ($data as &$values) {
            if (is_array($values)) {
                // Reset numeric indexes in case the array was filtered before
                $values = array_values($values);
                $count = max($count, count($values));
            }
            else {
                $count = max($count, 1);
            }
        }
        unset($values);

        // Fill the values
        $result = [];
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $item = '';
                foreach ($data as $values) {

                    if (is_array($values) && empty($values)) {
                        $values = $emptyValue;
                    }
                    elseif (is_array($values)) {
                        $values = $values[$i % count($values)] ?? $emptyValue;
                    }

                    // TODO: recycle deeper levels instead of JSON encoding the array
                    $values = is_array($values) ? json_encode($values) : $values;
                    $item .= $values;
                }

                $result[] = $item;
            }
        }

        return $result;
    }

    public static function array_remove_prefix($data, $prefix)
    {
        $result = [];

        foreach ($data as $value) {
            if (str_starts_with($value, $prefix)) {
                $value = substr($value, strlen($prefix));
            }
            $result[] = $value;
        }

        return $result;
    }

    /**
     * Add a prefix to each array value or key
     *
     * @param string[] $data
     * @param string $prefix
     * @param boolean $keys Whether to add the prefix to the keys instead of the values (default false)
     * @return string[]
     */
    public static function array_add_prefix($data, $prefix, $keys = false)
    {
        if ($keys) {
            $newKeys = array_map(fn($key) => $prefix . $key, array_keys($data));
            return array_combine($newKeys, $data);
        }
        else {
            return array_map(fn($value) => $prefix . $value, $data);
        }
    }

    /**
     * Flatten an array
     *
     * @param array $array The array to flatten
     * @param string $prefix The prefix
     * @return array The flattened array
     */
    public static function array_flatten($array, $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $new_key = $prefix . '.' . $key;

            if (is_array($value)) {
                if (Arrays::array_is_numeric($value)) {
                    $result[$new_key] = implode(",", $value);
                }
                else {
                    $result = array_merge($result, self::array_flatten($value, $new_key));
                }
            }
            else {
                $result[$new_key] = $value;
            }
        }

        return $result;
    }

    /**
     * Test whether an array is numeric and contains only primitive elements
     *
     * @param array $array
     * @return bool
     */
    public static function array_is_simple(array $array): bool
    {
        $result = true;
        foreach ($array as $key => $value) {
            if (!is_numeric($key) || is_array($value)) {
                $result = false;
                break;
            }
        }
        return $result;
    }

    /**
     * Check whether an array is numeric
     *
     * @param array $array
     * @return bool
     */
    public static function array_is_numeric($array)
    {
        if (!is_array($array)) {
            return false;
        }

        if (count($array) <= 0) {
            return true;
        }

        return array_unique(array_map("is_int", array_keys($array))) === array(true);
    }

    /**
     * Make array unique if key is numeric; drop value if there is a corresponding string key
     *
     * @param $array
     * @return array
     */
    public static function array_unique_mixed($array): array {
        $result = [];
        $current = null;
        $value = null;
        asort($array);
        $keys = array_keys($array);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $value;
                continue;
            }
            if ($value === $current) {
                continue;
            }
            if (!in_array($value, $keys)) {
                $result[$key] = $value;
                $current = $value;
            }
        }
        if ($value != $current) {
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Transform a flat associative array into a nested structure grouped by category.
     *
     * Expected input format:
     * [
     *     10 => ['name' => 'Foo', 'category' => 'A'],
     *     11 => ['name' => 'Bar', 'category' => 'A'],
     *     12 => ['name' => 'Baz', 'category' => 'B'],
     * ]
     *
     * Output format:
     * [
     *     'A' => [
     *         10 => ['name' => 'Foo', 'category' => 'A'],
     *         11 => ['name' => 'Bar', 'category' => 'A'],
     *     ],
     *     'B' => [
     *         12 => ['name' => 'Baz', 'category' => 'B'],
     *     ]
     * ]
     *
     * @param array $data Input key/value array where each value contains 'category'
     * @return array Nested array grouped by category
     */
    public static function array_nest($data, $categoryKey = 'category')
    {
        $nested = [];

        foreach ($data as $no => $option) {
            if (!isset($option[$categoryKey])) {
                continue;
            }

            $category = $option[$categoryKey];
            $nested[$category][$no] = $option;
        }

        return $nested;
    }

    /**
     * Convert a nested array to a flat list
     *
     * Each value in the result can contain the following keys:
     * - key The value name
     * - value The value itself, if it is not an array
     * - level The nesting level
     * - size In case of arrays, the number of elements
     * - id Item ID, if missing in the source data, a UUID is generated
     * - parent_id The parent ID or null
     *
     * @param array $array
     * @param int $level
     * @param mixed $parentId The parent ID of list items or null
     * @return array
     */
    public static function nestedToList($array, $level = 0, $parentId = null)
    {
        $out = [];
        foreach ($array as $key => $value) {

            if (is_array($value)) {
                $id = $value['id'] ?? Attributes::uuid('item');
                $size = count($value);
                $out[] = ['key' => $key, 'size' => $size, 'level' => $level, 'id' => $id, 'parent_id'=>$parentId];
                $out = array_merge($out, Arrays::nestedToList($value, $level + 1, $id));
            }
            else {
                $id = Attributes::uuid('item');
                $out[] = ['key' => $key, 'value' => $value, 'level' => $level, 'id' => $id, 'parent_id'=>$parentId];
            }
        }
        return $out;
    }

    /**
     * Render a nested array to HTML
     *
     * The key of each item in the array is used as the HTML tag name.
     * Numeric keys can be used to combine elements with the same name.
     * In this case, each item is an array with the tag name as key.
     *
     * Each tag item in the array can contain the following keys:
     * - attrs An array of attributes for the tag
     * - content An array of nested tags or plain text
     * - close Whether to close the tag (default true)
     *
     * See the test cases for examples.
     *
     * @param array $content An array keyed by HTML tags.
     */
    public static function nestedToHtml($content) {
        if (!is_array($content)) {
            return $content;
        }
        $out = '';
        foreach ($content as $key => $value) {
            // this allows to combine elements with plain text or elements of the same type
            // e.g [
            //    0 => ['li' => ...],
            //    1 => ['li' => ...],
            //]
            if (is_numeric($key)) {
                $out .= self::nestedToHtml($value);
                continue;
            }
            $out .= '<' . $key;
            if ($value['attrs'] ?? false) {
                $out .= ' ' . Attributes::toHtml($value['attrs']);
            }
            $out .= '>';
            if ($value['content'] ?? false) {
                $out .= self::nestedToHtml($value['content']);
            }
            if ($value['close'] ?? false) {
                $out .= '</' . $key . '>';
            }
        }
        return $out;
    }

    /**
     * Convert string to array value
     *
     * @param array|string $data
     * @param string $key Array key
     * @param string $default Default value
     * @return array
     */
    public static function stringToArray($data, $key, $default)
    {

        $value = is_string($data) ? $data : ($data[$key] ?? $default);
        $data = is_array($data) ? $data : [];
        $data[$key] = $value;

        return $data;

    }

    /**
     * Make sure the value is an array
     *
     * @param mixed $value The value
     * @param string $key If the value is not an array, it will be wrapped in an array with the given key
     * @return array
     */
    public static function valueToArray($value, $key)
    {
        $value = !is_array($value) ? [$key => $value] : $value;
        return ($value);
    }

    /**
     * Order items along a list of numbers
     *
     * Each items field value is looked up in the along list.
     * Used for sorting footnotes.
     *
     * @param $items
     * @param $field
     * @param $along
     *
     * @return CollectionInterface
     *
     * //todo: unit test
     */
    public static function orderAlong($items, $field, $along)
    {
        if (!is_a($items, 'Collection')) {
            $items = new Collection($items);
        }

        return $items->map(function ($row) use ($field, $along) {
            $row['number'] = $along[$row[$field]] ?? -INF;
            return $row;
        })->sortBy('number', SORT_ASC)->toArray();
    }

    /**
     * Remove the first grouping layer of a grouped array
     *
     * @param array $groups
     * @return array
     */
    public static function ungroup($groups = [])
    {
        $ungrouped = [];
        foreach ($groups as $group) {
            $ungrouped = array_merge($ungrouped, $group);
        }
        return $ungrouped;
    }

    /**
     * Give two arrays with fields indexed by their names,
     * return all fields from the first array that are different from the second.
     *
     * @param array $before
     * @param array $after
     * @return array
     */
    public static function fieldsDiff($before, $after): array
    {
        // Convert to arrays
        $before = (array)$before;
        $after = (array)$after;

        return array_filter(
            $before,
            fn($fieldValue, $fieldName) => !array_key_exists($fieldName,
                    $after) || ($fieldValue !== $after[$fieldName]),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Give two arrays with rows indexed by theirs IDs on the first level,
     * and fields indexed by their names on the second level,
     * return all rows with differences
     *
     * @param array $before
     * @param array $after
     * @return array
     */
    public static function rowsDiff($before, $after): array
    {
        // Convert to arrays
        $before = (array)$before;
        $after = (array)$after;


        return array_combine(
            array_keys($before),
            array_map(
                fn(string $id) => array_filter(
                    Arrays::fieldsDiff($before[$id], $after[$id] ?? []),
                    fn($row) => !empty($row)
                ),
                array_keys($before)
            )
        );
    }

    /**
     * Apply a function on a field in every table row
     *
     * @param array $rows
     * @param string $fieldName
     * @param $callback
     * @return array
     */
    public static function rowsMutate($rows, string $fieldName, $callback)
    {
        $rows = (array)$rows;

        return array_map(
            function ($row) use ($fieldName, $callback) {
                $row = (array)$row;
                if (isset($row[$fieldName])) {
                    $row[$fieldName] = $callback($row[$fieldName]);
                }
                return $row;
            },
            $rows
        );
    }

    /**
     * Give two arrays with table names on the first level
     * rows indexed by theirs IDs on the second level,
     * and fields indexed by their names on the third level,
     * return all tables with differences
     *
     * @param array $before First array
     * @param array $after Second array
     * @return array
     */
    public static function tablesDiff(array $before, array $after): array
    {
        // Convert to arrays
        $before = (array)$before;
        $after = (array)$after;

        return array_combine(
            array_keys($before),
            array_map(
                fn(string $tableName) => array_filter(
                    Arrays::rowsDiff(
                        $before[$tableName] ?? [],
                        $after[$tableName] ?? []
                    ),
                    fn($row) => !empty($row)
                ),
                array_keys($before)
            )
        );
    }

    /**
     * Compare two tables
     *
     * Both arrays should contain table names on the first level
     * and rows on the second level. The rows need an 'id' field
     * on the third level.
     *
     * @param array|object $before The first table
     * @param array|object $after The second table
     * @return array
     */
    public static function tablesCompare($before, $after): array
    {
        // Convert to arrays
        $before = (array)$before;
        $after = (array)$after;

//        $before = json_decode(json_encode($before), true);
//        $after = json_decode(json_encode($after), true);

        // Index by ID
        $tablesBefore = array_map(fn($rows) => array_combine(array_column($rows, 'id'), $rows), $before);
        $tablesAfter = array_map(fn($rows) => array_combine(array_column($rows, 'id'), $rows), $after);

        // Get differences
        $tablesBeforeDiff = Arrays::tablesDiff($tablesBefore, $tablesAfter);
        $tablesAfterDiff = Arrays::tablesDiff($tablesAfter, $tablesBefore);

        // Flatten
        $rowsBeforeDiff = Arrays::tablesFlatten($tablesBeforeDiff, '-before');
        $rowsAfterDiff = Arrays::tablesFlatten($tablesAfterDiff, '-after');

        // Merge
        $rowsDiff = array_merge($rowsBeforeDiff, $rowsAfterDiff);
        ksort($rowsDiff);
        return $rowsDiff;
    }

    /**
     * Flatten a table by prefixing all row keys with the table name
     *
     * @param array $tables An array with table names on the first level
     *                     and rows indexed by theirs IDs on the second level.
     * @param string $suffix
     * @return array
     */
    public static function tablesFlatten(array $tables, string $suffix = ''): array
    {
        return array_reduce(
            array_keys($tables),
            fn($carry, $tableName) => array_merge(
                $carry,
                Arrays::rowsPrepostfix($tables[$tableName], $tableName . '-', $suffix)
            ),
            []
        );
    }

    /**
     * Add a prefix and/or postfix to each array key
     *
     * @param array $array
     * @param string $prefix
     * @param string $postfix
     * @return array
     */
    public static function rowsPrepostfix(array $array, string $prefix = '', string $postfix = ''): array
    {
        return array_combine(
            array_map(
                fn($k) => $prefix . $k . $postfix,
                array_keys($array)
            ),
            $array
        );
    }

    /**
     * Construct query conditions from a search term
     *
     * Supports OR using the pipe |.
     * Supports AND using whitespace as separator.
     * Use quotes to include shitespace in the search term.
     *
     * @param array|string $term A search term or an array of search terms
     * @param array $fields A list of fields already present in the query.
     * @param string $operator One of 'like' or '='.
     * @param string $type  One of 'string' or 'integer'. Makes sure to select the right operator on numbers.
     * @param array $filter Conditions added to each token of the search term, used to select the appropriate full text index
     * @return array
     */
    public static function termConditions($term, $fields, $operator, $type, $filter = [])
    {
        // Recurse array
        if (is_array($term)) {
            $conditions = [];
            foreach ($term as $value) {
                $conditions[] = Arrays::termConditions($value, $fields, $operator, $type, $filter);
            }
            return ['AND' => $conditions];
        }

        $orTerms = array_filter(explode('|', $term));

        $conditions = [
            'OR' => array_map(
                function ($andTerm) use ($fields, $operator, $type, $filter) {
                    $andTerms = TextParser::tokenize($andTerm); // user can use quotes to keep words together
                    $andConditions =  array_map(
                        function ($term) use ($fields, $operator, $type) {
                            $or = array_map(
                                function ($field, $key) use ($term, $operator, $type) {

                                    // Nested operator options
                                    if (!is_numeric($key)) {
                                        $operator = $field['operator'] ?? $operator;
                                        $type = $field['type'] ?? $type;
                                        $field = $key;
                                    }

                                    // Assemble condition
                                    if (($operator === 'LIKE') && ($type === 'string')) {
                                        return [$field . ' LIKE' => "%$term%"];
                                    }
                                    elseif (($operator === '=') && ($type === 'string')) {
                                        return [$field => $term];
                                    }
                                    elseif (($operator === '=') && ($type === 'integer') && (ctype_digit($term))) {
                                        return [$field => $term];
                                    }
                                    else {
                                        return [];
                                    }
                                },
                                $fields, array_keys($fields)
                            );
                            return (['OR' => $or]);
                        },
                        $andTerms
                    );

                    if (!empty($filter)) {
                        $andConditions[] = $filter;
                    }
                    return $andConditions;
                },
                $orTerms
            )
        ];

        return $conditions;
    }

}
