<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity;

use App\Model\Table\FilesTable;

/**
 * File Entity
 *
 * # Database fields (without inherited fields)
 * @property string $name
 * @property string $description
 * @property string $config
 * @property string $type Enhanced by virtual property
 * @property int $size
 * @property string $root
 * @property string $path
 * @property bool $isfolder
 *
 * # Relations
 * @property FilesTable $table
 */
class FileRecord extends \Files\Model\Entity\FileRecord
{

    /**
     * Default limit
     *
     * @var int
     */
    public $limit = 15;

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
        '*' => true,
        'id' => false
    ];

    /**
     * Unfortunately, file entities have their file extension in the type field, not a type entity.
     *
     * TODO: Rename type field to filetype in the database
     *
     * @return string
     */
    protected function _getType()
    {
        return $this->_fields['type'] ?? null;
    }
}
