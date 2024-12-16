<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */

declare(strict_types=1);

namespace Epi\Model\Entity;

/**
 * SectionPath Entity for shortcuts in the exported data
 *
 * # Database fields
 * @property int $id
 * @property int $parent_id
 * @property int $sortno
 * @property string $sectiontype
 * @property int $number
 * @property string $name
 * @property string $alias
 *
 * @property int $children
 * @property int $level
 * @property int $lft
 * @property int $rght
 * @property int $articles_id
 * @property string $norm_iri
 *
 * # Virtual fields
 * @property int $siblings
 */
class SectionPath extends BaseEntity
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
    ];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'id',
        'parent_id',
        'articles_id',
        'sectiontype',
        'name',
        'alias',
        'number',
        'sortno',
        'level',
        'children',
        'siblings'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'parent_id',
        'articles_id',
        'sectiontype',
        'name',
        'alias',
        'number',
        'sortno',
        'level',
        'children',
        'siblings'
    ];

    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     *
     * @var string[]
     */
    public static $_fields_ids = ['id', 'parent_id', 'articles_id' => 'articles'];

    protected $_fields_formats = [
        'id' => 'id',
        'parent_id' => 'id',
        'articles_id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id'
    ];

    /**
     * Default tag name used in the XmlView
     *
     * @var string
     */
    public $_xml_tag = 'section';

    /**
     * Default table name
     *
     * @var string
     */
    public $_tablename = 'sections';

    /**
     * SectionPath constructor.
     *
     * @param array $properties
     * @param array $options
     *
     */
    public function __construct(Section $section = null, array $stack = [], array $options = [])
    {
        $options['source'] = 'Epi.Sections';

        if (empty($section)) {
            $pathdata = ['children' => 0, 'level' => 0, 'id' => null, 'parent' => null];
        }
        else {
            $pathdata = [
                'id' => $section['id'],
                'parent_id' => $section['parent_id'],
                'articles_id' => $section['articles_id'],
                'lft' => $section['lft'],
                'rght' => $section['rght'],
                'children' => 0, // Will be increased while traversing sections in ArticlesTable::findCollectItems()
                'siblings' => 'NA', // Will be determined by _getSiblings()
                'level' => sizeof($stack), //TODO: start at 0 not 1 (inconsistent, see Section->_getLevel()
                'sortno' => $section['sortno'],
                'number' => $section['number'],
                'sectiontype' => $section['sectiontype'],
                'name' => $section['name'],
                'alias' => $section['alias']
            ];
        }

        parent::__construct($pathdata, $options);

        if (!empty($section)) {
            $this->root = $section->root;
            $this->container = $section;
        }

        $this->parent = end($stack);
    }

    /**
     * Get number of siblings (including current section)
     *
     * @return int
     */
    protected function _getSiblings()
    {
        return $this->parent->children ?? 0;
    }

}
