<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace Widgets\View\Helper;

use App\Model\Table\PermissionsTable;
use App\Utilities\Converters\Attributes;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\Routing\Router;
use Epi\Model\Entity\Article;
use Epi\Model\Entity\BaseEntity;
use Epi\Model\Entity\RootEntity;
use Rest\Entity\LockInterface;

/**
 * Link helper
 *
 * Render links and buttons in the frontend.
 *
 * @property Helper\HtmlHelper $Html
 * @property ElementHelper $Element
 */
class LinkHelper extends Helper
{

    /**
     * Load helpers
     *
     * @var string[]
     */
    public $helpers = ['Element','Html', 'Url', 'Form', 'Paginator'];

    /**
     * Array of action buttons
     *
     * First level key indicates the position:
     * - top = next to breadcrumbs (e.g. for quick actions)
     * - right = right sidebar (e.g. for save and cancel buttons)
     * - bottom = footer of content (e.g. JSON or XML buttons)
     * - content = below the content (e.g. Yes, No, Confirm buttons)
     * First level value contains an array of button groups
     *
     * Second level key indicates the group name
     * Second level value contains an array with options:
     * - position = top|right|bottom|content (see above)
     * - type = list|bar|dropdown
     * - title = caption above button list or caption of the dropdown button
     * - actions = array of buttons
     *
     * Group types:
     * - list = vertical list
     * - bar = horizontal list
     * - dropdown = dropdown button
     *
     * @var array
     */
    public $groups = [];


    /**
     * Position of the current group
     *
     * See beginActionGroup and endActionGroup.
     *
     * @var string
     */
    public $currentPosition = 'bottom';

    /**
     * Name of the current group
     *
     * See beginActionGroup and endActionGroup.
     *
     * @var string
     */
    public $currentGroup = 'default';

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Open a group of action buttons
     *
     * @param string $position
     * @param array $options
     *
     * @return void
     */
    public function beginActionGroup(string $position = 'right', $options = [])
    {
        $this->currentPosition = $position;
        $this->currentGroup = $options['group'] ?? 'default';

        if (!isset($this->groups[$position])) {
            $this->groups[$position] = [];
        }

        if (!isset($this->groups[$position][$this->currentGroup])) {
            $options = array_merge(['type' => 'list', 'title' => __('Actions'), 'actions' => []], $options);
            $this->groups[$position][$this->currentGroup] = $options;
        }
    }

    /**
     * Close a group of action buttons
     *
     * @return void
     */
    public function endActionGroup()
    {
        $this->currentPosition = 'bottom';
        $this->currentGroup = 'default';
    }

    /**
     * Add an action to an action group
     *
     * @param string $title
     * @param array|string $url
     * @param array $options Options for the button with the following keys:
     *  - title The button text
     *  - url The linked URL
     *  - linktype: post, submit, button, cancel, checkbox.
     *              Generates an a-element, if empty and a URL is provided.
     *              Generates a label, if empty and no URL is provided.
     *  - value: Content of the data-value attribute
     *  - shortcuts: Shortcuts, e.g. 'Alt+x'
     *
     * @return void
     */
    public function addAction(string $title, $url, array $options = [])
    {
        $position = $this->currentPosition;
        $group = $this->currentGroup;

        if (!isset($this->groups[$position])) {
            $this->groups[$position] = [];
        }

        if (!isset($this->groups[$position][$group])) {
            $this->beginActionGroup($position, $group);
        }

        $this->groups[$position][$group]['actions'][] = ['title' => $title, 'url' => $url, 'options' => $options];
    }

    /**
     * Add create action
     *
     * @param string|null $caption
     * @param array $options
     * @param array $url
     * @return void
     */
    public function addCreateAction($caption = null, $options = [], $url=['action'=>'add'])
    {
        $options += ['popup' => true];
        if ($options['popup'] ?? false) {
            $options['class'] = 'popup actions-set-default';
            $options['data-popup-modal'] = true;
        }
        $options = array_merge(
            [
                'data-role' => 'add',
                'shortcuts' => ['Ctrl+M'] // TODO: popup not working with shortcuts, why?
            ],
            $options
        );

        $this->addAction($caption, $url, $options);
    }

    /**
     * Create a form submit button
     *
     * @param string|null $caption
     * @param array $options
     * @return void
     */
    public function addSubmitAction($caption = null, $options = [])
    {
        $options = array_merge(
            [
                'linktype' => 'submit',
                'shortcuts' => ['Ctrl+S', 'F10']
            ],
            $options
        );

        $this->addAction(
            $caption ?? __('Submit'),
            [],
            $options
        );
    }

    /**
     * Add a cancel button
     *
     * TODO: track pages and go back one step
     *
     * @param array|null $url The URL to redirect.
     * @param string|null $target The value for the data-target attribute, e.g. articles-123
     * @return void
     */
    public function addCancelAction($url = null, $target=null)
    {
        // Disallow external referrers
        // TODO: disallow all protocols, not only http
        if ($url && is_string($url) && str_starts_with($url,'http')) {
            $url = null;
        }

        if ($url) {
            $this->addAction(
                __('Cancel'),
                $url,
                [
                    'shortcuts' => ['Ctrl+Q'],
                    'class' => 'button button_cancel',
                    'data-role'=>'cancel',
                    'data-target' => $target
                ]
            );
        }
    }

