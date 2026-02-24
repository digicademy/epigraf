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

use App\Model\Entity\DefaultType;

/**
 * Node entity used in a tree structure
 *
 */
class Node extends BaseEntity
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
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
    ];

    /**
     * Default tag name used in the XmlView
     *
     * @var string
     */
    public $_xml_tag = 'node';

    /**
     * Default table name
     *
     * @var string
     */
    public $_tablename = '';

    /**
     * @var bool Whether the type is configured in the database or not. See _getType() and _getDefaultType().
     */
    protected $fixedType = true;

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
     * Get the default type for the entity, if no type configuration is available in the types table
     *
     * @return DefaultType
     */
    protected function _getDefaultType()
    {
        $type = new DefaultType([
            'scope' => 'nodes',
            'mode' => MODE_DEFAULT,
            'name' => 'default',
            'norm_iri' => 'default',
            'config' => []
        ]);

        return $type;
    }

    /**
     * Get the table name
     *
     * @return string
     */
    protected function _getTableName()
    {
        return $this->_fields['table_name'] ?? '';
    }

}
