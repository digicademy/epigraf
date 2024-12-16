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

use Cake\ORM\Entity;

/**
 * Property Entity for index generation
 */
class IndexProperty extends Property
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *  //TODO: make all internally handeled fields unaccessible
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'id',
        'parent_id',
        'propertytype',
        'level',
        'lft',
        'rght',
        'related_id',
        'sortno',
        'sortkey',

        'lemma',
        'name',
        'unit',
        'comment',
        'content',
        'elements',
        'iscategory',
        'ishidden',
        'signature',
        'keywords',

        'norm_type',
        'norm_data',
        'norm_iri',

        'file_name',
        'source_from',

        'property',
        'links',

        'sections'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'parent_id',
        'sortno',
        'sortkey',
        'related_id',
        'propertytype',
        'level',
        'lft',
        'rght',
        'iscategory',
        'ishidden'
    ];

    protected $_serialize_snippets = [
        'index' => ['sections', 'properties_id', 'related_id'],
        'published' => ['published'],
        'editors' => ['creator', 'modifier', 'created', 'modified'],
        'problems' => ['problems']
    ];

    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     *
     * @var string[]
     */
    public static $_fields_ids = ['id', 'parent_id', 'related_id', 'properties_id'];

    protected $_fields_formats = [
        'id' => 'id',
        'parent_id' => 'id',
        'related_id' => 'id',
        'properties_id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id',
        'published' => 'published'
    ];

    /**
     * Default table name
     *
     * @var string
     */
    public $_tablename = 'properties';

    /**
     * Default tag name used in the XmlView
     *
     * @var string
     */
    public $_xml_tag = 'property';

    /**
     * Added ancestors
     *
     * @var bool
     */
    public $_added_ancestors = false;

    /**
     * Added references
     *
     * @var bool
     */
    public $_added_references = false;

    /**
     * SectionPath constructor.
     *
     * @param Entity|array $property
     * @param array $options
     *
     */
    public function __construct($property = null, array $options = [])
    {
        // Construct metaproperty
        $metaproperty = null;
        if (!empty($property['property'])) {
            $metaproperty = new IndexProperty($property['property']);
            $metaproperty->prepareRoot($this, $metaproperty, true, true);
        }

        // Get links
//        $links = null;
//        if (is_object($data) && isset($data->links)) {
//            $links = $data->links;
//        }
        $links = [];
        foreach ($property['links'] ?? [] as $link) {
            $link = new IndexLink($link);
            $link->prepareRoot($this, $this, false, true);
            $links[] = $link;
        }

        // Convert to array
        if (!is_array($property)) {
            $property = $property->toArray();
        }

        // Add properties
        unset($property['type']);
        $property['links'] = $links;
        $property['property'] = $metaproperty;

        $property['sections'] = $property['sections'] ?? [];
        $options['source'] = 'Epi.Properties';
        parent::__construct($property, $options);

        $this->loadSections($property['sections']);
    }


    public function loadSections($data)
    {
        foreach ($data as $section) {
            $indexSection = new IndexSection($section);
            $this->addSectionPath($indexSection);
        }
    }

    public function addSectionPath($sectionPath)
    {
        if (!isset($this['sections'][$sectionPath['id']]) || !($this['sections'][$sectionPath['id']] instanceof IndexSection)) {
            $this['sections'][$sectionPath['id']] = $sectionPath;
        }
    }

}
