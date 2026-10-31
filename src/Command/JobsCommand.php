<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Command;

use App\Model\Entity\Job;
use App\Model\Table\BaseTable;
use App\Utilities\Converters\Attributes;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\Routing\Router;
use Predis\Client;
use Predis\Connection\ConnectionException;

/**
 * Commands to process jobs.
 *
 * Used for processing queued jobs with workers,
 * scheduled jobs, and executing single jobs by ID.
 *
 * When using Epigraf from the CLI,
 * set the following environment variables:
 * - APP_FULL_BASE_URL (e.g. https://epigraf.inschriften.net or http://localhost)
 * - JOBS_REDIS_HOST (e.g. redis)
 * - JOBS_DELAY true
 *
 * Alternatively, update the respective configuration in config/app.php.
 *
 * Make sure to disable timeouts of the Redis server:
 * - Set timeout = 0 in redis.conf
 *
 * # Single job
 *
 * Process a single job by its ID:
 * bin/cake jobs execute --id=JOB_ID
 *
 * # Add a job
 *
 * Example:
 * ```
 * bin/cake jobs add --type import --config='{"pipeline_tasks":[["type":"import","inputpath":"/var/www/data/databases/epi_movies/import/xml"], "database":"epi_movies", "table":"articles"}'
 *```
 *
 * # Queued jobs (=background jobs)
 *
 * Start a worker that processes jobs from the queue:
 * bin/cake jobs process
 *
 * supervisord ini file (not tested):
 * [program:epigraf_job_worker]
 * command=bin/cake jobs process
 * autostart=true
 * autorestart=true
 * stderr_logfile=/var/log/worker.err.log
 * stdout_logfile=/var/log/worker.out.log
 *
 * # Scheduled jobs
 *
 * Process the next due job and update its next run time:
 *
 * bin/cake jobs trigger
 *
 * Better use cron (and lock it with flock) that checks back every minute.
 *
 */
class JobsCommand extends Command
{

    /**
     * IO variable
     *
     * @var null
     */
    public $io = null;

    /**
     *  Models
     */
    public $Jobs = null;

