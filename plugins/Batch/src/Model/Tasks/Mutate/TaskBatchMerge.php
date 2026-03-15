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
use InvalidArgumentException;

/**
 * Merge properties
 */
class TaskBatchMerge extends BaseTaskMutate
{
    static public $caption = 'Merge properties';

    public static $taskModels = ['Epi.Properties'];

    /**
     * Merge all properties with the same lemma path
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
            throw new InvalidArgumentException('Invalid cursor for merge task');
        }

        $dataParams = $model->parseRequestParameters($dataParams);

        // Use cursor based pagination instead of offset
        if (($taskParams['cursor'] ?? 0) > 0) {
            $cursorNode = $model->find('all', ['deleted'=>[0,1]])
                ->where(['id' => $taskParams['cursor']])
                ->firstOrFail();

//            $cursorNode = $this->get($taskParams['cursor']);

            $cursorConditions = [
                'OR' => [
                    ['Properties.level' => $cursorNode->level, 'Properties.id >' => $taskParams['cursor'] ?? 0],
                    'Properties.level >' => $cursorNode->level
                ]
            ];
        } else {
            $cursorConditions = ['1=1'];
        }

        $dataParams['articleCount'] = false;
        $dataParams['ancestors'] = false;
        $dataParams['treePositions'] = false;

        $entities = $model
            ->find('hasParams', $dataParams)
            ->where($cursorConditions)
            ->orderAsc('Properties.level')
            ->orderAsc('Properties.id')
            ->limit($limit)
            ->toArray();

        /** @var Property $entity */
        foreach ($entities as $entity) {

            // Skip already merged entities
            if (!$model->exists(['id' => $entity->id, 'deleted' => 0])) {
                continue;
            }

            $sourceEntities = $entity->duplicates;
            $sourceIds = $sourceEntities->all()->extract('id')->toArray();
            if (!empty($sourceIds)) {
                $model->merge($entity->id, $sourceIds, ['concat' => true]);
            }
        }

        return $entities;
    }

}
