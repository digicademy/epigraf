<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Widgets\View\Helper;

use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use App\Utilities\Converters\Objects;
use Cake\ORM\Entity;
use Cake\Routing\Router;
use Epi\Model\Entity\Footnote;
use Epi\Model\Entity\Item;
use Epi\Model\Entity\RootEntity;
use Epi\Model\Entity\Section;

/**
 * Entity helper for Markdown
 *
 */
class EntityMarkdownHelper extends BaseEntityHelper
{
    const OUTPUT_HEADER_END = "\n\n";
    const OUTPUT_ITEM_END = "\n";
    const OUTPUT_FIELD_END = "\n";

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Render an entity
     *
     * @param RootEntity $entity
     * @param array $options
     * @return string
     */
    public function render($entity, $options=[])
    {
        $entity->prepareRoot();
        $options = [
            'edit'=> $this->Link->getEdit(),
            'mode'=> $this->Link->getMode(),
            'templateArticle'=>  $entity->type['merged'] ?? []
        ];

//        $options = ['edit' => false, 'mode' => $mode, 'template_article' => $templateArticle];
        $options['format'] = 'md';

        $out = "";
        $out .= $this->docHeader($entity, $options);
        $out .= $this->docContent($entity, $options);

        $options['levelOffset'] = 1;
        $out .= $this->sectionList($entity, $options);
        $out .= $this->footnoteList($entity, $options);

        return $out;
    }

    /**
     * Output the article header
     *
     * @param RootEntity $entity
     * @param array $options
     * @return string
     */
    public function docHeader($entity, $options=[])
    {

        // TODO: Render breadcrumbs instead of article specific header here
        $out = "# ";
        $out .= Attributes::concatText(
            $entity->project->name ?? '',
         $entity->name ?? '',
                ' » '
        );

        $headers = $entity->type->header ?? [];
        $headline = [];
        foreach ($headers as $no => $header) {

            $header['aggregate'] = false;
            $items = $entity->getValueNested($header['key'], $header);
            $items = is_array($items) ? implode(". ", $items) : $items;

            $headline[] = Attributes::concatText(
                $header['caption'] ?? '',
                $items,
                ': '
            );
        }

        $headline = array_filter($headline);

        if (!empty($headline)) {
            $out .= "\n\n*";
            $out .= implode(' - ', $headline);
            $out .= '*';
        }

        $out .= "\n\n";
        return $out;
    }

    /**
     *  Output article metadata
     *
     * @param Article $entity
     * @param array $options
     *
     * @return string
     */
    public function docContent($entity, $options = [])
    {
        $out = '';

        $url = $entity->iriUrl;
        if (!empty($url)) {
            $out .= "[" . $entity->signature . "](" . $url . ")\n\n";
        }

        return $out;
    }


    /**
     * Output sections
     *
     * ### Options
     * - article
     * - edit
     * - mode
     * - template_article
     * - ignore Records to ignore. An array with tables as keys and types as values.
     *   Example: ['sections'=>['summary']]
     *
     * @param RootEntity $entity An article entity
     * @param array $options
     * @return string
     */
    public function sectionList($entity, $options = [])
    {
        $out = '';
        $ignore = $options['ignore']['sections'] ?? [];
        foreach ($entity->sections ?? [] as $section) {
            if (in_array($section->sectiontype, $ignore)) {
                continue;
            }
            if (!$section->empty) {
                $out .= $this->sectionContent($section, $options);
            }
        }

        return $out;
    }

    /**
     * Output a single section
     *
     * @param $section
     * @param array $options Array with the keys 'edit', 'mode', 'article', 'template_article'
     *
     * @return string
     */
    public function sectionContent($section, $options = [])
    {
        if (empty($section)) {
            return '';
        }

        // For guest users, filter out nonpublic sections
        if (!$section->getEntityIsVisible($options)) {
            return '';
        }

        // Update options
        $options['article'] = $section->container;
        $options['section'] = $section;

        // Get template
        $options['mode'] = $options['mode'] ?? '';

        $sectionConfig = $section->type['merged'] ?? [];
        $sectionConfig['view'] = Arrays::stringToArray($sectionConfig['view'] ?? 'stack', 'name', 'stack');
        $options['template_section'] = $sectionConfig;

        if (($options['template_section']['display'] ?? '') === 'empty') {
            return "";
        }

//        TODO: Widgets (map, image)
//        $widgets = trim($this->sectionWidgets($options));
//        $options['haswidget'] = !empty($widgets);
//        $out .= $widgets;

        // Section content
        if ($sectionConfig['view']['name'] === 'stack') {
            $out = $this->sectionContentStacks($section, $options);
        }
        else {
            $out = $this->sectionContentTables($section, $options);
        }

        //Section start
        if ($out !== '') {
            $out = $this->sectionStart($section, $options) . $out;
        }


        return $out;
    }

