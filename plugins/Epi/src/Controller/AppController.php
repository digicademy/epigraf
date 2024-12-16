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

use App\Controller\AppController as BaseController;
use App\Model\Entity\Databank;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Epi\Model\Table\BaseTable;

/**
 * Base controller for access to Epigraf databases,
 * inherits from global AppController,
 * inits database connection.
 */
class AppController extends BaseController
{

    /**
     * Type definitions (property types etc.)
     *
     * Add elements that should be loaded.
     *
     * @var array
     */
    public $types = [];

    /**
     * beforeFilter callback
     *
     * Read database from query param and set connection for models
     *
     * @param \Cake\Event\Event $event Event.
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Http\Exception\BadRequestException if no database is selected
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        if (empty($this->activeDatabase)) {
            throw new BadRequestException('No database selected.');
        }

        BaseTable::$databaseUserId = $this->getDatabaseUserId();

        $this->viewBuilder()->addHelper('Epi.Types');
    }

    /**
     * Find the project database user ID by the main database user IRI.
     *
     * @return integer|null The user ID or null.
     */
    protected function getDatabaseUserId()
    {
        if (empty(BaseTable::$userIri)) {
            return null;
        }

        $users = $this->fetchTable('Epi.Users');
        $user = $users
            ->find('all')
            ->select('id')
            ->where(['norm_iri' => BaseTable::$userIri])
            ->first();

        return $user ? $user['id'] : null;
    }

    /**
     * Check whether the user is allowed to access an action.
     * Hardwired permissions are only valid for the default user database.
     * Otherwise, check the granted permissions in the database.
     *
     * @param array $user The user data from the auth component
     * @return bool
     */
    /**
     * Check whether the user is allowed to access an action
     * by comparing the action and request scope with the $authorized property
     *
     * @param array $user The user data from the auth component
     * @return bool
     */
    protected function hasWiredPermission($user) {

        $selectedDatabase = $this->request->getParam('database');
        if (empty($selectedDatabase)) {
            return false;
        }
        $selectedDatabase = Databank::addPrefix($selectedDatabase);

        $requestScope = $this->_getRequestScope();
        $requestAction = $this->request->getParam('action');

        $permissions = array_merge($user['permissions'] ?? [], $user['rolepermissions'] ?? []);
        foreach ($permissions as $permission) {
            if (
                (($permission['permission_type'] ?? '') === 'access') &&
                (($permission['entity_type'] ?? '') === 'databank') &&
                (
                    (($permission['entity_name'] ?? '') === $selectedDatabase) ||
                    (($permission['entity_name'] ?? '') === '*')
                ) &&
                (
                    (($permission['user_request'] ?? '') === $requestScope) ||
                    (($permission['user_request'] ?? '') === '')
                )
            ) {
                $userRole = $permission['user_role'] ?? $user['role'] ?? '';
                $allowedActions = $this->authorized[$requestScope][$userRole] ?? [];

                if (in_array($requestAction, $allowedActions)) {
                    return true;
                }
                elseif (in_array('*', $allowedActions)) {
                    return true;
                }
            }
        }

        // Admins and devels are gods on the web, if not hardwired otherwise
        $userRole = $user['role'] ?? '';
        if (($requestScope === 'web')  && in_array($userRole, ['admin','devel'])) {
            if (!isset($this->authorized[$requestScope][$userRole])) {
                return true;
            }
            $actions = $this->authorized[$requestScope][$userRole] ?? [];
            if (in_array($requestAction, $actions) || in_array('*', $actions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the permission mask for project database specific endpoints
     *
     * TODO: implement a PermissionComponent
     *
     * The result is used in AppController::hasGrantedPermission() to determine permissions.
     *
     * @param array|null $user If null, the current user is used.
     * @return array
     */
    public function getPermissionMask($user = null)
    {
        $action = 'epi/' . strtolower($this->request->getParam('controller')) . '/' . strtolower($this->request->getParam('action'));
//        $database = $this->request->getParam('database') ?? $this->request->getQuery('database') ?? $user['databank']['name'] ?? false;

        $selectedDatabase = $this->request->getParam('database');
        if (!empty($selectedDatabase)) {
            $selectedDatabase = Databank::addPrefix($selectedDatabase);
        }


        $permission = [
            'user_id' => $this->_getUserId($user),
            'user_role' => $this->_getUserRole($user, $selectedDatabase),
            'user_request' => $this->_getRequestScope(),
            'entity_type' => 'databank',
            'entity_name' => $selectedDatabase,
            'permission_type' => 'access',
            'permission_name' => $action
        ];

        return $permission;
    }

}
