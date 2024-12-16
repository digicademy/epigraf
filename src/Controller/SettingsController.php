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

use App\Utilities\Files\Logs;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;


/**
 * Class SettingsController
 *
 * Shows variables, php status and logs
 *
 */
class SettingsController extends AppController
{

    /**
     * Table, entity class and segment
     *
     * @var string $defaultTable The table model class name
     * @var string $modelClass The table model class name
     * @var string $segment The template folder (pages or docs)
     */
    public $defaultTable = null;
    public $modelClass = null;
    public $segment = 'settings';

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
        'caption' => 'Settings',
        'tree' => 'fixed',
        'scrollbox' => true,
        [
            'label' => 'Variables',
            'url' => ['controller' => 'Settings', 'action' => 'show', 'vars']
        ],
        [
            'label' => 'PHP',
            'url' => ['controller' => 'Settings', 'action' => 'show', 'php']
        ],
        [
            'label' => 'Logs',
            'url' => ['controller' => 'Settings', 'action' => 'logs']
        ],
    ];

    /**
     * Initialization hook method
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->modelClass = null;
        parent::initialize();
    }

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\Event $event Event
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->_activateMainMenuItem([]);
        $this->pagetitle = [__('System settings')];
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
     * Show variables
     *
     * @return Response
     * @throws NotFoundException If the page was not found
     */
    public function show($iri = null)
    {
        // Just to make sure, even if permissions were granted manually
        if (!$this->userHasRole(['admin', 'devel'])) {
            throw new ForbiddenException(__('You are not allowed to access this page.'));
        }

        return $this->Actions->static($iri, ['templates' => ['settings' => 'Settings']]);
    }

    /**
     * Show log
     */
    public function logs()
    {
        $log = Logs::parse(LOGS . 'error.log');
        $this->Answer->addAnswer(['log' => $log]);
    }
}