    /**
     * Output section content as tables
     *
     * @param Section $section
     * @param array $options
     * @return string
     */
    public function sectionContentTables($section, $options)
    {
        $out = "";
        list($groupedItems, $itemTypes) = $section->getGroupedItems($options);

        // TODO: support grouped items
//        $options['template_section']['view']['grouped'] = false;

        $tables = ($options['template_section']['view']['grouped'] ?? false) ? [$itemTypes] : array_map(fn($x) => [$x], $itemTypes);
        foreach ($tables as $tableKey => $groups) {
            $out .= $this->sectionContentTablesTable($groups, $groupedItems, $options);
        }
        return $out;
    }

    /**
     * @param $groups
     * @return string
     */
    public function sectionContentTablesTable($groups, $groupedItems, $options)
    {
        $template_section = $options['template_section'] ?? [];
        $template_article = $options['template_article'] ?? [];

        $itemTypeHeaders = count($groups) > 1;

        // Merge and output table headers
        $groupHeaders = [];
        $groupItemCount = 0;
        foreach ($groups as $itemConfig) {
            $itemConfig = is_array($itemConfig) ? $itemConfig : ['type' => $itemConfig];
            $itemType = $itemConfig['type'] ?? 'undefined';
            $itemCount = $itemConfig['count'] ?? '1';

            $itemHeaders = array_values($this->Types->getFields('items', $itemType, ['unnest' => true, 'edit' => false]));
            foreach ($itemHeaders as $i => $itemHeader) {
                $groupHeaders[$i][] = $itemHeader['caption'] ?? '';
            }
            $groupItemCount += sizeof($groupedItems[$itemType] ?? []);
        }
        $groupHeaders = array_map(
            fn($itemHeaders) => implode(' / ', array_unique($itemHeaders)),
            $groupHeaders
        );

        $rows = []; // Values
        $cols = []; // Column sizes
        $colOffset = $itemTypeHeaders ? 1 : 0;

        if (!empty($groupItemCount)) {
            $row = [];
            if (!empty($groupHeaders)) {
                if ($itemTypeHeaders) {
                    $groupHeader = __('Category');
                    $row[]  = $groupHeader;
                    $cols[0] = mb_strlen($groupHeader);
                }
                foreach ($groupHeaders as $colNo => $groupHeader) {
                    $row[] = $groupHeader;
                    $cols[$colNo + $colOffset] = max($cols[$colNo + $colOffset] ?? 0, mb_strlen($groupHeader));
                }
            }
            $rows[] = $row;

            // Output items
            foreach ($groups as $itemConfig) {
                $itemConfig = is_array($itemConfig) ? $itemConfig : ['type' => $itemConfig];
                $itemType = $itemConfig['type'] ?? 'undefined';

                $items = $groupedItems[$itemType] ?? [];
                $itemFields = $this->Types->getFields('items', $itemType, ['unnest' => true, 'edit' => false]);
                $template_item = $database->types['items'][$itemType]['merged'] ?? [];

                if (!empty($items)) {

                    foreach ($items as $idx => $item) {
                        $row = [];

                        if ($itemTypeHeaders) {
                            $value = $this->Types->getCaption('items', $itemType, ucfirst($itemType));
                            $row[] = $value;
                            $cols[0] = max($cols[0] ?? 0, mb_strlen($value));
                        }

                        $colNo = 0;
                        foreach ($itemFields as $fieldName => $fieldConfig) {
                            if (empty($fieldName)) {
                                $value = '';
                            } else {
                                $value = $this->itemField(
                                    $item,
                                    $fieldName,
                                    [
                                        'edit' => false,
                                        'mode' => $options['mode'],
                                        'format' => $options['format'] ?? 'html',
                                        'caption' => false,
                                        'template_item' => $template_item,
                                        'template_section' => $template_section,
                                        'template_article' => $template_article
                                    ]
                                );
                            }
                            $row[] = $value;
                            $cols[$colNo + $colOffset] = max($cols[$colNo + $colOffset] ?? 0, mb_strlen($value));
                            $colNo ++;
                        }

                        $rows[] = $row;
                    }
                }
            }
        }

        // Format table
        $out = "";
        $maxWidth = 30;
        if (!empty($rows)) {
            foreach ($rows as $rowNo => $row) {
//                $overflow = 0;

                if (($rowNo === 0) && !empty($groupHeaders)) {
                    $headerLine = "|";
                    $headerSep = "|";
                    foreach ($row as $colNo => $cell) {
                        $headerLine .= " " . mb_str_pad($cell, min($maxWidth, $cols[$colNo]), ' ', STR_PAD_RIGHT) . " |";
                        $headerSep .= "-" . mb_str_pad('', min($maxWidth, $cols[$colNo]), '-', STR_PAD_RIGHT) . "-|";
                    }
                    $out .= $headerLine . "\n" . $headerSep;
                } else {
                    $out .= "| ";
                    foreach ($row as $colNo => $cell) {
                        $width = min($maxWidth, $cols[$colNo]);
//                        $minWidth = mb_strlen($cell);
//                        if (($overflow > 0) && ($minWidth < $width)) {
//                            $overflow = $overflow - max(0,$width - $minWidth);
//                            $width = $minWidth;
//                        } else {
//                            $overflow = $overflow + max(0, $minWidth - $maxWidth);
//                        }

                        $out .= mb_str_pad($cell, $width, ' ', STR_PAD_RIGHT) . " | ";
                    }
                }
                $out .= "\n";
            }

            $out .= "\n";
        }

        return $out;
    }

