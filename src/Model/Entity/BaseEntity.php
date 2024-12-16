<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity;

use App\Utilities\Converters\Attributes;
use App\Utilities\Converters\Objects;
use App\Utilities\Files\Files;
use ArrayAccess;
use Cake\Datasource\EntityTrait;
use Cake\Datasource\FactoryLocator;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Epi\Model\Table\BaseTable;
use Exception;
use Rest\Entity\LockTrait;

/**
 * Base class for entities
 *
 * All entities should be derived from BaseEntity.
 *
 * # Database fields (some are not available on all derived entities)
 * @property int $id
 * @property int $deleted
 * @property int $published
 *
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $modified_by
 * @property int $created_by
 *
 * @property int $version_id
 * @property int $job_id
 * @property null|string $norm_iri
 *
 * # Virtual fields
 * @property string $tableName
 * @property array $htmlFields
 * @property array $type
 * @property array|null $defaultType
 * @property string $newId
 * @property null|string $currentUserRole
 * @property null|\Cake\ORM\Query $versions
 *
 * @property string $fileBasepath
 * @property string $fileDownloadpath
 * @property string $fileDownloadname
 *
 * @property null|string $iriIdentifier
 * @property null|string $iriFragment
 * @property null|string $iriPath
 * @property null|string $iri
 *
 * @property string $caption
 * @property string $captionPath
 * @property string $captionExt
 *
 * @property null|int $publishedState
 * @property array $publishedOptions
 * @property string $publishedLabel
 *
 * # Relations
 * @property \App\Model\Table\BaseTable $table
 */
class BaseEntity extends Entity
{

    use EntityTrait {
        EntityTrait::set as protected traitSet;
    }

    use LocatorAwareTrait;

    use LockTrait;

    /**
     * @var bool Whether the type is configured in the database or not. See _getType() and _getDefaultType().
     */
    protected $fixedType = true;

    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     * Items with numerical keys (default) will use the current table name.
     * Items with alphabetical keys will use the given value (not the key) as prefix.
     * For such alphabetical keys, the prefix value can be an array containing the field names
     * from which the id should be composed (used for polymorphic links/footnotes)
     *
     * @var string[] $_fields_ids Array of fields
     * @var string[] $_tables_ids Array of property names and property classes for child tables
     */
    public static $_fields_ids = ['id'];
    public static $_tables_ids = [];

    /**
     * Field formats used for getValueFormatted and getValueUnformatted
     *
     * Overwrite in child classes and don't forget to redeclare the base fields
     * (published)
     *
     * @var string[]
     */
    protected $_fields_formats = [
        'id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id',
        'published' => 'select',
        'file_name' => 'file'
    ];

    /**
     * Current table
     *
     * @var Table
     */
    protected $_table = null;

    /**
     * A temporary ID for entities that are not yet persisted to the database
     *
     * @var string
     */
    protected $_newId = null;

    /**
     * By default, entities outside the Epi plugin are visible,
     * visibility is controlled by access to controllers and actions.
     *
     * @param array $options
     * @return bool
     */
    public function getEntityIsVisible($options = [])
    {
        return true;
    }

    /**
     * Get the Table of the entity
     *
     * Magic property $this->table
     *
     * @return \Cake\ORM\Table
     */
    protected function _getTable()
    {
        if (empty($this->_table)) {
            $this->_table = $this->fetchTable($this->getSource());
        }
        return $this->_table;
    }

    /**
     * Get the table name
     *
     * @return string
     */
    protected function _getTableName()
    {
        return $this->table->getTable();
    }

    /**
     * Get the base folder for file uploads
     *
     * All pipeline related files are located in the 'pipelines' folder
     *
     * @return string
     */
    protected function _getFileBasepath()
    {
        return $this->table_name . DS;
    }

