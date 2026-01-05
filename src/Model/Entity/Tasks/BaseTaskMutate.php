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

use App\Model\Entity\BaseEntity;
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

        if (!empty($this->config['cursor'])) {
            $params['cursor'] = $this->config['cursor'] ?? null;
        }

        return $params;
    }

    /**
     * Get the number of steps for this task
     *
     * @return int
     */
    public function progressMax()
    {
        $databankName = empty($this->config['database']) ? $this->job->config['database'] : $this->config['database'];
        $this->job->activateDatabank($databankName);

        return $this->job->batchCount;
    }

    /**
     * Reset the task progress
     *
     * @return true
     */
    public function init()
    {
        $this->config['offset'] = 0;
        return true;
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

            foreach ($entities as $entity) {
                if ($entity instanceof BaseEntity) {
                    $errors = $entity->getErrors();
                    $errors = $errors['mutate'] ?? [];
                    $errors = array_map(fn($error) => ['message' => $error], $errors);
                    $this->job->addTaskErrors($errors);
                }
            }
        } catch (Exception $e) {
            $msg = __('Error mutating entitites: {error}.', ['error' => $e->getMessage()]);
            $this->job->addTaskError($msg, $taskParams, $e);
            return true;
        }

        if (empty($entities)) {
            $this->config['cursor'] = -1;
        } else {
            $this->config['cursor'] = end($entities)['id'] ?? null;
        }
        $this->config['offset'] += count($entities);
        $this->job->updateCurrentTaskConfig($this->config);

        // Is the task finished?
        return (count($entities) < $this->job->limit);
    }


}