    /**
     * Output section content as field stack
     *
     * @param Section $section
     * @param array $options
     * @return string
     */
    public function sectionContentStacks($section, $options)
    {
        $out = "";
        list($groupedItems, $itemTypes) = $section->getGroupedItems($options);
        if (!empty($groupedItems)) {
            foreach ($itemTypes as $itemConfig) {
                $itemConfig = is_array($itemConfig) ? $itemConfig : ['type' => $itemConfig];
                $itemType = $itemConfig['type'] ?? 'undefined';
                $items = $groupedItems[$itemType] ?? [];
                $out .= $this->sectionContentStacksStack($items, $options);
            }
        }

        //$items = Arrays::ungroup($groupedItems);
        //$options['wrap'] = false;
        //$out .= $this->annoLists($section->root, $items, $options);

        return $out;
    }

    /**
     * Return a single stack
     *
     * @param $items
     * @param $options
     * @return string
     */
    public function sectionContentStacksStack($items, $options) {
        // $itemTemplate = $database->types['items'][$itemType]['merged'] ?? [];
        $itemTemplate = count($items) > 0 ? ($items[0]->type['merged'] ?? []) : [];

        $defaultFields = [
            'content'=> [
                'caption' =>__('Content'),
                'force' => false
            ],
            'property'=> [
                'caption' => __('Category'),
                'force' => false
            ]
        ];

        $template_section = $options['template_section'] ?? [];
        $template_article = $options['template_article'] ?? [];
        $itemOptions = [
            'edit' => false,
            'mode' => $options['mode'],
            'defaultFields' => $defaultFields,
            'templates' => ['template_item'=>$itemTemplate, 'template_section' => $template_section, 'template_article' => $template_article]
        ];

        $itemOptions = array_merge($itemOptions, $options);

        $out = "";
        foreach ($items as $item) {
            $content = $this->itemContent($item, $itemOptions);
            if ($content !== '') {
                $out .= $content . self::OUTPUT_ITEM_END;
            }
        }

        return $out;
    }


