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
use App\Utilities\Converters\Attributes;
use App\View\MarkdownView;
use Cake\Utility\Hash;
use Epi\Model\Entity\Article;

/**
 * Summarize an article
 */
class TaskGenerateItems extends BaseTaskMutate
{

    static public $caption = 'Generate items from LLM output';

    public static $taskModels = ['Epi.Articles'];

    /**
     * Get options for the configuration form
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {

        $fields = [];

        $database = $this->job->activateDatabank($this->job->config['database']);
        $task = Attributes::cleanOption($this->job->config['params']['llmtask'] ?? 'summarize', ['summarize', 'coding', 'annotate'], 'summarize');
        $mode = Attributes::cleanOption($this->job->config['params']['mode'] ?? 'single', ['single', 'multi'], 'single');

        $fields['config.params.llmtask'] =
            [
                'caption' => __('Workflow'),
                'type' => 'select',
                'empty' => false,
                'options' => [
                    'summarize' => __('Summarize'),
                    'coding' => __('Coding'),
                    'annotate' => __('Annotate')
                ],
                'data-form-update' => 'llmtask',
                'default' => $task,
                'value' => $task
            ];

        if (in_array($task, ['summarize','coding'])) {
            $fields['config.params.mode'] =
                [
                    'caption' => __('Mode'),
                    'type' => 'select',
                    'empty' => false,
                    'options' => [
                        'single' => __('Single value output'),
                        'multi' => __('Multi value output'),
                    ],
                    'data-form-update' => 'mode',
                    'default' => $mode,
                    'value' => $mode
                ];
        }

        // Property types
        if (!(($task === 'summarize') && ($mode == 'single'))) {
            $propertyTypes = $database->types['properties'] ?? [];
            $propertyTypes = Hash::combine($propertyTypes, '{*}.name', '{*}.caption');
            $propertyType = $this->job->config['params']['propertytype'] ?? null;

            $fields['config.params.propertytype'] =
                [
                    'caption' => __('Rules'),
                    'type' => 'select',
                    'empty' => false,
                    'value' => $propertyType,
                    'options' => $propertyTypes,
                    'help' => __('The property type used to generate rules.'),
                ];
        }

        $fields['config.params.input'] = [
            'caption' => __('Input value'),
            'type' => 'textarea',
            'help' => __('Define the input using placeholder strings. Placeholder strings can contain extraction keys to get data from the selected article in curly brackets.'),
            'placeholder' => '{sections.*.items.*.content}',
            'value' => $this->job->config['params']['input'] ?? '{sections.*.items.*.content}',
        ];

        $fields['config.params.systemprompt'] = [
            'caption' => __('System prompt'),
            'type' => 'textarea',
            'help' => __('Leave empty to use the default prompt for the workflow. Alternatively, insert the system prompt. The placeholder {{text}} will be replaced by the input value. The placeholder {{rules}} will be replaced by the rules generated from the selected property type.'),
            'value' => $this->job->config['params']['systemprompt'] ?? '',
        ];

        $fields['config.params.userprompt'] = [
            'caption' => __('User prompt'),
            'type' => 'textarea',
            'help' => __('Leave empty to use the default prompt for the workflow. Alternatively, insert the system prompt. The placeholder {{text}} will be replaced by the input value. The placeholder {{rules}} will be replaced by the rules generated from the selected property type.'),
            'value' => $this->job->config['params']['userprompt'] ?? ''
        ];


        // Section and item types
        $sectionTypes = $database->types['sections'] ?? [];
        $sectionTypes = Hash::combine($sectionTypes, '{*}.name', '{*}.caption'); // , '{*}.category'

        $fields['config.params.sectiontype'] =
            [
                'caption' => __('Output section type'),
                'type' => 'select',
                'options' => $sectionTypes,
                'default' => $this->job->config['params']['sectiontype'] ?? 'summary',
                'value' => $this->job->config['params']['sectiontype'] ?? 'summary'
            ];


        $fields['config.params.sectionname'] = [
            'caption' => __('Output section name'),
            'type' => 'text',
            'help' => __('The section name in case a new section has to be generated.'),
            'default' => $this->job->config['params']['sectionname'] ?? __('Summary'),
            'value' => $this->job->config['params']['sectionname'] ?? __('Summary')

        ];

        $itemTypes = $database->types['items'] ?? [];
        $itemTypes = Hash::combine($itemTypes, '{*}.name', '{*}.caption'); // , '{*}.category'

        $fields['config.params.itemtype'] =
            [
                'caption' => __('Output item type'),
                'type' => 'select',
                'options' => $itemTypes,
                'default' => $this->job->config['params']['itemtype'] ?? 'summary',
                'value' => $this->job->config['params']['itemtype'] ?? 'summary'
            ];

        $fields['config.params.irifragment'] = [
            'caption' => __('Output item identifier'),
            'type' => 'text',
            'help' => __('IRI suffix of the new item. Existing items with this suffix will be updated.'),
            'default' => $this->job->config['params']['irifragment'] ?? 'summary',
            'value' => $this->job->config['params']['irifragment'] ?? 'summary'

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

        // LLM task
        $params['task'] = $this->job->config['params']['task'] ?? null;
        $params['mode'] = $this->job->config['params']['mode'] ?? null;

        // Rules
        $params['propertytype'] = $this->job->config['params']['propertytype'] ?? null;

        // Input
        $params['input'] = $this->job->config['params']['input'] ?? null;

        // Prompts
        $params['prompts'] = [];
        if (!empty($this->job->config['params']['systemprompt'])) {
            $params['prompts']['system'] = $this->job->config['params']['systemprompt'];
        }
        if (!empty($this->job->config['params']['userprompt'])) {
            $params['prompts']['user'] = $this->job->config['params']['userprompt'];
        }
        if (empty($params['prompts'])) {
            unset($params['prompts']);
        }

        // Output section
        $params['sectiontype'] = Attributes::nonEmptyOption($this->job->config['params']['sectiontype'] ?? null, 'summary');
        $params['sectionname'] = Attributes::nonEmptyOption($this->job->config['params']['sectionname'] ?? null, __('Summary'));

        // Output item
        $params['itemtype'] = Attributes::nonEmptyOption($this->job->config['params']['itemtype'] ?? null, 'summary');
        $params['irifragment'] = Attributes::nonEmptyOption($this->job->config['params']['irifragment'] ?? null,'summary');

        // Fields
        $params['resultfield'] = $this->job->config['params']['resultfield'] ?? 'content';
        $params['statefield'] = $this->job->config['params']['statefield'] ?? 'value';

        return $params;
    }

    /**
     * Mutate entities: Generate items
     *
     * TODO: refactor by implementing section and item creation in a separate patchItemValue() function
     *       with a callback to get the new field value
     *
     * ### Task params
     * - sectionname
     * - sectiontype
     * - itemtype
     * - resultfield
     * - statefield
     * - irifragment
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        // Prepare service
        $view = new MarkdownView();
        $view->set('database', $model->getDatabase());
        $apiService = ServiceFactory::create('llm');

        $articles = $model->getExportData($dataParams, ['limit' => $limit, 'offset' => $offset]);
        $entities = [];

        // Submit tasks
        /** @var Article $entity */
        $tasks = [];
        foreach ($articles as $idx => $entity) {

            // Get summary
            $sectionType = $taskParams['sectiontype'];
            $inputText = $view->renderDocument(
                [$entity],
                ['format' => 'md','ignore' => ['sections' => [$sectionType]]]
            );

            $task = [
                'input' => $inputText,
                'task' => 'summarize'
            ];
            if (!empty($taskParams['prompts'])) {
                $task['prompts'] = $taskParams['prompts'] ?? 'default';
            }
            $tasks[$entity->id] = $task;
        }
        $tasks = $apiService->awaitQueries($tasks, 60);

