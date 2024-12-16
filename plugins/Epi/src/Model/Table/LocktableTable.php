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

namespace Epi\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\Validation\Validator;
use Epi\Model\Entity\Lock;

/**
 * Locktable table
 *
 */
class LocktableTable extends BaseTable
{
    /**
     * Initialize hook
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('locktable');
        $this->setEntityClass('Epi.Lock');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules
     *
     * @param Validator $validator Validator instance
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('lock_token')
            ->allowEmptyString('lock_token');

        $validator
            ->integer('lock_mode')
            ->allowEmptyString('lock_mode');

        $validator
            ->scalar('lock_table')
            ->maxLength('lock_table', 1500)
            ->allowEmptyString('lock_table');

        $validator
            ->scalar('lock_segment')
            ->maxLength('lock_segment', 255)
            ->allowEmptyString('lock_segment');

        return $validator;
    }

    /**
     * Check whether the table or record is locked in the project database
     * (EpiDesktop locking mechanism)
     *
     * An entity is locked, if lock entries exit in the locktable table
     * that do not match the given permission ID.
     *
     * @param string $table
     * @param string|null $segment
     * @param integer|null $id
     * @param integer|null $permissionId Optionally, the permission ID in the global permissions table
     *
     * @return int Number of locks excluding locks identified by the permissionId
     */
    public function isLocked($table, $segment = null, $id = null, $permissionId = null)
    {
        $conditions = ['lock_table' => $table];
        if ($id !== null) {
            $conditions['lock_id'] = $id;
        }

        if ($segment !== null) {
            $conditions = [
                'OR' => [
                    $conditions,
                    ['lock_segment' => $segment, 'lock_id' => -1]
                ]
            ];
        }

        if (!is_null($permissionId)) {
            $conditions['lock_token <>'] = -intval($permissionId);
        }

        $this->pruneLocks();
        return $this->find('all')
            ->where($conditions)
            ->count();
    }

    /**
     * Return the lock record if present
     *
     * @param string $table
     * @param string|null $segment
     * @param integer|null $id
     * @param integer|null $permissionId The permission ID in the epigraf.permissions table
     * @return Lock|null
     */
    public function getLock($table, $segment, $id, $permissionId)
    {
        $conditions = ['lock_table' => $table];
        if ($id !== null) {
            $conditions['lock_id'] = $id;
        }

        if ($segment !== null) {
            $conditions = [
                'OR' => [
                    $conditions,
                    ['lock_segment' => $segment, 'lock_id' => -1]
                ]
            ];
        }

        if (!is_null($permissionId)) {
            $conditions['lock_token'] = -intval($permissionId);
        }

        return $this->find('all')
            ->where($conditions)
            ->first();
    }


    /**
     * Lock the table or record in the project database lock table
     *
     * Project database locks can only occur after locks  in the main database.
     * Thus, always provide a permission ID.
     *
     * @param integer $permissionId The permission ID in the epigraf.permissions table
     * @param integer $duration The seconds until the lock expires
     * @param string $tableName
     * @param string|null $segment
     * @param integer|null $entityId
     * @return bool Whether a lock could be created
     */
    public function createLock($permissionId, $duration = 60, $tableName = null, $segment = null, $entityId = null)
    {
        if (empty($permissionId)) {
            return false;
        }

        //Check for lock
        // TODO: what if a concurrent user locks the entity in the meantime?
        //       Prevent concurrent locks in the save operation by validation rules.
        if ($this->isLocked($tableName, $segment, $entityId, $permissionId)) {
            return false;
        }

        // Only lock if the user is logged into the project database
//        if (empty(BaseTable::$databaseUserId)) {
//            return false;
//        }

        // Define the required lock
        $expires = FrozenTime::now()->addSeconds($duration);
        $data = ['lock_table' => $tableName];
        $data['lock_id'] = $entityId ?? -1;
        $data['lock_segment'] = $segment ?? null;
        $data['lock_mode'] = LOCKMODE_EPIDESKTOP;
        $data['lock_token'] = -$permissionId;
        $data['expires'] = $expires;

        // If a permission ID is provided, the user already has locked the entity.
        // In this case, update the lock. Otherwise, create a new lock.
        $this->pruneLocks();
        $lock = $this->getLock($tableName, $segment, $entityId, $permissionId);
        if (empty($lock)) {
            $lock = $this->newEntity($data);
        }
        else {
            $lock = $this->patchEntity($lock, $data);
        }

        $result = $this->save($lock);
        return $result;
    }

    /**
     * Unlock the table or record in EpiDesktop
     *
     * @param integer $permissionId The permission ID in the epigraf.permissions table
     */
    public function releaseLock($permissionId)
    {
        if (empty($permissionId)) {
            return false;
        }

        return $this->deleteAll(['lock_token' => -$permissionId]);
    }

    /**
     * Remove all expired locks
     *
     * @return mixed
     */
    public function pruneLocks()
    {
        return $this->deleteAll(['expires IS NOT NULL', 'expires <' => FrozenTime::now()]);
    }
}
