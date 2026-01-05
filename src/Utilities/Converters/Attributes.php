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

use Cake\Utility\Inflector;

/**
 * Functions for parsing query parameters, HTML attributes and other data
 * that is used as parameters
 */
class Attributes
{
    public static $seed = null;
    public static $fixed = null;


    /**
     * Convert array to query parameters
     * - comma separated list of numerically indexed arrays
     * - dot notated list of string indexed arrays
     *
     * @param array $params
     * @param array $ignore Fields in $params that should not be contained in the output
     * @return array
     */
    public static function paramsToQueryString($params, $ignore = [])
    {
        $output = [];
        foreach ($params as $field => $value) {

            if (in_array($field, $ignore)) {
                continue;
            }
            // Comma separate a list of strings
            elseif (Arrays::array_is_numeric($value)) {
                $output[$field] = implode(",", $value);
            } // Dot-list an associative array
            elseif (is_array($value)) {
                $output = array_merge($output, Arrays::array_flatten($value, $field));
            }
            else {
                $output[$field] = $value;
            }
        }
        return $output;
    }


    /**
     * Extract all query parameters with the same prefix
     *
     * @param array $params
     * @param string $prefix
     * @param bool $remove Remove the prefix from the key
     * @return array|float[]|int[]|string[]
     */
    public static function extractQueryParams($params, $prefix, $remove = false)
    {
        $values = [];
        foreach ($params as $queryKey => $queryValue) {
            if (str_starts_with($queryKey, $prefix)) {
                $queryKey = $remove ? substr($queryKey, strlen($prefix)) : $queryKey;
                $values[$queryKey] = $queryValue;
            }
        }

        return $values;
    }

    /**
     * Extract prefixed array
     *
     * @param array $params
     * @param string $prefix
     * @return array
     */
    public static function extractPrefixedIntArray($params, $prefix)
    {
        $keys = array_filter(
            array_map(function ($key) use ($prefix) {
                return str_starts_with($key, $prefix) ? substr($key, strlen($prefix)) : null;
            }, array_keys($params))
        );

        $values = array_map(function ($key) use ($params, $prefix) {
            return Attributes::commaListToIntegerArray($params[$prefix . $key] ?? []);
        }, $keys);

        return array_combine($keys, $values);
    }

    public static function extractPrefixedNestedList($params, $prefix)
    {
        // filter by prefix and remove it from keys
        $filtered = [];
        foreach ($params as $key => $value) {
            if (str_starts_with($key, $prefix)) {
                $keySansPrefix = substr($key, strlen($prefix));
                $filtered[$keySansPrefix] = $value;
            }
        }

        // build nested array where '_' in the key introduces a new level
        $nested = [];
        foreach ($filtered as $key => $value) {
            $keys = explode('_', $key);
            $current = &$nested;
            foreach ($keys as $k) {
                if (!isset($current[$k])) {
                    $current[$k] = [];
                }
                $current = &$current[$k];
            }
            $current = Attributes::commaListToValueArray($value);
        }
        return $nested;
    }

    /**
     * Extract prefixed booleans
     *
     * @param array $params
     * @param string $prefix
     * @return array
     */
    public static function extractPrefixedBooleans($params, $prefix)
    {
        $keys = array_filter(
            array_map(function ($key) use ($prefix) {
                return str_starts_with($key, $prefix) ? substr($key, strlen($prefix)) : null;
            }, array_keys($params))
        );

        $values = array_map(function ($key) use ($params, $prefix) {
            return self::isTrue($params[$prefix . $key] ?? false);
        }, $keys);

        return array_combine($keys, $values);
    }


    /**
     * Merge all values from query parameters starting with the same prefix
     *
     * For example, the following query parameters:
     *
     *   ?properties_objecttypes=111,222&properties_wordseparators=333
     *
     * when calling mergeQueryParams with the prefix 'properties' will result in
     *
     *   [111,222,333]
     *
     * @param array $params
     * @param string $prefix
     * @return array
     */
    public static function mergeQueryParams($params, $prefix)
    {
        $values = [];
        foreach ($params as $queryKey => $queryValue) {
            if (str_starts_with($queryKey, $prefix)) {
                $queryValue = Attributes::commaListToStringArray($queryValue);
                $values = array_merge($values, $queryValue);
            }
        }

        return $values;
    }

