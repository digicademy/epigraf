<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 * Based on CakePHPs Hash-class
 * but allows access to (virtual) properties
 * without toArray conversion
 */

namespace App\Utilities\Converters;

use ArrayAccess;
use Cake\Cache\InvalidArgumentException;
use Cake\ORM\Entity;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Epi\Model\Entity\BaseEntity;

class Objects extends Hash
{

    /**
     * Get a single value specified by $path out of $data.
     * Does not support the full dot notation feature set,
     * but is faster for simple read operations.
     *
     * TODO: By now all object properties can be accessed Objects::get() and Objects::extract().
     *       Implement a restricted version that only allows access to properties
     *       explicitly permitted in the entities.
     *
     * @param \ArrayAccess|array $data Array of data or object implementing
     *   \ArrayAccess interface to operate on.
     * @param array<string>|string|int|null $path The path being searched for. Either a dot
     *   separated string, or an array of path segments.
     * @param mixed $default The return value when the path does not exist
     * @param array $options Not used yet. Planned to use it for configuring virtual property access. Still undecided if it will be needed.
     * @return mixed The value fetched from the array, or null.
     * @throws \InvalidArgumentException
     * @link https://book.cakephp.org/4/en/core-libraries/hash.html#Cake\Utility\Hash::get
     */
    public static function get($data, $path, $default = null, $options = [])
    {
        if (!(is_array($data) || $data instanceof ArrayAccess)) {
            throw new InvalidArgumentException(
                'Invalid data type, must be an array or \ArrayAccess instance.'
            );
        }

        if (empty($data) || $path === null) {
            return $default;
        }

        if (is_string($path) || is_numeric($path)) {
            $parts = explode('.', (string)$path);
        }
        else {
            if (!is_array($path)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid Parameter %s, should be dot separated path or array.',
                    $path
                ));
            }

