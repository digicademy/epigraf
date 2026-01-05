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

use App\Model\Behavior\TreeCorruptException;
use App\Model\Entity\BaseEntity;
use App\Model\Interfaces\ScopedTableInterface;
use App\Model\Table\PermissionsTable;
use App\Utilities\Converters\Attributes;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\View\Exception\MissingTemplateException;
use Epi\Model\Entity\RootEntity;
use Epi\Model\Table\BaseTable;

/**
 * Action component
 *
 * Handles crud actions
 * TODO: use service instead, see https://book.cakephp.org/4/en/development/dependency-injection.html
 */
class ActionsComponent extends Component
{
    // Other components used
    public $components = ['Lock', 'Answer'];

    /**
     * Default configuration.
     *
     * @var array
     *
     */
    protected $_defaultConfig = [];

    /**
     * Parse request parameters and prepare conditions, columns and pagination
     *
     * The default controller model needs to support the prepareParameters() function.
     * User settings are stored and loaded from the user record using applyUserSettings().
     *
     * @return array An array of four elements: params, columns, paging and filter
     */
    public function prepareParameters($joined = false)
    {
        $model = $this->getController()->fetchTable();

        $requestAction = $this->getController()->getRequest()->getParam('action');
        $requestPath = $this->getController()->getRequest()->getParam('pass')[0] ?? '';
        $requestParams = $this->getController()->getRequest()->getQueryParams();

        [$params, $columns, $paging, $filter] = $model->prepareParameters($requestParams, $requestPath, $requestAction, $joined);
        $params = $this->applyUserSettings($params);

        return [$params, $columns, $paging, $filter];
    }

    /**
     * Update user settings
     *
     * @param string $scope
     * @param string $key
     * @param array $value
     * @param string $storage user (store in the user record) or session (store in the user session)
     *
     * @return array|mixed
     */
    public function updateUserSettings($scope = null, $key = null, $value = [], $storage = 'user')
    {

        if ($storage === 'session') {
            $session = $this->getController()->getRequest()->getSession();

            $settings = $session->read('User.settings', []);

            if ($scope && $key) {
                $value = [$key => $value];
                unset($settings[$scope][$key]);
            }
            if ($scope) {
                $value = [$scope => $value];
            }
            $settings = array_replace_recursive($settings, $value);

            $session->write('User.settings', $settings);
            return $settings;


        }
        else {

            $user = $this->getController()->Auth->user();
            $users = TableRegistry::getTableLocator()->get('Users');
            $user = $users->updateSettings($user, $scope, $key, $value);
            $this->getController()->Auth->setUser($user);
            $this->getController()->set(compact('user'));

            return $user['settings'] ?? [];
        }
    }

    /**
     * Merge user settings
     *
     * @param string $scope
     * @param string $key
     * @param array $value
     * @param string $storage user (store in the user record) or session (store in the user session)
     *
     * @return array|mixed
     */
    public function mergeUserSettings($scope = null, $key = null, $value = [], $storage = 'user')
    {
        if ($storage === 'session') {
            $session = $this->getController()->getRequest()->getSession();

            $settings = $session->read('User.settings', []);

            if ($scope && $key) {
                $value = [$key => $value];
            }
            if ($scope) {
                $value = [$scope => $value];
            }
            $settings = array_replace_recursive($settings, $value);

            $session->write('User.settings', $settings);
            return $settings;
        }
        else {

            $user = $this->getController()->Auth->user();
            $users = TableRegistry::getTableLocator()->get('Users');
            $user = $users->updateSettings($user, $scope, $key, $value);
            $this->getController()->Auth->setUser($user);

            return $user['settings'] ?? [];
        }
    }

    /**
     * Delete user settings
     *
     * @param string $scope
     * @param string $key
     * @param string $storage user (store in the user record) or session (store in the user session)
     *
     * @return array|mixed
     */
    public function deleteUserSettings($scope = null, $key = null, $storage = 'user')
    {

        if ($storage === 'session') {
            $session = $this->getController()->getRequest()->getSession();

            if ($scope && $key) {
                $session->delete('User.settings.' . $scope . '.' . $key);
            }
            else {
                if ($scope) {
                    $session->delete('User.settings.' . $scope);
                }
                else {
                    $session->delete('User.settings');
                }
            }
            $settings = $session->red('User.settings', []);
            return $settings;

        }
        else {
            $user = $this->getController()->Auth->user();
            $users = TableRegistry::getTableLocator()->get('Users');

            if ($key & $scope) {
                unset($user['settings'][$scope][$key]);
            }
            elseif ($scope) {
                unset($user['settings'][$scope]);
            }
            else {
                $user['settings'] = [];
            }

            $user = $users->updateSettings($user, $scope);
            $this->getController()->Auth->setUser($user);

            return $user['settings'] ?? [];
        }
    }