    /**
     * Get the download  path.
     *
     * Property related files are always located in their base path.
     * Additional path segments are contained in the file_name field
     *
     * @return string
     */
    protected function _getFileDownloadpath()
    {

        $path = $this->type->config['fields']['file']['baseurl'] ?? '';

        if (empty($path)) {
            $path = rtrim($this->file_basepath, '/');
        }

        if ($this->hasDatabaseField('file_path')) {
            $path = $path . '/' . rtrim($this['file_path'], '/');
        }

        return Files::prependPath($path, Files::getFolder($this->file_name ?? ''));
    }

    protected function _getFileDownloadname()
    {
        return basename($this->file_name);
    }

    /**
     * Return fields to be rendered in view/add/edit actions
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        return $this->type->config['fields'] ?? [];
    }

    /**
     * Get the model table from the table name
     *
     * @param string $tableName
     * @param string $plugin
     *
     * @return Table
     */
    public function getModel($tableName, $plugin)
    {
        $model = $this->table->getModel($tableName, $plugin);
        if (empty ($model)) {
            throw new Exception('Model not found.');
        }

        return $model;
    }

    /**
     * Get the model name from the table name
     *
     * @param string $tableName
     * @param string|boolean $plugin
     *
     * @return string
     */
    public function getModelName($tableName, $plugin = false)
    {
        return $this->table->getModelName($tableName, $plugin);
    }

    /**
     * Check whether a field is virtual or in the database
     *
     * @param string $fieldname
     * @return boolean
     */
    public function hasDatabaseField($fieldname)
    {
        return $this->table->hasDatabaseField($fieldname);
    }

    /**
     * Get the names of ID fields.
     *
     * All fieldnames matching the following patterns will be extracted:
     * [
     *   'fieldname1',
     *   'xyz2' => 'fieldname2',
     *   'xyz3' => ['tablename3','fieldname3']
     * ]
     *
     * @return array|int[]|string[]
     */
    public static function getIdFields()
    {
        $fields = array_map(function ($field_key, $field_value) {
            if (is_array($field_value)) {
                return $field_value[1] ?? null;
            }
            elseif (is_numeric($field_key)) {
                return $field_value;
            }
            else {
                return $field_key;
            }

        }, array_keys(static::$_fields_ids), static::$_fields_ids);

        return $fields;
    }

    /**
     * Return an array of property names and entity classes for child tables
     *
     * @return array
     */
    public static function getIdTables()
    {
        return static::$_tables_ids;
    }

    /**
     * Overwrite setter to set field types
     *
     * Set the field type in the options array
     *
     * @param $field
     * @param $value
     * @param array $options
     * @return BaseEntity|void
     */
    public function set($field, $value = null, array $options = [])
    {

        if (isset($options['format'])) {
            $this->_fields_formats[$field] = $options['format'];
        }

        return $this->traitSet($field, $value, $options);
    }

    /**
     * Set the field format
     *
     * @param string $field
     * @param string $format
     * @return void
     */
    public function setFieldFormat($field, $format)
    {
        $this->_fields_formats[$field] = $format;
    }

    /**
     * Get the default type for the entity, if no type configuration is available in the types table
     *
     * @return array|null
     */
    protected function _getDefaultType()
    {
        return null;
    }

    /**
     * Get the type entity.
     *
     * For fixed types the default type is returned.
     * All app level entities and the types entities itself should be fixed types.
     *
     * Otherwise, loads the type entity if not already loaded
     *
     * @param $field
     * @return array
     */
    protected function _getType()
    {
        if (empty($this->_fields['type'])) {
            $type = $this->defaultType ?? null;
            if (!$this->fixedType) {
                $typeField = $this->table->typeField ?? null;
                if (!empty($typeField)) {
                    $tableName = $this->table->getTable();
                    $type = $this->table->getDatabase()->types[$tableName][$this->{$typeField}] ?? $type;
                }
            }
            $this->_fields['type'] = $type;
        }
        return $this->_fields['type'] ?? null;
    }

    /**
     * Get the field config
     *
     * @param string|array $fieldName
     * @return array
     */
    public function getFieldConfig($fieldName)
    {
        return [];
    }

    /**
     * Get format
     *
     * @param string|array $fieldName
     * @return string
     */
    public function getFieldFormat($fieldName)
    {
        $fieldName = is_array($fieldName) ? $fieldName : explode('.', $fieldName);
        return $this->_fields_formats[$fieldName[0]] ?? 'raw';
    }