    /**
     * Section header
     *
     * @param Section $section
     * @param array $options Array with the keys 'edit', 'mode', 'article', 'section', 'template_article', 'template_section.view'
     *
     * @return string
     */
    public function sectionStart($section, $options = [])
    {
        $out = '';
        $level = $section['level'] ?? 5;
        $levelOffset = $options['levelOffset'] ?? 0;
        $levelMark = str_repeat("#", $level + $levelOffset);
        $out .= $levelMark . ' ' . $this->sectionName($section, $options) . "  \n";
        return $out;
    }
    /**
     * Section name
     *
     * @param Section $section
     * @param array $options
     * @return string
     */
    public function sectionName($section, $options)
    {
        $out = '';

        $sectionId = ($section['id'] ?? '{sections-id}');
        $editSection = ($options['edit'] ?? false) && ($options['template_section']['edit'] ?? $options['template_article']['edit'] ?? true);
        $fieldConfig = $section->type->config['fields'] ?? [];
        $prefix = $section->type['merged']['name']['prefix'] ?? '';



        $showpath = $section->type['merged']['name']['path'] ?? false;
        if ($showpath && !empty($section['path'])) {
            $path = $this->sectionPath($section['path'], 'name');
        }
        else {
            $path = $section['name'] ?? $section->type->caption ?? $section->sectiontype ?? __('Section');
        }
        $out .= $prefix . $path;

        return $out;
    }


    /**
     * Extract and concatenate values from path
     *
     * @param array $path
     * @param string $value
     *
     * @return string
     */
    public function sectionPath($path = [], $value = 'name')
    {
        $items = Objects::extract((array)$path, '*.' . $value);
        return implode(".", $items);
    }


    /**
     * Output section widgets
     *
     * ### Options
     * - edit
     * - mode
     * - article
     * - section
     * - template_article
     * - template_section.view
     *
     * @param array $options
     * @return string
     */
    public function sectionWidgets($options)
    {
        $out = '';
        if (!empty($options['template_section']['view']['widgets']['map'] ?? false)) {
            $out .= $this->getView()->element('../Sections/widget_map', $options);
        }
        if (!empty($options['template_section']['view']['widgets']['thumbs'] ?? false)) {
            $out .= $this->getView()->element('../Sections/widget_thumbs', $options);
        }
        if (!empty($options['template_section']['view']['widgets']['grid'] ?? false)) {
            $out .= $this->getView()->element('../Sections/widget_grid', $options);
        }
        return $out;
    }

    /**
     * Render an item inside a grid
     *
     * Options:
     * - fields Array of fields that will be rendered
     * - edit true|false
     * - delete true|false
     * - preview true|false (whether to set the data-preview attribute which indicates
     *                       the item is a duplicate, not for saving)
     * - templates Array of templates for article, section and item (see sec_grid.php)
     *
     * @param Item|string $item Either an item object or the itemtype as string, which will render a template
     * @param array $options
     * @return string
     */
    public function itemTile($item, $options = []): string
    {
        $out = $this->itemStart($item, $options);

        $itemType = is_string($item) ? $item : $item['itemtype'];
        $itemFields = $this->Types->getFields('items', $itemType, $options);
        $itemFields = array_intersect_key($itemFields, array_flip($options['fields'] ?? []));

        foreach ($itemFields as $fieldName => $fieldConfig) {
                $out .= $this->itemField(
                    $item,
                    $fieldName,
                    array_merge(
                        [
                            'caption' => false,
                            'edit' => $options['edit'] ?? false,
                        ],
                        $options['templates'] ?? []
                    )
                );
        }

        $out .= $this->itemEnd();
        return $out;
    }

    /**
     * Render a stacked item
     *
     * Options:
     * - fields Array of fields that will be rendered
     * - edit true|false
     * - delete true|false
     * - preview true|false (whether to set the data-preview attribute which indicates
     *                       the item is a duplicate, not for saving)
     * - templates Array of templates for article, section and item (see sec_grid.php)
     *
     * @param Item|string $item Either an item object or the itemtype as string, which will render a template
     * @param array $options
     * @return string
     */
    public function itemContent($item, $options = []): string
    {
        $itemType = is_string($item) ? $item : $item['itemtype'];
        $itemFields = $this->Types->getFields('items', $itemType, ['edit' => false] + $options);
        unset($options['defaultFields']);

        $out = "";
        foreach ($itemFields as $fieldName => $fieldConfig) {
            if ((($fieldConfig['force'] ?? false) || !empty($item[$fieldName]))) {
                $content = $this->itemField(
                    $item,
                    $fieldName,
                    array_merge(
                        [
                            'edit' => $options['edit'] ?? false,
                            'mode' => $options['mode'] ?? 'view',
                            'format' => $options['format'] ?? 'html', // new: 2024-08-06
                            'caption'=> $fieldConfig['showcaption'] ?? true ? ($fieldConfig['caption'] ?? false) : false,
                        ],
                        $options['templates'] ?? []
                    )
                );

                if ($content !== '') {
                    $out .= $content . self::OUTPUT_FIELD_END;
                }
            }
        }

        return $out;
    }