    /**
     * Add an edit button, and optionally the unlock button
     *
     * ### Options
     * - roles: array of roles that are allowed to edit the entity
     *
     * @param \App\Model\Entity\BaseEntity $entity
     * @param array $options Passed to the addAction() method
     * @return void
     */
    public function addEditButtons($entity, $options = [])
    {
        $target = $entity->tableName . '-' . $entity->id;

        $editAction = ['action' => 'edit', $entity->id];
        $queryParams = $this->_View->getRequest()->getQueryParams();
        if (!empty($queryParams)) {
            $editAction['?'] = $queryParams;
        }

        $this->beginActionGroup ('bottom');

        $this->addAction(
            __('Edit'),
            $editAction,
            [
                'shortcuts' => ['F2'],
                'data-role'=>'edit',
                'data-target'=> $target
            ] + $options
        );

        if (($entity instanceof LockInterface) && $entity->isLockedByUser()) {
            $this->addAction(
                __('Unlock'),
                [
                    'action' => 'unlock',
                    $entity->id,
                    '?' => ['force' => true, 'redirect' => 'edit']
                ],
                [
                    'type' => 'post',
                    'data-role'=>'unlock',
                    'data-target' => $target
                ] + $options
            );
        }
    }
    /**
     * Add an edit button
     *
     * @deprecated Use addEditButtons() instead
     *
     * @param string $target The value for the data-target attribute, e.g. articles-123
     * @return void
     */
    public function addEditAction($url, $target)
    {
        return $this->addAction(
            __('Edit'),
            $url,
            [
                'shortcuts' => ['F2'],
                'data-role'=>'edit',
                'data-target'=> $target
            ]
        );
    }

    /**
     * Add a save button
     *
     * // TODO: replace, only used once
     *
     * The table prefixed id will be added as data-target attribute of links and forms,
     * so that after saving a hash fragment or hash parameter to all those links
     * can be added to stay in the current section of the record, see layout.js -> setActive().
     *
     * @param string $mode 'submit' (save and close) or 'add' (save a new record and close)
     * @param string $table The table name
     * @param integer|null $id The record ID or null for new records
     * @return void
     */
    public function addSaveButton($mode, $table, $id=null)
    {
        $target = isset($id) ? $table . '-' . $id : $table;
        $options =  [
            'linktype' => 'submit',
            'form' => 'form-' . ($mode === 'add' ? 'add' : 'edit') . '-' . $target,
            'data-role' => 'submit'
        ];

        if ($mode !== 'add') {
            $options['data-target'] = $target;
        }

        $options['shortcuts'] = ['Ctrl+S', 'F10'];
        $this->addAction(__('Save'), [], $options);
    }

    /**
     * Add save and cancel buttons for new entities
     *
     * @param \App\Model\Entity\BaseEntity $entity
     */
    public function addAddCancelButton($entity)
    {
        // Cancel
        $target = $entity->tableName;
        $form = 'form-add-' . $target;

        // Save
        $options =  [
            'linktype' => 'submit',
            'form' => $form,
            'data-role' => 'submit',
            'shortcuts' => ['Ctrl+S', 'F10']
        ];
        $this->addAction(__('Save'), [], $options);

        // Cancel
        $options =  [
            'linktype' => 'button',
            'type' => 'button',
            'form' => $form,
            'data-role' => 'cancel',
            'shortcuts' => ['Ctrl+Q']
        ];

        $this->addAction(__('Cancel'), [], $options);
    }

    /**
     * Add save, cancel and delete buttons
     *
     * ### Options
     * - delete: boolean, add a delete button (default: true)
     * - close: boolean, show a close (true) or a cancel button (default: false)
     * - action: string, the action name used for the form ID (default: 'edit')
     *
     * @param \App\Model\Entity\BaseEntity $entity
     * @param array $options
     * @return void
     */
    public function addSaveCancelDeleteButton($entity, $options=[])
    {
        $target = $entity->tableName . '-' . $entity->id;
        $form = 'form-' . ($options['action'] ?? 'edit') . '-' . $target;

//        $options =  [
//            'linktype' => 'submit',
//            'form' => 'form-edit-' . $target,
//            'data-target' => $target
//        ];
//        $this->addAction(__('Save'), ['?' => ['redirect' => 'edit']], $options + ['shortcuts' => ['Ctrl+S'], 'data-role' => 'save']);

        // Save and close
        $buttonOptions =  [
            'linktype' => 'submit',
            'form' => $form,
            'data-role' => 'save',
            'shortcuts' => ['Ctrl+S', 'F10']
        ];
        $buttonOptions['data-target'] = $target;

        $this->addAction(
             __('Save'),
            [], $buttonOptions
        );

        // Cancel
        $buttonOptions =  [
            'linktype' => 'button',
            'type' => 'button',
            'form' => $form,
            'data-role' => 'cancel',
            'shortcuts' => ['Ctrl+Q']
        ];

        $buttonOptions['data-target'] = $target;

        $closeCaption = $options['close'] ?? false ? __('Close') : __('Cancel');
        $this->addAction($closeCaption, [], $buttonOptions);

        // Delete
        if (($entity instanceof RootEntity) && $entity->hasDependencies) {
            $options['delete'] = false;
        }

        if ($options['delete'] ?? true) {
            $buttonOptions = [
                'linktype' => 'button',
                'type' => 'button',
                'form' => $form,
                'data-role' => 'delete'
            ];

            $buttonOptions['data-target'] = $target;
            $this->addAction(__('Delete'), [], $buttonOptions);
        }
    }

    /**
     * Output a confirm and a cancel button for delete actions
     *
     * @param \App\Model\Entity\BaseEntity $entity
     * @return string
     */
    public function getConfirmDeleteButtons($entity)
    {
        $out = '';

        // Confirm
        $target = $entity->tableName . '-' . $entity->id;
        $form = 'form-delete-' . $target;

        $options =  [
            'linktype' => 'button',
            'type' => 'submit',
            'form' => $form,
            'data-role' => 'confirm'
        ];

        $options['data-target'] = $target;

        $action = [
            'title' => __('Yes'),
            'url' => [],
            'options' => $options
        ];

        $out .= $this->renderActionButton( $action);

        $out .= "\n";

        // Cancel
        $options =  [
            'linktype' => 'button',
            'type' => 'button',
            'form' => $form,
            'data-role' => 'cancel',
            'shortcuts' => ['Ctrl+Q']
        ];

        $options['data-target'] = $target;

        $action = [
            'title' => __('Cancel'),
            'url' => [],
            'options' => $options
        ];

        $out .= $this->renderActionButton( $action);

        return $out;
    }

