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

use App\Model\Table\PermissionsTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;

/**
 * Permissions Controller
 *
 * @property PermissionsTable $Permissions
 */
class PermissionsController extends AppController
{

    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
    ];

    public $help = 'administration/users';

    /**
     * Pagination setup
     *
     * @var array
     */
    public $paginate = [
        'className' => 'Total',
        'order' => ['modified' => 'desc'],
        'limit' => 100
    ];

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\Event $event Event
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->_activateMainMenuItem([
            'plugin' => false,
            'controller' => 'Users',
            'action' => 'index'
        ]);

        $this->pagetitle = [__('Permissions')];
    }

    /**
     * Retrieve  list of permissions.
     *
     * @return void
     */
    public function index()
    {
        $this->Actions->index();
    }

    /**
     * Show a list of endpoints
     *
     * @return void
     */
    public function endpoints()
    {
        $table = $this->Permissions->getEndpointTable(2);
        $columns = $table['columns'];
        $rows = $table['rows'];

        $this->viewBuilder()->setOption('options', compact('columns'));
        $this->set(compact('rows'));
    }

    /**
     * Show a permission
     *
     * @param string $id Permission id
     * @return void
     */
    public function view($id)
    {
        $this->Actions->view($id);
    }

    /**
     * Add a new permission
     *
     * @return void
     */
    public function add()
    {
        $this->Actions->add();
    }

    /**
     * Edit a permission
     *
     * @param string $id
     * @return void
     */
    public function edit($id)
    {
        $this->Actions->edit($id);
    }

    /**
     * Delete a permission
     *
     * @param string $id Permission ID
     * @return void
     */
    public function delete($id)
    {
        $this->Actions->delete($id);
    }
}
