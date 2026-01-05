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

use Cake\Datasource\Exception\RecordNotFoundException;
use Rest\Controller\Component\LockTrait;

/**
 * Controller for project database users
 *
 * @property \Epi\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
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
        'web' => []
    ];

    public $help = 'administration/users';

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
     * Retrieve list of users
     *
     * @return void
     */
    public function index()
    {
        $this->Actions->index();
    }

    /**
     * Show a user account
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
     * Edit a user account
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
     * Add a new user account
     *
     * @return void
     */
    public function add()
    {
        $this->Actions->add();
    }

    /**
     * Delete a user account
     *
     * @param string $id Entity ID
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function delete($id)
    {
        $this->Actions->delete($id);
    }




}
