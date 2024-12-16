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

use App\Model\Table\BaseTable;
use App\Model\Table\PermissionsTable;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Epi\Model\Table\LocktableTable;

trait LockTrait
{

    public $lockid = null;

    /**
     * Check whether the entity is locked by the current user and can be unlocked
     *
     * @return boolean
     */
    public function isLockedByUser()
    {
        if (empty(BaseTable::$userId)) {
            return false;
        }

        $source = TableRegistry::getTableLocator()->get($this->getSource());
        $tableName = $source->getTable();
        $database = $source->getConnection()->config()['database'];

        $perms = TableRegistry::getTableLocator()->get('Permissions');
        return $perms->isLockedByUser(
            $database, $tableName, $this->id,
            BaseTable::$userId
        );
    }

    /**
     * Lock table entity
     *
     * @param int $userId The ID of the user who wants to lock the entity
     * @param int|null $permissionId The permission ID of previous lock operations or null
     *
     * @return int|null
     */
    public function lock($userId, $permissionId = null): ?int
    {
        // TODO: can this be replaced by $this->table ?
        $source = TableRegistry::getTableLocator()->get($this->getSource());
        $table = $source->getTable();
        $database = $source->getConnection()->config()['database'];

//        if (str_starts_with($source->getRegistryAlias(), 'Epi.') && empty(BaseTable::$userIri)) {
//            throw new UnauthorizedException(__('A user IRI is required to lock entities.'));
//        }

        // First, request lock for EpiWeb (in permissions table of the main database)
        $duration = 60;

        /** @var PermissionsTable $permissionsTable */
        $permissionsTable = TableRegistry::getTableLocator()->get('Permissions');
        $permissionId = $permissionsTable->createLock($permissionId, $duration, $database, $table, $this->id, $userId);

        // Second, request lock for EpiDesktop (in locktable table of the project database)
        if (!empty($permissionId) && str_starts_with($source->getRegistryAlias(), 'Epi.')) {

            $canLock = !empty(BaseTable::$userIri);

            // Create a user record in project database if not present
            if ($canLock && empty(\Epi\Model\Table\BaseTable::$databaseUserId)) {
                $usersTable = TableRegistry::getTableLocator()->get('Epi.Users');
                $user = $usersTable->newEntity([
                    'usertye' => 'human',
                    'userrole' => USER_AUTHOR,
                    'name' => BaseTable::$user['name'] ?? null,
                    'acronym' => BaseTable::$user['acronym'] ?? null,
                    'norm_iri' => BaseTable::$userIri,
                ]);
                $canLock = $usersTable->save($user);
                if ($canLock) {
                    \Epi\Model\Table\BaseTable::$databaseUserId = $user->id;
                    $canLock = !empty($user->id);
                }
            }

            // Create lock in project database
            if ($canLock) {
                $lockTable = TableRegistry::getTableLocator()->get('Epi.Locktable');
                $segment = isset($source->scopeField) ? $this->{$source->scopeField} : null;
                $canLock = $lockTable->createLock($permissionId, $duration, $table, $segment, $this->id);
            }

            // If not successful, release lock in main database
            if (!$canLock) {
                $permissionsTable->releaseLock($permissionId);
                $permissionId = null;
            }
        }

        $this->lockid = $permissionId;
        return $this->lockid;
    }

    /**
     * Unlock table entity
     *
     *
     * Usually, locks are  released by providing the permission ID of the lock operation.
     * Entities can be force unlocked by providing the force parameter.
     *
     * @param int $userId The ID of the user who wants to lock the entity
     * @param int|null $permissionId The permission ID of previous lock operations or null
     * @param bool $force Force unlock. Only possible if existing locks are owned by the user
     * @return bool
     */
    public function unlock($userId, $permissionId = null, $force = false): bool
    {
        $source = TableRegistry::getTableLocator()->get($this->getSource());
        $tableName = $source->getTable();
        $database = $source->getConnection()->config()['database'];

        // Release in main database
        /** @var PermissionsTable $permissionsTable */
        $permissionsTable = TableRegistry::getTableLocator()->get('Permissions');
        if (!empty($force)) {
            $permission = $permissionsTable->getLock($database, $tableName, $this->id, $userId);
            if (empty($permission)) {
                return true;
            }
            else {
                $permissionId = $permission->id;
            }
        }
        $released = $permissionsTable->releaseLock($permissionId);

        // Release lock for EpiDesktop (in project database)
        if ($released && str_starts_with($source->getRegistryAlias(), 'Epi.')) {
            $lockTable = TableRegistry::getTableLocator()->get('Epi.Locktable');
            $released = $lockTable->releaseLock($permissionId);
        }

        return $released;
    }
}
