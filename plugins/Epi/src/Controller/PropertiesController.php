<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Controller;

use Cake\Event\EventInterface;
use Epi\Controller\Component\TransferComponent;
use Rest\Controller\Component\LockTrait;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use App\Utilities\Converters\Attributes;
use Cake\Http\Response;

/**
 * Articles Controller
 *
 * Provide access to Epigraf3 articles of type 'objekt'
 *
 * @property \Epi\Model\Table\PropertiesTable $Properties
 * @property TransferComponent $Transfer
 */
class PropertiesController extends AppController
{

    /**
     * The lock trait provides lock and unlock actions
     */
    use LockTrait;

    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'api' => [
            'coder' => ['lock', 'unlock'],
            'author' => ['lock', 'unlock'],
            'editor' => ['lock', 'unlock']
        ],
        'web' => [
            'guest' => ['index', 'view'],
            'reader' => ['index', 'view', 'select', 'choose'],
            'coder' => [
                'index',
                'view',
                'select',
                'choose',
                'add',
                'edit',
                'delete',
                'move',
                'merge',
                'lock',
                'unlock'
            ],
            'desktop' => ['index', 'view', 'select', 'choose'],
            'author' => [
                'index',
                'view',
                'select',
                'choose',
                'add',
                'edit',
                'delete',
                'move',
                'merge',
                'lock',
                'unlock'
            ],
            'editor' => [
                'index',
                'view',
                'select',
                'choose',
                'add',
                'edit',
                'delete',
                'mutate',
                'move',
                'merge',
                'lock',
                'unlock'
            ]
        ]
    ];

    /**
     * Pagination setup
     *
     * @var array
     */
    public $paginate = [
        'className' => 'Total',
        'order' => ['Properties.lft' => 'asc'],
        'limit' => 100,
    ];

    /**
     * beforeFilter callback
     *
     * Load transfer component.
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->loadComponent('Epi.Transfer', ['model' => 'Epi.Properties']);
    }

    /**
     * Retrieve a list of properties
     *
     * // TODO: implement meta query parameter to filter by meta properties
     * // TODO: implement keywords query parameter to filter by keywords
     *
     * @param string|null $scope The property type
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function index($scope = '')
    {
        $this->Actions->index($scope, ['scopemenu' => 'propertytype']);
    }

    /**
     * View a property
     *
     * @param string $id Entity ID
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function view($id)
    {
        $this->Actions->view($id);
    }

    /**
     * Edit a property
     *
     * @param string $id Entity ID
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function edit($id)
    {
        $this->Actions->edit($id);
    }

    /**
     * Add a new property
     *
     * @param string $scope The property type
     * @return void
     */
    public function add($scope)
    {
        if (empty($scope) || empty($this->activeDatabase->types['properties'][$scope])) {
            throw new BadRequestException('No valid property type selected.');
        }

        // Default position
        $default = [
            'reference_pos' => $this->request->getQuery('position', 'after'),
            'reference_id' => $this->request->getQuery('reference')
        ];

        $this->Actions->add($scope, $default);
    }

    /**
     * Delete a property
     *
     * @param string $id Entity id
     * @return void redirects to index
     * @throws RecordNotFoundException If record not found
     */
    public function delete($id)
    {
        $this->Actions->delete($id);
    }

    /**
     * Move properties to a new position
     *
     * This endpoint supports only POST requests containing a moves field in the payload.
     * The moves field contains an array of all moves.
     * Each move is an array with the following keys:
     *
     * - id The ID of the property
     * - parent_id The ID of the new parent
     * - preceding_id The ID of the preceding sibling
     *
     * @param string $id For single moves the property ID, for batch moves the property type
     * @return \Cake\Http\Response|null|void
     */
    public function move($id)
    {
        $batchMove = false;
        if ($this->request->is(['post', 'put'])) {
            $moves = $this->request->getData('moves', null);
            $batchMove = !is_null($moves) && is_array($moves);
        }

        // Move a single entity
        if (!$batchMove) {
            $entity = $this->Properties->get($id, ['finder' => 'containAll']);

            if ($this->request->is(['post', 'put'])) {

                $entity = $this->Properties->patchEntity(
                    $entity, $this->request->getData(),
                    ['fields' => ['reference_id', 'reference_pos']]
                );

                $this->Lock->createLock($entity, true);
                $success = $this->Properties->moveToReference($entity);

                if ($success) {
                    $this->Lock->releaseLock($entity);
                    $entity['moved'] = '1';
                    $this->Answer->success(
                        __('The property has been moved.')
                    );
                }
                else {
                    $this->Answer->error(
                        __('The property could not be moved. Please try again.')
                    );
                }
            }
            $this->Answer->addAnswer(compact('entity'));
        }

        // Process move operations
        else {
            /* Locks from EpiDesktop */
            $scope =$id;
            if (empty($scope) | empty($this->activeDatabase->types['properties'][$scope])) {
                throw new BadRequestException('No valid property type selected.');
            }
            if ($this->Lock->isDesktopLocked('properties', $scope)) {
                $this->Answer->error(
                    __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
                );
            }

            $errors = [];
            foreach ($moves as $move) {

                // TODO: lock the items for other move operations
                $success = $this->Properties->moveTo(
                    $move['id'] ?? null,
                    $move['parent_id'] ?? null,
                    $move['preceding_id'] ?? null
                );

                if (!$success) {
                    $errors[] = __('Could not move property #{0} to the new target.', $move['id'] ?? null);
                }
            }

            if (!empty($errors)) {
                $this->Answer->addAnswer(['errors' => $errors]);
                $this->Answer->error(
                    __('{0} of {1} properties could not be moved to their new target.', count($errors), count($moves))
                );
            }
            else {
                $this->Answer->success(
                    __('Moved {0} properties to new targets.', count($moves))
                );
            }
        }
    }

    /**
     * Merge two properties
     *
     * Supported query parameters:
     * - source
     * - target
     * - concat
     * - preview
     *
     * @param integer $propertySourceIds A comma separated list of property IDs that will be deleted afterwards.
     *                                   Alternatively, provide the value as query parameter 'source'.
     * @param integer $propertyTargetId Property ID of the target property that will be used instead.
     *                                  Alternatively, provide the value as query parameter 'target'.
     *
     * @return \Cake\Http\Response|void|null
     * @throws \Rest\Error\Middleware\RestAnswerException
     */
    public function merge($propertySourceIds = null, $propertyTargetId = null)
    {
        $propertyTargetId = (int)$this->request->getQuery('target', $propertyTargetId);
        $propertySourceIds = Attributes::commaListToIntegerArray($this->request->getQuery('source', $propertySourceIds));

        $mergeOptions = ['concat' => $this->request->getQuery('concat',false)];

        if (empty($propertySourceIds)) {
            throw new BadRequestException(__('Missing source properties.'));
        }

        /* Locks from EpiDesktop */
//        if ($this->Lock->isDesktopLocked('properties', $propertytype)) {
//            $this->Answer->error(
//                __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
//            );
//        }

        // Perform the merge
        // TODO: Lock table
        if ($this->request->is('post') && !empty($propertyTargetId)) {

            // The merge function returns the merged entity if concat was set to true
            // Otherwise it returns null
            // In case the merge did not succeed, the function throws an exception
            $entity = $this->Properties->merge($propertyTargetId, $propertySourceIds, $mergeOptions);
            $this->Answer->success(__('The properties have been merged.'));
            $this->Answer->addAnswer(compact( 'entity'));
        }

        // Preview the merge
        else {
            $propertySources = $this->Properties
                ->find('containAncestors')
                ->where(['Properties.id IN' => $propertySourceIds]);

            if (!$propertySources->count()) {
                throw new BadRequestException(__('Missing source properties.'));
            }

            $this->Answer->addAnswer(compact('propertySources'));

            if (!empty($propertyTargetId) && !empty($propertySourceIds)) {
                 $propertyTarget = $this->Properties->get($propertyTargetId, ['finder' => 'containAncestors']);
//                 $this->Lock->getLock($propertyTarget, true);
                 $this->Answer->addAnswer(compact( 'propertyTarget'));

                $mergeOptions['preview'] = true;
                $entity = $this->Properties->merge($propertyTargetId, $propertySourceIds, $mergeOptions);
                $this->Answer->addAnswer(compact( 'entity'));
            }
        }
    }

    /**
     * Import properties
     *
     * @param string|null $propertytype property type
     *
     * @return \Cake\Http\Response|null|void
     * @throws BadRequestException if no valid property type is provided
     */
    public function import($propertytype = null)
    {
        if (empty($propertytype) || empty($this->activeDatabase->types['properties'][$propertytype])) {
            throw new BadRequestException('No valid property type selected.');
        }

        /* Locks from EpiDesktop */
        if ($this->Lock->isDesktopLocked('properties', $propertytype)) {
            $this->Answer->error(
                __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
            );
        }

        $this->Transfer->import('properties', $propertytype);
    }

    /**
     * Manipulate properties
     *
     * Supported query parameters:
     * - task The task. Example: sort
     * - selection Whether to mutate entities selected by IDs (selected)
     *             or all entities matching the filter criteria (filtered).
     * - config[params][sortby] The field used for sorting in the sort task (e.g. sortno)
     *
     *
     * @param string $scope The property type
     * @return Response|void|null
     */
    public function mutate($scope)
    {
        if (empty($scope)) {
            throw new BadRequestException('Missing property type.');
        }

        /* Locks from EpiDesktop */
        if ($this->Lock->isDesktopLocked('properties', $scope)) {
            $this->Answer->error(
                __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
            );
        }

        return $this->Transfer->mutate('properties', $scope);
    }

    /**
     * Transfer properties between databases
     *
     * @param string $scope The property type
     * @return void
     * @throws BadRequestException I the scioe is missing.
     */
    public function transfer($scope)
    {
        if (empty($scope)) {
            throw new BadRequestException('Missing property type.');
        }

        /* Locks from EpiDesktop */
        if ($this->Lock->isDesktopLocked('properties', $scope)) {
            $this->Answer->error(
                __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
            );
        }

//        [$params, $columns, $paging, $filter] = $this->Actions->prepareParameters();
//        unset($params['propertytype']);
        $params = $this->request->getQueryParams();
        $this->Transfer->transfer('properties', $scope, $params);
    }

}
