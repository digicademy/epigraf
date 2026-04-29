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

use App\Datasource\Services\ServiceFactory;
use Epi\Model\Entity\Article;
use InvalidArgumentException;

/**
 * Annotate items using LLM services configured in the item types.
 */
class TaskBatchAnnotate extends BaseTaskMutate
{

    static public $caption = 'Annotate items (LLM)';

    public static $taskModels = ['Epi.Articles'];

    protected $taskParameters = [
        'itemtype' => 'Item type',
        'servicekey' => 'Service key'
    ];

    /**
     * Mutate entities: Process the entities through external services configured in the item types
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        $itemType = $taskParams['itemtype'] ?? '';
        $serviceKey = $taskParams['servicekey'] ?? '';

        if (empty($itemType)) {
            throw new InvalidArgumentException(__('Item types are missing.'));
        }

        if (empty($serviceKey)) {
            throw new InvalidArgumentException(__('Service key is missing'));
        }

        $articles = $model->getExportData($dataParams, ['limit' => $limit, 'offset' => $offset]);

        // Get config
        $taskParams = $model->getDatabase()->types['items'][$itemType]['merged']['services'][$serviceKey] ?? [];
        if (empty($taskParams)) {
            throw new InvalidArgumentException(__('Service configuration not found.'));
        }

        $targetTag = $taskParams['target']['tagname'];

        // Submit tasks
        /** @var Article $entity */
        $tasks = [];
        $entities = [];
        // Create task
        $taskTemplate = [
            'task' => 'annotate',
            'database' => $model->getDatabaseName(),
            'tagname' => $targetTag
        ];

        if (!empty($taskParams['prompts'])) {
            $taskTemplate['prompts'] = $taskParams['prompts'];
        }

        foreach ($articles as $idx => $rootEntity) {

            foreach ($rootEntity->items as $itemEntity) {

                if ($itemEntity['itemtype'] !== $itemType) {
                    continue;
                }

                if ($itemEntity['content'] === '') {
                    continue;
                }

                $entityData = [
                    'id' =>  'items-' . $itemEntity['id'],
                    'root_id' => 'articles-' . $itemEntity['articles_id'],
                    'content' => $itemEntity['content']
                ];

                $tasks[$itemEntity->id] = $taskTemplate + ['input' => $itemEntity['content']];
                $entities[$itemEntity->id] = $entityData;
            }
        }

        $apiService = ServiceFactory::create('llm');
        $tasks = $apiService->awaitQueries($tasks, 120);

        // Save result
        foreach ($tasks as $entityId => $task) {

            $entityData = $entities[$entityId] ?? [];
            if (empty($entityData)) {
                continue;
            }

            // TODO: store error / state somewhere
            if (($task['state'] ?? 'ERROR') !== 'SUCCESS') {
                $error = 'Task not completed (' . ($task['state'] ?? '') . ')';
                $error .= !empty($task['message']) ? (': ' . $task['message']) : '';
                //$result = $error;
                // $article->setError('mutate', $error);
            }
            else {
                $entityData['content'] = $task['result']['answers'][0]['llm_result'] ?? '';
                $entities[$entityId] = $entityData;

                foreach ($task['result']['answers'][0]['llm_links'] ?? [] as $linkData) {
                    $linkData['id'] = 'links-tmp' . $linkData['from_tagid'];
                    $linkData['root_id'] = $entityData['root_id'];
                    $linkData['from_id'] = $entityData['id'];
                    $linkData['from_field'] = 'content';
                    $entities[$linkData['id']] = $linkData;
                }
            }
        }

        $entities = $model->toEntities($entities);
        $result = $model->saveEntities($entities);

        return $articles;
    }

}
