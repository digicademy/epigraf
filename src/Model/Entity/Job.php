<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity;

use App\Model\Interfaces\MutateTableInterface;
use App\Model\Table\BaseTable;
use App\Model\Table\JobsTable;
use App\Utilities\Files\Files;
use App\Cache\Cache;
use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\Error\ErrorLogger;
use Cake\Http\Exception\InternalErrorException;
use Cake\Log\Engine\FileLog;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Datasource\Exception\RecordNotFoundException;
use Throwable;
use Predis\Client;

/**
 * Job Entity
 *
 * # Database fields (without inherited fields)
 * @property string $typ
 * @property int $delay If this is a delayed job, a number greater than 0.
 * @property string $status
 * @property array $config
 * @property int $progress
 * @property int $progressmax
 *
 * # Virtual fields (without inherited fields)
 * @property mixed $typedJob
 * @property mixed $typedTask
 * @property string $exceptionMessage
 * @property string|null $entityClass
 * @property string $indexKey
 * @property string|null $queueStatus
 * @property bool $isCanceled
 * @property string $progressLabel Progress indicator in the form "2/10".
 *
 * @property null|string $redirect
 * @property array $redirectParams
 * @property string $redirectUrl
 * @property string $downloadUrl
 * @property string $cancelUrl
 *
 * @property string $jobPath
 * @property string $databasePath
 * @property string $sharedPath
 *
 * @property array $selectionOptions
 * @property array $dataParams
 *
 * # Relations
 * @property JobsTable $table
 */
class Job extends BaseEntity
{

    /**
     * Set initial limit and timeout
     *
     * @var int $limit
     * @var int $timeout
     */
    public int $limit = 30;
    public int $timeout = 1;

    /**
     * Current database
     *
     * @var null|Databank
     */
    public $databank = null;

    /**
     * Current job
     *
     * Holds the typed job entity that is created in $this->work / execute / preview
     *
     * @var null
     */
    protected $_typedJob = null;

    /**
     * Current job classes
     *
     * Map job type to entity
     *
     * @var array
     */
    public $jobClasses = [];

    /**
     * Current task classes
     *
     * Map job type to entity
     *
     * @var array
     */
    public $taskClasses = [];

    /**
     * Catches task errors
     *
     * @var array
     */
    protected $_taskErrors = [];

    /**
     * Whether to convert warnings to errors
     *
     * @var bool
     */
    protected $catchWarnings = false;


    /**
     * Cache (used in JobImport to link imported rows)
     *
     * @var array
     */
    protected $_index = [];

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * Expose virtual fields
     *
     * @var string[]
     */
    protected $_virtual = ['nexturl', 'redirect'];

    /**
     * Constructor
     *
     * @param array $content
     * @param array $options
     */
    public function __construct(array $content = [], array $options = [])
    {

        if (!isset($content['status'])) {
            $content['status'] = 'init';
        }

        $this->_initClasses();
        $this->setSource('Jobs');
        parent::__construct($content, $options);
    }

    /**
     * Get the Redis client to manage the queue
     *
     * @return Client
     */
    protected function getRedisClient() {
        return new Client([
            'scheme' => Configure::read('Jobs.scheme', 'tcp'),
            'host'   => Configure::read('Jobs.host', 'localhost'),
            'port'   => Configure::read('Jobs.port', 6379),
            'read_write_timeout' => -1
        ]);
    }

    /**
     * Add this job to the queue
     *
     * @return void
     */
    public function toQueue() {
        $data = [
            'job_id' => $this->id,
            'job_type' => $this->typ
        ];

        $queueName = Configure::read('Jobs.queue_name');
        $statusName = Configure::read('Jobs.status_name');

        $redis = $this->getRedisClient();
        $redis->hset($statusName, $this->id, "waiting");
        $redis->rpush($queueName, json_encode($data));
    }