    /**
     * Replace a placeholder string with entity values
     *
     * See Objects::parsePlaceholder() for examples.
     *
     * @param string|array $key
     * @param array $options Options passed to getValueNested(),
     *                       with aggregate set to false if not otherwise defined in a placeholder.
     * @return array
     */
    public function getValuePlaceholder($key, $options)
    {
        $value = Objects::parsePlaceholder($key, function ($path) use ($options) {
            $path = Objects::parseFieldKey($path, [], ['aggregate' => false]);
            $options['aggregate'] = $path['aggregate'];
            return $this->getValueNested($path['key'], $options);
        });

        return $value;
    }

    /**
     * Extract a value by a dot notated path,
     * format the last extracted component,
     * and optionally pipe it through processing steps
     *
     * TODO: optimize (access properties without toArray-conversion, including virtual fields)
     * TODO: convert arrays to JSON here instead of in index_table.php?
     *
     * ### Options
     * - format: Target format (xml, json, csv, html, md, ttl, rdf, jsonld, false).
     *           If the format is set and the extracted value is a BaseEntity,,
     *           the value is formatted using getValueFormatted().
     *           Otherwise the function falls back to Objects::extract().
     * - aggregate: Aggregation procedure, defaults to false, i.e. no aggregation.
     *              Can be a pipe separated string list or an array of processing steps.
     *              See processValues() for available steps.
     * - default: The default value if the key is not present in the object, defaults to false.
     *            TODO: implement or remove, see todo below
     *
     * See getValueFormatted() and Objects::extract() for further options.
     *
     * @param string|array $fieldPath A dot notation path or an array of path elements
     * @param array $options Options passed to getValueFormatted() and to Objects::extract()
     * @return array|string|integer|BaseEntity
     */
    public function getValueNested($fieldPath, $options = [])
    {
        // Format the value extracted by the last path component
        if (!empty($options['format'])) {

            $fieldPath = is_array($fieldPath) ? $fieldPath : explode('.', $fieldPath);
            $keyExtract = implode('.', array_slice($fieldPath, 0, -1));
            $keyField = end($fieldPath);

            if (str_contains($keyField, '*')) {
                $keyExtract .= '.' . $keyField;
                $keyField = 'id';
            }

            if (!empty($keyExtract)) {
                $value = Objects::extract($this, $keyExtract, false, $options);

                // Direct path to entity fields (e.g. project.name)
                if ($value instanceof BaseEntity) {
                    $value = $value->getValueFormatted($keyField, $options);
                }

                // Numeric arrays followed by an entity field (e.g. items.*.content)
                elseif ((is_array($value) && array_is_list($value))) {
                    foreach ($value as $valueKey => $valueItem) {
                        if ($valueItem instanceof BaseEntity) {
                            $value[$valueKey] = $valueItem->getValueFormatted($keyField, $options);
                        }
                        elseif (is_array($valueItem) || $valueItem instanceof ArrayAccess) {
                            $value[$valueKey] = Objects::extract($valueItem, $keyField, false, $options);
                        }
                    }
                }

                // Direct path to nested values (e.g. value.lat)
                elseif (is_array($value) || ($value instanceof ArrayAccess)) {
                    $value = Objects::extract($value, $keyField, false, $options);
                }
            }
            else {
                $value = $this->getValueFormatted($keyField, $options);
            }
        }

        // Extract the value without formatting
        else {
            $value = Objects::extract($this, $fieldPath, false, $options);
        }

        // TODO: implement default (check $value array)
        //        if (($value === null) && ($default !== false)) {
        //            $value = [$default];
        //        }

        // Post-processing
        $steps = $options['aggregate'] ?? false;
        if (!empty($steps)) {
            $steps = !is_array($steps) ? explode('|', $steps) : $steps;
            $value = Objects::processValues($value, $steps, false, $this);
        }

        return $value;
    }


