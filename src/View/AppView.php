<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\View;

use App\Model\Entity\Databank;
use App\Utilities\Converters\Attributes;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Epi\View\Helper\TypesHelper;

/**
 * Application View
 *
 * Your application’s default view class
 *
 * @link http://book.cakephp.org/3.0/en/views.html#the-app-view
 *
 *
 * @property \Widgets\View\Helper\ElementHelper $Element
 * @property \Widgets\View\Helper\LinkHelper $Link
 * @property \Files\View\Helper\FilesHelper $Files
 * @property \Widgets\View\Helper\TableHelper $Table
 * @property \Widgets\View\Helper\TreeHelper $Tree
 * @property \Widgets\View\Helper\EntityHtmlHelper $EntityHtml
 * * @property \Widgets\View\Helper\EntityInputHelper $EntityInput
 * @property \Widgets\View\Helper\MenuHelper $Menu
 * @property TypesHelper $Types
 */
class AppView extends View
{

    /**
     * Blocks that should be rendered
     * (top, bottom, left, right, bottom-right, content, searchbar)
     *
     * By default, in AJAX calls only the content will be rendered,
     * in other calls all blocks will be rendered.
     *
     * Use setShowBlock to override the default.
     * Use getShowBlock to determine whether a block should be rendered.
     *
     * @var array
     */
    protected $showBlocks = ['content'];

    /**
     * Tabsheet list.
     *
     * First level contains the sidebar position (left|right) in the key.
     * Second level has the keys 'sheets' and 'add'.
     * The third level of sheets contains an associated array of
     * tabsheet identifiers (e.g. 'footnotes') and captions (e.g. 'Fußnoten').
     *
     * @var array
     */
    public $tabsheets = [];

    /**
     * Initialize hook
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->addHelper('Widgets.Element');
        $this->addHelper('Widgets.Link');
        $this->addHelper('Widgets.Table');
        $this->addHelper('Widgets.Tree');
        $this->addHelper('Widgets.EntityHtml');
        $this->addHelper('Widgets.EntityInput');
        $this->addHelper('Widgets.Menu');
        $this->addHelper('Files.Files');
    }

    public static function contentType(): string
    {
        return 'text/html';
    }

    /**
     * Renders a layout. Returns output from _render().
     *
     * Overwritten to remove automatic title creation
     *
     * @param string $content Content to render in a template, wrapped by the surrounding layout.
     * @param string|null $layout Layout name
     * @return string Rendered output.
     * @throws \Cake\Core\Exception\CakeException if there is an error in the view.
     * @triggers View.beforeLayout $this, [$layoutFileName]
     * @triggers View.afterLayout $this, [$layoutFileName]
     */
    public function renderLayout(string $content, ?string $layout = null): string
    {
        $layoutFileName = $this->_getLayoutFileName($layout);

        if (!empty($content)) {
            $this->Blocks->set('content', $content);
        }

        $this->dispatchEvent('View.beforeLayout', [$layoutFileName]);

        $this->_currentType = static::TYPE_LAYOUT;
        $this->Blocks->set('content', $this->_render($layoutFileName));

        $this->dispatchEvent('View.afterLayout', [$layoutFileName]);

        return $this->Blocks->get('content');
    }

    /**
     * Render the page footer
     *
     * @param boolean $help Whether to add the help button
     * @return string
     */
    public function renderFooter($help = true)
    {
        if ($help) {
            $this->Link->addHelpAction();
        }

        $footer = '';
        if ($this->Link->hasActions('bottom')) {
            $footer .= $this->Link->renderSandwichActions(
                'widget-sandwich-items-bottom',
                'bottom'
            );
        }

        $footer .= $this->Link->renderSandwichButton(
            'widget-sandwich-items-bottom',
            ['dropdown' => 'topright', 'icon' => "\u{f152}", 'align-y' => 'footer']
        );

        if ($this->Link->hasActions('bottom-right')) {
            $footer .= $this->Link->renderSandwichActions(
                'widget-sandwich-items-footer',
                'bottom-right'
            );
        }

        $footer .= $this->Link->renderSandwichButton(
            'widget-sandwich-items-footer',
            ['dropdown' => 'topright', 'icon' => "\u{f56d}", 'align-y' => 'footer']
        );

        $footer = '<footer>' . trim($footer) . '</footer>';
        return $footer;

    }

