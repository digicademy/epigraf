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

use App\Model\Table\BaseTable;
use App\Utilities\Converters\Attributes;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
//use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use Predis\Client;
use Predis\Connection\ConnectionException;

/**
 * Worker for processing delayed jobs
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
 * supervisord ini file (not tested):
 * [program:epigraf_job_worker]
 * command=bin/cake jobs process
 * autostart=true
 * autorestart=true
 * stderr_logfile=/var/log/worker.err.log
 * stdout_logfile=/var/log/worker.out.log
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
        $parser->setDescription('Starts a worker that processes jobs.');

        $parser->addArgument('action', [
            'help' => [
                "process: Start processing jobs from the queue managed by Redis.\n" .
                "execute: Execute a specific job by its ID.\n"
            ],
            'choices' => ['process','execute'],
            'required' => true
        ]);

        $parser->addOption('id', [
            'help' => 'Job ID for the execute action.',
        ]);

        $parser->addOption('stepwise', [
            'help' => 'Set to 1 to execute just one step in the execute action or to 0 to process all steps. Defaults to 0.',
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

        if ($action == 'process') {
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
            $statusName = Configure::read('Jobs.status_name');

            while (true) {
                $io->out("Waiting for jobs...");


                while ($retryNo < $retryMax) {
                    try {
                        list(, $delayedJob) = $redis->blpop($queueName, 0);

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

                $jobData = json_decode($delayedJob, true);
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

        elseif ($action == 'execute') {
            $jobId = $args->getOption('id');
            $stepwise = Attributes::isTrue($args->getOption('stepwise') ?? 0);

            try {
                $io->out("Executing job: {$jobId}");
                $this->processJob($jobId, $stepwise);
                $io->out("Finished job: {$jobId}");
            } catch (\Exception $e) {
                $io->error("Error executing job: {$jobId} - " . $e->getMessage());
            }
        }
    }

    /**
     * Process a job by its ID
     *
     * @param integer $jobId
     * @param boolean $stepwise Set to true to process only one step of the job
     * @return void
     */
    protected function processJob($jobId, $stepwise = false) {

        // Setup route builder
//        $this->routeBuilder = Router::createRouteBuilder('/');
//        Router::defaultRouteClass(DashedRoute::class);
//        $this->routeBuilder->connect(
//            '/jobs/execute/*',
//            ['controller' => 'Jobs', 'action' => 'execute']
//        );

        $job = $this->Jobs->get($jobId);

        BaseTable::$userRole = $job->config['user_role'] ?? 'guest';
        BaseTable::$userId = $job->config['user_id'] ?? null;
        $this->Jobs::$userRole = BaseTable::$userRole;
        $this->Jobs::$userId = BaseTable::$userId;

        $baseUrl = rtrim($job->config['server'] ?? Configure::read('App.fullBaseUrl') ?? '', '/');
        if (!empty($baseUrl)) {
            Router::fullBaseUrl($baseUrl);
        }


        // TODO: Implement 'cancel' status in addition to redis cancel flag
        // TODO: Implement timeout handling
        while (!in_array($job->status, ['finish', 'error']) && (!$job->isCanceled)) {
            $job = $job->execute();
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