    /**
     * Formats an entity field for rendered output, api output, or transfer output
     *
     * Dot notated field names can be used to extract values from JSON fields.
     * This is not meant to be used for extracting nested entities.
     * since only the first key in the dot notated path will be used to determine
     * the field format.
     *
     * ### Options
     * - format: The target output format (not the source field format).
     *           Rendered formats are 'html', 'txt', 'md', 'jsonld', 'rdf', 'ttl'.
     *           For data transfers, set the format to 'transfer'.
     *           All other formats are considered API output.
     * - prefixIds: Set to true to output table-id style IDs.
     *              Set to a string value, if you want a prefix inserted between table name and id.
     * - iriIds: Set to true to output iri paths instead of raw IDs.
     *
     * @param string|array $fieldName The fieldname, e.g. "value".
     *                                JSON keys can be provided using dot notation,
     *                                e.g. "value.longitude".
     * @param array $options
     * @return array|bool|float|int|mixed|string|null
     */
    public function getValueFormatted($fieldName, $options = [])
    {
        if (!$this->getEntityIsVisible($options)) {
            return null;
        }

        $fieldName = is_array($fieldName) ? $fieldName : explode('.', $fieldName);
        $fieldFormat = $this->getFieldFormat($fieldName);

        // IDs
        if ($fieldFormat === 'id') {
            return $this->getIdFormatted($fieldName, $options);
        }

        // Get value
        $raw = $this->getValueRaw($fieldName);
        $outputFormat = $options['format'] ?? 'html';

        // Data for transfer
        if ($outputFormat === 'transfer') {
            return $this->formatForTransfer($raw, $fieldName, $outputFormat, $fieldFormat, $options);
        }

        // Rendered output
        elseif (in_array($outputFormat, RENDERED_FORMATS)) {
            return $this->formatForRendered($raw, $fieldName, $outputFormat, $fieldFormat, $options);
        }

        // Serialized API output (xml, json, csv)
        else {
            return $this->formatForApi($raw, $fieldName, $outputFormat, $fieldFormat, $options);
        }
    }


    /**
     * Get nested value
     *
     * The first key of a dot notated path or of an array is the field name.
     * The following keys, if present, are used to extract data from
     * - JSON fields (string values are parsed),
     * - objects, or
     * - arrays.
     *
     * @param array|string $fieldName A simple or dot notated field name or an array of fieldnames.
     * @return mixed|null
     */
    public function getValueRaw($fieldName)
    {
        $fieldName = is_array($fieldName) ? $fieldName : explode('.', $fieldName);
        $fieldKey = array_shift($fieldName);
        $raw = $fieldKey === '' ? null : $this->get($fieldKey);

        if (sizeof($fieldName) > 0) {
            if (is_string($raw)) {
                try {
                    $raw = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
                } catch (Exception $e) {
                    $raw = ['error' => __('Error parsing JSON: {0}', [$e->getMessage()])];
                }
            }

            if ($raw instanceof BaseEntity) {
                $raw = $raw->getValueRaw(implode('.', $fieldName));
            }
            else {
                if ((is_array($raw) || $raw instanceof ArrayAccess)) {
                    $raw = Objects::extract($raw, implode('.', $fieldName), false);
                }
            }
        }
        return $raw;
    }

    /**
     * Get the ID as ID, prefixed ID or IRI path
     *
     * Override in child entities to support linked IDs (see Link.php)
     *
     * ### Options
     * - prefixIds: A prefix that should be added to the ID, e.g. 'tmp'.
     * - iriIds: Whether to output an IRI path as ID instead of prefixed IDs.
     *
     * @param array $fieldName The field name as an array of one or two components
     * @param array $options
     * @return string|integer|null
     */
    public function getIdFormatted($fieldName, $options)
    {
        $prefix = $options['prefixIds'] ?? false;
        if ($prefix !== false) {
            $table = static::$_fields_ids[$fieldName[0]] ?? $this->_tablename ?? $this->table->getTable();

            if (($options['copy'] ?? false)) {

            }

            if (is_array($table) && isset($this->{$table[1]})) {
                return "{$this->{$table[0]}}-{$prefix}{$this->{$table[1]}}";
            }
            elseif (!is_array($table) && isset($this->{$fieldName[0]})) {
                return "{$table}-{$prefix}{$this->{$fieldName[0]}}";
            }
            else {
                return null;
            }

            // Alternative using _fields
//                if (is_array($table) && isset($this->_fields[$table[1]])) {
//                    return "{$this->_fields[$table[0]]}-{$prefix}{$this->_fields[$table[1]]}";
//                }
//                elseif (isset($this->_fields[$fieldName[0]])) {
//                    return "{$table}-{$prefix}{$this->_fields[$fieldName[0]]}";
//                }
        }
        elseif ($options['iriIds'] ?? false) {
            if ($fieldName[0] === 'id') {
                return $this->iriPath;
            }
        }

        return $this->getValueRaw($fieldName);
    }

