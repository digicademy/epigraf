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

use App\Cache\Cache;
use App\Model\Table\BaseTable;
use App\Model\Table\JobsTable;
use App\Utilities\Converters\Arrays;
use App\Utilities\Files\Files;
use Batch\Model\Jobs\JobExport;
use Batch\Model\Jobs\JobImport;
use Batch\Model\Jobs\JobMutate;
use Batch\Model\Jobs\JobTransfer;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Error\Debugger;
use Cake\Error\ErrorLogger;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenTime;
use Cake\Log\Engine\FileLog;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cron\CronExpression;
use Batch\Model\Tasks\BaseTask;
use Predis\Client;
use Throwable;

/**
 * Job Entity
 *
 * This is a factory class for the different job types,
 * which are implemented as subclasses of Job in the Batch plugin.
 * Typed jobs are instantiated in the virtual typedJob property
 * (see _getTypedJob() method).
 *
 * # Database fields (without inherited fields)
 * @property string name A job name for persistent jobs
 * @property string $jobtype
 * @property int $delay If this is a delayed job, a number greater than 0.
 * @property string|null $schedule A cron expression for scheduled jobs.
 * @property \DateTime|null $nextrun The next scheduled run time for scheduled jobs.
 * @property string $status
 * @property array $config The config is an array, serialized as JSON.
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
     * Current pipeline, loaded using the pipeline_id key in the config field
     *
     * @var Pipeline|null
     */
    protected $_pipeline = null;

    /**
     * Map job type to typed job entity
     *
     * @var array
     */
    public $jobClasses = [
        'import' => JobImport::class,
        'export' => JobExport::class,
        'mutate' => JobMutate::class,
        'transfer' => JobTransfer::class
    ];

    /**
     * Cache task classes
     *
     * @var array
     */
    protected $_taskClasses = null;

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
        'schedule' => true,
        'nextrun' => true,
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
     * @var string[]
     */
    protected $_virtual = ['nextUrl', 'redirectUrl','downloadUrl','etc'];

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
     * TODO: Implement tasks registry, without needing to scan the folders for classes on every job instantiation.
     *
     * @return void
     */
    protected function _getTaskClasses()
    {
        if ($this->_taskClasses === null) {

            // Create task classes list
            $basePath = ROOT . DS . 'plugins' . DS . 'Batch' . DS . 'src' . DS . 'Model' . DS . 'Tasks' . DS;
            $task_classes = Files::getClassesInPath(
                [
                    $basePath . 'Export' => 'Batch\Model\Tasks\Export',
                    $basePath . 'Import' => 'Batch\Model\Tasks\Import',
                    $basePath . 'Mutate' => 'Batch\Model\Tasks\Mutate',
                    $basePath . 'Transfer' => 'Batch\Model\Tasks\Transfer'
                ]
            );

            // TODO: filter out BaseTasks
            $task_names = array_map(function ($x) {
                $x = explode('\\', $x);
                $x = array_pop($x);
                $x = str_replace('Task', '', $x);
                return Inflector::underscore($x);
            }, $task_classes);

            $this->_taskClasses = array_combine($task_names, $task_classes);
        }

        return $this->_taskClasses;
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
            $job->nextUrl = false;
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
        $params = $this->config['params'] ?? [];
        $table = $this->config['table'] ?? '';

        $params = array_diff_key($params, array_flip(['task', 'selection', 'id', 'snippets', 'page', 'id', 'target','propertytype', $table]));

        $task = $this->config['task'] ?? false;
        if ($task) {
            $task = $this->_getTypedTask(['type' => $task]);
            $params = $task->updateRedirectParams($params);
        }

        $params = empty($params) ? [] : ['?' => $params];
        return $params;
    }

    /**
     * Get next URL
     *
     * @return string|false
     */
    public function _getNextUrl()
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
        Files::pruneFiles(Configure::read('Data.databases') . Databank::addPrefix($this->config['database']) . DS . 'jobs' . DS);
        Files::createFolder($this->jobPath, true);
    }

    /**
     * Get output path with trailing slash
     *
     * @return string
     */
    protected function _getJobPath()
    {
        $folderpath = Databank::addPrefix($this->config['database']) . DS
            . 'jobs' . DS .'job-' . $this->id . DS;
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

        // Check if file is in download list
        if (!empty($filename) ) {

            $found = false;

            // First look in the results
            $downloads = $this->result['downloads'] ?? [];
            foreach ($downloads as $download) {
                if ($download['name'] === $filename) {
                    $target = $download['target'] ?? '';
                    $root = $download['root'] ?? null;
                    $found = true;
                    break;
                }
            }

            // Then look in the pipeline tasks
            if (!$found) {
                $current = $this->getCurrentTaskConfig(true);
                $downloads = str_replace("\r\n", "\n", $current['files'] ?? '');
                $downloads = explode("\n", $downloads);
                if (in_array($filename, $downloads)) {
                    $found = true;
                    $target = $current['target'] ?? '';
                    $root = $current['root'] ?? null;
                }
            }

            if (!$found) {
                throw new RecordNotFoundException(__('The file {0} is not available for download.', $filename));
            }
        }

        // Tasks without download list in the results
        else {
            $current = $this->getCurrentTaskConfig(true);
            $task = $this->_getTypedTask($current);

            $filename = $task->getCurrentOutputFilePath();
            $target = $current['target'] ?? '';
            $root = $current['root'] ?? null;
        }

        if (empty($filename)) {
            throw new RecordNotFoundException(__('The file {0} is not available for download.', $filename));
        }

        if (strpos($filename, '/') !== false && strpos($filename, '\\') !== false) {
            throw new BadRequestException(__('The file name {0} contains invalid characters.', $filename));
        }

        // Make absolute path
        if ($root === 'database') {
            $rootPath = $this->databasePath;
        }
        elseif ($root === 'shared') {
            $rootPath = $this->sharedPath;
        }
        else {
            $rootPath = $this->jobPath;
        }

        $targetPath = Files::addSlash((Files::addSlash($rootPath) . $target)) . $filename;

        return $targetPath;
    }

    /**
     * Called from the mutate job
     *
     * @param array $dataParams Params passed to the find method of the model to get the entities to mutate.
     * @return int Number of articles for calculating the progress bar.
     */
    protected function getEntitiesCount($dataParams): int
    {
        $model = $this->getModel($this->config['table'], 'Epi');
        $dataParams = $model->parseRequestParameters($dataParams);

        // TODO: Implement somewhere else, this violates separation of concerns?
        if ($model->getAlias() === 'Epi.Properties') {
            $dataParams['ancestors'] = false;
            $dataParams['treePositions'] = false;
        }

        return $model
            ->find('hasParams', $dataParams)
            ->count();
    }


    /**
     * Get the maximum number of steps one task needs
     *
     * Called by the mutate tasks
     *
     * @return float|int
     */
    protected function _getBatchCount()
    {
        $count = $this->getEntitiesCount($this->dataParams);
        return ceil($count / $this->limit) + 1;
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
     * Don't mix the task option, pipeline_id option and pipeline_tasks option!
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
        if (!empty($this->config['pipeline_tasks'])) {
            foreach (($this->config['pipeline_tasks'] ?? []) as $taskKey => $taskConfig) {

                /** @var BaseTask $task */
                $task = $this->_getTypedTask($taskConfig);
                $this->config['pipeline_tasks'][$taskKey] = $task->reset();
            }
        }

        $this->setDirty('config');

        if (is_dir($this->jobPath)) {
            Files::delete($this->jobPath);
        }
    }

    /**
     * Update next run date based on cron schedule
     *
     * @return \DateTime|null The next run date, or null if no schedule is set
     */
    public function updateNextRun()
    {
        if (empty($this->schedule)) {
            $this->nextrun = null;
        } else {
            $cron = new CronExpression($this->schedule);
            $this->nextrun = new FrozenTime($cron->getNextRunDate());
        }

        return  $this->nextrun;
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
            $currentType = $current['type'] ?? 'init';

            try {

                //Init
                if ($currentType == 'init') {
                    $this->task_init();
                    $this->status = 'work';
                    $this->progress += 1;
                }
                elseif ($currentType === 'finish') {
                    $this->status = 'finish';
                }
                else {
                    // Instantiate task class
                    try {
                        $task = $this->_getTypedTask($current);
                        $finished = $task->execute();
                    } // Or fallback to the legacy methods based approach
                    catch (InvalidTaskException $e) {
                        $method = 'task_' . $currentType;
                        if (!method_exists($this, $method)) {
                            throw new \Cake\Core\Exception\CakeException("Method {$method} is not a valid export method.");
                        }
                        $finished = $this->{$method}();
                    }

                    if ($finished) {
                        $this->finishCurrentTask();
                    }
                    $this->progress += 1;
                }

                if (!empty($this->_taskErrors)) {
                    $errors = implode('\n', $this->_taskErrors);
                    throw new \Cake\Core\Exception\CakeException($errors);
                }

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
     * Get a caption containing the name or, as a fallback, the job id.
     *
     * @return string
     */
    protected function _getCaption()
    {
        $name = $this->name;
        if (empty($name)) {
            return 'job-' . $this->id;
        }
        return $name;
    }

    /**
     * Virtual pipeline property
     *
     * @return Pipeline|null
     */
    protected function _getPipeline()
    {
        if (empty($this->config['pipeline_id'])) {
            return null;
        }

        if (empty($this->_pipeline)) {
            $this->_pipeline = $this->fetchTable('Pipelines')->get($this->config['pipeline_id']);
        }

        return $this->_pipeline;
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
        $options = [];

        $selectedParams = $this->_getDataParams('selected');
        if (!empty($selectedParams)) {
            $countSelected = $this->getEntitiesCount($selectedParams);
            $options['selected'] = __('Selected records ({0})', $countSelected);
        }

        $filteredParams = $this->_getDataParams('filtered');
        if (!empty(array_diff($selectedParams, $filteredParams)) ||empty($filteredParams)) {
            $countAll = $this->getEntitiesCount($filteredParams);
            $options['filtered'] = __('All records ({0})', $countAll);
        }

        return $options;
    }

    /**
     * Return parameters used to retrieve entities
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
            'iri_path' => ['caption' => __('IRI path'), 'format' => 'iri', 'action' => 'view'],
            'norm_iri' => ['caption' => __('IRI fragment'), 'action' => ['edit', 'add']],
            'status' => ['caption' => __('Status')],
            'delay' => ['caption' => __('Delayed')],
            'schedule' => [
                'caption' => __('Schedule'),
                'help' => __('Add a crontab expression to schedule the job.')
            ],
            'nextrun' => [
                'caption' => __('Next run'),
                'help' => __('Current server time is {0}', FrozenTime::now())
            ],
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

    /**
     * Get pipeline task options grouped by category
     *
     * @return array
     */
    protected function _getHtmlOptions()
    {
        $options = [];

        $customOptions = [];
        foreach (($this->pipeline['tasks'] ?? []) as $taskNo => $taskConfig) {
            if (($taskConfig['type'] ?? '') === 'options') {
                $customOptions = $taskConfig['options'] ?? [];
                break;
            }
        }

        $customOptions = Arrays::array_nest($customOptions, 'category');
        foreach ($customOptions as $group => $groupOptions) {

            // Assemble radio options
            $radioOptions = array_values(array_filter($groupOptions, fn($x) =>  $x['type'] === 'radio'));
            if (!empty($radioOptions)) {
                $customKey = $radioOptions[0]['key'] ?? '';

                $options[$group] = [
                    [
                        'type' => 'radio',
                        'key' => $customKey,
                        'options' => Hash::combine($radioOptions,'{n}.value','{n}.label')
                    ]
                ];
            }

            // Keep other options
            else {
                $options[$group] = $groupOptions;
            }

        }

        return $options;
    }
}

class InvalidTaskException extends \Cake\Core\Exception\CakeException
{
}