    /**
     * Output a confirmation and a cancel button for merge actions
     *
     * @param \App\Model\Entity\BaseEntity $entity The first source entity
     * @return void
     */
    public function addMergeConfirmButtons($entity)
    {
        $this->addAction(
            __('Yes'),
            [],
            ['linktype' => 'submit', 'form' => 'form-merge-properties']
        );

        $this->addAction(
            __('No'),
            ['controller' => 'Properties', 'action' => 'view', $entity->id],
            [
                'data-role' => 'cancel',
                'form' => 'form-merge-properties',
                'linktype' => 'button',
                'type' => 'button'
            ]
        );

    }

    /**
     * Add a preview and a cancel button for merge actions
     *
     * @param \App\Model\Entity\BaseEntity $entity The first source entity
     * @return void
     */
    public function addMergePreviewButtons($entity)
    {

        $this->addAction(
            __('Preview'),
            [],
            [
                'data-role' => 'submit',
                'form' => 'form-merge-properties',
                'linktype' => 'submit'
            ]
        );
        $this->addAction(
            __('Cancel'),
            ['controller' => 'Properties', 'action' => 'view', $entity->id],
            [
                'data-role' => 'cancel',
                'form' => 'form-merge-properties',
                'linktype' => 'button',
                'type' => 'button'
            ]
        );
    }

    /**
     * Add a save button
     *
     * @param string $target The table prefixed id of the record, e.g. articles-123 for edit mode,
     *                       or the table name for add mode.
     *
     *                       The table prefixed id will be added as data-target attribute of links and forms,
     *                       so that after saving a hash fragment or hash parameter to all those links
     *                       can be added to stay in the current section of the record, see layout.js -> setActive().
     *
     * @return void
     */
    public function addQuickSaveButtons($entity)
    {
        $target = $entity->tableName . '-' . $entity->id;
        $form = 'form-edit-' . $target;

        $target = isset($id) ? $table . '-' . $id : $table;
        $options =  [
            'linktype' => 'submit',
            'form' => 'form-edit-' . $target,
            'data-target' => $target
        ];

        $this->addAction(__('Save'), [], $options + ['shortcuts' => ['F10', 'Ctrl+S'], 'data-role' => 'submit']);
//        $this->addAction(__('Save'), ['?' => ['redirect' => 'edit']], $options + ['shortcuts' => ['Ctrl+S'], 'data-role' => 'save']);
    }

    /**
     * Add a delete button
     *
     * @param array $url
     * @param array $options
     * @return void
     */
    public function addDeleteAction($url, $options= [])
    {
        $options['class'] = 'button button_cancel' . (empty($options['class']) ? '' : (' ' . $options['class']));
        $options['data-role'] = 'delete';

        $this->addAction(__('Delete'), $url, $options);
    }


    /**
     * Add a link to the help system in the footer, based on plugin, controller and action
     *
     * @return void
     */
    public function addHelpAction()
    {
        $request = $this->_View->getRequest();
        $norm_iri = implode('-', array_filter([
            'epiweb',
            Inflector::underscore($request->getParam('plugin') ?? ''),
            Inflector::underscore($request->getParam('controller') ?? ''),
            Inflector::underscore($request->getParam('action') ?? '')
        ]));

        $this->beginActionGroup('bottom-right');
        $this->addAction(
            '?',
            [
                'plugin' => false,
                'controller' => 'Help',
                'action' => 'show',
                $norm_iri
            ],
            [
                'class' => 'frame help-button',
                'data-frame-target'=>'help',
                'data-frame-caption' => __('Help'),
                'title' => __('Help'),
                'aria-label' => __('Help')
            ]);
    }


    /**
     * Add a toggle to the action group
     *
     * @param string $title
     * @param array $options
     *
     * @return void
     */
    public function addToggle(string $title, array $options = [])
    {
        $position = $this->currentPosition;
        $group = $this->currentGroup;

        if (!isset($this->groups[$position])) {
            $this->groups[$position] = [];
        }

        if (!isset($this->groups[$position][$group])) {
            $this->beginActionGroup($position, $group);
        }

        $options['linktype'] = 'button';
        $options['class'] = 'button-toggle ' . ($options['class'] ?? '');

        $this->groups[$position][$group]['actions'][] = ['title' => $title, 'url' => false, 'options' => $options];
    }

    /**
     * Create buttons to switch the templates
     *
     * // TODO: use permissions, introduce permission prefix 'template'
     * @param array $queryparams Query parameters, a template-Parameter will be added if not present
     *
     * @return void
     */
    public function toggleTemplates($queryparams, $datalist='')
    {
        $items = [
            'table' => ['caption' => __('Table'), 'options' => ['roles' => ['desktop', 'coder', 'author', 'editor']]],
            'map' => [
                'caption' => __('Map'),
                'params' => ['sort' => 'distance', 'direction' => 'asc'],
                'options' => ['roles' => ['author', 'editor']]
            ],
            'tiles' => ['caption' => __('Tiles'), 'options' => ['roles' => ['desktop', 'coder', 'author', 'editor']]],

            'lanes' => ['caption' => __('Lanes'), 'options' => ['roles' => []]]
        ];

        $activeItem = $queryparams['template'] ?? 'table';
        $lastItem = empty($items) ? 'default' : array_key_last($items);

        foreach ($items as $name => $options) {
            $queryparams['template'] = $name;
            $active = $name === $activeItem;
            $last = $name === $lastItem;

            $queryparams = array_merge($queryparams, $options['params'] ?? []);

            $actionOptions = $options['options'] ?? [];
//            if ($datalist !== '') {
//                $actionOptions = array_merge($actionOptions,[ 'data-list-select' => $datalist, 'data-list-param' => $datalist]);
//            }
            $actionOptions['active'] = $active;
            $actionOptions['class'] = 'toggle ';
            $actionOptions['class'] .= $active ? 'toggle-active' : 'toggle-inactive';
            $actionOptions['group'] = 'templates';
            $actionOptions['group-last'] = $last;

            $this->addAction($options['caption'], ['?' => $queryparams], $actionOptions);
        }
    }

