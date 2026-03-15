<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace Batch\Model\Jobs;

use App\Model\Entity\Job;
use App\Model\Table\BaseTable as AppBaseTable;

/**
 * Call a mutate method on all selected entities
 */
class JobMutate extends Job
{

    /**
     * Job name
     *
     * @var string
     */
    public $jobName = 'Mutate';

    /**
     * Number of entities that are processed in one iteration
     *
     * @var int
     */
    public int $limit = 30;


    /**
     * Return fields for the job configuration form
     *
     * The fields are obtained from the selected task.
     * See BaseEntityHelper::entityTable() for the supported options.
     *
     * @return array[] Field configuration array.
     */
    protected function _getHtmlFields()
    {
        $task = $this->config['task'] ?? false;
        if ($task) {
            $task = $this->_getTypedTask(['type' => $task]);
            return $task->updateHtmlFields([]);
        }
        else {
            return [];
        }
    }

    /**
     * Get the tasks for the job configuration form
     *
     * //TODO: use static variables of the task classes?
     *
     * @return string[]
     */
    protected function _getTasks()
    {
        $targetModel = $this->getModelName($this->config['table'], 'Epi');
        $tasks = [];
        foreach ($this->taskClasses as $taskName => $taskClass) {
            if (in_array($targetModel, $taskClass::$taskModels)) {

                // TODO: Implement granular permission management for tasks
                if (
                    in_array(AppBaseTable::$userRole, $taskClass::$allowed) ||
                    in_array(AppBaseTable::$userRole, ['admin', 'devel'])
                )
                {
                    $tasks[$taskName] = $taskClass::$caption;
                }
            }
        }

        return $tasks;
    }

}
