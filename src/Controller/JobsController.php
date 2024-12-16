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

use App\Model\Entity\Job;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;


/**
 * Export Controller
 *
 * Manages export of databases by adding
 * export jobs to database and executing
 * these jobs in a polling process.
 *
 * @property \App\Model\Table\JobsTable $Jobs
 */
class JobsController extends AppController
{

    /**
     * Access permissions
     *
     * We can grant all roles API execute permissions because only jobs that
     * were created by a user can be executed. Thus, users always need the
     * extra permission to add jobs (e.g. for importing data via the API)
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'api' => [
            'reader' => ['execute'],
            'coder' => ['execute'],
            'desktop' => ['execute'],
            'author' => ['execute'],
            'editor' => ['execute'],
            'admin' => ['execute'],
            'devel' => ['execute']
        ],
        'web' => [
            'reader' => ['add', 'execute', 'download'],
            'coder' => ['add', 'execute', 'download'],
            'desktop' => ['add', 'execute', 'download'],
            'author' => ['add', 'execute', 'download'],
            'editor' => ['add', 'execute', 'download']
        ]
    ];

    /**
     * Pagination setup
     *
     * @var array
     */
    public $paginate = [
        'className' => 'Total',
        'order' => ['Jobs.id' => 'desc'],
        'limit' => 100
    ];

    /**
     * Retrieve a list of jobs
     *
     * @return void
     */
    public function index()
    {
        $this->Actions->index();
    }

    /**
     * Show a job
     *
     * @param string|null $id job id
     *
     * @return \Cake\Http\Response|null|void
     * @throws RecordNotFoundException If record not found
     */
    public function view($id = null)
    {
        $job = $this->Jobs->get($id, [
            'contain' => []
        ]);

        $this->set('job', $job);
    }

    /**
     * Add a new export job, based on query params and user defaults
     *
     * TODO: move to TransferComponent (and rename it to JobComponent and implement export action in ArticlesController)
     *
     * @return \Cake\Http\Response|void redirects on successful job creation, renders view otherwise
     * @throws \Cake\Http\Exception\NotFoundException if valid database or pipeline is provided in the request
     */
    public function add()
    {
        // Get pipeline parameters
        $params = $this->request->getQueryParams();
        $params = $this->Jobs->parseRequestParameters($params, null, 'add');

        // Get configured pipelines
        $pipelinesTable = $this->fetchTable('Pipelines');
        $pipelines = $pipelinesTable->find('forArticles', $params)->order(['name' => 'asc'])->toArray();
        if (empty($params['pipeline']) && !empty($pipelines)) {
            $params['pipeline'] = array_keys($pipelines)[0];
        }

        // Get all pipelines
        if (in_array($this->userRole, ['admin', 'devel'])) {
            $pipelines = $pipelinesTable->find('list')->order(['name' => 'asc'])->toArray();
        }

        //Create job
        /** @var Job $job */
        $job = $this->Jobs->newEntity(['typ' => 'export'])->typedJob;
        $job = $job->patchExportOptions($params);

        // Load pipeline
        if ($job->config['pipeline_id']) {
            $pipeline = $pipelinesTable->get($job->config['pipeline_id']);
        }
        else {
            $pipeline = null;
        }

        //Patch default values for pipeline
        $job = $job->patchOptions($pipeline, $this->request->getParsedBody());

        //Save
        if ($this->request->is('post')) {

            if (empty($job->config['database'])) {
                throw new NotFoundException('No valid database selected.');
            }

            if (!$pipeline) {
                throw new NotFoundException('No valid pipeline selected.');
            }

            if ($this->Jobs->save($job)) {
                return $this->redirect([
                    'plugin' => false,
                    'controller' => 'Jobs',
                    'action' => 'execute',
                    $job->id,
                    '?' => ['database' => $job->config['database'], 'close' => false]
                ]);
            }
            else {
                $this->Flash->error(__('The job could not be created. Please, try again.'));
            }
        }

        $this->set(compact(['job', 'pipelines']));
    }

