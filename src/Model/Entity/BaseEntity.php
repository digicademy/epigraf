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

use App\Datasource\Services\ServiceFactory;
use App\Utilities\Converters\Attributes;
use App\Utilities\Converters\Objects;
use App\Utilities\Converters\Strings;
use App\Utilities\Files\Files;
use ArrayAccess;
use Cake\Datasource\EntityTrait;
use Cake\Datasource\FactoryLocator;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
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
 * @property array $warnings
 *
 * @property string $importTable The table name in the imported source
 * @property mixed $rowNumber The row number in the imported source table
 * @property array $fieldsImport The fields used for data import
 * @property string $fieldsScope The scope field name
 *
 * @property string $fileBasepath
 * @property string $fileDownloadpath
 * @property string $fileDownloadname
 *
 * @property null|string $iriIdentifier
 * @property null|string $iriFragment
 * @property null|string $iriUrl
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
     * The field used to create an IRI.
     * Optionally, IRIs can be prefixed by the database name.
     *
     * @var string $_field_iri
     * @var boolean $_prefix_iri
     */
    protected $_field_iri = 'id';
    protected $_prefix_iri = false;

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
     * Fields used for data import
     *
     * @var string[]
     */
    protected $_fields_import = [];


    /**
     * Imported IDs, table name and row number
     */
    public $_import_ids = [];
    protected $_import_table = null;
    protected $_import_id = null;
    protected $_import_row = null;
    protected $_import_values = null;
    public $_import_action = null;
    public $_import_copyfile = null;
    public $_import_irimatched = null;

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
     * A warnings array grouped by warning type.
     * Used for caching warnings. Null indicates that warnings have not been checked yet.
     * See _getWarnings() for details.
     *
     * @var null|array
     */
    protected $_warnings = null;

    /**
     * Constructor
     *
     * @param array $content
     * @param array $options
     */
    public function __construct(array $content = [], array $options = [])
    {
        if ($options['import'] ?? false) {
            $content = $this->importData($content, $options);
            $options['useSetters'] = false;
            $options['markNew'] = empty($content['id']);
        }

        parent::__construct($content, $options);
    }

    /**
     * Convert imported raw data to the formats expected by the entity.
     * Overwrite in entity classes for type conversions.
     *
     * Remove ids from $content and populate import properties
     * (_import_table, _import_row, _import_id, _import_ids, _import_action)
     *
     * @param $content
     * @param $options
     * @return array
     */
    public function importData($content, $options)
    {
        // Keep import options
        $this->_import_values = $content;
        $this->_import_table = $options['table_name'] ?? null;
        $this->_import_id = $content['id'] ?? null;
        $this->_import_row = $options['table_row'] ?? null;
        $this->_import_action = $options['action'] ?? null;
        $this->_import_copyfile = $content['file_copyfrom'] ?? null;

        // Ignore fields
        if (!empty($options['fields'])) {
            $content = array_intersect_key($content, array_flip($options['fields']));
        }

        foreach ($this->_fields_import as $old => $new) {

            // Rename fields
            if (is_numeric($old) || ($old === $new) || !isset($content[$old]) || (!is_array($new) && isset($content[$new]))) {
                continue;
            }

            //  Split id fields (IRI paths, polymorphic relations)
            if (is_array($new)) {
                // IRI path (e.g. properties/languages/de)
                $value = explode('/', $content[$old]);
                if (sizeof($value) === 3) {
                    $content[$new[0]] = $value[0];
                    $content[$new[1]] = $content[$old];
                }

                // Polymorphic and combined IDs (e.g. properties-123)
                else {
                    $value = explode('-', $content[$old], 2);
                    $content[$new[0]] = $value[0] ?? null;
                    $content[$new[1]] = $content[$old];
                }

            } // Raw value
            else {
                $content[$new] = $content[$old];
                unset($content[$old]);
            }
        }

        // Separate ID and content fields
        $fields_id = static::getIdFields();
        $fields_content = array_diff($this->fields_import, $fields_id);

        $ids = array_intersect_key($content, array_flip($fields_id));
        $content = array_intersect_key($content, array_flip($fields_content));

        // Add explicit ID matching the pattern <tablename>-<id> to the index
        $explicit = array_filter($ids, fn($id) => preg_match('/^[a-z]+-[0-9]+$/', $id ?? ''));
        foreach ($explicit as $importid) {
            if (empty($options['index']['targets'][$importid])) {
                $solvedId = explode('-', $importid);
                $savedId = [
                    'model' => $solvedId[0],
                    'id' => (int)$solvedId[1]
                ];

                $options['index']['targets'][$importid] = $savedId;
            }
        }

        // Clear empty IDs
        $empty = array_filter($ids, fn($id) => $id === '');
        foreach ($empty as $fieldId => $fieldEmpty) {
            $content[$fieldId] = null;
            unset($ids[$fieldId]);
        }


        $index = $options['index']['targets'] ?? [];

        // Solve norm_iri
        if ($index && !empty($content['norm_iri'])) {

            $typeField = $options['type_field'] ?? null;
            $typeName = ($typeField !== null) ? ($content[$typeField] ?? null) : null;

            $qualifiedIri = implode('/', array_filter([
                $this->_import_table,
                $typeName,
                $content['norm_iri']
            ]));

            $solvedId = !empty($qualifiedIri) ? ($index[$qualifiedIri] ?? null) : null;
            if (!empty($solvedId)) {
                $content['id'] = $solvedId['id'];
                $this->_import_irimatched = true;
            }

            if (!empty($solvedId) && !empty($this->_import_id) && empty($options['index']['targets'][$this->_import_id])) {
                $options['index']['targets'][$this->_import_id] = [
                    'model' => $solvedId['model'],
                    'id' => (int)$solvedId['id']
                ];
            }
        }

        // Solve IDs if possible from index (move from ids to content)
        $this->_import_ids = $ids;
        $this->solveIds($content, $index);

        // Handle JSON fields
        // TODO: Implement option to merge the new config instead of replacing it?
        foreach ($this->_fields_formats as $fieldName => $fieldFormat) {
            if (!isset($content[$fieldName])) {
                continue;
            }
            if (($fieldFormat === 'array') && (is_string($content[$fieldName] ?? null)) ) {
                try {
                    $content[$fieldName] = json_decode($content[$fieldName], true);
                } catch (Exception $e) {
                    return ['error' => __('Error parsing JSON: {0}', [$e->getMessage()])];
                }
            }
        }

        // Parse date and time fields
        // e.g. '2016-02-04T12:58:47+01:00'
        if (isset($content['created'])) {
            $content['created'] = new FrozenTime($content['created']);
        }
        if (isset($content['modified'])) {
            $content['modified'] = new FrozenTime($content['modified']);
        }

        // Add job id
        if (isset($options['job_id'])) {
            $content['job_id'] = $options['job_id'];
        }

        // Fix timestamps
        if (isset($content['modified']) && empty($content['modified'])) {
            unset($content['modified']);
        }

        if (isset($content['created']) && empty($content['created'])) {
            unset($content['created']);
        }

        return $content;
    }

    /**
     * @param array|Entity $data
     * @param array $index
     */
    public function solveIds(&$data, &$index)
    {
        if ($index) {
            $solved = array_keys(
                array_intersect_key(
                    array_flip(
                        array_filter(
                            array_unique(
                                $this->_import_ids
                            )
                        )
                    ),
                    $index
                )
            );
            $solved = array_intersect($this->_import_ids, $solved);
            $this->_import_ids = array_diff_key($this->_import_ids, $solved);
            foreach ($solved as $field => $importid) {
                $data[$field] = $index[$importid]['id'];
            }
        }
    }

    /**
     * Extract saved ID and add to index
     *
     * @param $index
     */
    public function indexIds(&$index)
    {

        foreach ($this->_import_ids as $field => $importedId) {
            if (empty($importedId)) {
                continue;
            }

            // Add the saved entity to targets index, others to sources
            if ($field === 'id') {
                $index['targets'][$importedId] = [
                    'model' => $this->getSource(),
                    'id' => $this->id
                ];
            }
            else {
                $index['sources'][$importedId][] = [
                    'model' => $this->getSource(),
                    'id' => $this->id,
                    'field' => $field,
                    'scope_field' => $this->fieldsScope
                ];
            }
        }
    }

    /**
     * By default, entities outside the Epi plugin are visible to authenticated users.
     * For non-authenticated users, the visibility is controlled by the published field.
     *
     * @param array $options
     * @return bool
     */
    public function getEntityIsVisible($options = [])
    {
        $userRole = $this->currentUserRole ?? $this->root->currentUserRole ?? 'guest';
        if ($userRole !== 'guest') {
            return true;
        }

        return !$this->hasDatabaseField('published') || $this->published;
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
     * Get the scope field of current table
     *
     * @return string
     */
    protected function _getFieldsScope()
    {
        return $this->table->scopeField;
    }

    /**
     * Get import fields
     *
     * @return array
     */
    protected function _getFieldsImport()
    {
        $fields = array_values(array_map(fn($x) => is_array($x) ? $x : [$x], $this->_fields_import));
        $fields = array_merge(...$fields);
        return $fields;
    }


    /**
     * Get current number of current import row
     *
     * @return mixed|null
     */
    protected function _getRowNumber()
    {
        return $this->_import_row ?? null;
    }

    /**
     * Get name of current import table
     *
     * @return string
     */
    protected function _getImportTable()
    {
        return $this->_import_table;
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
     * Return fields to be rendered in entity tables
     *
     * See BaseEntityHelper::entityTable() for the supported options.
     *
     * @return array[] Field configuration array.
     */
    protected function _getHtmlFields()
    {
        return $this->type->config['fields'] ?? [];
    }

    /**
     * Check whether the entity is well-formed
     *
     * Returns an array of warnings grouped by warning type.
     * Each warning is an array with at least a 'msg' key
     * containing a message to be displayed to the user.
     *
     * @return array An array of warnings grouped by warning type
     */
    protected function _getWarnings()
    {
        return $this->_warnings;
    }

    /**
     * Returns validation warning messages of a field
     *
     * @param string $field Field name to get the errors from
     * @return string[] An array of warning messages for the given field
     */
    public function getWarnings(string $field)
    {
        $warnings = $this->warnings[$this->tableName . '-' . $field] ?? [];
        return array_map(fn($warning) => $warning['msg'] ?? '', $warnings);
    }

    /**
     * Check whether another entity depends on the entity
     *
     * @param BaseEntity $entity
     * @return bool
     */
    public function hasRoot($entity)
    {
        if (empty($entity)) {
            return false;
        }
        elseif ($this->root === $entity) {
            return true;
        }
        else {
            return false;
        }
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
        $fields = array_map(
            function ($field_key, $field_value) {
                if (is_array($field_value)) {
                    return $field_value[1] ?? null;
                }
                elseif (is_numeric($field_key)) {
                    return $field_value;
                }
                else {
                    return $field_key;
                }

            },
            array_keys(static::$_fields_ids),
            static::$_fields_ids
        );

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
     * Get default published status options
     *
     * @return array
     */
    /**
     * @param string|array $fieldName
     * @return bool
     */
    public function getValueIsEmpty($fieldName)
    {
        $fieldName = is_array($fieldName) ? $fieldName : explode('.', $fieldName);
        $content = empty($fieldName[0]) ? '' : $this->get($fieldName[0]);
        return ($content === '') || ($content === null);
    }

    /**
     * Get the default type for the entity, if no type configuration is available in the types table
     *
     * @return DefaultType
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
     * Render a value according to a column configuration
     *
     * If the column configuration contains a placeholder ('value' key), getValuePlaceholder() is called.
     * Otherwise, getValueNested() is called with the column key ('key' key).
     *
     * @param array $column The column configuration as returned by $this->getColumns().
     * @return string | null
     */
    public function getValueRendered($column = []) {
        $column['format'] = 'html';
        $column['aggregate'] = $column['aggregate'] ?? 'collapse';
        $placeholder = $column['value'] ?? '';
        if ($placeholder !== '') {
            $value = $this->getValuePlaceholder($placeholder, $column + ['collapse' => ', ']);
        } else {
            $value = $this->getValueNested($column['key'], $column);
        }

        if (isset($column['prefix'])) {
            $value = $column['prefix'] . ' ' . $value;
        }

        return $value;
    }
    /**
     * Replace a placeholder string with entity values
     *
     * See Objects::parsePlaceholder() for examples.
     *
     * ### Options
     * - collapse By default, an array is returned.
     *   Provide a separator if you want to collapse the value to a single string.
     *
     * All other options are passed to getValueNested()
     * with aggregate set to false if not otherwise defined in a placeholder.
     *
     * @param string|array $key The placeholder string or an array of placeholder strings.
     * @param array $options Options passed to getValueNested(),
     *                       with aggregate set to false if not otherwise defined in a placeholder.
     * @return string|array
     */
    public function getValuePlaceholder($key, $options= [])
    {

        $value = [];
        if (is_array($key)) {
            foreach ($key as $part) {
                $loopValue = $this->getValuePlaceholder($part, $options);
                if (is_array($loopValue)) {
                    $value = array_merge($value, $loopValue);
                }
                else {
                    if ($loopValue ?? false) {
                        $value[] = $loopValue;
                    }
                }
            }
        }
        else
        {
            if (!str_contains($key, '{')) {
                return $key;
            }

            $value = Objects::parsePlaceholder($key, function ($pathList) use ($options) {
                $pathList = Objects::parsePathList($pathList);
                $result = null;
                foreach ($pathList as $path) {
                    $path = Objects::parseFieldKey($path, [], ['aggregate' => false]);
                    $options['aggregate'] = $path['aggregate']; // TODO: do we need to unset collapse here?
                    $options['format'] = $path['format'] ?? $options['format'] ?? false;
                    $result = $this->getValueNested($path['key'], $options);
                    if (!is_null($result) && ($result !== []) && ($result !== '')) {
                        break;
                    }
                }
                return $result;
            });
        }

        if (isset($options['collapse'])) {
            $value = is_array($value) ? array_map(fn($x) => strval($x), $value) : $value;
            $value = is_array($value) ? implode($options['collapse'], $value) : $value;
        }

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
     *           The extraction key in a field path may be prefixed with a format key, seperated by colon.
     *           This overrides the format in the options.
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

        // Extract the value without formatting
        // TODO: can the method be refactored to get rid of this special condition?
        //       In one of the last revisions, I moved calls of getValueFormatted() to Objects::extract(),
        //       this, this differentiation should not be necessary any more?
        if (empty($options['format'])) {
            $value = Objects::extract($this, $fieldPath, false, $options);
        }

        // Format the value extracted by the last path component
        else {

            $fieldPath = is_array($fieldPath) ? $fieldPath : explode('.', $fieldPath);
            $keyExtract = implode('.', array_slice($fieldPath, 0, -1));
            $keyField = end($fieldPath);

            if (str_contains($keyField, '*')) {
                $keyExtract .= '.' . $keyField;
                $keyField = 'id';
            }

            // Get everything up to the last component without formatting
            if (empty($keyExtract)) {
                $value = $this;
            }
            else {
                $published = $options['published'] ?? $options['params']['published'] ?? \App\Model\Table\BaseTable::$requestPublished;
                $value = Objects::extract($this, $keyExtract, false, ['published' => $published]);
            }

            // Numeric arrays followed by an entity field (e.g. items.*.content)
            if ((is_array($value) && array_is_list($value))) {
                foreach ($value as $valueKey => $valueItem) {
                  if (is_array($valueItem) || $valueItem instanceof ArrayAccess) {
                      $value[$valueKey] = Objects::extract($valueItem, $keyField, false, $options);
                  }
                }
            }

            // Direct path to field (e.g. projects.name) or nested values (e.g. value.lat)
            elseif (is_array($value) || ($value instanceof ArrayAccess)) {
                $value = Objects::extract($value, $keyField, false, $options);
            }

            // Try to extract value from JSON
            elseif (is_string($value) && !empty($keyField)) {
                try {
                    $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                    if (is_array($value) || ($value instanceof ArrayAccess)) {
                        $value = Objects::extract($value, $keyField, false, $options);
                    }
                } catch (Exception $e) {
                    // Not a JSON string, return as is
                }
            }
        }

        // TODO: implement default (check $value array)
        //        if (($value === null) && ($default !== false)) {
        //            $value = [$default];
        //        }

        // Post-processing
        $steps = $options['aggregate'] ?? false;
        if (!empty($steps)) {
            $steps = Objects::parseProcessing($steps);
            $value = Objects::processValues($value, $steps, true, $this);
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
     *           Rendered formats are 'html', 'txt', 'md', 'jsonld', 'rdf', 'ttl', 'geojson'.
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
     * - iriIds: Whether to output an IRI path as ID instead of prefixed IDs. In this case, prefixIds must be empty.
     * - copy (boolean): Whether the entity is to be copied. In this case, the prefix is only added for entities depending on the root.
     *                   For example, when articles are copied, item IDs are prefixed and, thus, copied, but property IDs are not.
     * - root (Entity): The root entity, necessary for copy operations to detect whether an entity depends on the root or not.
     *
     * @param array $fieldName The field name as an array of one or two components
     * @param array $options
     * @return string|integer|null
     */
    public function getIdFormatted($fieldName, $options)
    {
        $prefixIds = $options['prefixIds'] ?? false;
        if ($prefixIds !== false) {
            $table = static::$_fields_ids[$fieldName[0]] ?? $this->_tablename ?? $this->table->getTable();

            $prefix = is_bool($prefixIds) ? '' : $prefixIds;

            // Only copy entities that belong to the root
            if (($options['copy'] ?? false) && !$this->hasRoot($options['root'])) {
              $prefix = '';
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
     * ### Options
     * - geodata: For geodata fields, an array of values to be added to the rendered GeoJSON value
     * - default: For geodata fields, an array with default lat and lng values to be used if the coordinates are missing.
     *
     * @param mixed $raw The value that should be formatted
     * @param array $fieldName
     * @param string $outputFormat The output format ('html', 'txt', 'md', 'jsonld', 'rdf', 'ttl', 'geojson')
     * @param string $fieldFormat The input field format
     * @param array $options
     * @return array|bool|float|int|mixed|string|null
     */
    public function formatForRendered($raw, $fieldName, $outputFormat, $fieldFormat, $options = [])
    {
        if (($fieldFormat === 'xml') && ($this->root !== null)) {
            try {
                $raw = $this->injectXmlAttributes($raw ?? '', $options + ['prefixIds' => true], $fieldName);
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

        elseif ($fieldFormat === 'csv') {
            try {
                if ($outputFormat === 'html') {
                    return \App\Utilities\Converters\Csv::csvToHtml($raw);
                }
                else if (is_string($raw)) {
                    $value = str_getcsv($raw);
                } else {
                    $value = $raw;
                }
                return $value;
            } catch (Exception $e) {
                $this->setParsingError($fieldName[0], __('Error parsing CSV: {0}', [$e->getMessage()]));
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

            if (Attributes::isBlank($value['lng'] ?? null) || Attributes::isBlank($value['lat'] ?? null)) {
                if (!empty($options['default']) && is_array($options['default'])) {
                    $value['lng'] = $options['default']['lng'] ?? 0;
                    $value['lat'] = $options['default']['lat'] ?? 0;
                } else {
                    return null;
                }
            }

            // Root: The article or the property
            $root = empty($this->container) ?  $this->root : ($this->container->root ?? $this->container);

            $geometry = [
                "type" => "Point",
                // Careful: geojson uses lnglat, leaflet latlng
                "coordinates" => [floatval($value['lng'] ?? null), floatval($value['lat'] ?? null)]
            ];

            $data = [
                "id" =>  $options['geodata']['id'] ??  $this->id,
                "number" => $options['geodata']['sortno'] ?? $this->sortno ?? 0,
                //"caption" => $root->captionPath ?? '',
                "quality" => (int)($this->published ?? 0),
                "radius" => (int)($value["radius"] ?? 0),
                "rootId" => $root->id
            ] + ($options['geodata'] ?? []);

            $feature = [
                "type" => "Feature",
                "data" => $data,
                "geometry" => $geometry
            ];

            return $outputFormat === 'geojson' ? $feature :  json_encode($feature);
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

        elseif (in_array($outputFormat, TRIPLE_FORMATS) && ($raw instanceof FrozenTime)) {
            return $raw->jsonSerialize();
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
     * TODO: Do we still need this? Since array fields (e.g. for types) are not automatically decoded anymore, this should be obsolete?
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
        if (($fieldFormat === 'array') && is_array($raw)) {
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
     * @param string $fieldFormat : The input format
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
                if (!is_array($value)) {
                    $value = json_decode($value ?? '', true);
                }
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
            if (($outputFormat === 'xml') && ($fieldFormat !== 'xml')) {
                $value = h($value);
            }
        }

        return $value;
    }

    /**
     * Merge date into JSON fields
     *
     * Adds new JSON data to the existing JSON data.
     * Keeps the field format (encoded string or array).
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
                        $data[$fieldName] = array_replace_recursive($oldValue, $data[$fieldName]);
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

    /**
     * Merge data into an entity field
     *
     * - Adds new norm data entries to the existing norm data.
     *   Entries with the same prefix are replaced, other entries are appended.
     *
     * @param string $field
     * @param mixed $value
     * @return void
     */
    public function mergeData($field, $value)
    {
        if ($field === 'norm_data') {
            $prefix = Attributes::getPrefix($value);
            $normData = array_filter(
                explode("\n", $this->norm_data ?? ''),
                function ($line) use ($prefix) {
                    return ($line !== '') && (strpos($line, $prefix . ':') !== 0);
                }
            );
            $normData[] = $value;
            $value = implode("\n", $normData);
        }

        $this[$field] = $value;
    }

    /**
     * Autofill a field based on the type configuration
     *
     * @return $this
     */
    public function autofill($field = 'sortkey') {

        $sortKeyConfig = $this->type['config']['fields'][$field]['autofill'] ?? [];
        if (!empty($sortKeyConfig['source'])) {
            $sourceField = $sortKeyConfig['source'];
            $value = $this->{$sourceField} ?? '';

            $process = $sortKeyConfig['process'] ?? [];
            foreach ($process as $step) {
                $stepConfig =  is_array($step) ? $step : [];
                $step = $stepConfig['method'] ?? $step;

                if ($step === 'path') {
                    $path = $this->parentPath;
                    if (!Attributes::isBlank($path)) {
                        $value = $path . $this->_path_separator . $value;
                    }
                }
                else if ($step === 'number') {
                    $value = Attributes::extractNumber($value);
                }
                else if ($step === 'sortkey') {
                    $value = trim(mb_strtolower($value));
                    $value = Strings::prefixNumbersWithZero($value, $stepConfig['width'] ?? 5);
                    $value = Strings::collapseWhitespace($value);
                }
                else if ($step === 'irifragment') {
                    $value =  Attributes::cleanIdentifier($value);

                }
                else if ($step === 'prefix') {
                    // TODO: the prefix key is deprecated, use value
                    $value = ($stepConfig['value'] ?? $stepConfig['prefix'] ?? '') . $value;
                }
                else if ($step === 'postfix') {
                    // TODO: the postfix key is deprecated, use value
                    $value = ($stepConfig['value'] ?? $stepConfig['postfix'] ?? '') . $value;
                }
            }

            $this[$field] = $value;
        }

        return $this;
    }

    /**
     * Update the norm_iri field
     *
     * See _getIriFragment() for details.
     *
     * TODO: make dry, see autofill().
     *
     * @param boolean $overwrite Whether to overwrite existing values.
     * @param boolean $typed Whether to use the source field from the types configuration (if available).
     * @return void
     */
    public function setIri($overwrite = false, $typed = false)
    {
        $field = 'norm_iri';
        if (!$this->hasDatabaseField($field)) {
            return;
        }

        if (!empty($this->{$field}) && !$overwrite) {
            return;
        }

        if ($typed) {
            $sourceField = $this->type['merged']['fields'][$field]['autofill']['source'] ?? null;
            if (!empty($sourceField)) {
                $value = Attributes::cleanIdentifier($this->{$sourceField} ?? '');
            } else {
                $value = '';
            }

            if (!empty($value)) {
                $this->{$field} = $value;
                return;
            }
        }

        $this->{$field} = $this->iriIdentifier;
    }

    /**
     * Clear contained entities
     *
     * To be implemented in subclasses.
     *
     * @return boolean
     */
    public function clear()
    {
        return true;
    }

    /**
     * Call a reconciliation service
     *
     * The services need to be configured in the type configuration with the following keys:
     *
     * - service (string) Identifier of the service class:
     *   'reconcile' for ReconcileService or 'geo' for GeoService.
     * - input (string) Name of the field that contains input data for the service
     * - provider (string) Name of the provider used for the service
     * - score (int) Optionally, a minimum score for a candidate to be accepted. Defaults to 20.
     * - type (string) Optionally, a type parameter passed to the service.
     *
     * The service as identified by the service parameter must be implemented as a BaseService class.
     * Its query method is called with an empty path parameter and a data array containing the following keys:
     *
     * - q (string) The term to be reconciled.
     * - provider (string) The provider name.
     * - type (string) Optionally, the type value passed to the service.
     *
     * The result must be an array with the following keys:
     *
     * - state (string) The state of the service call (SUCCESS or ERROR)
     * - result.answers.candidates (array) The candidates returned by the service.
     *
     * Each candidate must have data about the match (match or score key).
     * If match is true or the score is greater than the minimum score from the service configuration,
     * the value (if null the id) is stored in the target field.
     *
     * - match (boolean) Whether the candidate is a match to the query term.
     * - score (int) The score of the canditate.
     * - value (string) The result value
     * - id (string)  The result ID
     *
     * Norm data (if the target field is norm_data)
     * is processed by removing existing entries with the same prefix
     * and then adding the value to the field. Other fields are replaced.
     *
     * ### Options
     * - onlyempty (bool) If set to true, only empty fields are reconciled.
     *
     * @param string $field The field name to be reconciled
     * @param array $options
     * @return Entity $this
     * @throws \Exception
     */
    public function reconcile($field = 'norm_data', $options = []) {

        $servicesConfigs = $this->type['merged']['fields'][$field]['services'] ?? [];

        if (!empty($servicesConfigs)) {
            foreach ($servicesConfigs as $serviceKey => $serviceConfig) {
                $apiService = ServiceFactory::get($serviceConfig['service'], false);

//                if ((($serviceConfig['service'] ?? '') !== $service) || empty($serviceConfig['provider'])) {
//                    continue;
//                }

                // Check empty
                if (!empty($options['onlyempty'])) {
                    if (!empty($this[$field])) {
                        continue;
                    }
                }

                // Get value
                $term = $this[$serviceConfig['input']] ?? '';
                if ($term === '') {
                    continue;
                }
                $data = [
                    'q' => $term,
                    'provider' => $serviceConfig['provider'] ?? ''
                ];
                if (!empty($serviceConfig['type'])) {
                    $data['type'] = $serviceConfig['type'];
                }
                $task = $apiService->query(null, $data);

                // Update value
                if ($task['state'] === 'SUCCESS') {
                    $minScore = $serviceConfig['score'] ?? 20;
                    foreach ($task['result']['answers'] ?? [] as $answer) {
                        foreach ($answer['candidates'] ?? [] as $candidate) {
                            if (!empty($candidate['match']) || ($candidate['score'] ?? 0) > $minScore) {
                                $value = $candidate['value'] ?? $candidate['id'];
                                $this->mergeData($field, $value);
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $this;
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

    protected function _getIriField() {
        return $this->_field_iri;
    }

    /**
     * Return a clean identifier build from the IRI field
     *
     * Falls back to an IRI derived from the entity ID,
     * optionally prefixed by the database name.
     *
     * @return string
     */
    protected function _getIriIdentifier()
    {
        $iri = '';

        $iriField = $this->iriField;
        if (!empty($iriField)) {
            $iri = Attributes::cleanIdentifier($this->{$iriField} ?? '');
        }

        if (empty($iri)) {
            $iri = (string)$this->id;
            $iriField = 'id';
        }

        // TODO: do we need a configurable _prefix_iri property or should we always prefix IDs in project databases?
        if (($iriField === 'id') && !empty($iri) && $this->_prefix_iri) {
            $tableName = $this->getSource();
            if ($tableName <> '') {
                $source = $this->fetchTable($tableName);
                $iri = $source->getDatabaseIri() . '~' . $iri;
            }
        }

        return $iri;
    }

    /**
     * If missing, creates an ad hoc IRI fragment
     * from the database name, table name and the row ID
     *
     * @return null|string
     */
    protected function _getIriFragment()
    {
        if (empty($this->norm_iri)) {
            return $this->iriIdentifier;
        } else {
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
                /** @var $typeField string|null */
                $typeField = $source->typeField ?? null;
                $typeName = ($typeField !== null) ? ($this[$typeField] ?? null) : null;

                $qualifiedIri = implode('/', array_filter([$tableName, $typeName, $norm_iri]));
                return $qualifiedIri;
            }
        }

        return null;
    }

    /**
     * Get the relative IRI
     *
     * @return null|string
     */
    protected function _getIriUrl()
    {
        $iriPath = $this->iriPath;
        if (!empty($iriPath)) {
            return  '/iri/' . $iriPath;
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
        return $this->iriPath;
    }

    /**
     * Check whether the entity has children
     *
     * Overwrite in child classes.
     *
     * @return false
     */
    protected function _getHasChildren()
    {
        return false;
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
            ['root', 'container', 'type', '_table', '_tableLocator', '_prepared_root', '_prepared_tree']);
    }
}
