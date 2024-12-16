<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Rest\Entity;

/**
 * Interface that is implemented in LockTrait.
 */
interface LockInterface
{
    public function isLockedByUser();

    public function lock($userId, $permissionId = null): ?int;

    public function unlock($userId, $permissionId = null): bool;
}
