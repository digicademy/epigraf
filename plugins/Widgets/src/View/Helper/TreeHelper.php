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
use Cake\ORM\Entity;
use Cake\ORM\ResultSet;
use Cake\View\Helper;
use Epi\Model\Entity\Type;

/**
 * Tree helper
 *
 * Methods to generate trees in the frontend
 *
 * @property Helper\HtmlHelper $Html
 */
class TreeHelper extends Helper
{
    /**
     * Load helpers
     *
     * @var string[]
     */
    public $helpers = ['Html', 'Link', 'Element', 'Paginator'];

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [];

 /**
     * Create select tree for entities
     *
     * ### Options
     * - select: true|false. Whether to generate a column selector
     * - indent: true|false. Whether to generate an extra first column. Defaults to false.
     * - columns: Column definitions, see PermissionsTable::getColumns() or ProjectsTable::getColumns() as examples
     * - actions: Key 'view' = true | false. Key 'open' contains a URL with placeholders
     * - class: A string to be added to the table classes.
     * - data: A list of key-value-pairs added to the table attributes.
     * - label: false|true. Whether to add the entities' captions to the table rows' data-label attributes. Defaults to false.
     * - targets: An object containing targets for filtering detail nodes
     * - snippet: true|false. Whether to add 'recordlist' as data-snippet value to the table. Defaults to true.
     * - tree: true|false|collapsed. Create tree markup. Defaults to false.
     * - details: true|false|cursor. Tree child nodes
     * - fold: foldable|fixed. Fixed trees omit the first level's indentations
     * - drag: true|false Whether Items can be dragged and dropped
     * - sort: true|false Set to false to omit table header sort links. For example, cursored collections such as property trees can not be sorted.
     *
     * @param string $model The model name with plugin notation, lower case, for example "epi.properties"
     * @param array|ResultSet $entities List of entities
     * @param array $options An array of optional options
     *
     * @return string
     */
    public function selectTree($model, $entities, $options=[])
    {
        $group = str_replace('.','_',$model);
        $options['seek'] = $this->_View->getConfig('options')['params']['seek'] ?? null;
        $options['selected'] = $options['selected'] ?? $this->_View->getConfig('options')['params']['selected'] ?? (!empty($options['seek']) ? [$options['seek']] : []);
        $options['direction'] = $this->_View->getConfig('options')['params']['direction'] ?? 'asc';

        $options['template'] = $this->_View->getRequest()->getQuery('template', 'select');
        $ulAttributes = [
           'class' => 'widget-tree',
           'data-snippet' => 'rows',
           'data-list-name' => $group,
           'data-list-seek' => $options['seek']
        ];

        $out = $this->Element->openHtmlElement('ul', $ulAttributes);
        $out .= $this->getTreeMetaNodes($model, $options);
        $out .= $this->getNodes($model, $entities, [$this, 'getTreeNode'], $options);
        $out .= $this->Element->closeHtmlElement('ul');

        return $out;
    }

    /**
     * @param string $model The model name with plugin notation, lower case, for example "epi.properties"
     * @param array $options An array of optional options
     * @return string
     */
    public function getTreeMetaNodes($model, $options=[])
    {
        // TODO:pass in $options
        $cursor = $this->_View->getConfig('options')['params']['cursor'] ?? false;
        $append = empty($cursor) && ($this->_View->getConfig('options')['params']['append'] ?? false);
        $empty = $this->_View->getConfig('options')['params']['empty'] ?? false;
        $manage = $this->_View->getConfig('options')['params']['manage'] ?? false;
        $term = $this->_View->getConfig('options')['params']['term'] ?? null;
        $term = $term ?? $this->_View->getConfig('options')['params']['find'] ?? '';

        $group = str_replace('.','_',$model);
        $out = '';

        // Manage item
        if (!empty($manage)) {
            $out .= $this->getTreeMetaNode($model, [
                'data' => ['data-role' => 'manage'],
                'content' => $this->Html->link(
                        __('Manage'),
                        ['action'=>'index', $options['scope'] ?? '','?' => ['seek' => $options['seek'] ?? '']],
                        ['target'=>'_blank']
                    )
            ]);
        }

        // Empty item
        if (!empty($empty)) {
            $out .= $this->getTreeMetaNode($model, [
                'data' => [
                    'data-role' => 'empty',
                    'data-list-itemof' => $group,
                    'data-id' => '',
                    'data-label' => ''
                ],
                'content' => __('None')
            ]);
        }

        // Append item
        if (!empty($append) && !empty($term)) {
            $out .= $this->getTreeMetaNode($model, [
                'data' => [
                    'data-role' => 'append',
                    'data-list-itemof' => $group,
                    'data-id' => Attributes::uuid('new'),
                    'data-type' => $options['scope'] ?? '',
                    'data-label' => $term,
                    'data-append' => '1'
                ],
                'content' => h($term)
            ]);
        }

        return ($out);
    }

