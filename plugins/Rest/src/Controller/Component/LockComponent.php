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

namespace Rest\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Epi\Model\Table\LocktableTable;
use Rest\Entity\LockInterface;

/**
 * Lock component
 */
class LockComponent extends Component
{
    /**
     * Components used by the component
     * @var string[]
     */
    public $components = ['Answer', 'Auth'];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];


    /**
     * Lock the entity for exclusive access of the current user
     *
     * To prevent double editing, you need to pass the retrieved permission ID in subsequent requests.
     * Use the returned permission ID to unlock the entity after editing.
     * Redirects to the view action, if the entity already is locked by another user.
     *
     * @param \Rest\Entity\LockTrait $entity Entity with a LockTrait
     * @param bool $redirect
     *
     * @return int|null The permission ID used to unlock the entity
     */
    public function createLock($entity, $redirect = false)
    {
        try {
            $user = $this->Auth->user();
            $permissionId = $entity->lockid ?? $this->getController()->getRequest()->getData('lock');
            $permissionId = $permissionId === '' ? null : $permissionId;
            $lock = empty($user) ? null : $entity->lock($user['id'], $permissionId);

            if (is_null($lock) && $redirect) {
                if (($entity instanceof LockInterface) && $entity->isLockedByUser()) {
                    $msg = __('The dataset is locked by your account. Close it in other windows and try again.');
                }
                else {
                    $msg = __('Another user edits the dataset. Please, try again later.');
                }
                $this->Answer->error($msg, ['action' => 'view', $entity->id]);
            }

            $this->Answer->addOptions(['lock' => $lock]);
            return $lock;
        } catch (UnauthorizedException $e) {
            $this->Answer->error($e->getMessage(), ['action' => 'view', $entity->id]);
        }
    }

    /**
     * Release the entity lock of the current user
     *
     * @param \Rest\Entity\LockTrait $entity
     * @return bool
     */
    public function releaseLock($entity)
    {
        $user = $this->Auth->user();
        $permissionId = $entity->lockid ?? $this->getController()->getRequest()->getData('lock');
        $permissionId = $permissionId === '' ? null : $permissionId;

        $force = $this->getController()->getRequest()->getQuery('force', false);

        if (!empty($user) && (!empty($permissionId) || !empty($force))) {
            return $entity->unlock($user['id'], $permissionId, $force);
        }
        return false;
    }

    /**
     * Check whether the table or record is locked in EpiDesktop
     *
     * @param $table
     * @param $segment
     * @param $id
     * @return mixed
     * @deprecated Use $this->Lock->createLock() wherever possible.
     *
     */
    public function isDesktopLocked($table, $segment = null, $id = null)
    {
        /** @var LocktableTable $locktable */
        $locktable = TableRegistry::getTableLocator()->get('Epi.Locktable');
        return $locktable->isLocked($table, $segment, $id);
    }
}