    public function renderAjaxFooter()
    {
        $footer = '';
        if ($this->Link->hasActions('bottom')) {
            $footer .= $this->Link->renderSandwichActions(
                'widget-sandwich-items-footer',
                'bottom'
            );
        }

        if ($this->Link->hasActions('bottom-right')) {
            $footer .= $this->Link->renderSandwichActions(
                'widget-sandwich-items-footer',
                'bottom-right'
            );
        }

        $footer = trim($footer);

        if (!empty($footer)) {
            $footer = '<footer>' . $footer . '</footer>';
        }
        return $footer;

    }

    /**
     * Output the sidebar content
     *
     * The content is fetched by $this->fetch($side) and
     * the title is fetched by $this->fetch($side. '-title');
     *
     * @param string $side left or right
     * @param array $options Supported keys: size, edit
     * @return string
     */
    public function renderSidebar($side, $options)
    {
        $options = array_replace_recursive($this->getSidebarConfig($side), $options);
        list($content, $count) = $this->renderTabsheets($side, $options);

        // Wrapper attributes
        $classes = [
            'sidebar',
            'sidebar-' . $side,
            isset($options['size']) ? 'sidebar-size-' . $options['size'] : null,
            !empty($options['init']) ? 'sidebar-init-' . $options['init'] : null,
            'accordion-item',
            !$count ? 'sidebar-empty' : null
        ];

        $divAttributes = [
            'class' => array_filter($classes),
            'data-accordion-item' => 'sidebar-' . $side,
            'data-snippet' => 'sidebar-' . $side
        ];

        // Get width from user session
        $width = $this->getUiSetting('sidebar-' . $side, 'width');
        if (!empty($width)) {
            $divAttributes['style'] = 'flex-basis: ' . $width . 'px';
        }

        return $this->Element->openHtmlElement('div', $divAttributes)
            . $this->Element->openHtmlElement('div', ['class' => 'sidebar-content'])
            . $content
            . $this->Element->closeHtmlElement('div')
            . $this->Element->closeHtmlElement('div');
    }

    /**
     * Set sidebar configuration
     *
     * ## Options:
     * An array with the keys 'left' and 'right'.
     * In each array you can set the following keys:
     * - init to 'expanded' or 'collapsed'
     * - size to a number
     *
     * @param array $options
     */
    public function setSidebarConfig($options)
    {
        $defaultOptions = $this->getConfig('sidebar', []);
        $options = array_replace_recursive($defaultOptions, $options);
        $this->setConfig('sidebar', $options);
    }

    /**
     * Set sidebar configuration depending on the mode
     *
     * - default: expanded left, collapsed right
     * - revise: collapsed left, expanded right
     *
     * @return void
     */
    public function setSidebarConfigByMode()
    {
        $mode = $this->Link->getMode();
        if ($mode === MODE_REVISE) {
            $this->setSidebarConfig([
                'left' => ['init' => 'collapsed', 'size' => 2],
                'right' => ['init' => 'expanded', 'size' => 6]
            ]);
        } else {
            $this->setSidebarConfig([
                'left' => ['init' => 'expanded', 'size' => 2],
                'right' => ['init' => 'collapsed', 'size' => 4]
            ]);
        }
    }

    /**
     * Show the sidebar if not in revise mode
     *
     * @return void
     */
    public function activateSidebar()
    {
        $mode = $this->Link->getMode();
        if ($mode !== MODE_REVISE) {
            $this->setSidebarConfig(['left' => ['init' => 'expanded']]);
        }
    }

    /**
     * Get sidebar configuration
     *
     * @param string $side The sidebar name (left|right)
     * @return array
     */
    public function getSidebarConfig($side)
    {
        $defaultOptions = [
            'left' => ['init' => 'collapsed', 'size' => 2],
            'right' => ['init' => 'collapsed', 'size' => 5]
        ];
        $options = $this->getConfig('sidebar', []);
        $options = array_replace_recursive($defaultOptions, $options);
        return $options[$side] ?? [];
    }

