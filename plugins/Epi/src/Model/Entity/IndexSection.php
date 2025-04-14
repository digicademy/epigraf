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
 * Section Entity for index generation
 *
 * # Database fields
 * @property int $id
 * @property int $articles_id
 */
class IndexSection extends BaseEntity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     * //TODO: make all internally handeled fields unaccessible
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
        'articles_id',
//        'articles_iri',
//        'sectiontype'
    ];

    /**
     * Attibutes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'articles_id',
//        'articles_iri',
//        'sectiontype'
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
        'modified_by' => 'id',
        'published' => 'published'
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
     * IndexSection constructor.
     *
     * @param Entity|array $section
     * @param array $options
     *
     */
    public function __construct($section = null, array $options = [])
    {
        $data = [
            'id' => $section['id'] ?? null,
            'sectiontype' => $section['type']['name'] ?? $section['sectiontype'] ?? null,
            'norm_iri' => $section['norm_iri'] ?? null,
            'articles_id' => $section['articles_id'] ?? null,
//            'articles_iri' => !empty($section->article) ? $section->article->iriPath : null
        ];
        $options['source'] = 'Epi.Sections';
        parent::__construct($data, $options);
        $this->root = $this;
    }

    /**
     * Get the ID as ID, prefixed ID or IRI pathc
     *
     * @param array $fieldName The field name as an array of one or two components
     * @param array $options
     * @return string|integer|null
     */
    public function getIdFormatted($fieldName, $options)
    {
        $prefix = $options['prefixIds'] ?? false;
        $iri = $options['iriIds'] ?? false;
        if (($prefix === false) && ($iri === true)) {
            if ($fieldName[0] === 'id') {
                return $this->iriPath;
            }
            // TODO: when cached, parent and article are not loaded
            elseif (($fieldName[0] === 'parent_id') && !empty($this->parent)) {
                return $this->parent->iriPath;
            }
            elseif (($fieldName[0] === 'articles_id') && !empty($this->article)) {
                return $this->article->iriPath;
            }
        }
        return parent::getIdFormatted($fieldName, $options);
    }
}
