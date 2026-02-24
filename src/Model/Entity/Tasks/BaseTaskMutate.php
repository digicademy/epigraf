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
use App\Model\Table\BaseTable;
use Cake\Http\Exception\InternalErrorException;
use Exception;

/**
 * Basic task to mutate articles or other entitites
 */
class BaseTaskMutate extends BaseTask
{

    /**
     * A list of models that can be used with the task.
     * (e.g. Epi.Articles)
     *
     * TODO: Implement task registry
     *
     * @var array
     */
    public static $taskModels = [];

    /**
     * A list of URL parameters managed by the task.
     *
     * @var array
     */
    protected $taskParameters = [];

    /**
     * Get options for the configuration form
     *
     * Overwrite in derived classes
     *
     * TODO: The derived classes mainly recreate the fields.
     *       Rename $field to $values and pass the values to the fields.
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {
        return $fields;
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

        foreach ($this->taskParameters as $paramName) {
            $params[$paramName] = $this->job->config['params'][$paramName] ?? null;
        }

        return $params;
    }

    /**
     * Get data query parameters that are passed to the mutateEntities method
     *
     * Filter out task params from the data params
     *
     * @return array
     */
    public function getDataParams()
    {
        $jobParams = parent::getDataParams();
        $taskParams = $this->getTaskParams();

        return array_diff_key($jobParams, $taskParams);
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
     * @return array The updated task config
     */
    public function reset()
    {
        $this->config['offset'] = 0;
        return $this->config;
    }

    /**
     * Execute task
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $this->job->activateDatabank($this->job->config['database']);

        /** @var \Epi\Model\Table\BaseTable $model */
        $model = $this->job->getModel($this->job->config['table'], 'Epi');
        if (!($model instanceof MutateTableInterface)) {
            throw new InternalErrorException('The model does not support entity mutation.');
        }

        $this->config['offset'] = $this->config['offset'] ?? 0;
        $limit = $this->job->limit;
        $offset = $this->config['offset'];

        // TODO: keep sort/order parameter
        $taskParams = $this->getTaskParams();
        $dataParams = $this->getDataParams();

        try {
            $entities = $this->mutate($model, $taskParams, $dataParams, $offset, $limit);

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

    /**
     * Mutate entities.
     *
     * Overwrite in derived classes if the respective mutate method is not implemented in the model.
     *
     * @param BaseTable $model
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function mutate($model, $taskParams, $dataParams, $offset, $limit)
    {
        return $model->mutateEntities(
            $taskParams,
            $dataParams,
            $offset,
            $limit
        );
    }


}
