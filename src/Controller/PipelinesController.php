<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Controller;

use App\Utilities\Converters\Attributes;
use Cake\Http\Exception\NotFoundException;

/**
 * Pipelines Controller
 *
 * Provides administration export pipelines
 *
 * @property \App\Model\Table\PipelinesTable $Pipelines
 */
class PipelinesController extends AppController
{

    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'api' => [
            'editor' => ['lock', 'unlock']
        ],
        'web' => [
            'reader' => [],
            'coder' => [],
            'desktop' => [],
            'author' => [],
            'editor' => ['*']
        ]
    ];

    public $help = 'export/pipelines';

    /**
     * Retrieve list of pipelines.
     *
     * @return void
     */
    public function index()
    {
        [$params, $columns, $paging, $filter] = $this->Actions->prepareParameters();

        $entities = $this->Pipelines->find('hasParams', $params);
        $this->paginate = $paging;
        $entities = $this->paginate($entities);

        $this->Answer->addOptions(compact('params', 'columns'));
        $this->Answer->addAnswer(compact('entities'));
    }

    /**
     * Show a pipeline
     *
     * @param string $id pipeline id
     * @return void
     */
    public function view($id)
    {
        $entity = $this->Pipelines->get($id, ['contain' => []]);
        $this->sidemenu = $entity->getMenu();
        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Add a new pipeline.
     *
     * @return \Cake\Http\Response|void|null redirects on successful add, renders view otherwise
     */
    public function add()
    {
        $entity = $this->Pipelines->newEntity([]);
        if ($this->request->is('post')) {

            //Save data
            $entity = $this->Pipelines->patchEntity($entity, $this->request->getData());
            if ($this->Pipelines->save($entity)) {
                $this->Answer->success(
                    __('The pipeline has been saved.'),
                    ['action' => 'edit', $entity->id]
                );
            }
            else {
                $this->Answer->error(__('The pipeline could not be saved. Please, try again.'));
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Edit a pipeline
     *
     * @param string $id Pipeline ID
     *
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException If record not found
     */
    public function edit($id)
    {
        $entity = $this->Pipelines->get($id, ['contain' => []]);

        if ($this->request->is(['patch', 'post', 'put'])) {

            //Reorder tasks
            //TODO: move to model
            $tasks = $this->request->getData('Tasks');
            if (is_array($tasks)) {
                usort($tasks, function ($a, $b) {
                    return ((int)$a['number'] - (int)$b['number']);
                });
            }

            //TODO: move to model
            $options = $this->request->getData('Tasks.0.options');
            if (is_array($options)) {
                usort($options, function ($a, $b) {
                    return ($a['number'] - $b['number']);
                });
                $tasks[0]['options'] = $options;
            }

            //Save data
            $this->request = $this->request->withData('tasks', $tasks);
            $entity = $this->Pipelines->patchEntity($entity, $this->request->getData());

            if ($this->Pipelines->save($entity)) {
                $this->Answer->success(__('The pipeline has been saved.'), ['action' => 'view', $entity->id]);
            }
            else {
                $this->Answer->error(__('The pipeline could not be saved. Please, try again.'));
            }
        }

        $entity->arrangeTasks();

        $this->sidemenu = $entity->getMenu();
        $this->sidemenu['move'] = true;
        $this->sidemenu['edit'] = true;
        $this->sidemenu['add'] = '/pipelines/add_task/' . $entity->id;
        $this->sidemenu['delete'] = true;

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Delete a pipeline
     *
     * @param string $id Pipeline ID
     * @return void
     */
    public function delete($id)
    {
        $entity = $this->Pipelines->get($id);

        if ($this->request->is(['delete'])) {
            if ($this->Pipelines->delete($entity)) {
                $this->Answer->success(__('The pipeline has been deleted.'));
            }
            else {
                $this->Answer->error(__('The pipeline could not be deleted. Please, try again.'));
            }
        }

        $this->Answer->addAnswer(compact('entity'));
    }

    /**
     * Add a new element to the current pipeline
     *
     * @param string $pipelineId Pipeline ID
     * @param string|null $type Element type
     * @return void
     */
    public function addTask($pipelineId, $type = null)
    {
        $entity = $this->Pipelines->get($pipelineId, ['contain' => []]);

        if ($type && in_array($type, array_keys($entity->tasksConfig))) {
            $number = Attributes::uuid('new');
            $entity->tasks = [['type' => $type, 'number' => $number]];
        }
        else {
            $entity->tasks = [];
        }

        $this->Answer->addAnswer(compact('entity'));
    }


}
