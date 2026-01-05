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
            'coder' => ['index', 'view', 'select', 'choose', 'add', 'edit', 'delete', 'move', 'merge', 'lock', 'unlock', 'export'],
            'desktop' => ['index', 'view', 'select', 'choose', 'export'],
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
                'unlock',
                'export',
                'mutate'
//                'mutate' => ['task' => ['batch_sort']]
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
                'unlock',
                'export',
                'mutate'
//                'mutate' => ['task' => ['batch_sort']]
            ]
        ]
    ];

    public $help = 'introduction/properties';

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
     * This endpoint supports only POST requests containing
     * either a single move operation or a batch of move operations.
     *
     * ## Single move operation
     * The payload contains the following keys:
     * - reference_id The ID of a reference node
     * - reference_pos The position of the reference node: 'parent' or 'preceding'
     *
     * ### Batch move operations
     * The moves field contains an array of all moves.
     * Each move is an array with the following keys:
     * - id The ID of the property
     * - parent_id The ID of the new parent
     * - preceding_id The ID of the preceding sibling
     *
     * @param string $scope For single moves the property ID, for batch moves the property type
     * @return \Cake\Http\Response|null|void
     */
    public function move($scope)
    {
        // Check batch move preconditions
        if ($this->request->is(['post', 'put'])) {
            $moves = $this->request->getData('moves', null);
            if (!is_null($moves) && is_array($moves)) {
                /* Locks from EpiDesktop */
                if (empty($scope) | empty($this->activeDatabase->types['properties'][$scope])) {
                    throw new BadRequestException('No valid property type selected.');
                }
                if ($this->Lock->isDesktopLocked('properties', $scope)) {
                    $this->Answer->error(
                        __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
                    );
                }
            }
        }

        $this->Actions->move($scope);
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
     * @param string|null $scope The property type
     *
     * @return \Cake\Http\Response|null|void
     * @throws BadRequestException if no valid property type is provided
     */
    public function import($scope = null)
    {
        if (empty($scope)) {
            $scope = $this->request->getQuery('propertytype', null);
        }

         if (empty($scope) || empty($this->activeDatabase->types['properties'][$scope])) {
            throw new BadRequestException('No valid property type selected.');
        }

        /* Locks from EpiDesktop */
        if ($this->Lock->isDesktopLocked('properties', $scope)) {
            $this->Answer->error(
                __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
            );
        }

        $this->Transfer->import($scope);
    }


    /**
     * Export entities
     *
     * @param string $scope The property type
     * @return Response|void|null
     */
    public function export($scope = null)
    {
        if (empty($scope)) {
            $scope = $this->request->getQuery('propertytype', null);
        }

        if (empty($scope)) {
            throw new BadRequestException('Missing property type.');
        }

        return $this->Transfer->export($scope);
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
    public function mutate($scope = null)
    {
        if (empty($scope)) {
            $scope = $this->request->getQuery('propertytype', null);
        }

        if (empty($scope)) {
            throw new BadRequestException('Missing property type.');
        }

        /* Locks from EpiDesktop */
        if ($this->Lock->isDesktopLocked('properties', $scope)) {
            $this->Answer->error(
                __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
            );
        }

        return $this->Transfer->mutate($scope);
    }

    /**
     * Transfer properties between databases
     *
     * @param string $scope The property type. ALternatively, provide the value as query parameter 'propertytype'.
     * @return void
     * @throws BadRequestException If the scope is missing.
     */
    public function transfer($scope = null)
    {
        if (empty($scope)) {
            $scope = $this->request->getQuery('propertytype', null);
        }

        if (empty($scope)) {
            throw new BadRequestException('Missing property type.');
        }

        /* Locks from EpiDesktop */
        if ($this->Lock->isDesktopLocked('properties', $scope)) {
            $this->Answer->error(
                __('The table is open in EpiDesktop. Close or unlock in EpiDesktop.')
            );
        }

        $params = $this->request->getQueryParams();
        $this->Transfer->transfer($scope, $params);
    }

}
