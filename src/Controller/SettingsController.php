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

use App\Cache\Cache;
use App\Utilities\Converters\Attributes;
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
        [
            'label' => 'Caches',
            'url' => ['controller' => 'Settings', 'action' => 'caches']
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
        // Params
        $requestParams = $this->request->getQueryParams();
        $paramConfig = [
            'type' => 'list',
            'exception' => 'string',
            'endpoints' => 'list',
            'endpoints_flags' => 'string'
        ];
        $params = Attributes::parseQueryParams($requestParams, $paramConfig);

        $log = Logs::parse(LOGS . 'error.log', $params);

        $columns = [
            'type' => [
                'caption' => __('Type'),
                'type' => 'select',
                'options' => ['warning','error'],
                'empty' => true,
                'default' => true,
                'filter'=>'select'

            ],

            'exception' => [
                'caption' => __('Exception'),
                'type' => 'text',
                'empty' => true,
                'default' => true,
                'filter' => 'text'
            ],

            'endpoints' => [
                'caption' => __('Endpoints'),
                'type' => 'select',
                'options' => ['/epi/' => '/epi/', '/services/' => '/services/','/pages/' => '/pages/','/files/'=>'/files/', '/settings/'=>'/settings/'],
                'empty' => true,
                'default' => true,
                'filter'=>'select'
            ],
        ];
        $this->Answer->addOptions(compact( 'columns'));

        $this->Answer->addAnswer(['log' => $log]);
    }

    public function caches($name = null)
    {
        // Just to make sure, even if permissions were granted manually
        if (!$this->userHasRole(['admin', 'devel'])) {
            throw new ForbiddenException(__('You are not allowed to access this page.'));
        }

        if ($this->request->is('delete')) {
            if (empty($name)) {
                $this->Answer->error(__('No cache name provided'));
            } else {
                Cache::initCache($name);
                $result = Cache::clear($name);
                if ($result) {
                    $this->Answer->success(__('Cache cleared for {0}', $name));
                } else {
                    $this->Answer->error(__('Could not clear the cache for {0}', $name));
                }
            }
        }

        $caches = Cache::getCacheList();
        $this->Answer->addAnswer(compact('caches'));
    }

}