    public function getTreeMetaNode($model, $options)
    {
        $liAttributes = array_merge(['class' => 'item item-meta'], $options['data'] ?? []);
        $out = $this->Element->openHtmlElement('li', $liAttributes);
        $out .= $this->Element->outputHtmlElement('div',$options['content'] ?? null,['class'=>'tree-content']);
        $out .= $this->Element->closeHtmlElement('li');
        return ($out);
    }

    /**
     * Output the node content as li element
     *
     * ### Options
     * - selected: Selected IDs
     * - template: select|choose
     *
     * @param string $model The model name with plugin notation, lower case, for example "epi.properties"
     * @param Entity $entity
     * @param boolean|string $cursor prev|next|false
     * @param array $options
     * @return string
     */
    public function getTreeNode($model, $entity, $cursor = false,  $options=[])
    {
        // Only return potential children
        if (!$entity) {
            return '';
        }

        if (($cursor === 'child') && (($entity->rght - $entity->lft) === 1 )) {
            return '';
        }

        $group = str_replace('.','_',$model);
        $type = $entity['type'] ?? null;
        $itemAttributes = $this->getAttributes($entity, $type, $cursor, $options);
        $itemAttributes['data-list-itemof'] = $group;

        $selected = $options['selected'] ?? [];
        $template = $options['template'] ?? 'select';

        $out = $this->Element->openHtmlElement('li', $itemAttributes);

        // Content
        $content = '';
        if (empty($cursor)) {
            if (!empty($entity->lookup_to)) {
                $content = __('See {0}', $entity['lookup_to']['path']);
            }
            elseif (!empty($entity->lookup_from)) {
                $content = __('Reference from {0}', $entity['lookup_from']['path']);
            }
            else {
                $content = ' <label class="text" title="' . $entity->shortname . '">';
                if ($template === 'select') {
                    $content .= '<input class="property" type="checkbox" '
                        . (in_array($entity['id'], $selected) ? 'checked ' : '')
                        . 'value="' . $entity['id'] . '"'
                        . '>';
                }
                $content .= $entity->shortname;
                $content .= '</label>';
            }
        }

        // Indentation
        $out .= $this->getIndentation($entity, $options['fold'] ?? 'foldable', $cursor);

        // Content
        $out .= '<div class="tree-content">' .$content . '</div>';

        // MEta
        $out .= '<div class="tree-meta">';
        if (empty($cursor)) {
            if ($template === 'choose') {
                $out .= '<div class="tree-meta-keywords">' . $entity['keywords'] . '</div>';
            }
            $out .= '<div class="tree-meta-count">' . $entity['articles_count'] . '</div>';
        }
        $out .= '</div>';

        $out .= $this->Element->closeHtmlElement('li');
        return $out;

    }


