<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

/**
 * App controller namespace
 *
 * All logic that is not specific to project databases
 */

namespace App\Controller;

use App\Model\Entity\Databank;
use App\Model\Table\BaseTable;
use App\Model\Table\PermissionsTable;
use App\Model\Table\UsersTable;
use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use App\Cache\Cache;
use App\View\AppView;
use App\View\CsvView;
use App\View\JsonldView;
use App\View\JsonView;
use App\View\MarkdownView;
use App\View\RdfView;
use App\View\TtlView;
use App\View\XmlView;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\FactoryLocator;
use Cake\Event\EventInterface;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Rest\Controller\Component\ActionsComponent;
use Rest\Controller\Component\AnswerComponent;
use Rest\Controller\Component\ApiPaginationComponent;

/**
 * Application Controller
 *
 * @property ActionsComponent $Actions
 * @property AnswerComponent $Answer
 * @property ApiPaginationComponent $ApiPagination
 */
class AppController extends Controller
{
    /**
     * Configuration for title extraction
     *
     * @var string|string[] $pagetitle An array of title segments or a string
     */
    public $pagetitle = [];

    public $modelClass;

    /**
     * Main menu items
     *
     * @var array $menu
     */
    public $menu = [];

    /**
     * Side menu items
     *
     * Use the following optional nonnumeric keys for general settings:
     * - caption The menu caption
     * - titel The title of the current item
     * - activate true|false Whether the active item is determined by the request
     * - tree fixed|foldable Whether the menu is rendered as a tree.
     *
     * Array items with numeric keys will result in menu items.
     * Each menu item can have the following optional keys:
     * - label
     * - url
     * - class
     * - data
     * - escape
     * - dropdown
     * - items
     * - spacer
     *
     * @var array $sidemenu
     */
    public $sidemenu = [];

    /**
     * Accessible databases
     *
     * @var null $allowedDatabases
     */
    public $allowedDatabases = null;

    /**
     * Currently active database
     *
     * @var null $activeDatabase
     */
    public $activeDatabase = null;

    /**
     * Data passed to the JavaScript App object
     *
     * @var array $js_data
     */
    public $js_data = [];

    /**
     * Data passed to the JavaScript App object
     *
     * @var array $js_user
     */
    public $js_user = [];

    /**
     * The user role and scope determine the actions a user can call
     *
     * The role is determined from the user record.
     * See PermissionsTable::$userRoles for the available options.
     *
     * The scope is determined from the login procedure:
     * - form authentication -> web
     * - token authentication -> api
     *
     * The mode is determined from the query parameter:
     * - default If no parameter is set.
     * - code Coding mode.
     * - present In presentation mode, this is the default for public users.
     *
     * The layout is determined from the query parameter, can be empty.
     *
     * @var string $requestAction
     * @var string $requestMode
     * @var string $requestPreset
     * @var string $requestScope
     * @var array $requestPublished An array of publication states the visible items must match or null to show all items.
     * @var string $userRole
     */
    public $requestAction = '';
    public $requestMode = '';
    public $requestPreset = '';
    public $requestScope = '';
    public $requestPublished = null;
    public $userRole = 'guest';

    /**
     * Access permissions
     *
     * //TODO: Can we remove api access for guest users (useful for EpiDesktop redirects only)?
     *
     * Three access levels are used:
     * - first level is the requestScope (web or api),
     * - second level the user role (guest, reader, author, editor, admin, devel)
     * - third level the actions (determined in each controller)
     *
     * Admins and Devels have all permissions if not defined otherwise.
     * You can use an asterisk to adress all actions.
     *
     * See AppController::isAuthorized for further details.
     * Call /permissions/endpoints for an overview.
     *
     * @var array $authorized
     */
    public $authorized = [
        'web' => [
            'guest' => [],
            'reader' => [],
            'coder' => [],
            'desktop' => [],
            'author' => [],
            'editor' => []
        ],
        'api' => [
            'guest' => [],
            'reader' => [],
            'coder' => [],
            'desktop' => [],
            'author' => [],
            'editor' => []
        ]
    ];

    /**
     * Navigation params are stripped when redirecting to user settings
     *
     * @var string[] $paramsForNavigation
     */
    public $paramsForNavigation = [
        'load',
        'redirect',
        'cursor',
        'children',
        'collapsed',
        'page',
        'limit',
        'offset',
        'tile',
        'id',
        'show',
        'action'
    ];

    /**
     * Cache configuration
     *
     * @var null $_cacheConfigName
     */
    protected $_cacheConfigName = null;

    public $paginate = [
        'className' => 'Total'
    ];