    /**
     * Initialization hook method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Jobs = $this->fetchTable('Jobs');
    }

    /**
     * Build option parser
     *
     * @param ConsoleOptionParser $parser
     *
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription('Processes jobs (worker, scheduled, single) or adds a new job.');

        $parser->addArgument('action', [
            'help' => [
                "process: Start processing jobs from the queue.\n" .
                "trigger: Start processing scheduled jobs.\n" .
                "execute: Execute a specific job by its ID.\n" .
                "add: Add a new job.\n"
            ],
            'choices' => ['process','trigger','execute','add'],
            'required' => true
        ]);

        $parser->addOption('queue', [
            'help' => 'Queue name for the execute action. Leave empty to use the default queue.',
        ]);

        $parser->addOption('id', [
            'help' => 'Job ID for the execute action.',
        ]);

        $parser->addOption('reset', [
            'help' => 'Whether to reset the job before processing the execute action.',
        ]);

        $parser->addOption('stepwise', [
            'help' => 'Set to 1 to execute just one step in the execute action or to 0 to process all steps. Defaults to 0.',
        ]);


        $parser->addOption('type', [
            'help' => 'Job type (import, export, mutate, transfer). Defaults to import.',
        ]);
        $parser->addOption('config', [
            'help' => 'The job config (JSON object).',
        ]);
        $parser->addOption('name', [
            'help' => 'Job name.',
        ]);
        $parser->addOption('role', [
            'help' => 'User role stored in the job config, defaults to admin.',
        ]);
        $parser->addOption('queue', [
            'help' => 'Queue the job for a worker. Defaults to false.',
            'boolean' => true
        ]);
        $parser->addOption('execute', [
            'help' => 'Whether to immedeately execute the added job. Defaults to false.',
            'boolean' => true
        ]);

        return $parser;
    }

    /**
     * Initialize action-depending data and directories
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     *
     * @return int|void|null
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->io = $io;
        $action = $args->getArgument('action');

        // Worker queue
        if ($action == 'process') {
            $this->actionProcess($args, $io);
        }
        // Scheduled jobs
        elseif ($action == 'trigger') {
            $this->actionTrigger($args, $io);
        }
        elseif ($action == 'execute') {
           $this->actionExecute($args, $io);
        }
        elseif ($action == 'add') {
           $this->actionAdd($args, $io);
        }
    }

    /**
     * Process jobs in the worker queues
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return void
     */
    protected function actionProcess(Arguments $args, ConsoleIo $io)
    {
        $retryNo = 0;
        $retryMax = Configure::read('Jobs.retries_max', 10);
        $retryDelay = Configure::read('Jobs.retries_delay', 1000000); // 1sec in microseconds

        $redis = new Client([
            'scheme' => Configure::read('Jobs.scheme', 'tcp'),
            'host'   => Configure::read('Jobs.host', 'localhost'),
            'port'   => Configure::read('Jobs.port', 6379),
            'read_write_timeout' => -1
        ]);

        $queueName = Configure::read('Jobs.queue_name');
        $queue = $args->getArgument('queue');
        if (!empty($queue)) {
            $queueName .= '_' . $queue;
        }
        $statusName = Configure::read('Jobs.status_name');

        while (true) {
            $io->out("Waiting for jobs...");

            while ($retryNo < $retryMax) {
                try {
                    list(, $queuedJob) = $redis->blpop($queueName, 0);

                    // Reset retry count on successful pop
                    $retryNo = 0;
                    break;
                } catch (ConnectionException $e) {
                    $io->error("Redis connection error: " . $e->getMessage());

                    $retryNo++;
                    if ($retryNo >= $retryMax) {
                        $io->error("Max retries reached. Exiting.");
                        return;
                    }

                    $io->error("Retrying in " . ($retryDelay / 1000000) . " seconds.");
                    usleep($retryDelay);
                }
            }

            $jobData = json_decode($queuedJob, true);
            $jobId = $jobData['job_id'] ?? null;

            // Track job status
            $redis->hset($statusName, $jobId, "pending");

            try {
                // Process the job
                $io->out("Processing job: {$jobId}");
                $this->processJob($jobId);

                // Remove from status tracker on success
                $redis->hdel($statusName, $jobId);
                $io->out("Finished job: {$jobId}");
            } catch (\Exception $e) {
                $io->error("Error processing job: {$jobId} - " . $e->getMessage());
                $redis->hset($statusName, $jobId, "failed");

                // Requeue job on failure
                //$redis->rpush($queueName, $jobData);
            }
        }
    }

    /**
     * Process scheduled jobs
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return void
     */
    protected function actionTrigger(Arguments $args, ConsoleIo $io)
    {
        $job = $this->Jobs
            ->find('due')
            ->limit(1)
            ->first();

        if (!empty($job)) {

            try {

                $nowTime = FrozenTime::now()->format('Y-m-d H:i:s');
                $scheduledTime = $job->nextrun->format('Y-m-d H:i:s');

                $io->out("{$nowTime} Executing scheduled job {$job->id} scheduled for {$scheduledTime}.");
                $job->updateNextRun();
                $this->Jobs->saveOrFail($job);

                $this->processJob($job->id, false, true);

                $nowTime = FrozenTime::now()->format('Y-m-d H:i:s');
                $scheduledTime = empty($job->nextrun) ? 'undefined' : $job->nextrun->format('Y-m-d H:i:s');
                $io->out("{$nowTime} Finished scheduled job {$job->id}. Next run at {$scheduledTime}.");

            } catch (\Exception $e) {
                $io->error("Error executing scheduled job {$job->id} - " . $e->getMessage());
            }
        }
    }