    /**
     * Generates a cursor paginated list of nodes (table rows or list items)
     *
     * ### Options
     * - direction
     * - paginate
     * - tree
     * - seek
     *
     * @param string $model The model name with plugin notation, lower case, for example "epi.properties"
     * @param array|ResultSet $entities List of entities
     * @param callable $nodeMethod The method that generates each node,
     *                             for select trees use [$this, 'getTreeNode'],
     *                             for filter tables use [$this, 'getTableRow']
     * @param array $options
     * @return string
     */
    public function getNodes($model, $entities, $nodeMethod, $options=[])
    {
        $nodes = [];

        $stack = [];
        $outputCursors = ($options['paginate'] ?? true) === 'cursor';
        $cursorDir = $options['direction'] ?? 'asc';
        //TODO: skip cursors if the tree start is complete, i.e. no gaps
        $prevCursor = ((($cursorDir === 'desc') || !empty($options['seek'])) && $this->Paginator->hasNext());
        $collapsed = $options['collapsed'] ?? false;
        $cursorLevel = $this->Paginator->params()['level'] ?? null;

        foreach ($entities as $entity) {

            if ($entity instanceof \Epi\Model\Entity\BaseEntity) {
                $entity->prepareRoot();
            }

            // Collect stack for cursor generation
            if ($outputCursors && empty($entity->lookup_from)) {
                // Truncate stack to include only ancestors
                while (!empty($stack) && ($stack[count($stack) - 1]['id'] !== $entity['parent_id'])) {
                    array_pop($stack);
                    $prevCursor = false;
                }
                // Add to stack
                $stack[] = $entity;
            }

            // Prev cursor
            if ($prevCursor) {
                $nodes[] = $nodeMethod($model, $entity, 'prev', $options);
            }

            // The node
            $options['collapsed'] = $collapsed && (!isset($cursorLevel) || ($cursorLevel <= $entity->level ?? 0));
            $nodes[] = $nodeMethod($model, $entity,  false, $options);

            // Child cursor
            if ($options['collapsed']) {
                $nodes[] = $nodeMethod($model, $entity, 'child', $options);
            }
        }

        // Closing cursors
        if (!empty($stack) && (($cursorDir === 'asc') || !empty($options['seek'])) && $this->Paginator->hasNext()) {
            $entity = end($stack);

            // Output child cursor, only if not already output before
            $options['collapsed'] = $collapsed && (!isset($cursorLevel) || ($cursorLevel <= $entity->level ?? 0));
            if (!$options['collapsed']) {
                $nodes[] = $nodeMethod($model, $entity, 'child', $options);
            }
            foreach (array_reverse($stack) as $entity) {
                $options['collapsed'] = $collapsed && (!isset($cursorLevel) || ($cursorLevel <= $entity->level ?? 0));
                $nodes[] = $nodeMethod($model, $entity, 'next', $options);
            }
        }

        return implode("\n",$nodes);
    }

    /**
     * Output tree indentation
     *
     * @param Entity $item
     * @param string $style foldable|fixed In fixed trees, the first level is skipped (no need for indentation)
     * @param boolean|string $cursor prev or next or false
     *
     * @return string
     */
    public function getIndentation($item, $style = 'foldable', $cursor = false)
    {
        $out = '';
        $maxlevel = ($style === 'foldable') ? 0 : 1;

        // Parent nodes
        $parent = $item->parent ?? $item['tree_parent'] ?? null;
        while ($parent) {
            if (($parent['tree_level'] ?? 0) < $maxlevel) {
                break;
            }

            $class = 'tree-indent ';
            if ($cursor) {
                $class .= 'tree-indent-cursor';
            } elseif ($parent['tree_last'] ?? false) {
                $class .= 'tree-indent-empty';
            } else {
                $class .= 'tree-indent-line';
            }

            $out = '<div class="' . $class . '"></div>' . $out;

            $parent = $parent->parent ?? $parent['tree_parent'] ?? null;
        }

        // Current node
        if (($item['tree_level'] ?? 0) >= $maxlevel) {
            $class = $cursor ? "tree-indent tree-indent-cursor" : "tree-indent tree-indent-leaf";
            $out .= '<div class="' . $class . '"></div>';
        }

        // Child nodes
        if ($cursor === 'child') {
            $out .= '<div class="tree-indent tree-indent-cursor tree-indent-cursor-child"></div>';
        }

        return $out;
    }

