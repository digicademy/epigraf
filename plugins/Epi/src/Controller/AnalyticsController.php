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
use Cake\Http\Exception\NotFoundException;
use Epi\Model\Analytics\Analytics;

/**
 * Class AnalyticsController
 *
 * @property Analytics $Analytics
 */
class AnalyticsController extends AppController
{

    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'api' => [
            'admin' => [],
            'devel' => []
        ],
        'web' => [
            'admin' => ['*'],
            'devel' => ['*']
        ]
    ];

    /**
     * Side menu items
     *
     * @var array
     */
    public $sidemenu = [
        'caption' => 'Analytics',
        'tree' => 'fixed',
        'scrollbox' => true,
        [
            'label' => 'Types',
            'url' => ['plugin' => 'Epi', 'controller' => 'Analytics', 'action' => 'types']
        ],
        [
            'label' => 'Files',
            'url' => ['plugin' => 'Epi', 'controller' => 'Analytics', 'action' => 'files']
        ],
        [
            'label' => 'Integrity',
            'url' => ['plugin' => 'Epi', 'controller' => 'Analytics', 'action' => 'integrity']
        ],
    ];

    /**
     * Analytics model, see initialize()
     *
     * @var Analytics
     */
    public $Analytics = null;

    /**
     * Initialization hook method
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->modelClass = null;
        parent::initialize();
        $this->Analytics = new Analytics();
    }


    /**
     * beforeRender callback
     *
     * @param EventInterface $event The beforeRender event
     *
     * @return \Cake\Http\Response|void|null
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);
    }

    /**
     * Index method
     *
     * Retrieve list of types.
     *
     * @return mixed
     */
    public function index()
    {
        return $this->redirect(['action' => 'types']);
    }

    /**
     * Get overview (types) of tables
     *
     * @return void
     */
    public function types()
    {
        $data = $this->Analytics->countTypes();
        $this->set(['data' => $data]);
    }

    /**
     * Compare the files- and items-tables in order to identify missing items or files
     *
     * @return void
     */
    public function files()
    {
        $data = $this->Analytics->getCompleteness();

        $this->set(['data' => $data]);
        $this->viewBuilder()->setOption('serialize', 'data');
    }


    /**
     * Get missing items or files
     *
     * URL path: analytics/files-cases
     *
     * @param string $table table name (files or items)
     *
     * @return void
     */
    public function filesCases($table)
    {

        $options = [
            'type' => $this->request->getQuery('type'),
            'online' => $this->request->getQuery('online'),
            'missing' => $this->request->getQuery('missing')
        ];

        // Get data
        $cases = $this->Analytics->getIncompleteCases($table, $options);
        $summary = $this->Analytics->getIncompleteSummary($table, $cases);
        $cases = $this->paginate($cases);

        // View variables and menu item
        $this->activateSideMenuItem(['controller' => 'Analytics', 'action' => 'files']);
        $this->set(compact('cases', 'table', 'summary', 'options'));

        // Options for serialization
        $this->ApiPagination->setConfig('model', $table);
        $this->viewBuilder()->setOption('serialize', ['cases']);
    }

    /**
     * Health method
     *
     * Outputs the result of integrity checks.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function integrity()
    {
        $orphans = $this->Analytics->getOrphans();
        $this->set(compact('orphans'));
    }

    /**
     * Show orphaned records
     *
     * URL path: analytics/integrity-cases
     *
     * @param string $id
     *
     * @return \Cake\Http\Response|null|void
     */
    public function integrityCases($id = '')
    {
        $query = $this->Analytics->getHealthQuery($id);

        if (empty($query)) {
            throw new NotFoundException('Health query not found');
        }

        $records = $this->Analytics->findHealthRecords($query);

        $this->activateSideMenuItem(['controller' => 'Analytics', 'action' => 'integrity']);
        $this->set(compact('records', 'query'));
    }

    /**
     * Remove orphaned records
     *
     * URL path: analytics/integrity-clear
     *
     * @param string $id
     * @return \Cake\Http\Response|null|void|mixed
     */
    public function integrityClear($id = '')
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $result = $this->Analytics->clearOrphans($id);
            if ($result) {
                $this->Flash->success(__('Orphans cleared'));
            } else {
                $this->Flash->error(__('Orphans could not be cleared.'));
            }
        }

        return $this->redirect(['action' => 'integrity']);
    }

}