    /**
     * Get the status from the queue
     *
     * @return string|null
     */
    protected function _getQueueStatus() {
        if (!Configure::read('Jobs.delay')) {
            return null;
        }

        $statusName = Configure::read('Jobs.status_name');
        return $this->getRedisClient()->hget($statusName, $this->id);
    }

    /**
     * Cancel the job
     *
     * @return Job
     */
    public function cancel(): Job {
        if (Configure::read('Jobs.delay')) {
            $redis = $this->getRedisClient();
            $statusName = Configure::read('Jobs.status_name');
            $redis->hset($statusName, $this->id, "canceled");
        }

        return $this;
    }


    /**
     * Check whether the job is canceled
     *
     * @return bool
     */
    protected function _getIsCanceled(): bool {
        if (!Configure::read('Jobs.delay')) {
            return false;
        }
        $statusName = Configure::read('Jobs.status_name');
        return $this->getRedisClient()->hget($statusName, $this->id) === 'canceled';
    }

    /**
     * Initialize classes
     *
     * @return void
     */
    protected function _initClasses()
    {
        // Create job classes list
        $job_classes = Files::getClassesInPath(
            APP . 'Model' . DS . 'Entity' . DS . 'Jobs' . DS,
            'App\Model\Entity\Jobs'
        );

        $job_names = array_map(function ($x) {
            $x = explode('\\', $x);
            $x = array_pop($x);
            $x = str_replace('Job', '', $x);
            return Inflector::underscore($x);
        }, $job_classes);

        $this->jobClasses = array_combine($job_names, $job_classes);

        // Create task classes list
        $task_classes = Files::getClassesInPath(
            APP . 'Model' . DS . 'Entity' . DS . 'Tasks' . DS,
            'App\Model\Entity\Tasks'
        );

        // TODO: filter out BaseTasks
        $task_names = array_map(function ($x) {
            $x = explode('\\', $x);
            $x = array_pop($x);
            $x = str_replace('Task', '', $x);
            return Inflector::underscore($x);
        }, $task_classes);

        $this->taskClasses = array_combine($task_names, $task_classes);
    }

    /**
     * Get typed job
     *
     * Factory method to create a job instance
     * //TODO: refactor using a separate factory class
     *
     * @return $this|mixed
     */
    protected function _getTypedJob()
    {
        if (get_class($this) === 'App\Model\Entity\Job') {
            $jobClass = $this->jobClasses[$this->typ ?? 'export'];

            if (empty($jobClass)) {
                throw new \Cake\Core\Exception\CakeException("Invalid job type.");
            }

            if (empty($this->_typedJob)) {
                $this->_typedJob = new $jobClass($this->toArray());
            }
            return $this->_typedJob;
        }
        else {
            return $this;
        }
    }

    /**
     * Get typed task
     *
     * Factory method to create a task instance
     * //TODO: refactor using a separate factory class
     *
     * @param $taskConfig
     * @return mixed
     */
    protected function _getTypedTask($taskConfig)
    {
        $taskClass = $this->taskClasses[$taskConfig['type'] ?? null] ?? null;

        if (empty($taskClass)) {
            throw new InvalidTaskException("Invalid task type.");
        }

        $task = new $taskClass($taskConfig, $this);
        return $task;
    }

    /**
     * Init global settings used in the job pipeline
     *
     * @return void
     */
    protected function _initGlobals()
    {
        // Set global variables for image import
        if (!empty($this->config['database'])) {
            Configure::write(
                'Data.currentdatabase',
                Configure::read('Data.databases') . Databank::addPrefix($this->config['database'])
            );
        }

        //Set alias for Images class and HistoricDates class that can be used in th XSL parser
        if (!class_exists('Images')) {
            class_alias('App\Utilities\Files\Images', 'Images');
        }

        if (!class_exists('HistoricDates')) {
            class_alias('App\Utilities\Converters\HistoricDates', 'HistoricDates');
        }
    }