    /**
     * Initialization hook method
     *
     * Set locale.
     * Load components for request handling.
     * Set authentication method (Token, Form).
     *
     * @return void
     */
    public function initialize(): void
    {
        // Map extension to mime types
        $this->getResponse()->setTypeMap('jsonld', ['application/ld+json']);
        $this->getResponse()->setTypeMap('ttl', ['text/turtle']);
        $this->getResponse()->setTypeMap('md', ['text/markdown']);

        parent::initialize();

        //Load components for request handling
        $this->loadComponent(
            'RequestHandler',
            [
                'viewClassMap' => [
                    'csv' => 'App.Csv',
                    'jsonld' => 'App.Jsonld',
                    'rdf' => 'App.Rdf',
                    'ttl' => 'App.Ttl',
                    'md' => 'App.Markdown'
                ],
                'enableBeforeRedirect' => false
            ]
        );
        $this->loadComponent('Flash', ['duplicate' => false]);
        $this->loadComponent(
            'Rest.ApiPagination',
            [
                'aliases' => ['prevPage' => 'page_prev', 'nextPage' => 'page_next', 'perPage' => 'perpage'],
                'visible' => [
                    'count',
                    'perpage',
                    'page',
                    'page_prev',
                    'page_next',
                    'seek',
                    'cursor',
                    'children',
                    'collapsed'
                ]
            ]
        );
        $this->loadComponent('Rest.Actions');
        $this->loadComponent('Rest.Answer');
        $this->loadComponent('Rest.Lock');

        // TODO: move to Rest plugin?
        $this->_initAuthorization();
        $this->_initLocale();
    }

    /**
     * beforeFilter callback
     *
     * Init main menu, set access mode, set layout, force SSL.
     *
     * @param EventInterface $event
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event)
    {

        // Enable CORS
        // TODO: create middleware or integrate in RestAnswerMiddleware
        $corsDomains = Configure::read('App.cors');
        if (!empty($corsDomains)) {
            $this->response = $this->response
                ->cors($this->request)
                ->allowOrigin($corsDomains)
                ->allowMethods(['GET'])
                ->build();
        }

        // Init user and database
        $this->_initUser();
        $this->_selectDatabase();

        // Set request format
        BaseTable::$requestFormat = $this->request->getParam('_ext', 'html');

        // Load user settings
        $this->_loadUserSettings();

        // Create menu
        $this->_createMainMenu();
    }

    /**
     * beforeRender callback
     *
     * Initialize main menu and side menu,
     * include helpers,
     * transfer data to JavaScript.
     *
     * @param EventInterface $event
     *
     * @return \Cake\Http\Response|void|null
     */
    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        // Helpers
        $this->viewBuilder()->addHelpers([
            'Form' => [
                'templates' => 'forms',
                'widgets' => [
                    'choose' => [
                        'Widgets\View\Widget\ChooseWidget',
                        'text',
                        'label'
                    ],
                    'reference' => [
                        'Widgets\View\Widget\ReferenceWidget',
                        'hidden',
                        'text'
                    ],
                    'jsoneditor' => [
                        'Widgets\View\Widget\JsonWidget',
                        'hidden',
                        'text'
                    ],
                    'htmleditor' => [
                        'Widgets\View\Widget\HtmlWidget',
                        'textarea'
                    ]
                ]
            ]
        ]);

        // Set menu and title view variables
        $this->menu = $this->_activateMenuByRequest($this->menu);
        $this->sidemenu = $this->_activateMenuByRequest($this->sidemenu);
        $this->pagetitle = $this->_getPageTitle($this->menu);
        $this->theme = $this->_getTheme();

        $this->set('menu', $this->menu);
        $this->set('sidemenu', $this->sidemenu);
        $this->set('pagetitle', $this->pagetitle);
        $this->set('theme', $this->theme);

