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
 * Lock Entity
 *
 * # Database fields
 * @property int $lock_token
 * @property int $lock_mode
 * @property string $lock_table
 * @property string $lock_segment
 * @property int $lock_id
 * @property \Cake\I18n\Time $expires
 *
 * # Virtual fields
 */
class Lock extends BaseEntity
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
        'lock_token' => true,
        'lock_mode' => true,
        'lock_table' => true,
        'lock_segment' => true,
        'lock_id' => true,
        'expires' => true
    ];

}