    /**
     * Initialize logger
     *
     * @param boolean $catchWarnings Convert warnings to errors
     *
     * @return void
     */
    protected function _initLogger($catchWarnings = true)
    {
        $path = $this->jobPath;
        Log::drop('job');
        Log::setConfig('job', function () use ($path) {
            return new FileLog(['path' => $path, 'file' => 'job', 'scope' => 'jobs']);
        });

        //Convert all warnings/errors to exceptions
        $this->error = false;
        $this->catchWarnings = $catchWarnings;

        if ($catchWarnings) {
            error_reporting(-1);
            set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext = []) {
                // error was suppressed with the @-operator
                if (0 === error_reporting()) {
                    return false;
                }

                $message = $errstr;
                if ($errcontext['pipeline']['xslfile'] ?? false) {
                    $message .= " in file " . $errcontext['pipeline']['xslfile'];
                }

                throw new \Cake\Core\Exception\CakeException($message);
            });
        }

    }

    /**
     * Restore the error handler
     *
     * @return void
     */
    protected function _finishLogger()
    {
        if (!empty($job->error)) {
            $job->nexturl = false;
        }

        if ($this->catchWarnings) {
            restore_error_handler();
        }
    }

    /**
     * Generate exception message
     *
     * @param \Throwable $exception The exception to log a message for.
     * @param bool $isPrevious False for original exception, true for previous
     *
     * @return string Error message
     *
     * @see ErrorLogger::getMessage()
     */
    protected function _getExceptionMessage(Throwable $exception, bool $isPrevious = false): string
    {
        $message = sprintf(
            '%s[%s] %s in %s on line %s',
            $isPrevious ? "\nCaused by: " : '',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        if ($exception instanceof \Cake\Core\Exception\CakeException) {
            $attributes = $exception->getAttributes();
            if ($attributes) {
                $message .= "\nException Attributes: " . var_export($exception->getAttributes(), true);
            }
        }

        /** @var array $trace */
        $trace = Debugger::formatTrace($exception, ['format' => 'points']);
        $message .= "\nStack Trace:\n";
        foreach ($trace as $line) {
            if (is_string($line)) {
                $message .= '- ' . $line;
            }
            else {
                $message .= "- {$line['file']}:{$line['line']}\n";
            }
        }

        $previous = $exception->getPrevious();
        if ($previous) {
            $message .= $this->_getExceptionMessage($previous, true);
        }

        return $message;
    }

    /**
     * Add task error message to log file
     *
     * @param string $msg
     * @param array $context
     * @param \Exception $e
     *
     * @return void
     */
    public function addTaskError($msg, $context, $e = null)
    {
        $context['error'] =  !empty($e) ? $e->getMessage() : '';
        $context['scope'] = 'jobs';
        $this->_taskErrors[] = $msg;

        if (!empty($e)) {
            $msg .= "\n" . $this->_getExceptionMessage($e) . "\n\n";
        }

        Log::write('error', $msg, $context);
    }

    /**
     * Add multiple task errors at once
     *
     * @param array $errors
     * @return void
     */
    public function addTaskErrors($errors)
    {
        foreach ($errors as $error) {
            $this->addTaskError(
                $error['message'],
                $error['context'] ?? [],
                $error['exception'] ?? null
            );
        }
    }

    /**
     * Get task errors
     *
     * @return array
     */
    protected function getTaskErrors()
    {
        return $this->_taskErrors;
    }

    /**
     * Get the entity class name from a table name
     *
     * @param $tablename
     * @param $plugin
     *
     * @return string|null
     */
    protected function _getEntityClass($tablename, $plugin = 'Epi')
    {
        $model = $this->getModel($tablename, $plugin);
        return $model ? $model->getEntityClass() : null;
    }

    /**
     * Caching methods.
     *
     * Used for storing and linking imported IDs in JobImport.
     * /
     *
     * /**
     * Get index key
     *
     * @return string
     */
    protected function _getIndexKey()
    {
        return 'index_job_' . $this->id;
    }

    /**
     * Return parameters used to redirect to the processed entities
     *
     * @return array[]
     */
    protected function _getRedirectParams()
    {
        return [];
    }

    /**
     * Get next URL
     *
     * @return string|false
     */
    public function _getNexturl()
    {
        if (in_array($this->status, ['init', 'work'])) {
            return Router::url([
                'plugin' => false,
                'controller' => 'Jobs',
                'action' => 'execute',
                $this->id,
                'database' => $this->config['database'] ?? '',
                '?' => ['timeout' => $this->timeout]
            ]);
        }

        return false;
    }

    /**
     * Get cancel URL
     *
     * @return string
     */
    public function _getCancelUrl()
    {
        return Router::url([
            'plugin' => false,
            'controller' => 'Jobs',
            'action' => 'cancel',
            $this->id,
            'database' => $this->config['database'] ?? ''
        ]);
    }

    /**
     * The redirect URL used in the finish stage
     *
     * @return string
     */
    protected function _getRedirectUrl()
    {
        $url = $this->config['redirect'] ?? [];
        if (!is_array($url)) {
            return $url;
        }

        $url = array_merge_recursive($url, $this->redirectParams);
        return Router::url($url);
    }

    /**
     * The download URL used in the download stage
     *
     * @return string
     */
    protected function _getDownloadUrl()
    {
        $params =  ['download' => $this->config['download'] ?? ''];
        if (!empty($this->config['force'])) {
            $params['force'] = '1';
        }
        return Router::url([
            'controller' => 'Jobs',
            'action' => 'execute',
            $this->id,
            '?' => $params
        ]);
    }

    /**
     * Get response URL depending on the status
     *
     * //TODO: rename to _getResponseUrl / response_url
     *
     * @return null|string
     */
    protected function _getRedirect()
    {
        if ($this->status === 'finish') {
            if (!empty($this->config['download'])) {
                return $this->downloadUrl;
            }
            elseif (!empty($this->config['redirect'])) {
                return $this->redirectUrl;
            }
        }
        return null;
    }

    /**
     * Load index specified by current index key
     *
     * @return void
     */
    protected function _loadIndex()
    {
        $this->_index = Cache::read($this->index_key, 'index');
    }

    /**
     * Save index specified by current index key
     *
     * @return void
     */
    protected function _saveIndex()
    {
        Cache::write($this->index_key, $this->_index, 'index');
    }

    /**
     * Clear index specified by current index key
     *
     * @return void
     */
    protected function _clearIndex()
    {
        Cache::delete($this->index_key, 'index');
        $this->_index = [];
    }

    /**
     * Publish the _index property
     *
     * * //TODO: implement Index class
     *
     * @return array
     */
    public function &getIndex()
    {
        if ($this->_index === null) {
            $this->_index = [];
        }
        return $this->_index;
    }

    /**
     * Get current task
     *
     * Returns the last element if the pipeline is finished.
     *
     * @param bool $last If the progress is beyond the number of tasks,
     *                   return the last task (true)
     *                   or the finish task (false)
     * @return mixed|string[]
     */
    public function getCurrentTask($last = false)
    {
        if (!isset($this->config['pipeline_progress'])) {
            return ['type' => 'init'];
        }
        else {
            $idx = $this->config['pipeline_progress'];
            if ($last && empty($this->config['pipeline_tasks'][$idx])) {
                $idx = count($this->config['pipeline_tasks']) - 1;
                return $this->config['pipeline_tasks'][$idx];
            }
            if (empty($this->config['pipeline_tasks'][$idx])) {
                return ['type' => 'finish'];
            }
            else {
                return $this->config['pipeline_tasks'][$idx];
            }
        }
    }

    /**
     * Update current pipeline task
     *
     * @param array $current Task array
     * @return void
     */
    public function updateCurrentTask($current)
    {
        $config = $this->config;
        $config['pipeline_tasks'][$config['pipeline_progress']] = $current;
        $this->config = $config;
    }

    /**
     * Finish current task and proceed to next pipeline task
     *
     * @return void
     */
    public function finishCurrentTask()
    {
        $config = $this->config;
        $config['pipeline_progress'] += 1;
        $this->config = $config;
    }

    /**
     * Prepare output path
     *
     * @return void
     */
    public function _prepareOutputPath()
    {
        // Create output folder
        $path = $this->jobPath;

        Files::pruneFiles(Configure::read('Data.databases') . Databank::addPrefix($this->config['database']) . DS . 'jobs');
        Files::createFolder($path, true);
    }

    /**
     * Get output path with trailing slash
     *
     * @return string
     */
    protected function _getJobPath()
    {
        $folderpath = Databank::addPrefix($this->config['database']) . DS . 'jobs' . DS . 'job_' . $this->id . DS;
        $folderpath = Configure::read('Data.databases') . $folderpath;

        return $folderpath;
    }

    /**
     * Get database path with trailing slash
     *
     * @return string
     */
    protected function _getDatabasePath()
    {
        $databaseName = $this->config['database'];
        if (empty($databaseName)) {
            throw new \Cake\Core\Exception\CakeException(__('No database selected'));
        }
        $folderpath = Configure::read('Data.databases') . Databank::addPrefix($databaseName) . DS;

        return $folderpath;
    }

    /**
     * Get shared folder path, i.e. the path that contains the pipelines folder,
     * with trailing slash
     *
     * @return string
     */
    protected function _getSharedPath()
    {
        return Configure::read('Data.shared');
    }

    /**
     * Get current input file (XML)
     *
     * @return string
     */
    public function getCurrentInputFilePath()
    {
        $current = $this->getCurrentTask();
        $filename = empty($current['inputfile']) ? 'job_' . $this->id . '.xml' : $current['inputfile'];

        return $this->jobPath . $filename;
    }

    /**
     * Get list of previous output files
     *
     * TODO: bundle files in a subfolder or specific files, not all tasks' files
     *
     * @return array
     */
    public function getPreviousOutputFiles()
    {
        $current = min(
            $this->config['pipeline_progress'] ?? INF,
            count($this->config['pipeline_tasks']) - 1
        );

        $files = [];
        for ($idx = 0; $idx < $current; $idx++) {
            $files[] = $this->jobPath . $this->config['pipeline_tasks'][$idx]['outputfile'] ?? '';
        }
        $files = array_filter($files);
        $files = array_unique($files);
        return $files;
    }

    /**
     * Get the current output file name
     *
     * @return string
     */
    public function getCurrentOutputFileName()
    {
        $current = $this->getCurrentTask(true);
        $filename =  $current['outputfile'] ?? '';
        if (empty($filename)) {
            $ext = empty($current['extension']) ? 'xml' : trim($current['extension'], " \n\r\t\v\x00/.");
            $filename = 'job_' . $this->id . '.' . $ext;
        }
        return $filename;
    }

    /**
     * Get the path of the current output file
     *
     * @param string|null $filename If the job results in multiple output files, set the filename
     * @return string
     */
    public function getCurrentOutputFilePath($filename = null)
    {
        $current = $this->getCurrentTask(true);

        if (!empty($filename) ) {
            $downloads = str_replace("\r\n", "\n", $current['files'] ?? '');
            $downloads = explode("\n", $downloads);
            if (!in_array($filename,  $downloads)) {
                $filename = null;
            }
        }

        else {
            $filename = $this->getCurrentOutputFileName();
            $current['outputfile'] = $filename;
            $this->updateCurrentTask($current);
        }

        if (empty($filename)) {
            throw new RecordNotFoundException(__('The file {0} is not available for download.', $filename));
        }

        return $this->jobPath . $filename;
    }

    /**
     * Get the progress bar maximum from all job tasks
     *
     * @return void
     */
    public function initProgress()
    {
        $this->progress = 0;
        $this->progressmax = 1; //init task

        foreach (($this->config['pipeline_tasks'] ?? []) as $taskConfig) {

            try {
                /** @var BaseTask $task */
                $task = $this->_getTypedTask($taskConfig);
                $this->progressmax += $task->progressMax();
            } // Fallback
            catch (InvalidTaskException $e) {
                $this->progressmax += 1;
            }
        }
    }

    /**
     * Setup the tasks from the selected pipeline
     *
     * @return void
     */
    protected function initPipeline()
    {
        $options = $this->config;

        $options['pipeline_name'] = $this->jobName;
        $options['pipeline_tasks'] = [['number' => 1, 'type' => $options['task'] ?? '']];
        $options['pipeline_progress'] = 0;

        $this->config = $options;
    }

    /**
     * Update pipeline progress
     *
     * @param int $steps The steps to increase (if a task skips steps)
     * @return void
     */
    public function updateProgress($steps = 1)
    {
        if ($this->status == 'finish') {
            return;
        }

        $this->progress += $steps;
    }

    /**
     * Return a job preview (or the job itself).
     *
     * @param $options
     *
     * @return Job
     */
    public function preview($options)
    {
        $typedJob = $this->typedJob;
        $preview = method_exists($typedJob, 'task_preview') ? $typedJob->task_preview($options) : $this;
        $this->_taskErrors = $typedJob->_taskErrors;
        return $preview;
    }

    /**
     * Instantiate a typed job and call its work method
     *
     * @param int $timeout
     * @param bool $catchwarnings
     *
     * @return mixed
     */
    public function execute($timeout = 1, $catchwarnings = true)
    {
        $this->typedJob->work($timeout, $catchwarnings);
        return $this->typedJob;
    }

    /**
     * Initialize task
     *
     * @return bool Always true, indicates that the initialization step is done
     */
    protected function task_init()
    {
        $this->initPipeline();
        $this->initProgress();

        return true;
    }

    /**
     * Perform pipeline task
     *
     * @param int $timeout
     * @param bool $catchWarnings Convert warnings to errors
     *
     * @return void
     */
    public function work($timeout = 1, $catchWarnings = false)
    {
        // Init logger
        $this->_prepareOutputPath();
        $this->_initLogger($catchWarnings);

        // Load cache
        $this->_loadIndex();

        // Init globals variables and class aliases
        $this->_initGlobals();

        //TableRegistry::getTableLocator()->clear();

        // Set time out
        set_time_limit(300);
        $this->timeout = $timeout;

        // Call tasks
        $round = 0;
        $time_start = microtime(true);
        while (
            (($round < 1) || ((microtime(true) - $time_start) < $this->timeout)) &&
            ($this->status !== 'finish') &&
            ($this->status !== 'error')
        ) {

            $round += 1;
            $current = $this->getCurrentTask();
            $errors = '';

            try {

                //Init
                if (($current['type'] ?? 'init') == 'init') {
                    $this->task_init();
                    $this->status = 'work';
                }
                elseif ($current['type'] === 'finish') {
                    $this->status = 'finish';
                }
                else {
                    // Instantiate task class
                    try {
                        $task = $this->_getTypedTask($current);
                        $finished = $task->execute();
                    } // Or fallback to the legacy methods based approach
                    catch (InvalidTaskException $e) {
                        $method = 'task_' . $current['type'];
                        if (!method_exists($this, $method)) {
                            throw new \Cake\Core\Exception\CakeException("Method {$method} is not a valid export method.");
                        }
                        $finished = $this->{$method}();
                    }

                    if ($finished) {
                        $this->finishCurrentTask();
                    }
                }

                if (!empty($this->_taskErrors)) {
                    $errors = implode('\n', $this->_taskErrors);
                    throw new \Cake\Core\Exception\CakeException($errors);
                }

                // Update progress
                $this->updateProgress();

            } catch (\Cake\Core\Exception\CakeException $e) {
                $this->error = __(
                    'Error executing task of type {type}: {error}',
                    [
                        'type' => $current['type'] ?? '',
                        'error' => h($e->getMessage())
                    ]);
                $this->status = 'error';

                if (!empty($errors)) {
                    Log::write('error', $errors, ['scope' => 'jobs']);
                }
                Log::write('error', $this->error, ['scope' => 'jobs']);
                //stackTrace();
            }
        }

        // Store cache
        $this->_saveIndex();

        // Restore error handler
        $this->_finishLogger($catchWarnings);
    }

    /**
     * Activate database
     *
     * @param $dbname
     * @return Databank
     */
    public function activateDatabank($dbname) : Databank
    {
        BaseTable::setDatabase($dbname);

        $databanks = $this->fetchTable('Databanks');
        $this->databank = $databanks->activateDatabase($dbname);
        return $this->databank;
    }

    /**
     * Parse query parameters to options field
     *
     * TODO: move to JobExport class
     *
     * @param $queryparams
     * @return Job
     */
    public function patchExportOptions($queryparams = [])
    {
        $config = [];

        // Pipeline
        $config['pipeline_id'] = $queryparams['pipeline'] ?? null;

        // Database
        $config['database'] = $queryparams['database'] ?? null;

        // Server
        $config['server'] = Router::url('/', true);

        // Search conditions
        $config['model'] = 'articles';
        $config['table'] = 'articles';

        $params = $queryparams;
        unset($params['database']);
        unset($params['pipeline']);
        unset($params['columns']);
        unset($params['template']);
        unset($params['save']);
        if (empty($params['term'])) {
            unset($params['field']);
        }

        // Rename project to projects
        // @deprecated Used for export from epidesktop. Change in EpiDesktop and then remove.
        if (isset($params['project'])) {
            $params['projects'] = $params['project'];
            unset($params['project']);
        }

        $params = array_filter($params);
        $config['params'] = $params;

        // Selection
        $config['selection'] = $queryparams['selection'] ?? 'selected';
        $this['selection'] = $config['selection'];

        // Update field
        $this->config = $config;
        return $this;
    }

    /**
     * Transfer options from the pipeline and from post request data to the job
     *
     * // TODO: move to JobExport class
     *
     * @param $pipeline
     * @param $requestData
     *
     * @return $this
     */
    public function patchOptions($pipeline = null, $requestData = [])
    {

        // Get options of the job
        // TODO: rename "tasks" to "options" and "options" to "custom"
        $options_job = $this->config['tasks'] ?? [];

        //
        // 1. Enable / disable tasks
        //
        $options_job['enabled'] = [];
        foreach ($pipeline['tasks'] as $taskNo => $taskConfig) {
            if (!empty($taskConfig['canskip'])) {
                $options_job['enabled'][$taskNo]['caption'] = $taskConfig['caption'] ?? $taskNo;
                $options_job['enabled'][$taskNo]['enabled'] = intval($requestData['config']['tasks']['enabled'][$taskNo]['enabled'] ?? true);
            }

            if (($taskConfig['type'] ?? '') === 'data_index') {
                $options_job['index'] = $options_job['enabled'][$taskNo]['enabled'] ?? 1;
            }
        }

        //$options_job['data'] = array_map('intval', $options_job['data']);

        //
        // 2. Merge options of the pipeline
        //

        // Get options of the option task (expected in the first task)
        if (($pipeline['tasks'][0]['type'] ?? '') === 'options') {
            $options_job['options'] = $pipeline['tasks'][0]['options'] ?? [];
        }
        else {
            $options_job['options'] = [];
        }

        // Merge options of the post request values
        $selectedCheckboxes = array_keys(array_filter($requestData['config']['tasks']['check'] ?? [],
            fn($x) => !empty($x)));
        $selectedRadios = array_values($requestData['config']['tasks']['radio'] ?? []);
        $selected = array_merge($selectedCheckboxes, $selectedRadios);
        $selected = array_map(fn($value) => (string)$value, $selected);

        foreach ($options_job['options'] as $key => $option) {
            // @deprecated, legacy code that maps the radio option to the type option
            if (($option['radio'] ?? '') === '1') {
                $option['type'] = 'radio';
            }
            elseif (($option['radio'] ?? '') === '0') {
                $option['type'] = 'check';
            }
            elseif (empty($option['type'])) {
                $option['type'] = 'check';
            }

            if (($option['type'] ?? '') === 'check') {
                $option['output'] = intval($option['output']);
                if (!empty($requestData['config']['tasks']['check'])) {
                    $option['output'] = in_array((string)$option['number'], $selected) ? 1 : 0;
                }
            }
            elseif (($option['type'] ?? '') === 'radio') {
                $option['output'] = intval($option['output']);
                if (!empty($requestData['config']['tasks']['radio'])) {
                    $option['output'] = in_array((string)$option['number'], $selected) ? 1 : 0;
                }
            }
            elseif (($option['type'] ?? '') === 'text') {
                $option['output'] = $option['value'] ?? '';
                if (!empty($requestData['config']['tasks']['text'])) {
                    $option['output'] = $requestData['config']['tasks']['text'][$option['number']];
                }
            }
            $options_job['options'][$key] = $option;
        }

        //
        // 3. Transfer to job config
        //
        $this->config['tasks'] = $options_job;
        $this->config['pipeline_name'] = $pipeline->name;
//        $this->config['pipeline_progress'] = 0;

        return $this;
    }

    /**
     * Get the selection options for the job configuration form
     *
     * TODO: implement common base class for JobExport and JobMutate, move function there?
     * TODO: for properties, show number of scopes, allow multiple scopes
     * TODO: Rename to _getRecordOptions()
     *
     * @return array
     */
    protected function _getSelectionOptions()
    {
        $model = $this->getModel($this->config['table'], 'Epi');
        if (!($model instanceof MutateTableInterface)) {
            throw new InternalErrorException('The model does not support entity mutation.');
        }
        $options = [];

        $selectedParams = $this->_getDataParams('selected');
        if (!empty($selectedParams)) {
            $countSelected = $model->mutateGetCount($selectedParams, $this);
            $options['selected'] = __('Selected records ({0})', $countSelected);
        }

        $filteredParams = $this->_getDataParams('filtered');
        if (!empty(array_diff($selectedParams, $filteredParams))) {
            $countAll = $model->mutateGetCount($filteredParams, $this);
            $options['filtered'] = __('All records ({0})', $countAll);
        }

        return $options;
    }

    /**
     * Return parameters used to retrieve entities
     *
     * TODO: implement common base class for JobExport and JobMutate, move function there?
     *
     * @param string $selection 'selected', 'filtered' or null to use the config
     * @return array[]
     */
    protected function _getDataParams($selection = null)
    {
        $params = $this->config['params'] ?? [];
        $selection = $selection ?? $this['config']['selection'] ?? '';
        $table = $this->config['table'] ?? '';
        $ids = $params[$table] ?? '';

        // Remove non data params and empty values
        $params = array_diff_key(
            $params,
            [
                'task' => false,
                'selection' => false,
//                'sort' => false,
                'sortby' => false,
                'columns' => false,
                'template' => false,
                'mode' => false,
                'save' => false,
                'load' => false,
                $table => false
            ]
        );

        $params = array_filter($params, fn($param) => $param !== '');

        if (($selection === 'selected') && !empty($ids)) {
            $params[$table] = $ids;
        }

        return $params;
    }

    protected function _getProgressLabel()
    {
        return $this->progress . '/' . $this->progressmax;
    }

    /**
     * Return fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        $fields = [
            'typ' => ['caption' => __('Job type')],
            'status' => ['caption' => __('Status')],
            'delay' => ['caption' => __('Delayed')],
            'queueStatus' => ['caption' => __('Queue status')],
            'progressLabel' => ['caption' => __('Progress')],
            'created' => [
                'caption' => __('Created'),
                'action' => 'view'
            ],

            'modified' => [
                'caption' => __('Modified'),
                'action' => 'view'
            ]
        ];

        return $fields;
    }
}

class InvalidTaskException extends \Cake\Core\Exception\CakeException
{
}

