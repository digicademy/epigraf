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

use App\Utilities\Converters\Attributes;
use Cake\View\Helper;


/**
 * Menu helper
 *
 * Renders menu in the frontend
 *
 * @property Helper\HtmlHelper $Html
 */
class MenuHelper extends Helper
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Load helpers
     *
     * @var string[]
     */
    public $helpers = ['Html', 'Url', 'Form', 'Paginator', 'Link', 'Tree', 'User'];

    /**
     * Render menu
     *
     * ### Options
     * - class: The class of the menu
     * - data: Data attributes of the menu
     *
     * ### Menu
     *
     * Menu items have numeric keys. Each item is an array with the following keys:
     * - label: The label of the menu item
     * - url: The url of the menu item
     * - roles: The roles that are allowed to see the menu item
     * - escape: Whether to escape the label
     * - forceshow: Whether to show the menu item even if the user does not have the required role (optional)
     * - dropdown: Whether the menu item is a dropdown
     * - items: The items of the dropdown, an array of menu items
     * - group: The group of the menu item.
     * - active: Whether the menu item is active
     * - spacer: Whether the menu item is a spacer (true) or the spacer text (string)
     * - template: Whether the menu item is a template
     * - tree-published: The published status of the tree item
     * - tree-comment: The comment status of the tree item
     *
     * Additional settings can be set in non-numeric keys:
     * - class: The class of the menu
     * - tree: Whether the menu is a tree. The tree widget will be used.
     * - edit:
     * - scrollbox:
     * - grouped: Whether the menu items should be grouped. Group headers are not automatically generated,
     *            instead you should insert spacer items with the group caption in the spacer value.
     *
     * @return string
     *@var array $options Additional options
     * @var array $menu The menu items.
     */
    public function renderMenu(array $menu, array $options=[]): string
    {
        $out = '';

        //Init
        $class = $options['class'] ?? '';
        $data = $options['data'] ?? [];
        $userRole = $this->_View->get('user_role') ?? 'guest';

        //Extract settings (all non-numeric keys)
        $settings = array_filter($menu, function($x, $idx) { return !is_numeric($idx); },ARRAY_FILTER_USE_BOTH);

        // Filter items
        $menu = array_filter($menu, function($x, $idx) use ($userRole) {
            if (!is_numeric($idx)) {
                $keep = false;
            }
            elseif (($x['forceshow'] ?? false)) {
                $keep = true;
            }
            else {
                $keep = in_array($userRole, ['devel']) || in_array($userRole, $x['roles'] ?? [$userRole]);
                $keep = $keep && $this->User->hasPermission($x['url'] ?? false, $x);
            }
            return $keep;
        }, ARRAY_FILTER_USE_BOTH);

        if ($settings['scrollbox'] ?? false) {
            $out .= '<div class="widget-scrollbox">';
        }

        $classes=[];

        if ($class ?? false) {
            $classes[] = is_array($class) ? implode(" ", $class) : $class;
        }

        if ($settings['class'] ?? false) {
            $classes[] = is_array($settings['class']) ? implode(" ", $settings['class']) : $settings['class'];
        }

        if ($settings['tree'] ?? false) {
            $classes[] = 'widget-tree';
            $classes[] = 'widget-tree-' . $settings['tree'];
        }

        if ($settings['edit'] ?? false) {
            $classes[] = 'widget-dragitems';
        }

        $data = array_merge($data ?? [], $settings['data'] ?? []);

        $out .= '<ul class="' . implode(" ",$classes) . '" ' . Attributes::toHtml($data) . '>';

        foreach (array_values($menu) as $idx => $item) {
            $classes=[];

            // Common classes
            if (!empty($item['class']))  {
                $classes[] = is_array($item['class']) ? implode(" ", $item['class']) : $item['class'];
            }

            if ($idx == 0) {
                $classes[] = 'first';
            }
            if (!empty($item['active'])) {
                $classes[] = 'active';
            }
            if (!empty($item['dropdown'])) {
                $classes [] = 'dropdown';
            }
            if (isset($item['spacer']) && ($item['spacer'] !== false)) {
                $classes[] = 'spacer';
            }
            if (!empty($item['grouplabel'])) {
                $classes[] = 'grouplabel';
            }
            if (!empty($item['group'])) {
                $classes[] = 'grouped';
            }
            if ($item['fixed'] ?? false) {
                $classes[] = 'fixed';
            }

            // Tree classes
            if ($settings['tree'] ?? false) {
                $treeClasses = $this->Tree->getClasses($item);
                $classes = array_merge($classes, $treeClasses);
            }

            // Data attributes
            $data = Attributes::toHtml($item['data'] ?? []);

            if ($item['template'] ?? false) {
                $out .= '<script type="text/template" class="template">';
            }

            $out .= '<li class="' . implode(" ",$classes) . '" ' . $data . '>';

            if (!empty($item['dropdown'])) {
                if (!empty($item['items'])) {
                    $out .= '<button class="widget-dropdown widget-dropdown-toggle"'
                        . ' data-toggle="databasemenu-dropdown" >'
                        . $item['label'] . ' &raquo;</button>';
                    $out .= '<div class="widget-dropdown-pane-header">' . __("Databases") . '</div>';
                    $out .= '<div id="databasemenu-dropdown"'
                        . ' class="widget-dropdown-pane dropdown-menu">'
                        . $this->renderMenu($item['items'], ['class' => 'vertical', 'data' => []])
                        . '</div>';
                }
                else {
                    $out .= '<span>' . $item['label'] . '&raquo;</span>';
                }
            }

            elseif (isset($item['spacer']) && ($item['spacer'] !== false)) {
                $spacer = is_string($item['spacer']) ? $item['spacer'] : '|';
                $out .= '<span>&nbsp;' . $spacer . '&nbsp;</span>';
            }

            elseif ($settings['tree'] ?? false) {
                $out .= $this->Tree->getIndentation($item, $settings['tree']);
                $out .= '<div class="tree-content">'
                            . $this->Html->link($item['label'], $item['url'] ?? '#', ['escape' => $item['escape'] ?? true])
                            . '</div>';
                $out .= '<div class="tree-meta">';

                if (isset($item['tree-published'])) {
                    $out .= '<div class="tree-published tree-published-' . $item['tree-published'] . '">'
                                . '●'
                                . '</div>';
                }

                if (isset($item['tree-comment'])) {
                    $out .= '<div class="tree-comment tree-comment-' . $item['tree-comment'] . '">'
                                . '</div>';
                }

                $out .= '</div>';
            }

            elseif (!empty($item['url'])) {
                $out .= $this->Html->link($item['label'], $item['url'], ['escape' => $item['escape'] ?? true]);
            }

            else {
                $out .= '<span>' . ($item['label'] ?? '') . '</span>';
            }

            $out .= '</li>';

            if ($item['template'] ?? false) {
                $out .= '</script>';
            }
        }

        $out .= '</ul>';

        if ($settings['scrollbox'] ?? false) {
            $out .= '</div>';
        }

        return $out;
    }

}