    /**
     * Create buttons to switch the modes
     *
     * // TODO: use permissions, introduce permission prefix 'template'
     * @param array $params Query parameters, a mode-Parameter will be added if not present
     * @param string|null $entityId The entity ID
     * @param array $allowed List of allowed modes or empty
     *
     * @return void
     */
    public function toggleModes($params, $entityId=null, $allowed = [])
    {
        $items = [
            'default' => ['caption' => __('Read'), 'options' => ['roles' => ['desktop', 'coder', 'author', 'editor']]],
            'code' => ['caption' => __('Revise'), 'options' => ['roles' => ['desktop', 'coder', 'author', 'editor']]]
        ];
        if (!empty($allowed)) {
            $items = array_intersect_key($items,array_flip($allowed));
        }

        $activeMode = $params['mode'] ?? 'default';
        $lastMode = empty($items) ? 'default' : array_key_last($items);

        foreach ($items as $name => $options) {
            $active = $name === $activeMode;
            $last = $name === $lastMode;

            $params['mode'] = $name;
            $params = array_merge($params, $options['params'] ?? []);

            $actionOptions = $options['options'] ?? [];
            $actionOptions['active'] = $active;
            $actionOptions['class'] = 'toggle';
            $actionOptions['class'] .= $active ? ' toggle-active' : ' toggle-inactive';
            $actionOptions['group'] = 'modes';
            $actionOptions['group-last'] = $last;

            if ($params['mode'] === 'default') {
                unset($params['mode']);
            }
            $this->addAction($options['caption'], [$entityId, '?' => $params], $actionOptions);
        }
    }

    /**
     * Add download buttons for xsv, json and xml
     *
     * The initial parameters are updated by Javascript when
     * filter widgets of a datalist are changed.
     *
     * ### Options
     * - triples Boolean value to indicate if triple buttons should be displayed.
     *           By default, triple buttons are displayed if the entity has a type with triple configuration.
     *
     * @param array|null $url The URL. If empty, query parameters will be retrieved from the config.
     *                        TODO: deprecated, remove $url parameter, pass $entity as in addEditButtons()
     * @param string $parameter The URL parameter handling selected IDs (e.g. "articles")
     * @param string $datalist Name of the datalist, e.g. "epi_articles"
     * @param array $options
     * @return void
     */
    public function downloadButtons($url = null, $parameter= '', $datalist='', $options=[])
    {
        if (empty($url)) {
            $action = $this->getView()->getRequest()->getParam('action');
            $url = ['action' => Attributes::cleanOption($action,['index', 'view'], 'view')];

            // Scope
            $scopeField = $this->_View->getConfig('options')['scopefield'] ?? null;
            $scope = isset($scopeField) ? $this->_View->getConfig('options')['params'][$scopeField] ?? null : null;
            if (isset($scope)) {
               $url[] = $scope;
            }

            // Entity ID
            $entity = $this->_View->get('entity');
            if (!empty($entity)) {
                $url[] = $entity->id;

                // Skip download links in right sidebar
                if  ($this->_View->getRequest()->is('ajax')) {
                    return;
                }
            }

            // Query parameters
            $url['?'] = Attributes::paramsToQueryString(
                $this->_View->getConfig('options')['params'] ?? [],
                ['collapsed', 'selected', 'template', 'action', $scopeField]
            );
        }

        $this->beginActionGroup('bottom-right');

        // Epigraf data
        $this->addAction(__('CSV'),
            array_merge($url,['_ext' => 'csv']),
            ['target' => '_blank', 'rel' => 'nofollow', 'group' => 'export', 'data-list-select' => $datalist, 'data-list-param' => $parameter]
        );
        $this->addAction(__('XML'),
            array_merge($url,['_ext' => 'xml']),
            ['target' => '_blank', 'rel' => 'nofollow', 'group' => 'export', 'data-list-select' => $datalist, 'data-list-param' => $parameter]
        );
        $this->addAction(__('JSON'),
            array_merge($url,['_ext' => 'json']),
            ['target' => '_blank', 'rel' => 'nofollow', 'group' => 'export', 'data-list-select' => $datalist, 'data-list-param' => $parameter]
        );

        // Linked data
        $tripleButtons = $options['triples'] ?? !empty($entity) && !empty($entity->type->config['triples']);
        if ($tripleButtons) {
            $this->addAction(__('TTL'),
                array_merge($url,['_ext' => 'ttl']),
                ['target' => '_blank', 'rel' => 'nofollow', 'group' => 'export', 'data-list-select' => $datalist, 'data-list-param' => $parameter]
            );

            $this->addAction(__('JSON-LD'),
                array_merge($url,['_ext' => 'jsonld']),
                ['target' => '_blank', 'rel' => 'nofollow', 'group' => 'export', 'data-list-select' => $datalist, 'data-list-param' => $parameter]
            );

            $this->addAction(__('RDF'),
                array_merge($url,['_ext' => 'rdf']),
                ['target' => '_blank', 'rel' => 'nofollow', 'group' => 'export', 'data-list-select' => $datalist, 'data-list-param' => $parameter]
            );
        }
    }

