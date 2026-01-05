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

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Databank;
use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use Cake\Datasource\QueryInterface;
use Cake\ORM\Entity;
use Cake\ORM\ResultSet;
use Cake\Utility\Hash;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\View\Helper;
use Cake\I18n\Number;

/**
 * Table helper
 *
 * Renders data tables in the frontend.
 * - simpleTable: A table without schnick schnack.
 * - nestedTable: A table for nested key value lists.
 * - filterTable: A full blown table with column selector, filter dropdowns, scroll pagination and resizable columns
 *
 * @property Helper\FormHelper $Form
 * @property ElementHelper $Element
 */
class TableHelper extends Helper
{

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Load helpers
     *
     * @var string[]
     */
    public $helpers = ['Html', 'Form', 'Link', 'Element', 'Files', 'Paginator', 'Tree'];

    /**
     * Output a simple table
     *
     * ### Options
     * - align-right: An array of field names that should be right aligned.
     * - nowrap: Set to true to wrap all content and apply the nowrap class.
     * - actions: An array of actions to be added to each row.
     *            The url key contains a URL with placeholders that will be processed
     *            by LinkHelper->fillPlaceholders() with the current row data.
     *            If the rows itself contain an actions key, set to true to render the action column.
     *
     * @param array $data A dataframe with rows on the first level and fields on the second.
     *                    Use the links key in a row to create links, see code below.
     * @param array $fields The fields to extract, field names as keys, captions as values.
     *                      Instead of the caption, you can use an array with the keys 'caption'
     *                      and 'link' to create a link.
     * @param array $options
     * @return string
     */
    public function simpleTable($data, $fields = [], $options = [])
    {
        $default = ['class' => 'simple-table'];
        $options = array_merge($default, $options);
        $rowActions = $options['actions'] ?? [];

        $out = '<table class="' . $options['class'] . '">';

        $outHeader = '<thead><tr>';
        foreach ($fields as $field => $fieldOptions) {
            if (!is_array($fieldOptions)) {
                $fieldOptions = ['caption' => $fieldOptions];
            }

            if (is_numeric($field)) {
                $field = $fieldOptions['caption'];
            }

            $caption = $fieldOptions['caption'] ?? $field;
            $align = $fieldOptions['align'] ?? (in_array($field, $options['align-right'] ?? []) ? 'right' : '');

            $classes = ($align === 'right') ? ' class="align-right"' : '';
            $outHeader .= '<th' . $classes . '>' . $caption . '</th>';
        }

        if (!empty($rowActions)) {
            $outHeader .= '<th>' . __('Actions') . '</th>';
        }
        $outHeader .= '</tr></thead>';

        $out .= $outHeader;
        $out .= '<tbody>';
        foreach ($data as $row) {
            $out .= '<tr>';
            foreach ($fields as $field => $fieldOptions) {
                if (!is_array($fieldOptions)) {
                    $fieldOptions = ['caption' => $fieldOptions];
                }
                if (is_numeric($field)) {
                    $field = $fieldOptions['caption'];
                }
                $align = $fieldOptions['align'] ?? (in_array($field, $options['align-right'] ?? []) ? 'right' : '');

                if ($align === 'right') {
                    $row['classes'][$field][] = 'align-right';
                }

                $class = implode(" ", $row['classes'][$field] ?? []);
                $class = $class ? ' class="' . $class . '"' : '';
                $value = $row[$field] ?? '';

                // Format value
                $value = is_numeric($value) ? Number::format($value) : $value;
                $value = is_array($value) ? array_filter($value, fn($x) => $x !== '') : $value;
                $value = (is_array($value) && (count($value) === 1)) ? end($value) : $value;
                $value = (is_array($value) && (count($value) === 0)) ? '' : $value;
                $value = !is_string($value) ? json_encode($value) : $value;

                $out .= '<td' . $class . '>';
                if (!empty($options['nowrap'])) {
                    if (mb_strlen($value) > 40) {
                        $out .= '<span title="' . h($value) .'">';
                    } else {
                        $out .= '<span>';
                    }
//                    $value = mb_strimwidth($value,0,40,'...');
                }

                // Link the value
                if (!empty($fieldOptions['link'])) {
                    $link = $this->Link->fillPlaceholders($fieldOptions['link'], $row);
                    $value =  $this->Html->link($value, $link);
                }
                else if(!empty($row['link'][$field])) {
                    $value =  $this->Html->link($value, $row['link'][$field]);
                } else {
                    $value = h($value);
                }
                $out .= $value;

                // Add action buttons
                $actions = $fieldOptions['actions'] ?? [];
                foreach ($actions as $action) {
                   $action['url'] = $this->Link->fillPlaceholders($action['url'], $row);
                   $out .= ' ' . $this->Link->renderActionButton($action);
                }

                if (!empty($options['nowrap'])) {
                    $out .= '</span>';
                }
                $out .= '</td>';
            }

            if (!empty($rowActions)) {
                $out .= '<td>';

                // Specific row actions
                if(!empty($row['actions'])) {
                    foreach ($row['actions'] as $action) {
                        $action['url'] = $this->Link->fillPlaceholders($action['url'], $row);
                        $out .= ' ' . $this->Link->renderActionButton($action);
                    }
                }

                // Generic row actions
                if (is_array($rowActions)) {
                    foreach ($rowActions as $action) {
                        $action['url'] = $this->Link->fillPlaceholders($action['url'], $row, '*');
                        $out .= ' ' . $this->Link->renderActionButton($action);
                    }
                }
                $out .= '</td>';
            }

            $out .= '</tr>';
        }
        $out .= '</tbody>';

        $out .= '</table>';

        return $out;
    }

    /**
     * Output nested table
     *
     * ### Options
     * - class The class added to the table element
     * - header Whether to output a header row
     * - key The key caption in the header row
     * - value The value caption in the jeader row
     * - tree Whether to output a foldable tree
     * - list-name The list name
     *
     * @param array $array The nested data
     * @param array $options
     *
     * @return string
     */
    public function nestedTable($array, $options = [])
    {
        if (!is_array($array)) {
            $array = [];
        }

        $default = [
            'class' => 'nested-table',
            'key' => __('Key'),
            'value' => __('Value'),
            'header' => true
        ];
        $options = array_merge($default, $options);

        $listName = $options['list-name'] ?? Attributes::uuid('list-');
        $bodyAttributes = ['data-list-name' => $listName];

        if ($options['tree'] ?? false) {
            $options['class'] = ['widget-table', 'widget-tree', $options['class']];
            $bodyAttributes['data-list-tree'] = 'collapsed';
        }

        $array = Arrays::nestedToList($array);

        $out = $this->Element->openHtmlElement('table',
            [
                'class' => $options['class']
            ]
        );

        if ($options['header']) {
            $out .= '<thead><tr>';
            $out .= '<th>' . $options['key'] . '</th>';
            $out .= '<th>' . $options['value'] . '</th>';
            $out .= '</tr></thead>';
        }


        $out .= $this->Element->openHtmlElement('tbody', $bodyAttributes);
        foreach ($array as $value) {

            if ($options['tree'] ?? false) {
                $indent = str_repeat('<div class="tree-indent"></div>', $value['level'] + 1);
                $indent .= '<div class="tree-content">' . $value['key'] . '</div>';
            } else {
                $indent = str_repeat("\t", $value['level']) . $value['key'];
            }

            if (isset($value['size'])) {
                $label = " <i>[" . $value['size'] . "]</i>";
            } else if (is_bool($value['value'])) {
                $label = ($value['value'] ? 'true' : 'false');
            } else {
                $label = $value['value'];
            }

            $trAttributes = [
                'data-list-itemof' => $listName,
                'data-id' => $value['id'] ?? null,
                'data-tree-parent' => $value['parent_id'] ?? null,
                'class' => 'node item-collapsed'
            ];

            $out .= $this->Element->openHtmlElement('tr', $trAttributes);
            $out .= '<th scope="row">' . "{$indent}" . '</th>';
            $out .= '<td>' . $label . '</td>';
            $out .= $this->Element->closeHtmlElement('tr');
        }
        $out .= '</tbody>';

        $out .= $this->Element->closeHtmlElement('table');

        return $out;
    }