    /**
     * Output the formatted content of an item field
     *
     * @param string|Item $item
     * @param string $fieldName
     * @param array $options
     *
     * @return string
     */
    public function itemField($item, $fieldName, $options = []): string
    {
        $edit = false;

        // Get IDs
        $item_id = $item->id ?? '{id}';
        $table = $item->table_name;
        $section_id = $item->sections_id ?? '{sections-id}';
        $fieldNameParts = explode('.', $fieldName);

        // Get config
        $itemFormat = $item->getFieldFormat($fieldNameParts);
        $empty = $item->getValueIsEmpty($fieldNameParts);
        $fieldConfig = $item->getFieldConfig($fieldNameParts);

        // Properties
        if ($item instanceof RootEntity) {
            $inputPath = $fieldNameParts[0] ?? '';
            $inputField = Attributes::fieldName($fieldNameParts, true);
        }

        // Items and sections
        else {
            // TODO: what about containers that are not sections?
            $inputPath = empty($item['sections_id']) ? $table : 'sections[' . $section_id . '][' . $table . ']';
            $inputPath .= '[' . $item_id . ']';
            $inputField = $inputPath . Attributes::fieldName($fieldNameParts, false);
        }

        // Merge options
        $options = array_merge($options, compact(
            'item_id','section_id', 'table',
             'inputPath', 'inputField',
             'itemFormat', 'empty', 'fieldConfig'
        ));

        // JSON
        if ($itemFormat === 'json') {
            $content = $this->itemFieldJson($item, $fieldNameParts, $edit, $options);
        }

        // XML
        elseif ($itemFormat === 'xml') {
            $content = $this->itemFieldXml($item, $fieldNameParts, $edit, $options);
        }

        // Property
        elseif ($itemFormat === 'property') {
            $content = $this->itemFieldProperty($item, $fieldNameParts, $edit, $options);
        }

        // Property: Auto fill from the caption selector
        elseif ($itemFormat === 'sectionname') {
            $content = $this->itemFieldSectionname($item, $fieldNameParts, $edit, $options);
        }

        // Property with unit
        elseif ($itemFormat === 'unit') {
            $content = $this->itemFieldUnit($item, $fieldNameParts, $edit, $options);
        }

        // Linked record
        elseif ($itemFormat === 'record') {
            $content = $this->itemFieldRecord($item, $fieldNameParts, $edit, $options);
        }

        // Linked record
        elseif ($itemFormat === 'relation') {
            $content = $this->itemFieldRecord($item, $fieldNameParts, $edit, $options);
        }

        // Date
        elseif ($itemFormat === 'date') {
            $content = $this->itemFieldDate($item, $fieldNameParts, $edit, $options);
        }

        // Checkbox
        elseif ($itemFormat === 'check') {
            $content = $this->itemFieldCheck($item, $fieldNameParts, $edit, $options);
        }

        // Select
        elseif ($itemFormat === 'select') {
            $content = $this->itemFieldSelect($item, $fieldNameParts, $edit, $options);
        }

        // Published
        elseif ($itemFormat === 'published') {
            $content = $this->itemFieldPublished($item, $fieldNameParts, $edit, $options);
        }

        // Position in grid
        elseif ($itemFormat === 'position') {
            $content = $this->itemFieldPosition($item, $fieldNameParts, $edit, $options);
        }

        // File
        elseif (($itemFormat === 'file') || ($itemFormat === 'image')) {
            $content = $this->itemFieldFile($item, $fieldNameParts, $edit, $options);
        }

        // Image URL
        elseif ($itemFormat === 'imageurl') {
            $content = $this->itemFieldImageurl($item, $fieldNameParts, $edit, $options);
        }

        // Hyperlink
        elseif ($itemFormat === 'link') {
            $content = $this->itemFieldLink($item, $fieldNameParts, $edit, $options);
        }

        // Number
        elseif ($itemFormat === 'number') {
            $content = $this->itemFieldNumber($item, $fieldNameParts, $edit, $options);
        }

        // Raw values
        else {
            $content = $this->itemFieldRaw($item, $fieldNameParts, $edit, $options);
        }

        if ($content === '') {
            return '';
        }


        // Caption
        $caption = $options['caption'] ?? null;
        $showcaption = ($caption !== false) && (($caption !== null) && !$empty) &&
            ($itemFormat !== 'check') && ($itemFormat !== 'position');

        if ($showcaption) {
            $out = "*" . $caption . "*: " . $content;
        } else {
            $out = strval($content);
        }
        return $out;
    }

