<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Controller;

use App\Model\Entity\Databank;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Rest\Controller\Component\LockTrait;

/**
 * Databanks Controller
 *
 * Provides administration of Epigraf database connections.
 * Database content is handled by Epi3 plugin, not here.
 *
 * @property \App\Model\Table\DatabanksTable $Databanks
 */
class DatabanksController extends AppController
{

    /**
     * The lock trait provides lock and unlock actions
     */
    use LockTrait;

    public $help = 'administration/databases';

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\Event $event Event
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->_activateMainMenuItem([]);
    }

    /**
     * Retrieve a list of databases
     *
     * @return void
     */
    public function index()
    {
        $this->Actions->index();
    }

    /**
     * Open a database record and redirects to articles of the database.
     *
     * @param string|null $id Database id
     *
     * @return \Cake\Http\Response
     */
    public function open(string $id = null): Response
    {
        $databank = $this->Databanks->get($id, ['contain' => []]);

        return $this->redirect([
            'plugin' => $databank['plugin'],
            'database' => Databank::removePrefix($databank['name']),
            'controller' => 'Articles',
            'action' => 'index'
        ]);

    }

    /**
     * Show database details
     *
     * @param string|null $id database id
     *
     * @return void
     */
    public function view(string $id = null)
    {
        $this->Actions->view($id);
    }

    /**
     * Edit a project database
     *
     * @param integer|string $id
     *
     * @return Response|void
     */
    public function edit($id = null)
    {
        $this->Actions->edit($id);
    }

    /**
     * Add a new project database
     *
     * @return \Cake\Http\Response|void redirect on successful add, renders view otherwise
     */
    public function add()
    {
        $entity = $this->Databanks->newEntity(['category' => __('Examples')]);
        $entity->version = DATABASE_CURRENT_VERSION;

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $data['name'] = Databank::addPrefix($data['name'] ?? '');
            $entity = $this->Databanks->patchEntity($entity, $data);

            if (empty(Databank::removePrefix($data['name'] ?? ''))) {
                $this->Answer->error(__('The database name is invalid.'));
            }
            else {
                if ($this->Databanks->save($entity)) {
                    $flash = [];
                    $flash['success'][] = __('The database connection has been saved.');

                    //Create folder
                    $created = $entity->createFolders();
                    if (($created['created'] > 0) && ($created['created'] == $created['missing'])) {
                        $flash['success'][] = __('The database folders have been created.');
                    }
                    else {
                        if (($created['missing'] > 0)) {
                            $flash['error'][] = __('The database folders could not be created. Please, try again.');
                        }
                    }

                    //Create database
                    if (!$entity->available) {
                        if ($entity->createDatabase()) {
                            $flash['success'][] = __('The database has been created.');
                        }
                        else {
                            $flash['error'][] = __('The database could not be created. Please, check your permissions.');
                        }
                    }

                    // Init database
                    if ($entity->isempty) {
                        if ($entity->initDatabase()) {
                            $flash['success'][] = __('The database has been initialized.');
                        }
                        else {
                            $flash['error'][] = __('The database could not be initialized. Please, check your permissions.');
                        }
                    }

                    // Flash messages
                    if (!empty($flash['error'])) {
                        $this->Answer->error(implode(' ', $flash['error']), ['action' => 'view', $entity->id]);
                    }
                    if (!empty($flash['success'])) {
                        $this->Answer->success(implode(' ', $flash['success']), ['action' => 'view', $entity->id]);
                    }
                }
                else {
                    $this->Answer->error(
                        __('The database connection could not be saved. Please, try again.'),
                        [],
                        $entity->getErrors()
                    );
                }
            }
        }

        $connections = $this->Databanks->getConnections();
        $presets = $this->Databanks->getPresets();
        $this->Answer->addAnswer(compact('entity', 'connections', 'presets'));
    }

    /**
     * Create database dump
     *
     * @param string|null $id database id
     *
     * @return \Cake\Http\Response|void redirects on successful backup, renders view otherwise
     * @throws \Cake\Http\Exception\NotFoundException if record not found
     */
    public function backup(string $id = null)
    {
        $entity = $this->Databanks->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['post', 'put'])) {
            if ($entity->backupDatabase()) {
                $this->Answer->success(__('The database has been backed up.'), ['action' => 'view', $id]);
            }
            else {
                $this->Answer->error(__('The database could not be backed up. Please, try again.'));
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Import an sql script
     *
     * @param string|null $id database id
     *
     * @return \Cake\Http\Response|void redirects on successful import, renders view otherwise
     * @throws \Cake\Http\Exception\NotFoundException if record not found
     */
    public function import(string $id = null)
    {
        $entity = $this->Databanks->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['post', 'put'])) {
            if ($entity->import($this->request->getData('filename'))) {
                $this->Answer->success(
                    __('The file has been imported.'),
                    ['action' => 'view', $id]
                );
            }
            else {
                $this->Answer->error(__('The file could not be imported. Please, try again.'));
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Init the project database structure
     *
     * @param string|null $id Database id
     *
     * @return \Cake\Http\Response|void redirects on successful import, renders view otherwise
     * @throws \Cake\Http\Exception\NotFoundException if record not found
     */
    public function init(string $id = null)
    {
        $entity = $this->Databanks->get($id, ['contain' => []]);

        if ($this->request->is(['post', 'put'])) {
            if ($entity->initDatabase()) {
                $this->Answer->success(__('The database has been initialized.', ['action' => 'view', $id]));
            }
            else {
                $this->Answer->error(__('The database could not be initialized. Please, try again.'));
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Select database names
     *
     * @return void
     */
    public function select()
    {
        $connections = $this->Databanks->getConnections();
        $this->Answer->addAnswer(compact('connections'));
    }

    /**
     * Create database
     *
     * @param string|null $id database id
     * @return \Cake\Http\Response|void redirects on successful create, renders view otherwise
     * @throws \Cake\Http\Exception\NotFoundException if record not found
     * @var \App\Model\Entity\Databank $databank
     *
     */
    public function create(string $id = null)
    {
        $entity = $this->Databanks->get($id, ['contain' => []]);

        if ($this->request->is(['post', 'put'])) {
            if (!$entity->createDatabase()) {
                $this->Answer->error(__('The database could not be created. Please, check your permissions and check the database name.'));
            }
            else {
                if (!$entity->initDatabase()) {
                    $this->Answer->error(__('The database could not be initialized. Please, try again.'));
                }
                else {
                    $this->Answer->success(__('The database has been created and initialized.'), ['action' => 'view', $id]);
                }
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Drop a database.
     *
     * @param string|null $id database id
     *
     * @return \Cake\Http\Response|void redirects on successful drop, renders view otherwise
     * @throws \Cake\Http\Exception\NotFoundException if record not found
     */
    public function drop(string $id = null)
    {
        $entity = $this->Databanks->get($id, ['contain' => []]);
        if ($this->request->is(['post', 'put'])) {
            if ($entity->dropDatabase()) {
                $this->Answer->success(__('The database has been dropped.', ['action' => 'view', $id, 'database' => false]));
            }
            else {
                $this->Answer->error(__('The database could not be dropped. Please, try again.'));
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Delete a database record
     *
     * @param string $id Entity id
     * @return \Cake\Http\Response|void|null redirects to index
     */
    public function delete(string $id)
    {
        $entity = $this->Databanks->get($id, ['contain' => []]);
        if ($this->request->is(['delete'])) {
            if ($this->Databanks->delete($entity)) {
                $entity->deleted = 1;
                $this->Answer->success(__('The database connection has been deleted.'));
            }
            else {
                $this->Answer->error(__('The database connection could not be deleted. Please, try again.'));
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

}
