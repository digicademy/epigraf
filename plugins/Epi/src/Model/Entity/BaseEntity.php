<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */

namespace Epi\Model\Entity;

use App\Model\Entity\Databank;
use App\Model\Interfaces\ExportEntityInterface;
use App\Utilities\Converters\Arrays;
use App\Utilities\Files\Files;
use App\Utilities\XmlParser\XmlMunge;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Epi\Model\Table\BaseTable;
use Exception;
use Rest\Entity\LockInterface;

/**
 * Base entity class for project databases
 *
 * The Epi base entity class inherits all methods and properties from the App base entity class.
 * All project database entities should be derived from this class.
 *
 * # Virtual fields
 * @property int|null $publishedState
 * @property bool $visible Whether the item is visible for the current user
 * @property string $caption A display name for the entity
 * @property string $captionPath A display name for the entity including its path in ancestor items
 *
 * @property string $databaseName The prefixed database name, where the entity is stored
 * @property string $database The unprefixed database name, where the entity is stored
 *
 * @property string $iriFragment
 * @property string $iriIdentifier
 * @property string $localIri @deprecated
 * @property string $publicIri @deprecated
 * @property string[]|false $normDataParsed Array of parsed norm data lines
 *
 * @property string $importTable The table name in the imported source
 * @property mixed $rowNumber The row number in the imported source table
 * @property array $fieldsImport The fields used for data import
 * @property string $fieldsScope The scope field name
 * @property array $solvedIds In the process of importing data, the array solved Ids
 *
 * @property array $index A lookup index
 * @property array $parsedErrors Parsing errors
 * @property array $linkErrors Link errors
 *
 * @property string $typePath
 * @property array|Query $typeOptions
 * @property string $xmlTag The tag name used in the XmlView
 *
 * @property string $fileBasepath
 * @property string $fileDefaultpath
 * @property string $fileCopyfrom
 * @property array $fileProperties
 * @property boolean $fileExists
 * @property string $fileFullpath
 * @property string $thumbUrl
 * @property array $thumb
 *
 * # Relations
 * @property BaseTable $table
 * @property Type $type
 * @property array|ResultSet $footnotes
 * @property array|ResultSet $annotations
 * @property array $tags
 *
 */
class BaseEntity extends \App\Model\Entity\BaseEntity implements ExportEntityInterface, LockInterface
{
    /**
     * @var bool Whether the type is configured in the database or not. See _getType() and _getDefaultType().
     */
    protected $fixedType = false;

    /**
     * The root entity that contains the list of links and footnotes (the Article or Property)
     * @var null
     */
    public ?BaseEntity $root = null;

    /**
     * The container entity is the parent in tables other than the entity's table (e.g. the section of an item)
     * @var null
     */
    public ?BaseEntity $container = null;

    /**
     * The parent is the parent in the same table (e.g. the parent section of a section)
     * @var null
     */
    public $parent = null;

    /**
     * @var string The property name of child entities. Used for triple generation.
     */
    public $_children = null;

    //TODO: define fields in the table and only override in entities if necessary
    // (e.g. for properties inside of properties or sections in the section path)

    /**
     * Fields to be serialized in getDataForExport.
     * You can rename keys by providing a named list (old => new)
     *
     * @var array
     */
    public $_serialize_fields = [];

    /**
     * Snippets for export
     * Each snippet contains a list of fields that is added to $_serialize_fields.
     *
     * @var array
     */
    protected $_serialize_snippets = ['problems' => ['problems']];

    /**
     * Fields to be serialized as attributes the XmlView
     *
     * @var array
     */
    public $_serialize_attributes = [];

    /**
     * Indicates whether the item root was prepared.
     * Used to avoid loops because getDataForExport is called recursively.
     *
     * @var bool
     */
    protected $_prepared_root = false;

    /**
     * Whether a new local IRI was assigned
     *
     * @var bool
     */
    protected $_prepared_iri = false;

    /**
     * Whether tree positions were assigned
     *
     * @var bool
     */
    protected $_prepared_tree = false;

    protected $_fields_formats = [
        'id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id',
        'published' => 'published',
        'property' => 'property'
    ];

    /**
     * Tagname used in the XmlView
     * Set to null to automatically derive tagname from entity name.
     *
     * @var null
     */
    public $_xml_tag = null;

    /**
     * Fields used for data import
     *
     * @var string[]
     */
    protected $_fields_import = [];

    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     * Items with numerical keys (default) will use the current table name.
     * Items with alphabetical keys will use the given value (not the key) as prefix.
     * For such alphabetical keys, the prefix value can be an array containing the field names
     * from which the id should be composed (used for polymorphic links/footnotes)
     *
     * @var string[]
     */
    public static $_fields_ids = ['id'];


    /**
     * The field used to create an IRI.
     * Optionally, IRIs can be prefixed by the database name
     *
     * @var string $_field_iri
     * @var boolean $_prefix_iri
     */
    protected $_field_iri = 'id';
    protected $_prefix_iri = true;

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
     * Parsing errors
     */
    public $_parsing_errors = [];

    /**
     * Link errors
     */
    public $_link_errors = [];

    /**
     * Cached file properties
     *
     * @var null
     */
    protected $_file_properties = null;

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
            //$options['markClean'] = true;
            $options['markNew'] = empty($content['id']);
        }

        parent::__construct($content, $options);
    }