    /**
     * Add export buttons
     *
     * In the article list, leave $article empty.
     * In the article view, hand over the article.
     *
     * @param array $queryparams Additional query parameters
     * @param null|Article $article Hand over an article to create specific pipeline buttons
     * @return void
     */
    public function exportButtons($queryparams, $article=null)
    {

        $action = $this->_View->getRequest()->getParam('action');
        if (in_array($action, ['view', 'index'])) {
            $dataParams = [
                'class'=>'popup',
                'data-popup-modal' => true,
                'shortcuts' => ['F7'],
                'rel' => 'nofollow',
                'group' => 'export',
            ];
        } else {
            $dataParams = [
                'shortcuts' => ['F7'],
                'rel' => 'nofollow',
                'group' => 'export',
                'target' => '_blank'
            ];
        }

        // Get parameters from the article list selection
        if (empty($article)) {
            $dataParams['data-list-select'] = 'epi_articles';
            $dataParams['data-list-param'] = 'articles';
        }

        // General export (F7)
        $this->addAction(__('Export'),
            ['plugin' => false, 'controller' => 'Jobs', 'action' => 'add', '?' => $queryparams],
            $dataParams
        );

        // Quick export (F6)
        if (!empty($article)) {
            $pipelines = $article->type->config['pipelines'] ?? [];
            $index = 0;
            foreach ($pipelines as $pipelineIri => $pipelineConfig) {
                if (is_string($pipelineConfig)) {
                    $pipelineConfig = ['caption' => $pipelineConfig];
                }
                if (!($pipelineConfig['button'] ?? true) || !is_string($pipelineIri)) {
                    continue;
                }



                if (in_array($action, ['view', 'index'])) {
                    $buttonOptions = [
                        'class' => 'popup',
                        'data-popup-modal' => true,
                        'rel' => 'nofollow',
                        'group' => 'export',
                    ];
                } else {
                    $buttonOptions = [
                        'rel' => 'nofollow',
                        'group' => 'export',
                        'target' => '_blank'
                    ];
                }

                if ($index === 0) {
                    $buttonOptions['shortcuts'] = ['F6'];
                }

                $this->addAction(
                    $pipelineConfig['caption'] ?? $pipelineIri,
                    [
                        'plugin' => false,
                        'controller' => 'Jobs',
                        'action' => 'download',
                        '?' => $queryparams + [
                            'scope' => 'article',

                            'projects' => $article->project->id ?? null,
                            'articles' => $article->id,
                            'pipeline' => $pipelineIri
                        ]
                    ],
                    $buttonOptions
                );

                $index++;
            }
        }
    }

    /**
     * Create a link to the help system for the given norm_iri
     *
     * @param string $norm_iri
     * @param array $data If the page does not exist, it will be created with the provided data
     *
     * @return mixed
     */
    public function helpLink(string $norm_iri, array $data = [])
    {
        return
            $this->Html->link(
                '?',
                [
                    'plugin' => false,
                    'controller' => 'Help',
                    'action' => 'show',
                    'iri' => $norm_iri,
                    '?' => $data
                ],
                [
                    'class' => 'frame help-button',
                    'data-frame-target'=>'help',
                    'data-frame-caption' => __('Help'),
                    'title' => __('Help'),
                    'aria-label' => __('Help')
                ]
            );
    }

    /**
     * Add an action button
     *
     * @param array $options
     *
     * @return void
     */
    public function addButton(array $options)
    {
        $options['linktype'] = 'button';
        $options['type'] = '';
        $this->addAction('', '', $options);
    }

    /**
     * Add an action label
     *
     * @param string $text
     * @param array $options
     *
     * @return void
     */
    public function addLabel(string $text, array $options = [])
    {
        $position = $this->currentPosition;
        $group = $this->currentGroup;

        if (!isset($this->groups[$position])) {
            $this->groups[$position] = [];
        }

        if (!isset($this->groups[$position][$group])) {
            $this->beginActionGroup($position, $group);
        }

        $this->groups[$position][$group]['actions'][] = ['title' => $text, 'url' => false, 'options' => $options];
    }

    /**
     * Add a label with the number of records
     *
     * @return void
     */
    public function addCounter()
    {
        $this->addLabel(
            $this->_View->Paginator->counter('{{count}}') . __(' records'),
            ['class' => 'actions-set-default']
        );
    }

    /**
     * Returns whether actions were added to the given position
     *
     * @param string $position right|top|bottom|content
     *
     * @return bool
     */
    public function hasActions(string $position): bool
    {
        return !empty($this->groups[$position] ?? []);
    }

    /**
     * Render a single action button
     *
     * ### Action array options
     *  - linktype: post, submit, button, cancel, checkbox.
     *              If empty and we have an URL, an a tag is generated.
     *              If empty without an URL, a label is generated.
     *  - value
     *  - shortcuts
     * @param array $action An array with the keys title, URL, and options.
     * @return string
     */
    public function renderActionButton($action)
    {
        $linkType = $action['options']['linktype'] ?? false;
        unset($action['options']['linktype']);

        // Split shortcuts
        $shortCuts = $action['options']['shortcuts'] ?? [];
        $shortCuts = $shortCuts ? [
            'data-shortcuts' => implode(' ', $shortCuts),
            'title' => $action['title'] . ' (' . implode(' ',$shortCuts) . ')',
            'aria-label' => $action['title'],
            'aria-keyshortcuts' => implode(' ',$shortCuts),
            'class' => 'widget-shortcut'
        ] : [];
        unset($action['options']['shortcuts']);


        $buttonOptions = $action['options'] ?? [];
        unset($buttonOptions['group']);
        unset($buttonOptions['group-last']);

        if ($linkType === 'post') {
            $out = $this->Form->postLink($action['title'], $action['url'], $action['options'] ?? []);
        }
        elseif ($linkType === 'submit') {
            $buttonOptions['data-role'] = $buttonOptions['data-role'] ?? 'submit';
            $buttonOptions['class'] = implode(' ', array_filter(
                [
                    $buttonOptions['class'] ?? '',
                    $shortCuts['class'] ?? '',
                ]
            ));
            $buttonOptions = array_merge($shortCuts, $buttonOptions);

            $out = $this->Form->button($action['title'], $buttonOptions);
        }
        elseif ($linkType === 'button') {
            $buttonOptions['class'] = implode(' ', array_filter(
                [
                    $buttonOptions['class'] ?? '',
                    $shortCuts['class'] ?? '',
                ]
            ));
            $buttonOptions = array_merge($shortCuts, $buttonOptions);
            $out = $this->authButton($action['title'], $buttonOptions);
        }
        elseif ($linkType === 'cancel') {
            $out = $this->cancelLink($action['url']);
        }
        elseif ($linkType === 'checkbox') {
            $action['options']['type'] = 'checkbox';
            $action['options']['label'] = $action['title'];
            $out = $this->Form->control($action['name'], $action['options']);
        }
        elseif (!($action['url'] ?? false)) {
            $labelClasses = [
                'label',
                $action['options']['class'] ?? null
            ];

            $out = $this->Element->outputHtmlElement(
                'span',
                $action['title'] ?? '',
                ['class' => $labelClasses]
            );
        }
        else {
            $buttonOptions['class'] = implode(' ', array_filter(
                [
                    $buttonOptions['class'] ?? '',
                    $shortCuts['class'] ?? '',
                ]
            ));
            $buttonOptions = array_merge($shortCuts, $buttonOptions);

            $out = $this->authLink($action['title'], $action['url'], $buttonOptions);
        }
        return $out;
    }
    /**
     * Create a list of links, checkboxes or buttons
     *
     * @param array $actions Array of action items. See renderActionButton().
     * @param array $options Array of options (possible keys: class)
     *
     * @return string
     */
    public function renderActionList(array $actions, array $options = []): string
    {
        $out = '';

        $out .=
            '<ul class="' . ($options['class'] ?? '') . '"' .
            (isset($options['id']) ? (' id="' . $options['id'] . '"') : '') .
            '>';

        foreach ($actions as $action) {
            $contents = $this->renderActionButton($action);

            if (!empty($contents)) {

                $attributes = [];
                $actionGroup = $action['options']['group'] ?? '';
                if ($actionGroup !== '') {
                    $attributes['class'] = 'action-group';
                    $attributes['class'] .=  ' action-group-' . $actionGroup;
                    $attributes['class'] .= ($action['options']['group-last'] ?? false) ? ' action-group-last' : '';
                }

                if (isset($action['value'])) {
                    $attributes['data-value '] = $action['value'];
                }

                $out .= $this->Element->outputHtmlElement(
                    'li',
                    $contents,
                    $attributes
                );
            }
        }

        $out .= '</ul>';

        return $out;
    }

