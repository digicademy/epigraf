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

use App\Model\Table\BaseTable;
use App\Model\Table\SaveManyException;
use InvalidArgumentException;

/**
 * Autofill fields based on the types' configuration
 */
class TaskBatchAutofill extends BaseTaskMutate
{

    static public $caption = 'Autofill values';

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
                'value' => $this->job->config['params']['targetfield'] ?? 'sortkey',
                'data-form-update' => 'targetfield',
                'options' => [
                    'sortkey' => __('Sort key field'),
                    'name' => __('Name field')
                ]
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
        return $params;
    }

    /**
     * Process all entities using cursor-based pagination
     *
     * @param BaseTable $model
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        $modelAlias = $model->getAlias();

        if (($taskParams['cursor'] ?? 0) < 0) {
            throw new InvalidArgumentException('Invalid cursor for task');
        }
        $dataParams = $model->parseRequestParameters($dataParams);
        $dataParams['ancestors'] = false; // See below, we want to contain ancestors but not have them in the entity list
        $dataParams['articleCount'] = false;

        // Use cursor based pagination instead of offset
        if (($taskParams['cursor'] ?? 0) > 0) {
            $cursorConditions = [$modelAlias . '.id >' => $taskParams['cursor'] ?? 0];
        } else {
            $cursorConditions = ['1=1'];
        }

        $entities = $model
            ->find('hasParams', $dataParams)
            ->find('containAncestors')
            ->contain(['Types'])
            ->where($cursorConditions)
            ->orderAsc($modelAlias . '.id')
            ->limit($limit)
            ->toArray();

        $targetField = $taskParams['targetfield'] ?? 'norm_data';

        foreach ($entities as $entity) {
            $entity->autofill($targetField);
        }

        if (!$model->saveMany($entities, [])) {
            throw new SaveManyException('Could not save entities.');
        }

        return $entities;
    }

}
