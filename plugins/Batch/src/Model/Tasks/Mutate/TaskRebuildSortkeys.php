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

use Epi\Model\Entity\Property;

/**
 * Generate sort keys from lemma paths
 *
 */
class TaskRebuildSortkeys extends BaseTaskMutate
{

    static public $caption = 'Rebuild sort keys';
    static public $allowed = ['author' , 'editor'];

    public static $taskModels = ['Epi.Properties'];

    /**
     * Mutate: Rebuild sort keys
     *
     * TODO: The limit must be set in the job because it is passed through from there
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 100)
    {
        $dataParams = $model->parseRequestParameters($dataParams);
        $dataParams['ancestors'] = false;
        $dataParams['treePositions'] = false;

        $entities = $model
            ->find('hasParams', $dataParams)
            //->find('containAll', $dataParams)
            ->contain(['Types'])
            ->limit( $limit)
            ->offset($offset)
            ->toArray();

        foreach ($entities as $entity) {
            /** @var Property $entity */
            $entity->updateSortKey();
        }
        $model->saveMany($entities);

        return $entities;
    }
}