        // Save result
        /** @var Article $article */
        foreach ($articles as $idx => $entity) {
            $task = $tasks[$entity->id] ?? [];

            if (($task['state'] ?? 'ERROR') !== 'SUCCESS') {
                $error = 'Task not completed (' . ($task['state'] ?? '') . ')';
                $error .= !empty($task['message']) ? (': ' . $task['message']) : '';
                $result = $error;
                // $article->setError('mutate', $error);
            } else {
                $result = $task['result']['answers'][0]['llm_result'] ?? '';
            }

            $resultData =  [
                $taskParams['resultfield'] => $result,
                $taskParams['statefield'] => $task['state'] ?? 'ERROR'
            ];

            // Find property ID
            if (!empty($taskParams['propertytype'])) {
                $property = $model->Items->Properties->find('all')
                    ->select(['id'])
                    ->where([
                        'propertytype' => $taskParams['propertytype'],
                        'lemma' => trim($result)
                    ])
                    ->order(['lft' => 'ASC'])
                    ->first();
                if (!empty($property)) {
                    $resultData['properties_id'] = 'properties-' . $property->id;
                }
            }

            $newEntities = $entity->getItemPatch($taskParams, $resultData);
            $entities = array_merge($entities, $newEntities);
        }

        $entities = $model->toEntities($entities);
        $result = $model->saveEntities($entities);

        return $articles;
    }
}
