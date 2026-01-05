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
 * Tag extracted from XML content
 *
 * TODO: lookup links / footnote record
 *
 * # Database fields (if it was a database entity)
 * @property string $tab
 * @property int $id
 * @property string $field
 * @property string $tagid
 * @property string $tagname
 * @property string $content
 *
 */
class Tag extends BaseEntity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
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
        'field',
        'tagid',
        'tagname',
        'caption',
        'content'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'field',
        'tagid',
        'tagname'
    ];

    /**
     * Default tag name used in the XmlView
     *
     * @var string
     */
    public $_xml_tag = 'tag';

    /**
     * Default table name
     *
     * @var string
     */
    public $_tablename = '';

    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data, array $options = [])
    {
        parent::__construct($data, $options);
    }
}