    /**
     * Output rendered values for HTML, Markdown and full text search indexing
     *
     * @param mixed $raw The value that should be formatted
     * @param array $fieldName
     * @param string $outputFormat The output format ('html', 'txt', 'md', 'jsonld', 'rdf', 'ttl')
     * @param string $fieldFormat The input field format
     * @param array $options
     * @return array|bool|float|int|mixed|string|null
     */
    public function formatForRendered($raw, $fieldName, $outputFormat, $fieldFormat, $options = [])
    {
        if (($fieldFormat === 'xml') && ($this->root !== null)) {
            try {
                $raw = $this->injectXmlAttributes($raw ?? '', $options, $fieldName);
                return $this->root->table->renderXmlFields($raw, $outputFormat);
            } catch (Exception $e) {
                $this->setParsingError($fieldName[0], __('Error parsing XML: {0}', [$e->getMessage()]));
                return $raw;
            }

        }
        elseif ($fieldFormat === 'json') {
            try {
                if (is_string($raw)) {
                    $value = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
                    $value = Attributes::toList($value, true);
                    return $value;
                }
                return '';
            } catch (Exception $e) {
                $this->setParsingError($fieldName[0], __('Error parsing JSON: {0}', [$e->getMessage()]));
                return $raw;
            }
        }

        elseif ($fieldFormat === 'geodata') {
            $value = $raw;

            if (is_string($value)) {
                try {
                    $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                } catch (Exception $e) {
                    $this->setParsingError($fieldName[0], __('Error parsing JSON: {0}', [$e->getMessage()]));
                }
            }

            // Workaround to get article for geolocations via items action
            // TODO: avoid $this->article since base entities should be abstract
            if ($this->root === $this) {
                $this->root = $this->article;
            }

            $url = $this->root->internalUrl;
            if ((BaseTable::$requestMode ?? 'default') !== 'default') {
                if (is_array($url)) {
                    $url['?'] = ['mode' => BaseTable::$requestMode ?? 'default'];
                }
                elseif (is_string($url)) {
                    $url .= '?mode=' . BaseTable::$requestMode ?? 'default';
                }
            }

            $value = [
                "type" => "Feature",
                "data" => [
                    "sortno" => $this->sortno ?? 0,
                    "id" => $this->id,
                    "signature" => $this->root->signature ?? '',
                    "name" => $this->root->name ?? '',
                    "quality" => (int)($this->published ?? 0),
                    "radius" => (int)($value["radius"] ?? 0),

                    "url" => Router::url($url)
                ],
                "geometry" => [
                    "type" => "Point",
                    // Careful: geojson uses lnglat, leaflet latlng
                    "coordinates" => [floatval($value['lng'] ?? null), floatval($value['lat'] ?? null)]
                ]
            ];

            return json_encode($value);
        }

        elseif ($fieldFormat === 'property') {
            $displayfield = $this[$fieldName[0]]['type']['merged']['displayfield'] ?? 'path';
            // TODO: add unit
            return $this[$fieldName[0]][$displayfield] ?? $this[$fieldName[0]]['lemma'] ?? $this[$fieldName[0]]['name'] ?? '';
        }

        elseif ($fieldFormat === 'name') {
            $displayfield = $this[$fieldName[0]]['type']['merged']['displayfield'] ?? 'name';
            // TODO: add unit
            return $this[$fieldName[0]][$displayfield] ?? $this[$fieldName[0]]['lemma'] ?? $this[$fieldName[0]]['name'] ?? '';
        }

        elseif ($fieldFormat === 'unit') {
            // TODO: what if the field is not named 'property'?
            return $this['property']['unit'] ?? '';
        }

        elseif (($fieldFormat === 'record') || ($fieldFormat === 'relation')) {
            $links_tab = $this->get($fieldName[0] . '_tab');
            $links_id = $this->get($fieldName[0] . '_id');
            $value = empty($links_id) ? '' : ($links_tab . '-' . $links_id);

            // TODO: output footnote
            // TODO: return caption of external links, footnotes, brands...
            if (!empty($links_id) && ($links_tab === 'sections')) {
                $section = $this->root->getSection($links_id);
                return $section['caption_path'] ?? $section['caption'] ?? $value;
            }
            elseif (!empty($links_id) && ($links_tab === 'articles')) {
                return ($links_id === $this->root->id) ? __('Article') : $this->links_article->captionPath;
            }
            else {
                return $value;
            }
        }

        elseif ($fieldFormat === 'select') {
            $fieldConfig = $this->getFieldConfig($fieldName);
            $codes = $fieldConfig['options'] ?? [];
            return $codes[$raw] ?? $raw;
        }

        elseif ($fieldFormat === 'published') {
            $fieldConfig = $this->getFieldConfig($fieldName);
            $codes = $fieldConfig['options'] ?? $this->publishedOptions;
            $value = $codes[$raw] ?? $raw;
            return $value;
//            return is_null($value) ? null : '● ' . $value;
        }

        elseif ($fieldFormat === 'timeago') {
            return empty($raw) ? '' : $raw->timeAgoInWords();
        }

        else {
            // TODO: By now works. Should escaping better be moved to the view?
            //       Or should all values from all fieldFormats be escaped for HTML rendering?
            if ($outputFormat === 'html') {
                $raw = h($raw);
            }
            return $raw;
        }
    }

