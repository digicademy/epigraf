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

/**
 * Base class for tasks
 *
 * To create a new task:
 * - Create a new class in the folder Model/Entity/Tasks that extends BaseTask
 * - Create a new template in the folder templates/Tasks that provides the configuration form
 * - If the task will be used within a pipeline, add the task to the pipeline configuration
 *   (Model/Entity/Pipeline.php -> Pipeline->tasksConfig)
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
     * @return Databank
     */
    protected function activateDatabank()
    {
        // TODO: Check permissions for the database.
        $databankName = empty($this->config['database']) ? $this->job->config['database'] : $this->config['database'];
        /** @var Databank $databank */
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
     * @return array An array with the keys offset and limit, and optionally sort
     */
    public function getPagingParams()
    {
        $offset = $this->config['offset'];
        $limit = $this->job->limit;
        return compact('offset', 'limit');
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