    /**
     * Unnest query parameters separated by an underscore
     *
     * // TODO: implement recursive function that handles multiple levels
     *
     * @param array $params
     * @param callable $callback Optionally, process the values by a callback.
     * @return array
     */
    public static function unnestQueryParams($params, $callback = null)
    {
        $values = [];
        foreach ($params as $nestedKey => $value) {
            $nestedKey = explode('_', $nestedKey, 2);
            if (count($nestedKey) === 2) {
                if (!is_null($callback)) {
                    $value = $callback($value);
                }
                $values[$nestedKey[0]][$nestedKey[1]] = $value;
            }
        }

        return $values;
    }

    /**
     * Parse query parameters according to the config
     *
     * In the config, provide a method for each parameter
     * (see $parameters attribute of BaseTable):
     * - raw: As is with default value null
     * - list: Comma separated list
     * - string: As is with default value empty string
     * - list-integer: Comma separated list of integers
     * - hybrid-list-integer: First try is to handle the parameter als list-integer (e.g 'properties').
     *                       If the result is an empty string, all values from parameters prefixed with the parameter name
     *                       will be extracted (e.g. from 'properties_fonttypes', 'properties_languages' etc.).
     * - json: JSON encoded string, will be decoded
     * - float: Float value
     * - boolean: Boolean value: 'true' or '1' will be true, 'false' or '0' will be false
     * - merge: Merge all values with the same prefix
     * - nested-boolean: Nested boolean values
     *
     * Nested arrays are supported
     * (see $parameters of PropertiesTable).
     *
     * //TODO: sanitize parameters
     *
     * @param array $params
     * @param array $config The parameter configuration, see the $parameters property of table classes
     * @param null|string $prefix Lookup params with the given prefix (e.g. articles)
     * @param boolean $clean Remove empty values
     * @return array
     */
    public static function parseQueryParams($params, $config, $prefix = null, $clean = true)
    {
        $prefix = $prefix !== null ? ($prefix . '_') : '';
        $parsed = [];
        foreach ($config as $name => $method) {
            if (is_array($method)) {
                $prefixedParams = array_filter(
                    $params,
                    fn($key) => strpos($key, $name . '_') === 0,
                    ARRAY_FILTER_USE_KEY
                );
                // Or should we remove the prefix from the params and call it with a null prefix?
                // This would allow nested configs with deeper levels
                $parsed[$name] = Attributes::parseQueryParams($prefixedParams, $method, $name, $clean);
            }
            elseif ($method === 'list') {
                $value = $params[$name] ?? $params[$prefix . $name] ?? '';
                $parsed[$name] = Attributes::commaListToStringArray($value);
            }
            elseif ($method === 'list-or-false') {
                $value = $params[$name] ?? $params[$prefix . $name] ?? '';
                if (Attributes::isFalse($value)) {
                    $parsed[$name] = false;
                } else {
                    $parsed[$name] = Attributes::commaListToStringArray($value);
                }
            }
            elseif ($method === 'list-integer') {
                $value = $params[$name] ?? $params[$prefix . $name] ?? '';
                $parsed[$name] = Attributes::commaListToIntegerArray($value);
            }
            elseif ($method === 'hybrid-list-integer') {
                $value = $params[$name] ?? $params[$prefix . $name] ?? '';
                if ($value === '') {
                    $value = Attributes::extractPrefixedIntArray($params, $name . '_');
                    if (empty($value)) {
                        $value = Attributes::extractPrefixedIntArray($params, $prefix . $name . '_');
                    }
                }
                else {
                    $value = Attributes::commaListToIntegerArray($value);
                }

                $parsed[$name] = $value;
            }
            elseif ($method === 'hybrid-list-boolean') {
                $value = $params[$name] ?? $params[$prefix . $name] ?? '';
                if ($value === '') {
                    $value = Attributes::extractPrefixedBooleans($params, $name . '_');
                    if (empty($value)) {
                        $value = Attributes::extractPrefixedBooleans($params, $prefix . $name . '_');
                    }
                }
                else {
                    $value = [];
                }

                $parsed[$name] = $value;
            }
            elseif ($method === 'nested-list') {
                $value = $params[$name] ?? $params[$prefix . $name] ?? '';
                if ($value === '') {
                    $value = Attributes::extractPrefixedNestedList($params, $name . '_');
                    if (empty($value)) {
                        $value = Attributes::extractPrefixedNestedList($params, $prefix . $name . '_');
                    }
                }
                else {
                    $value = Attributes::commaListToIntegerArray($value);
                }

                $parsed[$name] = $value;
            }
            elseif ($method === 'nested-boolean') {
                $nestedParams = Attributes::extractQueryParams($params, $name . '_', true);
                $parsed[$name] = Attributes::unnestQueryParams($nestedParams, fn($x) => Attributes::isTrue($x));
            }
            elseif ($method === 'merge') {
                $parsed[$name] = Attributes::mergeQueryParams($params, $name . '_');
            }
            elseif ($method === 'string') {
                $value = $params[$name] ?? $params[$prefix . $name] ?? '';
                $parsed[$name] = $value;
            }
            elseif ($method === 'json') {
                $value = $params[$name] ?? $params[$prefix . $name] ?? '';
                $parsed[$name] = json_decode($value, true);
            }
            elseif ($method === 'float') {
                $value = $params[$name] ?? $params[$prefix . $name] ?? '';
                if ($value !== '') {
                    $parsed[$name] = (float)$value;
                }
            }
            elseif ($method === 'boolean') {
                $value = $params[$name] ?? $params[$prefix . $name] ?? '';
                if ($value !== '') {
                    $parsed[$name] = Attributes::isTrue($value);
                }
            }
            elseif ($method == 'constant-mode') {
                $mode = Attributes::cleanOption(
                    $params[$name] ?? MODE_DEFAULT,
                    [MODE_DEFAULT, MODE_REVISE, MODE_STAGE],
                    MODE_DEFAULT
                );
                if ($mode !== MODE_DEFAULT) {
                    $parsed[$name] = $mode;
                }
            }
            else {
                $parsed[$name] = $params[$name] ?? $params[$prefix . $name] ?? null;
            }
        }

        // Unset save and load for choose and select templates
        $template = $parsed['template'] ?? null;
        if (in_array($template, ['choose', 'select'])) {
            unset($parsed['save']);
            unset($parsed['load']);
        }

        if ($clean) {
            $parsed = Arrays::array_remove_empty($parsed);
        }
        return $parsed;
    }