//    Conflicts with the type entity
//
//    protected function _getType() {
//        if (empty($this->table->typeField)) {
//            return null;
//        } else {
//            return $this->{$this->table->typeField} ?? null;
//        }
//    }

    /**
     * Get publication state
     *
     * Overrides the method of the BaseEntity.
     *
     * @return int|null
     */
    protected function _getPublishedState()
    {
        if ($this->published !== null) {
            return $this->published;
        }
        elseif ($this->container && ($this->container !== $this)) {
            return $this->container->publishedState;
        }
        elseif ($this->root && ($this->root !== $this)) {
            return $this->root->publishedState;
        }
        else {
            // TODO: return database published state?
            return null;
        }
    }

    /**
     * By default, entities are not visible for guests.
     * To show entities, in the types config, set the public key to true
     * and set the published field of the item, section or article at least to PUBLICATION_PUBLISHED
     *
     * # Options
     * - published An array of publication states to compare against. Will be taken from the request options, if not provided.
     *
     * @param array $options
     * @return bool
     */
    public function getEntityIsVisible($options = [])
    {
        if ($this['hide'] ?? false) {
            return false;
        }

        $userRole = $this->currentUserRole ?? $this->root->currentUserRole ?? 'guest';
        $requestAction = \App\Model\Table\BaseTable::$requestAction;
        $published = $options['published'] ?? $options['params']['published'] ?? \App\Model\Table\BaseTable::$requestPublished;

        if (($userRole !== 'guest') && ($requestAction !== 'edit') && !empty($published) && $this->hasDatabaseField('published')) {
            return in_array($this->publishedState ?? PUBLICATION_PUBLISHED, $published);
        }

        if ($userRole === 'guest') {
            $public = $this->type['published'] ?? PUBLICATION_BINARY_UNPUBLISHED;
            return ($public && ($this->publishedState >= PUBLICATION_PUBLISHED));
        }

        return true;
    }

    /**
     * Parse the norm data field
     * TODO: add the full prefixes in the data
     *       (the urn is truncated in the DIO import, no valid urn)
     *
     * @return false|string[]
     */
    protected function _getNormDataParsed()
    {
        $items = explode("\n", $this->norm_data ?? '');

        $namespaces = $this->type['merged']['namespaces'] ?? [];
        $namespaces = $namespaces + [
                'http' => ['button' => 'Web', 'baseurl' => 'http:'],
                'https' => ['button' => 'Web', 'baseurl' => 'https:']
            ];

        foreach ($items as $key => $value) {
            $value = explode(':', trim($value), 2);
            if ((count($value) > 1) && (isset($namespaces[$value[0]]))) {

                $url = ($namespaces[$value[0]]['baseurl'] ?? '') . trim($value[1]);
                $button = ($namespaces[$value[0]]['button'] ?? $value[0]);

                $items[$key] = [
                    'button' => $button,
                    'value' => implode(':', $value),
                    'url' => $url
                ];
            }
        }

        return $items;
    }


    /**
     * Clear contained items.
     * To be implemented in subclasses.
     *
     * @return boolean
     */
    public function clear()
    {
        return true;
    }

    /**
     * Convert imported raw data to the formats expected by the entity.
     * Overwrite in entity classes for type conversions.
     *
     * Remove ids from $content and populate import properties
     * (_import_table, _import_row, _import_id, _import_ids)
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
                    'scope_field' => $this->fields_scope
                ];
            }
        }
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
     * Index Ids of the entity and all contained entities
     *
     * @param array $options Needs an index key
     *
     */
    public function collectIds($options = [])
    {
        if (!isset($options['index'])) {
            $options['index'] = [];
        }

        $this->indexIds($options['index']);
        $this->callRecursively('collectIds', null, $options);

        return $options['index'];
    }

    /**
     * Check dependencies: only can solve the parent_id if the scope fields have been solved
     *
     * // TODO: merge JobImport::_canSolve and _canSolve (create Index class?)
     *
     * @param $sourceLink
     * @param $targetLink
     * @return bool
     */
    protected function _canSolve(&$index, $sourceLink, $targetLink)
    {

        if (empty($sourceLink['scope_field'])) {
            return true;
        }

        if ($sourceLink['field'] != 'parent_id') {
            return true;
        }

        // Parent rows must be processed first
        $unsolvedRows = array_map(
            function ($x) {
                return $x['model'] . '-' . $x['id'];
            },
            Hash::extract($index['sources'], '{*}.{n}')
        );

        $targetRow = $targetLink['model'] . '-' . $targetLink['id'];
        if (in_array($targetRow, $unsolvedRows)) {
            return false;
        }

        // Scope of child records mus be filled first
        $unsolvedFields = array_map(
            function ($x) {
                return $x['model'] . '-' . $x['id'] . '-' . $x['field'];
            },
            Hash::extract($index['sources'], '{*}.{n}')
        );

        $sourceField = $sourceLink['model'] . '-' . $sourceLink['id'] . '-' . $sourceLink['scope_field'];
        if (in_array($sourceField, $unsolvedFields)) {
            return false;
        }

        return true;
    }

    /**
     * Get an array of sources and their respective targets that are solved.
     * Solved sources are removed from the index.
     *
     * // TODO: merge ImportBehavior::getSolvedIds and _getSolvedIds (create Index class?)
     *
     * @return array Each item in the array has a source and a target key
     *               containing the item from the index
     */
    protected function _getSolvedIds(&$index)
    {
        $links = [];

        $solvedTargets = array_intersect_key($index['targets'] ?? [], $index['sources'] ?? []);

        foreach ($solvedTargets as $targetId => $targetLink) {
            $solvedSources = $index['sources'][$targetId];
            foreach ($solvedSources as $sourceNo => $sourceLink) {

                if ($this->_canSolve($index, $sourceLink, $targetLink)) {
                    $links[] = [
                        'source' => $sourceLink,
                        'target' => $targetLink
                    ];

                    unset($index['sources'][$targetId][$sourceNo]);
                }
            }
            if (empty($index['sources'][$targetId])) {
                unset($index['sources'][$targetId]);
            }
        }
        return $links;
    }

    /**
     * Get the lookup index for solving links
     *
     * @return array
     */
    protected function &_getIndex(): ?array
    {
        if (!isset($this->_lookup['index'])) {
            $this->_lookup['index'] = [];
        }

        $index = &$this->_lookup['index'];
        return $index;
    }

    /**
     * Link records
     *
     * Updates foreign key fields with the respective IDs as soon as
     * a matching record occurs in the index.
     *
     * // TODO: merge JobImport::_solveLinks and saveLinks (create Index class? Trait? Interface?)
     *
     * @return bool
     */
    public function saveLinks()
    {
        $index = &$this->_getIndex();
        $this->collectIds(['index' => &$index]);

        $result = true;
        while ($links = $this->_getSolvedIds($index)) {

            $sourceModels = collection($links)->groupBy('source.model');
            foreach ($sourceModels as $modelName => $sourceLinks) {

                $sourceModel = $this->fetchTable($modelName);
                $sourceIds = collection($sourceLinks)->groupBy('source.id')->toArray();

                $rows = $sourceModel->find('all')
                    ->where(['id IN' => array_keys($sourceIds)])
                    ->formatResults(function ($results) use ($sourceIds) {
                        return $results->map(function ($row) use ($sourceIds) {
                            foreach ($sourceIds[$row->id] as $sourceLink) {
                                $row[$sourceLink['source']['field']] = $sourceLink['target']['id'];
                            }
                            return $row;
                        });
                    });
                $result = $result && $sourceModel->saveMany($rows);
                // TODO: add errors to the entity
            }
        }

        return $result;
    }

    /**
     * Get all tags in xml fields
     *
     * @param string|array $fieldName The fieldname, e.g. "value".
     *                                JSON keys can be provided using dot notation, e.g. "value.longitude".
     *                                If the fieldname is null, then all fields and child entities will be visited.
     * @param bool $content Whether to extract the text content of the tags
     * @param bool $recurse Whether to recurse into child entities
     *
     * @return array An array containing tag IDs as keys and an array with from-keys.
     */
    public function extractXmlTags($fieldName = null, $content = false, $recurse = false)
    {
        $tags = [];

        // Iterate all fields and recurse into child entities
        if ($fieldName === null) {
            $fields = $this->getExportFields(['snippets' => ['comments']]);
            $fields[] = 'footnotes'; //TODO: Why added manually
            foreach ($fields as $oldName => $fieldConfig) {

                $fieldKey = is_array($fieldConfig) ?
                    $fieldConfig['key'] ?? $oldName :
                    (is_numeric($oldName) ? $fieldConfig : $oldName);

                if (isset($this->{$fieldKey})) {
                    $value = $this->{$fieldKey};

                    // Entity
                    if (($value instanceof BaseEntity) && ($this->root === $value->root)) {
                        $tags += $value->extractXmlTags(null, $content, $recurse);
                    }

                    // Array
                    elseif (is_array($value) && $recurse) {
                        foreach ($value as $row) {
                            if ($row instanceof BaseEntity && ($this->root === $row->root)) {
                                $tags += $row->extractXmlTags(null, $content, $recurse);
                            }
                        }
                    }

                    // Single value
                    else {
                        $tags += $this->extractXmlTags($fieldKey, $content, $recurse);
                    }
                }
            }
        }

        // Extract tags from a field
        else {

            $fieldName = is_array($fieldName) ? $fieldName : explode('.', $fieldName);
            $format = $this->getFieldFormat($fieldName);

            if ($format === 'xml') {
                $value = $this->getValueRaw($fieldName);
                try {
                    $fieldTags = XmlMunge::getXmlElements($value, $content);
                } catch (Exception $e) {
                    $this->setError($fieldName[0], $e->getMessage());
                    $fieldTags = [];
                }

                $entity = $this;
                $fieldTags = array_combine(
                    array_keys($fieldTags),
                    array_map(
                        function ($tagid, $tag) use ($entity, $fieldName, $content) {
                            $tag = [
                                'tab' => $this->tableName,
                                'id' => $entity->id,
                                'field' => $fieldName[0],
                                'tagid' => $tagid,
                                'tagname' => ($content && is_array($tag)) ? $tag['name'] : $tag,
                                'content' => ($content && is_array($tag)) ? $tag['content'] : null
                            ];

                            $tag = new Tag($tag,
                                [
                                    'source' => 'Epi.links',
                                    'useSetters' => false,
                                    'markClean' => true,
                                    'markNew' => false
                                ]
                            );
                            $tag->container = $entity;
                            $tag->root = $entity->root;
                            return $tag;
                        },
                        array_keys($fieldTags),
                        $fieldTags
                    )
                );

                $tags += $fieldTags;
            }
        }

        return $tags;
    }

    /**
     * Inject link data into XML elements
     *
     * Adds the attributes data-link-target and data-link-value.
     *
     * @param string $value The XML text
     * @param array $options Options passed to getValueFormatted() when rendering to_id and to_caption
     * @param string $fieldName The field name
     * @return false|mixed|string
     *
     */
    public function injectXmlAttributes($value = null, $options = [], $fieldName = '')
    {
        $links = $this->root->links_by_tagid ?? [];
        $footnotes = $this->root->footnotes_by_tagid ?? [];

        $entity = $this;
        $callback_ids = static function (&$element, &$parser) use ($links, $footnotes, $options, $entity, $fieldName) {
            if ($element['position'] == 'open') {
                if (!empty($element['attributes']['id']) && !empty($links[$element['attributes']['id']])) {
                    //TODO: unnest duplicate tag IDs
                    $link = $links[$element['attributes']['id']][0];

                    $element['attributes']['data-link-target'] = $link->getValueFormatted('to_id', $options);
                    $element['attributes']['data-link-iri'] = $link->getValueFormatted('to_iri_path', $options);
                    $element['attributes']['data-link-value'] = $link->getValueFormatted('to_caption', $options);

                    if (!isset($link->type->config['attributes']['value'])) {
                        unset($element['attributes']['value']);
                    }
                }
                elseif (!empty($element['attributes']['id']) && empty($footnotes[$element['attributes']['id']])) {
                    $entity->setLinkError(
                        is_array($fieldName) ? implode('.', $fieldName) : $fieldName,
                        __('Missing link record for element ID {0}. ', $element['attributes']['id'])
                    );
                }

            }
            return true;
        };

        if (empty($value) || empty($links)) {
            return $value;
        }
        else {
            return XmlMunge::parseXmlString($value, $callback_ids);
        }
    }

    /**
     * Query select input options from the database
     *
     * @param string $modelName Model name including the plugin prefix (e.g. Epi.Properties)
     * @param array $conditions Where condition
     * @param string $fieldName The field with the values
     * @return Query
     */
    public function getOptions($modelName, $conditions, $fieldName): Query
    {
        $options = $this->fetchTable($modelName)
            ->find('list', ['keyField' => 'id', 'valueField' => $fieldName])
            ->where($conditions);

        return $options;
    }

    /**
     * Get field caption
     *
     * @param $fieldName
     *
     * @return array|mixed|string|string[]
     */
    public function getFieldCaption($fieldName)
    {
        $fieldName = is_array($fieldName) ? $fieldName : explode('.', $fieldName);

        if (sizeof($fieldName) === 1) {
            $field = $this->type['merged']['fields'][$fieldName[0]] ?? $fieldName[0];
        }
        elseif (sizeof($fieldName) > 1) {
            $field = $this->type['merged']['fields'][$fieldName[0]]['keys'][$fieldName[1]] ?? $fieldName[1];
        }

        return is_string($field) ? $field : ($field['caption'] ?? $fieldName);
    }


    /**
     * Get the field format from the type the entity belongs to
     * (if it is loaded)
     *
     * @param string|array $fieldName
     * @return string
     */
    public function getFieldFormat($fieldName)
    {
        $fieldName = is_array($fieldName) ? $fieldName : explode('.', $fieldName);

        $default = $this->_fields_formats[$fieldName[0]] ?? 'raw';
//        if ($default !== 'raw') {
//            return $default;
//        }

        $type = $this->type ?? [];
        if (empty($type)) {
            return $default;
        }

        if ((sizeof($fieldName) === 1)) {
            return $type->config['fields'][$fieldName[0]]['format'] ?? $default;
        }

        if ((sizeof($fieldName) > 1)) {
            return $type->config['fields'][$fieldName[0]]['keys'][$fieldName[1]]['format'] ?? $default;
        }

        return $default;
    }

    /**
     * Get whether the field is public
     *
     * @param string|array $fieldName
     * @param array $options
     * @return string
     */
    public function getFieldIsVisible($fieldName, $options = [])
    {
        $userRole = $this->root->currentUserRole ?? 'guest';
        $requestPublished = $options['published'] ?? \App\Model\Table\BaseTable::$requestPublished ?? [];
        $requestAction = \App\Model\Table\BaseTable::$requestAction ?? null;

        // Only show the published field in edit actions or when explicitly requested
        if (in_array($fieldName, ['published'])) {
            return ($userRole !== 'guest') && (empty($requestPublished) || ($requestAction !== 'view'));
        }

        // Logged in users can see all fields
        if ($userRole !== 'guest') {
            return true;
        }

        // ID fields are always public by default
        $fieldName = is_array($fieldName) ? $fieldName : explode('.', $fieldName);
        if (in_array($fieldName[0], static::$_fields_ids) || isset(static::$_fields_ids[$fieldName[0]])) {
            $default = true;
        }
        else {
            $default = false;
        }

        // Check the type config
        if ((sizeof($fieldName) === 1)) {
            return $this->htmlFields[$fieldName[0]]['public'] ?? $default;
        }
        elseif ((sizeof($fieldName) > 1)) {
            return $this->htmlFields[$fieldName[0]]['keys'][$fieldName[1]]['public'] ?? $default;
        }
        else {
            return $default;
        }
    }

    /**
     * Get the field config from the type the entity belongs to
     * (and loads the type entity if not already loaded)
     *
     * @param string|array $fieldName
     * @return array
     */
    public function getFieldConfig($fieldName)
    {
        //TODO: merge default config from getHtmlFields, e.g. for items showing the published field
        $fieldName = is_array($fieldName) ? $fieldName : explode('.', $fieldName);

        if (!empty($this->type) && (sizeof($fieldName) === 1)) {
            return $this->type->config['fields'][$fieldName[0]] ?? [];
        }
        elseif (!empty($this->type) && (sizeof($fieldName) > 1)) {
            return $this->type->config['fields'][$fieldName[0]]['keys'][$fieldName[1]] ?? [];
        }
        else {
            return [];
        }
    }


    /**
     * Get unformatted value
     *
     * @param $fieldName
     * @return false|mixed|string|null
     */
    public function getValueUnformatted($fieldName)
    {
        $raw = $this->get($fieldName);

        $format = $this->getFieldFormat($fieldName);
        if ($format === 'xml') {
            try {
                return $this->table->deRenderXmlFields($raw);
            } catch (Exception $e) {
                $this->setParsingError($fieldName, __('Error encoding XML: {0}', [$e->getMessage()]));
//                return __('Error encoding XML: {0}', [$e->getMessage()]);
                return $raw;
            }

        }
        elseif ($format === 'json') {
            try {
                $jsonOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR;
                return json_encode($raw, $jsonOptions);
            } catch (Exception $e) {
                $this->setParsingError($fieldName, $e->getMessage());

                // TODO: better return $raw although not a string?
                return __('Error encoding JSON: {0}', [$e->getMessage()]);
            }
        }
        elseif (($format === 'geodata') && !is_string($raw)) {
            try {
                $jsonOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR;
                return json_encode($raw, $jsonOptions);
            } catch (Exception $e) {
                $this->setParsingError($fieldName, $e->getMessage());

                // TODO: better return $raw although not a string?
                return __('Error encoding JSON: {0}', [$e->getMessage()]);
            }
        }
        else {
            return $raw;
        }
    }

    /**
     * Log a parsing error, e.g. when XML content could not be parsed
     *
     * @param string $fieldname The field that could not be parsed
     * @param string $error The error message
     * @return void
     */
    public function setParsingError($fieldname, $error)
    {
        if (!isset($this->_parsing_errors[$fieldname])) {
            $this->_parsing_errors[$fieldname] = [];
        }
        $this->_parsing_errors[$fieldname][] = $error;
    }

    /**
     * Get a list of messages regarding parsing errors.
     *
     * @return string[]
     */
    protected function _getParsingErrors()
    {
        $output = [];

        foreach ($this->_parsing_errors as $field => $errors) {
            foreach ($errors as $error) {
                $output[] = "Error in $field: $error";
            }
        }

        return $output;
    }

    /**
     * Log a link error, e.g. when a link record for a tag ID could not be found
     *
     * @param string $fieldname
     * @param string $error The error message
     * @return void
     */
    public function setLinkError($fieldname, $error)
    {
        if (!isset($this->_link_errors[$fieldname])) {
            $this->_link_errors[$fieldname] = [];
        }
        $this->_link_errors[$fieldname][] = $error;
    }

    /**
     * Get a list of messages regarding link errors.
     *
     * @return string[]
     */
    protected function _getLinkErrors()
    {
        $output = [];

        foreach ($this->_link_errors as $field => $errors) {
            foreach ($errors as $error) {
                $output[] = "Link error in $field: $error";
            }
        }

        return $output;
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
        $format = $this->getFieldFormat($fieldName);

        if ($format === 'date') {
            $content = $this->get($fieldName[0] . '_value');
            return ($content === '') || ($content === null);
        }
        elseif ($format === 'file') {
            $content = $this->get($fieldName[0] . '_name');
            return ($content === '') || ($content === null);
        }
        elseif ($format === 'record') {
            return empty($this->get($fieldName[0] . '_id'));
        }
        elseif ($format === 'relation') {
            return empty($this->get($fieldName[0] . '_id'));
        }
        elseif ($format === 'property') {
            return empty($this->get('properties_id'));
        }
        else {
            $content = empty($fieldName[0]) ? '' : $this->get($fieldName[0]);
            return ($content === '') || ($content === null);
        }
    }

    /**
     * Get type path
     *
     * @return string
     */
    protected function _getTypePath()
    {

        $typeField = $this->table->typeField ?? null;
        if (empty($typeField)) {
            return '';
        }

        $path = $this->_fields[$typeField];
        if (!empty($this->container)) {
            $path = $this->container->typePath . '.' . $path;
        }

        return $path;
    }

    /**
     * Get types of an entity as defined in the config
     *
     * @return array|\Cake\ORM\Query
     */
    protected function _getTypeOptions()
    {
        if (!$this->table->hasAssociation('Types')) {
            return [];
        }

        return $this->table->Types
            ->find('list', ['keyField' => 'name', 'valueField' => 'caption'])
            ->order(['scope' => 'ASC', 'sortno' => 'ASC']);
    }

    /**
     * Virtual field database name
     *
     * @return string The database name, with prefix
     */
    protected function _getDatabaseName()
    {
        return method_exists($this->table, 'getDatabaseName') ? $this->table->getDatabaseName() : '';
    }

    /**
     * Virtual field database as it is exposed in article and project entities
     *
     * TODO: can this be ommited in favor of _getDatabaseName() ? -> still needed for $_serialize_fields
     *
     * @return string
     */
    protected function _getDatabase()
    {
        return Databank::removePrefix($this->databaseName);
    }

    /**
     * Get the URL of the epigraf article
     *
     * @return string
     */
    protected function _getInternalUrl()
    {
        return '/epi/'
            . Databank::removePrefix($this->databaseName)
            . '/' . $this->tableName . '/view/'
            . $this->id;
    }

    /**
     * Get XML tag
     *
     * @return mixed
     */
    protected function _getXmlTag()
    {
        return $this->_xml_tag ?? Inflector::underscore(substr(strrchr(get_class($this), '\\'), 1));
    }


    /**
     * Get the filepath relative to the database directory
     *
     * @return string
     */
    protected function _getFileCopyfrom()
    {
        if (!$this->hasDatabaseField('file_name')) {
            return '';
        }

        $root = Configure::read('Data.databases') . Databank::addPrefix($this->databaseName) . DS;
        $filepath = $this->file_fullpath . $this['file_name'];
        return is_file($root . $filepath) ? $filepath : '';
    }

    /**
     * Get URLs and filename data of file (virtual field)
     *
     * @return array
     */
    protected function _getFileProperties()
    {
        if ($this->_file_properties) {
            return $this->_file_properties;
        }

        $baseUrl = $this->type->config['fields']['file']['baseurl'] ?? '';

        // No file
        if (!$this->hasDatabaseField('file_name') || empty($this->file_name)) {
            $props = ['exists' => false];
        }

        // Images by URL
        elseif (!empty($baseUrl)) {
            if ($this->hasDatabaseField('file_path')) {
                $downloadpath = rtrim($this['file_path'] ?? '', '/');
                $fullfilename = $downloadpath . '/' . $this['file_name'];
            }
            else {
                $downloadpath = '';
                $fullfilename = $this['file_name'];
            }

            $viewurl = $displayurl = $thumburl = $baseUrl . $fullfilename;
            $exists = true;

            $filename = $this['file_name'];
            $basename = pathinfo($this['file_name'], PATHINFO_FILENAME);
            $extension = pathinfo($this['file_name'], PATHINFO_EXTENSION);
            $preview = (bool)(int)in_array($extension, ['png', 'jpg', 'svg']) * (int)$exists;

            $props = [
                'path' => $downloadpath,
                'name' => $basename,
                'extension' => $extension,
                'group' => $filename . DS . $basename,
                'filepath' => $fullfilename,
                'exists' => $exists,
                'preview' => $preview,
                'url_view' => $viewurl,
                'url_thumb' => $thumburl,
                'url_display' => $displayurl
            ];
        }

        // Images from the server
        else {
            $root = Configure::read('Data.databases') . Databank::addPrefix($this->databaseName) . DS;

            $filename = $this->file_downloadname;
            $basename = pathinfo($this['file_name'], PATHINFO_FILENAME);
            $extension = pathinfo($this['file_name'], PATHINFO_EXTENSION);

            $downloadpath = rtrim($this->file_downloadpath ?? '', '/');
            $fullfilename = $downloadpath . '/' . $filename;

            $exists = is_file($root . $fullfilename);
            $preview = (bool)(int)in_array($extension, ['png', 'jpg', 'svg']) * (int)$exists;

            $viewurl = Router::url([
                'controller' => 'Files',
                'action' => 'view',
                'database' => Databank::removePrefix($this->databaseName),
                'plugin' => 'Epi',
                '?' => [
                    'path' => $downloadpath,
                    'filename' => $filename
                ]
            ]);

            $displayurl = Router::url([
                'controller' => 'Files',
                'action' => 'display',
                'database' => Databank::removePrefix($this->databaseName),
                'plugin' => 'Epi',
                '?' => [
                    'path' => $downloadpath,
                    'filename' => $filename
                ]
            ]);

            $thumburl = Router::url([
                'controller' => 'Files',
                'action' => 'download',
                'database' => Databank::removePrefix($this->databaseName),
                'plugin' => 'Epi',
                '?' => [
                    'path' => $downloadpath,
                    'filename' => $filename,
                    'format' => 'thumb',
                    'size' => 400
                ]
            ]);

            $props = [
                'exists' => $exists,
                'preview' => $preview,

                'root' => $root,
                'path' => $downloadpath,
                'name' => $basename,
                'extension' => $extension,

                'filepath' => $fullfilename,
                'group' => $filename . DS . $basename,
                'metadata' => $this->type->config['metadata'] ?? [],

                'url_view' => $viewurl,
                'url_thumb' => $thumburl,
                'url_display' => $displayurl
            ];

        }

        $this->_file_properties = $props;
        return $props;
    }

    /**
     * Return whether the entity has a file that exists on the server or is an online image
     *
     * @return boolean
     */
    protected function _getFileExists()
    {
        return $this->file_properties['exists'] ?? false;
    }

    /**
     * Get the file path including the base path
     *
     * @return string
     */
    protected function _getFileFullpath()
    {
        $path = $this->type->config['fields']['file']['baseurl'] ?? '';

        if (empty($path)) {
            $path = rtrim($this->file_basepath ?? '', '/');
        }

        if ($this->hasDatabaseField('file_path')) {
            $path = $path . '/' . rtrim($this['file_path'] ?? '', '/');
        }
        return $path . '/';
    }

    protected function _getThumbUrl()
    {
        if (empty($this['file_name'])) {
            return '';
        }

        $baseUrl = $this->type->config['fields']['file']['baseurl'] ?? '';

        // Server files
        if (empty($baseUrl)) {
            return $this->file_properties['url_thumb'];
        }

        // Online files
        else {
            return $baseUrl . $this['file_name'];
        }
    }

    /**
     * Get thumb properties: URL, caption, exists, published
     *
     * TODO: simplify image handling, merge some methods if possible
     *
     * @return array
     */
    protected function _getThumb()
    {
        if (empty($this->file_name)) {
            return [];
        }

        $baseUrl = $this->type->config['fields']['file']['baseurl'] ?? '';
        if (!empty($baseUrl)) {
            return [
                'thumburl' => ($baseUrl . $this->file_path . '/' . $this->file_name),
                'exists' => true,
                'published' => $this->publishedState >= PUBLICATION_PUBLISHED,
                'caption' => $this->content
            ];
        }
        else {
            return [
                'thumburl' => $this->file_properties['url_thumb'],
                'exists' => $this->file_properties['preview'] ?? false,
                'published' => $this->publishedState >= PUBLICATION_PUBLISHED,
                'caption' => $this->content
            ];
        }
    }

    /**
     * Copy the item image to a target folder
     *
     * Typical metadata fields:
     *
     * [
     *   'dc:title' => 'DI 103, Nr. 251 - Wismar, St. Georgen - 1479, 1681',
     *   'dc:rights' => 'Niedersächsische Akademie der Wissenschaften zu Göttingen (Jürgen Herold)',
     *   'xmpRights:UsageTerms' => 'CC BY-NC 4.0'
     * ]
     *
     * Adding exif data works for: jpg and jpeg files
     *
     * @param string $targetFolder The target folder, absolute path on the server
     * @param array $metadataConfig If a metadata configuration is provided, metadata is written to the files.
     *                              Each key is a metadata field in the file, each value is a placeholder extraction key.
     *                              Instead of a single placeholder, an array of placeholders can be provided.
     *                              In this case, the first value not evaluating to null or an empty string is used.
     *                              The placeholder extraction key is resolved from the perspective of an image item.
     *                              Example: ["xmpRights:UsageTerms" => "{file_licence}","dc:rights" => "{file_copyright}"]
     * @return boolean
     */
    public function copyImage($targetFolder, $metadataConfig)
    {
        if (!($this->file_properties['exists'] ?? false) || empty($this->file_properties['root'])) {
            return false;
        }

        $filename = $this->file_name;
        $sourceFolder = Files::joinPath([$this->file_properties['root'], $this->file_properties['path']]);
        $result = Files::copyFile($filename, $sourceFolder, $targetFolder);

        // Inject metadata (exif) into the file
        if ($result && in_array($this->file_properties['extension'], ['jpg', 'jpeg'])) {
            $metadataConfig = array_merge($this->file_properties['metadata'] ?? [], $metadataConfig);
            $metadata = [];
            foreach ($metadataConfig as $field => $placeholderArray) {
                $placeholderArray = !is_array($placeholderArray) ? [$placeholderArray] : $placeholderArray;
                $value = null;
                foreach ($placeholderArray as $placeholderString) {
                    $value = $this->getValuePlaceholder($placeholderString, ['format' => false]);
                    $value = is_array($value) ? array_map(fn($x) => strval($x), $value) : $value;
                    $value = is_array($value) ? implode(', ', $value) : $value;
                    if (!is_null($value) && ($value !== '')) {
                        break;
                    }
                }
                $metadata[$field] = $value;
            }
            $metadata = array_filter($metadata);
            $newFilename = $metadata['filename'] ?? $filename;
            unset($metadata['filename']);

            $result = Files::updateXmp(Files::joinPath([$targetFolder, $filename]), $metadata, true);
            if ($result && ($newFilename !== $filename)) {
                $result = Files::renameFile($targetFolder, $filename, $newFilename);
            }
        }

        return $result;
    }

    /**
     * Get local IRI
     *
     * @deprecated
     * A local IRI is prefixed with the database name, followed by the table name, row type and id.
     */
    protected function _getLocalIri()
    {

        $source = $this->fetchTable($this->getSource());
        $iri = Databank::removePrefix($source->getDatabaseName()) . ':' . $source->getTable() . '/';

        if ($source->typeField !== null) {
            $iri .= $this->{$source->typeField} . '/';
        }

        $iri .= $this->id;

        return $iri;
    }

    /**
     * Get public IRI
     *
     * @return mixed
     * @deprecated
     */
    protected function _getPublicIri()
    {
        return str_contains($this->norm_iri ?: ':', ':') ? $this->id : $this->norm_iri;
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
            return  '/epi/' . $this->database . '/iri/' . $iriPath;
        }
        return null;
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
     * Get the scope field of current table
     *
     * @return string
     */
    protected function _getFieldsScope()
    {
        return $this->table->scopeField;
    }

    /**
     * Get the base folder for file uploads
     *
     * All article related files are located in the 'articles' folder.
     *
     * @return string
     */
    protected function _getFileBasepath()
    {
        return ($this->root && ($this->root !== $this)) ? $this->root->file_basepath : parent::_getFileBasepath();
    }

    /**
     * Get the base folder for file uploads
     *
     * All article related files are located in the 'articles' folder.
     *
     * @return string
     */
    protected function _getFileDefaultpath()
    {
        return ($this->root && ($this->root !== $this)) ? $this->root->file_defaultpath : '';
    }

    /**
     * Get all footnotes linked to fields in the entity
     *
     * @return array|ResultSet
     */
    protected function _getFootnotes()
    {
        if (isset($this->_fields['footnotes'])) {
            return $this->_fields['footnotes'];
        }
        elseif (isset($this->root) && ($this->root !== $this)) {
            return $this->root->getFootnotesFrom($this->table->getTable(), $this->id);
        }
        else {
            return [];
        }
    }

    /**
     * Get all annotations linked to fields in the entity
     *
     * @return array|ResultSet
     */
    protected function _getAnnotations()
    {
        if (isset($this->_fields['links'])) {
            return $this->_fields['links'];
        }
        elseif (isset($this->root) && ($this->root !== $this)) {
            return $this->root->getLinksFrom($this->table->getTable(), $this->id);
        }
        else {
            return [];
        }
    }

    /**
     * Get all xml tags and their content
     *
     * @return array
     */
    protected function _getTags()
    {
        return $this->extractXmlTags(null, true);
    }

    /**
     * Generate triples based on the types config.
     *
     * Adds triples of the entity and its children as defined in $_children.
     * The configuration should contain the keys:
     * - namespaces: An keyed list of prefixes and IRIs.
     * - templates: A list of triple patterns.
     *
     * Each triple pattern contains the keys:
     * - comment: Optionally, add a comment to remember what the pattern should express
     * - subject: Placeholder string for the subject
     * - predicate: Placeholder string for the predicate
     * - object: Placeholder string for the object
     *
     * If a placeholder string generates multiple values, then multiple triples are generated.
     *
     * Reserved values in placeholder strings (see TtlView for details):
     * - Values rendered by placeholder strings must not start with underscores.
     * - Values rendered by placeholder strings that start with a configured namespace prefix followed by a colon must be IRIs.
     *
     * Configuration example:
     *
     * "triples" : {
     *   "namespaces": {
     *     "epi": "https://epigraf.inschriften.net/iri/"
     *     "rdf":"http://www.w3.org/1999/02/22-rdf-syntax-ns#",
     *     "xsd":"http://www.w3.org/2001/XMLSchema#",
     *     "schema":"https://schema.org/",
     *     "dcterms":"http://purl.org/dc/terms/",
     *   },
     *   "templates": [
     *     {
     *       "comment": "The article is an article",
     *       "subject": "epi:{iri}",
     *       "predicate": "rdf:type",
     *       "object": "schema:Article"
     *     },
     *     {
     *       "comment": "The article has an author",
     *       "subject": "epi:{iri}",
     *       "predicate": "schema:author",
     *       "object": "{creator.name}"
     *     },
     *     {
     *       "comment": "The article has a title",
     *       "subject": "epi:{iri}" ,
     *       "predicate": "schema:title",
     *       "object": "{name}"
     *     },
     *     {
     *       "comment": "The article contains references",
     *       "subject": "epi:{iri}" ,
     *       "predicate": "dcterms:relation",
     *       "object": "epi:{sections.*[sectiontype=references].items.*[itemtype=literature].property.lemma}"
     *     }
     *   ]
     * }
     *
     * @param array $options In the options, provide a format key (html, ttl...)
     * @return array
     */
    public function getExportTriples($options = []): array
    {

        if ($options['setRoot'] ?? false) {
            $this->prepareRoot();
        }

        // Set tree positions if requested
        if ($options['prepareTree'] ?? false) {
            $this->prepareTree();
        }

        if (!$this->getEntityIsVisible($options)) {
            return [];
        }

        $tripleConfig = $this->type->merged['triples']['templates'] ?? [];

        $triples = [];
        foreach ($tripleConfig as $config) {
            // Get values
            $data = ['subject' => null, 'predicate' => null, 'object' => null];
            foreach ($data as $part => $value) {
                $value = $this->getValuePlaceholder($config[$part], $options);
                if (!isset($value) || ($value === null) || ($value === '') || ($value === []) || ($value === [""])) {
                    continue 2;
                }
                $data[$part] = $value;
            }

            // Fill values
            $triples = array_merge($triples, Arrays::array_recycle($data));
        }

        // Add section triples if available and not a collection
        if (!empty($this->_children)) {
            foreach (($this[$this->_children] ?? []) as $child) {
                $triples = array_merge($triples, $child->getExportTriples($options));
            }
        }

        return $triples;
    }

    /**
     * Generate geo data based on the types config.
     *
     * Add the geodata key to the types configuration and
     * set its value to the field that contains the geodata.
     *
     * Example:
     *
     * "geodata": "value"
     *
     * TODO: Do we need the distinction between getDateGeoData() and getExportGeoData(),
     *       analog to the triples generator?
     *
     * @param array $options In the options, provide a format key (html, ttl...)
     * @return array
     */
    public function getExportGeoData($options = []): array
    {

        if ($options['setRoot'] ?? false) {
            $this->prepareRoot();
        }

        if (!$this->getEntityIsVisible($options)) {
            return [];
        }

        $geodata = [];
        $format = $options['format'] ?? 'geojson';
        $geoDataField = $this->type->merged['geodata'] ?? [];

        if (!empty($geoDataField) && is_string($geoDataField)) {

            $extraData = [
                'id' => $this->id,
                'segment' => $this->type->name ?? ''
            ];

            if (!empty($options['properties'])) {
                $root = empty($this->container) ?  $this->root : ($this->container->root ?? $this->container);
                $extraData['properties'] = $root->getMatchedProperties($options['properties']);
            }

            $geoValue = $this->getValueNested($geoDataField, ['format' => $format, 'geodata' => $extraData]);
            if (!empty($geoValue)) {
                $geodata[] = $geoValue;
            }
        }

        // Add geo data from children
        if (!empty($this->_children)) {
            foreach (($this[$this->_children] ?? []) as $child) {
                $geodata = array_merge($geodata, $child->getExportGeoData($options));
            }
        }

        return $geodata;
    }

    /**
     * Get selected fields
     *
     * The export fields can be determined in three ways
     *
     * 1. By setting the columns in the controller, e.g.
     *    $this->viewBuilder()->setOption('options', compact('params', 'template', 'columns'));
     *    All options are passed through by the export view classes.
     *
     * 2. By setting the template 'table' and
     *    providing column definitions in the types config.
     *    The column selection can be narrowed down
     *    using the fields option.
     *
     * 3. By using $this->_serialize_fields and $this->_serialize_snippets
     *    of the entity.
     *
     * @param array $options
     * @return array
     */
    public function getExportFields($options)
    {

        $template = $options['template'] ?? 'view';
        $columns = $options['columns'] ?? [];

        // Get fields from the 'columns' option
        if (!empty ($columns)) {
            $selected = array_filter($columns, fn($x) => ($x['selected'] ?? false));
            $selected = empty($selected) ? array_filter($columns, fn($x) => ($x['default'] ?? false)) : $selected;

            return $selected;
        }

        // Get fields from the types config
        if ($template === 'table') {
            // Column config
            $columns = $this->type['merged']['columns'] ?? [];

            $columns = collection($columns)
                ->map(function ($item) {
                    $item['name'] = $item['name'] ?? $item['key'];
                    return $item;
                })
                ->indexBy("name")
                ->toArray();

            $fields = $options['fields'] ?? [];
            $fields = empty($fields ?? []) ? Hash::extract($columns, '{*}[default=1].name') : $fields;
            $columns = array_filter($columns, fn($x) => in_array($x['name'], $fields) | !count($fields));
            $columns = array_merge($fields, $columns);

            return $columns;
        }

        // Get fields from the serialize properties
        else {
            $fields = $this->_serialize_fields;

            // Add snippet fields
            if (!empty($this->_serialize_snippets)) {
                $snippetFields = array_intersect_key(
                    $this->_serialize_snippets,
                    array_flip($options['snippets'] ?? [])
                );

                $snippetFields = array_reduce($snippetFields, fn($carry, $item) => array_merge($carry, $item), []);
                $fields = array_merge($snippetFields, $fields);
            }

            return $fields;
        }
    }

    /**
     * Create an array of values for serialization
     *
     *
     * ### Options
     * Options processed in getExportValues
     * - setIri (boolean) Defaults to false.
     * - clearIri (boolean) Will skip IRIs for non-root entities. Defaults to false.
     * - prefixIds (string|boolean) The prefix added to ID fields. Defaults to false.
     * - prepareTree (boolean) Prepare tree properties before getting export values.
     * - removeEmpty (boolean) Defaults to true.
     * - parseField (boolean) Defaults to true.
     * - seed (boolean) Whether this is the seed node, the entry point in getDataForExport() or getDataForTransfer().
     * - copy (boolean) Whether the goal is to copy an entity with all dependencies. Defaults to false.
     *
     * Options passed to getValueFormatted:
     * - format (string) Defaults to 'xml'
     *
     * Options passed to getExportFields
     * - snippets
     *
     * @param $options
     * @return array
     */
    public function getExportValues($options)
    {
        // Set root if requested
        // TODO: always set root?
        if ($options['setRoot'] ?? false) {
            $this->prepareRoot();
        }

        // Set IRI if requested
        if ($options['setIri'] ?? false) {
            $this->prepareIri();
        }

        // Set tree positions if requested
        if ($options['prepareTree'] ?? false) {
            $this->prepareTree();
        }

        // Get values
        $fields = $this->getExportFields($options);
        $values = [];

        // Clear IRIs
        if (($options['clearIri'] ?? false) && ((!$this instanceof RootEntity))) {
            unset($fields['norm_iri']);
            $fields = array_filter($fields, fn($v) => $v !== 'norm_iri');
        }

        // Rename types fields
        if (($options['types'] ?? '') === 'merge' ) {
            $typeField = $this->_fields_import['type'] ?? '';
            $fieldIdx = array_search($typeField, $fields);
            if (!empty($typeField) && $fieldIdx !== false) {
                $fields[$fieldIdx] = is_array($fields[$fieldIdx]) ? $fields[$fieldIdx] : ['key' => $typeField];
                $fields[$fieldIdx]['name'] = 'type';
            }
        }

        // Rename, remove and parse fields
        foreach ($fields as $oldName => $fieldConfig) {

            if (is_array($fieldConfig)) {
                $fieldName = $fieldConfig['name'] ?? $oldName;
                $fieldKey = $fieldConfig['key'] ?? $oldName;

                //$value = is_object($entity) ? $entity->getValueNested($x) : Objects::extract($entity, $x);
                $fieldConfig = array_merge($options, $fieldConfig);
                $fieldConfig['format'] = ($options['formatFields'] ?? false) ? ($options['format'] ?? 'xml') : false;
                $fieldConfig['aggregate'] = $fieldConfig['aggregate'] ?? 'collapse';
                $value = $this->getValueNested($fieldKey, $fieldConfig);
            }
            else {
                $fieldName = $fieldConfig;
                $oldName = is_numeric($oldName) ? $fieldName : $oldName;

                // Rename fields
                if (($oldName == !$fieldName) && isset($this->{$oldName})) {
                    $valueName = $oldName;
                }
                else {
                    $valueName = $fieldName;
                }

                // Parse XML fields and encode JSON
                // TODO: mark formatted fields
                // TODO: getNestedData here instead of double loop in getNestedData???
                if ($options['formatFields'] ?? true) {
                    $options['format'] = $options['format'] ?? 'xml';
                    $value = $this->getValueFormatted($valueName, $options);
                }
                else {
                    $value = $this->{$valueName};
                }

                // Prefix copies
                if (($options['copy'] ?? false) && ($options['seed'] ?? false)) {
                    $displayField = $this->table->getDisplayField();
                    if ($fieldName === $displayField) {
                        $value = 'Copy: ' . $value;
                    }
                }
            }

            // Skip empty fields if requested
            if (
                (!($options['removeEmpty'] ?? true)) ||
                !(
                    is_null($value) ||
                    (is_string($value) && ($value === '')) ||
                    (is_bool($value) && empty($value)) ||
                    (is_array($value) && empty($value))
                )
            ) {
                $values[$fieldName] = $value;
            }
        }

        // Additional snippets
        if ($options['files'] ?? false) {
            $values['file_copyfrom'] = $this->file_copyfrom;
        }

        if (in_array('tags', $options['snippets'] ?? [])) {
            $values['tags'] = array_values($this->tags);
        }

        // XML tags and attributes
        if ($options['format'] === 'xml') {
            $xmlAttributes = $this->getExportAttributes($options);
            if (($options['removeEmpty'] ?? true)) {
                $xmlAttributes = array_intersect($xmlAttributes, array_keys($values));
            }
            $values['_xml_attributes'] = $xmlAttributes;
            $values['_xml_tag'] = $this->xml_tag;
        }

        return $values;
    }

    /**
     * Get export attributes
     *
     * @param $options
     * @return array|mixed
     */
    public function getExportAttributes($options)
    {
        return $this->_serialize_attributes;
    }

    /**
     * Return a nested array of all entities with export ready data
     *
     * @param array $options passed to getExportValues
     * @return array|mixed[]
     */
    public function getDataNested($options = [], $container = null)
    {
        $data = $this->getExportValues($options);
        $out = [];
        unset($options['columns']);

        // Recurse
        // TODO: recurse inside of getExportValues???
        foreach ($data as $key => $value) {

            // One entity
            if (($value instanceof BaseEntity) && $value->getEntityIsVisible($options)) {
                $out[$key] = $value->getDataNested($options, $this);
            } // List of entities
            elseif (is_array($value)) {
                foreach ($value as $idx => $row) {
                    if (($row instanceof BaseEntity) && $row->getEntityIsVisible($options)) {
                        $out[$key][$idx] = $row->getDataNested($options, $this);
                    }
                    elseif (!($row instanceof BaseEntity)) {
                        $out[$key][$idx] = $row;
                    }
                }
            }
            elseif (!($value instanceof BaseEntity)) {
                $out[$key] = $value;
            }
        }

        return $out;
    }

    /**
     * Return triples from the entities with export ready data
     *
     * @param array $options passed to getExportValues
     * @return array
     */
    public function getDataTriples($options)
    {

        if (!$this->getEntityIsVisible($options)) {
            return [];
        }

        // The index action indicates a collection
        if (($options['params']['action'] ?? 'view') === 'index') {
            $base = Router::url('/iri/', true);
            $namespaces = [
                'epi' => $base,
                'rdf' => SERIALIZE_NAMESPACES['rdf'],
                'hydra' => SERIALIZE_NAMESPACES['hydra']
            ];
            return ['member' => 'epi:' . $this->iri, 'namespaces' => $namespaces, 'base' => $base];
        }

        // Otherwise, let the entity generate triples from the configuration
        else {
            $namespaces = $this->type->merged['triples']['namespaces'] ?? [];
            $base = $this->type->merged['triples']['base'] ?? '';
            $triples = $this->getExportTriples($options);
            return compact('namespaces', 'triples', 'base');
        }

    }

    /**
     * Return geo data from the entities with export ready data
     *
     * @param array $options passed to getExportValues
     * @return array
     */
    public function getDataGeo($options)
    {
        if (!$this->getEntityIsVisible($options)) {
            return [];
        }

        $geodata = $this->getExportGeoData($options);
        return compact('geodata');
    }

    /**
     * Get all property IDs referenced in the article
     *
     * @return \Generator Property IDs
     */
    public function getDataProperties() {
        // Yield nothing, overwrite in child classes
        if (false) {
            yield;
        }
    }


    /**
     * Return a flat array of all nested entities with export ready data
     *
     * Nested Entities (not nested arrays) can be converted into wide format,
     * e.g. the project of an article, if you set $options['params']['shape']
     * to 'wide'.
     *
     * All other nested entities are returned in long format.
     *
     * @param array $options Options passed to getExportValues.
     * @param boolean|string $keyCol If set, the path to the current entity is inserted into this field.
     * @param array $keyValues The path to the current entity.
     * @return array
     */
    public function getDataUnnested($options, $keyCol = false, $keyValues = [])
    {
        $shape = $options['params']['shape'] ?? 'long';
        $nestedrows = [];

        $data = [];

        // Store extraction key
        if (!empty($keyCol)) {
            $data[$keyCol] = implode('.', $keyValues);
        }

        // Clear first level entities
        if (empty($keyValues) && !empty($options['clear'])) {
            $data['_action'] = 'clear';
        }

        //$data['table'] = $data['table_name'] ?? '';

        $data = array_merge($data, $this->getExportValues($options));
        unset($options['columns']);
        unset($options['seed']);

        // Recurse
        foreach ($data as $key => $value) {

            // One entity
            if (($data instanceof BaseEntity) && (!$value->getEntityIsVisible($options))) {
                unset($data[$key]);
            }

            elseif ($value instanceof BaseEntity) {
                $nestedKey = array_merge($keyValues, [$key]);
                $data[$key] = $value->getValueFormatted('id', $options) ?? null;
                $newrows = $value->getDataUnnested($options, $keyCol, $nestedKey);

                // Wide format
                if (!empty($newrows) && ($shape === 'wide')) {
                    $entityrow = array_pop($newrows);
                    unset($entityrow[$keyCol]);
                    unset($entityrow['id']);
                    $entityrow = array_combine(
                        array_map(function ($k) use ($key) {
                            return $key . '.' . $k;
                        }, array_keys($entityrow)),
                        $entityrow
                    );

                    $data = array_merge($data, $entityrow);
                }

                $nestedrows = array_merge($nestedrows, $newrows);
            }

            // List of entities
            elseif (is_array($value)) {
                $nestedarray = [];
                foreach ($value as $idx => $row) {
                    if ($row instanceof BaseEntity && ($row->getEntityIsVisible($options))) {
                        $nestedKey = array_merge($keyValues, [$key, $idx]);
                        $newrows = $row->getDataUnnested($options, $keyCol, $nestedKey);
                        $nestedrows = array_merge($nestedrows, $newrows);
                    }
                    elseif (!($row instanceof BaseEntity)) {
                        $nestedarray[$idx] = $row;
                    }
                }

                if (empty($nestedarray)) {
                    unset($data[$key]);
                }
                else {
                    $data[$key] = $nestedarray;
                }
            }
        }

        $nestedrows = array_merge([$data], $nestedrows);
        return $nestedrows;
    }

    /**
     * Call a method for all contained objects
     *
     * @param string $method A method name.
     * @param mixed $params Params passed to the method.
     * @return void
     */
    public function callRecursively(string $method, $fields = null, ...$params)
    {
        if ($fields === null) {
            $fields = $this->getVisible();
        }

        foreach ($fields as $field) {
            $value = $this->get($field);

            if (is_object($value) && method_exists($value, $method) && is_callable([$value, $method])) {
                $value->{$method}(...$params);
            }
            else {
                if (is_iterable($value)) {
                    foreach ($value as $v) {
                        if (is_object($v) && method_exists($v, $method) && is_callable([$v, $method])) {
                            $v->{$method}(...$params);
                        }
                    }
                }
            }
        }
    }

    /**
     * Set the root entity for dependent entitities
     *
     * For example, articles are the root of sections and items,
     * and articles are the container of sections, sections are the
     * container of items.
     *
     * @param \App\Model\Entity\BaseEntity $container The container entity
     * @param \App\Model\Entity\BaseEntity $root The root entity
     * @param bool $recurse Whether to recursively prepare the root and container property
     * @param bool $reset Usually, already prepared entities are skipped.
     *                    In case you change the structure of nested entities changes,
     *                    you can reset the container.
     * @return $this
     */
    public function prepareRoot($container = null, $root = null, $recurse = true, $reset = false)
    {
        if ($reset) {
            $this->_prepared_root = false;
            $recurse = false;
        }

        if ($this->_prepared_root) {
            return $this;
        }
        else {
            $this->_prepared_root = true;
        }

        // Set root
        if (($root === null) || $this instanceof RootEntity) {
            $root = $this;
        }

        $this->container = $container;
        $this->root = $root;

        // Recursively set the root
        if ($recurse) {
            $this->callRecursively('prepareRoot', null, $this, $root, $recurse);
        }

        return $this;

    }

    /**
     * Prepare the IRI of the entity
     *
     * Assigns a local IRI to the entity if the norm_iri field is empty.
     * The local IRI consists of the database name, table name and the row ID.
     *
     * @return void
     */
    public function prepareIri()
    {
        if ($this->_prepared_iri) {
            return;
        }

        $normIri = $this['norm_iri'] ?? null;
        if (empty($normIri)) {
            $this->norm_iri = $this->iriFragment;
        }

        $this->_prepared_iri = true;
    }

    /**
     * Prepare the tree properties of the entity
     *
     * @return void
     */
    public function prepareTree()
    {
        if ($this->_prepared_tree) {
            return;
        }

        $this->_prepared_tree = true;
    }

    /**
     * Recursively prepare fields for API export
     *
     * The id will be replaced by a combination of table and id.
     * Fields listed with an alphabetic key in _serialize_fields will be renamed.
     * Xml fields will be parsed.
     *
     * ### Supported options:
     * - params.snippets
     * - params.shape long|nested
     * - params.idents id|tmp|iri
     * - params.types merge Whether to generate a single type field for all tables (type instead of articletyle, sectiontype...)
     * - ... //TODO: add all options
     *
     * @param array $options
     * @param string $extension xml|csv|json|md
     * @return array
     * @throws Exception
     */
    public function getDataForExport($options = [], $extension = '')
    {
        $options['format'] = $extension;
        $options['snippets'] = $options['params']['snippets'] ?? [];
        $options['formatFields'] = ($options['format'] ?? '') !== 'raw';
        $options['setRoot'] = true;

        // Which type of IDs?
        if (($options['params']['idents'] ?? 'id') === 'id') {
            $options['prefixIds'] = '';
        }
        elseif (($options['params']['idents'] ?? 'id') === 'tmp') {
            $options['prefixIds'] = 'tmp';
        }
        else {
            $options['iriIds'] = true;
        }

        // Nested, long, wide or triple structure?
        if (!isset($options['params']['shape'])) {

            $options['params']['shape'] = 'nested';
            if ($extension === 'csv') {
                $options['params']['shape'] = 'long';
            }
            elseif (in_array($extension, TRIPLE_FORMATS)) {
                $options['params']['shape'] = 'triples';
                $options['prepareTree'] = true;
                $options['format'] = $extension;
            }
            elseif ($extension === 'geojson') {
                $options['params']['shape'] = 'geojson';
                $options['format'] = $extension;
            }
        }

        // Get data
        if ($options['params']['shape'] === 'triples') {
            $data = $this->getDataTriples($options);
        }
        elseif ($options['params']['shape'] === 'geojson') {
            $data = $this->getDataGeo($options);
        }
        else {
            if ($options['params']['shape'] === 'nested') {
                $data = $this->getDataNested($options);
            }
            else {
                $data = $this->getDataUnnested($options, false);
            }
        }

        return $data;
    }

    /**
     * Prepare entity for import into another database
     * (analog to API preparation, but with IRIs)
     *
     * TODO: support clearing IRIs of projects, articles, section, items, links, footnotes
     *       (e.g. by $options['setIri'] = 'clear')
     * @param array $options
     * @return array
     */
    public function getDataForTransfer($options = [])
    {
        $options['format'] = 'transfer';
        $options['snippets'] = $options['snippets'] ?? [];
        $options['removeEmpty'] = false;
        $options['prefixIds'] = 'tmp';
        $options['setRoot'] = true;
        $options['files'] = true;

        if ($options['copy'] ?? false) {
            $options['setIri'] = false;
            $options['clearIri'] = true;
            $options['seed'] = true;
        }
        else {
            $options['setIri'] = true;
            $options['clearIri'] = false;
        }

        return $this->getDataUnnested($options);
    }

    /**
     * Return the full text fields
     *
     * @return string[] Keys are field names, values are index keys
     */
    public function getFulltextFields()
    {
        $fields = [];
        if ($this->type['merged']['fulltext'] ?? false) {
            foreach (($this->type['merged']['fields'] ?? []) as $fieldName => $fieldConfig) {
                $indexKey = $fieldConfig['fulltext'] ?? false;
                if ($indexKey !== false) {
                    $fields[$fieldName] = $indexKey;
                }
            }

        }

        return $fields;
    }

    /**
     * Get items for fulltext index
     *
     * @return \Generator
     */
    public function getSearchText()
    {
        foreach ($this->getFulltextFields() as $fieldName => $fieldIndex) {
            $text = trim($this->getValueFormatted($fieldName, ['format' => 'txt']) ?? '');
            if ($text !== '') {
                yield [
                    'index' => $fieldIndex,
                    'text' => $text,
                    'published' => $this->published
                ];
            }
        }
    }

    /**
     * Get property IDs referenced in the article grouped by property type
     * that match the filter criteria
     *
     * @param array $filter An array of property IDs grouped by the property type
     * @return array An array of property IDs grouped by the property type
     */
    public function getMatchedProperties($filter)
    {
        $filteredProperties = [];

        if (!empty($filter)) {
            foreach ($this->getDataProperties() as $propertyId) {
                foreach ($filter as $propertyType => $propertyFilter) {
                    $propertyIds = $propertyFilter['selected'] ?? [];
                    if (in_array($propertyId, $propertyIds) && !in_array($propertyId,
                            $filteredProperties[$propertyType] ?? [])) {
                        $filteredProperties[$propertyType][] = $propertyId;
                    }
                }
            }
        }

        return $filteredProperties;
    }

}
