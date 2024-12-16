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

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\I18n\I18n;
use Cake\Utility\Inflector;
use Rest\Controller\Component\LockTrait;

/**
 * Wiki Controller
 *
 * ### Administration of help pages
 *
 * Help pages are called Docs and are stored in the database.
 * When F1 is hit in EpigrafDesktop the show action of
 * docs controller is requested with the norm_iri in the query
 * parameter key. The corresponding help page is opened if it
 * exists, otherwise the request is redirected to the add action.
 *
 * @property \App\Model\Table\DocsTable $Docs
 */
class WikiController extends AppController
{

    /**
     * The lock trait provides lock and unlock actions
     */
    use LockTrait;

    /**
     * Table, entity class and segment
     *
     * @var string $defaultTable The table model class name
     * @var string $modelClass The table model class name
     * @var string $segment The segment in the table
     */
    public $defaultTable = 'Docs';
    public $modelClass = 'Docs';
    public $segment = 'wiki';


    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'api' => [
            'desktop' => ['lock', 'unlock'],
            'author' => ['lock', 'unlock'],
            'editor' => ['lock', 'unlock']
        ],
        'web' => [
            'guest' => ['show', 'view', 'index'],
            'reader' => ['show', 'view', 'index'],
            'desktop' => ['show', 'view', 'index', 'add', 'edit', 'delete', 'lock', 'unlock'],
            'author' => ['show', 'view', 'index', 'add', 'edit', 'delete', 'lock', 'unlock'],
            'editor' => ['show', 'view', 'index', 'add', 'edit', 'delete', 'lock', 'unlock']
        ]
    ];

    /**
     * Initialization hook method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * beforeFilter callback
     *
     * @param EventInterface $event
     *
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->loadComponent('Epi.Transfer', ['model' => 'Docs']);

        $this->Docs->setScope($this->segment);

        //TODO: Do we still need to pass segment to the view?
        $this->set('segment', $this->segment);
        $this->set(['title' => I18n::getTranslator()->translate(Inflector::humanize($this->segment))]);

        $this->_activateMainMenuItem(
            [
                'plugin' => false,
                'controller' => 'Wiki',
                'action' => 'show',
                'start'
            ]);


        if (($this->_getUserRole() !== 'guest') || ($this->segment === 'help')) {
            $published = $this->_getUserRole() === 'guest';
            $this->sidemenu = $this->Docs->getMenu($published);
        }
    }

    /**
     * Retrieve list of docs
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->Actions->index($this->Docs->scopeValue, ['sidemenu' => 'category']);
        $this->render('/Docs/' . $this->request->getParam('action'));
    }

    /**
     * Show a doc (help, wiki, page, note)
     *
     * @param string $id Doc id.
     *
     * @return \Cake\Http\Response|null|void
     * @throws RecordNotFoundException If record not found
     */
    public function view($id = null)
    {
        $this->Actions->view($id, ['sidemenu' => 'category', 'speaking' => 'show']);

        $action = $this->request->getParam('action');
        $action = $action === 'show' ? 'view' : $action;
        $this->render('/Docs/' . $action);
    }

    /**
     * Show a doc by its IRI or category
     *
     * Redirects to the view action based on path or query parameter.
     * If the entity does not exist, redirects authenticated users
     * to the add action. Throws a NotFoundException for
     * unauthenticated users.
     *
     * @param string|null $iri The document IRI
     * @queryparam string|null key Alternative to provide the IRI
     * @queryparam string|null category The category
     * @return void
     * @throws RecordNotFoundException If record not found and unauthenticated user
     */
    public function show($iri = null)
    {
        $addRoles = ['desktop', 'author', 'editor', 'admin', 'devel'];
        $response = $this->Actions->show($iri,
            ['static' => true, 'add' => $addRoles, 'sidemenu' => 'category', 'speaking' => 'show']);
        if (!empty($response)) {
            return $response;
        }
        else {
            $action = $this->request->getParam('action');
            $action = $action === 'show' ? 'view' : $action;
            $this->render('/Docs/' . $action);
        }
    }

    /**
     * Edit a doc
     *
     * @param string $id Entity ID
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function edit($id)
    {
        $this->Actions->edit($id, ['sidemenu' => 'category']);
        $this->render('/Docs/' . $this->request->getParam('action'));
    }

    /**
     * Add a new doc
     *
     * //TODO: when created from a single page, show the new page afterwards
     *
     * @param string|null $iri The default IRI fragment
     * @return void
     */
    public function add($iri = null)
    {
        $default = [
            'format' => 'html',
            'norm_iri' => $iri,
            'name' => $this->request->getQuery('data-name', Inflector::humanize(str_replace('-', '_', $iri ?? '')))
        ];
        $this->Actions->add(null, $default, ['sidemenu' => 'category']);
        $this->render('/Docs/' . $this->request->getParam('action'));
    }

    /**
     * Delete a doc
     *
     * @param string $id Entity ID
     * @return void
     * @throws RecordNotFoundException If record not found
     */
    public function delete($id)
    {
        $this->Actions->delete($id, ['sidemenu' => 'category']);
        $this->render('/Docs/' . $this->request->getParam('action'));
    }

    /**
     * Import docs
     *
     * @param string|null $scope
     * @return void
     */
    public function import($scope = null)
    {
        $this->Transfer->import('docs', $scope);
    }
}
