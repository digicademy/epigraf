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
 * Import data.
 * Transfer data between databases.
 * - See TransferCompontent->transfer() method.
 * - See JobTransfer which derivates from JobImport.
 * - See Transfer/transfer.php for the settings.
 *
 * Batch manipulate data.
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
     * Import method
     *
     * @param string $tablename @deprecated use $this->model->getTable()
     * @param string $scope The table scope
     *
     * @return \Cake\Http\Response|null|void
     *
     * @throws BadRequestException if record not found
     */
    public function import($tablename = 'articles', $scope = null)
    {
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
        }
        elseif ($this->request->is('get')) {
            $source = $this->request->getQuery('filename', '');
        }
        else {
            $this->controller->Answer->error(__('Data is missing.'));
            $source = '';
        }

        // Validate file name
        $filePath = $folder . $source;
        if (!empty($source) && realpath($filePath) !== $filePath) {
            throw new BadRequestException();
        }

        // Create job entity
        $job = false;
        if (!empty($source) && file_exists($filePath)) {

            $jobdata = [
                'typ' => 'import',
                'config' => [
                    'database' => $this->controller->activeDatabase['caption'],
                    'table' => $tablename,
                    'scope' => $scope,
                    'source' => $filePath,
                    'redirect' => Router::url(
                        [
                            'controller' => $this->request->getParam('controller'),
                            'action' => 'index',
                            ($scope ? $scope : null),
                        ])
                ]
            ];
            $job = $this->Jobs->newEntity($jobdata);
        }

        // Preview
        if ($job && $this->request->is('get')) {

            $page = $this->request->getQuery('page', 1);
            $preview = $job->preview(['page' => $page]);
            $errors = $job->taskErrors;

            if (!empty($errors)) {
                $this->controller->Flash->error($errors[0]);
            }

            $this->controller->set(compact('preview', 'scope', 'source'));

        }

        // Import
        elseif ($job && $this->request->is('post')) {

            if ($this->Jobs->save($job)) {
                $this->controller->Answer->success(
                    false,
                    [
                        'plugin' => false,
                        'controller' => 'Jobs',
                        'action' => 'execute',
                        $job->id,
                        '?' => ['database' => $job->config['database']]
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

       if (!$this->request->is('api')) {
            $this->controller->render('/Transfer/import');
       } else {
           $this->controller->Answer->error(__('You need to post data to this endpoint.'));
       }
    }

    /**
     * Transfer records from source to target database
     **
     * @param string $tablename @deprecated use $this->model->getTable()
     * @param string $scope The table scope
     * @param array $params Filter parameters
     *
     * @return \Cake\Http\Response|null|void
     * @throws BadRequestException if record not found
     */
    public function transfer($tablename = 'articles', $scope = null, $params = [])
    {
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
            'table' => $tablename,
            'scope' => $scope,

            'params' =>

                [
                    'snippets' => $snippets,
                    'published' => $published

                ]
                + Arrays::array_remove_keys($params, [
                    'scope', 'source', 'target', 'tablename','skip',
                    'page','columns', 'template', 'sort', 'save', 'load', 'stage','close','collapsed',
                    'tree', 'versions', 'dates', 'fulltext','files', 'comments','snippets','published'
                ])
            ,

            'skip' => Attributes::commaListToStringArray($params['skip'] ?? ''),
            'tree' => ($params['tree'] ?? '0') === '1',
            'versions' => ($params['versions'] ?? '0') === '1',
            //'dates' => ($params['dates'] ?? '0') === '1',
            //'fulltext' => ($params['fulltext'] ?? '0') === '1',
            'timestamps' => false,
            'files' => ($params['files'] ?? '0') === '1',
            'copy' => ($source === $target),

            'redirect' => Router::url(
                [
                    'controller' => $this->request->getParam('controller'),
                    'action' => 'index',
                    $scope
                ]
            )
        ];


        $this->Jobs = FactoryLocator::get('Table')->get('Jobs');
        $jobdata = ['typ' => 'transfer', 'config' => $jobConfig];
        $job = $this->Jobs->newEntity($jobdata)->typedJob;

        // Preview
        if ($stage === 'preview') {
            $page = $this->request->getQuery('page', 1);
            $preview = $job->preview(['page' => $page]);
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
     * Manipulate records identified by query parameters
     *
     * @param string $tablename @deprecated use $this->model->getTable()
     * @param string $scope The table scope
     *
     * @return \Cake\Http\Response|null|void
     * @throws BadRequestException if record not found
     */
    public function mutate($tablename = 'articles', $scope = null)
    {
        // Prepare job
        $database = BaseTable::getDatabaseName();
        $params = $this->request->getQueryParams();

        if (!empty($scope)) {
            $params['scope'] = $scope;
        }

        $jobdata = [
            'typ' => 'mutate',
            'config' => [
                'database' => $database,
                'table' => $tablename,
                'scope' => $scope,
                'params' => $params, //TODO: restrict to dataParams
                'task' => $params['task'] ?? '',
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

            // TODO: validate, config.task should never be empty
            if ($this->Jobs->save($job)) {
                $this->controller->Answer->success(
                    false,
                    [
                        'plugin' => false,
                        'controller' => 'Jobs',
                        'action' => 'execute',
                        $job->id,
                        '?' => ['database' => $job->config['database'], 'close'=>0]
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

        // Configure job
        $this->controller->set(compact('job'));
        $this->controller->render('/Transfer/mutate');
    }
}
