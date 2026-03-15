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

/**
 * Delete articles
 */
class TaskBatchDelete extends BaseTaskMutate
{

    static public $caption = 'Delete articles';

    public static $taskModels = ['Epi.Articles'];

    /**
     * Mutate entities: Delete the selected entities
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        $dataParams = $model->parseRequestParameters($dataParams);
        $entities = $model
            ->find('hasParams', $dataParams)
            //->find('containAll', $dataParams)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        foreach ($entities as $entity) {
            $model->delete($entity);
        }

        return $entities;
    }

}
