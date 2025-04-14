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
use App\Utilities\Files\Files;
use Cake\ORM\Entity;
use Cake\Utility\Inflector;
use Cake\View\Helper\FormHelper;
use Epi\Model\Entity\Article;
use Epi\Model\Entity\Footnote;
use Epi\Model\Entity\Item;
use Epi\Model\Entity\Section;

/**
 * Entity helper for HTML view generation
 *
 * @property FormHelper $Form
 */
class EntityInputHelper extends BaseEntityHelper
{

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Helpers
     *
     * @var string[]
     */
    public $helpers = ['EntityHtml', 'Html', 'Url', 'Types', 'Element', 'Form', 'Table', 'Link', 'Files'];

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
                'edit' => true,
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

            // May items be edited, added or deleted?
            $itemCount = $itemConfig['count'] ?? '1';
            $itemEdit = $itemTemplate['edit'] ?? $template_section['edit'] ?? $template_article['edit'] ?? true;
            $itemCreate = $itemEdit && ($itemCount === '1') && (count($items) === 0);
            $itemAdd = $itemDelete = $itemEdit && ($itemCount === '*');

            if ($itemCreate) {
                $item = new Item([
                    'itemtype' => $itemType,
                    'id' => Attributes::uuid('new'),
                    'sections_id' => $section->id,
                    'articles_id' => $section->articles_id,
                    'pos_x' => '1',
                    'pos_y' => '1',
                    'pos_z' => '1'
                ], [
                    'source' => 'Epi.Items',
                    'useSetters' => false,
                    'markClean' => true,
                    'markNew' => true
                ]);
                $item->setSource('Epi.Items');
                $item->container = $section;
                $item->root = $section->root;

                $out .= $this->itemContent($item, $itemOptions);
            }
        }

        $out .= '</div>';

        $items = Arrays::ungroup($groupedItems);
        $out .= $this->annoLists($article, $items, ['edit' => true, 'mode' => $mode]);

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

        $tables = ($template_section['view']['grouped'] ?? false) ? [$itemTypes] : array_map(fn($x) => [$x], $itemTypes);
        $moreSection = $template_section['view']['more'] ?? false;
        $fileUpload = $template_section['view']['widgets']['upload'] ?? false;

        $out = '';

        foreach ($tables as $table) {
            $groupHeaders = [];
            $groupItemCount = 0;
            $moreItem = $moreSection;

            // Combine table headers
            foreach ($table as $itemConfig) {
                $itemConfig = is_array($itemConfig) ? $itemConfig : ['type' => $itemConfig];
                $itemType = $itemConfig['type'] ?? 'undefined';

                $mergedConfig = $this->Types->getTypes()['items'][$itemType]['merged'] ?? [];
                $moreItem = $moreItem || (($mergedConfig['display'] ?? true) === 'more');

                $itemsFields = $this->Types->getFields('items', $itemType, ['unnest' => true, 'edit' => true] + $options);

                $i = 0;
                foreach ($itemsFields as $fieldName => $fieldConfig) {
                    // More fields are only displayed by using the more button
                    $moreField = ($fieldConfig['display'] ?? true) === 'more';

                    $groupHeaders[$i]['fields'][$itemType] = [
                        'fieldname' => $fieldName,
                        'caption' => $fieldConfig['caption'] ?? '',
                        'more' => $moreField,
                        'fieldconfig' => $fieldConfig
                    ];

                    $groupHeaders[$i]['more'] = ($groupHeaders[$i]['more'] ?? false) || $moreField;
                    $groupHeaders[$i]['captions'][] = $fieldConfig['caption'] ?? '';
                    $moreItem = $moreItem || $moreField;

                    $i++;
                }
                $groupItemCount += sizeof($groupedItems[$itemType] ?? []);
            }

            // Output table headers
            $groupClasses = ($template_section['view']['grouped'] ?? false) ? ['doc-section-groups doc-section-groups-isgrouped'] : ['doc-section-groups'];
            $groupClasses[] = (count($table) < 2) ? 'doc-section-groups-one' : 'doc-section-groups-multi';
            $groupClasses[] = (count($groupHeaders) < 2) ? 'doc-section-headers-one' : 'doc-section-headers-multi';
            $groupClasses[] = ($groupItemCount > 0) ? '' : 'doc-section-groups-empty';

            $out .= '<div class="' . implode(' ', array_filter($groupClasses)) . '">';
            $out .= '<div class="doc-group-headers">';
            $out .= '<div class="doc-field doc-field-itemtype"></div>';

            foreach ($groupHeaders as $groupHeader) {
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
            $out .= '<div class="doc-group-header"></div>'; // For the delete-button

            $out .= '</div>';

            // Table rows
            foreach ($table as $itemConfig) {
                $itemConfig = is_array($itemConfig) ? $itemConfig : ['type' => $itemConfig];
                $itemType = $itemConfig['type'] ?? 'undefined';

                $items = $groupedItems[$itemType] ?? [];
                $template_item = $database->types['items'][$itemType]['merged'] ?? [];

                $itemCount = $itemConfig['count'] ?? '1';
                $itemEdit = $template_item['edit'] ?? $template_section['edit'] ?? $template_article['edit'] ?? true;
                $itemCreate = $itemEdit && ($itemCount === '1') && (count($items) === 0);
                $itemAdd = $itemDelete = $itemEdit && ($itemCount === '*');

                foreach ($items as $idx => $item) {
                    $itemClasses = [];
                    $itemClasses[] = ($idx === 0) ? 'doc-section-item-first' : '';
                    $itemClasses[] = $item->hasErrors() ? 'doc-section-item-error' : '';
                    $itemClasses = implode(' ', array_filter($itemClasses));

                    $out .= $this->sectionContentTableRow(
                        $groupHeaders, $itemType,  $item, $section,
                        ['edit' => true, 'class' => $itemClasses],
                        [
                            'mode' => $mode,
                            'template_item' => $template_item,
                            'template_section' => $template_section,
                            'template_article' => $template_article
                        ],
                         [
                             'more' => $moreItem,
                             'delete' => $itemDelete
                         ]
                    );
                }

                // Template for new items
                if ($itemAdd || $itemCreate) {
                    $out .= '<div class="doc-section-item ' . (!count($items) ? 'doc-section-item-first' : '') . '">';
                    $out .= '<div class="doc-field doc-field-itemtype">'
                        . $this->Types->getCaption('items', $itemType, ucfirst($itemType))
                        . '</div>';
                    $out .= $this->itemAddButton($itemCount, $itemType);
                    if ($fileUpload) {
                        $out .= $this->itemFolderButton($section);
                    }

                    $templateItem = new Item([
                        'itemtype' => $itemType,
                        'id' => '{id}',
                        'sections_id' => $section->id, //'{sections-id}',
                        'articles_id' => $section->articles_id,
                        'pos_x' => '1',
                        'pos_y' => '1',
                        'pos_z' => '1'
                    ], [
                        'source' => 'Epi.Items',
                        'useSetters' => false,
                        'markClean' => true,
                        'markNew' => true
                    ]);

                    $templateItem->setSource('Epi.Items');
                    $templateItem->container = $section;
                    $templateItem->root = $section->root;

                    $out .= '<script type="text/template" class="template template-doc-section-item">';
                    $out .= $this->sectionContentTableRow(
                        $groupHeaders, $itemType,  $templateItem, $section,
                        ['edit' => true],
                        [],
                        [
                            'more' => $moreItem,
                            'delete' => $itemDelete
                        ]
                    );
                    $out .= '</script>';

                    $out .= '</div>';
                }
            }

            $out .= '</div>';
        }

        // Annotations
        $items = Arrays::ungroup($groupedItems);
        $out .= $this->annoLists($article, $items, ['edit' => true, 'mode' => $mode]);

        return $out;
    }

    /**
     * Output a row in a section table
     *
     * @param array $groupHeaders
     * @param $itemType
     * @param $item
     * @param $section
     * @param array $itemOptions
     * @param array $fieldOptions
     * @param array $buttons
     * @return string
     */
    public function sectionContentTableRow(
        array $groupHeaders,
        $itemType,
        $item,
        $section,
        array $itemOptions = [],
        array $fieldOptions = [],
        array $buttons = []
    ): string {

        $out = $this->itemStart($item, $itemOptions);

        // First column contains the item type caption
        $out .= $this->Element->outputHtmlElement(
            'div',
            $this->Types->getCaption('items', $itemType, ucfirst($itemType)),
            ['class' => 'doc-field doc-field-itemtype']
        );

        foreach ($groupHeaders as $groupHeader) {
            $fieldName = $groupHeader['fields'][$itemType]['fieldname'] ?? '';
            if (empty($fieldName)) {
                $out .= '<div></div>';
                continue;
            }
            $out .= $this->itemField(
                $item,
                $fieldName,
                [
                    'edit' => true,
                    'caption' => false,
                ] + $fieldOptions
            );
        }


        if ($buttons['more'] ?? false) {
            $out .= $this->itemMoreButton($section->id, $item->id, true);
        }

        if ($buttons['delete'] ?? false) {
            $out .= $this->itemRemoveButton($section->id, $item->id);
        }

        $out .= $this->itemEnd();

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
        if (!$edit) {
            return $this->EntityHtml->itemFieldDate($item, $fieldNameParts, $edit, $options);
        }

        $value = parent::itemFieldDate($item, $fieldNameParts, $edit, $options);


        $fieldAttributes = [
            'value' => $value,
            'data-row-field' => 'date_value'
        ];

        if (!empty($options['fieldConfig']['width'])) {
            $fieldAttributes['style'] = Attributes::toStyles(
                ['width' => $options['fieldConfig']['width'] . 'em'],
                true
            );
        }

        $inputField = $options['inputPath'] . '[date_value]';
        $content = $this->Form->input($inputField, $fieldAttributes);
        return "<div class=\"doc-field-content\">{$content}</div>";
    }

    /**
     * Output a JSON field
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit true
     * @param array $options
     * @return string
     */
    public function itemFieldJson($item, $fieldNameParts, $edit, $options=[])
    {
        if (!$edit) {
            return $this->EntityHtml->itemFieldJson($item, $fieldNameParts, $edit, $options);
        }

        $value = $item->getValueRaw($fieldNameParts);
        $value = $this->Form->textarea($options['inputField'], ['value' => $value]);
        return "<div class=\"doc-field-content\">{$value}</div>";
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
        if (!$edit) {
            return $this->EntityHtml->itemFieldImageurl($item, $fieldNameParts, $edit, $options);
        }

        $fieldName = implode('.', $fieldNameParts);

        $value = $item[$fieldName . '_name'] ?? '';
        $path = $item[$fieldName . '_path'] ?? '';

        $content = $this->Form->input($options['inputField'], ['value' => $value, 'data-row-field' => $fieldName]);

        return "<div class=\"doc-field-content\">{$content}</div>";
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
        if (!$edit) {
            return $this->EntityHtml->itemFieldRecord($item, $fieldNameParts, $edit, $options);
        }

        // TODO: output name and create chooser
        // TODO: rename 'links' to 'to' in the database and everywhere
        // TODO: output class field-problem for missing targets as in itemFieldProperty()

        $content = $item->getValueFormatted($fieldNameParts);

        $out = '';
        $inputField = $options['inputPath'] . '[links_tab]';

        $out .= $this->Form->hidden(
            $inputField,
            ['value' => $item['links_tab'], 'data-row-field' => 'links_tab']
        );
        $inputField = $options['inputPath'] . '[links_id]';
        $out .= $this->Form->hidden(
            $inputField,
            ['value' => $item['links_id'], 'data-row-field' => 'links_id']
        );

        // Construct target ID value
        $inputField = $options['inputPath'] . '[to_id]';
        if (!empty($item['to_id'])) {
            $value = $item['to_id'];
        }
        elseif (!empty($item['links_tab']) && !empty($item['links_id'] )) {
            $value = $item['links_tab'] . '-' . $item['links_id'];
        }
        else {
            $value = '';
        }

        $options = [
            'caption' => false,
            'type' => 'reference',
            'value' => $value,
            'text' => $content,
        ];

        $out .= $this->Form->input($inputField, $options);


        return $out;
    }

    /**
     * Render a button that adds items in tables and grids
     *
     * @param integer $itemCount
     * @param string $itemType
     * @return string
     */
    public function itemAddButton($itemCount = 1, $itemType = '')
    {
        $out = $this->Element->openHtmlElement(
            'div',
            ['class' => 'doc-field doc-field-add doc-field-add-' . $itemType]
        );

        $out .= $this->Element->outputHtmlElement(
            'button',
            '+',
            [
                'class' => 'doc-item-add tiny',
                'data-items-max' => $itemCount,
                'title' => __('Add item'),
                'type' => 'button',
                'aria-label' => __('Add item')
            ]
        );

        $out .= $this->Element->closeHtmlElement('div');
        return $out;
    }

    /**
     * Render a button that opens the folder for file uploads
     *
     * @param Section $article
     * @return string
     */
    public function itemFolderButton(Section $section) : string
    {
        $article = $section->container;
        $listName = "files-for-sections-{$section->id}";

        $out = $this->Element->openHtmlElement(
            'div',
            [
                'class' => 'doc-field doc-field-folder',
                'data-target-list' => $listName
            ]
        );

        $manageUrl = $this->Url->build([
            'controller' => 'Files',
            'action' => 'index',
            '?' => [
                'path' => Files::joinPath([$article->fileBasepath, $article->fileDefaultpath])
            ]
        ]);

        $out .= $this->Html->link(
            __('Folder {0}',$article->fileDefaultpath),
            [
                'controller' => 'Files',
                'action' => 'select',
                '?' => [
                    'path' => Files::joinPath([$article->fileBasepath, $article->fileDefaultpath]),
                    'basepath' => Files::joinPath([$article->fileBasepath, $article->fileDefaultpath]),
                    'template' => 'upload',
                    'list' => $listName,
                ]
            ],
            [
                'class' => 'frame button tiny doc-item-folder',
                'data-frame-target' => 'folder',
                'data-frame-caption' => 'Files',
                'data-frame-external' => $manageUrl,
            ]
        );

        $out .= $this->Element->closeHtmlElement('div');
        return $out;
    }
    /**
     * Render a button that removes items in tables
     *
     * @param string $sectionId The section ID
     * @param string $itemId The item ID or {id} as a placeholder
     * @return string
     */
    public function itemRemoveButton($sectionId, $itemId)
    {
        return '<div class="doc-field doc-field-remove">'
            . $this->Form->hidden('sections[' . $sectionId . '][items][' . $itemId . '][deleted]', ['value' => 0, 'data-row-field' => 'deleted'])
            . '<button class="doc-item-remove tiny"
                title="' . __('Remove item') . '"
                type="button"
                aria-label="' . __('Remove item') . '">-</button>'
            . '</div>';

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

        $formId = 'form-edit-'
            . ($data['root_tab'] ?? '{rootTab}')
            . '-'
            . ($data['root_id'] ?? '{rootId}');

        $edit = $data['edit'] ?? true;

        $rowAttributes =  [
            'id' => 'doc-footnote-id-' . ($data['from_tagid'] ?? '{fromTagid}'),
            'class' => 'doc-footnote',
            'data-tagid' => ($data['from_tagid'] ?? '{fromTagid}'),
            'data-row-table' => 'footnotes',
            'data-row-id' => ($data['id'] ?? '{id}'),
            'data-row-type' => ($data['type'] ?? '{type}')
        ];

        $out = $this->Element->openHtmlElement('div', $rowAttributes);

        // Hidden inputs
        $out .= implode(' ', array_map(
            fn($x) => $this->Form->hidden(
                'footnotes[' . ($data['idx'] ?? '{idx}') . '][' . $x . ']',
                [
                    'value' => ($data[$x] ?? ('{' . Inflector::variable($x) . '|attr}')),
                    'data-row-field' => $x,
                    'form' => $formId
                ]
            ),
            [
                'id',
                'root_id',
                'root_tab',
                'from_id',
                'from_tab',
                'from_field',
                'from_tagid',
                'from_tagname',
                'name'
            ]
        ));

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

        $out .= $this->Element->outputHtmlElement(
            'div',
            $this->footnoteRemoveButton($data['id'] ?? '{id}', $formId),
            ['class' => 'doc-footnote-buttons']
        );

        $out .= $this->Element->closeHtmlElement('div');

        return $out;
    }



}
