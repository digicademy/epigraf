<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace Epi\Model\Entity;

use App\Model\Entity\DefaultType;
use App\Model\Table\BaseTable;
use App\Model\Table\PermissionsTable;

/**
 * Type Entity
 *
 * # Database fields (without inherited fields)
 * @property string $scope
 * @property string $mode
 * @property string $preset
 * @property string $name
 * @property int $sortno
 * @property string $category
 * @property string $caption
 * @property string $description
 * @property mixed $config
 * @property string $norm_iri
 *
 * # Virtual fields
 * @property null|array $defaultType
 * @property null|int $publishedState
 * @property array $subtypes
 * @property array $merged
 * @property null|string $categoryPath
 * @property array $htmlFields
 * @property array $header
 */
class Type extends RootEntity
{

    /**
     * @var bool Whether the type is configured in the database or not. See _getType() and _getDefaultType().
     */
    protected $fixedType = true;
    protected $_merged = null;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'published' => true,
        'scope' => true,
        'mode' => true,
        'preset' => true,
        'name' => true,
        'category' => true,
        'caption' => true,
        'description' => true,
        'config' => true,
        'sortno' => true,
        'norm_iri' => true
    ];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'id',
        'created',
        'modified',
        'scope',
        'mode',
        'preset',
        'name',
        'sortno',
        'category',
        'caption',
        'description',
        'config',
        'norm_iri'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'scope',
        'mode',
        'preset',
        'category',
        'sort_no',
        'norm_iri',
        'published'
    ];
    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     *
     * @var string[]
     */
    public static $_fields_ids = [
        'id'
    ];

    protected $_fields_formats = [
        'id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id',
        'published' => 'select',
        'config' => 'array'
    ];

    /**
     * The field used to create an IRI
     * @var string
     */
    protected $_field_iri = 'name';
    protected $_prefix_iri = false;

    /**
     * Fields used for data import
     *
     * TODO: What about published? Implement import option whether to import published or not
     *
     * @var string[]
     */
    protected $_fields_import = [
        'id',
        'created',
        'modified',
        'type' => 'scope',
        'iri' => 'norm_iri', //TODO: rename in database
        'sortno',
        'name',
        'caption',
        'mode',
        'preset',
        'category',
        'description',
        'config'
    ];


    /**
     * Convert imported data
     * (parse json)
     *
     * @param $content
     * @param $options
     * @return array
     */
    public function importData($content, $options)
    {

        // TODO: Implement option to merge the new config instead of replacing it
        if (is_string($content['config'] ?? null)) {
            try {
                $content['config'] = json_decode($content['config'], true);
            } catch (Exception $e) {
                return ['error' => __('Error parsing JSON: {0}', [$e->getMessage()])];
            }
        }

        return parent::importData($content, $options);
    }

    /**
     * Get the default type for the entity, if no type configuration is available in the types table
     *
     * @return array|null
     */
    protected function _getDefaultType()
    {
        $type = new DefaultType([
            'scope' => 'types',
            'mode' => 'default',
            'name' => 'default',
            'norm_iri' => 'default',
            'published' => PUBLICATION_BINARY_PUBLISHED,
            'config' => []
        ]);
        return $type;
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
        return $this->published ? PUBLICATION_PUBLISHED : PUBLICATION_DRAFTED;
    }

    /**
     * Loads the subtypes if not already loaded
     *
     * Subtypes are types with a mode different from 'default',
     * e.g. 'code' or 'preview'
     *
     * @return Type[]
     */
    protected function _getSubtypes()
    {
        if (!isset($this->_fields['subtypes']) && ($this->getSource() === 'Epi.Types')) {
            $this->_fields['subtypes'] = $this->table->getDatabase()->types[$this->scope][$this->name]['subtypes'];
        }

        return $this->_fields['subtypes'] ?? [];
    }

    /**
     * Merges the mode config into the default config
     *
     * @return array
     */
    protected function _getMerged()
    {
        if (is_null($this->_merged)) {
            $config = $this->_fields['config'] ?? [];
            $config = is_array($config) ? $config : [];
            $action = BaseTable::$requestAction;

            // TODO: replace 'preview' by 'view' in modes
            $mode = $action === 'view' ? 'preview' : BaseTable::$requestMode;
            $presetName = BaseTable::$requestPreset;
            $presets = [];

            foreach ($this->subtypes as $subtype) {

                // Default layout (empty string) is always merged
                if (($subtype['mode'] === $mode) && ($subtype['preset'] === 'default') && is_array($subtype->config)) {
                    $config = array_replace_recursive($config, $subtype->config ?? []);
                }

                // Specific layout is merged if it matches the current layout
                elseif (($subtype['mode'] === $mode) && ($subtype['preset'] === $presetName) && is_array($subtype->config)) {
                    $presets[] = $subtype->config ?? [];
                }
            }

            foreach ($presets as $preset) {
                $config = array_replace_recursive($config, $preset);
            }

            $this->_merged = $config;
        }

        return $this->_merged;
    }

    /**
     * Virtual field for index view
     *
     * @return string|null
     */
    protected function _getCategoryPath()
    {
        if ($this->category) {
            return $this->scope . ' / ' . $this->category;
        }
        else {
            return $this->scope;
        }
    }


    /**
     * Returns fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        $scopes = $this->table->getScopes();
        $scopes = array_combine($scopes, $scopes);
        $modes = PermissionsTable::$requestModes;

        $fields = [
            'scope' => [
                'caption' => __('Scope'),
                'id' => 'scope',
                'type' => 'select',
                'options' => $scopes
            ],

            'name' => [
                'caption' => __('Name')
            ],

            'config' => [
                'caption' => __('Config'),
                'id' => 'textarea_config',
                'rows' => 15,
                'format' => 'json',
                'type' => 'jsoneditor',
                'layout' => 'stacked'
            ],

            'mode' => [
                'caption' => __('Mode'),
                'id' => 'mode',
                'type' => 'select',
                'options' => $modes
            ],

            'preset' => [
                'caption' => __('Preset')
            ],

            'caption' => [
                'caption' => __('Caption')
            ],

            'description' => [
                'caption' => __('Description'),
                'id' => 'textarea_content',
                'rows' => 2
            ],

            'category' => [
                'caption' => __('Category')
            ],

            'published' => [
                'caption' => __('Published'),
                'type' => 'checkbox'
            ],

            'sortno' => [
                'caption' => __('Number')
            ],

            'norm_iri' => [
                'caption' => __('IRI fragment')
            ]
        ];

        return $fields;
    }

    /**
     * Get header configuration
     *
     * @return array
     */
    protected function _getHeader()
    {
        return $this->merged['header'] ?? [];
    }

    /**
     * Merge the types config with defaults
     *
     * @param array $defaultConfig A key value list of field definitions
     * @param array $defaultFields A list of field names that should be included if no types config is present
     * @param array $required A list of field names that should be included in any case
     * @return array
     */
    public function getHtmlFields($defaultConfig, $defaultFields = [], $required = [])
    {

        if (empty($this->merged['fields'])) {
            $defaultFields = $defaultFields + $required;
            $fields = array_intersect_key($defaultConfig, array_flip($defaultFields));
        }

        // Take captions and allowed fields from the config
        // TODO: Why not take the whole config?
        else {
            $result = [];

            foreach ($required as $field) {
                $result[$field] = $defaultConfig[$field] ?? [];
            }

            foreach ($this->merged['fields'] as $field => $caption) {
                $caption = is_string($caption) ? $caption : ($caption['caption'] ?? '');
                $result[$field] = $defaultConfig[$field] ?? [];
                $result[$field]['caption'] = $caption;
            }
            $fields = $result;
        }

        return $fields;
    }

}
