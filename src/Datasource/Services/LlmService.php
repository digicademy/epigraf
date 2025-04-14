<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace App\Datasource\Services;

use App\Model\Entity\Databank;
use App\Model\Table\BaseTable;
use App\Utilities\Converters\Arrays;
use App\View\MarkdownView;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Client;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Epi\Model\Table\BaseTable as EpiBaseTable;

class LlmService extends BaseService
{

    public string $serviceKey = 'llm';

    public array $queryOptions = [
        'task',
        'database',
        'record',

        'input',
        'prompts',
        'multinomial',

        'itemtype',
        'propertytype',
        'sectiontypes',
        'tagname'
    ];

    public $client = null;
    public $oldDatabaseName = '';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->client->setConfig(
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . ($this->config['access_token'] ?? '')
                ]
            ]
        );

    }

    /**
     * Activate database
     *
     * @param $dbname
     * @return Databank
     */
    public function setDatabank($dbname) : Databank
    {
        $this->oldDatabaseName = EpiBaseTable::getDatabaseName();
        BaseTable::setDatabase($dbname);

        $databanks = TableRegistry::getTableLocator()->get('Databanks');
        return $databanks->activateDatabase($dbname);
    }

    public function resetDatabank()
    {
        if (!empty($this->oldDatabaseName)) {
            EpiBaseTable::setDatabase($this->oldDatabaseName);
        }
    }

    public function withDatabank($databaseName, $callback)
    {
        if (empty($databaseName)) {
            throw new RecordNotFoundException(__('Database name is missing.'));
        }

        $databank = $this->setDatabank($databaseName);
        try {
            return $callback($databank);
        } finally {
            $this->resetDatabank();
        }
    }

    public function withTable($databaseName, $modelName, $callback)
    {
        return $this->withDatabank($databaseName,
            function($databank) use ($modelName, $callback) {
                $table = TableRegistry::getTableLocator()->get($modelName);
                if (!$table instanceof \App\Model\Interfaces\ExportTableInterface) {
                    throw new RecordNotFoundException(__('Table does not support data export.'));
                }
                return $callback($databank, $table);
            }
        );
    }

    /**
     * Get the configured property type for a given annotation (=links) or item (=items) type
     *
     * ### Options
     * - tagname (string): The annotation type
     * - itemtype (string): The item type
     *
     * @param array $options
     * @return string
     */
    public function getPropertyType($options)
    {
        return $this->withDatabank(
            $options['database'] ?? '',
            function($databank) use ($options) {
                if (!empty($options['tagname'])) {
                    return $databank->types['links'][$options['tagname']]['merged']['fields']['to']['targets']['properties'][0] ?? '';
                } elseif (!empty($options['itemtype'])) {
                    return $databank->types['items'][$options['itemtype']]['merged']['fields']['property']['types'] ?? '';
                }
                throw new RecordNotFoundException(__('Annotation type is missing.'));
            }
        );
    }

    /**
     * Get properties for coding and annotation tasks
     *
     * TODO: Think about how to make it dry with BaseTaskData
     *
     * The description value will be combined from the path and the comment field.
     * The example value will be the content field.
     * The category value will be the norm_iri field.
     *
     * ### Options
     * propertytype (string): The property type to construct a codebook or rulebook,
     *                        prefixed with the database name and a dot
     *                        (e.g. playground.topics).
     *
     * @param array $options
     * @return array
     */
    public function getProperties($options) {

        $propertyType = $options['propertytype'] ?? '';
        $properties = $this->withTable($options['database'] ?? '', 'Epi.Properties',
            function($databank, $table) use ($propertyType) {
                return $table->getExportData(['scope' => $propertyType]);
            }
        );

        $codebook = [];
        foreach ($properties as $row) {

            $description = $row['path'];
            if (!empty($row['comment'])) {
                $description .= ': ' . $row['comment'];
            }

            if (empty($row['norm_iri']) || empty($row['path'])) {
                continue;
            }

            $rule = [
                'category' => $row['norm_iri'],
                'description' => $description
            ];
            if (!empty($row['content'])) {
                $rule['example'] = $row['content'];
            }
            $codebook[] = $rule;
        }

        if (empty($codebook)) {
            throw new RecordNotFoundException(__('Codebook is empty (category=norm_iri, description=lemma+comment and example=content are required).'));
        }

        return $codebook;
    }

    /**
     * Get the rendered article content
     *
     * TODO: Think about how to make it dry with BaseTaskData
     *
     * ### Options
     * record (string): A table id in the format database.table.id
     *
     * @param array $options
     * @return string
     */
    public function getInputText($options) {

        $databaseName = $options['database'] ?? '';
        if (empty($databaseName)) {
            throw new RecordNotFoundException(__('Database name is missing.'));
        }

        $id = explode('-', $options['record'] ?? '');
        $modelName = 'Epi.' . Inflector::camelize($id[0]);

        $rendered = $this->withTable(
            $databaseName, $modelName,
            function($databank, $table) use ($id, $options) {
                $conditions = ['id' => $id[1]];
                if (!empty($options['sectiontypes'])) {
                    $conditions['sectiontypes'] = $options['sectiontypes'];
                }

                $rows = $table->getExportData($conditions);
                if (empty($rows)) {
                    throw new RecordNotFoundException(__('Record not found'));
                }

                $view = new MarkdownView();
                $view->set('database', $databank);
                return $view->renderDocument($rows, ['format' => 'md']);
            }
        );
        return $rendered;
    }

    public function preProcess($options) {
        if (!empty($options['record'])) {
           $options['input'] = $this->getInputText($options);
        }

        $taskOptions = [];
        if (!empty($options['prompts'])) {
            $taskOptions['prompts'] = $options['prompts'];
        }

        if (!empty($options['tagname']) || !empty($options['itemtype'])) {
            $options['propertytype'] = $this->getPropertyType($options);
        }

        if (!empty($options['propertytype'])) {
            $taskOptions['rules'] = $this->getProperties($options);
        }

        if (!empty($options['multinomial'])) {
            $taskOptions['multinomial'] = true;
        }

        $data = [
            'task' => $options['task'] ?? '',
            'input' => $options['input'] ?? '',
            'options' => $taskOptions
        ];
        return $data;
    }

    public function postProcess($response, $options)
    {
        if ($response['state'] !== 'SUCCESS') {
            return $response;
        }

        $task = $options['task'] ?? '';

        // Map LLM annotations to HTML span tags
        if (($task === 'annotate') && !empty($options['tagname']) ) {

            $tagName = $options['tagname'];
            $xmlText = $response['result']['answers'][0]['llm_result'] ?? '';

            $xmlText = $this->withTable(
                $options['database'] ?? '',
                'Epi.Properties',
                function($databank, $table) use ($tagName, $xmlText) {
                    $propertyType = $databank->types['links'][$tagName]['merged']['fields']['to']['targets']['properties'][0] ?? '';
                    if (!empty($propertyType)) {
                        return $table->renderAnnotations($xmlText, $tagName, $propertyType);

                    }
                    return [];
                }
            );

            $response['result']['answers'][0]['llm_result'] = $xmlText;
        }

        if (($task === 'coding') && !empty($options['itemtype']) ) {
            $typeName = $options['itemtype'];
            $multinomial = $options['multinomial'] ?? false;
            $answers = $response['result']['answers'] ?? [];

            $response['result']['answers'] = $this->withTable(
                $options['database'] ?? '',
                'Epi.Properties',
                function($databank, $table) use ($typeName, $answers, $multinomial) {
                    $propertyType = $databank->types['items'][$typeName]['merged']['fields']['property']['types'] ?? '';
                    $items = [];
                    if (!empty($propertyType)) {
                        $properties = $table->getExportData(['scope' => $propertyType]);
                        $properties = Arrays::array_group($properties,'norm_iri', true);

                        foreach ($answers as $no => $answer) {
                            if (!empty($multinomial)) {
                                foreach($properties as $property) {
                                    if (!empty($answer['llm_result_' . $property['norm_iri']])) {
                                        $items[] = [
                                            'properties_id' => $property['id'] ?? '',
                                            'properties_label' => $property['path'] ?? '',
                                            'value' => $answer['llm_result_' . $property['norm_iri']]
                                        ];
                                    }
                                }
                            } else {
                                $property = $properties[$answer['llm_result'] ?? ''] ?? [];
                                $items[] = [
                                    'properties_id' => $property['id'] ?? '',
                                    'properties_label' => $property['path'] ?? ''
                                ];
                            }
                        }
                    }
                    return $items;
                }
            );
        }

        return $response;
    }

    /**
     * Query the LLM service
     *
     * When generating a new task, a task_id is returned.
     * Provide the task_id in the following calls as $path parameter to get the status and result of the task.
     *
     * ### Options
     * - input (string): The input data for the task when generating a new task.
     * - task (string): The type of the task to be executed (summarize, coding, triples).
     *
     * ### Result
     * An array with the following keys:
     * - task_id (string): The task id, used to query the task status and result in subsequent calls
     * - state (string): The state of the task (PENDING, SUCCESS, FAILURE, ERROR)
     * - result (array): The result of the task
     *   - answers (array): The answers of the LLM,
     *     with the text in llm_result
     *     and the number of processed tokens in llm_tokens
     *
     * @param string|null $path The id of a running task or null for new tasks
     * @param array $options The parameters for the task
     * @param array $wait Seconds to wait for instant completion
     * @return array The result of the task or empty if an error occurred
     */
    public function query($path = null, array $options = [], $wait = 0): array
    {
        $options = $this->sanitizeParameters($options);
        $baseUrl = $this->config['base_url'] ?? null;

        if (empty($baseUrl)) {
            return ['state' => 'ERROR','message' =>'Service URL is missing.'];
        }

        // Get the task status and result
        try {
            if (!empty($path)) {
                $response = $this->client->get($baseUrl . 'tasks/run/' . $path);
            }
            else {
                $data = $this->preProcess($options);
                $response = $this->client->post(
                    $baseUrl . 'tasks/run?wait=' . $wait, json_encode($data),
                    ['type' => 'json']);
            }

            if ($response->isOk()) {
                return $this->postProcess($response->getJson(), $options);
            } else {
                throw new BadRequestException('Service returned an error (' . $response->getStatusCode(). '): ' . $response->getReasonPhrase());
            }
        } catch (\Exception $e) {
            return ['state' => 'ERROR', 'message' => $e->getMessage()];
        }
    }

    /**
     * Await the result of a query
     *
     * @param array $options Options passed to the query() method
     * @param int $timeout The timeout in seconds
     * @return array The task result, see the query() method
     */
    public function awaitQuery(array $options = [], int $timeout = 30): array
    {
        $state = $this->query(null, $options);
        $taskId = $state['task_id'] ?? null;

        while (!empty($taskId) && ($state['state'] === 'PENDING')) {
            sleep(1);
            $state = $this->query($taskId, $options);
            $timeout--;
            if ($timeout <= 0) {
                break;
            }
        }

        return $state;
    }

    /**
     * Submit multiple queries and await the results
     *
     * @param array $tasks A task array
     * @param int $timeout
     * @return array
     */
    public function awaitQueries(array $tasks, int $timeout = 30) : array {
        // Submit tasks
        foreach ($tasks as $idx => $task) {
            $tasks[$idx] = $this->query(null, $task, 1);
        }

        // Wait for tasks to finish
        $timeout++;
        do {
            $finished = 0;
            $timeout--;
            foreach ($tasks as $idx => $task) {
                if (($task['state'] ?? '') !== 'PENDING') {
                    $finished++;
                }
                else {
                    $task = $this->query($task['task_id'] ?? null, $task);
                    if (($task['state'] ?? '') !== 'PENDING') {
                        $finished++;
                    }
                    $tasks[$idx] = $task;
                }
            }

            if ($finished < count($tasks) && ($timeout > 0)) {
                sleep(1);
            }
        } while ($finished < count($tasks) && ($timeout > 0));

        return $tasks;
    }

}