    /**
     * Output values for transfer between databases
     *
     * Returns the raw field values, except for arrays.
     * Arrays are converted to JSON.
     *
     * @param mixed $raw The value that should be formatted
     * @param array $fieldName The field name
     * @param string $outputFormat The output format
     * @param string $fieldFormat The input field format
     * @param array $options
     * @return array|bool|float|int|mixed|string|null
     */
    public function formatForTransfer($raw, $fieldName, $outputFormat, $fieldFormat, $options = [])
    {
        if ($fieldFormat === 'array') {
            try {
                $jsonOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR;
                return json_encode($raw, $jsonOptions);
            } catch (Exception $e) {
                return __('Error encoding JSON: {0}', [$e->getMessage()]);
            }
        }
        else {
            return $raw;
        }
    }

    /**
     * Output values for API serialization
     *
     * TODO: do we still need a special handling for API output? Better use processing steps (piped fieldnames)?
     *
     * @param mixed $value The value that should be formatted
     * @param array $fieldName
     * @param string $outputFormat The output format
     * @param string $fieldFormat : The inputl format
     * @param array $options
     * @return array|bool|float|int|mixed|string|null
     */
    public function formatForApi($value, $fieldName, $outputFormat, $fieldFormat, $options = [])
    {
        if ($fieldFormat === 'xml') {
            try {
                $value = $this->injectXmlAttributes($value, $options, $fieldName);
            } catch (Exception $e) {
                $value = __('Error injecting IDs: {0}', [$e->getMessage()]);
            }

        }
        elseif ($fieldFormat === 'json') {
            try {
                $value = (sizeof($fieldName) > 1) ? $value : json_decode($value ?? '', true);
            } catch (Exception $e) {
                $value = ['error' => __('Error parsing JSON: {0}', [$e->getMessage()])];
            }

        }
        elseif (($fieldFormat === 'array') && ($outputFormat === 'csv')) {

            try {
                $jsonOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR;
                $value = json_encode($value, $jsonOptions);
            } catch (Exception $e) {
                $value = __('Error encoding JSON: {0}', [$e->getMessage()]);
            }
        }

        // Encode xml special characters
        elseif (($fieldFormat === 'array') && ($outputFormat === 'xml')) {
            if (is_array($value)) {
                array_walk_recursive(
                    $value,
                    function (&$value) {
                        $value = is_string($value) ? htmlspecialchars($value) : $value;
                    }
                );
            }
        }
        elseif (($fieldFormat === 'raw') && ($value instanceof FrozenTime)) {
            $value = $value->jsonSerialize();
        }
        elseif (!is_array($value) && is_string($value)) {
            // Remove non-printing characters except tab and line feed
            $value = preg_replace('/[\x00-\x08\x0B-\x0C\x0E-\x1f\x7F]/', '', $value);
            $value = h($value);
        }

        return $value;
    }