    /**
     * Render action group
     *
     * @param string $position
     *
     * @return string
     */
    public function renderActions(string $position): string
    {
        $out = '';

        foreach ($this->groups[$position] ?? [] as $group) {
            $options = [];
            $options['class'] = ($position == 'left' ? 'side-nav' : 'action-menu');
            $out .= $this->renderActionList($group['actions'], $options);
        }

        return $out;
    }



    /**
     * Render actions and wrap them in markup for a sandwich button
     *
     * @param string $itemContainerClass The class name of the container that contains items to be collected
     * @param string $position The group name of the actions (see beginActionGroup)
     *
     * @return string
     */
    public function renderSandwichActions(string $itemContainerClass, string $position)
    {
        return $this->Element->outputHtmlElement(
            'nav',
            $this->renderActions($position),
            [
               'class' => [
                   'actions-' . $position,
                   'widget-sandwich-source',
                   $itemContainerClass,
               ],
                'data-snippet' => 'actions-' . $position,
                'data-sandwich-source' => $position
            ]
        );
    }

    /**
     * Render the sandwich button
     *
     * ### Options
     * - dropdown Class that aligns the dropdown pane to the button, e.g. "topright" or "topleft"
     * - align-y Selector of an element to vertically align the dropdown pane to
     * @param string $itemContainerClass The class name of the containers that contain items to be collected
     * @param array $options
     *
     * @return string
     */
    public function renderSandwichButton(string $itemContainerClass, array $options)
    {
        $dropdownPosition = $options['dropdown'] ?? 'topleft';
        $icon = $options['icon'] ?? "\u{f0c9}"; //  '☰'

        $out = '<nav class="widget-sandwich hidden" data-sandwich-sources=".' . $itemContainerClass . '">';

        $out .= $this->Element->outputHtmlElement(
            'button',
            $icon,
            [
                'class' => 'widget-dropdown widget-dropdown-toggle',
                'data-toggle' => 'actions-' . $itemContainerClass . '-sandwich-pane',
                'data-pane-align-to-y' => $options['align-y'] ?? null,
                'title' => __('More functions'),
                'aria-label' => __('More functions')
            ]
        );

        $out .= '<div id="actions-' . $itemContainerClass . '-sandwich-pane" '
                . 'class="widget-dropdown-pane dropdown-menu" '
                . 'data-widget-dropdown-position="' . $dropdownPosition . '">'
                . '</div>';

        $out .= '</nav>';

        return $out;
    }

    /**
     * Generate a sort link
     *
     * @param string $field The name of the field to sort by.
     * @param string $title The link title.
     *
     * @return string
     */
    public function sortLink(string $field, string $title): string
    {
        $sort = $this->_View->getRequest()->getQuery('sort');
        $direction = $this->_View->getRequest()->getQuery('direction') === 'asc' ? 'desc' : 'asc';
        $query = $this->_View->getRequest()->getQuery();

        $isCurrent = $sort === $field;
        $direction = $isCurrent ? $direction : 'asc';
        $class = null;
        if ($isCurrent) {
            $class = $direction === 'asc' ? 'desc' : 'asc';
        }

        return $this->Html->link(
            $title,
            ['?' => ['sort' => $field, 'direction' => $direction] + $query],
            ['class' => $class]
        );
    }

    /**
     * Assemble URL for next page, used for scroll pagination
     *
     * @param string|null $path The (first) path parameter passed to the action
     * @param bool $forceNext Whether to create the URL without the Paginator component
     * @param array $model Passed to the Paginator helper
     * @return string
     */
    public function nextPageUrl($path = null, $forceNext = false, $model = null)
    {
        $request = $this->_View->getRequest();
        $queryparams = $request->getQueryParams();

        unset($queryparams['page']);
//        unset($queryparams['sort']);
//        unset($queryparams['direction']);

        // Restrict to content (no nav bars and sidebars)
        //$queryparams['show'] = 'content';

        $urlparams = ['?' => $queryparams];

        if (isset ($path)) {
            array_unshift($urlparams, $path);
        }

        $this->Paginator->options(['url' => $urlparams, 'model' => $model]);

        if (!$forceNext) {
            $paging = $this->Paginator->params($model);
        }
        else {
            $paging = [
                'nextPage' => true,
                'page' => (int)$request->getQuery('page', 1)
            ];
        }

        if ($paging['nextPage'] ?? false) {
            return $this->Paginator->generateUrl(['page' => $paging['page'] + 1], $model);
        }
        else {
            return '';
        }
    }