    /**
     * Output a placeholder for content that will automatically be fetched
     *
     * @param $url
     * @return void
     */
    public function ajaxTable($url) {
      return '<div class="widget-loadcontent" data-url="' . Router::url($url, true) . '">'
          . '<div class="loader"></div>'
          . '<div class="widget-scrollbox" data-snippet="rows"></div>'
          . '</div>';
    }

    /**
     * Create a paginated table for entities
     *
     * // TODO: bundle tree options (tree, fold, details)  into a subarray of options
     * // TODO: bundle select options (label, targets)  into a subarray of options
     *
     * ### Options
     * - select: true|false. Whether to generate a column selector
     * - indent: true|false. Whether to generate an extra first column. Defaults to false.
     * - columns: Column definitions, see PermissionsTable::getColumns() or ProjectsTable::getColumns() as examples
     * - actions: Key 'view' = true | false. Key 'open' contains a URL with placeholders
     *            that will be processed by LinkHelper->fillPlaceholders. Placeholder are in curly brackets
     *            and will be replaced by the entity's values.
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
     * - flow: Where clicked rows are opened: frame (default) tab or popup
     *
     * @param string $model The model name with plugin notation, lower case, for example "epi.projects"
     * @param array|ResultSet $entities List of entities
     * @param array $options An array of optional options
     *
     * @return string
     */
    public function filterTable($model, $entities, $options=[])
    {
        $group = str_replace('.','_',$model);
        $options['seek'] = $this->_View->getConfig('options')['params']['seek'] ?? null;
        $options['selected'] = $options['selected'] ?? $this->_View->getConfig('options')['params']['selected'] ?? (!empty($options['seek']) ? [$options['seek']] : []);
        $options['direction'] = $this->_View->getConfig('options')['params']['direction'] ?? 'asc';

        $options['columns_all']  = $options['columns'] ?? $this->_View->getConfig('options')['columns'] ?? [];
        $options['columns'] = $this->getSelectedColumns($options['columns_all']);

        // Collapsed option
        if (empty($options['tree'])) {
            $options['tree'] = false;
        }  elseif ($options['collapsed'] ?? false) {
            $options['tree'] = 'collapsed';
        } else {
            $options['tree'] =  $options['tree']  ?? true;
        }

        if ($options['tree'] === 'collapsed') {
            $options['collapsed'] = true;
        } else {
            $options['collapsed'] = $options['collapsed'] ?? false;
        }

        $loadChildren = $this->_View->getConfig('options')['params']['children'] ?? false; // TODO: and cursor is set
        if ($loadChildren) {
            $options['details'] = true;
        }

        // Table
        // TODO: rename class recordlist to filter-table
        $clickTarget = $options['flow'] ?? 'frame';
        $tableClasses = ['recordlist widget-table actions-to' . $clickTarget, $options['class'] ?? null];
        if ($options['tree'] ?? false) {
            $tableClasses[] = 'widget-tree';
        }
        if ($options['drag'] ?? false) {
            $tableClasses[] = 'widget-dragitems';
        }

        $tableAttributes = [
            'class' => $tableClasses,
            'data-filter-group' => $group,
            'data-snippet' => 'rows',
            'data-model' => $model
        ];

        if ($options['sort'] ?? true) {
            $tableAttributes['data-sortdir'] = $this->Paginator->sortDir();
            $tableAttributes['data-sortkey'] = $this->Paginator->sortKey();
        }

        if (!($options['snippet'] ?? true)) {
            unset($tableAttributes['data-snippet']);
        }

        $tableAttributes = array_merge($tableAttributes, $options['data'] ?? []);

        // Output Table
        $out = $this->Element->openHtmlElement('table', $tableAttributes );
        $out .= $this->getTableHeader($model, $entities, $options);
        $out .= $this->getTableBody($model, $entities, $options);
        $out .= $this->getTableFooter($model, $entities, $options);
        $out .= $this->Element->closeHtmlElement('table');
        return $out;
    }

    public function getTableHeader($model, $enitites, $options=[])
    {
        $request = $this->_View->getRequest();
        $group = str_replace('.','_',$model);
        $columns_all = $options['columns_all'] ?? [];
        $columns = $options['columns'] ?? [];

        $out = "<thead><tr>\n";
        $colSelector = $options['select'] ?? false;
        $colExtra = $options['indent'] ?? false;

        if ($colExtra) {
            $out .= '<th scope="col" class="cols-toggle cols-fixed">';
            if ($colSelector) {
                $out .= $this->columnSelector($group, $columns_all);
            }
            $out .= '</th>';
        }

        $firstColumn = true;
        foreach ($columns as $fieldName => $fieldOptions) {

            // @deprecated: always use alphabetic column keys in the types config
            $fieldName = is_numeric($fieldName) ? $fieldOptions['name'] ?? $fieldName : $fieldName;

            // Open table cell
            $thAttributes = array_filter([
                'scope' => 'col',
                'data-col' => Inflector::underscore($fieldName),
                'data-width' =>  $fieldOptions['width'] ?? '',
                'class' => isset($fieldOptions['align']) ? 'align-' . $fieldOptions['align'] : ''
            ]);

            // Initial column width
            if (!empty($fieldOptions['width'])) {
                $thAttributes['style'] = [
                    'position' =>'relative',
                    'width' => ($fieldOptions['width'] ?? '') .'px',
                    'max-width' => ($fieldOptions['width'] ?? '') .'px'
                ];
            }
            $out .= '<th ' . Attributes::toHtml($thAttributes) . '>';

            if ($firstColumn && $colSelector && !$colExtra) {
                $out .= $this->columnSelector($group, $columns_all);
            }

            // Sort
            if (($options['sort'] ?? true) && ($fieldOptions['sort'] ?? false)) {
                $sortField = is_string($fieldOptions['sort']) ? $fieldOptions['sort'] : $fieldOptions['sort']['key'] ?? $fieldName;
                $out .= $this->Paginator->sort($sortField, ['label' => $fieldOptions['caption'] ?? $fieldName]);
            } else {
                $out .=  $fieldOptions['caption'] ?? $fieldName;
            }

            // Filter
            if (($fieldOptions['filter'] ?? '') === 'select') {
                $out .= $this->columnFilter(
                    $group,
                    $fieldName,
                    $fieldOptions['options'] ?? [],
                    Attributes::commaListToStringArray($request->getQuery($fieldName)),
                    [
                        'class' => ($fieldOptions['main'] ?? false) ? 'content-searchbar-item-main' : 'content-searchbar-item',
                        'checkboxlist' => true,
                        'dropdown' => true,
                        'reset' => true
                    ]
                );
            }

            $out .= "</th>\n";
            $firstColumn = false;
        }

        if ($options['actions'] ?? true) {
            $out .= '<th scope="col" class="actions">' . __('Actions') . '</th>';
        }

        $out .= '</tr></thead>';
        return $out;
    }

