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

use App\Utilities\Files\Files;
use Cake\Datasource\Exception\RecordNotFoundException;
use Exception;

/**
 * Base class for tasks
 *
 * To create a new task:
 * - Create a new class in the folder Model/Entity/Tasks that extends BaseTask
 * - Create a new template in the folder templates/Tasks that provides the configuration form
 * - If the task will be used within a pipeline, add the task to the pipeline configuration
 *   (Model/Entity/Pipeline.php -> Pipeline->tasksConfig)
 * - If the task will be used for mutate operations, implement configuration options in the
 *   task class. Then implement mutateGetTasks() and a respective mutate method in the target table class
 *   (e.g. in ArticlesTable.php).
 */
class BaseTask
{

    /**
     * Task configuration
     *
     * @var array
     */
    public array $config = [];

    /**
     * Job the task belongs to
     *
     * @var Job
     */
    public Job $job;

    /**
     * Constructor
     *
     * @param array $config
     * @param Job $job
     */
    public function __construct($config, $job)
    {
        $this->config = $config;
        $this->job = $job;
    }

    /**
     * Activate the database
     *
     * @param string $databankName The database name. Leave empty to use the task or job default.
     * @return Databank
     */
    protected function activateDatabank($databankName = null)
    {
        // TODO: Check permissions for the database.
        if (empty($databankName)) {
            $databankName = empty($this->config['database']) ? $this->job->config['database'] : $this->config['database'];
        }
        return $this->job->activateDatabank($databankName);
    }

    /**
     * Get the data query conditions
     *
     * @return array
     */
    public function getDataParams()
    {
        return $this->job->dataParams ?? [];
    }

    /**
     * Get paging parameters
     *
     * @return array An array with the keys offset and limit, and optionally order
     */
    public function getPagingParams()
    {
        $offset = $this->config['offset'];
        $limit = $this->job->limit;
        return compact('offset', 'limit');
    }


    /**
     * Get current input file
     *
     * The full path to the input file is stored in 'inputpath'.
     * The path relative to the job folder is stored in 'inputfile'.
     *  In case both are empty, a default file name based on the job id is generated.
     *
     * @return string
     */
    public function getCurrentInputFilePath()
    {
        $task = $this->config ?? [];

        // If present, 'inputpath' contains the full path to the input file
        $filename = $task['inputpath'] ?? null;
        if (!empty($filename)) {
            return $filename;
        }

        // Otherwise, 'inputfile' may contain the relative path to the input file
        $filename = $task['inputfile'] ?? null;
        if (!empty($filename)) {
            return $this->job->jobPath . $filename;
        }

        // If no input file is specified, return the default job file
        $filename = 'job_' . $this->job->id . '.xml';
        return $this->job->jobPath . $filename;
    }

    /**
     * Import modes depend on the source: csv file, xml file or folder (with xml files
     *
     * @return string
     * @throws Exception
     */
    protected function getCurrentInputMode()
    {
        $inputFile = $this->getCurrentInputFilePath();

        if (empty($inputFile)) {
            return '';
        }

        $ext = strtolower(pathinfo($inputFile, PATHINFO_EXTENSION));

        // xml, csv
        if (is_file($inputFile)) {
            return $ext;
        }

        // folder
        elseif (is_dir($inputFile)) {
            return 'folder';
        }

        // unclear
        else {
            return '';
        }
    }

    /**
     * Get the current output file name
     *
     * @return string
     */
    public function getCurrentOutputFileName()
    {
        $current = $this->config;
        $filename =  $current['outputfile'] ?? '';
        if (empty($filename)) {
            $ext = empty($current['extension']) ? 'xml' : trim($current['extension'], " \n\r\t\v\x00/.");
            if (empty($this->job->id)) {
                $filename = Files::getTempFilename('temp', $ext);
            }
            else {
                $filename = 'job_' . $this->job->id . '.' . $ext;
            }
        }
        return $filename;
    }

    /**
     * Get the path of the current output file
     *
     * @return string
     */
    public function getCurrentOutputFilePath()
    {
        $filename = $this->getCurrentOutputFileName();

        $current = $this->config;
        $current['outputfile'] = $filename;
        $this->job->updateCurrentTaskConfig($current);

        if (empty($filename)) {
            throw new RecordNotFoundException(__('The file {0} is not available for download.', $filename));
        }

        // Make absolute path
        if (strpos($filename, '/') !== 0 && strpos($filename, '\\') !== 0) {
            $filename  = $this->job->jobPath . $filename;
        }

        return $filename;
    }

    /**
     * Reset the task progress
     *
     * @return true
     */
    public function init()
    {
        return true;
    }

    /**
     * Get a preview of the task results
     *
     * Used for import tasks to show a preview of the data that will be imported.
     *
     * @param array $options
     * @return array
     */
    public function preview($options = [])
    {
        return [];
    }

    /**
     * Execute the task
     *
     * Execute is called until the task is finished.
     *
     * @return boolean Returns true when the task is finished.
     */
    public function execute()
    {
        return true;
    }

    /**
     * Get steps needed
     *
     * How many calls of execute will be needed to finish the task?
     *
     * @return int
     */
    public function progressMax()
    {
        return 1;
    }

}
