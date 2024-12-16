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

/**
 * Footnote Entity
 *
 * # Database fields (without inherited fields)
 * @property int $sortno
 * @property mixed $fntype The footnote type, @deprecated use from_tagname
 * @property string $name The rendered footnote number as saved in the database
 * @property mixed $segment
 * @property mixed $content
 * @property int $root_id
 * @property string $root_tab
 * @property int $from_id
 * @property string $from_tab
 * @property string $from_field
 * @property string $from_tagname The footnote type
 * @property string $from_tagid
 * @property int $from_sort
 *
 * # Virtual fields (without inherited fields)
 * @property string $caption
 * @property string $captionPath
 * @property string $captionExt
 */
class Footnote extends BaseEntity
{
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
        'deleted' => true,

        'root_id' => true,
        'root_tab' => true,
        'from_id' => true,
        'from_tab' => true,
        'from_field' => true,
        'from_tagname' => true,
        'from_tagid' => true,
        'from_sort' => true,

        'sortno' => true,
        'fntype' => true,
        'name' => true,
        'content' => true,
        'segment' => true,

        'norm_iri' => true
    ];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'id',
        'from_id',
        'from_field',
        'from_tagid',
        'from_tagname',
        'from_sort',
        'content',
        'segment',
        'root_id',
        'norm_iri'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'root_id',
        'from_id',
        'from_field',
        'from_tagid',
        'from_tagname',
        'from_sort',
        'norm_iri'
    ];

    /**
     * Snippets for export
     * Each snippet contains a list of fields that is added to $_serialize_fields.
     *
     * @var array[]
     */
    protected $_serialize_snippets = [
        'deleted' => ['deleted', 'version_id', 'created', 'modified'],
        'editors' => ['creator', 'modifier', 'created', 'modified'],
        'problems' => ['problems']
    ];

    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     *
     * @var string[]
     */
    public static $_fields_ids = [
        'id',
        'root_id' => ['root_tab', 'root_id'],
        'from_id' => ['from_tab', 'from_id']
    ];

    /**
     * Fields used for data import
     *
     * @var string[]
     */
    protected $_fields_import = [
        'id',
        'created',
        'modified',
        'root_id' => ['root_tab', 'root_id'],
        'from_id' => ['from_tab', 'from_id'],
        'from_field',
        'type' => 'from_tagname',
        'from_tagid',
        'from_sort',
        'iri' => 'norm_iri', //TODO: rename in database
        //'type' => 'fntype', //@deprecated, use from_tagname
        //'fntype',           //@deprecated, use from_tagname
        'sortno',
        'content',
        'segment'
    ];

    protected $_fields_formats = [
        'id' => 'id',
        'root_id' => 'id',
        'from_id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id',
        'published' => 'published',
        'content' => 'xml',
        'segment' => 'xml'
    ];

    /**
     * Get the label injected into xml attributes, see Link->_getToValue()
     *
     * @return string
     */
    protected function _getCaption()
    {
        $name = $this->name;
        $name = $name === '' ? null : $name;
        return $name;
    }

    /**
     * Get the display path
     *
     * @return string
     */
    protected function _getCaptionPath()
    {
        return $this->name;
    }

    /**
     * Get a caption for external references, including the article caption
     *
     * @return string
     */
    protected function _getCaptionExt()
    {
        if (!empty($this->root)) {
            $caption = $this->root->captionExt;
        }
        else {
            $caption = '';
        }

        return $caption . '[' . __('Footnote') . ' ' . $this->name . ']';
    }

    /**
     * Convert fntype to boolean
     *
     * //TODO: use from_tagname
     *
     * @param $content
     * @param $options
     * @return array
     */
    public function importData($content, $options)
    {
        if (isset($content['fntype'])) {
            $content['fntype'] = !empty($content['fntype']);
        }

        return parent::importData($content, $options);
    }

    /**
     * Get export fields
     *
     * @param $options
     * @return array
     */
    public function getExportFields($options)
    {
        $fields = parent::getExportFields($options);

        // Filter out content in link targets
        if ($this->container instanceof Link) {
            $fields = array_diff($fields, ['content']);
        }
        return $fields;
    }
}