        // Transfer data to javascript
        $this->set('js_data', $this->js_data);
        $this->set('js_user', $this->js_user);
    }

    public function viewClasses(): array
    {
        return [
            AppView::class,
            JsonView::class,
            XmlView::class,
            CsvView::class,
            MarkdownView::class,
            JsonldView::class,
            RdfView::class,
            TtlView::class
        ];
    }

    /**
     * Initialize main menu item
     *
     * Set active main menu item based on URL parameter,
     * used in some controllers.
     *
     * @param array $url An empty array will disable all active items
     * @return void
     */
    protected function _activateMainMenuItem($url = [])
    {
        foreach ($this->menu as $key => $item) {
            if (($item['url'] ?? []) == $url) {
                $this->menu[$key]['active'] = true;
                break;
            }
        }
    }


    /**
     * Initialize side menu item
     *
     * Set active side menu item based on URL parameter,
     * used in some controllers.
     *
     * @param $url
     *
     * @return void
     */
    public function activateSideMenuItem($url = [])
    {
        foreach ($this->sidemenu as $key => $item) {
            if (($item['url'] ?? []) == $url) {
                // TODO: open tree items in the item's trace,
                //       its children and respective siblings
                //       (tree-collapsed, tree-hidden)
                $this->sidemenu[$key]['active'] = true;
                break;
            }
        }
    }

    /**
     * Initialize menu item
     *
     * Set active main and side menu item based on request,
     * see beforeRender.
     *
     * @param $url
     *
     * @return array
     */
    protected function _activateMenuByRequest($menu)
    {
        if (!$menu || !($menu['activate'] ?? true)) {
            return $menu;
        }

        $points = [];
        foreach ($menu as $key => $item) {
            if (!is_numeric($key)) {
                $points[$key] = -1;
            }
            else {
                $points[$key] = 3 * (int)($item['active'] ?? false);
                $points[$key] += (int)(($item['url']['plugin'] ?? false) == $this->request->getParam('plugin', false));
                $points[$key] += 2 * (int)(strtolower($item['url']['controller'] ?? '') == Inflector::dasherize(
                            $this->request->getParam('controller', '')
                        ));
                $points[$key] += (int)(($item['url']['action'] ?? false) == $this->request->getParam('action', false));
                $points[$key] += (int)(($item['url'][0] ?? false) == ($this->request->getParam('pass')[0] ?? false));
            }
        }

        $maxpoints = array_keys($points, max($points));

        if (($menu[$maxpoints[0]]['url']['plugin'] ?? false) === $this->request->getParam('plugin', false)) {
            $menu[$maxpoints[0]]['active'] = true;
            $menu['title'] = $menu[$maxpoints[0]]['label'] ?? '';
        }

        return $menu;
    }

    /**
     * Initialize main menu for guest users
     *
     * Get main menu items for users that are not logged in,
     * see _createMainMenu.
     *
     * @return array $menu
     */
    protected function _getGuestMenu()
    {
        $menu = [];
        $menu[] = [
            'label' => 'Epigraf »',
            'escape' => false,
            'class' => 'menu_public menu-home',
            'url' => ['controller' => 'Pages', 'action' => 'show', 'start', 'plugin' => false]
        ];

        $menu[] = [
            'label' => __('Documentation'),
            'class' => 'menu_global',
            'url' => ['plugin' => false, 'controller' => 'Help', 'action' => 'show', 'start']
        ];

        // TODO: more elegant reservedItems and menu handling, implement tree for docs?
        $pages = TableRegistry::getTableLocator()->get('Docs');
        $pages->setScope('pages');
        $reservedItems = ['start'];
        $pages = $pages
            ->find('list', [
                'conditions' => ['published' => 1, 'norm_iri NOT IN' => $reservedItems],
                'contain' => [],
                'keyField' => 'norm_iri',
                'valueField' => 'name'
            ])
            ->order(['sortkey' => 'ASC', 'name' => 'ASC']);

        foreach ($pages as $key => $name) {
            $menu[] = array(
                'label' => $name,
                'class' => 'menu_public',
                'url' => ['controller' => 'Pages', 'action' => 'show', $key, 'plugin' => false]
            );
        }

        return $menu;
    }

    /**
     * Initialize main menu for authenticated users
     *
     * See _createMainMenu().
     *
     * @return array
     */
    protected function _getUserMenu()
    {
        $menu = [];

        //database menu
        $subitems[] = [
            'label' => '<i>' . __('Manage databases') . '</i>',
            'roles' => ['admin'],
            'escape' => false,
            'url' => ['plugin' => false, 'controller' => 'Databanks', 'action' => 'index'],
            'grouped' => true
        ];

        $allowedDatabases = $this->getAllowedDatabases();
        $allowedDatabases = Arrays::array_group($allowedDatabases, 'category');
        foreach ($allowedDatabases as $dbGroup => $dbItems) {

            if (!empty($dbGroup)) {
                $subitems [] = [
                    'label' => $dbGroup,
                    'group' => $dbGroup,
                    'grouplabel' => true
                ];
            }

            foreach ($dbItems as $dbItem) {
                $db_name = Databank::removePrefix($dbItem['name']);
                $subitems [] = [
                    'label' => $db_name,
                    'group' => $dbGroup,
                    'url' => [
                        'plugin' => $dbItem['plugin'],
                        'controller' => 'Articles',
                        'action' => 'index',
                        'database' => $db_name
                    ]
                ];
            }
        }

        $database = $this->activeDatabase['caption'] ?? false;

        $menu[] = [
            'label' => empty($database) ? __('Databases') : $database,
            'dropdown' => true,
            'class' => 'menu_database',
            'items' => $subitems
        ];

        if (!empty($this->activeDatabase)) {
            $menu[] = [
                'label' => __('Projects'),
                'class' => 'menu_database',
                'url' => [
                    'plugin' => $this->activeDatabase->plugin,
                    'controller' => 'Projects',
                    'action' => 'index',
                    'database' => $database,
                    '?' => ['load' => true]
                ]
            ];
            $menu[] = [
                'label' => __('Articles'),
                'class' => 'menu_database',
                'url' => [
                    'plugin' => $this->activeDatabase->plugin,
                    'controller' => 'Articles',
                    'action' => 'index',
                    'database' => $database,
                    '?' => ['load' => true]
                ]
            ];
            $menu[] = [
                'label' => __('Categories'),
                'class' => 'menu_database',
                'url' => [
                    'plugin' => $this->activeDatabase->plugin,
                    'controller' => 'Properties',
                    'action' => 'index',
                    'database' => $database,
                    '?' => ['load' => true]
                ]
            ];
            $menu[] = [
                'label' => __('Files'),
                'class' => 'menu_database',
                'url' => [
                    'plugin' => $this->activeDatabase->plugin,
                    'controller' => 'Files',
                    'action' => 'index',
                    'database' => $database,
                    '?' => ['load' => true]
                ]
            ];
            $menu[] = [
                'label' => __('Notes'),
                'class' => 'menu_database',
                'url' => [
                    'plugin' => $this->activeDatabase->plugin,
                    'controller' => 'Notes',
                    'action' => 'index',
                    'database' => $database,
                    '?' => ['load' => true]
                ]
            ];
            $menu[] = [
                'label' => __('Types'),
                'roles' => ['editor', 'admin', 'devel'],
                'class' => 'menu_database',
                'url' => [
                    'plugin' => $this->activeDatabase->plugin,
                    'controller' => 'Types',
                    'action' => 'index',
                    'database' => $database,
                    '?' => ['load' => true]
                ]
            ];
            $menu[] = [
                'label' => __('Users'),
                'roles' => ['admin', 'devel'],
                'class' => 'menu_database',
                'url' => [
                    'plugin' => $this->activeDatabase->plugin,
                    'controller' => 'Users',
                    'action' => 'index',
                    'database' => $database,
                    '?' => ['load' => true]
                ]
            ];
            $menu[] = [
                'label' => __('Analytics'),
                'roles' => ['editor', 'admin', 'devel'],
                'class' => 'menu_database',
                'url' => [
                    'plugin' => $this->activeDatabase->plugin,
                    'controller' => 'Analytics',
                    'action' => 'index',
                    'database' => $database,
                    '?' => ['load' => true]
                ]
            ];

            $menu[] = ['spacer' => true, 'class' => 'menu_database'];
        }

        //admin menu
        $menu[] = [
            'label' => __('Pipelines'),
            'class' => 'menu_global',
            'url' => ['plugin' => false, 'controller' => 'Pipelines', 'action' => 'index']
        ];
        $menu[] = [
            'label' => __('Users'),
            'class' => 'menu_global',
            'url' => ['plugin' => false, 'controller' => 'Users', 'action' => 'index']
        ];
        $menu[] = [
            'label' => __('Repository'),
            'class' => 'menu_global',
            'url' => ['plugin' => false, 'controller' => 'Files', 'action' => 'index', '?' => ['root' => 'shared']]
        ];

        //doc and wiki menu item
        $menu[] = [
            'label' => __('Help'),
            'class' => 'menu_global',
            'url' => ['plugin' => false, 'controller' => 'Help', 'action' => 'show', 'start']
        ];
        $menu[] = [
            'label' => __('Wiki'),
            'class' => 'menu_global',
            'url' => ['plugin' => false, 'controller' => 'Wiki', 'action' => 'show', 'start']
        ];
        $menu[] = [
            'label' => __('Pages'),
            'roles' => ['editor', 'admin', 'devel'],
            'class' => 'menu_global',
            'url' => ['plugin' => false, 'controller' => 'Pages', 'action' => 'show', 'start']
        ];

        return $menu;
    }

    /**
     * Initialize main menu
     *
     * See beforeFilter().
     *
     * @return void
     */
    protected function _createMainMenu()
    {
        if (($this->userRole ?? 'guest') === 'guest') {
            $this->menu = $this->_getGuestMenu();
        }
        else {
            $this->menu = $this->_getUserMenu();
        }
    }


    /**
     * Assemble the page title
     *
     * @return string
     */
    public function _getPageTitle($menu): string
    {
        // Option 1: Get fixed title
        $title = $this->pagetitle;
        if (!is_array($title)) {
            $title = [$title];
        }

        // Option 2: Extract title from viewVars
        // @deprecated
        if (empty($title) && !empty($this->viewVar) && !empty($this->entityVar)) {
            $entity = $this->viewBuilder()->getVar($this->viewVar);
            $title[] = $entity[$this->entityVar] ?? null;
            $title = array_filter($title);
        }

        // Option 2: Extract title from entity
        if (empty($title)) {
            $entity = $this->viewBuilder()->getVar('entity');
            $title[] = $entity['caption'] ?? null;
            $title = array_filter($title);
        }

        // Option 3: Get the selected menu item title
        if (empty($title)) {
            $title[] = $menu['title'] ?? 'Epigraf';
        }

        // Add database
        $database = $this->request->getParam('database');
        if (!empty($database)) {
            $title[] = '[' . $database . ']';
        }

        $title = array_filter($title);
        return implode(' ', $title);
    }

    /**
     * Get the theme from the user settings or the URL parameter
     *
     * Possible theme names are defined in UsersTable::$themes.
     * Note: The dark/light theme is automatically loaded in default.css using a media query.
     *
     * @return string
     */
    public function _getTheme()
    {
        // Set color theme. URL parameter 'theme' has priority over user settings.
        $theme = $this->request->getQuery('theme', null);
        if (empty($theme)) {
            $theme = $this->Actions->getUserSettings('ui', 'theme', null);
        }

        return Attributes::cleanOption($theme, array_keys(UsersTable::$themes), 'default');
    }

    /**
     * Build a login URL that redirect to the page afterwards
     *
     * @return array
     */
    public function getLoginUrl()
    {
        // Build redirect URL
        $request = $this->getRequest();
        $params = $request->getAttribute('params');
        $redirect = $params + $params['pass'] ?? [];
        unset($redirect['pass']);
        unset($redirect['_matchedRoute']);
        $redirect['?']['token'] = false;

        // If alread tried to login on this URL, then redirect to the homepage
        if (!empty($redirect['?']['login'])) {
            $redirect = Router::url('/');
        }
        else {
            $redirect['?']['login'] = '1';
            $redirect = Router::url($redirect);
        }

        $url = ['plugin' => false, 'controller' => 'Users', 'action' => 'login', '?' => ['redirect' => $redirect]];
        return $url;
    }

    public function getStrippedDatabaseUrl()
    {
        $request = $this->getRequest();
        $params = $request->getAttribute('params');
        $url = $params + $params['pass'] ?? [];
        unset($url['pass']);
        unset($url['_matchedRoute']);
        unset($url['?']['database']);

        $url['database'] = false;
        $url['?']['token'] = false;

        return $url;
    }

    /**
     * Set locale based on user settings
     *
     * @return void
     */
    protected function _initLocale()
    {
        $uiLocale = $this->Actions->getUserSettings('ui', 'locale', I18n::getDefaultLocale());
        $uiLocale = Attributes::cleanOption($uiLocale, array_keys(UsersTable::$locales), I18n::getDefaultLocale());
        I18n::setLocale($uiLocale);
    }

    /**
     * Set the locale and save it in the user settings
     *
     * // TODO: implement settings selector in the user profile page
     *
     * @param string|null $locale The locale, e.g. 'de_DE.UTF-8'.
     *                            Set to null to use the default locale.
     * @return void
     */
    public function setLocale($locale = null)
    {
        $locale = $locale ?? I18n::getDefaultLocale();
        I18n::setLocale($locale);
        $this->Actions->mergeUserSettings('app', 'i18n', ['locale' => $locale]);
    }

    /**
     * Authorisation settings
     *
     * Users can access the database via tokens or web authorisation.
     *
     * @return void
     * @throws \Exception
     */
    protected function _initAuthorization()
    {
        // Switch between token authentication or form authentication
        $token = $this->request->getQuery('token');

        if ($token) {
            $this->loadComponent('Auth', [
                'authenticate' => [
                    'Token' => [
                        'finder' => 'auth',
                        'fields' => ['token' => 'accesstoken', 'password' => 'password'],
                        'parameter' => 'token',
                        'header' => false
                    ]
                ],
                'authorize' => ['Controller'],
                'unauthorizedRedirect' => $this->request->is('api') ? false : $this->getLoginUrl(),
                'authError' => $this->request->is('api') ? null : false,
                'storage' => 'Memory',
                'loginRedirect' => false,
                'logoutRedirect' => false
            ]);
        }
        else {
            $this->loadComponent('Auth', [
                'authenticate' => [
                    'Form' => ['finder' => 'auth']
                ],
                'authorize' => ['Controller'],
                'unauthorizedRedirect' => false,
                'storage' => '\App\Auth\Storage\SessionStorage',
                'loginRedirect' => [
                    'controller' => 'Users',
                    'action' => 'start'
                ],
                'logoutRedirect' => [
                    'controller' => 'Pages',
                    'action' => 'show',
                    'start'
                ]
            ]);
        }

        //Check user in initialize to provide user info in beforeFilter
        $this->Auth->setConfig('checkAuthIn', 'Controller.initialize');

        // Allow public actions
        $this->allowPublic($this->authorized['web']['guest'] ?? []);

    }

    /**
     * Get request scope
     *
     * @return string 'api' for requests with access tokens, othereise 'web'
     */
    protected function _getRequestScope()
    {
        return $this->request->getQuery('token', false) ? 'api' : 'web';
    }

    /**
     * Get request mode
     *
     * Two request modes are possible:
     * - default The default types config is used.
     *           For view actions, the preview config is merged into the default config.
     * - code   The code types config is used.
     *          In this mode, only explicitly configured fields are editable
     *          and some article sections are fixed for quick access.
     * - preview The preview types config is used.
     *
     * @return string Depending on the query parameter returns 'code', 'preview' or 'default'
     */
    protected function _getRequestMode()
    {
        return Attributes::cleanOption(
            $this->request->getQuery('mode'),
            ['code'],
            'default'
        );
    }

    /**
     * Get request preset
     *
     * @return string The preset name used to find type configurations
     */
    protected function _getRequestPreset()
    {
        return $this->request->getQuery('preset', 'default');
    }

    /**
     * Get request action
     *
     * @return string The controller action
     */
    protected function _getRequestAction()
    {
        return $this->request->getParam('action');
    }

    /**
     * Get requested publicaton states
     *
     * @return integer[]
     */
    protected function _getRequestPublished()
    {
        return Attributes::commaListToIntegerArray($this->request->getQuery('published', null));
    }


    /**
     * Get user role
     *
     * Note: This method is overridden in the Epi\AppController.
     * // TODO: Implement own component for all user related methods
     *
     * @param array $user The user data from the Auth component
     * @param string $database The currently selected database
     * @return mixed|string
     */
    public function _getUserRole($user = null, $database = null)
    {
        $user = $user ?? $this->Auth->user();
        $userRole = Attributes::cleanOption(
            $user['role'] ?? 'guest',
            array_keys(PermissionsTable::$userRoles),
            'guest'
        );

        if (!empty($database) && !in_array($userRole, ['admin', 'devel'])) {
            $database = Databank::addPrefix($database);
            $requestScope = $this->_getRequestScope();

            foreach ($user['permissions'] ?? [] as $permission) {
                if (
                    (($permission['permission_type'] ?? '') === 'access') &&
                    (($permission['entity_type'] ?? '') === 'databank') &&
                    (($permission['entity_name'] ?? '') === $database) &&
                    (($permission['user_request'] ?? 'web') === $requestScope)
                ) {
                    $userRole = $permission['user_role'] ?? $user['role'] ?? '';
                    break;
                }
            }
        }

        return $userRole;
    }

    /**
     * Get user id
     *
     * @param $user
     *
     * @return integer|null
     */
    public function _getUserId($user = null)
    {
        $user = $user ?? $this->Auth->user();
        return $user['id'] ?? null;
    }

    /**
     * Get user iri
     *
     * @param $user
     *
     * @return string|null
     */
    protected function _getUserIri($user = null)
    {
        $user = $user ?? $this->Auth->user();
        return $user['norm_iri'] ?? null;
    }

    /**
     * Allow public actions
     *
     * Auth->allow() bypasses the Authorization process.
     * Therefore, check for empty user and tokens.
     * Otherwise, for token users, only public databases are accessible.
     *
     * @param $actions
     *
     * @return void
     */
    protected function allowPublic($actions)
    {
        if (empty($actions)) {
            return;
        }

        $user = $this->Auth->user();
        $token = $this->request->getQuery('token');

        if (empty($user) && empty($token)) {
            $this->Auth->allow($actions);
        }

    }

    /**
     * Get accessible databases
     *
     * @param $user
     *
     * @return array|null
     */
    public function getAllowedDatabases($user = null)
    {
        if ($this->allowedDatabases === null) {
            $user = $user ?? $this->Auth->user();
            $this->allowedDatabases = $this->fetchTable('Databanks')
                ->find('allowedBy', ['user' => $user])
                ->all()
                ->indexBy('name')
                ->toArray();
        }

        return $this->allowedDatabases;
    }

    /**
     * Test whether the specified database is accessible
     *
     * @param $databaseName
     *
     * @return bool
     */
    protected function isAllowedDatabase($databaseName)
    {
        return ($databaseName !== null) && isset($this->getAllowedDatabases()[Databank::addPrefix($databaseName)]);

    }

    /**
     * Test whether the user has one of the roles specified
     *
     * @param array $roles
     *
     * @return bool
     */
    public function userHasRole(array $roles)
    {
        return in_array($this->userRole, $roles);
    }

    /**
     * Get the permission mask for a user
     *
     * The result is used in AppController::hasGrantedPermission() to determine permissions.
     *
     * @param array|null $user If null, the current user is used.
     * @return array
     */
    public function getPermissionMask($user = null)
    {
        $action = 'app/' . strtolower($this->request->getParam('controller')) . '/' . strtolower($this->request->getParam('action'));

        $permission = [
            'user_id' => $this->_getUserId(),
            'user_role' => $this->_getUserRole($user),
            'user_request' => $this->_getRequestScope(),
            'permission_name' => $action,
            'permission_type' => 'access'

        ];

        return $permission;
    }

    /**
     * Check whether the user is allowed to access an action
     * by comparing the action and request scope with the $authorized property
     *
     * @param array $user The user data from the auth component
     * @return bool
     */
    protected function hasWiredPermission($user)
    {
        $requestScope = $this->_getRequestScope();
        $userRole = $user['role'] ?? '';

        // Check $authorized property
        $allowedActions = $this->authorized[$requestScope][$userRole] ?? [];
        if (in_array($this->request->getParam('action'), $allowedActions)) {
            return true;
        }
        elseif (in_array('*', $allowedActions)) {
            return true;
        }

        // Admins and devels are gods on the web, if not hardwired otherwise
        elseif (
            ($requestScope === 'web') && in_array($userRole, ['admin', 'devel']) &&
            !isset($this->authorized[$requestScope][$userRole])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check whether the user is allowed to access the endpoint
     * by comparing the request to the permission table.
     *
     * The app wide controller and the epi controller provide
     * specific implementations of getPermissionMask().
     *
     * @param array $user The user data from the auth component
     * @return bool
     */
    protected function hasGrantedPermission($user = null)
    {
        $permissionTable = $this->fetchTable('Permissions');
        $permissionMask = $this->getPermissionMask($user);
        return $permissionTable->hasPermission($permissionMask);
    }

    /**
     * Determines whether a user is allowed to access an action.
     *
     * Remember that this function or implicitly called functions may be overwritten in other controllers.
     *
     * TODO: refactor to use a static method in PermissionsTable
     *
     * @param array|\ArrayAccess|null $user The user data from the auth component
     * @return bool
     */
    public function isAuthorized($user)
    {

        // 1. KEEP IN MIND: This function is bypassed by Auth->allow() / Auth->deny()

        // 2. Check hardwired permissions in $authorized
        if ($this->hasWiredPermission($user)) {
            return true;
        }

        // 3. Check permission database
        return $this->hasGrantedPermission($user);
    }

    /**
     * Select project database by parameter or query parameter
     *
     * @return void
     */
    protected function _selectDatabase()
    {
        $user = $this->Auth->user();
        $db_allowed = $this->getAllowedDatabases($user);

        $db_selected = Databank::addPrefix(
            $this->request->getParam('database') ?? $this->request->getQuery('database') ?? false
        );

        $this->activeDatabase = $db_selected ? ($db_allowed[$db_selected] ?? false) : false;

        if ($db_selected && !$this->activeDatabase) {
            // First try with stripping the database query parameter
            if (!empty($this->request->getQuery('database'))) {
                $this->Answer->redirect($this->getStrippedDatabaseUrl());
            }

            // Second, redirect to login
            else {
                $this->Answer->error(
                    __('You are not allowed to access the selected database.'),
                    $this->getLoginUrl()
                );
            }
        }

        if ($this->activeDatabase) {
            BaseTable::setDatabase($this->activeDatabase['name']);
        }

        $this->set('database', $this->activeDatabase);
    }

    /**
     * Initialize user settings
     *
     * //TODO: always pass User entity to controller, templates and js
     *
     * @return void
     */
    protected function _initUser()
    {
        // Pass user record to view, update activity flags and update session object
        $user = $this->Auth->user();
        if ($user) {
            $users = TableRegistry::getTableLocator()->get('Users');

            try {
                $user_db = $users->updateActive($user);
            } catch (RecordNotFoundException $e) {
                $this->Auth->logout();
                $this->Answer->redirect(['controller' => 'Users', 'action' => 'login']);
            }

            if (
                ($user['modified'] ?? false) != ($user_db['modified'] ?? false) ||
                ($user['lastaction'] ?? false) != ($user_db['lastaction'] ?? false)
            ) {
                $this->Auth->setUser($user_db);
            }
        }

        // Pass to controllers
        $activeDatabase = $this->getRequest()->getParam('database');
        $selectedDatabase = $this->getRequest()->getParam('database') ?? $this->getRequest()->getQuery('database');
        $this->userRole = $this->_getUserRole($user, $activeDatabase);
        $this->userDbRole = $this->_getUserRole($user, $selectedDatabase);
        $this->requestScope = $this->_getRequestScope();
        $this->requestMode = $this->_getRequestMode();
        $this->requestAction = $this->_getRequestAction();
        $this->requestPreset = $this->_getRequestPreset();
        $this->requestPublished = $this->_getRequestPublished();

        // Pass to view / templates
        $this->set('user_role', $this->userRole);
        $this->set('user_dbrole', $this->userDbRole);
        $this->set('user_scope', $this->requestScope);

        // Pass to model
        BaseTable::$user = $user;
        BaseTable::$requestScope = $this->requestScope;
        BaseTable::$requestMode = $this->requestMode;
        BaseTable::$requestPreset = $this->requestPreset;
        BaseTable::$requestPublished = $this->requestPublished;
        BaseTable::$requestAction = $this->requestAction;
        BaseTable::$userRole = $this->userRole;
        BaseTable::$userId = $this->_getUserId($user);
        BaseTable::$userIri = $this->_getUserIri($user);
        BaseTable::$userSettings = $this->Actions->getUserSettings();

        // Pass to JavaScript
        $this->addToJsUser(['role' => $this->userRole, 'scope' => $this->requestScope]);

        // Pass to view
        $this->set('user', $user);
    }

    /**
     * Load user settings
     *
     * //TODO @return void
     * @deprecated Refactor to use applyUserSettings, save widths in query parameter?
     *
     * Load user settings based on plugin, controller and action.
     * Pass the settings to JavaScript (see models.js)
     * and to the view class.
     *
     *
     */
    protected function _loadUserSettings()
    {
        // Get column sizes from user settings
        $action = $this->request->getParam('action');
        if ($action === 'index') {
            $configKey = Inflector::tableize($this->modelClass ?? $this->name);
            $columns = $this->Actions->getUserSettings('columns', $configKey);
            $settings = ['settings' => ['columns' => [$configKey => $columns]]];

            $this->Answer->addOptions($settings);
            $this->addToJsUser($settings);
        }

        if (in_array($action, ['index', 'show', 'view', 'edit'])) {
            $ui = $this->Actions->getSessionSettings('ui');
            // TODO: filter by current controller and action
            $settings = ['session' => ['ui' => $ui]];

            $this->Answer->addOptions($settings);
            $this->addToJsUser($settings);
        }

    }

    /**
     * Redirect user settings
     *
     * If the request params are empty, redirect to the URL
     * saved in the user settings (= recover the last search settings).
     *
     * @return void
     * @deprecated
     *
     */
    protected function redirectToUserSettings()
    {
        // No redirect for API calls
        if ($this->request->is('api')) {
            return;
        }

        // TODO: don't redirect or update settings for sidebar calls
        //$template = $requestParams['template'] ?? '';

        $requestParams = $this->request->getQueryParams();
        $requestPath = $this->request->getPath();

        // Should we redirect? Only if no parameters (despite redirect) are set
        $redirect = $requestParams['redirect'] ?? true;
        unset($requestParams['redirect']);
        $redirect = $redirect && !$requestParams;

        if ($redirect) {
            $params = $this->Actions->getUserSettings('paths', $requestPath);
            $params = array_diff_key($params, array_flip($this->paramsForNavigation));

            if ($params) {
                $url = $this->request->getParam('pass');
                $url['?'] = $params;
                $this->Answer->redirect($url);
            }
        }

        else {
            $requestParams = array_diff_key($requestParams, array_flip($this->paramsForNavigation));
            if ($requestParams) {
                $this->Actions->updateUserSettings('paths', $requestPath, $requestParams);
            }
        }
    }

    /**
     * Reconnect models to a currently activated database
     *
     * After activating a new database you need to reconnect the models
     * to work inside the new database.
     *
     * @param string $property e.g. 'Properties'
     *
     * @return \Cake\Datasource\RepositoryInterface|null
     */
    protected function reconnectModel($property)
    {
        $this->{$property} = FactoryLocator::get('Table')->get(
            $this->{$property}->getRegistryAlias()
        );

        return $this->{$property};
    }

    /**
     * Merge the current data into the JS user array
     *
     * Makes the data available in the JavaScript variable App.user.
     *
     * @param array $data Data
     */
    protected function addToJsUser($data)
    {
        $this->js_user = array_merge($this->js_user, $data);
    }

    /**
     * Create a job and start job execution
     *
     * //TODO: put into a dedicated Job component
     *
     * @param $type
     * @param $config
     *
     * @return void
     */
    protected function startJob($type, $config)
    {

        $jobdata = ['typ' => $type, 'config' => $config];
        $jobsTable = $this->fetchTable('Jobs');
        $job = $jobsTable->newEntity($jobdata);

        if ($jobsTable->save($job)) {
            $this->Answer->success(
                __('The job was created'),
                [
                    'plugin' => false,
                    'controller' => 'Jobs',
                    'action' => 'execute',
                    $job->id,
                    '?' => ['database' => $job->config['database']]
                ]
            );

        }
        else {
            $this->Answer->error(
                __('The job could not be created. Please, try again.')
            );
        }
    }

    /**
     * Initialize view cache
     *
     * @return string|null The config name
     */
    protected function initViewCache()
    {
        if (empty($this->_cacheConfigName)) {
            $configName = 'epi_views_' . $this->plugin . '_' . $this->name;
            $this->_cacheConfigName = $configName;
            Cache::initCache($configName, 'views');
        }

        if (Configure::read('debug')) {
            Cache::clear($this->_cacheConfigName);
        }

        return $this->_cacheConfigName;
    }

    /**
     * Clear view cache
     *
     * @return void
     */
    protected function clearViewCache()
    {
        $this->initViewCache();
        Cache::clearCache($this->_cacheConfigName);
    }

    /**
     * Get cache key
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return (BaseTable::$userId ?? 'public') . '_' . md5($this->request->getRequestTarget());
    }
}
