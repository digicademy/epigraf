<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Batch\Model\Tasks\Mutate;

use App\Model\Table\SaveManyException;
use Epi\Model\Entity\Property;
use InvalidArgumentException;

/**
 * Reconcile norm data and geo data
 */
class TaskBatchReconcile extends BaseTaskMutate
{

    static public $caption = 'Reconcile properties';

    public static $taskModels = ['Epi.Properties'];

    /**
     * Get options for the configuration form
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {

        $fields = [];

        $fields['config.params.targetfield'] =
            [
                'caption' => __('Target Field'),
                'type' => 'select',
                'empty' => false,
                'value' => $this->job->config['params']['targetfield'] ?? 'norm_data',
                'data-form-update' => 'targetfield',
                'options' => [
                    'norm_data' => __('Norm data field'),
                    'content' => __('Content field'),
                ]
            ];

        $fields['config.params.onlyempty'] =
            [
                'caption' => __('Only empty fields'),
                'type' => 'checkbox',
                'data-form-update' => 'onlyempty',
                'value' => '1',
                'checked' => $this->job->config['params']['onlyempty'] ?? false,
            ];

        return $fields;
    }

    /**
     * Get parameters that are passed to the mutate method
     *
     * @return array
     */
    public function getTaskParams()
    {
        $params = parent::getTaskParams();
        $params['targetfield'] = $this->job->config['params']['targetfield'] ?? null;

        $params['onlyempty'] = $this->job->config['params']['onlyempty'] ?? false;
        return $params;
    }

    /**
     * Reconcile all properties using external services configured in the property type
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        if (($taskParams['cursor'] ?? 0) < 0) {
            throw new InvalidArgumentException('Invalid cursor for task');
        }
        $dataParams = $model->parseRequestParameters($dataParams);
        $dataParams['ancestors'] = false;
        $dataParams['treePositions'] = false;

        // Use cursor based pagination instead of offset
        if (($taskParams['cursor'] ?? 0) > 0) {
            $cursorConditions = ['Properties.id >' => $taskParams['cursor'] ?? 0];
        } else {
            $cursorConditions = ['1=1'];
        }

        $entities = $model
            ->find('hasParams', $dataParams)
            ->contain(['Types'])
            ->where($cursorConditions)
            ->orderAsc('Properties.id')
            ->limit($limit)
            ->toArray();

        $targetField = $taskParams['targetfield'] ?? 'norm_data';
        $reconcileOptions = ['onlyempty' => !empty($taskParams['onlyempty'])];

        /** @var Property $entity */
        foreach ($entities as $entity) {
            $entity->reconcile($targetField, $reconcileOptions);
        }

        if (!$model->saveMany($entities, [])) {
            throw new SaveManyException('Could save entities.');
        }

        return $entities;
    }

}