    /**
     * Convert comma separated list of integer values to array
     *
     * @param $params
     * @return array|float[]|int[]|mixed|string[]
     */
    public static function commaListToIntegerArray($params)
    {
        if ($params === '') {
            return [];
        }
        elseif (is_string($params)) {
            $params = array_map('intval', explode(',', $params));
            $params = Arrays::array_remove_empty($params);
        }
        elseif (is_numeric($params)) {
            $params = [$params];
        }
        return $params;
    }

    /**
     * Convert comma separated list of integer or string values to array
     *
     * @param $params
     * @return array|float[]|int[]|mixed|string[]
     */
    public static function commaListToValueArray($params)
    {
        if ($params === '') {
            return [];
        }
        elseif (is_string($params)) {
            $params = array_map(function($el) {
                    return is_numeric($el) ? (int)$el : (string)$el;
                }, explode(',', $params));
            $params = Arrays::array_remove_empty($params);
        }
        elseif (is_numeric($params)) {
            $params = [$params];
        }
        return $params;
    }

    /**
     * Convert comma separated list of strings to array
     *
     * @param $params
     * @return array|float[]|int[]|mixed|string[]
     */
    public static function commaListToStringArray($params)
    {
        if ($params === null) {
            $params = [];
        }
        elseif (is_string($params)) {
            $params = array_map('trim', explode(',', $params));
            $params = Arrays::array_remove_empty($params);
        }
        elseif (!is_array($params)) {
            $params = [$params];
        }
        return $params;
    }

