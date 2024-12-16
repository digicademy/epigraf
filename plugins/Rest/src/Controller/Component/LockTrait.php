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

use Cake\Datasource\FactoryLocator;

/**
 * LockTrait
 *
 * @property LockComponent $Lock
 * @property AnswerComponent $Answer
 * @property string|null $modelClass
 */
trait LockTrait
{

    /**
     * Lock a doc
     *
     * Polled from documents.js.
     *
     * @param string|null $id Entity ID
     * @throws \Cake\Http\Exception\NotFoundException if record not found
     */
    public function lock($id = null)
    {
        $entity = FactoryLocator::get('Table')->get($this->modelClass)->get($id, ['contain' => []]);
        $lock = $this->Lock->createLock($entity);
        if ($lock) {
            $this->Answer->success(__('Locked'), false, ['lock' => $lock]);
        }
        else {
            $this->Answer->error(__('Another user edits the dataset. Please try again later.'));
        }

        $this->viewBuilder()->setClassName('Json');
    }

    /**
     * Unlock method
     *
     * @param string|null $id Entity ID
     * @throws \Cake\Http\Exception\NotFoundException if record not found
     * return \Cake\Http\Response|null
     */
    public function unlock($id = null)
    {
        /** @var \Rest\Entity\LockTrait $entity */
        $entity = FactoryLocator::get('Table')->get($this->modelClass)->get($id, ['contain' => []]);
        $unlock = $this->Lock->releaseLock($entity);
        $redirect = $this->getRequest()->getQuery('redirect', false);
        if ($unlock) {
            if (!empty($redirect)) {
                $redirect = ['action' => $redirect, $entity->id];
            }
            $this->Answer->success(__('Unlocked'), $redirect, ['unlock' => $unlock]);
        }
        else {
            $this->Answer->error(__('Could not unlock the entity'));
        }

        $this->viewBuilder()->setClassName('Json');
    }

}
