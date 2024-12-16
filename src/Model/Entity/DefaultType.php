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

namespace App\Model\Entity;

/**
 * Default types are used as fallback and
 * for app level types - they don't come from the database
 *
 * # Database fields (if it was a database entity, without inherited fields)
 * @property string $scope
 * @property string $name
 * @property int $sortno
 * @property string $category
 * @property string $caption
 * @property string $description
 * @property mixed $config
 * @property string $norm_iri
 *
 * # Virtual fields (without inherited fields)
 * @property array $subtypes
 * @property array $merged
 * @property string|null $categoryPath
 */
class DefaultType extends BaseEntity
{

    /**
     * @var bool Whether the type is configured in the database or not. See _getType() and _getDefaultType().
     */
    protected $fixedType = true;

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

    /**
     * The field used to create an IRI
     * @var string
     */
    protected $_field_iri = 'name';

    protected $_fields_formats = [
        'id' => 'id',
        'published' => 'select',
        'config' => 'array'
    ];

    /**
     * Default types don't have subtypes
     *
     * @return DefaultType[]
     */
    protected function _getSubtypes()
    {
        return [];
    }

    /**
     * Default types don't have subtypes, thus,
     * they don't have merged types
     *
     * @return array
     */
    protected function _getMerged()
    {
        return $this->_fields['config'] ?? [];
    }


    /**
     * Scope and category
     *
     * @return null|string
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
     * Merge the types config with defaults
     *
     * See Type::getHtmlFields()
     *
     * TODO: improve class hierarchy, derive DefaultType and Type from a common base class
     *
     * @param array $defaultConfig A key value list of field definitions
     * @param array $defaultFields A list of field names that should be included if no types config is present
     * @param array $required A list of field names that should be included in any case
     * @return array
     */
    public function getHtmlFields($defaultConfig, $defaultFields = [], $required = [])
    {
        $defaultFields = $defaultFields + $required;
        $fields = array_intersect_key($defaultConfig, array_flip($defaultFields));

        return $fields;
    }
}
