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
use App\Utilities\Converters\Geo;

/**
 * Group Entity
 *
 * Group entities are hydrated when performing aggregation.
 *
 * # Database fields (if it was a database entity)
 * @property int $x X coordinate
 * @property int $y Y coordinate
 * @property int $z Zoom level
 * @property int $totals The total number of records in the group
 * @property string $grouptype One of periods or tiles
 *
 */
class Group extends BaseEntity
{

    protected $fixedType = true;

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
        'x',
        'y',
        'z',
        'x_type',
        'x_id',
        'x_label',
        'y_type',
        'y_id',
        'y_label',
        'totals',
        'grouptype'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'x',
        'y',
        'z',
        'grouptype'
    ];

    /**
     * Default tag name used in the XmlView
     *
     * @var string
     */
    public $_xml_tag = 'group';

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


    /**
     * Return export ready geo data from the group
     *
     * @param array $options passed to getExportValues
     * @return array
     */
    public function getDataGeo($options)
    {
        if (is_null($this['x']) || is_null($this['y']) || is_null($this['z'])) {
            return [];
        }

        $tile = [$this['z'], $this['y'], $this['x']];
        $coords = Geo::tileToCoords($tile);
        $poly = Geo::boxToPolygon($coords);

        $feature = [
            "type" => "Feature",
            "data" => [
                "tile" => implode('/', $tile),
                "totals" => $this->totals,
                "grouptype" => $this['grouptype']
            ],
            "geometry" =>  [
                "type" => "Polygon",
                "coordinates" => $poly
            ]
        ];

//        return $outputFormat === 'geojson' ? $feature :  json_encode($feature);

        return ['geodata' => [$feature]];
    }

}
