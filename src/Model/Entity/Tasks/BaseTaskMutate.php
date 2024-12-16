<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity\Tasks;

use App\Model\Entity\BaseTask;
use App\Model\Interfaces\MutateTableInterface;
use Cake\Http\Exception\InternalErrorException;
use Exception;

/**
 * Basic task to mutate articles or other entitites
 */
class BaseTaskMutate extends BaseTask
{

    /**
     * Get options for the configuration form
     *
     * Overwrite in derived classes
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {
        return $fields;
    }

    /**
     * Update parameters that redirect to the mutated entitites
     *
     * Overwrite in derived classes
     *
     * @param array $params The parameters to be changed
     * @return array The updated parameters
     */
    public function updateRedirectParams($params)
    {
        return $params;
    }

    /**
     * Get parameters that are passed to the mutateEntities method
     *
     * @return array
     */
    public function getTaskParams()
    {
        $params = [
            'task' => $this->job->config['task'] ?? null,
            'sortby' => $this->job->config['params']['sortby'] ?? null
        ];

        return $params;
    }

    /**
     * Get the number of steps for this task
     *
     * @return int
     */
    public function progressMax()
    {
        return $this->job->batchCount;
    }

    /**
     * Execute task
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $this->job->activateDatabank($this->job->config['database']);

        $model = $this->job->getModel($this->job->config['table'], 'Epi');
        if (!($model instanceof MutateTableInterface)) {
            throw new InternalErrorException('The model does not support entity mutation.');
        }

        $this->config['offset'] = $this->config['offset'] ?? 0;
        $limit = $this->job->limit;
        $offset = $this->config['offset'];

        // TODO: keep sort/order parameter
        $taskParams = $this->getTaskParams();
        $dataParams = $this->job->dataParams;

        try {
            $entities = $model->mutateEntities(
                $taskParams,
                $dataParams,
                $offset,
                $limit
            );
        } catch (Exception $e) {
            $msg = __('Error mutating entitites: {error}.', $taskParams);
            $this->job->addTaskError($msg, $taskParams, $e);
            return true;
        }

        $this->config['cursor'] = empty($entities) ? -1 : $entities[count($entities) - 1]['id'] ?? null;
        $this->config['offset'] += count($entities);
        $this->job->updateCurrentTask($this->config);

        // Is the task finished?
        return (count($entities) < $this->job->limit);
    }


}