            $parts = $path;
        }


        foreach ($parts as $key) {
            if ((is_array($data) || $data instanceof ArrayAccess) && isset($data[$key])) {
                $data = $data[$key];
            }
            elseif (is_object($data)) {
                $data = $data->{$key} ?? null;
                if (is_null($data)) {
                    return $default;
                }
            }
            else {
                return $default;
            }
        }

        return $data;
    }

    /**
     * Gets the values from an array matching the $path expression.
     * The path expression is a dot separated expression, that can contain a set
     * of patterns and expressions:
     *
     * - `{n}` Matches any numeric key, or integer.
     * - `{s}` Matches any string key.
     * - `{*}` Matches any value.
     * - `*` Matches any value.
     * - `Foo` Matches any key with the exact same value.
     *
     * There are a number of attribute operators:
     *
     *  - `=`, `!=` Equality.
     *  - `>`, `<`, `>=`, `<=` Value comparison.
     *  - `=/.../` Regular expression pattern match.
     *
     * Given a set of User array data, from a `$usersTable->find('all')` call:
     *
     * - `1.User.name` Get the name of the user at index 1.
     * - `{n}.User.name` Get the name of every user in the set of users.
     * - `{n}.User[id].name` Get the name of every user with an id key.
     * - `{n}.User[id>=2].name` Get the name of every user with an id key greater than or equal to 2.
     * - `{n}.User[username=/^paul/]` Get User elements with username matching `^paul`.
     * - `{n}.User[id=1].name` Get the Users name with id matching `1`.
     *
     * @param array|\ArrayAccess $data The data to extract from.
     * @param string $path The path to extract.
     * @param boolean $toArray Whether to convert result objects to arrays
     * @param array $options Not used
     * @return array|\ArrayAccess An array of the extracted values.
     *                            Returns an empty array if there are no matches.
     * @link https://book.cakephp.org/4/en/core-libraries/hash.html#Cake\Utility\Hash::extract
     */
    public static function extract($data, string $path, $toArray = true, $options = [])
    {

        if (!(is_array($data) || $data instanceof ArrayAccess)) {
            throw new \Cake\Cache\Exception\InvalidArgumentException(
                'Invalid data type, must be an array or \ArrayAccess instance.'
            );
        }

        if (($data instanceof BaseEntity) && !$data->getEntityIsVisible($options)) {
            return null;
        }

        if (empty($path)) {
            return $data;
        }

        // Simple paths.
        if (!preg_match('/[*{\[]/', $path)) {
            $data = static::get($data, $path);
            if ($data !== null && !(is_array($data) || $data instanceof ArrayAccess)) {
                return $toArray ? [$data] : $data;
            }

            if ($toArray && is_object($data) && method_exists($data, 'toArray')) {
                /** @var \Cake\Datasource\EntityInterface $data */
                $data = $data->toArray();
                return $data !== null ? (array)$data : [];
            }
            elseif ($toArray) {
                return $data !== null ? (array)$data : [];
            }
            else {
                return $data;
            }
        }

        // Complex paths
        if (strpos($path, '[') === false) {
            $tokens = explode('.', $path);
        }
        else {
            $tokens = Text::tokenize($path, '.', '[', ']');
        }

        // TODO: $_key is unnecessary, isn't it?
        $_key = '__set_item__';
        $context = [$_key => [$data]];

        foreach ($tokens as $token) {
            $next = [];

            [$token, $conditions] = self::_splitConditions($token);

            foreach ($context[$_key] as $item) {

                /*
                 * @author: Jakob Jünger (the if conditions), the asterisk expansion
                 */
                if (($token === '*') || $token === '') {
                    $token = '{*}';
                }

                if (($item instanceof BaseEntity) && !$item->getEntityIsVisible($options)) {
                    // Skip unpublished entities
                    continue;
                }

                elseif (!str_starts_with($token, '{') && is_object($item)) {
                    // TODO: support $formatFields instead of direct property access
                    $v = $item->{$token} ?? null;
                    if (static::_matches($v, $conditions)) {
                        $next[] = $v;
                    }
                }
                elseif (!str_starts_with($token, '{') && !str_starts_with($token, '*') && is_array($item)) {
                    $v = $item[$token] ?? null;
                    if (static::_matches($v, $conditions)) {
                        $next[] = $v;
                    }
                }
                else {
                    if (is_object($item) && method_exists($item, 'toArray')) {
                        /** @var \Cake\Datasource\EntityInterface $item */
                        $item = $item->toArray();
                    }
                    foreach ((array)$item as $k => $v) {
                        if (static::_matchToken($k, $token) && static::_matches($v, $conditions)) {
                            $next[] = $v;
                        }
                    }
                }
            }

            $context = [$_key => $next];
        }

        return $context[$_key];
    }

    /**
     * Check a key against a token.
     *
     * @param mixed $key The key in the array being searched.
     * @param string $token The token being matched.
     * @return bool
     */
    protected static function _matchToken($key, string $token): bool
    {
        switch ($token) {
            case '{n}':
                return is_numeric($key);
            case '{s}':
                return is_string($key);
            case '{*}':
                return true;
            case '*':
                return true;
            default:
                return is_numeric($token) ? ($key == $token) : $key === $token;
        }
    }

    protected static function _matches($data, string $selector): bool
    {
        // Filter out null values
        if ($data === null) {
            return false;
        }

        // Keep if not filtered
        if (!$selector) {
            return true;
        }

        // Entity property filter
        if (($data instanceof Entity) && (preg_match('/^\[([a-z]+)=([a-z]+)\]$/', $selector, $matches))) {
            return ($data[$matches[1]] ?? null) === $matches[2];
        }

        // Filter for other attributes.
        return (
            (is_array($data) || $data instanceof ArrayAccess) &&
            parent::_matches($data, $selector)
        );
    }

    /**
     * Parse processing steps
     *
     * Processing steps are a list of instructions separated by a pipe character (|).
     * Each instruction consists of a name and optionally options separated by a colon.
     *
     * If the options contain a pipe, the pipe must be escaped by a slash.
     * Slashes are escaped by a slash as well.
     *
     * @param string|array $steps Arrays are returned without modification, strings are parsed
     * @return array An array of processing steps, each step is a string (including options, if provided)
     */
    public static function parseProcessing($steps)
    {
        if (is_array($steps)) {
            return $steps;
        }
        return Strings::tokenize($steps, '|', '\\');
    }

    /**
     * Augment an extraction key to a column configuration array
     *
     * The pattern is `<caption>=<key>|<aggregate>`.
     *
     * Missing values will be replaced by the default configuration and the options if available.
     * The path elements take precedence over the default configuration,
     * which in turn takes precedence over the options.
     *
     * Examples:
     * modifier=modifier.name
     * links=links.*|count
     * title=txt:content
     *
     * @param string $path The extraction key
     * @param array $default An array of default column configurations, keyed by column name
     * @param array $options A single column configuration, used as default and merged into the result
     * @return array The augmented column configuration with the keys
     *               - name: The column name. Everything before the first equal sign or the key itself.
     *               - caption: The column caption. Equals the name if not set in the default configuration or the options.
     *               - key: The extraction key.
     *               - format: Extraction keys can be prefixed with a format key, followed by a colon.
     *                         The format key is handled by the extraction method.
     *                         Examples include 'txt', 'json' or 'html'.
     *               - aggregate: The aggregation function, everything after the first pipe.
     */
    public static function parseFieldKey($path, array $default = [], array $options = [])
    {
        $field = explode('=', $path, 2);

        // No reserved characters in field name allowed
        // Makes sure keys without a name, such as  `items.*[itemtype="geolocations"]`, are parsed correctly
        $reserved = '|={}[]*.';
        if (strpbrk($field[0], $reserved)) {
            $field = [$path];
        }

        $fieldName = $field[0];
        $custom = array_merge($options, $default[$fieldName] ?? []);

        $path = $field[1] ?? $custom['key'] ?? $fieldName;
        $keyParts = explode('|', $path, 2);

        // Split format prefix
        $keyFormat = explode(':', $keyParts[0], 2);
        $key = count($keyFormat) > 1 ? $keyFormat[1] : $keyFormat[0];
        $format = count($keyFormat) > 1 ? $keyFormat[0] : null;

        $custom['name'] = $fieldName;
        $custom['caption'] = $custom['caption'] ?? $fieldName;
        $custom['key'] = $key;
        $custom['format'] = $format;
        $custom['aggregate'] = $keyParts[1] ?? $custom['aggregate'] ?? false;

        return $custom;
    }

    /**
     * Split a path list into an array of paths
     *
     * A path list starts with a [ and ends with a ].
     * Each path is separated by a comma.
     *
     * @param string $path
     * @return string[]
     */
    public static function parsePathList($path)
    {
        if (str_starts_with($path,'[') && str_ends_with($path,']')) {
            $path = substr($path, 1, -1);
            return  Text::tokenize($path, ',', '[', ']');
        }
        return [$path];
    }

    /**
     * Parse placeholder string
     *
     * Placeholders in a string are enclosed in curly brackets, e.g. {id}.
     *
     * Example placeholder strings:
     *
     * - "{project.name}" extracts project.name using getValueFormatted
     * - "{[project.name,project.signature]}" extracts project.name if not empty, else project.signature
     * - "isPartOf" is a literal
     * - "Number {sortno}" inserts sortno using getValueFormatted into the string
     * - "epi:{iri}" inserts iri using getValueFormatted into the string
     * - "\{project\}" escapes the placeholder special characters { and }
     * - "w\\o" escapes the escape character \
     *
     * If no callback is provided, returns an array of tokens.
     * Each token is an array with the following structure:
     * - value: The token value
     * - type: The token type (literal or path)
     *
     * If a callback is provided, the placeholders are replaced by
     * the return value of the callback. The callback receives the
     * token as the first parameter.
     *
     * TODO: Refactor, extract the parser to a separate function in the Strings class.
     *
     * @param string $key
     * @param callable $callback
     * @return array|string An array of strings with placeholders replaced (if callback is provided) or an array of tokens
     */
    public static function parsePlaceholder($key, ?callable $callback = null)
    {
        $resultTokens = [];
        $token = '';
        $tokenType = 'literal';
        $escaped = false;

        $reserved = '{}';
        $escapeChar = '\\';

        $addToken = function ($token, $tokenType) use (&$resultTokens, $callback) {
            if (empty($token)) {
                return;
            }
            elseif ($callback !== null) {
                $value = $tokenType === 'literal' ? $token : $callback($token);
                // Return null if the placeholder string denotes null data (e.g. unpublished data)
                $value = is_array($value) ? Arrays::array_remove_null($value) : $value;
                if (is_null($value) || (is_array($value) && empty($value))) {
                    throw new  \DomainException('Placeholder string contains null data');
                }
                $resultTokens[] = $value;
            }
            else {
                $resultTokens[] = ['value' => $token, 'type' => $tokenType];
            }
        };

        try {

            for ($i = 0; $i < strlen($key); $i++) {
                $char = $key[$i];

                // If the current char is escaped, add it to the current token
                if ($escaped) {
                    $token .= $char;
                    $escaped = false;
                }
                // Set escaped flag (look ahead, only if reserved characters are escaped
                elseif (($char === $escapeChar) && (strpbrk($key[$i+1] ?? '', $reserved))) {
                    $escaped = true;
                }
                elseif (($char === '{') && ($tokenType === 'literal')) {
                    $addToken($token, $tokenType);
                    $token = '';
                    $tokenType = 'path';
                }
                elseif (($char === '}') && ($tokenType === 'path')) {
                    $addToken($token, $tokenType);
                    $token = '';
                    $tokenType = 'literal';
                }
                else {
                    $token .= $char;
                }
            }

            $addToken($token, $tokenType);

            // Return tokens if no callback is provided
            if ($callback === null) {
                return $resultTokens;
            }

        } catch (\DomainException $e) {
            return null;
        }

        // Recycle tokens in case $callback returns multiple values and concat all strings array by array
        $result = Arrays::array_concat($resultTokens);
        return $result;
    }


    /**
     * Process values according to a list of processing instructions
     *
     * The following instructions are supported:
     * - 'first': Return the first element of an array
     * - 'last': Return the last element of an array
     * - 'min': Return the minimum value of an array
     * - 'max': Return the maximum value of an array
     * - 'collapse': Flatten an array and return a comma-separated list of values
     * - 'count': Return the number of elements in an array
     * - 'split': Split string at new lines and return result as an array
     * - 'filter': Return only the elements of an array matching a given pattern
     * - 'strip': Removes a pattern from a string. By default, all HTML tags from a string or an array of string are removed.
     *            Alternatively, provide a regex expression in the instruction options.
     * - 'trim': Trim a string. Be default, whitespace is removed from both ends.
     *           Alternatively, provide a string of characters in the instruction options.
     * - 'ltrunc': Remove a prefix from a string.
     *             The prefix is determined by the field value of the root object
     *             as defined in the step options.
     * - 'json': Extract a json value or a value from a nested array
     * - 'padzero': Pad a number with zeros. The number of digits should be passed as parameter.
     *
     * @param mixed $value Input value
     * @param array $steps A list of processing instructions.
     *                     Each instruction is a string, optionally followed by a colon and options.
     * @param bool $recursive Whether to process nested arrays
     * @param mixed $root The root value of the current processing step
     * @return mixed
     */
    public static function processValues($value, $steps = [], $recursive = true, $root = null)
    {
        foreach ($steps as $step) {
            $step = explode(':', $step, 2);
            $options = $step[1] ?? '';
            $step = $step[0] ?? '';

            if (is_array($value)) {
                if ($step === 'first') {
                    $value = reset($value);
                }
                elseif ($step === 'last') {
                    $value = end($value);
                }
                elseif ($step === 'min') {
                    $value = min($value);
                }
                elseif ($step === 'max') {
                    $value = max($value);
                }
                elseif ($step === 'collapse') {
                    //$value = Hash::flatten($value);

                    foreach ($value as &$item) {
                        if (!is_scalar($item) && !is_null($item)) {
                            $item = json_encode($item);
                        }
                    }
                    unset ($item);
                    $value = implode(', ', array_filter($value, fn($x) => is_numeric($x) || is_bool($x) || !empty($x)));
                }
                elseif ($step === 'count') {
                    $value = count($value);
                }
                elseif($step === 'filter') {
                    $value = array_filter($value, function ($elem) use ($options) {
                        return preg_match("/$options/", $elem);
                    });
                }
                elseif ($recursive) {
                    //$value = array_map(fn($x) => Objects::processValues($x, [$step]), $value);
                    $value = array_reduce(
                        $value,
                        function ($carry, $x) use ($step) {
                            $subValue = Objects::processValues($x, [$step]);
                            if (!is_array($subValue)) {
                                $subValue = [$subValue];
                            }
                            return array_merge($carry, $subValue);
                        },
                        []
                    );
                }
            }

            elseif($step === 'split') {
                $value = preg_split("/\r?\n\r?/", $value ?? '');
            }

            elseif (($step === 'ltrunc') && !empty($root)) {
                $prefixValue = $root->getValueNested($options);
                $value = ltrim($value, $prefixValue);
            }

            elseif ($step === 'trim') {
                if ($options === '') {
                    $options = " \n\r\t\v\0›";
                }
                $value = trim($value, $options);
            }

            elseif ($step === 'strip') {
                if ($options === '') {
                    $options = '<[^>]*>';
                }
                $value = preg_replace('/'.$options.'/', '', $value);
            }

            elseif ($step === 'padzero') {
                $value = str_pad($value, intval($options), '0', STR_PAD_LEFT);
            }
            elseif ($step === 'json') {
                if (is_null($value) || $value === '') {
                    $value = null;
                }
                else if (!is_array($value) && is_string($value)) {
                    $value = json_decode($value, true);
                }

                if (is_array($value)) {
                    $value = Objects::get($value, $options);
                } else {
                    $value = null;
                }
            }
        }

        return $value;
    }

    /**
     * Parse a path expression into components
     *
     * @param string $pathExpression Path expression (e.g., "root/item[@type='special']").
     * @param string $separator The path delimiter, e.g. '.' or '/'.
     * @return array Parsed path components as an array, each item with the keys name, attr and value.
     */
    public static function parsePath($pathExpression, $separator = '.') {
        $segments = Text::tokenize($pathExpression, $separator, '[', ']');

        return array_map(function ($segment) {
            $tag = $segment;
            $attr = null;
            $value = null;

            // Check for attribute conditions (e.g., item[@type='special'])
            if (strpos($segment, '[') !== false) {
                $tag = substr($segment, 0, strpos($segment, '['));
                $condition = substr($segment, strpos($segment, '[') + 1, -1);
                [$attr, $value] = explode('=', str_replace(["'"], '', $condition));
            }

            return ['name' => $tag, 'attr' => $attr, 'value' => $value];
        }, $segments);
    }

}
