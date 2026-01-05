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
use App\Utilities\Converters\Attributes;
use Cake\Core\Configure;
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
            'coder' => ['execute', 'cancel'],
            'desktop' => ['execute', 'cancel'],
            'author' => ['execute', 'cancel'],
            'editor' => ['execute', 'cancel'],
            'admin' => ['execute', 'cancel'],
            'devel' => ['execute', 'cancel']
        ],
        'web' => [
            'coder' => ['add', 'execute', 'cancel', 'download'],
            'desktop' => ['add', 'execute', 'cancel', 'download'],
            'author' => ['add', 'execute', 'cancel', 'download'],
            'editor' => ['add', 'execute', 'cancel', 'download']
        ]
    ];


    public $help = 'export/pipelines';

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
     * @param string|null $id Job ID
     *
     * @return \Cake\Http\Response|null|void
     * @throws RecordNotFoundException If record not found
     */
    public function view($id = null)
    {
        $this->Actions->view($id);
    }

    /**
     * Edit a job
     *
     * @param string|null $id Job ID
     *
     * @return \Cake\Http\Response|null|void
     * @throws RecordNotFoundException If record not found
     */
    public function edit($id = null)
    {
        $this->Actions->edit($id);
    }

    /**
     * Delete a job
     *
     * @param string $id Job ID
     * @return void
     */
    public function delete($id)
    {
        $this->Actions->delete($id);
    }


    /**
     * Add a new export job, based on query params and user defaults
     *
     * @deprecated Use TransferComponent (and rename it to JobComponent)
     *
     * @return \Cake\Http\Response|void redirects on successful job creation, renders view otherwise
     * @throws \Cake\Http\Exception\NotFoundException if valid database or pipeline is provided in the request
     */
    public function add()
    {
        // Get pipeline parameters
        $params = $this->request->getQueryParams();
        $params = $this->Jobs->parseRequestParameters($params, null, 'add');

        $pipelinesTable = $this->fetchTable('Pipelines');

        // Get all pipelines
        if (in_array($this->userRole, ['admin', 'devel'])) {
            $pipelines = $pipelinesTable->find('list')->order(['name' => 'asc'])->toArray();
        }
        // Get configured pipelines
        else {
            $pipelines = $pipelinesTable->find('forArticles', $params)->order(['name' => 'asc'])->toArray();
        }

        if (empty($params['pipeline']) && !empty($pipelines)) {
            $params['pipeline'] = array_keys($pipelines)[0];
        }

        // Delayed jobs will be processed by a worker
        $delayedJob = !empty(Configure::read('Jobs.delay', false));

        //Create job
        /** @var Job $job */
        $job = $this->Jobs->newEntity(['jobtype' => 'export', 'delay' => $delayedJob ? 1 : 0])->typedJob;
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
                $this->Answer->error(__('The job could not be created. Please, try again.'));
            }
        }

        $this->set(compact(['job', 'pipelines']));
    }

    /**
     * Download method
     *
     * @deprecated Use TransferComponent (and rename it to JobComponent)
     *
     * Immediately executes export job, based on query params and user defaults.
     * Provide the following query parameters:
     * - pipeline_id (default value: user setting)
     * - database (default value: user setting)
     * - project_id
     * - articles_ids
     *
     * @return \Cake\Http\Response|void Redirects on successful job execution, renders view otherwise.
     * @throws \Cake\Http\Exception\NotFoundException If no valid database or pipeline is provided in the request.
     */
    public function download()
    {
        // Get pipeline parameters
        $params = $this->request->getQueryParams();
        $params = $this->Jobs->parseRequestParameters($params, null, 'download');

        // Delayed jobs will be processed by a worker
        $delayedJob = !empty(Configure::read('Jobs.delay', false));

        //Create job
        $job = $this->Jobs->newEntity(['jobtype' => 'export', 'delay' => $delayedJob ? 1 : 0])->typedJob;
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
            $this->Answer->error(
                __('The export job could not be created. Please, try again.'),
                [
                    'plugin' => false,
                    'controller' => 'Jobs',
                    'action' => 'add',
                    '?' => ['database' => $job->config['database'] ?? '']
                ]
            );
        }
    }

    /**
     * Execute the job step by step in a polling process
     *
     * The endpoint delivers three types of results:
     *
     * a) Rendered view which will be managed by the Javascript JobWidget in the frontend
     * b) JSON data for polling from the JobWidget or using the Epigraf package in R
     * c) Resulting file for download, if the job is finished and the endpoint is not called via AJAX
     *    In case a job provides several downloads, set the 'download' query parameter to the file name
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

        $job = $this->Jobs->get($job_id)->typedJob;

        //Check if user has database access
        if (!$this->isAllowedDatabase($job->config['database'] ?? null)) {
            throw new ForbiddenException('You have no access to the selected database');
        }

        //Check if the current user created the job
        if (($job->config['user_id'] ?? '') !== $this->Jobs::$userId) {
            throw new ForbiddenException('You have no access to the selected job');
        }

        // Reset
        if (Attributes::isTrue($this->request->getQuery('reset', 0))) {
            $job->reset();
        }

        //Process job
        if ($job->status !== 'finish') {

            // Excecute non-delayed jobs
            if (empty($job['delay'])) {

                // Set time limit in seconds for one request
                if (($job->status === 'init') && ($this->request->is(['post', 'patch', 'put']))) {
                    $timeout = 0;
                } else {
                    $timeout = (int)$this->request->getQuery('timeout', 1);
                    $timeout = min([$timeout, 3]);
                }
                $job = $job->execute($timeout);

                //Save Job
                if (!$this->Jobs->save($job)) {
                    $job->error = __('The job could not be saved. Please try again.');
                }
            } elseif ($job->queueStatus === 'failed') {
                $job->error = __('The job worker failed.');
            }
        }

        if ($job->status === 'finish') {

            // Send download file from the results...
            $download = $this->request->getQuery('download');
            $forceDownload = $this->request->getQuery('force');
            if (!$this->request->is('ajax') && !empty($download)) {
                $this->response = $this->response
                    ->withFile(
                        $job->getJobOutputFilePath($download),
                        ['download' => !empty($forceDownload)]
                    );
                return $this->response;
            }

            //...or send redirect URL
            $this->Answer->success(
                __('The job has been finished.'),
                $this->request->is('ajax') ? null : $job->responseUrl
            );
        }

        $this->Answer->addAnswer(compact('job'));
    }

    /**
     * Cancel a running job
     *
     * @param int $jobId
     * @return \Cake\Http\Response|void
     */
    public function cancel($jobId)
    {
        if (!$this->request->is('delete')) {
            throw new NotFoundException('To cancel a job, please issue a DELETE request.');
        }

        if (empty($jobId)) {
            throw new NotFoundException('Could not find that job');
        }

        $job = $this->Jobs->get($jobId);

        //Check if user has database access
        if (!$this->isAllowedDatabase($job->config['database'] ?? null)) {
            throw new ForbiddenException('You have no access to the selected database');
        }

        //Check if the current user created the job
        if (!$this->userHasRole(['admin', 'devel'])  && (($job->config['user_id'] ?? '') !== $this->Jobs::$userId)) {
            throw new ForbiddenException('You have no access to the selected job');
        }

        $job = $job->cancel();
//        if (!$this->Jobs->save($job)) {
//            $job->error = __('The job could not be canceled. Please try again.');
//        }

        $this->Answer->addAnswer(compact('job'));
    }

}