    public function getTableFooter($model, $entities, $options=[])
    {
        if (empty($options['more'])) {
             return '';
        }

        $out = '<tfoot><tr>';

        $colSpan = count($options['columns']) +
            (int)($options['indent'] ?? false);

        $out .= '<td colspan="' . $colSpan. '">'
            . $this->Html->link(__('See more...'), $options['more'], ['class' => 'more'])
            . '</td>';

        if ($options['actions'] ?? true) {
            $out .= '<td class="actions"></td>';
        }

        $out .= '</tr></tfoot>';
        return $out;
    }

    public function getTableBody($model, $entities, $options=[])
    {

        $out = '';
        $group = str_replace('.','_',$model);

        if ($this->_View->getConfig('options')['params']['children'] ?? false) { // TODO: and cursor is set
            $data = [];
        }
        else {
            $data = [];

            if (($options['paginate'] ?? true) === 'cursor') {
                $data['data-list-seek'] = $options['seek'] ?? '';
                $data['data-list-tree'] = empty($options['tree']) ? '' : 'collapsed';
            }
            else {
                $data['data-list-action-next'] = $this->Link->nextPageUrl($options['scope'] ?? null);
            }

            $data['data-list-action-get'] = $this->Link->getRowUrl($options['scope'] ?? null);

            if ($options['drag'] ?? false) {
                $data['data-list-action-move'] = $this->Link->moveUrl($options['scope'] ?? null);
            }
        }

        // Table body
        $out .= '<tbody data-list-name="' . $group . '" '  . Attributes::toHtml($data, false) . '>';

        $out .= $this->Tree->getNodes($model, $entities, [$this, 'getTableRow'], $options);

        $out .= '</tbody>';
        return $out;
    }

    /**
     * Output all rows of an entity. See filterTable().
     *
     * ### Options
     * - columns
     * - indent
     * - collapsed
     * - label
     * - tree
     * - fold
     * - actions
     * - details
     *
     * @param string $model The model name with plugin notation, lower case, for example "epi.projects"
     * @param Entity $entity
     * @param boolean|string $cursor prev|next|child|false
     * @param array $options
     * @return string A string containing the fully blown tr element
     */
    public function getTableRow($model, $entity, $cursor=false, $options=[])
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
        $typeMerged = $type['merged'] ?? [];
        $typeName = $type['name'] ?? null;

        $itemAttributes = $this->Tree->getAttributes($entity, $type, $cursor, $options);
        $itemAttributes['data-list-itemof'] = $group;

        // Label for selectors
        if ($options['label'] ?? false) {
            $itemAttributes['data-label'] = $entity['caption_ext'] ?? $entity['caption_path'] ?? $entity['caption'] ?? '';
            $itemAttributes['data-value'] = ($entity['table_name'] ?? '') . '-' . ($entity['id'] ?? '');
        }

        // TODO: upgly condition, refactor
        if ($entity['search'] ?? false) {
            if (!isset($options['content'])) {
                $options['content'] = [];
            }
            if (!in_array('search', $options['content'])) {
                $options['content'][] = 'search';
            }
        }

        if (!empty($options['content'])) {
            $itemAttributes['class'][] = 'row-main';
        }

        $out = $this->Element->openHtmlElement('tr', $itemAttributes);

        $columns = $options['columns'] ?? [];
        $colExtra = $options['indent'] ?? false;
        if ($colExtra) {
            $out .= '<td class="first cols-fixed"></td>';
        }

        $firstColum = true;
        foreach ($columns as $fieldName => $fieldOptions) {

            $content = '';
            if (empty($cursor)) {
                // Get value path from type or field options
                $valuePath = $fieldOptions['types'][$typeName]['extract'] ??
                    $fieldOptions['types'][$typeName]['key'] ??
                    $fieldOptions['types'][$typeName]['id'] ??
                    $fieldOptions['extract'] ??
                    $fieldOptions['key'] ??
                    $fieldOptions['id'] ??
                    $fieldName;

                // - See: //TODO: use getValueFormatted()
                if (($valuePath === 'lemma') && !empty($entity->lookup_to)) {

                    $edgeLabelField = $typeMerged['edge']['displayfield'] ?? 'lemma';
                    $edge = $entity->getValueNested($edgeLabelField);
                    $edge = empty($edge) ? __('See') : $edge;

                    $content = $edge . ' ' . $entity['lookup_to']['path'];
                }
                // - Reference from: //TODO: use getValueFormatted()
                elseif (($valuePath === 'lemma') && !empty($entity->lookup_from)) {

                    //$edgeLabelField = $typeMerged['edge']['displayfield'] ?? 'lemma';
                    //$edge = $entity->getValueNested($edgeLabelField);
                    $edge = __('Reference from:');
                    $content = $edge . ' ' .  $entity['lookup_from']['path'];
                }
                // Image: //TODO: use getValueFormatted instead special image handling
                elseif (($valuePath === 'file_name') && $entity->file_exists) {
                    $content = $this->Files->outputThumb(
                        $entity->file_downloadname,
                        $entity->file_downloadpath,
                        Databank::removePrefix($entity->databaseName)
                    );
                }

                elseif ($entity instanceof BaseEntity) {
                    $fieldOptions['format'] = 'html';
                    $fieldOptions['aggregate'] = $fieldOptions['aggregate'] ?? 'collapse';
                    $content = $entity->getValueNested($valuePath, $fieldOptions);
                    $content = is_array($content) ? h(json_encode($content)) : $content;
                }
                else {
                    $content = $entity[$valuePath] ?? '';
                }

                // Link value
                if (!empty($fieldOptions['link'])) {
                    if (($fieldOptions['link'] === 'iri')  && ($type ?? false)) {
                        // Default namespace
                        $namespace = Hash::get(
                            $typeMerged,
                            'namespaces.default',
                            '/epi/' . Databank::removePrefix($entity->databaseName) . '/iri/' . $entity->table_name . '/' . $type->name . '/'
                        );
                        $link = $namespace . $entity['norm_iri'];
                    }
                    else {
                        $link = $this->Link->fillPlaceholders($fieldOptions['link'], $entity);
                    }

                    $content = $this->Html->link($content, $link);
                }
            }

            // Open table cell
            $tdClasses = [];
            if (isset($fieldOptions['align'])) {
                $tdClasses[] = 'align-' . $fieldOptions['align'];
            }

            // TODO: use getValueFormatted to output the values
            if (empty($cursor)) {
                if (($fieldOptions['type'] ?? '') === 'test') {
                    $tdClasses[] = 'test_' . (empty($content) ? 'failed' : 'success');
                    $content = empty($content) ? 'x' : '✓';
                }

                if ((($fieldOptions['type'] ?? '') === 'badge')) {
                    $tdClasses[] = 'badge_' . (empty($content) ? 'false' : 'true');
                    $content = empty($content) ? $fieldOptions['badge'][0] ?? '' : $fieldOptions['badge'][1] ?? '✓';
                }
            }

            $tdAttributes = [];
            $tdAttributes['class'] =  empty($tdClasses) ? null : $tdClasses;

            $out .= $this->Element->openHtmlElement('td', $tdAttributes);

            // Wrap content in the first column
            if ($firstColum) {
                if ($options['tree'] ?? false) {
                    $out .=  $this->Tree->getIndentation($entity, $options['fold'] ?? 'foldable', $cursor);
                }
                $out .= '<div class="tree-content">' . $content . '</div>';
                $firstColum = false;
            }
            // Node content
            else {
                $out .= $content;
            }

            // Close table cell
            $out .= $this->Element->closeHtmlElement('td');
        }