    /**
     * Convert an array with boolean checked values to an array of keys
     *
     * Example:
     *   ['iri' => 1, 'search' => 0, 'published' => 1]
     * becomes
     *   ['iri', 'published']
     *
     * @param array $value
     * @return string[]
     */
    public static function optionArrayToStringArray($value)
    {
        if (!is_array($value)) {
            return [];
        }

        return array_keys(array_filter($value, fn($x) => !empty($x)));
    }

    /**
     * Combine a comma separated string of sort fields with a comma separated string of directions
     *
     * @param string $sort e.g 'name,created'
     * @param string $direction e.g. 'asc,desc'
     * @param array|null $allowed Allowed sort fields
     * @return array
     */
    public static function combineSortLists($sort, $direction, $allowed = null)
    {
        $sort = Attributes::commaListToStringArray($sort);

        $direction = Attributes::commaListToStringArray($direction);
        $direction = array_intersect($direction, ['asc', 'desc']);
        $direction = array_slice(array_pad($direction, count($sort), 'asc'), 0, count($sort));

        $sort = array_filter(array_combine($sort, $direction));
        if (!is_null($allowed)) {
            $sort = array_filter($sort, fn($key) => in_array($key, $allowed), ARRAY_FILTER_USE_KEY);
        }

        return $sort;
    }

    /**
     * Get key-value-pairs as HTML string to be used in HTML attributes
     *
     * ### Array structure
     * All key-value pairs on the first level will be rendered as <key>="<value>".
     * If a value contains an array, all array values will be imploded using a whitespace separator.
     * The data key value receives special treatment:
     * All array values will be mapped to data-<key>="<value>"; before they are imploded.
     * The style key value receives special treatment:
     * All array values will be mapped to <key>: <value>; before they are imploded.
     * Nullish array values will be filtered out.
     *
     * @param array $array
     * @param boolean $escape Escape values
     * @return string
     */
    public static function toHtml(array $array, $escape = true)
    {
        return implode(' ',
            array_filter(
                array_map(function ($key, $value) use ($escape) {
                    if (is_null($value)) {
                        return null;
                    }
                    if (is_array($value)) {

                        if ($key === 'data') {
                            $dataValues = array_filter($value, fn($x) => $x !== null);
                            $dataAttr = array_map(function ($key, $value) use ($escape) {
                                if ($escape) {
                                    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                                }
                                return 'data-' . $key . '="' . $value . '"';
                            }, array_keys($dataValues), $dataValues);
                            // return from within the nested if-branch!
                            return implode(' ', $dataAttr);
                        }

                        if ($key === 'style') {
                            $value = Attributes::toStyles($value);
                        }

                        $value = implode(' ', array_filter($value, fn($x) => $x !== null));
                    }

                    if ($escape) {
                        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                    }

                    return $key . '="' . $value . '"';
                },
                    array_keys($array), $array
                )
            )
        );
    }

    /**
     * Convert a nested array of styles to style strings
     *
     * @param array $value
     * @param boolean $concat Implode the styles
     * @return string[]|string
     */
    public static function toStyles(array $value, $concat = false)
    {
        $value = array_map(
            fn($styleKey, $styleValue) => $styleKey . ': ' . $styleValue . ';',
            array_keys($value), $value
        );

        if ($concat) {
            $value = implode(' ', $value);
        }

        return $value;
    }

    /**
     * Create a class string
     *
     * @param array $classes
     * @return string
     */
    public static function toClasses(array $classes)
    {
        return implode(' ', array_filter($classes));
    }

