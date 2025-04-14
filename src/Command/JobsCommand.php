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
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
//use Cake\Routing\Route\DashedRoute;
use Cake\Routing\Router;
use Predis\Client;

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
            'help' => 'Action.',
            'choices' => ['process'],
            'required' => true
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

                list(, $delayedJob) = $redis->blpop($queueName, 0);
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
    }

    protected function processJob($jobId) {

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

        Router::fullBaseUrl(rtrim($job->config['server'] ?? Configure::read('App.fullBaseUrl'), '/'));

        while (!in_array($job->status, ['finish', 'error']) && (!$job->isCanceled)) {
            $job = $job->execute();
            if (!$this->Jobs->save($job)) {
                $job->error = __('The job could not be saved. Please try again.');
                break;
            }
        }
    }

}