    /**
     * Reload user settings after they were changed
     *
     * Updates the settings in the auth object from the database
     *
     * @param int $id The user ID
     * @return array|mixed
     */
    public function reloadUserSettings($id)
    {
        $user = $this->getController()->Auth->user();
        if ($id === $user['id']) {
            $users = TableRegistry::getTableLocator()->get('Users');
            $user = $users->get($user['id'], ['finder' => 'auth'])->toArray();
            $this->getController()->Auth->setUser($user);
        }
        return $user;
    }


    /**
     * Get user settings
     *
     * @param string $scope
     * @param string $key
     * @param mixed $default The default value if the setting is not found
     * @param string $storage user (store in the user record) or session (store in the user session)
     *
     * @return array|mixed
     */
    public function getUserSettings($scope = '', $key = '', $default = [], $storage = 'user')
    {
        if ($storage === 'session') {
            $session = $this->getController()->getRequest()->getSession();
            $settings = $session->read('User.settings', null);
        }
        else {
            $user = $this->getController()->Auth->user();
            $settings = $user['settings'] ?? null;
        }

        if ($key && $scope) {
            return $settings[$scope][$key] ?? $default;
        }
        elseif ($scope) {
            return $settings[$scope] ?? $default;
        }
        else {
            return $settings ?? $default;
        }
    }

    /**
     * Get user settings from the session
     *
     * @param string $scope
     * @param string $key
     * @param mixed $default The default value if the setting is not found
     *
     * @return array|mixed
     */
    public function getSessionSettings($scope = '', $key = '', $default = [])
    {
        return $this->getUserSettings($scope, $key, $default, 'session');
    }

    /**
     * Load & save user settings
     *
     * @param array $params Request parameters
     * @return array The updated parameters
     */
    protected function applyUserSettings($params)
    {
        // No modification for API calls
        $request = $this->getController()->getRequest();
        if ($request->is('api')) {
            return $params;
        }

        // Only modify if requested
        $load = Attributes::isTrue($params['load'] ?? false);
        $save = Attributes::isTrue($params['save'] ?? false);

        if (!$load && !$save) {
            return $params;
        }

        // Endpoint identifier
        $endpointIdentifier = strtolower(implode('/',
            array_filter(
                [
                    $request->getParam('plugin', null),
                    $request->getParam('database', null),
                    $request->getParam('controller', ''),
                    $request->getParam('action', '')
                ]
            )
        ));

        $requestPath = $request->getParam('pass', [])[0] ?? '';

        // Save user settings by endpoint and by request
        if ($save) {
            $saveParams = array_diff_key($params, array_flip($this->getController()->paramsForNavigation));
            $saveParams['path'] = $requestPath;

            $this->updateUserSettings('paths', $endpointIdentifier, $saveParams);

            //Load user settings (identified by path, mode and template)
            $requestIdentifier = $endpointIdentifier
                . '/' . $requestPath
                . '?template=' . ($params['template'] ?? '')
                . '&mode=' . ($params['mode'] ?? '');

            $this->updateUserSettings('paths', $requestIdentifier, $saveParams);
            $this->updateUserSettings('log', 'request', $requestIdentifier);
        }

        // Redirect
        if ($load) {
            $userParams = $this->getUserSettings('paths', $endpointIdentifier);

            if (empty($requestPath)) {
                $model = $this->getController()->fetchTable();
                $requestPath = $userParams['path'] ?? $model->getDefaultScope(); //TODO: define defaults in array
            }

            if (empty($params['template'])) {
                $params['template'] = $userParams['template'] ?? ''; //TODO: define defaults in array
            }
            if ($params['template'] === '') {
                unset($params['template']);
            }

            //Load user settings (identified by path, mode and template)
            $requestIdentifier = $endpointIdentifier
                . '/' . $requestPath
                . '?template=' . ($params['template'] ?? '')
                . '&mode=' . ($params['mode'] ?? '');

            $userParams = $this->getUserSettings('paths', $requestIdentifier);
            unset($userParams['path']);

            $userParams = Attributes::paramsToQueryString($userParams);
            $params = Attributes::paramsToQueryString($params);
            $params = array_replace_recursive($userParams, $params);

            $redirectParams = array_diff_key($params, array_flip($this->getController()->paramsForNavigation));
            $redirectParams['save'] = true;

            $url = [
                $requestPath,
                '?' => $redirectParams
            ];

            $this->Answer->redirect($url);
        }

        return $params;
    }

