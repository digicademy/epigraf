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

use Cake\Collection\Collection;
use Cake\Collection\CollectionInterface;

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
     * @param string $field
     * @return array
     */
    public static function array_group($items, $field)
    {
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item[$field] ?? ''][] = $item;
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
     * @param array $keep Keep values
     * @return array
     */
    public static function array_remove_empty(array $data, array $keep = [])
    {
        return array_filter(
            $data,
            fn($value, $key) => in_array($key, $keep) || (
                    !is_null($value) && ($value !== '') &&
                    (!is_array($value) || !empty($value))
                ),
            ARRAY_FILTER_USE_BOTH
        );
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
     * Convert a nested array to a flat list
     *
     * Each value in the result can contain the following keys:
     * - key The value name
     * - value The value itself, if it is not an array
     * - level The nesting level
     * - size In case of arrays, the number of elements
     *
     * @param array $array
     * @param int $level
     * @return array
     */
    public static function nestedToList($array, $level = 0)
    {
        $out = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $size = count($value);
                $out[] = ['key' => $key, 'size' => $size, 'level' => $level];
                $out = array_merge($out, Arrays::nestedToList($value, $level + 1));
            }
            else {
                $out[] = ['key' => $key, 'value' => $value, 'level' => $level];
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

}
