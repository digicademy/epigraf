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

        // If a user has at least one permission allowing access to the database with the request scope,
        // the user gains all wired permissions of the role associated with the permission.
        $permissions = array_merge($user['permissions'] ?? [], $user['rolepermissions'] ?? [], $user['guestpermissions'] ?? []);
        foreach ($permissions as $permission) {
            if (
                (($permission['permission_type'] ?? '') === 'access') &&
                (($permission['entity_type'] ?? '') === 'databank') &&
                ((($permission['entity_name'] ?? '') === $selectedDatabase) || (($permission['entity_name'] ?? '') === '*')) &&
                ((($permission['user_request'] ?? '') === $requestScope) || (($permission['user_request'] ?? '') === '')) &&
                $this->inWiredPermissions($permission['user_role'] ?? $user['role'] ?? '', $requestScope, $requestAction)
            ) {
                return true;
            }
        }

        return $this->inWiredPermissions($user['role'] ?? '', $requestScope, $requestAction);
    }
}
