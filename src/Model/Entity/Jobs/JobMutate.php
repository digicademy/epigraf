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

namespace App\Model\Entity\Jobs;

use App\Model\Entity\Job;
use App\Model\Interfaces\MutateTableInterface;
use Cake\Http\Exception\InternalErrorException;
use Cake\I18n\FrozenTime;

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
    public $jobName = 'Mutate entities';

    /**
     * Number of entities that are processed in one iteration
     *
     * @var int
     */
    public int $limit = 30;


    /**
     * Get the tasks for the job configuration form
     *
     * //TODO: use static variables of the task classes?
     *
     * @return string[]
     */
    protected function _getTasks()
    {
        $model = $this->getModel($this->config['table'], 'Epi');
        if (!($model instanceof MutateTableInterface)) {
            throw new InternalErrorException('The model does not support entity mutation.');
        }

        return $model->mutateGetTasks();
    }

    /**
     * Return fields for the HTML configuration form
     *
     * The fields are obtained from the selected task
     *
     * @return array[]
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
     * Return parameters used to redirect to the mutated entities
     *
     * @return array[]
     */
    protected function _getRedirectParams()
    {
        $params = $this->config['params'] ?? [];
        $table = $this->config['table'] ?? '';

        $params = array_diff_key(
            $params,
            [
                'task' => false,
                'selection' => false,
                $table => false
            ]
        );

        $task = $this->config['task'] ?? false;
        if ($task) {
            $task = $this->_getTypedTask(['type' => $task]);
            $params = $task->updateRedirectParams($params);
        }

        return ['?' => $params];
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
        $model = $this->getModel($this->config['table'], 'Epi');
        if (!($model instanceof MutateTableInterface)) {
            throw new InternalErrorException('The model does not support entity mutation.');
        }

        $params = $this->dataParams;
        $count = $model->mutateGetCount($params, $this);
        return ceil($count / $this->limit) + 1;
    }

}