    /**
     * Capture tabsheet content
     *
     * ### Options
     * -role sheet|fixed|template
     * -active true|false
     *
     * @param string $caption
     * @param string $identifier
     * @param string $position left|right
     * @param array $options
     * @return void
     */
    public function beginTabsheet(string $caption, string $identifier, string $position, $options = [])
    {
        $role = $options['role'] ?? 'sheet';
        if (!isset($this->tabsheets[$position][$role][$identifier])) {
            $tabConfig = [
                'caption' => $caption,
                'active' => $options['active'] ?? false
            ];
            $this->tabsheets[$position][$role][$identifier] = $tabConfig;
        }

        $blockId = 'tabsheet-' . $position . '-' . $identifier;
        $this->start($blockId);
    }

    public function endTabsheet()
    {
        $this->end();
    }

    public function addTabsheetSelector($position, $options)
    {
        $this->tabsheets[$position]['add'] = $options;
    }

    /**
     * Output the tabsheets for a specific position
     *
     * @param string $position left or right
     * @param array $options
     * @return array [$content, $tabCount]
     */
    public function renderTabsheets($position, $options = [])
    {
        $selectors = '';
        $sheets = '';
        $tabCount = 0;
        $tabActive = false;
        $addButton = $this->tabsheets[$position]['add'] ?? [];
        $templates = $this->tabsheets[$position]['template'] ?? [];

        $dynamic = $this->tabsheets[$position]['sheet'] ?? [];
        $fixed = $this->tabsheets[$position]['fixed'] ?? [];
        $tabsheets = array_merge($fixed, $dynamic);

        // Tabsheets
        foreach ($tabsheets as $identifier => $tabConfig) {
            // Tabsheet sheet
            $sheetContent = $this->fetch('tabsheet-' . $position . '-' . $identifier);
            $sheetClasses = ['widget-tabsheets-sheet'];
            if (empty($sheetContent)) {
                $sheetClasses[] = 'empty';
            }
            else {
                $tabCount += 1;

                //if (!$tabActive) {
                if ($tabConfig['active'] ?? false) {
                    $tabActive = $tabCount;
                    $sheetClasses[] = 'active';
                }
            }

            $sheets .= $this->Element->outputHtmlElement(
                'div',
                $sheetContent,
                [
                    'class' => $sheetClasses,
                    'data-tabsheet-id' => $identifier
                ]
            );

            // Tabsheet button
            $buttonClasses = ['widget-tabsheets-button'];
            if (empty($sheetContent)) {
                $buttonClasses[] = 'empty';
            }
            elseif ($tabCount === $tabActive) {
                $buttonClasses[] = 'active';
            }

            $buttonContent = '<button class="caption">' . ($tabConfig['caption'] ?? '') . '</button>';
            $deleteButton = !empty($addButton) && empty($fixed[$identifier]);
            if (!empty($deleteButton)) {
                $buttonContent .= '<button class="btn-remove" '
                    . 'title="' . __("Remove tabsheet") . '" '
                    . 'aria-label="' . __("Remove tabsheet") . '">'
                    . 'x</button>';
            }

            $selectors .= $this->Element->outputHtmlElement(
                'div',
                $buttonContent,
                ['class' => $buttonClasses, 'data-tabsheet-id' => $identifier]
            );

        }

        // Tabsheet templates
        foreach ($templates as $identifier => $tabConfig) {
            $sheetContent = $this->fetch('tabsheet-' . $position . '-' . $identifier);

            $sheets .= $this->Element->outputHtmlElement(
                'script',
                $sheetContent,
                [
                    'type' => 'text/template',
                    'class' => 'template template-tabsheet'
                ]
            );
        }

        // Assemble tab buttons
        $manageButtons = '';
        if (!empty($addButton)) {
            $manageButtons .= '<button class="btn-add widget-dropdown '
                . ' widget-dropdown-toggle" '
                . 'title="' . __('Add filter') . '" '
                . 'aria-label="' . __('Add filter') . '" '
                . 'data-toggle="select-propertytype-pane">'
                . '+</button>';
        }

        $closeButton = $options['close'] ?? false;
        if ($closeButton) {
            $manageButtons .= '<button class="btn-close' . ($closeButton === 'small' ? ' show-small' : '') . '" '
                . 'title="' . __('Close') . '" '
                . 'aria-label="' . __('Close') . '"'
                . '></button>';
        }

        $applyButton = $options['apply'] ?? false;
        if ($applyButton) {
            $manageButtons .= '<button class="btn-apply' . ($applyButton === 'small' ? ' show-small' : '') . '" '
                . 'title="' . __('Apply filter') . '" '
                . 'aria-label="' . __('Apply filter') . '" '
                . '>✓</button>';
        }

        $selectors = '<div class="widget-tabsheets-selectors">'
            . '<div class="widget-tabsheets-selectors-tabs">' . $selectors . '</div>'
            . '<div class="widget-tabsheets-selectors-manage">' . $manageButtons . '</div>'
            . '</div>';

        // Assemble tabsheets
        $sheets = '<div class="widget-tabsheets-sheets">' . $sheets . '</div>';

        $containerClasses = ['widget-tabsheets'];
        if (!empty($addButton)) {
            $containerClasses[] = 'extendable';
        }
        elseif ($tabCount === 0) {
            $containerClasses[] = 'empty';
        }
        elseif ($tabCount === 1) {
            $containerClasses[] = 'simple';
        }

        // Select pane
        $panes = '';
        if (!empty($addButton)) {
            $panes .= '<div id="select-propertytype-pane" class="widget-dropdown-pane select-pane" data-widget-dropdown-position="right">';

            // Pane
            $paneId = 'select-propertytype-pane-content';
            $paneContent = $this->Table->filterPane(
                $paneId,
                $addButton,
                [],
                [
                    'grouped' => true,
                    'checkboxlist' => false,
                    'frame' => true
                ]
            );
            //$options['dropdown'] = true;


            // Search input
            $inputName = $paneId . '-search';
            $inputText = '';
            $inputValue = '';

            $options = [
                'caption' => false,
                'type' => 'reference',
                'frame' => true,
                'pane' => $paneContent,
                'paneId' => $paneId,
                'value' => $inputValue,
                'text' => $inputText,
                'search' => true,
                'autofocus' => 'autofocus'
            ];

            $panes .= $this->Form->input($inputName, $options);

            $panes .= '</div>';
        }

        // Assemble
        $content = $this->Element->outputHtmlElement(
            'div',
            $selectors . $sheets . $panes,
            ['class' => $containerClasses]
        );

        return [$content, $tabCount];
    }