    /**
     * Download method
     *
     * Immediately executes export job, based on query params and user defaults.
     * Provide the following query parameters:
     * - pipeline_id (default value: user setting)
     * - database (default value: user setting)
     * - project_id
     * - articles_ids
     *
     * @return \Cake\Http\Response|void redirects on successful job execution, renders view otherwise
     * @throws \Cake\Http\Exception\NotFoundException if no valid database or pipeline is provided in the request
     */
    public function download()
    {
        // Get pipeline parameters
        $params = $this->request->getQueryParams();
        $params = $this->Jobs->parseRequestParameters($params, null, 'download');

        //Create job
        $job = $this->Jobs->newEntity(['typ' => 'export']);
        $job->patchExportOptions($params);

        //Check if user has database access
        if (!$this->isAllowedDatabase($job->config['database'] ?? null)) {
            throw new ForbiddenException('You have no access to the selected database');
        }

        if (empty($job->config['pipeline_id'])) {
            throw new NotFoundException('No valid pipeline selected. Please check your user profile.');
        }

        //Patch default values for pipeline
        $pipelinesTable = $this->fetchTable('Pipelines');
        $pipeline = $pipelinesTable->get($job->config['pipeline_id']);
        $job->patchOptions($pipeline, $this->request->getParsedBody());

        //Save and start
        if ($this->Jobs->save($job)) {
            return $this->redirect([
                'plugin' => false,
                'controller' => 'Jobs',
                'action' => 'execute',
                $job->id,
                '?' => ['timeout' => '3', 'database' => $job->config['database'], 'close' => false]
            ]);
        }
        else {
            $this->Flash->error(__('The export job could not be created. Please, try again.'));
            return $this->redirect([
                'plugin' => false,
                'controller' => 'Jobs',
                'action' => 'add',
                '?' => ['database' => $job->config['database'] ?? '']
            ]);
        }
    }

    /**
     * Execute the job step by step in a polling process
     *
     * The endpoint delivers three types of results:
     *
     * a) Render view which will be managed by the Javascript JobWidget in the frontend
     * b) JSON data for polling from the JobWidget or using epigraf package in R
     * c) Resulting file for download, if the job is finished and the endpoint is not called via AJAX
     *
     * @param string|null $job_id Job id
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Http\Exception\ForbiddenException if user is not allowed to export database
     * @throws \Cake\Http\Exception\NotFoundException if job record not found
     */
    public function execute($job_id = null)
    {
        if (empty($job_id)) {
            throw new NotFoundException('Could not find that job');
        }

        $job = $this->Jobs->get($job_id);

        //Check if user has database access
        if (!$this->isAllowedDatabase($job->config['database'] ?? null)) {
            throw new ForbiddenException('You have no access to the selected database');
        }

        //Check if the current user created the job
        if (($job->config['user_id'] ?? '') !== $this->Jobs::$userId) {
            throw new ForbiddenException('You have no access to the selected job');
        }

        //Process job (timelimit in seconds for one request
        $timeout = (int)$this->request->getQuery('timeout', 1);
        $timeout = min([$timeout, 3]);

        if (($job->status === 'init') && ($this->request->is(['post', 'patch', 'put']))) {
            $timeout = 0;
        }

        $job = $job->execute($timeout);

        //Save Job
        if (!$this->Jobs->save($job)) {
            $job->error = __('The job could not be saved. Please try again.');
        }

        //Send download
        if ($job->status === 'download') {
            if (!$this->request->is('ajax')) {
                $download = $this->request->getQuery('download');
                $download = ($download == null) || !empty($download);
                $this->response = $this->response->withFile(
                    $job->getCurrentOutputFile(),
                    ['download' => $download]);

                return $this->response;
            }
        }

        elseif ($job->status === 'finish') {
            $this->Answer->success(
                __('The job has been finished.'),
                $this->request->is('ajax') ? null : $job->redirect,
                $job->toArray()
            );
        }

        $this->Answer->addAnswer(compact('job'));
    }

}
