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

use App\Model\Entity\BaseEntity;

/**
 * Sort tree
 */
class TaskRebuildTree extends BaseTaskMutate
{

    static public $caption = 'Recover section order';

    public static $taskModels = ['Epi.Articles'];

    /**
     * Mutate entities: Recover the section tree
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

        /** @var BaseEntity $entity */
        foreach ($entities as $entity) {
            $entity->recoverTree();
        }

        return $entities;
    }
}
