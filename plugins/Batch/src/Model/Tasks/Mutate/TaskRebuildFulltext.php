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

use Epi\Model\Entity\Article;

/**
 * Build fulltext index
 */
class TaskRebuildFulltext extends BaseTaskMutate
{

    static public $caption = 'Rebuild fulltext index';

    public static $taskModels = ['Epi.Articles'];

    /**
     * Mutate entities: Rebuild fulltext index
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
        $dataParams['snippets'][] = 'search';
        $dataParams['snippets'][] = 'comments';
        $entities = $model
            ->find('hasParams', $dataParams)
            ->find('containAll', $dataParams)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        $items = [];
        foreach ($entities as $entity) {
            /** @var Article $entity */
            $items = array_merge($items, $entity->updateSearchItems());
        }
        $model->Items->saveMany($items);
        return $entities;
    }

}