    /**
     * Convert an array to a list string
     *
     * The key on the first level will be used as the prefix,
     * followed by a colon and a space. The value will be added.
     * In case the value is an array, it will be imploded with a space.
     *
     * @param array|string|null $array
     * @param bool $removeEmpty Remove empty values
     * @param string $terminator Separator between the key-value pairs.
     * @return string
     */
    public static function toList($array, $removeEmpty = false, $terminator = '. ')
    {
        if (is_string($array)) {
            return $array;
        }

        else {
            if (is_array($array) && (empty($array))) {
                return '';
            }

            else {
                if (!is_array($array)) {
                    return '';
                }
            }
        }

        return implode(' ', array_filter(array_map(
                function ($key, $value) use ($removeEmpty, $terminator) {
                    if (is_array($value)) {
                        $value = implode(' ', Attributes::toList($value, $removeEmpty, ', '));
                    }

                    if ($removeEmpty && ($value === '')) {
                        return null;
                    }
                    else {
                        if (!is_numeric($key)) {
                            return $key . ': ' . htmlspecialchars($value) . $terminator;
                        }
                        else {
                            return htmlspecialchars($value) . $terminator;
                        }
                    }
                },
                array_keys($array), $array)
        ));
    }

    /**
     * Create a list of data attributes with curly template string
     *
     * @param array $keys Attributes to create (keys in the $data array
     * @param array $data If the data contains a value, it will be used.
     *                    Otherwise a curly template string will be created.
     * @return string[] Array of templated strings
     */
    public static function toTemplateAttributes($keys, $data)
    {
        $values = array_map(
            fn($x) => $data[$x] ?? ('{' . Inflector::variable($x) . '}'),
            $keys
        );

        $keys = array_map(
            fn($x) => 'data-' . str_replace('_', '-', $x),
            $keys
        );

        return array_combine($keys, $values);
    }

    /**
     * Replace placeholders in a string by data in an array
     *
     * TODO: is this the same functionality as in getValuePlaceholder() ?
     *
     * Placeholders are enclosed in curly brackets, e.g. {id}.
     * The corresponding data is taken from the array with the key 'id'.
     *
     * @param string $haystack
     * @param array $data
     */
    public static function replacePlaceholders($haystack, $data)
    {
        // Extract placeholders from $haystack
        preg_match_all('/\{([a-zA-Z0-9._-]+)\}/', $haystack, $matches);

        // Iterate over placholders and replace them in $haystack
        foreach ($matches[1] as $match) {
            $value = implode(',', Objects::extract($data, $match));
            $haystack = str_replace('{' . $match . '}', $value, $haystack);
        }

        return $haystack;
    }

    /**
     * Remove special characters to create an identifier
     *
     * @param string $id
     * @param string $replacer Character used to replace special characters
     * @param bool $lower Whether to convert to lower case
     * @return string
     */
    public static function cleanIdentifier($id, $replacer = '-', $lower = true)
    {
        if ($lower) {
            $id = mb_strtolower($id);
        }

        $replacements = [
            'ü' => 'ue',
            'ä' => 'ae',
            'ö' => 'oe',
            'ß' => 'ss',
            '(' => '-',
            ')' => ''
        ];

        $id = str_replace(array_keys($replacements), array_values($replacements), $id);

        $id = preg_replace('/[^a-zA-Z0-9+_.~-]/', $replacer, $id);
        $id = preg_replace('/' . $replacer . '+/', $replacer, $id);
        $id = preg_replace('/[.]+/', '.', $id);
        return $id;
    }

    /**
     * Remove special characters to create a valid xml tag.
     *
     * @param string $tagname The non-conform element name
     * @param string $default If no clean element name can be created, returns the default value
     * @return string
     */
    public static function cleanTag($tagname, $default = '_')
    {
        $tagnames = explode(':', $tagname);
        if (count($tagnames) > 1) {
            $cleantags = [];
            foreach ($tagnames as $tagname) {
                $cleantags[] = Attributes::cleanTag($tagname, $default);
            }
            $tagname = implode(':', $cleantags);
            return $tagname;

        }
        else {
            $tagname = $tagnames[0] ?? '';
        }

        $tagname = Attributes::cleanIdentifier($tagname, '_', false);
        $tagname = preg_replace('/^[^:a-zA-Z_]+/', '', $tagname);
        $tagname = preg_replace('/^xml/', '', $tagname);
        return $tagname !== '' ? $tagname : $default;
    }