    /**
     * Output a raw text field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldRaw($item, $fieldNameParts, $edit, $options = [])
    {
        $content = $item->getValueFormatted($fieldNameParts);

        if (is_array($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
        return "{$content}";
    }

    /**
     * Output a number field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldNumber($item, $fieldNameParts, $edit, $options=[])
    {
        $content = $item->getValueFormatted($fieldNameParts);

        if (is_array($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
        return "{$content}";
    }

    /**
     * Output a JSON field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldJson($item, $fieldNameParts, $edit, $options=[])
    {
        if (($options['fieldConfig']['template'] ?? '') === 'list') {
            $value = $item->getValueFormatted($fieldNameParts);
            $value = Attributes::toList($value);
        }
        else {
            $value = $item->getValueFormatted($fieldNameParts);
            $value = $this->Table->nestedTable($value);
        }

        return "<div class=\"doc-field-content\">{$value}</div>";
    }

    /**
     * Output a Xml field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldXml($item, $fieldNameParts, $edit, $options = [])
    {
        if (is_array($item)) {
            $content = $item[$fieldNameParts[0]] ?? ('{' . $fieldNameParts[0] . '}');
        }
        else {
            $content = $item->getValueFormatted($fieldNameParts, $options);
        }

        $out = $content;
        return $out;
    }

    /**
     * Output a unit field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldUnit($item, $fieldNameParts, $edit, $options=[])
    {
        $content = $item->getValueFormatted($fieldNameParts);
        return $content;
    }

    /**
     * Output a record field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldRecord($item, $fieldNameParts, $edit, $options=[])
    {
        // TODO: output name and create chooser
        // TODO: rename 'links' to 'to' in the database and everywhere
        // TODO: output class field-problem for missing targets as in itemFieldProperty()
        $content = $item->getValueFormatted($fieldNameParts);
        return $content;
    }

    /**
     * Output a date field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldDate($item, $fieldNameParts, $edit, $options=[])
    {
        return parent::itemFieldDate($item, $fieldNameParts, $edit, $options);
    }

    /**
     * Output a Xml field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldCheck($item, $fieldNameParts, $edit, $options=[])
    {
        $fieldName = implode('.', $fieldNameParts);
        $content = $item->getValueFormatted($fieldNameParts);
        $content = empty($content) ? '' : $item->getFieldCaption($fieldName);
        return $content;
    }

    /**
     * Output a select field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldSelect($item, $fieldNameParts, $edit, $options=[])
    {
        $content = $item->getValueFormatted($fieldNameParts);
        return $content;
    }

    /**
     * Output a field to select publishing options
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldPublished($item, $fieldNameParts, $edit, $options=[])
    {
        if (empty($options['fieldConfig']['options'])) {
            $options['fieldConfig']['options'] = $item->publishedOptions;
        }
        return $this->itemFieldSelect($item, $fieldNameParts, $edit, $options);
    }

    /**
     * Output a position field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldPosition($item, $fieldNameParts, $edit, $options=[])
    {

        $content = implode('/', [$item['pos_x'], $item['pos_y'], $item['pos_z']]);

        return $content;
    }

    /**
     * Output a file or image field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldFile($item, $fieldNameParts, $edit, $options=[])
    {
        $out = '';
        $fieldName = implode('.', $fieldNameParts);
        $inputPath = $options['inputPath'];
        $itemFormat = $options['itemFormat'];

        $valueFilename = $item[$fieldName . '_name'] ?? '';
        $valueFilepath = $item[$fieldName . '_path'] ?? '';
        $valueFullname = empty($valueFilepath) ? $valueFilename : ($valueFilepath . '/' . $valueFilename);

        // Assemble path from base folder and file_path
        $fullpath = trim($item->file_basepath . $valueFilepath, '/');
        $selectPath = empty($valueFilename) ? trim($item->file_basepath . $item->file_defaultpath, '/') : $fullpath;

        // Output the image
        if ($itemFormat === 'image') {
            if (!empty($item->file_name)) {
                $url = Router::url(['action' => 'view', $item->articles_id, '#' => 'items-' . $item->id]);
                $image = $this->Files->outputImage($item, true, $url);
            } else {
                $image = '';
            }
            $out .= "{$image}";
        }

        // View path
        $out .= "{$valueFullname}";

        return $out;
    }

    /**
     * Output a URL field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldImageurl($item, $fieldNameParts, $edit, $options=[])
    {
        $fieldName = implode('.', $fieldNameParts);

        $value = $item[$fieldName . '_name'] ?? '';
        $path = $item[$fieldName . '_path'] ?? '';

        $url = ($options['fieldConfig']['baseurl'] ?? '') . trim($path, '/') . '/' . $value;
        return $url;
    }

    /**
     * Output a link field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldLink($item, $fieldNameParts, $edit, $options=[])
    {
        $content = $item->getValueFormatted($fieldNameParts);

        // TODO: test
        $content = "[" . $content . "]" . "(" . $content . ")";
        return $content;
    }


    /**
     * Output a property field
     *
     * Handles the formats "property" and "sectionname"
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldProperty($item, $fieldNameParts, $edit, $options=[])
    {
        $content = $item->getValueFormatted($fieldNameParts);
        $value = $item['properties_id'];
        $error = !empty($value) && is_array($fieldNameParts) && empty($item[$fieldNameParts[0]]);

        if ($error) {
            return "Error in property " . $value;
        }

        return $content;

    }

    /**
     * Output a property field that holds the section name options (only relevant for editing)
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldSectionname($item, $fieldNameParts, $edit, $options=[])
    {
        return '';
    }

    /**
     * Output the footnote sections
     *
     * @param RootEntity $article
     * @param array $options
     * @return string
     */
    public function footnoteList(RootEntity $article, $options = [])
    {
        $out = '';
        foreach ($article->footnoteTypes as $typeName => $typeConfig) {
            $out .= $this->footnoteSection($article, $typeName, $typeConfig, $options);
        }
        return $out;
    }

