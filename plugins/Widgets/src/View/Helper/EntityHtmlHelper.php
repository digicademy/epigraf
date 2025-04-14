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
use Cake\ORM\Entity;
use Epi\Model\Entity\Footnote;
use Epi\Model\Entity\Item;

/**
 * Entity helper for HTML view generation
 *
 * TODO: refactor BaseEntityHelper, move code here
 *
 */
class EntityHtmlHelper extends BaseEntityHelper
{

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [];


    /**
     * Render section content as stack
     *
     * @param array $options
     * @return string
     */
    public function sectionContentStacks($section, $options) {

        list($groupedItems, $itemTypes) = $section->getGroupedItems($options);

        if (empty($groupedItems)) {
            return '';
        }

        $database = $section->table->getDatabase();
        $article = $section->container;

        $mode = $options['mode'] ?? 'view';
        $template_section = $options['template_section'] ?? [];
        $template_article = $options['template_article'] ?? [];


        $groupClasses = ['doc-section-stack'];
        $out = '<div class="' . implode(' ', $groupClasses) . '">';

        foreach ($itemTypes as $itemConfig) {
            $itemConfig = is_array($itemConfig) ? $itemConfig : ['type' => $itemConfig];
            $itemType = $itemConfig['type'] ?? 'undefined';
            $items = $groupedItems[$itemType] ?? [];
            $itemTemplate = $database->types['items'][$itemType]['merged'] ?? [];

            // TODO: document 'force' key in the help. Because of the default value true in itemContent(),
            //       it hides empty fields for unconfigured types.
            // TODO: show headings between multiple items (for unconfigured types)

            $itemOptions = [
                'edit' => false,
                'mode' => $mode,
                'defaultFields' => [
                    'content' => [
                        'caption' => __('Content'),
                        'force' => false
                    ],
                    'property' => [
                        'caption' => __('Category'),
                        'force' => false
                    ]
                ],
                'templates' => [
                    'template_item' => $itemTemplate,
                    'template_section' => $template_section,
                    'template_article' => $template_article
                ]
            ];

            foreach ($items as $item) {
                $out .= $this->itemContent($item, $itemOptions);
            }
        }

        $out .= '</div>';

        $items = Arrays::ungroup($groupedItems);
        $out .= $this->annoLists($article, $items, ['edit' => false, 'mode' => $mode]);

        return $out;
    }