    /**
     * Remove special characters to create a valid SQL field.
     *
     * @param string $fieldname The non-conform field name
     * @param string $default If no clean name can be created, returns the default value
     * @return string
     */
    public static function cleanFieldname($fieldname, $default = '_')
    {
        $fieldname = preg_replace('/[^a-zA-Z_]+/', '_', $fieldname);
        return $fieldname !== '' ? $fieldname : $default;
    }

    /**
     * Check whether a value is in the list and return a default value otherwise
     *
     * @param string $needle
     * @param string[] $haystack
     * @param string $default
     * @return string
     */
    public static function cleanOption($needle, $haystack = [], $default = '')
    {
        return in_array($needle, $haystack) ? $needle : $default;
    }

    /**
     * Check whether a value is not empty and return a default value otherwise
     *
     * @param string $value
     * @param string $default
     * @return string
     */
    public static function nonEmptyOption($value, $default = '')
    {
        return empty($value) ? $default : $value;
    }

    /**
     * Check whether a value is true or false.
     * If it is not in the list, return the default value.
     *
     * @param string $needle
     * @param string[] $haystack
     * @param string $value
     * @param bool $default
     * @return bool
     */
    public static function isOption($needle, $value, $haystack = [], bool $default = false)
    {
        if (isset($haystack[$needle])) {
            return $haystack[$needle] === $value;
        }
        return $default;
    }

    /**
     * Create a caption from a name or an identifier
     *
     * @param $name
     * @return string
     */
    public static function cleanCaption($name)
    {
        return Inflector::humanize($name);
    }

    /**
     * Concat and trim text
     *
     * @param string $old The current text.
     * @param string $add Text to be added.
     * @param string $sep The separator.
     * @return string
     */
    public static function concatText($old, $add, $sep = ". ")
    {
        $add = trim($add ?? '');
        $old = trim($old ?? '');

        if ($add === '') {
            return $old;
        }
        elseif ($old === '') {
            return $add;
        }
        else {
            return $old . $sep . $add;
        }
    }


    /**
     * Create a fieldname for input elements
     *
     * @param array $fieldNameParts The field parts
     * @param boolean $root Root field names start with the first fielname part, other items start with bracketed fieldnames.
     * @return string
     */
    public static function fieldName($fieldNameParts, $root = true)
    {
        $fieldName = $root ? array_shift($fieldNameParts) : '';
        $fieldName .= implode('', array_map(fn($x) => '[' . $x . ']', $fieldNameParts));

        return $fieldName;
    }

    /**
     * Fix the seed for uuid creation
     *
     * @param int $seed The seed value
     * @return void
     */
    public static function fixSeed(int $seed = 0)
    {
        self::$seed = $seed;
        self::$fixed = $seed;
        mt_srand(self::$seed);
    }

    /**
     * Generate a uuid
     *
     * @param string $prefix Prefix of the generated ID
     * @param string $hyphen Set hypen to '-' to generate a segmented ID
     * @return string
     */
    public static function uuid($prefix = '', $hyphen = false)
    {
        if (self::$fixed !== null) {
            $id = self::$fixed++;
        }
        elseif (self::$seed !== null) {
            $id = mt_rand();
        }
        else {
            $seed = round((double)microtime() * 10000);
            mt_srand($seed);
            $id = uniqid(mt_rand(), true);
        }

        $charid = strtolower(md5($id));
        if ($hyphen) {
            $uuid =
                substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
        }
        else {
            $uuid = $charid;
        }
        return $prefix . $uuid;
    }

    /**
     * Return whether a query parameter value evaluates to true
     *
     * @param string|integer|boolean $value
     * @return bool
     */
    public static function isTrue($value)
    {
        $value = is_array($value) ? reset($value) : $value;
        return ($value === 'true') || ($value === true) || ($value === '1') || ($value === 1);
    }

    /**
     * Return whether a query parameter value evaluates to false
     *
     * @param string|integer|boolean $value
     * @return bool
     */
    public static function isFalse($value)
    {
        $value = is_array($value) ? reset($value) : $value;
        return ($value === 'false') || ($value === false) || ($value === '0') || ($value === 0);
    }

