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

namespace App\Model\Behavior;

/**
 * Exception thrown when the tree fields are corrupt.
 *
 */
class TreeCorruptException extends \Cake\Core\Exception\CakeException
{
}