        // Row actions
        if ($options['actions'] ?? true) {
            $out .= '<td class="actions">';

            if (empty($cursor)) {

                foreach (['view', 'open', 'tab'] as $actionType) {
                    if ($options['actions'][$actionType] ?? false) {
                        $action = $options['actions'][$actionType];
                        $action = $action === true ? ['action' => 'view', $entity['id'] ?? ''] : $action;
                        $action = $this->Link->fillPlaceholders($action, $entity);
                        $action['title'] = $action['title'] ?? __('View');
                        $out .= $this->Html->link($action['title'], $action, ['data-role' => $actionType]);
                    }
                }

            }
            $out .= '</td>';
        }

        $out .= '</tr>';

        // Child nodes
        // TODO: Merge with getTableDetailRows()
        if (!empty($options['content'])) {
            $out .= $this->getTableTextRows($model, $entity, $options);
        }

        if ($options['details'] ?? false) {
            $out .= $this->getTableDetailRows($model, $entity, $options);
        }

        return $out;
    }

    /**
     * Output fulltext search results
     *
     * // TODO: Merge with filterTableDetailRows()
     *
     * @param $model
     * @param $entity
     * @param $options
     * @return string
     */
    public function getTableTextRows($model, $entity, $options)
    {
        $out = '';
        $columns = $options['columns'] ?? [];
        $group = str_replace('.','_',$model);
        $colExtra = $options['indent'] ?? false;

        // Child nodes
        $childColumns = ['caption' => 'caption', 'content' => 'content'];
        $contentConfig = $options['content'] ?? [];
        foreach ($contentConfig as $contentField) {
            // TODO: Do we need this special condition? Refactor
            if ($contentField === 'search') {
                $entityContent = $entity->search;
            } else {
                $entityContent = $entity->getValueNested($contentField);
            }

            foreach ($entityContent as $childRow) {

                if (empty($childRow)) {
                    continue;
                }

                $childRow->id = 'hit-' . $childRow->id;
                $childRow->parent_id = $entity->id;
                $childRow->level = 1;
                $childRow->tree_level = 1;
                $childRow->tree_parent = $entity;


                $trAttributes = $this->Tree->getAttributes($childRow);
                $trAttributes['data-list-itemof'] = $group;

                $trAttributes['class'][] = 'row-supplement';
                if (($options['tree'] ?? false) === 'collapsed') {
                    $trAttributes['class'][] = 'item-hidden';
                }

                $out .= $this->Element->openHtmlElement('tr', $trAttributes);

                if ($colExtra) {
                    $out .= '<td class="first cols-fixed"></td>';
                }

                // First column
                $indent = ($options['tree'] ?? false) ? $this->Tree->getIndentation($childRow,
                    $options['fold'] ?? 'foldable') : '';
                $prefix = ($options['tree'] ?? false) ? '' : ' ▪ ';

                $content = $childRow[$childColumns['caption']] ?? '';
                if (!empty($content)) {
                    $content = $prefix . $content;
                }

                $out .= $this->Element->outputHtmlElement(
                    'td',
                    $indent
                    . '<div class="tree-content">'
                    . $content
                    . '</div>',
                    ['class' => 'tree-cell']
                );

                // Second column (spans all other columns)
                $out .= $this->Element->outputHtmlElement(
                    'td',
                    '<p>' . $childRow[$childColumns['content']] . '</p>',
                    ['colspan' => count($columns) - 1]
                );

                if ($options['actions'] ?? true) {
                    $out .= '<td class="actions">';


                    foreach (['view', 'open', 'tab'] as $actionType) {
                        if ($options['actions'][$actionType] ?? false) {
                            $action = $options['actions'][$actionType];
                            $action = $action === true ? ['action' => 'view', $entity['id'] ?? ''] : $action;
                            $action = $this->Link->fillPlaceholders($action, $entity);
                            $action['title'] = $action['title'] ?? __('View');

                            if (($action['action'] === 'view') && !empty($childRow['highlight'])) {
                                $action['?']['highlight'] = implode(',', $childRow['highlight']);
                            }

                            $out .= $this->Html->link($action['title'], $action, ['data-role' => $actionType]);
                        }
                    }

                    $out .= '</td>';
                }

                $out .= $this->Element->closeHtmlElement('tr');
            }
        }
        return $out;
    }


    /**
     * Output article sections, items and so on
     *
     * Set the details option to:
     * - false No detail rows are generated
     * - true All detail rows are generated
     * - 'cursor'  Cursor rows for loading details are generated
     *
     * @param $model
     * @param $entity
     * @param $options
     * @return string
     */
    public function getTableDetailRows($model, $entity, $options)
    {
        $details = $options['details'] ?? 'cursor';

        $out = '';
        $columns = $options['columns'] ?? [];
        $group = str_replace('.','_',$model);
        $colExtra = $options['indent'] ?? false;

        // Cursor for loading details
        if ($details === 'cursor') {
            $trAttributes = $this->Tree->getAttributes($entity, null, 'child');
            $trAttributes['data-list-itemof'] = $group;

            if (($options['tree'] ?? false) === 'collapsed') {
                $trAttributes['class'][] = 'item-hidden';
            }
            $out .= $this->Element->openHtmlElement('tr', $trAttributes);
            if ($colExtra) {
                $out .= '<td class="first cols-fixed"></td>';
            }

            // First column
            $indent = ($options['tree'] ?? false) ? $this->Tree->getIndentation($entity, $options['fold'] ?? 'foldable', 'child') : '';
            $out .= $this->Element->outputHtmlElement(
                'td',
                $indent,
                ['class' => 'tree-cell']
            );

            // Second column (spans all other columns)
            $out .= $this->Element->outputHtmlElement(
                'td', '', ['colspan' => count($columns) - 1]
            );

            $out .= '<td class="actions"></td>';
            $out .= $this->Element->closeHtmlElement('tr');
        }

        elseif ($details) {

            foreach ($entity->getTree($options['targets'] ?? []) as $childRow) {

                if ($childRow['id'] === ('articles-' . $entity->id)) {
                    continue;
                } elseif ($childRow['parent_id'] === ('articles-' . $entity->id)) {
                    $childRow->parent = $entity;
                    $childRow->parent_id = $entity->id;
                    $childRow->tree_parent = $entity;
                }

//                $childRow->id = 'hit-' . $childRow->id;
//                $childRow->parent_id = $entity->id;
//                $childRow->level = 1;
//                $childRow->tree_level = 1;

                $trAttributes = $this->Tree->getAttributes($childRow);
                $trAttributes['data-list-itemof'] = $group;

                if ($options['label'] ?? false) {
                    $trAttributes['data-label'] = $childRow['caption_ext'] ?? $childRow['caption_path'] ?? $childRow['caption'] ?? '';
                    $trAttributes['data-value'] = $childRow['id'] ?? '';
                }

                if (in_array($childRow['id'], $options['selected'] ?? [])) {
                    $trAttributes['class'][] = 'row-selected';
                }


                $trAttributes['class'][] = 'row-supplement';
                if (($options['tree'] ?? false) === 'collapsed') {
                    $trAttributes['class'][] = 'item-hidden';
                }

                $out .= $this->Element->openHtmlElement('tr', $trAttributes);

                if ($colExtra) {
                    $out .= '<td class="first cols-fixed"></td>';
                }

                // First column
                $indent = ($options['tree'] ?? false) ? $this->Tree->getIndentation($childRow, $options['fold']  ?? 'foldable') : '';
                $prefix = ($options['tree'] ?? false) ? '' : ' - ';

                $content = '<em>' . $prefix . $childRow['caption'] . '</em>';

                $out .= $this->Element->outputHtmlElement(
                    'td',
                    $indent
                    . '<div class="tree-content">'
                    . $content
                    . '</div>',
                    ['class' => 'tree-cell']
                );

                // Second column (spans all other columns)
                $out .= $this->Element->outputHtmlElement(
                    'td',
                    '',
                    ['colspan' => count($columns) - 1]
                );


                $out .= '<td class="actions"></td>';
                $out .= $this->Element->closeHtmlElement('tr');
            }
        }

        return $out;
    }

    /**
     * Create a key value list of sortable columns
     *
     * @param array $columns Column definitions
     *
     * @return array
     *
     * //todo merge getSortableColumns and getSortableFields
     */
    public function getSortableColumns($columns)
    {
        $columns = array_filter($columns, fn($x) =>  ( ($x['sort'] ?? false)  !== false) && ($x['selectable'] ?? true) );
        $sortFields = array_map(
            fn($col, $key) => (is_string($col['sort'] ?? false) ? $col['sort'] : ($col['sort']['key'] ?? $col['name'] ?? $key)),
            $columns,
            array_keys($columns)
        );
        $sortLabels = array_map(fn($col) => is_string($col) ? $col : $col['caption'], $columns);
        $fields = array_combine($sortFields,$sortLabels);
        return $fields;
    }

    /**
     * Get selected columns
     *
     * @param $columns
     *
     * @return array
     */
    public function getSelectedColumns($columns)
    {
        $selected = array_filter($columns, fn($x) => ($x['selected'] ?? false));
        if (empty($selected)) {
            $selected = array_filter($columns, fn($x) => ($x['default'] ?? false));
        }

        //$selected = $this->getSelectableColumns($selected);

        return $selected;
    }

    /**
     * Get columns for the column selector
     *
     * @param $columns
     *
     * @return array
     */
    public function getSelectableColumns($columns)
    {
        $action = $this->_View->getRequest()->getParam('action');
        return array_filter($columns, fn($x) => $this->getCanShowColumn($x, $action));
    }

    /**
     * Output column
     *
     * @param $fieldOptions
     * @param $action
     *
     * @return bool
     */
    protected function getCanShowColumn($fieldOptions, $action) {
        $modes = !isset($fieldOptions['action']) ?
            [$action] :
            (
            is_string($fieldOptions['action']) ?
                [$fieldOptions['action']] :
                $fieldOptions['action']
            );

        $visible = $fieldOptions['selectable'] ?? true;
        return $visible && in_array($action, $modes);
    }

    /**
     * Create a selector filter in the table header
     * (e.g. to select results by project)
     *
     * @param string $group The filter group (see filter.js)
     * @param string $param The URL parameter that should be managed. Leave empty to manage the path parameter.
     * @param array $items The options that can be selected
     * @param array $selected Currently selected options
     * @param array $options An option array containing the following keys:
     *                       - label
     *                       - class
     *                       - search
     *                       - checkboxlist
     * @return string
     */
    public function columnFilter($group, $param, $items, $selected = [], $options = [])
    {
        // Wrapper
        $out = '';

        $classes = array_filter([
            'widget-filter-item widget-filter-item-selector',
            $options['class'] ?? ''
        ]);

        if (!empty($selected)) {
            $classes[] = 'widget-filter-item-selector-active';
        }

        $data = [
            'data-filter-group' => $group,
            'data-filter-param' => $param
        ];
        $out .= '<div class="' . implode(' ',$classes) . '" ' . Attributes::toHtml($data) . '>';

        // Pane
        $paneId = 'select-pane-' . str_replace('.','-', $param);
        $pane = $this->filterPane($paneId, $items, $selected, $options);

        // Inputs
        $inputName = 'select-input-' . str_replace('.','-', $param);
        $inputValue = implode(',',$selected);

        $options = [
            'caption' => false,
            'type' => 'reference',
            'pane' => $pane,
            'paneId' => $paneId,
            'paneAlignTo' =>' th',
            'value' => $inputValue,
            'button' => '<input type="button" class="widget-filter-item-selector-button" value=" ">'
        ];
        $out .= $this->Form->input($inputName, $options);

        $out .= '</div>';

        return $out;
    }

    /**
     * Create a column selector button
     *
     * The named list of column definitions may contain the following keys:
     * - name Internal name of the column, corresponds the key in the column definitions
     * - key: Key to extract in Hash compatible syntax
     * - caption Column caption
     * - default Boolean value indicating if the column is visible by default
     * - aggregate If the key extracts multiple values, aggregate them using "collapse" or "count"
     * - sort Array defining the sort field and necessary joins (table, type, field, aggregate)
     *
     * @param string $group The model name
     * @param array $columns The named list of column definitions
     *
     * @return string
     */
    public function columnSelector($group, $columns)
    {
        $columns = $this->getSelectableColumns($columns);

        // Create checkbox items
        $items = collection($columns)->map(
            function ($column)  {
                $name = Inflector::underscore(strval($column['name'] ?? $column['key']));
                //$selected = in_array($name, $selected) || !count($selected);
                return [
                    'title' => $column['caption'],
                    'name' => 'fields.' . $name,
                    'value' => $name,
                    'checked' => $column['selected'] ?? false
                ];
            }
        )->toArray();

        $out = '<button
            type="button"
            class="widget-dropdown widget-dropdown-toggle button-settings widget-filter-item widget-filter-item-columns"
            data-toggle="' . $group . '-settings-fields"
            data-filter-group="' . $group . '"
            title="' . __('Select columns') . '"
            aria-label="' . __('Select columns') . '">⚙</button>';

        $out .= $this->Element->outputHtmlElement('div', __('Select Columns'), [
            'class' => 'widget-dropdown-pane-header',
        ]);

        $out .= '<div id="' . $group . '-settings-fields"
            class="widget-dropdown-pane widget-scrollbox"
            data-widget-dropdown-position="right"
            data-snippet="' . $group . '-settings-fields">';

        $out .= $this->checkboxList($items, [
            'class' => 'selector-columns selector-checkboxes widget-tree widget-dragitems widget-dragitems-enabled',
            'data' => [
                'list-name' => 'columns'
            ],
            'item-data' => [
                'list-itemof' => 'columms'
            ],
            'reset' => true,
            'grip-icon' => true
        ]);
        $out .= '</div>';

        return $out;
    }

    /**
     * Create a filter bar based on column definition
     *
     * @param string $model The model name, with plugin notation, lowercase (knits together the different search inputs)
     * @param boolean $options Whether to show select filters
     * @param array $columns Column definitions, see PermissionsTable::getColumns() as an example.
     *                       Set to null to get columns from the options
     *
     * @return string
     */
    public function filterBar($model, $options = false, $columns=null)
    {
        $group = str_replace('.','_',$model);

        $request = $this->_View->getRequest();
        if ($columns === null) {
            $columns = $this->_View->getConfig('options')['columns'] ?? [];
        }
        if (empty($options)) {
            $columns = array_filter($columns, fn($x) => (($x['filter'] ?? '') === 'text'));
        }
        $out = $this->filterFormStart($group);

        foreach ($columns as $fieldName => $fieldOptions) {

            if (empty($fieldOptions['filter']) || !$this->getCanShowColumn($fieldOptions, $request->getParam('action'))) {
                continue;
            }

            if (($fieldOptions['filter'] ?? '') === 'text') {
                $out .= $this->filterSearch(
                    $group,
                    '',
                    $fieldName,
                    $request->getQuery($fieldName),
                    [],
                    [
                        'class' => ($fieldOptions['main'] ?? false) ? 'content-searchbar-item-main' : '',
                        'label' => $fieldOptions['caption'] ?? $fieldName,
                    ]
                );
            }

            if (($fieldOptions['filter'] ?? '') === 'select') {
                $out .= $this->filterSelector(
                    $group,
                    $fieldName,
                    $fieldOptions['options'] ?? [],
                    Attributes::commaListToStringArray($request->getQuery($fieldName)),
                    [
                        'class' => ($fieldOptions['main'] ?? false) ? 'content-searchbar-item-main' : '',
                        'label' => $fieldOptions['caption'] ?? $fieldName,
                        'checkboxlist' => true,
                        'reset' => true
                    ]
                );
            }
        }

//        $out .= $this->filterReset($group);

        $out .= $this->filterFormEnd();
        return $out;
    }

    /**
     * Open a form element for the search bar
     *
     * @param $group
     * @param $url
     *
     * @return mixed
     */
    public function filterFormStart($group, $url = null)
    {
        return $this->Form->create(null, [
            'type' => 'get',
            'class' => 'search widget-filter',
            'data-filter-group' => $group,
            'data-action' => $url
        ]);
    }

    /**
     * Close the form element of the search bar
     *
     * @return mixed
     */
    public function filterFormEnd()
    {
        return $this->Form->end();
    }

    /**
     * Create a filter reset buttons
     *
     * @return string
     */
    public function filterReset($group, $url = [])
    {
        // Wrapper
        $out = '';
        $classes = array_filter([
            'content-searchbar-item content-searchbar-item-reset',
            'widget-filter-item widget-filter-item-reset',
            $options['class'] ?? ''
        ]);
        $data = [
            'data-filter-group' => $group,
            'data-snippet' => 'filter-search-reset'
        ];

        $out .= '<div class="' . implode(' ',$classes) . '" ' . Attributes::toHtml($data) . '>';


        $url['?'] = array_merge($url['?'] ?? [], ['save'=>true]);
        $out .= $this->Html->link('Reset',$url,['title'=>__('Reset filter and columns.')]);

        // Close wrapper
        $out .= '</div>';


        return $out;
    }

    /**
     * Create a search box
     *
     * ### Options
     * - label
     * - placeholder
     * - class
     * - form: If the form key contains a URL, a get form is created.
     * - data: Additional data attributes for the search bar
     *
     * @param string $group The filter group (see filter.js)
     * @param string $prefix Prefix added to the parameters (to field, sort, direction, term)
     * @param string $param The URL parameter that should be managed (usually 'term')
     * @param string $value Current search value
     * @param array $searchFields If not empty, a field selector is created. Set the selected field to the field key, provide all field options in the options key
     * @param array $options
     *
     * @return string
     */
    public function filterSearch($group, $prefix, $param, $value, $searchFields = [], $options = [])
    {
        // Wrapper
        $out = '';
        $classes = array_filter([
            'content-searchbar-item widget-filter-item widget-filter-item-searchbar',
            $options['class'] ?? ''
        ]);
        $data = [
            'data-filter-group' => $group,
            'data-filter-param' => $param,
            'data-filter-prefix' => $prefix
        ];
        $data = array_merge($data, $options['data'] ?? []);

        $out .= '<div class="' . implode(' ',$classes) . '" ' . Attributes::toHtml($data) . '>';

        if ($options['form'] ?? false) {
            $out .= $this->filterFormStart($group, $options['form']);
        }

        $out .='<div class="input-group input-group-filter first">'; //first parameter?

        // Label
        if (!empty($options['label'])) {
            $out .= '<div class="input-group-label hide-small">' . $options['label'] . '</div>';
        }

        // Input
        $out .= '<div class="input-group-field">';

        $inputOptions = [
            'label' => false,
            'value' => $value,
            'placeholder' => $options['placeholder'] ?? '',
            'class' => 'search-term',
            'type' => 'text',
            'autocomplete' => 'off'
        ];
        if ($options['autofocus'] ?? false) {
            $inputOptions['autofocus'] = true;
        }

        $out .= $this->Form->control($param, $inputOptions);

        $out .= '</div>';

        // Field selector
        if (!empty($searchFields)) {
            $out .= '<div class="input-group-button">';

            $out .= $this->Form->select('field', $searchFields['options'] ?? [],
                [
                    'value' => $searchFields['field'] ?? '',
                    'empty' => false,
                    'class' => 'search-field'
                ]
            );

            $out .= '</div>';
        }

        // Close wrapper
        $out .= '</div>';

        if ($options['form'] ?? false) {
            $out .= $this->filterFormEnd();
        }

        $out .= '</div>';

        return $out;
    }

    /**
     * Create a row of letters
     *
     * @param string $group The filter group (see filter.js)
     * @param string $prefix Prefix added to the parameters (to field, sort, direction, term)
     * @param string $param The URL parameter that should be managed (usually 'letter')
     * @param string $value Current letter
     * @param array $options An array containing the keys label, placeholder, class and form. If the form key contains a URL, a get form is created.
     *
     * @return string
     */
    public function filterLetters($group, $prefix, $param, $value, $options = [])
    {
        // Wrapper
        $out = '';
        $classes = array_filter([
            'content-searchbar-item content-searchbar-item-letters',
            $options['class'] ?? ''
        ]);
        $data = [
            'data-filter-group' => $group,
            'data-filter-param' => $param,
            'data-filter-prefix' => $prefix,
            'data-snippet' => 'filder-search-letters'
        ];

        $out .= '<div class="' . implode(' ',$classes) . '" ' . Attributes::toHtml($data) . '>';

        $out .= '<div class="input-group input-group-letters">';

        // Label
        if (!empty($options['label'])) {
            $out .= '<div class="input-group-label">' . $options['label'] . '</div>';
        }

        // Input
        $out .= '<div class="input-group-field">';

        foreach (range('a', 'z') as $letter) {
            $letterClasses = ['content-searchbar-letter'];
            if (mb_strtolower($value) === $letter) {
                $letterClasses[] = 'active';
            }

            $letterUrl = $this->Link->getUpdatedUrl(['?'=>['letter'=>$letter,'save'=>true]]);

            $out .= '<span '. Attributes::toHtml(['class' => $letterClasses]) .'>'
                . $this->Html->link($letter,$letterUrl)
                . '</span>';
        }

        $letterUrl = $this->Link->getUpdatedUrl(['?'=>['letter'=>'','save'=>true]]);
        $out .= '<span '. Attributes::toHtml(['class' => 'content-searchbar-letter']) .'>'
            . $this->Html->link('*',$letterUrl)
            . '</span>';

        $out .= '</div>';

        // Close wrapper
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }

    /**
     * Create a filter selector element (e.g. to select results by project)
     *
     * @param string $group The filter group (see filter.js)
     * @param string $param The URL parameter that should be managed. Leave empty to manage the path parameter.
     * @param array $items The options that can be selected
     * @param array $selected Currently selected options
     * @param array $options Options:
     *                       - label
     *                       - class
     *                       - searchable
     *                       - checkboxlist
     *                       - clear Whether all other parameters should be cleared when this selector changes     *
     * @return string
     */
    public function filterSelector($group, $param, $items, $selected = [], $options = [])
    {
        $dropdown = $options['dropdown'] ?? true;

        // Wrapper
        $out = '';

        $classes = array_filter([
            'content-searchbar-item widget-filter-item widget-filter-item-selector',
            $options['class'] ?? ''
        ]);
        $data = [
            'data-filter-group' => $group,
            'data-filter-param' => $param,
            'data-filter-clear' => $options['clear'] ?? false
        ];
        $data = array_merge($data, $options['data'] ?? []);

        $out .= '<div class="' . implode(' ',$classes) . '" ' . Attributes::toHtml($data) . '>';

        // Group wrapper
        $out .= '<div class="input-group input-group-filter">';

        // Label
        if (!empty($options['label'])) {
            $out .= '<div class="input-group-label">' . $options['label'] . '</div>';
        }

        // Field
        $out .= '<div class="input-group-field">';

        // Pane
        $paneId = 'select-pane-' . str_replace('.','-', $param);
        $options['dropdown'] = $dropdown;
        $pane = $this->filterPane($paneId, $items, $selected, $options);

        // Inputs
        $inputName = 'select-input-' . str_replace('.','-', $param);
        $inputText =  $this->filterSelectorDropdownCaption($selected, $items, $options['grouped'] ?? false,  __('selected'));
        $inputValue = implode(',',$selected);

        $options = [
            'caption' => false,
            'type' => 'reference',
            'pane' => $pane,
            'paneId' => $paneId,
            'paneAlignTo' => '.input-group',
            'value' => $inputValue,
            'text' => $inputText,
            'search' => $options['searchable'] ?? false
        ];
        $out .= $this->Form->input($inputName, $options);

        // TODO: not working
//        if ($options['autofocus'] ?? false) {
//            $options['autofocus'] = true;
//        }


        $out .= '</div>';

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }

    /**
     * Get dropdown caption
     *
     * @param $selected
     * @param $items
     * @param $grouped
     * @param $suffix
     *
     * @return string
     */
    public function filterSelectorDropdownCaption($selected, $items, $grouped, $suffix): string
    {
        $items = $grouped ? array_reduce($items,fn($carry, $group) => $carry + $group, []) : $items;

        if ((count($selected) === 1) && isset($items[$selected[0]])) {
            return $items[$selected[0]];
        }
        elseif ((count($selected) === 0) || (count($selected) === count($items)) ) {
            return '';
        }
        else {
            return count($selected) . ' ' . $suffix;
        }
    }


    /**
     * Create an input to sort results
     *
     * @param string $group The filter group (see filter.js)
     * @param string $param The URL parameter that should be managed (usually sort)
     * @param array $columns The column definitions
     * @param string $selected Currently selected option
     * @param array $options An array containing the keys label and class
     *
     * @return string
     */
    public function filterSort($group, $param ='sort' , $columns = [], $selected = '', $options = [])
    {
        $fields = $this->getSortableColumns($columns);

        // Wrapper
        $out = '';

        $classes = array_filter([
            'content-searchbar-item widget-filter-item widget-filter-item-sort',
            $options['class'] ?? ''
        ]);
        $data = [
            'data-filter-group' => $group,
            'data-filter-param' => $param
        ];
        $out .= '<div class="' . implode(' ',$classes) . '" ' . Attributes::toHtml($data) . '>';

        $out .= '<div class="input-group input-group-filter">';

        // Label
        if (!empty($options['label'])) {
            $out .= '<div class="input-group-label">' . $options['label'] . '</div>';
        }

        // Selector
        $out .= '<div class="input-group-field">';
        $out .= $this->Form->select($param, $fields, [
            'value' => $selected,
            'class' => 'widget-filter-item-sort-field'
        ]);

        $out .= '</div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }




    /**
     * Output filter pane
     *
     * // TODO: add option documentation
     *
     * ### Options
     * - dropdown
     * - checkboxlist
     * - grouped
     * - reset
     * - frame
     *
     * @param string $paneId
     * @param array $items
     * @param array $selected
     * @param array $options Options
     *
     * @return string
     */
    public function filterPane($paneId, $items, $selected = [], $options = [])
    {
        $paneClasses = [];
        if ($options['dropdown'] ?? false) {
            $paneClasses[] = 'widget-dropdown-pane';
        }

        if ($options['checkboxlist'] ?? false) {
            $paneClasses[] = 'widget-checkboxlist';
        }

        if ($options['frame'] ?? false) {
            $paneClasses[] = 'widget-dropdown-pane-frame';
        }


        $out = '<div id="' . $paneId . '" class="' . implode(' ',$paneClasses) . '">';
        $out .= '<div>';

        if ($options['reset'] ?? false) {
            $out .= '<span class="selector-reset">' . __('Reset selection') . '</span>';
        }

        // Group items
        $items = ($options['grouped'] ?? false) ? $items : [''=>$items];

        foreach ($items as $groupName => $groupItems) {
            $groupId = Attributes::cleanIdentifier($paneId . '_' . $groupName);
            $groupName = !is_string($groupName) ? '' : $groupName;

            if ($groupItems instanceof QueryInterface) {
                $groupItems = $groupItems->toArray();
            }

            $groupItems = array_map(
                function ($id, $name) use ($selected, $groupId) {
                    return [
                        'name' => $groupId . '_' . $id,
                        'title' => $name,
                        'value' => $id,
                        'url' => '#',
                        'checked' => in_array($id, $selected ?? [])
                    ];
                },
                array_keys($groupItems),
                $groupItems
            );

            if ($groupName !== '') {
                $out .= '<span class="selector-grouplabel widget-switch" '
                    . 'data-switch-element="#selector-group-' . $groupId . '"'
                    . 'data-switch-class="toggle-hide"'
                    . '>' . ucfirst($groupName) . '</span>';
            }

            if ($options['checkboxlist'] ?? false) {
                $out .= $this->checkboxList($groupItems, [
                    'class' => 'selector-checkboxes',
                    'id' => 'selector-group-' . $groupId
                ]);
            } else {
                $out .= $this->Link->renderActionList($groupItems,[
                    'class' => 'selector-actionlist',
                    'id' => 'selector-group-' . $groupId
                ]);
            }
        }

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }


    /**
     * Create a list of checkboxes
     *
     * @param array $actions Array of items
     * @param array $options Array of options (possible keys: class)
     *
     * @return string
     */
    public function checkboxList(array $actions, array $options = []): string
    {
        $out = $this->Element->openHtmlElement(
            'ul',
            [
                'class' => $options['class'] ?? '',
                'id' => $options['id'] ?? null,
                'data' => $options['data'] ?? []
            ]
        );

        // Reset selection button
        if ($options['reset'] ?? false) {
            $out .= '<li class="selector-reset">' . __('Reset selection') . '</li>';
        }

        foreach ($actions as $action) {
            $action['options']['type'] = 'checkbox';
            $action['options']['label'] = strval($action['title']);
            $action['options']['checked'] = $action['checked'] ?? false;
            $action['options']['id'] = mb_strtolower(Text::slug($action['name'], ['replacement'=>'-','preserve'=>'_']));
            $contents = $this->Form->control($action['name'], $action['options']);

            $liDataAttrs = [];
            $liDataAttrs['value'] = $action['value'] ?? null;
            $liDataAttrs = array_merge($liDataAttrs, $options['item-data'] ?? []);

            $out .= $this->Element->openHtmlElement(
                'li',
                [
                    'data' => $liDataAttrs,
                ]
            );
            $out .= $contents;
            if ($options['grip-icon'] ?? false) {
                $out .= '<span class="grip-icon">⋮</span>';
            }
            $out .= $this->Element->closeHtmlElement('li');
        }

        $out .= $this->Element->closeHtmlElement('ul');

        return $out;
    }


    /**
     * Render a checkmark for published fields
     *
     * @param bool|null $published
     * @return string
     */
    public function checkBadge($published)
    {
        return $published ? '<span class="badge success">✓</span>' : '';
    }

    /**
     * Output orange problem boxes
     *
     * @return string
     */
    public function getProblems()
    {
        $out = '';

        $problems = $this->_View->getConfig('options')['problems'] ?? [];
        foreach ($problems as $problem) {
            $out .= '<div class="art-problems-value">' . $problem . '</div>';
        }

        if (!empty($out)) {
            $out = '<div class="art-problems">' . $out . '</div>';
        }

        return $out;
    }


    /**
     * Render one option rows in vertical table style, for example for transfer jobs
     *
     * @param array $options
     * @return string
     */
    public function getOptionRow($header, $options)
    {
        $out = '<tr>';
        $out .= '<th scope="row">' . $header . '</th>';
        $out .= '<td>';

        foreach ($options as $fieldName => $fieldConfig) {
            $fieldConfig = !is_array($fieldConfig) ? ['caption' => $fieldConfig, 'type' => 'checkbox'] : $fieldConfig;
            if ($fieldConfig['type'] === 'checkbox') {
                $out .= $this->Form->control($fieldName, [
                    'type' => 'checkbox',
                    'label' => $fieldConfig['caption'],
                    'checked' => $fieldConfig['checked'] ?? false
                ]);
            }
        }
        $out .= '</td>';
        $out .= '</tr>';
        return $out;

    }

    /**
     * Render option rows in vertical table style, for example for transfer jobs
     *
     * @param array $options
     * @return string
     */
    public function getOptionRows($options, $selected = [])
    {
        $optionGroups = [];
        foreach ($options as $fieldName => $fieldConfig) {
            $fieldConfig = !is_array($fieldConfig) ? ['caption' => $fieldConfig, 'type' => 'checkbox', 'checked' => false] : $fieldConfig;

            // Gather single Checkboxes
            if ($fieldConfig['type'] === 'checkbox') {
                $fieldConfig['checked'] = $selected[$fieldName] ?? false;
                $optionGroups[__('Options')][$fieldName] = $fieldConfig;
            }

            // Group multi checkboxes
            elseif ($fieldConfig['type'] === 'multi') {
                $optionGroups[$fieldConfig['caption']] = [];

                $prefix = $fieldConfig['prefix'] ?? '';
                $key = $fieldConfig['key'];

                foreach ($fieldConfig['options'] as $optionKey => $optionValue) {
                    // TODO: what if no key is present (at the moment used for fields nested in the params key.
                    $optionGroups[$fieldConfig['caption']][$fieldName . '.' . $prefix . $optionKey] = [
                        'type' => 'checkbox',
                        'caption' => $optionValue,
                        'checked' => (in_array($optionKey, $selected[$key][$fieldName] ?? []))
                    ];
                }
            }
        }

        $out = '';
        foreach ($optionGroups as $groupLabel => $groupOptions) {
            $out .= $this->getOptionRow($groupLabel, $groupOptions);
        }
        return $out;

    }
}

