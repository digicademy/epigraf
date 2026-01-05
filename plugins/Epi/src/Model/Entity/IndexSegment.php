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
 * Segment Entity for index generation
 *
 * The IndexSegment entity represents an index.
 *
 * # Database fields
 * @property string $propertytype
 */
class IndexSegment extends RootEntity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     * //TODO: make all internally handled fields inaccessible
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
        'propertytype',
        'properties'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'propertytype'
    ];

    /**
     * Default tag name used in the XmlView
     *
     * @var string
     */
    public $_xml_tag = 'index';

    /**
     * Default table name
     *
     * @var string
     */
    public $_tablename = 'properties';

    /**
     * SectionPath constructor.
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data, array $options = [])
    {
        $data['properties'] = $data['properties'] ?? [];
        $options['source'] = 'Epi.Properties';
        parent::__construct($data, $options);

        $this->loadProperties($data['properties']);
    }

    public function addProperty(IndexProperty $property)
    {
        if (!isset($this['properties'][$property['id']]) || !($this['properties'][$property['id']] instanceof IndexProperty)) {
            $this['properties'][$property['id']] = $property;
            $property->container = $this;
            $property->root = $property;
        }

        return $this['properties'][$property['id']];
    }

    public function loadProperties($data)
    {
        foreach ($data as $property) {
            $indexProperty = new IndexProperty($property);
            $this->addProperty($indexProperty);
        }
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
        return [
            'propertytype' => [
                'caption' => __('Property type')
            ]
        ];
    }
}