    protected function actionExecute(Arguments $args, ConsoleIo $io)
    {
        $jobId = $args->getOption('id');
        $stepwise = Attributes::isTrue($args->getOption('stepwise') ?? 0);
        $reset = $args->hasOption('reset');

        try {
            $io->out("Executing job: {$jobId}");
            $this->processJob($jobId, $stepwise, $reset, $io);
            $io->out("Finished job: {$jobId}");
        } catch (\Exception $e) {
            $io->abort("Error executing job: {$jobId} - " . $e->getMessage());
        }
    }

    /**
     * Add a new job
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return void
     */
    protected function actionAdd(Arguments $args, ConsoleIo $io)
    {
        // Job type must be a known job class
        $jobType = $args->getOption('type');
        if (!isset($this->Jobs::$jobTypes[$jobType])) {
            $io->error('Invalid job type "' . $jobType . '". Allowed: ' . implode(', ', array_keys($this->Jobs::$jobTypes)));
            return;
        }

        // Job config (JSON)
        $jobConfig = $args->getOption('config');
        if (!empty($jobConfig)) {
            $jobConfig = json_decode($jobConfig, true);
            if (!is_array($jobConfig)) {
                $io->error('Invalid JSON payload.');
                return;
            }
        } else {
            $jobConfig = [];
        }

        $jobName = $args->getOption('name');
        $queuedJob = $args->getOption('delay') ?? false;

        // Set user context so beforeSave fills the user_role/user_id config
        $role = $args->getOption('role') ?? 'admin';
        BaseTable::$userRole = $role;
        BaseTable::$userId = null;
        $this->Jobs::$userRole = $role;
        $this->Jobs::$userId = null;

        $jobdata = [
            'name' => $jobName,
            'jobtype' => $jobType,
            'delay' => $queuedJob ? 1 : 0,
            'config' => $jobConfig
        ];

        $job = $this->Jobs->newEntity($jobdata)->typedJob;

        if ($this->Jobs->save($job)) {
            $io->out('Created job ' . $job->id);

            $doExecute = $args->getOption('execute') ?? false;
            if ($doExecute) {
                try {
                    $io->out("Executing job: {$job->id}");
                    $this->processJob($job->id);
                    $io->out("Finished job: {$job->id}");
                } catch (\Exception $e) {
                    $io->abort("Error executing job: {$job->id} - " . $e->getMessage());
                }
            } else {
                $io->out('Execute with: bin/cake jobs execute --id=' . $job->id);
            }
        } else {
            $io->abort('The job could not be created.');
        }
    }

    /**
     * Process a job by its ID
     *
     * @param integer $jobId
     * @param boolean $stepwise Set to true to process only one step of the job.
     *                          Otherwise the job will be processed until finished or canceled.
     * @param boolean $reset Whether to reset the job before execution
     * @return void
     */
    protected function processJob($jobId, $stepwise = false, $reset = false, $io = false) {

        // TODO: Lock jobs

        /** @var Job $job */
        $job = $this->Jobs->get($jobId)->typedJob;

        if ($reset) {
            $job->reset();
        }

        BaseTable::$userRole = $job->config['user_role'] ?? 'guest';
        BaseTable::$userId = $job->config['user_id'] ?? null;
        $this->Jobs::$userRole = BaseTable::$userRole;
        $this->Jobs::$userId = BaseTable::$userId;

        $baseUrl = rtrim($job->config['server'] ?? Configure::read('App.fullBaseUrl') ?? '', '/');
        if (!empty($baseUrl)) {
            Router::fullBaseUrl($baseUrl);
        }

        // TODO: Implement timeout handling
        while (!$job->isFinished && !$job->isCanceled) {
            $job = $job->execute();

            if (!empty($io)) {
                $current = $job['progress'] ?? 'NA';
                $max = $job['progressmax'] ?? 'NA';
                $io->out("Progress {$current} / {$max}, state {$job['status']}");
            }

            if (!$this->Jobs->save($job)) {
                $job->error = __('The job could not be saved. Please try again.');
                break;
            }
            if ($stepwise) {
                break;
            }
        }
    }

}