    /**
     * Assemble URL for next page within a lane, used for scroll pagination
     *
     * @param array $params Set the lane parameter
     * @param boolean $firstPage Whether this is the link for the first page in a lane
     * @return string
     */
    public function laneNextPageUrl($params, $firstPage = false)
    {
        $request = $this->_View->getRequest();

        $queryparams = array_merge($request->getQueryParams(), $params);
        //$queryparams = $request->getQueryParams();

        unset($queryparams['page']);
        unset($queryparams['sort']);
        unset($queryparams['direction']);

        // Restrict to content (no nav bars and sidebars)
        //$queryparams['show'] = 'content';
        $urlparams = ['?' => $queryparams];

        $this->Paginator->options(['url' => $urlparams]);


        $paging = $this->Paginator->params();

        if ($firstPage) {
            $paging['nextPage'] = true;
            $paging['page'] = 0;
        }

        $nexturl = ($paging['nextPage'] ?? false) ? $this->Paginator->generateUrl(['page' => $paging['page'] + 1]) : '';
        return $nexturl;
    }

    /**
     * Assemble URL to retrieve a single record
     *
     * @param string|null $path path parameter
     * @param $id
     *
     * @return mixed
     */
    public function getRowUrl($path = null, $id = null)
    {
        $request = $this->getView()->getRequest();

        $queryparams = $request->getQueryParams();
        unset($queryparams['prev_cursor']);
        unset($queryparams['next_cursor']);
        unset($queryparams['page']);
        unset($queryparams['sort']);
        unset($queryparams['direction']);

        $url = $this->Url->build([
            'action' => $request->getParam('action'),
            $path,
            '?' => array_merge($queryparams, ['id' => $id])
        ]);

        return $url;
    }

    /**
     * Assemble a cursor URL
     *
     * Used for scroll pagination in trees that determine cursors from visible cursor nodes
     *
     * ### Options
     * - boolean collapsed Whether the cursor node is collapsed
     *
     * @param string|null $path Path parameter
     * @param string|null $id ID of the cursor node
     * @param string $direction next or prev or child
     * @param array $options Additional options
     *
     * @return mixed
     */
    public function cursorUrl($path = null, $id = null, $direction='next', $options=[])
    {
        $request = $this->getView()->getRequest();
        $queryparams = $request->getQueryParams();
        $queryparams['cursor'] = $id;
        $queryparams['direction'] = $direction === 'prev' ? 'desc' : 'asc';
        $queryparams['children'] = $direction === 'child';
        unset($queryparams['seek']);

        if ($options['collapsed'] ?? false) {
            $queryparams['collapsed'] = 1;
        }

        $url = $this->Url->build([
            'action' => $request->getParam('action'),
            $path,
            '?' => array_merge($queryparams)
        ]);

        return $url;
    }

    /**
     * Assemble move endpoint URL
     *
     * @param string|null $path path parameter
     * @return string
     */
    public function moveUrl($path = null)
    {
        $request = $this->getView()->getRequest();
        $url = $this->Url->build([
            'database' => $request->getParam('database'),
            'controller' => $request->getParam('controller'),
            'action' => 'move',
            $path,
        ]);

        return $url;
    }

    /**
     * Generates a login link (without token)
     *
     * @return string
     */
    public function loginLink(): string
    {
        // Build redirect URL
        $request = $this->_View->getRequest();
        $params = $request->getAttribute('params');
        $redirect = $params + $params['pass'] ?? [];
        unset($redirect['pass']);
        unset($redirect['_matchedRoute']);
        $redirect['?']['token'] = false;

        $redirect = $this->Url->build($redirect);
        $url = ['plugin' => false, 'controller' => 'Users', 'action' => 'login', '?' => ['redirect' => $redirect]];

        return $this->Html->link('Login', $url);
    }

    /**
     * Get the current URL updated by some parameters
     *
     * @param array $newParams The new parameters
     * @return array
     */
    public function getUpdatedUrl($newParams)
    {
        $request = $this->_View->getRequest();
        $params = $request->getAttribute('params');
        $url = $params + $params['pass'] ?? [];
        unset($url['pass']);
        unset($url['_matchedRoute']);

        return array_replace_recursive($url,$newParams);
    }

    /**
     * Generates a cancel link
     *
     * @param array|string $url URL array or URL string
     * @return string
     */
    public function cancelLink($url, $form = ''): string
    {
        $options =  [
            'linktype' => 'button',
            'type' => 'button',
            'form' => $form,
            'data-role' => 'cancel'
//            'shortcuts' => ['Ctrl+Q']
        ];

//        $request = $this->_View->getRequest();
//        $options['class'] = 'button button_cancel';

        return $this->authLink(__('Cancel'), $url, $options);
    }

    /**
     * Generate a button based on the allowed actions of the user
     *
     * @param string $title
     * @param array $options
     *
     * @return string
     */
    public function authButton(string $title, $options = []): string
    {
        $role = $this->_View->get('user_role') ?? 'guest';
        $allowed = array_merge(($options['roles'] ?? ['*']), ['admin', 'devel']);
        unset($options['roles']);

        if (in_array($role, $allowed) | in_array('*', $allowed)) {
            return $this->Form->button($title, $options);
        }
        else {
            return '';
        }
    }