    /**
     * Retrieve entities list
     *
     * ### Options
     * - sidemenu: Set to the parameter that should be used for the sidemenu handling, e.g. `category`. Disable by null (default).
     * - scopemenu: Set to the parameter that shoul be used to create a sidemenu for the scopes, e.g. propertytype. Disable by null (default).
     *
     * @param string|null $scope For scoped tables, the scope
     * @param array $options
     * @return void
     */
    public function index($scope = null, $options = [])
    {
        $model = $this->getController()->fetchTable();

        try {
            // Get search parameters from request
            [$params, $columns, $paging, $filter] = $this->prepareParameters();
            $params['action'] = 'index';

            // Assemble query
            $scopefield = $model->scopeField;
            $query = $model->find('hasParams', $params);

            // If the columns parameter is set to false, full entity data is requested
            // and serialized in Epi\BaseEntity::getExportFields().
            if (($params['columns'] ?? []) === false) {
                $query = $query->find('containAll', $params);
            } else {
                $query = $query->find('containColumns', $params);
            }

            // Get data
            $this->getController()->paginate = $paging;
            $entities = $this->getController()->paginate($query);
        } catch (TreeCorruptException $e) {
            $redirect = [];
            if (isset($model->scopeField)) {
                $redirect = [
                    'action' => 'mutate',
                    $params[$model->scopeField ?? ''] ?? $scope,
                    '?' => ['task' => 'batch_sort','sortby'=>'lft']
                ];
            }
            $this->Answer->error($e->getMessage(), $redirect);
        }
        // Get problems
        $problems = $model->getProblems();

        // Sidemenu
        $scopedMenuField = $options['scopemenu'] ?? null;
        $sidemenuField = $options['sidemenu'] ?? null;

        if (isset($scopedMenuField)) {
            $sideMenu = $model->getMenu();
            $this->getController()->sidemenu = $sideMenu;
            $this->getController()->activateSideMenuItem(['action' => 'index', $scope, '?' => ['load' => true]]);
        }
        elseif (isset($sidemenuField)) {
            // - If one hit only, redirect to the view action
            if (!empty($params[$sidemenuField]) and (count($entities) === 1) and (empty($params['id']))) {
                $this->Answer->redirect(['action' => 'view', $entities->first()->id]);
            }

            // - Activate menu item
            if (($params[$sidemenuField] ?? null) === '') {
                $this->getController()->activateSideMenuItem(['action' => 'index', '?' => [$sidemenuField => '']]);
            }
            else {
                if (($params[$sidemenuField] ?? null) === null) {
                    $this->getController()->activateSideMenuItem(['action' => 'index']);
                }
                else {
                    $this->getController()->activateSideMenuItem([
                        'action' => 'show',
                        '?' => [$sidemenuField => $params[$sidemenuField]]
                    ]);
                }
            }
        }

        // Output
        // TODO: set model in a way that can be used by filterTable()
        $this->Answer->addOptions(compact('params', 'scopefield', 'scope', 'columns', 'filter', 'problems'));
        $this->Answer->addAnswer(compact('entities'));

        // Summary
        if ($options['summary'] ?? false) {
            $summary = $model->getSummary($params);
            $this->Answer->addAnswer(compact('summary'));
        }
    }

