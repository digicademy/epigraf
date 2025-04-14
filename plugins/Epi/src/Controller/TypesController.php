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

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Rest\Controller\Component\LockTrait;

/**
 * Types Controller
 *
 * Administration of notes
 *
 * @property \Epi\Controller\Component\TransferComponent $Transfer
 */
class TypesController extends AppController
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
        ],
        'web' => [
            'guest' => ['index', 'view'],
            'reader' => ['index', 'view'],
            'coder' => ['index', 'view'],
            'desktop' => ['index', 'view'],
            'author' => ['index', 'view'],
            'editor' => ['index', 'view']
        ]
    ];

    /**
     * Pagination setup
     *
     * @var array
     */
    public $paginate = [
        'className' => 'Total',
        'order' => [
            'Types.category' => 'asc',
            'Types.scopenumber' => 'asc',
            'Types.sortno' => 'asc',
            'Types.name' => 'asc'
        ],
        'limit' => 100
    ];

    /**
     * beforeFilter callback
     *
     * Init menu
     *
     * @param \Cake\Event\Event $event Event
     *
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->loadComponent('Epi.Transfer', ['model' => 'Epi.Types']);

        $this->_activateMainMenuItem([
            'plugin' => $this->activeDatabase->plugin,
            'controller' => 'Types',
            'action' => 'index',
            'database' => $this->activeDatabase['caption']
        ]);
    }

    /**
     * Retrieve a list of types
     *
     * TODO: filter out nonpublic types for guests. JJ: Already done?
     *
     * @return void
     */
    public function index()
    {
        $this->Actions->index();
    }

    /**
     * View a type
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
     * Edit a type
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
     * Add a new project.
     *
     * @param string|null $scope The project type
     * @param string|null $name The project name
     * @return void
     */
    public function add($scope = null, $name = null)
    {
        $default = [
            'scope' => $scope,
            'name' => $name,
            'norm_iri' => $name,
            'caption' => ucfirst($name ?? '')
        ];
        $this->Actions->add(null, $default);
    }

    /**
     * Delete a type
     *
     * @param string|null $id Entity ID
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function delete($id)
    {
        $this->Actions->delete($id);
    }

    /**
     * Import types
     *
     * @param string|null $scope The scope of the types
     *
     * @return \Cake\Http\Response|null|void
     */
    public function import($scope = null)
    {
        $this->Transfer->import($scope);
    }

    /**
     * Transfer types between databases
     *
     * @param $scope
     *
     * @return \Cake\Http\Response|null|void
     * @throws BadRequestException if record not found
     */
    public function transfer($scope = null)
    {
        $requestParams = $this->request->getQueryParams();
        $this->Transfer->transfer($scope, $requestParams);
    }

}