    /**
     * Check whether the active user has permission to the endpoint
     * on the selected database
     *
     * TODO: implement a plugin
     * TODO: check entity_id
     *
     * @param array $url
     * @param array $options
     * @return bool
     */
    public function hasPermission($url, $options = []) {
        // Check role option
        $role = $this->_View->get('user_dbrole') ?? 'guest';

        $allowed = array_merge(($options['roles'] ?? ['*']), ['admin', 'devel']);

        if (!(in_array($role, $allowed) | in_array('*', $allowed))) {
            return false;
        }

        // Check hardwired permissions ($authorized property of controllers)
        if (is_array($url)) {
            $request = $this->_View->getRequest();

            $endpoint = [
                'plugin' => strtolower($request->getParam('plugin') ?? ''),
                'controller' => strtolower($request->getParam('controller') ?? ''),
                'action' => strtolower($request->getParam('action') ?? '')
            ];

            $endpoint = array_merge($endpoint, $url);

            $endpoint['plugin'] = is_string($endpoint['plugin']) ? $endpoint['plugin'] : '';
            $endpoint['scope'] = strtolower($endpoint['plugin']) === 'epi' ? 'epi' : 'app';
            unset($endpoint['plugin']);
            unset($endpoint['?']);
            if (!PermissionsTable::getEndpointHasRole($endpoint, $role, 'web')) {
                return false;
            }
        }

        return true;

    }

    /**
     * Generate a link based on the allowed actions of the user
     *
     * ### Options
     * - linktype post, submit, cancel, get (default is get)
     *
     * @param string $title
     * @param array|string $url
     * @param array $options
     *
     * @return string
     */
    public function authLink(string $title, $url, array $options = []): string
    {
        if (!$this->hasPermission($url, $options)) {
            return '';
        }
        unset($options['roles']);

        // Generate link
        $linkType = $options['linktype'] ?? false;
        unset($options['linktype']);

        if ($linkType === 'post') {
            return $this->Form->postLink($title, $url, $options);
        }
        elseif (($linkType === 'submit') || ($linkType === 'button')) {
            return $this->Form->button($title, $options);
        }
        elseif ($linkType === 'cancel') {
            return $this->cancelLink($url);
        }
        else {
            return $this->Html->link($title, $url, $options);
        }

    }

    /**
     * Get a URL to view or code the entity
     *
     * @param array $url Provide at least the entity ID
     * @return string
     */
    public function openUrl($url)
    {
        $request = $this->getView()->getRequest();
        $mode = Attributes::cleanOption($request->getQuery('mode'),['preview', 'code', 'default'], '');

        if (empty($mode)) {
            $url = array_merge($url, ['action' => 'view']);
        }
        elseif ($mode === 'code') {
            $url = array_merge($url, ['action' => 'edit','?' => ['mode' => 'code']]);
        }
        else {
            $url = array_merge($url, ['action' => 'view', '?' => ['mode' => $mode]]);
        }

        return $url;
    }

    /**
     * Output a link to view or edit the entity
     *
     * @param array $url Provide at least the entity ID
     * @return string
     */
    public function openLink($url)
    {
        $params = $this->_View->getConfig('options')['params'];
        $mode = $params['mode'] ?? 'default';

        if ($mode === 'code') {
            return $this->authLink(__('Edit'), $this->openUrl($url));
        }
        else {
            $url['?']['mode'] = $mode;
            return $this->authLink(__('View'), $this->openUrl($url));
        }

    }

    /**
     * Update placeholders in an array by entity properties
     *
     * TODO: write test
     * Used to generate URL arrays, e.g. to link values in tables.
     * Example for $link:
     *  ['controller' => 'Articles', 'action' => 'index', '?' => ['projects' => '{id}','settings'=>true]]
     *
     * @param array $link The URL array with placeholders
     * @param object $entity The entity having the properties
     * @param null|string $defaultValue The default value for properties with null values or empty strings
     * @return array The URL array with placeholders replaced by entity property values
     */
    public function fillPlaceholders($link, $entity, $defaultValue = null)
    {
         $replaceRecursive = function($haystack, $entity) use (&$replaceRecursive, $defaultValue) {
            $replaced = [];
            if (is_array($haystack)) {
                foreach($haystack as $key => $value) {
                    if (preg_match('/{(.*)}/', strval($key), $arr_match)) {
                        $entityValue = $entity[$arr_match[1]] ?? $defaultValue;
                        if (($entityValue === '') && !is_null($defaultValue)) {
                            $entityValue = $defaultValue;
                        }
                        $key = str_replace('{' . $arr_match[1] . '}', $entityValue, $key);
                    }

                    if (is_array($value)) {
                        $replaced[$key] = $replaceRecursive($value, $entity);
                    } else {
                        if (preg_match('/^{(.*)}$/', strval($value), $arr_match)) {
                            $value = $entity[$arr_match[1]] ?? $defaultValue ?? '';
                            if (($value === '') && !is_null($defaultValue)) {
                                $value = $defaultValue;
                            }
                        }

                        $replaced[$key] = $value;
                    }
                }
            } else if (is_string($haystack)) {
                if (preg_match('/^{(.*)}$/', strval($haystack), $arr_match)) {
                    $replaced = $entity[$arr_match[1]] ?? $defaultValue ?? '';
                    if (($replaced === '') && !is_null($defaultValue)) {
                        $replaced = $defaultValue;
                    }
                }
            }
            return $replaced;
        };

        return $replaceRecursive($link, $entity);
    }

    /**
     * Based on the request, return whether we are in edit mode or not
     *
     * @return bool
     */
    public function getEdit()
    {
        $request = $this->_View->getRequest();
        return ($request->getParam('action') === 'edit') || ($request->getParam('action') === 'add');
    }

    /**
     * Based on the config parameters and the request, return the mode: default, code, preview
     *
     * @return string
     */
    public function getMode()
    {
        $mode = $this->_View->getConfig('options')['params']['mode'] ?? null;
        if (empty($mode)) {
            $request = $this->_View->getRequest();
            $mode = $request->getQuery('mode', 'default');
        }
        return $mode;
    }
}
