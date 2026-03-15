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
 * Parse dates and update start, end and sort field
 *
 */
class TaskRebuildDates extends BaseTaskMutate
{
    static public $caption = 'Rebuild dates index';

    public static $taskModels = ['Epi.Articles'];

    /**
     * Mutate: Rebuild date fields
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

        // Retrieve relevant item types
        $dataParams['itemtypes'] = [];
        $itemTypes = $model->Items->Types->find('all');
        foreach ($itemTypes as $itemType) {
            if (!empty($itemType->config['fields']['date'] ?? false)) {
                $dataParams['itemtypes'][] = $itemType->name;
            }
        }

        $dataParams['itemtypes'] = array_unique($dataParams['itemtypes']);

        if (empty($dataParams['itemtypes'])) {
            return [];
        }

        $entities = $model
            ->find('hasParams', $dataParams)
            ->find('containColumns', $dataParams)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        $items = [];
        foreach ($entities as $entity) {
            foreach ($entity->items as $item) {
                /** @var \Epi\Model\Entity\Item $item */
                if ($item->type->merged['fields']['date'] ?? false) {
                    $item->updateDate();
                    $items[] = $item;
                }
            }
        }

        $model->Items->saveManyFast($items);
        return $entities;
    }

}