    /**
     * Render section content as table
     *
     * @param array $options
     * @return string
     */
    public function sectionContentTables($section, $options) {
        $database = $section->table->getDatabase();
        $article = $section->container;

        $mode = $options['mode'] ?? 'view';
        $template_section = $options['template_section'] ?? [];
        $template_article = $options['template_article'] ?? [];

        list($groupedItems, $itemTypes) = $section->getGroupedItems($options);

        if (empty($groupedItems)) {
            return '';
        }

        $tables = ($template_section['view']['grouped'] ?? false) ? [$itemTypes] : array_map(fn($x) => [$x], $itemTypes);
        $moreSection = $template_section['view']['more'] ?? false;
        $action = 'view';

        $out = '';

        foreach ($tables as $table) {
            $groupHeaders = [];
            $groupItemCount = 0;
            $moreItem = $moreSection;

            foreach ($table as $itemConfig) {
                $itemConfig = is_array($itemConfig) ? $itemConfig : ['type' => $itemConfig];
                $itemType = $itemConfig['type'] ?? 'undefined';

                $mergedConfig = $this->Types->getTypes()['items'][$itemType]['merged'] ?? [];
                $moreItem = $moreItem || (($mergedConfig['display'] ?? true) === 'more');

//                $itemCount = $itemConfig['count'] ?? '1';
                $itemsFields = $this->Types->getFields('items', $itemType, ['unnest' => true, 'edit' => false] + $options);

                $i = 0;
                foreach ($itemsFields as $fieldName => $fieldConfig) {
                    // More fields are only displayed by using the more button
                    $moreField = ($fieldConfig['display'] ?? true) === 'more';

                    $groupHeaders[$i]['fields'][$itemType] = [
                        'fieldname' => $fieldName,
                        'caption' => $fieldConfig['caption'] ?? '',
                        'more' => $moreField,
                        'display' => $fieldConfig['display'] ?? true,
                        'fieldconfig' => $fieldConfig
                    ];

                    $groupHeaders[$i]['display'] = (!isset($groupHeaders[$i]['display']) && ($fieldConfig['display'] ?? true)) ||
                        (isset($groupHeaders[$i]['display']) && ($groupHeaders[$i]['display'] ?? false) && ($fieldConfig['display'] ?? true));
                    $groupHeaders[$i]['more'] = ($groupHeaders[$i]['more'] ?? false) || $moreField;
                    $groupHeaders[$i]['captions'][] = $fieldConfig['caption'] ?? '';
                    $moreItem = $moreItem || $moreField;

                    $i++;
                }
                $groupItemCount += sizeof($groupedItems[$itemType] ?? []);
            }

            $groupClasses = ($template_section['view']['grouped'] ?? false) ? ['doc-section-groups doc-section-groups-isgrouped'] : ['doc-section-groups'];
            $groupClasses[] = (count($table) < 2) ? 'doc-section-groups-one' : 'doc-section-groups-multi';
            $groupClasses[] = (count($groupHeaders) < 2) ? 'doc-section-headers-one' : 'doc-section-headers-multi';
            $groupClasses[] = ($groupItemCount > 0) ? '' : 'doc-section-groups-empty';

            $out .= '<div class="' . implode(' ', array_filter($groupClasses)) . '">';

            if ($template_section['view']['caption'] ?? false) {
                foreach ($table as $itemConfig) {
                    $itemConfig = is_array($itemConfig) ? $itemConfig : ['type' => $itemConfig];
                    $itemType = $itemConfig['type'] ?? 'undefined';
                    $out .= '<div class="doc-group-caption">'
                        . $this->Types->getCaption('items', $itemType, ucfirst($itemType))
                        . '</div>';
                }
            }
            $out .= '<div class="doc-group-headers">';
            $out .= '<div class="doc-field doc-field-itemtype"></div>';

            foreach ($groupHeaders as $groupHeader) {
                if (! ($groupHeader['display'] ?? true)) {
                    continue;
                }

                $out .= $this->Element->outputHtmlElement(
                    'div',
                    implode(' / ', array_unique($groupHeader['captions'])),
                    [
                        'class' => [
                            'doc-group-header',
                            ($groupHeader['more'] ?? false) ? 'doc-group-header-more' : null
                        ]
                    ]
                );
            }

            $out .= '<div class="doc-group-header"></div>'; // For the more-button
            $out .= '</div>';

            foreach ($table as $itemConfig) {
                $itemConfig = is_array($itemConfig) ? $itemConfig : ['type' => $itemConfig];
                $itemType = $itemConfig['type'] ?? 'undefined';

                $items = $groupedItems[$itemType] ?? [];
                $template_item = $database->types['items'][$itemType]['merged'] ?? [];


                foreach ($items as $idx => $item) {
                    $itemClasses = [];
                    $itemClasses[] = ($idx === 0) ? 'doc-section-item-first' : '';
                    $itemClasses[] = $item->hasErrors() ? 'doc-section-item-error' : '';
                    $itemClasses = implode(' ', array_filter($itemClasses));

                    $out .= $this->itemStart($item, ['edit' => false, 'class' => $itemClasses]);

                    // First column contains the item type caption
                    $out .= $this->Element->outputHtmlElement(
                        'div',
                        $this->Types->getCaption('items', $itemType, ucfirst($itemType)),
                        ['class' => 'doc-field doc-field-itemtype']
                    );

                    foreach ($groupHeaders as $groupHeader) {
                        if (! ($groupHeader['display'] ?? true)) {
                            continue;
                        }

                        $fieldName = $groupHeader['fields'][$itemType]['fieldname'] ?? '';
                        $display = $groupHeader['fields'][$itemType]['fieldconfig']['display'] ?? true;
                        if (empty($fieldName) || empty($display)) {
                          $out .= '<div></div>';
                          continue;
                        }
                        $out .= $this->itemField(
                            $item,
                            $fieldName,
                            [
                                'edit' => $action === 'edit',
                                'mode' => $mode,
                                'caption' => false,
                                'template_item' => $template_item,
                                'template_section' => $template_section,
                                'template_article' => $template_article
                            ]
                        );
                    }

                    if ($moreItem) {
                        $out .= $this->itemMoreButton($section->id, $item->id);
                    }

                    $out .= $this->itemEnd();
                }

            }

            $out .= '</div>';
        }

        $items = Arrays::ungroup($groupedItems);
        $out .= $this->annoLists($article, $items, ['edit' => $action === 'edit', 'mode' => $mode]);

        return $out;
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
        $content = parent::itemFieldDate($item, $fieldNameParts, $edit, $options);
        return "<div class=\"doc-field-content\">{$content}</div>";
    }

