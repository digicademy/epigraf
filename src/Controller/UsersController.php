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
use App\Model\Entity\User;
use App\Model\Table\PermissionsTable;
use App\Utilities\Converters\Attributes;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Log\Log;
use Rest\Controller\Component\LockTrait;

/**
 * Users Controller
 *
 * Login and administration of users
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    use LockTrait;

    /**
     * Access permissions
     *
     * See isAuthorized() method for further restrictions to self records
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'api' => [
            'guest' => ['login'],
            'reader' => ['login', 'track'],
            'coder' => ['login', 'track'],
            'desktop' => ['login', 'track'],
            'author' => ['login', 'track'],
            'editor' => ['login', 'track'],
            'admin' => ['login', 'track'],
            'devel' => ['login', 'track']
        ],
        'web' => [
            'guest' => ['login', 'logout'],
            'bot' => ['logout'],
            'reader' => ['start', 'login', 'logout', 'track', 'settings', 'view'],
            'coder' => ['start', 'login', 'logout', 'track', 'settings', 'view'],
            'desktop' => ['start', 'login', 'logout', 'track', 'settings', 'view', 'edit'],
            'author' => ['start', 'login', 'logout', 'track', 'settings', 'view', 'edit'],
            'editor' => ['start', 'login', 'logout', 'track', 'settings', 'view', 'edit']
        ]
    ];

    /**
     * Login method
     *
     * @return \Cake\Http\Response|null|void redirects to start page after login, render login view otherwise
     */
    public function login()
    {
        //$this->Auth->logout();

        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
            }
            else {
                $this->Flash->error(__('Invalid username or password, try again'));
            }
        }

        $loggedIn = !empty($this->Auth->user());
        if ($loggedIn && !$this->request->is('api')) {
            return $this->redirect($this->Auth->redirectUrl());
        }

        $this->Answer->addAnswer(['success' => $loggedIn]);
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|null Logout and redirect to login page
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Start method
     *
     * Redirects admin and devel users to list of database connections.
     * Redirects users to their default database.
     *
     * @return \Cake\Http\Response|null redirects to start page
     * @throws \Cake\Http\Exception\NotFoundException if no access to any database
     */
    public function start()
    {
        // Load last visited page...
        $requestIdentifier = $this->Actions->getUserSettings('log', 'request');
        $requestIdentifier = is_string($requestIdentifier) ? trim($requestIdentifier, " \n\r\t\v\x00/.") : '';

        if (!empty($requestIdentifier)) {
            $requestIdentifier .= '&load=1';
            $this->Actions->deleteUserSettings('log', 'request');
            return $this->redirect($requestIdentifier);
        }

        // ... or default pages
        $user = $this->Auth->user();
        if (
            !empty($user['databank']['name']) &&
            in_array(Databank::addPrefix($user['databank']['name']), array_keys($this->getAllowedDatabases()))
        ) {
            return $this->redirect([
                'controller' => 'Articles',
                'action' => 'index',
                'database' => Databank::removePrefix($user['databank']['name']),
                'plugin' => 'Epi'
            ]);
        }
        elseif (in_array($user['role'], ['admin', 'devel'])) {
            return $this->redirect(['controller' => 'Databanks', 'action' => 'index']);
        }
        else {
            return $this->redirect('/help');
        }
    }


    /**
     * Get or set user settings.
     *
     * Issue a put request to replace settings with the payload.
     * Issue a patch request to merge the payload with existing settings.
     * Issue a delete request to delete settings.
     *
     * @param string $scope Scope of the user settings, e.g. "paths". Leave empty to use the first settings key.
     * @param string $key Key of user settings, e.g. a specific path. Leave empty to use the second settings key.
     * @return void
     */
    public function settings($scope = null, $key = null)
    {
        $storage = Attributes::cleanOption($this->request->getQuery('storage'), ['user', 'session'], 'user');

        if ($this->request->is(['post', 'put'])) {
            $settings = $this->request->getData();
            $settings = array_intersect_key($settings, array_flip(preg_grep('/^_[^_]/', array_keys($settings))));
            $this->Actions->updateUserSettings($scope, $key, $settings, $storage);
        }
        elseif ($this->request->is('patch')) {
            $settings = $this->request->getData();
            $settings = array_intersect_key($settings, array_flip(preg_grep('/^[^_]/', array_keys($settings))));
            $this->Actions->mergeUserSettings($scope, $key, $settings, $storage);
        }
        elseif ($this->request->is('delete')) {
            $this->Actions->deleteUserSettings($scope, $key, $storage);
        }

        $settings = $this->Actions->getUserSettings($scope, $key, [], $storage);

        $this->set(compact('settings'));
        $this->viewBuilder()->setOption('serialize', ['settings']);
        $this->viewBuilder()->setClassName('Json');
    }

    /**
     * Clear settings method
     *
     * @param string|null $id user id
     * @return \Cake\Http\Response
     * @throws RecordNotFoundException If record not found
     */
    public function clearsettings($id = null)
    {
        $this->Actions->deleteUserSettings(null, null);
        $this->Flash->success(__('The user settings have been cleared.'));
        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Receive log entries from the client and store them in the server log file.
     *
     * @param string $level Log level (error, warning, info, debug)
     * @param string $message Log message
     * @return void
     */
    public function track($level = 'info', $message = '')
    {

        $status = false;
        if ($this->request->is(['post', 'put', 'patch'])) {
            $logData = $this->request->getData();

            $messageType = 'JS';
            if (is_array($logData)) {
                if (isset($logData['name'])) {
                    $messageType .= ': ' . $logData['name'];
                    unset($logData['name']);
                }
                foreach ($logData as $key => $value) {
                    $value = is_string($value) ? $value : json_encode($value);
                    $message .= "\n" . $key . ': ' . $value;
                }
            }

            $message = '[' . $messageType . '] ' . $message;
            Log::write($level, $message);

            $status = true;
        }

        $this->Answer->addAnswer(['response' => $status]);
    }

    /**
     * Retrieve list of users.
     *
     * @return void
     */
    public function index()
    {
        [$params, $columns, $paging, $filter] = $this->Actions->prepareParameters();

        $entities = $this->Users
            ->find('hasParams', $params)
            ->find('containFields', $params);

        $this->paginate = $paging;
        $entities = $this->paginate($entities);

        // TODO: move to model
        $connected = $this->Users->sqlConnectedUsers();
        foreach ($entities as $user) {
            unset($connected['epi_' . $user->username]);
        }
        $connected = array_map(
            fn($username, $connections) => ['username' => $username, 'connections' => $connections],
            array_keys($connected), array_values($connected)
        );

        // Count active users
        // TODO: move to model
        $summary = [];
        if (!$this->request->is('api')) {
            $activeusers = array_reduce($entities->toArray(), function ($carry, $user) {
                return $carry + ($user->sqlconnections || $user->active);
            }, 0);
            $summary[] = __('{0} active user(s).', $activeusers);
        }

        $this->Answer->addOptions(compact('params', 'columns', 'filter'));
        $this->Answer->addAnswer(compact('entities', 'connected', 'summary'));
    }

    /**
     * Show a user account
     *
     * @param string $id User id or 'me' for the currently logged-in user
     *
     * @return void
     */
    public function view($id = null)
    {
        if ($id === 'me') {
            $id = $this->Auth->user('id');
        }

        $entity = $this->Users->get($id, [
            'contain' => ['PermissionsById', 'PermissionsByRole', 'Databanks', 'ArticlePipelines', 'BookPipelines']
        ]);

        // TODO: create method addSection in LinkHelper instead of definig $sidemenu
        $this->sidemenu = [
            'tree' => 'fixed',
            'scrollbox' => true,
            'class' => 'widget-scrollsync',
            [
                'label' => 'Übersicht',
                'url' => '#toc-overview',
                'data' => ['data-id' => 'toc-overview']
            ],
            [
                'label' => 'Epigraf Desktop',
                'url' => '#toc-epidesktop',
                'data' => ['data-id' => 'toc-epidesktop']
            ],
            [
                'label' => 'Databases',
                'url' => '#toc-databases',
                'data' => ['data-id' => 'toc-databases']
            ],
            [
                'label' => 'Permissions',
                'url' => '#toc-permissions',
                'data' => ['data-id' => 'toc-permissions']
            ],
            [
                'label' => 'User settings',
                'url' => '#toc-usersettings',
                'data' => ['data-id' => 'toc-usersettings']
            ],
        ];

        $columns = array_merge(['database' => __('Database')], PermissionsTable::$userRoles);

        $this->Answer->addOptions(compact('columns'));
        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Set the password of a mySQL user or create a mySQL user
     *
     * @param string|null $id user id
     *
     * @return \Cake\Http\Response|void
     * @throws RecordNotFoundException If record not found
     */
    public function password($id = null)
    {
        /** @var \App\Model\Entity\User $user */
        $user = $this->Users->get($id, ['contain' => ['Databanks']]);

        if (empty($user->databank)) {
            throw new RecordNotFoundException(__('Select the default user database first.'));
        }

        if (($this->request->is(['patch', 'post', 'put']))) {

            if (
                $user->databank->setPassword(
                    'epi_' . $user['username'],
                    $this->request->getData('password')
                )
            ) {
                $this->Flash->success(__('The password has been changed.'));
                return $this->redirect(['action' => 'view', $id]);
            }
            else {
                $this->Flash->error(__('The password could not be changed.'));
            }
        }
        $this->set('user', $user);
    }

    /**
     * Create a new access token for a user
     *
     * @param string $id User id
     * @return \Cake\Http\Response|void redirects on successful edit, renders view otherwise
     * @throws \Cake\Http\Exception\NotFoundException if record not found
     */
    public function token($id = null)
    {
        $entity = $this->Users->get($id, ['contain' => []]);

        // If the currently logged-in user is an admin or devel,
        // allow changes to the identity fields of a user record.
        if (in_array($this->Auth->user('role'), ['admin', 'devel'])) {
            $entity->setAccess(['accesstoken'], true);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $token = $this->Users->generateAccesstoken();
            $entity['accesstoken'] = $token;
            if ($this->Users->save($entity)) {
                $this->Answer->success(__('Your new access token is {0}', $token), ['action' => 'view', $entity->id]);
            }
            else {
                $this->Answer->error(__('The user could not be saved. Please, try again.'));
            }
        }

        $this->Answer->error(__('This operation needs a post request.'), ['action' => 'view', $id]);
    }

    /**
     * Grant user access to a database, including EpiWeb and EpiDesktop access
     *
     * This creates a permission record for the combination of database, request scope, user id and user role.
     *
     * @param string|null $id User id
     * @param int $databankId The database ID can be either passed as path parameter
     *                        or in a form using the key databank_id.
     * @param string $scope web|api|desktop or null to set web and desktop access
     * @param string $role User role for which access is granted or null to grant for the user's default role.
     * @return \Cake\Http\Response|void
     * @throws RecordNotFoundException If record not found
     */
    public function grant($id = null, $databankId = null, $scope = null, $role = null)
    {
        if (($this->request->is('post'))) {

            $databankId = $this->request->getData('databank_id', $databankId);
            $scope = $this->request->getData('scope', $scope);
            $role = $this->request->getData('role', $role);

            if ($databankId === '*') {
                $this->Answer->error(__('Wildcard grants are not yet supported.'), ['action' => 'view', $id]);
            }

            // Select database
            $user = $this->Users->get($id, ['contain' => ['Databanks']]);
            if (!empty($databankId)) {
                $databank = $this->Users->Databanks->get($databankId);
            }
            else {
                $databank = $user->databank;
            }

            // Grant access
            if (empty($databank)) {
                $this->Answer->error(__('No database selected.'), ['action' => 'view', $id]);
            }
            else {
                $scope = Attributes::cleanOption($scope, ['web', 'api', 'desktop']);
                $role = Attributes::cleanOption($role, array_keys(PermissionsTable::$userRoles));

                $result = true;
                if (empty($scope) || ($scope === 'desktop')) {
                    $result = $result && $databank->grantDesktopAccess('epi_' . $user['username']);
                }

                if (empty($scope) || ($scope === 'web')) {
                    $result = $result && $databank->grantWebAccess($user['id'], $role);
                }

                if (empty($scope) || ($scope === 'api')) {
                    $result = $result && $databank->grantApiAccess($user['id'], $role);
                }

                if ($result) {
                    $this->Answer->success(__('Access has been granted.'), ['action' => 'view', $id]);
                }
                else {
                    $this->Answer->error(__('Access could not be granted.'), ['action' => 'view', $id]);
                }
            }
        }
        else {
            $entity = $this->Users->get($id, ['contain' => ['Databanks', 'ArticlePipelines', 'BookPipelines']]);
            $databanks = $this->Users->Databanks->find('list')->toArray();
            $scopes = ['web' => 'Web', 'api' => 'API', 'desktop' => 'Desktop'];
            $roles = PermissionsTable::$userRoles;
            $this->Answer->addOptions(compact('databanks', 'scopes', 'roles'));
            $this->Answer->addAnswer(compact('entity'));
        }
    }

    /**
     * Revoke access to a database, including EpiWeb and EpiDesktop access
     *
     * @param string|null $id user id
     * @param string $databank Either the numeric databank id or the databank name.
     *                           Alternatively, pass the database ID in a form using the key databank_id.
     * @param string $scope web|api|desktop or null to revoke all access.
     *                      An asterisk has the same effect as null.
     * @param string $role User role for which access is revoked or null to revoke access for all roles.
     *                     An asterisk has the same effect as null.
     * @return \Cake\Http\Response|null
     * @throws RecordNotFoundException If record not found
     */
    public function revoke($id = null, $databank = null, $scope = null, $role = null)
    {
        /** @var User $entity */
        $entity = $this->Users->get($id, ['contain' => ['Databanks']]);

        if ($this->request->is('delete') || $this->request->is('post')) {

            // Select database
            $databank = $this->request->getData('databank_id', $databank);

            if ($databank === '*') {
                $this->Answer->error(__('Wildcard grants are not yet supported.'), ['action' => 'view', $id]);
            }

            if (empty($databank)) {
                $databankEntity = $entity->databank;

            }
            elseif (is_numeric($databank)) {
                $databankEntity = $this->Users->Databanks->get($databank);
            }
            else {
                $databankEntity = $this->Users->Databanks->find('all')
                    ->where(['name' => $databank])
                    ->first();
            }

            $scope = Attributes::cleanOption($scope, ['web', 'api', 'desktop']);
            $role = Attributes::cleanOption($role, array_keys(PermissionsTable::$userRoles), null);

            $result = true;
            if (empty($scope) || ($scope === 'desktop')) {
                $result = $result && $databankEntity->revokeDesktopAccess('epi_' . $entity['username']);
            }

            if (empty($scope) || ($scope === 'web')) {
                $result = $result && $databankEntity->revokeWebAccess($entity['id'], $role);
            }

            if (empty($scope) || ($scope === 'api')) {
                $result = $result && $databankEntity->revokeApiAccess($entity['id'], $role);
            }

            if ($result) {
                $this->Answer->success(__('Access has been revoked.'));
            }
            else {
                $this->Answer->error(__('Access could not be revoked.'));
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Add a new user account
     *
     * @return void
     */
    public function add()
    {
        $entity = $this->Users->newEntity([]);

        // If the currently logged-in user is an admin or devel,
        // allow changes to the identity fields of a user record.
        if (in_array($this->Auth->user('role'), ['admin', 'devel'])) {
            $entity->setAccess(['role', 'username', 'norm_iri', 'accesstoken'], true);
        }

        if ($this->request->is('post')) {
            $entity = $this->Users->patchEntity($entity, $this->request->getData());
            if ($this->Users->save($entity, ['associated' => false])) {
                $this->Answer->success(__('The user has been saved.'), ['action' => 'view', $entity->id]);
            }
            else {
                $this->Answer->error(__('The user could not be saved. Please, try again.'));
            }
        }

        if (empty($entity->password)) {
            $entity->password = $this->Users->generateAccesstoken();
        }
        $databanks = $this->Users->Databanks->find('list')->toArray();
        $pipelines = $this->Users->BookPipelines->find('list')->toArray();

        $this->Answer->addAnswer(compact('entity', 'databanks', 'pipelines'));
    }

    /**
     * Edit a user account
     *
     * @param string $id User id
     * @return void
     */
    public function edit($id)
    {
        $entity = $this->Users->get($id, ['contain' => []]);

        // If the currently logged-in user is an admin or devel,
        // allow changes to the identity fields of a user record.
        if (in_array($this->Auth->user('role'), ['admin', 'devel'])) {
            $entity->setAccess(['role', 'username', 'norm_iri', 'accesstoken'], true);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $entity = $this->Users->patchEntity($entity, $this->request->getData());
            if ($this->Users->save($entity)) {
                $this->Actions->reloadUserSettings($entity->id);
                $this->Answer->success(__('The user has been saved.'), ['action' => 'view', $entity->id]);
            }
            else {
                $this->Answer->error(__('The user could not be saved. Please, try again.'));
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Delete a user account
     *
     * @param string $id User ID
     *
     * @return void
     */
    public function delete($id)
    {
        $entity = $this->Users->get($id);

        if ($this->request->is(['post', 'delete'])) {
            if ($this->Users->delete($entity)) {
                $entity->deleted = 1;
                $this->Answer->success(__('The user has been deleted.'));
            }
            else {
                $this->Answer->error(__('The user could not be deleted. Please, try again.'));
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Restrict to self records
     *
     * @param array|\ArrayAccess|null $user
     *
     * @return bool
     */
    public function isAuthorized($user)
    {
        // Deny non-admin access to other users' profiles
        if (!in_array($user['role'] ?? '', ['admin', 'devel'])) {

            $action = $this->request->getParam('action');
            if (!in_array($action, ['start', 'login', 'logout', 'settings', 'track'])) {

                $passedParams = $this->request->getParam('pass', []);
                $entityId = $passedParams[0] ?? null;
                if (empty($entityId)) {
                    return false;
                }
                if (!(((int)$entityId === $user['id']) || ($entityId === 'me'))) {
                    return false;
                }

            }
        }

        return parent::isAuthorized($user);
    }
}
