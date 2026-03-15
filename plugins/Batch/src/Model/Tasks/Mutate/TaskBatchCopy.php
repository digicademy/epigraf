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
 * Copy articles
 */
class TaskBatchCopy extends BaseTaskMutate
{

    static public $caption = 'Copy articles';

    public static $taskModels = ['Epi.Articles'];

    /**
     * Mutate entities: Create copies of the selected entities
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
        $entities = $model->getExportData($dataParams, ['offset' => $offset, 'limit' => $limit], null);

        foreach ($entities as $entity) {

            $transferOptions = ['copy' => true];
            $newData = $entity->getDataForTransfer($transferOptions);
            $newEntities = $model->toEntities($newData);

            $importConfig = [
                'tree' => true,
                'versions' => true,
                'timestamps' => true
            ];

            $result = $model->saveEntities($newEntities, $importConfig);
//            $this->addTaskErrors($importBehavior->getErrors());
        }

        return $entities;
    }

}