    /**
     * View an entity
     *
     * ### Options
     * - sidemenu: Optionally, set to the parameter that should be used for the sidemenu handling, e.g. `category`. Disable by null (default).
     * - speaking: Optionally, set to the action name that can show entities by norm_iri (show).
     *
     * @param string $id The entity ID
     * @param array $options
     * @return void
     * @throws RecordNotFoundException If the record does not exist
     */
    public function view($id, $options = [])
    {
        /** @var BaseEntity $entity */
        $entity = $this->getController()->fetchTable()->get($id, ['finder' => 'containAll']);

        // Check if the entity is published
        if (!$entity->getEntityIsVisible()) {
            $this->Answer->redirectToLogin();
        }

        $this->Lock->releaseLock($entity);

        // Redirect to speaking URL
        if ($options['speaking'] ?? false) {
            $action = $this->getController()->getRequest()->getParam('action');
            if (($action === 'view') && !empty($entity->norm_iri)) {
                $redirectUrl = ['action' => 'show', $entity->norm_iri];
                $highlight = $this->getController()->getRequest()->getQuery('highlight');
                if (!empty($highlight)) {
                    $redirectUrl['?']['highlight'] = $highlight;
                }
                $this->Answer->redirect($redirectUrl);
            }
        }

        $this->Answer->addAnswer(compact('entity'));

        // Sidemenu handling
        $sidemenuField = $options['sidemenu'] ?? null;
        if (isset($sidemenuField)) {
            $this->getController()->activateSideMenuItem(
                [
                    'action' => empty($entity[$sidemenuField]) ? 'index' : 'show',
                    '?' => [$sidemenuField => $entity[$sidemenuField]]
                ]
            );
        }
    }

    /**
     * Show an entity by its IRI or category
     *
     * Redirects to the view action based on path or query parameter.
     * If the entity does not exist, redirects authenticated users
     * to the add action. Throws a NotFoundException for
     * unauthenticated users.
     *
     * For category based requests, only entities with menu=1 are considered.
     *
     * ### Options
     * - static Optionally, whether to load static pages from the root templates folder (templates/Pages) if the entity does not exist (true).
     *          For unauthenticated users, only the start page (templates/Pages/start.php) is accessible.
     *          Only authenticated admin and devel users can access other static pages.
     * - add Optionally, a list of user roles (admin, devel)
     *       that are redirected to the add action if the entity does not exist.
     *       Note that if used in combination with the fallback option, the user permissions are
     *       not checked for the target controller before redirecting.
     * - sidemenu Optionally, set to the parameter that should be used for the sidemenu handling, e.g. `category`. Disable by null (default).
     * - speaking Optionally, set to the action name that can show entities by norm_iri (show).
     * - fallback Optionally, the segment to which the request should be redirected if the entity does not exist.
     *             The segment name must match the controller name in camel case.
     *
     * @param string|null $iri The document IRI
     * @queryparam string|null key Alternative to provide the IRI
     * @queryparam string|null category The category
     * @param array $options
     * @return void|\Cake\Http\Response Return a response for static pages. Make sure to return the response in the controller.
     * @throws RecordNotFoundException If record not found and unauthenticated user
     */
    public function show($iri = null, $options = [])
    {
        $iri = $iri ?? $this->getController()->getRequest()->getQuery('key');
        $category = $this->getController()->getRequest()->getQuery('category');

        $model = $this->getController()->fetchTable();
        if (!empty($category) || empty($iri)) {
            $query = $model->find('all')->where(['category' => $category, 'menu' => 1]);

            if ($query->count() > 1) {
                return $this->Answer->redirect(['action' => 'index', '?' => ['category' => $category]]);
            }
            else {
                $entity = $query->first();
            }

        }
        else {
            $query = $model->find('all')->where(['norm_iri' => $iri]);
            $entity = $query->first();
        }

        // Redirect to wiki
        if (empty($entity) && !empty($iri) && !empty($options['fallback'])) {

            $segment = $model->getScope();
            $entity = $model
                ->setScope($options['fallback'])
                ->find('all')
                ->where(['norm_iri' => $iri])
                ->first();

            if (!empty($entity)) {
                $this->Answer->redirect(
                    ['controller' => Inflector::camelize($options['fallback']), 'action' => 'show', $iri]
                );
            }
            $model->setScope($segment);
        }

        // Redirect to speaking URL
        if (!empty($entity) && $options['speaking'] ?? false) {
            if (($entity->norm_iri !== $iri) && (!empty($entity->norm_iri))) {
                $this->Answer->redirect(
                    ['action' => $options['speaking'], $entity->norm_iri]
                );
            }
        }

        // Try to find static page
        if (empty($entity) && ($options['static'] ?? false) && !empty($iri)) {

            // Prevent directory traversal
            if (strpos($iri, '.')) {
                throw new ForbiddenException();
            }

            $segment = $this->getController()->segment;
            $templateFolder = $segment === 'pages' ? 'Pages' : 'Docs';

            $path = ROOT . '/templates/' . $templateFolder . '/' . $iri . '.php';
            if (file_exists($path)) {
                if (!$this->getController()->userHasRole(['admin', 'devel']) && ($iri !== 'start')) {
                    throw new ForbiddenException(__('You are not allowed to access this page, please login.'));
                }

                try {
                    $this->getController()->sidemenu = [];
                    return $this->getController()->render('/' . $templateFolder . '/' . $iri);
                } catch (MissingTemplateException $exception) {
                    throw new NotFoundException(__('Page not found.'));
                }
            }
        }

        // Redirect to add action
        if (empty($entity) && (!empty($options['add'] ?? false)) && !empty($iri)) {
            // TODO: When redirecting to the fallback, the permissions of the target controller should be checked.
            //       Maybe output the page not born message in the add action?
            if (!$this->getController()->userHasRole($options['add'])) {
                throw new NotFoundException('This page is not born yet.');
            }
            $data = Attributes::extractQueryParams($this->getController()->getRequest()->getQueryParams(), 'data-');
            $controller = Inflector::camelize($options['fallback'] ?? $this->getController()->getRequest()->getParam('controller'));
            $this->Answer->redirect(['controller' => $controller, 'action' => 'add', $iri, '?' => $data]);
        }

        if (empty($entity)) {
            throw new NotFoundException(__('Entity not found'));
        }

        $this->view($entity->id, $options);
    }