    /**
     * Output the footnotes belonging to one footnote type
     *
     * @param RootEntity $article
     * @param string $typeName
     * @param array $typeConfig
     * @param array $options
     * @return string
     */
    public function footnoteSection(RootEntity $article, $typeName, $typeConfig, $options): string
    {
        $out = '';
        $edit = $options['edit'] ?? false;

        $items = $article->getOrderedFootnotes($typeName);

        if (!empty($items)) {

            $out .= "## " . $this->Types->getCaption('footnotes', $typeName, __('Footnotes'));
            $out .= "\n\n";

            foreach ($items as $key => $item) {
                $out .= $this->footnoteContent($item, $article, $options, $typeConfig);
                $out .= "\n";
            }

            //$out .= $this->annoLists($article, $items, ['edit' => $editFootnotes, 'mode' => $mode, 'lists' => ['links']]);
        }
        return $out;
    }

    /**
     * Output a footnote
     *
     * @param false|Footnote $footnote The footnote entity or empty
     * @param Entity $root The root entity
     * @param array $options Keys: edit (true|false)
     * @param array $typeConfig The footnote's type configuration
     * @return string
     */
    public function footnoteContent($footnote, $root, $options=[], $typeConfig = [])
    {
        $data = parent::footnoteContent($footnote, $root, $options, $typeConfig);

        // Footnote number
        $out = '[' . ($data['name'] ?? '{name}') . '] ';

        foreach ($typeConfig['merged']['fields'] ?? [] as $fieldName => $fieldConfig) {
            if ($fieldName === 'name') {
                continue;
            }
            $caption = $fieldConfig['caption'] ?? null;
            if (!is_null($caption)) {
                $out .= "**{$caption}**: ";
            }

            $fieldOptions = $options;
            $fieldOptions['fieldConfig'] = $fieldConfig;
            $out .= $this->footnoteField($data, $fieldName, $fieldOptions);
            $out .= "\n";
        }

        return $out;
    }

    /**
     * Output a footnote field
     *
     * @param array|Footnote $footnote The footnote entity or a data array
     * @param string $fieldname
     * @param array $options Keys: edit (true|false) and form (the form ID)
     * @return string
     */
    public function footnoteField($footnote, $fieldname, $options)
    {
        $fieldNameParts = explode('.', $fieldname);
        $out = $this->itemFieldXml($footnote, $fieldNameParts, false, $options);
        return $out;
    }

}

