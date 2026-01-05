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
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Epi\Model\Table\NotesTable;
use Rest\Controller\Component\LockTrait;

/**
 * Notes Controller
 *
 * TODO: Make it dry, see DocsController.php
 *
 * @property NotesTable $Notes
 */
class NotesController extends AppController
{

    use LockTrait;

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
            'reader' => [],
            'coder' => ['index', 'view', 'show'],
            'desktop' => ['index', 'view', 'show', 'add', 'edit', 'delete', 'lock', 'unlock'],
            'author' => ['index', 'view', 'show', 'add', 'edit', 'delete', 'lock', 'unlock'],
            'editor' => ['index', 'view', 'show', 'add', 'edit', 'delete', 'lock', 'unlock']
        ]
    ];

    public $help = 'introduction/notes';

    /**
     * Pagination setup
     *
     * @var array
     */
    public $paginate = [
        'className' => 'Total',
        'order' => [
            'Notes.name' => 'asc'
        ]
    ];

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\Event $event Event
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->sidemenu = $this->Notes->getMenu();
        $this->loadComponent('Epi.Transfer', ['model' => 'Epi.Notes']);
    }

    /**
     * beforeRender callback
     *
     * @param EventInterface $event
     * @return void
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);
        $this->set(['title' => __('Notes')]);
    }

    /**
     * Retrieve notes list
     *
     * If a category is given in the query parameters and only one entity exists,
     * redirect to the view action.
     *
     * @return void
     */
    public function index()
    {
        $this->Actions->index($this->Notes->scopeValue, ['sidemenu' => 'category']);
        $this->render('/Docs/' . $this->request->getParam('action'));
    }

    /**
     * View a single note
     *
     * @param string $id Note id
     * @return void
     */
    public function view($id)
    {
        $this->Actions->view($id, ['sidemenu' => 'category', 'speaking'=>'show']);

        $action = $this->request->getParam('action');
        $action = $action === 'show' ? 'view' : $action;
        $this->render('/Docs/' . $action);
    }

    /**
     * Show a note by its IRI or category
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
        $this->Actions->show($iri, ['sidemenu' => 'category', 'speaking'=>'show']);
        $action = $this->request->getParam('action');
        $action = $action === 'show' ? 'view' : $action;
        $this->render('/Docs/' . $action);
    }

    /**
     * Edit a note
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
     * Add a new note
     *
     * * //TODO: when created from a single page, show the new page afterwards
     *
     * @return void
     */
    public function add()
    {
        $default = [
            'format' => 'html'
        ];
        $this->Actions->add(null, $default, ['sidemenu' => 'category']);
        $this->render('/Docs/' . $this->request->getParam('action'));
    }

    /**
     * Delete a note
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
     * Import note method
     *
     * @return \Cake\Http\Response|null|void
     */
    public function import()
    {
        $this->Transfer->import();
    }

}