    /**
     * Show a static page by its IRI
     *
     * TODO: call static() from show() for static pages
     *
     * ### Options
     * - menu boolean Whether to keep the menu intact (default: true) or to clear it
     * - templates An array with supported template folders, keyed by the segment.
     *             Falls back to 'Docs' if no key for the segment is found.
     *
     * @param string|null $iri The document IRI
     * @param array $options
     * @return \Cake\Http\Response Return a response for static pages.
     *                             Make sure to return the response in the controller.
     * @throws NotFoundException If page not found and unauthenticated user
     */
    public function static($iri = null, $options = [])
    {

        // Try to find static page
        if (!empty($iri)) {

            // Prevent directory traversal
            if (strpos($iri, '.')) {
                throw new ForbiddenException();
            }

            $segment = $this->getController()->segment;

            $templateFolder = $options['templates'][$segment] ?? 'Docs';

            $path = ROOT . '/templates/' . $templateFolder . '/' . $iri . '.php';
            if (file_exists($path)) {
                if (!$this->getController()->userHasRole(['admin', 'devel']) && ($iri !== 'start')) {
                    throw new ForbiddenException(__('You are not allowed to access this page, please login.'));
                }

                try {
                    if (!($options['menu'] ?? true)) {
                        $this->getController()->sidemenu = [];
                    }
                    return $this->getController()->render('/' . $templateFolder . '/' . $iri);
                } catch (MissingTemplateException $exception) {
                    throw new NotFoundException(__('Page not found.'));
                }
            }
        }

        throw new NotFoundException(__('Page not found'));
    }