    /**
     * Merge date into JSON fields
     *
     * @param string $fieldName
     * @param array $data
     * @param boolean $recursive Recursiveley merge arrays
     * @return void
     */
    public function mergeJson($fieldName, $data, $recursive = true)
    {
        //$format = $this->getFieldFormat('value');
        if (isset($data[$fieldName]) && is_array($data[$fieldName])) {
            try {
                $oldValue = $this->getOriginal($fieldName);
                $encodeJson = !is_array($oldValue);
                if (is_string($oldValue)) {
                    try {
                        $oldValue = json_decode($oldValue, true);
                    } catch (Exception $e) {
                        $this->setError($fieldName, __('Error parsing original JSON value: {0}', [$e->getMessage()]));
                    }
                }

                if (is_array($oldValue)) {
                    if ($recursive) {
                        $data[$fieldName] = array_merge_recursive($oldValue, $data[$fieldName]);
                    }
                    else {
                        $data[$fieldName] = array_merge($oldValue, $data[$fieldName]);
                    }

                }

                if ($encodeJson) {
                    $jsonOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR;
                    $data[$fieldName] = json_encode($data[$fieldName], $jsonOptions);
                }
            } catch (Exception $e) {
                $this->setError($fieldName, __('Error encoding new JSON value: {0}', [$e->getMessage()]));
            }

            $this->set($fieldName, $data[$fieldName]);
        }
    }

    public function setIri()
    {
        if ($this->hasDatabaseField('norm_iri')) {
            $this->norm_iri = $this->iriFragment;
        }
    }

    /**
     * Return a temporary ID for entities that are not yet persisted to the database
     *
     * The ID can be used to link annotations to entities that are not yet saved.
     *
     * @return string
     */
    protected function _getNewId()
    {
        if (empty($this->_newId)) {
            $this->_newId = Attributes::uuid('new-');
        }
        return $this->_newId;
    }

    /**
     * Return a clean identifier build from the IRI field
     *
     * @return string
     */
    protected function _getIriIdentifier()
    {
        return mb_strtolower(Attributes::cleanIdentifier($this->{$this->_field_iri}));
    }

    /**
     * If missing, creates an IRI from the database name, table name and the row ID
     *
     * @return null|string
     */
    protected function _getIriFragment()
    {
        if (empty($this->norm_iri)) {
            $tableName = $this->getSource();
            if ($tableName <> '') {
                $source = $this->fetchTable($tableName);

                $iri = empty($this->_field_iri) ?
                    $this->id :
                    mb_strtolower(Attributes::cleanIdentifier($this->{$this->_field_iri} ?? ''));

                $iri = empty($iri) ? $this->id : $iri;

                if (!empty($iri) && $this->_prefix_iri) {
                    $iri = $source->getDatabaseIri() . '~' . $iri;
                }
                return $iri;
            }
            else {
                return null;
            }
        }
        else {
            return Attributes::cleanIdentifier($this->norm_iri);
        }
    }

