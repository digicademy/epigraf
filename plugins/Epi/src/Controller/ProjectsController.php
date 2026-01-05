<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Controller;

use Cake\Event\EventInterface;
use Epi\Controller\Component\TransferComponent;
use Rest\Controller\Component\LockTrait;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;

/**
 * Manage projects
 *
 * @property TransferComponent $Transfer
 */
class ProjectsController extends AppController
{

    /**
     * The lock trait provides lock and unlock actions
     */
    use LockTrait;

    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'api' => [
            'author' => ['lock', 'unlock'],
            'editor' => ['lock', 'unlock']
        ],
        'web' => [
            'guest' => ['view', 'index'],
            'reader' => ['index', 'view'],
            'coder' => ['index', 'view'],
            'desktop' => ['index', 'view'],
            'author' => ['index', 'view', 'add', 'edit', 'delete','lock','unlock'],
            'editor' => ['index', 'view', 'add', 'edit', 'delete','lock','unlock']
        ]
    ];

    public $help = 'introduction/projects';

    /**
     * Pagination setup
     *
     * @var array
     */
    public $paginate = [
        'className' => 'Total',
        'order' => ['name' => 'asc'],
        'limit' => 100
    ];

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->loadComponent('Epi.Transfer', ['model' => 'Epi.Projects']);
    }

    /**
     * Retrieve list of projects
     *
     * @return void
     */
    public function index()
    {
       $this->Actions->index();
    }

    /**
     * View a project
     *
     * @param string $id Entity ID
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function view($id)
    {
        $this->Actions->view($id);
    }

    /**
     * Edit a project
     *
     * @param string $id Entity ID
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function edit($id)
    {
       $this->Actions->edit($id);
    }

    /**
     * Add a new project
     *
     * @return void
     */
    public function add()
    {
        $this->Actions->add();
    }

    /**
     * Delete a project
     *
     * @param string $id Entity ID
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function delete($id)
    {
        $this->Actions->delete($id);
    }

    /**
     * Import a project.
     *
     * @return mixed
     */
    public function import()
    {
        /* Locks from EpiDesktop */
        if ($this->Lock->isDesktopLocked('projects')) {
            $this->Answer->error(
                __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
            );
        }

        return $this->Transfer->import();
    }

    /**
     * Transfer entity to another database
     *
     * @return \Cake\Http\Response|null|void
     * @throws BadRequestException When record not found.
     */
    public function transfer()
    {
        /* Locks from EpiDesktop */
        if ($this->Lock->isDesktopLocked('projects')) {
            $this->Answer->error(
                __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
            );
        }

        $requestParams = $this->request->getQueryParams();
        $this->Transfer->transfer(null, $requestParams);
    }
}