    /**
     * Edit an entity
     *
     * ### Options
     * - sidemenu: Optionally, set to the parameter that should be used for the sidemenu handling, e.g. `category`. Disable by null (default).
     * @param string $id
     * @param array $options
     * @return void Redirects on successful edit, renders view otherwise
     * @throws RecordNotFoundException If record not found
     */
    public function edit($id, $options = [])
    {
        $model = $this->getController()->fetchTable();
        $entityCaption = $model->getEntityName();

        /** @var BaseEntity $entity */
        $entity = $model->get($id, ['finder' => 'containAll']);

        if (!Configure::read('debug', false)) {
            $this->Lock->createLock($entity, true);
        }


        if ($this->getController()->getRequest()->is(['patch', 'post', 'put'])) {
            /** @var BaseEntity $entity */
            $entity = $model->patchEntity($entity, $this->getController()->getRequest()->getData(),
                $model->patchOptions ?? []);

            if ($model instanceof ScopedTableInterface && !empty($model->scopeField)) {
                $model->setScope($entity->{$model->scopeField});
            }

            if ($model instanceof BaseTable) {
                $hasSaved = $model->saveWithLinks($entity, $model->patchOptions ?? []);
            }
            else {
                $hasSaved = $model->save($entity, $model->patchOptions ?? []);
            }

            if ($hasSaved) {
                $this->Lock->releaseLock($entity);

                $action = Attributes::cleanOption(
                    $this->getController()->getRequest()->getQuery('redirect'),
                    ['view', 'edit'], 'view'
                );
                $redirect = ['action' => $action, $entity->id];
                $this->Answer->success(
                    __('The {0} has been saved.', $entityCaption),
                    $redirect
                );
            }
            else {
                $this->Answer->error(
                    __('The {0} could not be saved, please try again.', $entityCaption)
                );
            }
        }

        // Sidemenu handling
        $sidemenuField = $options['sidemenu'] ?? null;
        if (isset($sidemenuField)) {
            $this->getController()->activateSideMenuItem(
                [
                    'action' => empty($entity[$sidemenuField]) ? 'index' : 'show',
                    '?' => [$sidemenuField => $entity[$sidemenuField]]
                ]
            );
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Add a new entity
     *
     * //TODO: Set proceed URL as option that will be rendered in the form/entity/doc instead of URL parameter
     *         Harmonize with nexturl in jobs.js
     *
     * ### Options
     * - sidemenu: Optionally, set to the parameter that should be used for the sidemenu handling, e.g. `category`. Disable by null (default).
     * - save: Optionally, parameters passed to the save method, e.g.  ['associated' => ['Sections', 'Sections.Items']]
     * - sections: Set to true for articles. Calls addDefaultSections() if the entity is new.
     * - open: To open the new entity in a new tab, set the action, e.g. 'edit'
     *
     * @param array $default Default values for the entity
     * @param array $options
     * @return void
     */
    public function add($scope = null, $default = [], $options = [])
    {
        $model = $this->getController()->fetchTable();
        $entityCaption = $model->getEntityName();

        /* Locks from EpiDesktop */
        if (isset($scope)) {
            if ($this->Lock->isDesktopLocked($model->getTable(), $scope)) {
                $this->Answer->error(
                    __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
                );
            }
        }

        $entity = $model->newEntity($default);
        if (isset($scope)) {
            $entity->{$model->scopeField} = $scope;
        }

        if ($this->getController()->getRequest()->is('post')) {
            $entity = $model->patchEntity($entity, $this->getController()->getRequest()->getData());

            if ($model instanceof BaseTable) {
                $hasSaved = $model->saveWithLinks($entity, $options['save'] ?? []);
            }
            else {
                $hasSaved = $model->save($entity, $options['save'] ?? []);
            }

            if ($hasSaved) {
                $action = Attributes::cleanOption(
                    $this->getController()->getRequest()->getQuery('redirect'),
                    ['view', 'edit', 'index'], 'view'
                );
                $redirect = ['action' => $action, $entity->id];
                if ($options['open'] ?? false) {
                    $redirect['?'] = ['open' => Router::url(['action' => $options['open'], $entity->id])];
                }
                $redirect = array_merge($redirect, $options['redirect'] ?? []);
                $this->Answer->success(__('The {0} has been saved.', $entityCaption), $redirect, ['id' => $entity->id]);
            }
            else {
                $this->Answer->error(__('The {0} could not be saved. Please, try again.', $entityCaption));
            }
        }

        // Sidemenu handling
        $sidemenuField = $options['sidemenu'] ?? null;
        if (isset($sidemenuField)) {
            $this->getController()->activateSideMenuItem(
                [
                    'action' => empty($entity[$sidemenuField]) ? 'index' : 'show',
                    '?' => [$sidemenuField => $entity[$sidemenuField]]
                ]
            );
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Delete an entity
     *
     * ### Options
     * - sidemenu: Optionally, set to the parameter that should be used for the sidemenu handling, e.g. `category`. Disable by null (default).
     *
     * @param string $id Entity id
     * @param array $options
     * @return void
     * @throws RecordNotFoundException If the record does not exist
     */
    public function delete($id, $options = [])
    {
        $model = $this->getController()->fetchTable();
        $entityCaption = $model->getEntityName();

        /** @var BaseEntity $entity */
        $entity = $model->get($id);
        $this->Lock->releaseLock($entity);

        if (($entity instanceof RootEntity) && ($entity->hasDependencies)) {
            $this->Answer->error(
                __('The {0} has dependencies. Please remove the dependencies first.', $entityCaption),
                [
                    'action' => 'view',
                    $entity->id
                ]
            );
        }

        if ($this->getController()->getRequest()->is(['delete'])) {
            $this->Lock->createLock($entity, true);

            if ($entity->hasChildren) {
                if (!method_exists($model, 'resolve')) {
                    $this->Answer->error(
                        __('The {0} has children. Please remove the children first.', $entityCaption),
                        [
                            'action' => 'view',
                            $entity->id
                        ]
                    );
                }
                $result = $model->resolve($entity);
            } else {
                $result = $model->delete($entity);
            }

            $this->Lock->releaseLock($entity);

            if ($result) {
                $this->Answer->success(
                    __('The {0} has been deleted.', $entityCaption),
                );
            }
            else {
                $this->Answer->error(__('The {0} could not be deleted. Please, try again.', $entityCaption));
            }
        }

        // Sidemenu handling
        $sidemenuField = $options['sidemenu'] ?? null;
        if (isset($sidemenuField)) {
            $this->getController()->activateSideMenuItem(
                [
                    'action' => empty($entity[$sidemenuField]) ? 'index' : 'show',
                    '?' => [$sidemenuField => $entity[$sidemenuField]]
                ]
            );
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Move nodes to a new position
     *
     * This endpoint supports only POST requests containing
     * either a single move operation or a batch of move operations.
     *
     * ## Single move operation
     * The payload contains the following keys:
     * * - reference_id The ID of a reference node
     * * - reference_pos The position of the reference node: 'parent' or 'preceding'
     *
     * ### Batch move operations
     * The moves field contains an array of all moves.
     * Each move is an array with the following keys:
     * - id The ID of the node
     * - parent_id The ID of the new parent node
     * - preceding_id The ID of the preceding sibling node
     *
     * @param string $scope For single moves the node ID, for batch moves the scope (e.g. property type)
     * @return \Cake\Http\Response|null|void
     */
    public function move($scope)
    {
        $model = $this->getController()->fetchTable();
        $request = $this->getController()->getRequest();

        $batchMove = false;
        if ($request->is(['post', 'put'])) {
            $moves =$request->getData('moves', null);
            $batchMove = !is_null($moves) && is_array($moves);
        }

        // Move a single entity
        if (!$batchMove) {
            $entity = $model->get($scope, ['finder' => 'containAll']);

            if ($request->is(['post', 'put'])) {

                $entity = $model->patchEntity(
                    $entity, $request->getData(),
                    ['fields' => ['reference_id', 'reference_pos']]
                );

                $this->Lock->createLock($entity, true);
                $success = $model->moveToReference($entity);
                $this->Lock->releaseLock($entity);

                if ($success) {
                    $entity['moved'] = '1';
                    $this->Answer->success(
                        __('The property has been moved.')
                    );
                }
                else {
                    $this->Answer->error(
                        __('The node could not be moved. Please try again.')
                    );
                }
            }
            $this->Answer->addAnswer(compact('entity'));
        }

        // Process move operations
        else {
            $errors = [];
            foreach ($moves as $move) {

                // TODO: lock the items for other move operations
                $success = $model->moveTo(
                    $move['id'] ?? null,
                    $move['parent_id'] ?? null,
                    $move['preceding_id'] ?? null
                );

                if (!$success) {
                    $errors[] = __('Could not move node #{0} to the new target.', $move['id'] ?? null);
                }
            }

            if (!empty($errors)) {
                $this->Answer->addAnswer(['errors' => $errors]);
                $this->Answer->error(
                    __('{0} of {1} nodes could not be moved to their new target.', count($errors), count($moves))
                );
            }
            else {
                $this->Answer->success(
                    __('Moved {0} nodes to new targets.', count($moves))
                );
            }
        }
    }
}