    /**
     * Assembles a IRI path from the table name, type name and norm_iri field.
     * If any of those are empty, null will be returned.
     *
     * @return null|string
     */
    protected function _getIriPath()
    {
        // Add qualified norm_iri
        $norm_iri = $this->iriFragment;
        if ($norm_iri !== null) {
            $sourceName = $this->getSource();
            if (!empty($sourceName)) {
                $source = $this->fetchTable($sourceName);
                $tableName = $source->getTable();
                $typeField = $source->typeField ?? null;
                $typeName = ($typeField !== null) ? ($this[$typeField] ?? null) : null;

                $qualifiedIri = implode('/', array_filter([$tableName, $typeName, $norm_iri]));
                return $qualifiedIri;
            }
        }

        return null;
    }

    /**
     * Alias for _getIriPath()
     *
     * @return mixed
     * @deprecated Use iri_path in the config (check the Themenbrücken first)
     */
    protected function _getIri()
    {
        return $this->iri_path;
    }


    public function getEntityName()
    {
        return Inflector::singularize($this->tableName);
    }

    /**
     * Get a caption for the entity
     *
     * @return string
     */
    protected function _getCaption()
    {
        //if ($this->hasDatabaseField('caption')) {
        if (isset($this->_fields['caption'])) {
            return $this->_fields['caption'] ?? $this->{$this->table->captionField};
        }
        else {
            return $this->{$this->table->captionField};
        }
    }

    /**
     * Get a caption containing parent item captions
     *
     * @return string
     */
    protected function _getCaptionPath()
    {
        return $this->caption;
    }

    /**
     * Get a caption for external references
     *
     * @return string
     */
    protected function _getCaptionExt()
    {
        return $this->caption;
    }

    /**
     * Get older versions
     *
     * @return \Cake\ORM\Query|null
     */
    protected function _getVersions()
    {
        if ($this->table->hasFinder('versions')) {
            return $this->table->find('versions', ['version_id' => $this->id]);
        }
        else {
            return null;
        }
    }

    /**
     * Get the publication state of an entity.
     *
     * The method is overriden by specific entity types,
     * e.g. the publication state of an article is inherited from
     * the state of the project.
     *
     * @return int|null
     */
    protected function _getPublishedState()
    {
        return $this->published;
    }

    /**
     * Assign labels to publication states.
     *
     * See BaseEntity::_getPublishedLabel().
     *
     * @return array
     */
    protected function _getPublishedOptions()
    {
        return [
            PUBLICATION_DRAFTED => __("Drafted"),
            PUBLICATION_INPROGRESS => __("In progress"),
            PUBLICATION_COMPLETE => __("Complete"),
            PUBLICATION_PUBLISHED => __("Published"),
            PUBLICATION_SEARCHABLE => __("Searchable")
        ];
    }

    /**
     * Get a label of the publication state.
     *
     * The publication state is determined by the published field of the
     * entity and its containers. See the _getPublishedState()-method of
     * the entities. See BaseEntity::_getPublishedOptions() for the labels
     * that are assigned to different publication states.
     *
     * @return string
     */
    protected function _getPublishedLabel()
    {
        $value = $this->publishedState;
        return $this->publishedOptions[$value] ?? __("None");
    }

    /**
     * Return the role of the current user
     *
     * @return string|null
     */
    protected function _getCurrentUserRole()
    {
        //return $this->table::$userRole; //TODO: not working, source is not correctly set in IndexProperty entities
        return \App\Model\Table\BaseTable::$userRole;
    }

    /**
     * Check whether the user is permitted to access the entity
     *
     * @return boolean
     */
    public function isPermitted($permissionMask)
    {
        $permissionTable = FactoryLocator::get('Table')->get('Permissions');
        $permissionMask['entity_type'] = 'record';
        $permissionMask['entity_id'] = $this->id;
        return $permissionTable->hasPermission($permissionMask);
    }

    /**
     * Don't serialize root because it may contain a PDO
     * Necessary for writing entities to cache in IndexBehavior
     * Keep in mind: private properties are not serialized when using __sleep
     *
     * @return int[]|string[]
     */
    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)),
            ['root', 'container', 'type', '_table', '_prepared_root', '_prepared_tree']);
    }
}
