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

use App\Model\Behavior\TreeCorruptException;
use App\Model\Table\JobsTable;
use App\Utilities\Converters\Attributes;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Epi\Controller\Component\TransferComponent;
use Epi\Model\Entity\Article;
use Rest\Controller\Component\LockTrait;

/**
 * Articles Controller
 *
 * TODO: use ActionsComponent
 *
 * @property \Epi\Model\Table\ArticlesTable $Articles
 * @property JobsTable $Jobs
 * @property TransferComponent $Transfer
 */
class ArticlesController extends AppController
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
            'coder' => ['lock', 'unlock'],
            'author' => ['lock', 'unlock'],
            'editor' => ['lock', 'unlock']
        ],
        'web' => [
            'guest' => ['view', 'index'],
            'reader' => ['index', 'view', 'images'],
            'desktop' => ['index', 'view', 'items'],
            'coder' => ['index', 'view', 'edit','unlock', 'lock'],
            'author' => ['index', 'view', 'items','add','edit','delete', 'unlock', 'lock'],
            'editor' => ['index', 'view', 'items','add','edit','delete', 'unlock', 'lock']
        ]
    ];

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\Event $event
     *
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->loadComponent('Epi.Transfer', ['model' => 'Epi.Articles']);
    }

    /**
     * beforeRender callback
     *
     * @param EventInterface $event
     *
     * @return \Cake\Http\Response|void|null
     */
    public function beforeRender(EventInterface $event)
    {
        // Call AppController::beforeRender
        parent::beforeRender($event);

        // Cache API views
        $cacheConfigName = $this->initViewCache();
        $this->viewBuilder()->setOption('cacheConfig', ['config' => $cacheConfigName, 'key' => $this->getCacheKey()]);
    }

    /**
     * Retrieve a list of articles.
     *
     * Provides the following query parameters:
     * - columns [alias fields (deprecated)]
     * -snippets
     *
     * -template
     * -sort
     * -direction
     * -shape
     * -idents
     *
     * -deleted
     * -published
     *
     * -articletypes [alias articles_articletypes]
     * -sectiontypes
     * -itemtypes
     * -propertytypes
     * -field [alias articles_field]
     * -term [alias articles_term]
     * -articles [alias articles_articles]
     * -projects [alias articles_projects]
     * -properties
     * -lat
     * -lng
     * -tile
     * -lanes
     * -lane
     *
     * @return \Cake\Http\Response|void|null
     */
    public function index()
    {
        // Get search parameters from request
        [$params, $columns, $paging, $filter] = $this->Actions->prepareParameters();

        // Build query and get data
        // TODO: only fetch necessary data, not complete data (constrain by fields-parameter and by sortableFields)
        $query = $this->Articles
            ->find('hasParams', $params)
            ->find('containFields', $params)
            ->cache(
                $this->getCacheKey(),
                $this->Articles->initResultCache()
            );

        $this->paginate = $paging;
        $entities = $this->paginate($query);

        // Summary statistics (missing geocodings)
        // TODO: implement lazy loading (generator in combination with iteratoraggregate?)
        // TODO: remove ugly api condition, maybe call in view template? Or use snippet parameter?
        $summary = [];
        if (!$this->request->is('api')) {
            $summary = $this->Articles->getSummary($params);
        }

        $this->Answer->addOptions(compact('params', 'columns', 'filter'));
        $this->Answer->addAnswer(compact('entities', 'summary'));
    }

    /**
     * View article action
     *
     * ### Query parameters:
     * - snippets: a comma separated list with values "paths", "search", "indexes"
     * - template: a string with the template name
     *
     * See parseRequestParameters for more query parameters (not all are relevant for this action)
     *
     * @param string $id article id
     * @param boolean $showSections Show the sidemenu
     *
     * @return void|Response
     * @throws RecordNotFoundException If record not found
     */
    public function view($id, $showSections = true)
    {
        // Params
        $requestParams = $this->request->getQueryParams();
        $params = $this->Articles->parseRequestParameters($requestParams, $id, 'view');

        // Specific view parameters
        $findParams = $params;
        $findParams['articles'] = (int)$id;
        $findParams['regroup'] = true;
        $template = $requestParams['template'] ?? '';

        $findParams['snippets'][] = 'search';
        $findParams['snippets'][] = 'footnotes';
        $findParams['snippets'][] = 'notes';
        $findParams['action'] = 'view';

        // Load data
        try {
            /** @var  $entity Article */
            $entity = $this->Articles
                ->find('hasParams', $findParams)
                ->find('containAll', $findParams)
                ->first();

        } catch (TreeCorruptException $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect(['action' => 'mutate','?' => ['articles' => $id, 'task' => 'sort']]);
        }

        if (!$entity) {
            $this->Answer->redirectToLogin(__('This article does not exist or is not published.'));
        }

        $this->Lock->releaseLock($entity);

        // Menu
        if ($showSections) {
            $this->sidemenu = $entity->getMenu('last');
        }

        // Indexes
        if (in_array('indexes', $findParams['snippets'] ?? [])) {
            $indexes = $this->Articles->getIndexes();
        } else {
            $indexes = [];
        }

        // Results
        $this->Answer->addAnswer(compact('entity','indexes'));
        $this->Answer->addOptions(compact('params', 'template'));
    }

    /**
     * Get items list
     *
     * Retrieve list of items inside of articles,
     * filtered by article parameters. Used for map views.
     *
     * //TODO: use index action
     *
     * @return \Cake\Http\Response|void|null
     */
    public function items()
    {
        $this->redirectToUserSettings();

        // Update user parameters or redirect to user parameters
        $requestParams = $this->request->getQueryParams();

        // Load model
        $itemsTable = $this->fetchTable('Epi.Items');

        // Get search parameters from request
        $params = $this->Articles->parseRequestParameters($requestParams);
        $columns = $itemsTable->getColumns($params['columns'] ?? []);
        $paging = $itemsTable->getPaginationParams($requestParams, $columns);

        // Build query and get data
        $query = $itemsTable
            ->find('hasArticleParams', $params)
            ->order(['Items.articles_id' => 'ASC', 'Items.id' => 'ASC'])
            ->find('containFields', $params)
            ->find('deleted', $params);

        if (($params['template'] ?? '') === 'raw') {
            $query = $query->enableHydration(false);
        }

        // Get items
        $paging['maxLimit'] = 1000;
        $this->paginate = $paging;
        $this->ApiPagination->setConfig('model', 'items');

        // Cache query results
        $query = $query->cache($this->getCacheKey(), $this->Articles->initResultCache());

        $items = $this->paginate($query);


        $this->viewBuilder()->setOption('serialize', ['items']);
        $this->viewBuilder()->setOption('options', compact('params', 'columns'));

        //Search results
        $this->set(compact('items'));
    }

    /**
     * Edit an article
     *
     * @param string $id article id
     * @param boolean $showSections
     *
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function edit($id, $showSections = true)
    {
        // Params
        $params = $this->Articles->parseRequestParameters($this->request->getQueryParams(), $id, 'edit');

        // Specific view parameters
        $findParams = $params;
        $findParams['articles'] = (int)$id;
        $findParams['regroup'] = true;

        $findParams['snippets'][] = 'search';
        $findParams['snippets'][] = 'footnotes';
        $findParams['snippets'][] = 'notes';

        // Load data
        try {
            /** @var  $entity Article */
            $entity = $this->Articles
                ->find('hasParams', $findParams)
                ->find('containAll', $findParams)
                ->first();

        } catch (TreeCorruptException $e) {
            $this->Answer->error($e->getMessage(), ['action' => 'mutate','?' => ['articles' => $id, 'task' => 'sort']]);
        }

        if (!$entity) {
            throw new RecordNotFoundException('This article does not exist.');
        }

        // Lock record
        if (!Configure::read('debug', false)) {
            $this->Lock->createLock($entity, true);
        }
        // Save
        if ($this->request->is(['post', 'put'])) {

            $options = [
                'associated' => ['Sections', 'Sections.Items', 'Links', 'Footnotes'],
                'import' => true,
                'recover' => true,
                'fulltext' => true,
                'atomic' => true
            ];
            $entity = $this->Articles->patchEntity($entity, $this->request->getData(), $options);

            //TODO: auto fix issues, renumber sections...
            $hasSaved = $this->Articles->saveWithLinks($entity, $options);

            // Release lock before redirecting
            if ($hasSaved) {
                $this->Lock->releaseLock($entity);
            }

            if ($hasSaved) {
                // Redirect to section
                // TODO: implement save without exit button (using the redirect param)
                $redirectParams = $this->request->getQueryParams();
                $hash = $redirectParams['hash'] ?? '';
                $action =  $redirectParams['redirect'] ?? 'view';
                unset($redirectParams['redirect']);
                unset($redirectParams['hash']);

                $redirectUrl = null;
                if ($action !== 'edit') {
                    $redirectUrl = [
                        'action' => $action,
                        $entity->id,
                        '?' => $redirectParams,
                        '#' => $hash
                    ];
                }

                $this->Answer->success(
                    __('The article has been saved.'),
                    $redirectUrl
                );
            } else {
                $this->Answer->error(
                    __('Could not save article, please try again.'),
                    [],
                    ['errors' => $entity->getErrors()]
                );
            }
        }

        // Menu
        if ($showSections) {
            $this->sidemenu = $entity->getMenu('first');
            $this->sidemenu['move'] = true;
            $this->sidemenu['edit'] = true;
            $this->sidemenu['add'] = 'epi/' . $this->request->getParam('database') . '/sections/add/' . $entity->id;
            $this->sidemenu['delete'] = true;
        }

        // Results
        $this->Answer->addAnswer(compact('entity'));
        $this->Answer->addOptions(compact('params'));
        $this->render('view');
    }

    /**
     * Add method
     *
     * Add a new article.
     *
     * @param string|null $id article id
     *
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function add()
    {
        $default = array_filter([
            'published' => PUBLICATION_BINARY_UNPUBLISHED,
            'projects_id' => Attributes::commaListToIntegerArray($this->request->getQuery('projects_id'))[0] ?? null,
            'articletype' => Attributes::commaListToStringArray($this->request->getQuery('articletype'))[0] ?? '',
        ]);

        $this->Actions->add(
            null,
            $default,
            [
                'save' => ['associated' => ['Sections', 'Sections.Items'], 'addsections' => true],
                'open' => 'edit'
            ]
        );
    }

    /**
     * Delete method
     *
     * Delete specified article.
     *
     * @param string|null $id article id
     *
     * @return \Cake\Http\Response|null|void redirects to index
     * @throws RecordNotFoundException If record not found
     */
    public function delete($id = null)
    {
        $this->Actions->delete($id);
    }

    /**
     * Transfer method
     *
     * Transfer article to another database.
     *
     * @param $scope
     *
     * @return \Cake\Http\Response|null|void
     * @throws BadRequestException if record not found
     */
    public function transfer($scope = null)
    {
        $requestParams = $this->request->getQueryParams();

        // Defaults
        $requestParams['skip'] = 'properties,users,projects';

        if (($requestParams['term'] ?? '') === '') {
            unset($requestParams['term']);
            unset($requestParams['field']);
        }

        $this->Transfer->transfer('articles', $scope, $requestParams);
    }

    /**
     * Import method
     *
     * Import articles and all linked data from a csv file.
     *
     * @return Response|void|null
     */
    public function import()
    {
        /* Locks from EpiDesktop */
        if ($this->Lock->isDesktopLocked('articles')) {
            $this->Answer->error(
                __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
            );
        }

        return $this->Transfer->import('articles');
    }

    /**
     * Manipulate articles
     *
     * @return Response|void|null
     */
    public function mutate()
    {
        return $this->Transfer->mutate('articles');
    }

    /**
     * Recover tree
     *
     * Adjust lft, rght, level in specified tree.
     *
     * @deprecated Use mutate
     * @param $id
     *
     * @return \Cake\Http\Response|void|null
     */
    public function recovertree($id = null)
    {
        // Redirect to job system for all IDs
        if ($this->request->is('post') && ($id === null)) {
            //Create job
            $jobdata = [
                'type' => 'recover_tree',
                'config' => [
                    'database' => $this->activeDatabase['caption'],
                    'model' => 'Sections',
                    'scopefield' => 'articles_id'
                ]
            ];
            $jobsTable = $this->fetchTable('Jobs');
            $job = $jobsTable->newEntity($jobdata);

            if ($jobsTable->save($job)) {
                return $this->redirect([
                    'plugin' => false,
                    'controller' => 'Jobs',
                    'action' => 'execute',
                    $job->id,
                    '?' => ['database' => $job->config['database']]
                ]);
            } else {
                $this->Flash->error(__('The job could not be created. Please, try again.'));
                $this->redirect(['action' => 'index']);
            }
        } // Handle single ID
        elseif ($this->request->is('post')) {
            $scopes = [$id];

            foreach ($scopes as $scope) {
                $this->Articles->Sections->setScope($scope);
                $this->Articles->Sections->recover();
            }

            $this->Flash->error(__('Recovered tree of article {0}', $id));
            $this->redirect(['action' => 'view', $id]);
        }

        $this->set(compact('id'));
    }

}