    /**
     * Output a JSON field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit false
     * @param array $options
     * @return string
     */
    public function itemFieldJson($item, $fieldNameParts, $edit, $options=[])
    {
        if (($options['fieldConfig']['template'] ?? '') === 'list') {
            $value = $item->getValueFormatted($fieldNameParts);
            $value = Attributes::toList($value, true);
        }
        else {
            $value = $item->getValueFormatted($fieldNameParts);
            $value = $this->Table->nestedTable($value);
        }

        return "<div class=\"doc-field-content\">{$value}</div>";
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
        $content = $item->getValueFormatted($fieldNameParts);
        return "<div class=\"doc-field-content\">{$content}</div>";
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

        // Filter out unpublished footnotes for guests
//        if (!empty($data['problems']) && ($footnote->currentUserRole === 'guest')) {
//            return '';
//        }

        $formId = 'form-edit-'
            . ($data['root_tab'] ?? '{rootTab}')
            . '-'
            . ($data['root_id'] ?? '{rootId}');

        $edit = $data['edit'] ?? true;

        $out = $this->Element->openHtmlElement(
            'div',
            [
                'id' => 'doc-footnote-id-' . ($data['from_tagid'] ?? '{fromTagid}'),
                'class' => 'doc-footnote',
                'data-tagid' => ($data['from_tagid'] ?? '{fromTagid}'),
                'data-row-table' => 'footnotes',
                'data-row-id' => ($data['id'] ?? '{id}'),
                'data-row-type' => ($data['type'] ?? '{type}')
            ]
        );

        // Footnote number
        $numberClasses = ['doc-footnote-number'];
        if (!empty($data['problems'])) {
            $numberClasses[] = 'tag-problem';
        }
        $out .= $this->Element->outputHtmlElement(
            'div',
            $data['name'] ?? '{name}',
            ['class' => $numberClasses]
        );

        // Footnote content fields
        $out .= $this->Element->openHtmlElement('div', ['class' => 'doc-footnote-content']);

        foreach ($typeConfig['merged']['fields'] ?? [] as $fieldName => $fieldConfig) {
            if ($fieldName === 'name') {
                continue;
            }
            $caption = $fieldConfig['caption'] ?? null;
            if (!is_null($caption)) {
                $out .= "<span class=\"doc-field-caption\">{$caption}</span>";
            }

            $fieldOptions =  ['edit' => $edit, 'form'=> $formId, 'fieldConfig' => $fieldConfig];
            $out .= $this->footnoteField($data, $fieldName, $fieldOptions);
        }

        $out .= $this->Element->closeHtmlElement('div');
        $out .= $this->Element->closeHtmlElement('div');

        return $out;
    }
}
