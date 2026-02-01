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

use App\Utilities\Converters\Attributes;
use App\Utilities\Exceptions\DeprecatedException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Authorization\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\RedirectException;


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
     * Add a new articles export job
     *
     * @deprecated Always use the articles controller
     *
     * Provide the following query parameters:
     *
     * - pipeline_id (default value: user setting)
     * - database (default value: user setting)
     * - project_id
     * - articles_ids
     *
     * @return void
     * @throws RedirectException Redirects to the articles controller
    */
    public function add()
    {
        $params = $this->request->getQueryParams();
        unset($params['database']);

        $this->Answer->redirect([
            'plugin' => 'Epi',
            'database' => $this->request->getQuery('database'),
            'controller' => 'articles',
            'action' => 'export',
            '?' => $params
        ]);
    }

    /**
     * Download method
     *
     * @deprecated Always use the articles controller. Can already be removed, blackout test with bad request exception.
     *
     * Immediately executes export job, based on query params and user defaults.
     *
     * Provide the following query parameters:
     *
     * - pipeline_id (default value: user setting)
     * - database (default value: user setting)
     * - project_id
     * - articles_ids
     *
     * @return void
     * @throws RedirectException Redirects to the articles controller
     */
    public function download()
    {
        throw new DeprecatedException('Deprecated. Always use the articles controller.');

        $params = $this->request->getQueryParams();
        $params = $this->Jobs->parseRequestParameters($params, null, 'download');

        $this->Answer->redirect([
            'plugin' => 'Epi',
            'database' => $this->request->getParam('database'),
            'controller' => 'articles',
            'action' => 'export',
            3,
            '?' => $params
        ]);

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
     * @throws \Authorization\Exception\ForbiddenException if user is not allowed to export database
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
            throw new ForbiddenException(null, __('You have no access to the selected database'));
        }

        //Check if the current user created the job
        if (($job->config['user_id'] ?? '') !== $this->Jobs::$userId) {
            throw new ForbiddenException(null, __('You have no access to the selected job'));
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
            throw new ForbiddenException(null, __('You have no access to the selected database'));
        }

        //Check if the current user created the job
        if (!$this->userHasRole(['admin', 'devel'])  && (($job->config['user_id'] ?? '') !== $this->Jobs::$userId)) {
            throw new ForbiddenException(null, __('You have no access to the selected job'));
        }

        $job = $job->cancel();
//        if (!$this->Jobs->save($job)) {
//            $job->error = __('The job could not be canceled. Please try again.');
//        }

        $this->Answer->addAnswer(compact('job'));
    }

}