    /**
     * Check whether a value is a valid XML Qname
     *
     * @param string $value
     * @return bool
     */
    public static function isQname($value)
    {
        $pattern = '~
            (?(DEFINE)
                (?<NameStartChar> [:A-Z_a-z\\xC0-\\xD6\\xD8-\\xF6\\xF8-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}])
                (?<NameChar>      (?&NameStartChar) | [.\\-0-9\\xB7\\x{0300}-\\x{036F}\\x{203F}-\\x{2040}])
                (?<Name>          (?&NameStartChar) (?&NameChar)*)
            )
            ^(?&Name)$
        ~ux';

        return preg_match($pattern, $value) === 1;
    }

    /**
     * Check whether a value is a valid Turtle prefixed name
     *
     * A superset of Qnames, see https://www.w3.org/TR/turtle/#sec-iri
     *
     * TODO: support excape sequences
     *
     * @param string $value
     * @return bool
     */
    public static function isNcName($value)
    {
        $pattern = '~
            (?(DEFINE)
                (?<NameStartChar> [:A-Z_a-z0-9\\xC0-\\xD6\\xD8-\\xF6\\xF8-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}])
                (?<NameChar>      (?&NameStartChar) | [:.\\-0-9\\xB7\\x{0300}-\\x{036F}\\x{203F}-\\x{2040}])
                (?<Name>          (?&NameStartChar) (?&NameChar)*)
            )
            ^(?&Name)$
        ~ux';

        return preg_match($pattern, $value) === 1;
    }

    /**
     * Determine whether a value is a literal or an IRI,
     * convert them to relative IRIs if necessary
     * or expand them if necessary.
     *
     * Literals with '^^' are considered to be typed literals.
     * The type is the part after '^^'.
     *
     * TODO: Options are never passed, do we need them?
     *
     * @param string $value
     * @param string $baseIri
     * @param array $namespaces An array with namespace prefixes as keys and namespace URIs as values
     * @param mixed $prefixedNames Whether prefixed names are allowed (true),
     *                             should always be expanded ('expand')
     *                             or should be expanded if they are not NCNames ('ncname')
     * @return array An array with the value and the type. Types:
     *               - iri
     *               - prefixed name
     *               - literal
     *               - The type added to a literal by '^^'
     */
    public static function parseIriValue($value, $baseIri, $namespaces, $prefixedNames = true)
    {
        $value = trim($value);
        $type = $options['_data_type'] ?? 'literal';

        // Because array_merge_recursive() may produce multiple _data_type values
        $type = is_array($type) ? ($type[0] ?? 'literal') : $type;

        // Detect prefixed names
        if ($type === 'literal') {

            foreach ($namespaces as $nspKey => $nspValue) {
                if (str_starts_with($value, $nspKey . ':')) {

                    // Remove prefix for relative IRIs
                    if ($nspValue === $baseIri) {
                        $value = str_replace($nspKey . ":", '', $value);
                        $type = 'iri';
                    }

                    // Expand invalid local parts of a prefixed name
                    else {
                        if ($prefixedNames === 'expand') {
                            $value = str_replace($nspKey . ":", $nspValue, $value);
                            $type = 'iri';
                        }
                        else {
                            if (($prefixedNames === 'ncname') && !Attributes::isNcName($value)) {
                                $value = str_replace($nspKey . ":", $nspValue, $value);
                                $type = 'iri';
                            }
                            else {
                                $type = 'prefixed name';
                            }
                        }
                    }

                    break;
                }
            }
        }

        // Detect types
        if ($type === 'literal') {
            $value = explode('^^',$value, 2);
            $type = $value[1] ?? 'literal';
            $value = $value[0] ?? '';
        }

        return [$value, $type];
    }

    /**
     * Get the prefix of a string
     *
     * @param string $value The input string
     * @param string $separator The character that separates the prefix from the rest of the string
     * @param string $default The default value if no prefix is found
     * @return string
     */
    public static function getPrefix($value, $separator = ':', $default = '') {
        $pos = strpos($value, $separator);
        return ($pos !== false) ? substr($value, 0, $pos) : $default;
    }

}