    public function renderSidebarButtons($sidemenu, $options = [])
    {
        $output = '';

        if ($sidemenu['move'] ?? false) {
            $output .= $this->Element->outputHtmlElement('button', __('Move'), [
                'class' => 'btn-edit-sidebar widget-switch',
                'data-switch-element' => '.sidebar-left ul.widget-tree',
                'data-switch-class' => 'widget-tree-edit',
                'data-role' => 'move',
            ]);
        }

        if ($sidemenu['delete'] ?? false) {
            $output .= $this->Element->outputHtmlElement('button', '-', [
                'class' => 'btn-edit-sidebar doc-section-remove',
                'data-list-for' => 'menu-left',
                'data-role' => 'delete',
                'title' => __('Remove selected section'),
                'aria-label' => __('Remove selected section')
            ]);
        }

        if (($sidemenu['add'] ?? false) === 'popup') {
            $output .= $this->Element->outputHtmlElement('button', '+', [
                'class' => 'btn-edit-sidebar doc-section-add',
                'data-list-for' => 'menu-left',
                'data-role' => 'add',
                'title' => 'Add section',
                'ariaLabel' => 'Add section'
            ]);
        }
        elseif ($sidemenu['add'] ?? false) {
            $output .= $this->Element->outputHtmlElement('button', '+', [
                'class' => 'btn-edit-sidebar doc-section-add widget-dropdown-selector',
                'data-list-for' => 'menu-left',
                'data-role' => 'add',
                'data-url' => $sidemenu['add'] ?? '',
                'title' => 'Add section',
                'ariaLabel' => 'Add section'
            ]);
        }

        $output .= $this->Element->outputHtmlElement(
            'button', '',
            ['class' => 'btn-close', 'title' => __('Close'), 'aria-label' => __('Close')]
        );

        return $output;
    }

