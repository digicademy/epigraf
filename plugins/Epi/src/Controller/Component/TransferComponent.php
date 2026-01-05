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

namespace Epi\Controller\Component;

use App\Controller\AppController;
use App\Model\Entity\Databank;
use App\Model\Table\DatabanksTable;
use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Datasource\FactoryLocator;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\Event\EventInterface;
use Epi\Model\Table\BaseTable;

/**
 * Transfer component
 *
 * Import data, transfer data between databases, and manipulate data in batches.
 *
 * - See TransferComponent->transfer() method.
 * - See JobTransfer which derivates from JobImport.
 * - See Transfer/transfer.php for the settings.
 *
 * TODO: rename to JobComponent
 *
 */
class TransferComponent extends Component
{
    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
    ];

    /**
     * Parameters managed by the transfer component
     *
     * The transfer parameters will be stripped from the redirect URL.
     * The job parameters will be stripped from the job's filter criteria.
     *
     * TODO: remove redundancy, better naming scheme
     */
    protected $parameters = [
        'scope',
        'source', 'target', 'stage','close', 'tablename','skip',
        'tree', 'versions', 'dates', 'fulltext','files', 'comments','snippets', 'published'
    ];

    /**
     * Current controller
     *
     * @var AppController
     */
    protected $controller;

    /**
     * Current request
     *
     * @var ServerRequest
     */
    protected $request;

    /**
     * Current model
     *
     * @var BaseTable
     */
    protected $model;

    /**
     * Current Scope
     *
     * Tables with the tree behavior always need a scope when saving data.
     * See _reconnectModel.
     *
     * @var string
     */
    protected $scope;

    /**
     * Current database
     *
     * @var DatabanksTable
     */
    protected $Databanks;

    /**
     * Startup method
     *
     * @param \Cake\Event\EventInterface $event Event instance.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function startup(EventInterface $event)
    {
        $this->controller = $this->getController();
        $this->request = $this->controller->getRequest();
        $this->Databanks = FactoryLocator::get('Table')->get('Databanks');

        $modelname = $this->getConfig('model');
        $this->model = FactoryLocator::get('Table')->get($modelname);

        //TODO: check whether model implements findIri, cloneEntities, transferNodes, scopeField
        // maybe use an interface class?
    }

    /**
     * Activate target database for interdatabase transfer of data
     *
     * @param $dbname
     *
     * @return array|\Cake\Datasource\EntityInterface
     */
    protected function _activateTargetDatabase($dbname)
    {
        $db = $this->Databanks->activateDatabase($dbname);
        $this->_reconnectModel();

        return $db;
    }

    /**
     * Deactivate target database after interdatabase transfer
     *
     * @return array|\Cake\Datasource\EntityInterface
     */
    protected function _activateSourceDatabase()
    {
        $db = $this->Databanks->deactivateDatabase();
        $this->_reconnectModel();

        return $db;
    }

    /**
     * Reconnect model
     *
     * Get a new instance of the model Since the connection is not updated in the model.
     *
     * @return void
     */
    protected function _reconnectModel()
    {
        $modelname = $this->getConfig('model');
        $this->model = FactoryLocator::get('Table')->get($modelname);

        if ($this->scope) {
            $this->model->setScope($this->scope);
        }

        $this->entityclass = $this->model->getEntityClass();
    }

    /**
     * Get a pipeline ID from the IRI or ID
     *
     * @param string|int $iri Numeric values will be returned as is.
     *                        Strings will be looked up in the pipelines table.
     * @return int
     */
    protected function _getPipelineId($iri)
    {
        // Pipeline from norm_iri
        if (!is_numeric($iri)) {
            $pipeline = $this->Jobs->fetchTable('Pipelines')
                ->find('all')
                ->where(['norm_iri' => $iri])
                ->first();
            $iri = $pipeline['id'] ?? null;
        }

        return $iri;
    }

    protected function _getPipelineList($pipelineType, $tableName, $params)
    {
        $pipelines = [];
        // TODO: What about pipelines for other tables?
        if ($tableName === 'articles') {
            $pipelinesTable = $this->Jobs->fetchTable('Pipelines');
            if (in_array($this->userRole, ['admin', 'devel'])) {
                $pipelines = $pipelinesTable->find('list');
            }
            // Get configured pipelines
            else {
                $pipelines = $pipelinesTable->find('forArticles', $params);
            }

            $pipelines = $pipelines
                ->where(['type' => $pipelineType])
                ->order(['name' => 'asc'])
                ->toArray();
        }
        return $pipelines;
    }

    /**
     * Import method
     *
     * Import data from a file or JSON data in the payload.
     * The method supports both GET and POST requests.
     * You must decide between uploading a file, providing JSON data or referring to a filename on the server.
     *
     * POST requests support the following payload keys:
     * - file: Uploaded file.
     * - data: JSON data to import.
     * - filename: Name of a file on the server to import.
     * - pipeline_id: ID of a pipeline to use for the import. Only used in combination with a filename.
     * - tree [0 or 1]: Whether to update the tree structure after data has been imported (default: '1').
     * - solved [0 or 1]: Whether to return a mapping of input IDs and database IDs (default: '0').
     *                    Handle with care, as the mapping is saved in the job's result field and may grow large.
     *
     * GET requests support the following query parameters:
     * - filename: Name of a file on the server to import.
     * - pipeline_id: ID of a pipeline to use for the import. Only used in combination with a filename.
     *
     *
     * @param string $scope The table scope
     *
     * @return \Cake\Http\Response|null|void
     * @throws BadRequestException if record not found
     */
    public function import($scope = null)
    {
        if (empty($this->model)) {
            throw new BadRequestException(__('Model not configured'));
        }
        $tableName = $this->model->getTable();
        $pipelineId = null;

        $this->Jobs = FactoryLocator::get('Table')->get('Jobs');

        // Create import folder
        $folder = Configure::read('Data.databases') . $this->controller->activeDatabase['name'] . DS;
        Files::createFolder($folder . 'import' . DS, true);

        // Upload file
        if ($this->request->is('post') && $this->request->getData('file')) {

            $file = Files::saveUploadedFile($folder . '/import', $this->request->getData('file'));

            if (empty($file['error'])) {
                $source = 'import/' . $file['filename'];
            }
            else {
                $source = '';
                $this->controller->Answer->error(
                    __('The file could not be uploaded: {0}', $file['error'])
                );
            }
        }

        // Upload JSON / array
        elseif ($this->request->is('post') && $this->request->getData('data', '')) {
            $targetFile = 'import-' . date('Y-m-d\Th-i-sO') . '.csv';
            $file = Files::saveCsv($folder . '/import', $this->request->getData('data'), false, $targetFile);
            if (empty($file['error'])) {
                $source = 'import/' . $file['filename'];
            }
            else {
                $source = '';
                $this->controller->Answer->error(
                    __('The data could not be saved: {0}', $file['error'])
                );
            }
        }

        // Get file name
        elseif ($this->request->is('post') && $this->request->getData('filename', '')) {
            $source = $this->request->getData('filename', '');
            $pipelineId = $this->request->getData('pipeline_id', '');
        }
        elseif ($this->request->is('get')) {
            $source = $this->request->getQuery('filename', '');
            $pipelineId = $this->request->getQuery('pipeline_id', '');
        }
        else {
            $this->controller->Answer->error(__('Data is missing.'));
            $source = '';
        }

        // Validate file name
        $source = rtrim($source, '/');
        $filePath = $folder . $source;
        if (!empty($source) && realpath($filePath) !== $filePath) {
            throw new BadRequestException();
        }

        // Create job entity
        // Delayed jobs will be processed by a worker
        $delayedJob = !empty(Configure::read('Jobs.delay', false));

        $tasksConfig = [];

        if (!empty($source) && file_exists($filePath)) {

            // Generate default task
            if (empty($pipelineId)) {
                $tasksConfig = [['number' => 1, 'type' => 'import', 'inputpath' => $filePath]];
            }

            $stage =  $this->request->is('post') ? 'import' : 'preview';
        } else {
            $stage = 'select';
        }

        $treeOption =  $this->request->getData('tree', '1');
        $solvedOption =  $this->request->getData('solved', '0');

        $jobConfig = [
            'pipeline_id' => $pipelineId,
            'pipeline_tasks' => $tasksConfig,

            'database' => $this->controller->activeDatabase['caption'],
            'table' => $tableName,
            'scope' => $scope,
            'inputpath' => $filePath,
            'tree' => $treeOption === '1',
            'solved' => $solvedOption === '1',
            'redirect' => Router::url(
                [
                    'controller' => $this->request->getParam('controller'),
                    'action' => 'index',
                    ($scope ? $scope : null),
                ])
        ];

        $jobdata = [
            'jobtype' => 'import',
            'delay' => $delayedJob ? 1 : 0,
            'config' => $jobConfig
        ];
        $job = $this->Jobs->newEntity($jobdata)->typedJob;

        // Preview: only get parameters
        if (($stage == 'preview') && $this->request->is('get')) {

            $page = $this->request->getQuery('page', 1);
            $preview = $job->preview(['page' => $page] + $jobConfig);
            $errors = $job->taskErrors;

            if (!empty($errors)) {
                $this->controller->Flash->error($errors[0]);
            }

            $this->controller->set(compact('preview', 'scope', 'source', 'pipelineId'));
        }

        // Import
        elseif (($stage == 'import') && $this->request->is('post')) {

            if ($this->Jobs->save($job)) {
                $this->controller->Answer->success(
                    false,
                    [
                        'plugin' => false,
                        'controller' => 'Jobs',
                        'action' => 'execute',
                        $job->id,
                        '?' => ['database' => $job->config['database'], 'close'=> false]
                    ],
                    ['job_id' => $job->id]
                );

            }
            else {
                $this->controller->Answer->error(
                    __('The job could not be created. Please, try again.'),
                    ['action' => 'index']
                );
            }
        }

        $this->controller->set(compact('job', 'scope', 'stage'));
        if (!$this->request->is('api')) {
            $pipelines = $this->_getPipelineList('import', $tableName, []);
            $this->controller->set(compact('pipelines'));

            $this->controller->render('/Transfer/import');
        }
        else {
            $this->controller->Answer->error(__('You need to post data to this endpoint.'));
        }
    }

    /**
     * Transfer records from source to target database
     **
     * @param string $scope The table scope
     * @param array $params Filter parameters
     *
     * @return \Cake\Http\Response|null|void
     * @throws BadRequestException if record not found
     */
    public function transfer($scope = null, $params = [])
    {
        if (empty($this->model)) {
            throw new BadRequestException(__('Model not configured'));
        }
        $tableName = $this->model->getTable();

        // Detect stage
        $current = Databank::removePrefix(BaseTable::getDatabaseName());
        $source = $this->request->getQuery('source', $current);
        $target = $this->request->getQuery('target');

        // Redirect to target database as soon as selected
        if (!empty($target)) {
            $params['stage'] = 'preview';
            $params['source'] = $source;
            $params['close'] = false;
            unset($params['target']);

            $this->controller->Answer->redirect(
                [
                    'database' => $target,
                    'action' => 'transfer',
                    $scope,
                    '?' => $params
                ]
            );
        }

        $target = $target ?? $current;
        if (!empty($source) && ($target === $current) && $this->request->is('post')) {
            $stage = 'transfer';
        } else {
            $stage = $this->request->getQuery('stage','select');
        }

        // Prepare job
        // TODO: split params into dataParams and transferParams, redirect to $dataParams
        // TODO: Let the Job decide which params are allowed
        $snippets = $params['snippets'] ?? 'published,iris';
        if (is_string($snippets)) {
            $snippets = Attributes::commaListToStringArray($snippets);
        } elseif (is_array($snippets) && !Arrays::array_is_simple($snippets)) {
            $snippets = Attributes::optionArrayToStringArray($snippets);
        }

        $published = $params['published'] ?? [];
        if (is_string($published)) {
            $published = Attributes::commaListToStringArray($published);
        } elseif (is_array($published) && !Arrays::array_is_simple($published)) {
            $published = Attributes::optionArrayToStringArray($published);
            $published = array_map(fn($x) => intval($x), Arrays::array_remove_prefix($published, 'val_'));
        }


        $jobConfig = [
            'database' => $target,
            'source' => $source,
            'table' => $tableName,
            'scope' => $scope,

            'params' =>
                [
                    'snippets' => $snippets,
                    'published' => $published
                ]
                + Arrays::array_remove_keys($params, array_merge($this->parameters,[
                    'page',
                    'sort',
                    'columns', 'template', 'collapsed',
                    'save', 'load'
                ])),

            'skip' => Attributes::commaListToStringArray($params['skip'] ?? ''),
            'tree' => ($params['tree'] ?? '0') === '1',
            'versions' => ($params['versions'] ?? '0') === '1',
            //'dates' => ($params['dates'] ?? '0') === '1',
            //'fulltext' => ($params['fulltext'] ?? '0') === '1',
            'timestamps' => false,
            'files' => ($params['files'] ?? '0') === '1',
            'copy' => ($source === $target),
            'task' => 'transfer',

            'redirect' => Router::url(
                [
                    'controller' => $this->request->getParam('controller'),
                    'action' => 'index',
                    $scope,
                    '?' => Arrays::array_remove_keys($params, array_merge($this->parameters, ['page','id']))
                ]
            )
        ];


        $this->Jobs = FactoryLocator::get('Table')->get('Jobs');

        // Delayed jobs will be processed by a worker
        $delayedJob = !empty(Configure::read('Jobs.delay', false));
        $jobdata = [
            'jobtype' => 'transfer',
            'delay' => $delayedJob ? 1 : 0,
            'config' => $jobConfig
        ];
        $job = $this->Jobs->newEntity($jobdata)->typedJob;

        // Preview
        if ($stage === 'preview') {
            $page = $this->request->getQuery('page', 1);
            $preview = $job->preview(['page' => $page] + $jobConfig);
            $errors = $job->taskErrors;

            if (!empty($errors)) {
                $this->controller->Flash->error($errors[0]);
            }

            $this->controller->set(compact('preview', 'scope'));
        }

        // Execute
        elseif ($stage === 'transfer') {

            if ($this->Jobs->save($job)) {
                $this->controller->Answer->success(
                    false,
                    [
                        'plugin' => false,
                        'controller' => 'Jobs',
                        'action' => 'execute',
                        $job->id,
                        '?' => ['database' => $job->config['database'], 'close'=> false]
                    ],
                    ['job_id' => $job->id]
                );

            }
            else {
                $this->controller->Answer->error(
                    __('The job could not be created. Please, try again.'),
                    ['action' => 'index']
                );
            }
        }

        elseif ($stage === 'select') {
            $databases = $this->controller->getAllowedDatabases();
            $this->controller->set('databases', $databases);
        }

        $this->controller->set(compact('job', 'scope', 'stage'));
        $this->controller->render('/Transfer/transfer');
    }

    /**
     * Export data through the job system
     *
     * @param string $scope
     * @return void
     */
    public function export($scope = null)
    {
        if (empty($this->model)) {
            throw new BadRequestException(__('Model not configured'));
        }
        $tableName = $this->model->getTable();

        // Prepare job
        $database = BaseTable::getDatabaseName();
        $params = $this->request->getQueryParams();

        if (!empty($scope)) {
            $params['scope'] = $scope;
        }

        // Delayed jobs will be processed by a worker
        $delayedJob = !empty(Configure::read('Jobs.delay', false));
        $selection = $params['selection'] ?? 'selected';

        // TODO: Refactor, move to model
        $jobdata = [
            'jobtype' => 'export',
            'delay' =>  $delayedJob ? 1 : 0,
            'config' => [
                'server' => Router::url('/', true),
                'database' => $database,
                'table' => $tableName,
                'scope' => $scope,
                'params' => $params,
                'selection' => $selection
            ]
        ];

        // Pipeline parameter
        $pipelineIri = $params['pipeline'] ?? null;

        // Pipeline from user settings
        // @deprecated: remember last setting
        if (($tableName === 'articles') && is_null($pipelineIri)) {
            $user = BaseTable::$user;
            $pipelineIri = $user['pipeline_article_id'] ?? null;
        }

        if (!empty($pipelineIri)) {
            $jobdata['config']['pipeline_id'] = $this->_getPipelineId($pipelineIri);
        }

        $this->Jobs = FactoryLocator::get('Table')->get('Jobs');
        $job = $this->Jobs->newEntity($jobdata)->typedJob;

        // Add pipeline data
        if (!empty($job->config['pipeline_id'])) {
            $pipeline = $this->Jobs->fetchTable('Pipelines')->get($job->config['pipeline_id']);
            $job = $job->patchOptions($pipeline, $this->request->getParsedBody());
        }

        // Add default tasks
        // TODO: Refactor, move to model
        elseif ($selection !== 'entity') {
            // TODO: better use patch function and merge in afterMarshal or beforeMarshal?
            $job->mergeJson('config', ['config' => $this->request->getData()]);
            $outputFormat = $job->config['format'] ?? 'xml';
            $job->config['pipeline_tasks'] = [
                [
                    'number' => 1,
                    'type' => 'data_' . $tableName, // TODO: Implement universal data task
                    'scope' => $scope,
                    'format' => $outputFormat,
                    'preset' => $job->config['preset'] ?? '',
                    'wrap' => true,
                    'expand' => Attributes::isTrue($job->config['expand'] ?? true),
                    'columns' => $job->config['params']['columns'] ?? ''
                ],
                [
                    'number' => 2,
                    'type' => 'save',
                    'bom' => $outputFormat !== 'xlsx',
                    'extension' => $outputFormat,
                    'download' => '0' // TODO: always force download?
                ]
            ];
        }

        // Save job
        if ($this->request->is('post')) {
            if ($this->Jobs->save($job)) {
                $this->controller->Answer->success(
                    false,
                    [
                        'plugin' => false,
                        'controller' => 'Jobs',
                        'action' => 'execute',
                        $job->id,
                        '?' => ['database' => $job->config['database'], 'close'=> false]
                    ],
                    ['job_id' => $job->id]
                );

            } else {
                $this->controller->Answer->error(
                    __('The job could not be created. Please, try again.')
                );
            }
        }

        // Get all pipelines
        $pipelines = $this->_getPipelineList('export', $tableName, $params);

        // Output
        $this->controller->set(compact('job', 'pipelines'));
        $this->controller->render('/Transfer/export');
    }

    /**
     * Manipulate records identified by query parameters
     *
     * @param string $scope The table scope
     *
     * @return \Cake\Http\Response|null|void
     * @throws BadRequestException if record not found
     */
    public function mutate($scope = null)
    {
        if (empty($this->model)) {
            throw new BadRequestException(__('Model not configured'));
        }
        $tableName = $this->model->getTable();

        // Prepare job
        $database = BaseTable::getDatabaseName();
        $params = $this->request->getQueryParams();

        if (!empty($scope)) {
            $params['scope'] = $scope;
        }

        $allowedTasks = $this->model->mutateGetTasks();
        $task = $params['task'] ?? '';

        // Delayed jobs will be processed by a worker
        $delayedJob = !empty(Configure::read('Jobs.delay', false));

        $jobdata = [
            'jobtype' => 'mutate',
            'delay' =>  $delayedJob ? 1 : 0,
            'config' => [
                'database' => $database,
                'table' => $tableName,
                'scope' => $scope,
                'params' => $params,
                'task' => $task,
                'selection' => $params['selection'] ?? 'selected',

                'redirect' =>
                    [
                        'plugin' => $this->request->getParam('plugin'),
                        'database' => $this->request->getParam('database'),
                        'controller' => $this->request->getParam('controller'),
                        'action' => 'index',
                        $scope
                    ]
            ]
        ];

        $this->Jobs = FactoryLocator::get('Table')->get('Jobs');
        $job = $this->Jobs->newEntity($jobdata)->typedJob;

        // Save job
        if ($this->request->is('post')) {
            // TODO: better use patch function and merge in afterMarshal or beforeMarshal?
            $job->mergeJson('config', $this->request->getData());

            $task = $job->config['task'] ?? '';
            if (!isset($allowedTasks[$task]) || empty($task)) {
                $this->controller->Answer->error(
                    __('You have no permission to execute the selected task or the task does not exist.'),
                    ['action' => 'mutate', $scope]
                );
            }

            // TODO: validate, config.task should never be empty
            if ($this->Jobs->save($job)) {
                $this->controller->Answer->success(
                    false,
                    [
                        'plugin' => false,
                        'controller' => 'Jobs',
                        'action' => 'execute',
                        $job->id,
                        '?' => ['database' => $job->config['database'], 'close' => false]
                    ],
                    ['job_id' => $job->id]
                );

            } else {
                $this->controller->Answer->error(
                    __('The job could not be created. Please, try again.'),
                    ['action' => 'index']
                );
            }
        }

        // Configure job
        $this->controller->set(compact('job'));
        $this->controller->render('/Transfer/mutate');
    }
}