    /**
     * Create css classes for a tree item
     *
     * @param array|Entity $item Items with the keys tree_level (or level), tree_children, tree_last and tree_first
     * @param Type $itemType The type entity
     * @param string|boolean $cursor next, prev or false
     * @param integer[] $selected IDs of the selected nodes
     *
     * @return string[]
     */
    public function getClasses($item, $itemType = null, $cursor = false, $selected=[])
    {
        $selected = is_array($selected) ? $selected : [(int)$selected];
        $nodeLevel = $item['tree_level'] ?? $item['level'] ?? 0;
        $nodeLevel += (int)($cursor === 'child');

        // TODO: why node and item? Decide!
        $classes = ['node', 'item'];

        if ($cursor) {
            $classes[] = 'node-cursor';
        }

        $classes[] = 'item-level-' . $nodeLevel;

        if ($itemType ?? false) {
            $classes[] = ($nodeLevel == ($itemType->config['level'] ?? 0) - 1) ? 'item-main' : '';
        }

        $classes[] = empty($item['tree_children']) ? 'item-nochildren' : 'item-haschildren';

        $classes[] = ($item['tree_last'] ?? false) ? 'item-last' : '';
        $classes[] = ($item['tree_first'] ?? false) ? 'item-first' : '';

        $classes[] = (($item['tree-collapsed'] ?? false) && (!empty($item['tree_children']))) ? 'item-collapsed' :'';
        $classes[] = ($item['tree-hidden'] ?? false) ? 'item-hidden' :'';

        $classes[] = !empty($item->lookup_to) ? 'reference_to' : '';
        $classes[] = !empty($item->lookup_from) ? 'reference_from item-virtual' :'';

        if (!$cursor && !empty($item['id']) && (in_array($item['id'], $selected))) {
            // TODO: decide for one class. row-selected is used in tables (TableWidget), selected in uls (DropdownSelector)
            $classes[] = 'row-selected';
            $classes[] = 'selected';
        }

        return array_filter($classes);
    }

    /**
     *  Get attributes for generating a tr element
     *
     *  ### Options
     * - integer[] selected IDs of the selected nodes
     * - boolean collapsed Whether the tree is collapsed
     *
     * @param array|Entity $item Items with the keys tree_level (or level), tree_children, tree_last and tree_first
     * @param Type $propertyType The type entity
     * @param string|boolean $cursor next, prev or false
     * @param array $options
     * @return string[]
     */
    public function getAttributes($item, $propertyType = null, $cursor = false, $options=[])
    {
        // Select trees have checkboxes, no need to highlight selected rows
        if (($options['template'] ?? '') === 'select') {
            $selected = [];
        } else {
            $selected = $options['selected'] ?? [];
        }

        $nodeLevel = $item['tree_level'] ?? $item['level'] ?? 0;
        $nodeLevel += (int)($cursor === 'child');
        $nodeParent = ($cursor === 'child') ? $item['id'] : ($item['parent_id'] ?? '');

        // TODO: Can $property->parent['id'] be removed?
        $nodeTreeParent = ($cursor === 'child') ? $nodeParent : ($item->parent['id'] ??  $item['parent_id'] ?? $item['tree_parent']['id'] ?? '');

        $data = [
            'class' => $this->getClasses($item, $propertyType, $cursor, $selected),

            // TODO: add epi prefix if necessary
            'data-list-itemof' => $item->table_name,

            // TODO: instead of adding suffix '-from' implement different roles (in addition to the
            //       existing roles data-role="manage" and data-role="empty". Make sure, merging
            //       nodes works in ScrollPaginator.mergeRows() (paginator.js).
            'data-id' =>  empty($item->lookup_from) ? $item['id'] : $item['id'] . '-from',
            'data-parent' => $nodeParent,
            'data-tree-parent' => $nodeTreeParent,
            'data-level' => $nodeLevel
        ];

        if ($options['collapsed'] ?? false) {
            $data['class'][] = 'item-collapsed';
        }

        // Necessary after adding new nodes to mount them in the tree (paginator.js)
        // TODO: Check whether loading conditionally could work
        if (isset($item->preceding) && !($cursor)) {
            $data['data-preceding'] = $item->preceding['id'] ?? '';
        }

        if ($cursor) {
            $data['data-cursor-id'] = $item['id'];
            $data['data-cursor-dir'] = $cursor === 'prev' ? 'prev' : 'next';
            $data['data-cursor-child'] = $cursor === 'child';
            $data['data-cursor-collapsed'] = $options['collapsed'] ?? false;

            $cursorUrl = $this->Link->cursorUrl($item->propertytype, $item->id, $cursor, $options);
            $data['data-cursor-action'] = htmlspecialchars_decode($cursorUrl);

        }

        return $data;
    }

}