    /**
     * Enable block output in AJAX calls.
     *
     * By default, only the content is rendered in AJAX calls,
     * see getShowBlock.
     *
     * @param array $blocks E.g. ['footer', 'bottom', 'bottom-right']
     */
    public function setShowBlock($blocks)
    {
        $this->showBlocks = array_merge($this->showBlocks, $blocks);
    }

    /**
     * Return whether a content block should be rendered.
     *
     * By default, all blocks are rendered.
     * Set the show query parameter to a comma separated list.
     * If you want to render the main content only, use the `content` key.
     *
     * As an example, the following request renders the main content and the search bar:
     *
     * `/epi_all/articles/index?show=content,searchbar`
     *
     * @param string $name Block name as defined in the templates (e.g. see default.php)
     * @return bool
     */
    public function getShowBlock($name)
    {
        $blocks = $this->request->getQuery('show', '');
        $blocks = Attributes::commaListToStringArray($blocks);

        if (empty($blocks) && $this->request->is('ajax')) {
            $blocks = $this->showBlocks;
        }

        return empty($blocks) || in_array($name, $blocks);
    }

    /**
     * Toggle whether to show the toolbar for the first input element.
     *
     * Activating the toolbar from the beginning prevents flashing on page load.
     *
     * @param boolean $show Whether to show or hide the toolbar
     * @return void
     */
    public function initToolbar($show = true)
    {
        $this->setConfig('showToolbar', $show);
    }

    /**
     * Get interface settings from the user session
     * (e.g. width of a sidebar)
     *
     * TODO: Maybe move to a separate helper?
     *
     * @param string $widgetKey The widget key, e.g. 'sidebar-left'
     * @param string $valueKey The value key, e.g. 'width'
     * @param string|bool|null $actionKey The action key, e.g. 'articles-index'.
     *                                    If false, the action key is empty.
     *                                    If null, the action key is determined from the current controller and action.
     * @return mixed|null
     */
    public function getUiSetting($widgetKey, $valueKey, $actionKey = null)
    {
        $options = $this->getConfig('options', []);
        if ($actionKey === false) {
            $actionKey = '';
        }
        elseif (is_null($actionKey)) {
            $actionKey = Inflector::underscore($this->request->getParam('controller'))
                . '-' . Inflector::underscore($this->request->getParam('action'))
                . '-';
        }

        $uiKey = $actionKey . $widgetKey;
        return $options['session']['ui'][$uiKey][$valueKey] ?? null;
    }

    /**
     * Get the theme name from the query parameters
     *
     *  Currently supported themes are minimal and default.
     *
     * @return array|string|null
     */
    public function getThemeName()
    {
        //TODO: validate request parameters in a more smart way (prevent code injection)
        $theme = $this->request->getQuery('theme');
        $theme = in_array($theme, ['minimal']) && ($this->request->getQuery('show', '') !== '') ? $theme : 'default';
        return $theme;

    }

    /**
     * Output Javascript app settings
     *
     * @return string
     */
    public function getAppJs()
    {
        $baseUrl = $this->Url->build('/', ['fullBase' => true]);

        $databaseUrl = '';
        $database = $this->viewVars['database'] ?? null;
        if (!empty($database)) {
            $db_name = Databank::removePrefix($database->name);
            $databaseUrl = $baseUrl . $database->route . '/' . $db_name . '/';
        }

        // TODO: implement a Settings() class instantiated in EpiApp (see app.js)
        $js = 'if (window.App) {';
        $js .= 'window.App.baseUrl = "' . $baseUrl . '";'
            . 'window.App.databaseUrl = "' . $databaseUrl . '";'
            . 'window.App.debug = ' . (Configure::read('debug', false) ? 'true' : 'false') . ';'
            . 'window.App.user.load(' . json_encode($this->viewVars['js_user'] ?? '{}') . ');';
        $js .= '}';

        return $js;
    }
}

