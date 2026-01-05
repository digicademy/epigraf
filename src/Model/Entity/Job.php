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
use Cake\Http\Exception\BadRequestException;
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
 * @property string name A job name for persistent jobs
 * @property string $jobtype
 * @property int $delay If this is a delayed job, a number greater than 0.
 * @property string $status
 * @property array $config
 * @property array $result
 * @property int $progress
 * @property int $progressmax
 * @property string $norm_iri
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
        'name' => true,
        'jobtype' => true,
        'delay' => true,
        'status' => true,
        'config' => true,
        'result' => true,
        'progress' => true,
        'progressmax' => true,
        'norm_iri' => true
    ];

    /**
     * Expose virtual fields
     *
     * TODO: Rename nexturl to nextUrl
     *
     * @var string[]
     */
    protected $_virtual = ['nexturl', 'redirectUrl','downloadUrl','etc'];

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
            'job_type' => $this->jobtype
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
     * Check whether the job is finished
     *
     * @return bool
     */
    protected function _getIsFinished(): bool {
        return !in_array($this->status, ['init', 'work']);
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
            $jobClass = $this->jobClasses[$this->jobtype ?? 'export'];

            if (empty($jobClass)) {
                throw new \Cake\Core\Exception\CakeException("Invalid job type.");
            }

            if (empty($this->_typedJob)) {
                $this->_typedJob = new $jobClass($this->toArray());
                $this->_typedJob->setDirty('modified', false);
                $this->_typedJob->setIndex($this->getIndex());
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
     * Add result data to the job
     *
     * @param array $data
     * @return void
     */
    public function addResultData($data)
    {
        $this['result'] = array_merge($this['result'] ?? [], $data);
    }

    /**
     * Check whether the job is finished
     *
     * @return bool
     */
    protected function _isFinished()
    {
        return $this->status === 'finish';
    }

    /**
     * @return int|void
     */
    protected function _getEtc()
    {
        if ($this->finished) {
            return 0;
        }

        if (empty($this->progressmax) || empty($this->progress)) {
            return INF;
        }

        $diffSeconds = $this->modified->getTimestamp() - $this->created->getTimestamp();
        if ($diffSeconds < 3) {
            return INF;
        }
        return max(0, ($diffSeconds / $this->progressmax) * ($this->progressmax - $this->progress));
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

        return null;
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
        if (($this->status !== 'finish') || empty($this->config['redirect'])) {
            return null;
        }

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
        if (($this->status !== 'finish') || empty($this->config['download'])) {
            return null;
        }

        $fileName = $this->config['download'];

        // We can't just add the extension because unknown extensions behind an ID will
        // not be recognized by the controller (e.g. jobs/execute/1234.doc will not work).
        $fileExt = $this->config['format'] ?? ''; //pathinfo($fileName, PATHINFO_EXTENSION);

        $params =  ['download' => $fileName];
        if (!empty($this->config['force'])) {
            $params['force'] = '1';
        }

        return Router::url([
            'controller' => 'Jobs',
            'action' => 'execute',
            $this->id,
            '_ext' => $fileExt,
            '?' => $params
        ]);
    }

    /**
     * Get response URL depending on the status
     *
     * @return null|string
     */
    protected function _getResponseUrl()
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
    protected function initIndex()
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
     * Attach index by reference, when factoring typed jobs
     *
     * * //TODO: implement Index class
     *
     * @return array
     */
    public function setIndex(&$index)
    {
        $this->_index = &$index;
        return $this->_index;
    }

    /**
     * Get current task config
     *
     * Returns the last element if the pipeline is finished.
     *
     * @param bool $last If the progress is beyond the number of tasks,
     *                   return the last task (true)
     *                   or the finish task (false)
     * @return mixed|string[]
     */
    public function getCurrentTaskConfig($last = false)
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
    public function updateCurrentTaskConfig($current)
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
     * Create a job folder and prune old job folders
     *
     * @return void
     */
    public function _prepareOutputPath()
    {
        Files::pruneFiles(Configure::read('Data.databases') . Databank::addPrefix($this->config['database']) . DS . 'jobs');
        Files::createFolder($this->jobPath, true);
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
     * Get the path of the current output file
     *
     * @param string|null $filename If the job results in multiple output files, set the filename
     * @return string
     */
    public function getJobOutputFilePath($filename = null)
    {
        $current = $this->getCurrentTaskConfig(true);

        // Check if file name is valid
        if (!empty($filename) ) {
            $downloads = str_replace("\r\n", "\n", $current['files'] ?? '');
            $downloads = explode("\n", $downloads);
            if (!in_array($filename,  $downloads)) {
                $filename = null;
            }
        }

        else {
            $task = $this->_getTypedTask($current);
            $filename = $task->getCurrentOutputFilePath();
        }

        if (empty($filename)) {
            throw new RecordNotFoundException(__('The file {0} is not available for download.', $filename));
        }

        if (strpos($filename, '/') !== false && strpos($filename, '\\') !== false) {
            throw new BadRequestException(__('The file name {0} contains invalid characters.', $filename));
        }

        // Make absolute path
        $root = $current['root'] ?? null;
        if ($root === 'database') {
            $rootPath = $this->databasePath;
        }
        elseif ($root === 'shared') {
            $rootPath = $this->sharedPath;
        }
        else {
            $rootPath = $this->jobPath;
        }

        $target = $current['target'] ?? '';
        $targetPath = Files::addSlash((Files::addSlash($rootPath) . $target)) . $filename;

        return $targetPath;
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
     * Setup the tasks
     *
     * By default, jobs contain one task defined in the task options.
     *
     * In case a pipeline_id option is provided, the tasks are taken from the respective pipeline.
     * In case a pipeline_tasks option is provided, those tasks are used.
     *
     * Don't mix the task option, pipeline_id option and pipeline_tasks option.
     *
     * @return void
     */
    protected function initPipeline()
    {
        $options = $this->config;

        $options['pipeline_name'] = $this->jobName ?? '';
        $options['pipeline_progress'] = 0;

        // Add default task
        if (isset($options['task'])) {
            $options['pipeline_tasks'] = [];
            $options['pipeline_tasks'][] = ['number' => 1, 'type' => $options['task'] ?? ''];
        }

        // Add pipeline tasks and name
        else if (!empty($options['pipeline_id'])) {

            //TableRegistry::getTableLocator()->clear();
            $pipelines = $this->fetchTable('Pipelines');

            /** @var Pipeline $pipeline */
            $pipeline = $pipelines->get($options['pipeline_id']);

            $options['pipeline_name'] = $pipeline['name'] ?? $options['pipeline_name'];

            $options['pipeline_tasks'] = [];
            foreach (($pipeline['tasks'] ?? []) as $taskNo => $taskConfig) {

                // Skip disabled tasks
                if (!empty($taskConfig['canskip']) && empty($this->config['options']['enabled'][$taskNo]['enabled'])) {
                    continue;
                }

                // Set input file name
                if (($taskNo === 0) && !empty($this->config['inputpath'])) {
                    $taskConfig['inputpath'] = $this->config['inputpath'];
                }

                $options['pipeline_tasks'][] = $taskConfig;
            }
        }

        else {
            $options['pipeline_tasks'] = $options['pipeline_tasks'] ?? [];
        }

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
     * Preview the first task or the job itself
     *
     * @param array $options
     *
     * @return array|Job
     */
    public function preview($options = [])
    {
        // Get the preview from the last task
        // Intermediate preview data is passed to the next task as options
        $previewData = $options;
        $this->initPipeline();
        foreach (($this->config['pipeline_tasks'] ?? []) as $taskConfig) {
            try {
                /** @var BaseTask $task */
                $task = $this->_getTypedTask($taskConfig);
                $task->config = array_merge($task->config, $previewData);
                $previewData = $task->preview($task->config);
            } // Fallback
            catch (InvalidTaskException $e) {
                return $this;
            }
        }
        return $previewData;
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
        $this->initIndex();

        return true;
    }

    /**
     * Reset a job, for running it another time
     *
     * @return void
     */
    public function reset()
    {
        $this->status = 'init';
        unset($this->config['pipeline_progress']);
        Files::removeFolder($this->jobPath, true);
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
            $current = $this->getCurrentTaskConfig();

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

                $this->addResultData(['error' => $this->error]);
                $this->status = 'error';

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
        if (!empty(array_diff($selectedParams, $filteredParams)) ||empty($filteredParams)) {
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
        $ids = $params['id'] ?? '';

        // Remove non data params and empty values
        $params = array_diff_key(
            $params,
            [
                'task' => false,
                'selection' => false,
                'sortby' => false,
                'columns' => false,
                'template' => false,
                'mode' => false,
                'save' => false,
                'load' => false,
                'id' => false,
                'seek' => false
            ]
        );

        if (empty($this->config['expand'] ?? true)) {
            $params['columns'] = $this->config['params']['columns'] ?? '';
        }

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
     * Return fields to be rendered in entity tables
     *
     * See BaseEntityHelper::entityTable() for the supported options.
     *
     * @return array[] Field configuration array.
     */
    protected function _getHtmlFields()
    {
        $fields = [
            'name' => ['caption' => __('Job name')],
            'jobtype' => ['caption' => __('Job type')],
            'iri_path' => ['caption' => __('IRI path'), 'action' => 'view'],
            'norm_iri' => ['caption' => __('IRI fragment'), 'action' => 'edit'],
            'status' => ['caption' => __('Status')],
            'delay' => ['caption' => __('Delayed')],
            'queueStatus' => ['caption' => __('Queue status'), 'action' => 'view'],
            'progressLabel' => ['caption' => __('Progress'), 'action' => 'view'],
            'progress' => ['caption' => __('Progress'), 'action' => 'edit'],
            'progressmax' => ['caption' => __('Max progress'), 'action' => 'edit'],
            'config' => [
                'caption' => __('Config'),
                'rows' => 15,
                'format' => 'json',
                'type' => 'jsoneditor',
                'action' => 'edit'
            ],
            'result' => [
                'caption' => __('Result'),
                'rows' => 15,
                'format' => 'json',
                'type' => 'jsoneditor',
                'action' => 'edit'
            ],
            'created' => [
                'caption' => __('Created'),
                'action' => 'view'
            ],

            'modified' => [
                'caption' => __('Modified'),
                'action' => 'view'
            ],
            'etc' => ['caption' => __('ETC'), 'action' => 'view']
        ];

        return $fields;
    }
}

class InvalidTaskException extends \Cake\Core\Exception\CakeException
{
}

