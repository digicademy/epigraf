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

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Databank;
use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use App\Utilities\Converters\Objects;
use App\Utilities\Files\Files;
use Cake\Collection\CollectionInterface;
use Cake\Core\Configure;
use Cake\Datasource\QueryInterface;
use Cake\ORM\Entity;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use App\Utilities\Converters\Numbers;
use Epi\Model\Entity\Article;
use Epi\Model\Entity\Footnote;
use Epi\Model\Entity\Item;
use Epi\Model\Entity\RootEntity;
use Epi\Model\Entity\Section;
use Epi\View\Helper\TypesHelper;
use Exception;
use Rest\Entity\LockTrait;

/**
 * Entity helper
 *
 * Renders entities in the frontend.
 * Creates editable sections, items and related stuff.
 * The created HTML elements are made interactive in models.js
 * and are styled by models.css.
 *
 * The main functions are:
 * - sectionList() to create the sections of articles
 * - entityForm() to create input forms for entities
 *
 * @property Helper\HtmlHelper $Html
 * @property Helper\FormHelper $Form
 * @property TypesHelper $Types
 * @property LinkHelper $Link
 * @property ElementHelper $Element
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class BaseEntityHelper extends Helper
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
    public $helpers = ['Html', 'Url', 'Types', 'Element', 'Form', 'Table', 'Link', 'Files'];

    /**
     * Output the article header
     *
     * // TODO: make editable?
     *
     * @param RootEntity $entity
     * @param array $options
     * @return string
     */
    public function docHeader($entity, $options=[])
    {
        $headers = $entity->type->header ?? [];
        if (!$headers) {
            return '';
        }
        $elementCount = 0;

        // Norm data
        $out_normdata = '';
        foreach (($entity->normDataParsed ?? []) as $norm_data) {
            if (!empty($norm_data['button'])) {
                $out_normdata .= $this->Html->link($norm_data['button'], $norm_data['url'],
                    ['target' => '_blank', 'class' => 'button']);
                $elementCount++;
            }
        }

        // Header fields
        $out_headers = '';
        foreach ($headers as $no => $header) {

            // Hide non-public headers for guests
            if (!($header['public'] ?? true) && ($entity->currentUserRole === 'guest')) {
                continue;
            }

            $classes = ['doc-header-' . ($no + 1)];
            $classes[] = ($header['grow'] ?? false) ? 'doc-header-grow' : '';

            $out_headers .= '<div class="' . implode(" ", array_filter($classes)) . '">';
            $out_headers .= $header['caption'] ?? '';

//            $header['aggregate'] = false;
//            $items = $entity->getValueNested($header['key'], ['format' => 'html'] + $header);
            $items = $entity->getValuePlaceholder($header['key'], ['format' => 'html'] + $header);
            $items = is_array($items) ? implode("<br> ", $items) : $items;

            $out_headers .= $items;
            $out_headers .= '</div>';

            if (!empty($items)) {
                $elementCount++;
            }
        }

        $out_classes = [];
        $out_classes[] = 'doc-header';
        if (empty($elementCount)) {
            return '';
//            $out_classes[] = 'doc-header-empty';
        }

        return $this->Element->outputHtmlElement(
            'div',
            $out_normdata . $out_headers,
            ['class' => $out_classes]
        );

    }

    /**
     *  Output metadata in the article footer
     *
     *  (based on _getHtmlFields of the article entity)
     *
     * @param Article $entity
     * @param array $options
     *
     * @return string
     */
    public function docContent($entity, $options = [])
    {
        $action = $this->_View->getRequest()->getParam('action') ?? 'view';
        $out = $this->Element->openHtmlElement('div', ['id' => 'doc-' . $entity->id . '-content', 'class' => 'doc-content']);

        foreach ($entity->htmlFields as $fieldName => $fieldOptions) {

            // Visible for which actions?
            $actions = !isset($fieldOptions['action']) ?
                [$action] :
                (is_string($fieldOptions['action']) ? [$fieldOptions['action']] : $fieldOptions['action']);

            if (!in_array($action, $actions)) {
                continue;
            }

            // Public visibility?
            // TODO: respect $public = $fieldOptions['public'] ?? true;
            if (!$entity->getFieldIsVisible($fieldName)) {
                continue;
            }

            $fieldOptions['edit'] = $options['template_article']['edit'] ?? $options['edit'] ?? false;
            $out .= $this->docContentField($entity, $fieldName, $fieldOptions, $action);
        }

        $out .= $this->Element->closeHtmlElement('div');

        return $out;
    }

    /**
     * Output footer field
     *
     * @param Entity $entity
     * @param string $fieldName
     * @param array $options
     * @param string $action add|edit|view
     *
     * @return string
     */
    public function docContentField($entity, $fieldName, $options, $action = 'edit')
    {
        $help = $options['help'] ?? '';
        unset($options['help']);

        $content = $this->entityField($entity, $fieldName, $options, $action);

        $classes = [];
        $classes[] = 'doc-content-element';
        $classes[] = empty($content) ? 'doc-content-element-empty' : '';
        $classes = array_filter($classes);

        $out = $this->Element->openHtmlElement(
            'div',
            ['class' => $classes, 'data-row-field' => $fieldName]
        );
        $out .= '<div class="doc-content-fieldname">' . ($options['caption'] ?? '') . '</div>';

        if ($help !== '') {
            $out .= '<div class="doc-content-help widget-tooltip" title="' . $help . '">?</div>';
        }

        $out .= '<div class="doc-content-content">' . $content . '</div>';

        $out .= $this->Element->closeHtmlElement('div');

        return $out;
    }

    /**
     * Output problems in orange boxes
     *
     * @param BaseEntity $entity
     * @param array $options Not used yet
     * @return string
     */
    public function docProblems($entity, $options = [])
    {
        $problems = $entity->problems ?? [];
        if (empty($problems)) {
            return '';
        }

        $out = '<div class="art-problems">';
        foreach ($problems as $problem) {
            $out .= '<div class="art-problems-value">' . $problem . '</div>';
        }
        $out .= '</div>';

        return $out;
    }

    /**
     * Output sections
     *
     * @param $article
     * @param array $options Array with the keys 'article', 'edit', 'mode' and 'template_article'.
     *
     * @return string
     */
    public function sectionList($article, $options = [])
    {
        $out = '';
        $ignore = $options['ignore']['sections'] ?? [];
        foreach ($article->sections as $section) {
            if (in_array($section->sectiontype, $ignore)) {
                continue;
            }
            $out .= $this->sectionContent($section, $options);
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
        $mode = $options['mode'];

        $sectionConfig = $section->type['merged'] ?? [];
        $sectionConfig['view'] = Arrays::stringToArray($sectionConfig['view'] ?? 'stack', 'name', 'stack');
        $options['template_section'] = $sectionConfig;

        //Section start
        $out = $this->sectionStart($section, $options);

        // Widgets (map, image)
        $widgets = trim($this->sectionWidgets($options) ?? '');
        $options['haswidget'] = !empty($widgets);
        $out .= $widgets;

        // Section content
        if ($sectionConfig['view']['name'] === 'stack') {
            $out .= $this->sectionContentStacks($section, $options);
        } else {
            $out .= $this->sectionContentTables($section, $options);
        }

        // Section end
        $out .= $this->sectionEnd();

        return $out;
    }

    /**
     * Render section content as table
     *
     * Overwrite in child helpers.
     *
     * @param array $options
     * @return string
     */
    public function sectionContentTables($section, $options) {
        return '';
    }

    /**
     * Render section content as stack
     *
     * Overwrite in child helpers.
     *
     * @param array $options
     * @return string
     */
    public function sectionContentStacks($section, $options) {
        return '';
    }

    /**
     * Open a section tag
     *
     * @param Section $section
     * @param array $options Array with the keys 'edit', 'mode', 'article', 'section', 'template_article', 'template_section.view'
     *
     * @return string
     */
    public function sectionStart($section, $options = [])
    {

        $sectionId = ($section['id'] ?? '{sections-id}');
        $parentId = ($section['parent_id'] ?? ''); // '{sections-parent-id}'

        $out = $this->Element->openHtmlElement('div',[
            'id' => 'sections-' . $sectionId,
            'data-row-table' => 'sections',
            'data-row-type' => $section['sectiontype'],
            'data-row-id' => $sectionId,
            'data-row-parent-id' => $parentId,
            'data-row-level' => ($section['level'] ?? '{sections-level}'),
            'data-position' => $options['template_section']['position'] ?? false,
            'class' => [
                'doc-section',
                'doc-section-type-' . $section['sectiontype'],
                'doc-section-level-' . $section['level'],
                'widget-scrollsync-section',
                ($section->type->merged['display'] ?? '') === 'highlight' ? 'doc-section-highlight' : null,
                ($section->type->merged['display'] ?? '') === 'addendum' ? 'doc-section-addendum' : null,
                !empty($section->collapsed ?? false) ? 'doc-section-collapsed' : null,
                empty($section->type->merged['display'] ?? true) ? 'doc-section-hide' : null,
                'doc-section-view-' . ($options['template_section']['view']['name'] ?? ''),
                $section->empty ? 'doc-section-empty' : null,
                $section->children ? 'doc-section-children' : null
            ]
        ]);

        // Hidden inputs
        if ($options['edit'] ?? false) {
            $disabled = $section->isNew() ? false : 'disabled';
            $out .= $this->Form->hidden('sections[' . $sectionId . '][id]', ['value' => $sectionId]);
            $out .= $this->Form->hidden(
                'sections[' . $sectionId . '][deleted]',
                ['value' => 0, 'data-row-field' => 'deleted']
            );
            $out .= $this->Form->hidden(
                'sections[' . $sectionId . '][number]',
                ['value' => $section->number, 'data-row-field' => 'number', 'disabled' => $disabled]
            );
            $out .= $this->Form->hidden(
                'sections[' . $sectionId . '][parent_id]',
                ['value' => $parentId, 'data-row-field' => 'parent_id', 'disabled' => $disabled]
            );
            $out .= $this->Form->hidden(
                'sections[' . $sectionId . '][sectiontype]',
                ['value' =>  $section['sectiontype'], 'data-row-field' => 'sectiontype', 'disabled' => $disabled]
            );
            $out .= $this->Form->hidden(
                'sections[' . $sectionId . '][sortno]',
                ['value' => $section->sortno, 'data-row-field' => 'sortno', 'disabled' => $disabled]
            );

            // Search items as hidden fields (so they don't get lost when patching request data)
            $searchItems = $section->getItemsByType(ITEMTYPE_FULLTEXT);
            foreach ($searchItems as $item) {
                $out .= $this->Form->hidden(
                    'sections[' . $sectionId . '][items][' . $item['id'] . '][id]',
                    [
                        'value' => $item['id'],
                        'data-row-table' => 'items',
                        'data-row-id'=>$item['id'],
                        'data-row-type' => $item['itemtype']
                    ]
                );
            }
        }

        // Header
        $uiKey = null;
        $uiCollapsed = null;
        if ($section->root && !empty($section['sectiontype'])) {
            $uiKey = 'switch-'
                . $section->root->tableName . '-'
                . ($section->root->articletype ?? 'undefined') . '-'
                . ($section['sectiontype'] ?? 'undefined');
            $uiActive = $this->getView()->getUiSetting($uiKey, 'active', false);
            $uiCollapsed = is_null($uiActive) ? $uiActive : $uiActive === 'false';
        }
        $sectionCollapsed = $uiCollapsed ?? $section['collapsed'] ?? false;

        $out .= $this->Element->openHtmlElement(
            'div',
            [
                'class' => [
                    'doc-section-head',
                    'widget-switch',
                    $sectionCollapsed ? null : 'widget-switch-active'
                ],
                'data-switch-element' => '#doc-section-content-' . $sectionId,
                'data-switch-class' => 'toggle-hide',
                'data-ui-key' => $uiKey,
                'data-switch-reverse' => '1'
            ]
        );

        // Indentation
        if ($section->level > 1) {
            $indent = str_repeat(' ⬥ ', $section->level - 1);
            $out .= '<div class="doc-section-indent">' . $indent . '</div>';
        }

        // Title
        $out .= '<div class="doc-section-name" data-row-name="' . htmlentities($section->name) . '">';
        $fieldConfig = $section->type->config['fields'] ?? [];

        // Edit section fields?
        $editSection = ($options['edit'] ?? false) && ($options['template_section']['edit'] ?? $options['template_article']['edit'] ?? true);

        $out .= $this->sectionName($section, $options);

        // Published
        if ($section->getFieldIsVisible('published') && !empty($fieldConfig['published'])) {
            $out .= $this->sectionPublishedButton(
                $sectionId,
                $section,
                ['edit' => ($editSection) && ($fieldConfig['published']['edit'] ?? true)]
            );
        }

        // Help button
        if ($options['edit'] ?? false) {
            $out .= $this->sectionHelpButton($section);
        }

        // Detach button
        if ($options['edit'] ?? false) {
            $out .= $this->sectionDetachButton($section);
        }

        // Toggle content button
        $out .= $this->sectionToggleButton();

        $out .= $this->Element->closeHtmlElement('div');

        // Note
        if ($options['note'] ?? false) {
            $out .= $this->notesItem($section, $options);
        }

        $out .= $this->Element->openHtmlElement(
            'div',
            [
                'id' => 'doc-section-content-' . $sectionId,
                'class' => [
                    'doc-section-content',
                    $sectionCollapsed ? 'toggle-hide' : ''
                ]
            ]
        );

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

        // Caption
        if ($editSection && ($fieldConfig['name']['edit'] ?? false)) {
            $out .= ($prefix ?? '') . ' ';

            $options = $fieldConfig['name']['options'] ?? [];

            // as text field
            if (empty($options)) {
                $out .= $this->Form->input(
                    'sections[' . $sectionId . '][name]',
                    ['value' => $section->name, 'data-row-field' => 'name']
                );
            }

            // as select field
            else {
                $out .= $this->Form->hidden(
                    'sections[' . $sectionId . '][name]',
                    ['value' => $section->name, 'data-row-field' => 'name']
                );

                $options = $section
                    ->getOptions('Epi.Properties', ['propertytype' => $options], 'lemma')
                    ->toArray();
                $section->properties_id = array_search($section->name, $options);

                $out .= $this->Form->input(
                    'sections[' . $sectionId . '][properties_id]',
                    [
                        'type' => 'select',
                        'empty' => false,
                        'class' => 'doc-section-property',
                        'value' => $section->properties_id,
                        'data-row-field' => 'properties_id',
                        'options' => $options
                    ]
                );
            }
        }
        else {
            $out .= $this->Form->hidden(
                'sections[' . $sectionId . '][name]',
                [
                    'value' => $section->name ?? $section->type->caption ?? $section->sectiontype ?? 'Section',
                    'data-row-field' => 'name'
                ]
            );

            $showpath = $section->type['merged']['name']['path'] ?? false;
            if ($showpath && !empty($section['path'])) {
                $path = $this->sectionPath($section['path'], 'name');
            }
            else {
                $path = $section['name'] ?? $section->type->caption ?? $section->sectiontype ?? __('Section');
            }
            $out .= '<span data-value="name">' . $prefix . $path . '</span>';
        }

        // Alias
        if (($editSection) && ($fieldConfig['alias']['edit'] ?? false)) {
            $out .= ' ['
                . $this->Form->input(
                    'sections[' . $sectionId . '][alias]',
                    ['value' => $section->alias]
                )
                . ']';
        }
        else {
            $alias = $section['alias'] ?? '';
            if (!empty($alias)) {
                $out .= ' [<span data-value="alias">' . $alias . '</span>]';
            }
        }
        $out .= '</div>';

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
     * Output a label or a selector for the publication state
     *
     * Outputs a selector if the edit key of the $options array is true,
     * a label otherwise.
     *
     * @param string|int $sectionId
     * @param Section $section
     * @param array $options
     * @return string
     */
    public function sectionPublishedButton($sectionId, $section, $options = []): string
    {
        $out = '<div class="doc-section-published doc-section-published-' . ($section['published'] ?? PUBLICATION_DRAFTED) . '">';
        if ($options['edit'] ?? false) {
            $out .= $this->Form->input(
                'sections[' . $sectionId . '][published]',
                [
                    'type' => 'select',
                    'empty' => true,
                    'class' => 'doc-section-published',
                    'value' => $section->published,
                    'options' => $section->publishedOptions
                ]
            );
        }
        else {
            $out .= '● ' . $section->published_label;
        }

        $out .= '</div>';

        return $out;
    }


    /**
     * Get the comment button
     *
     * @param $toggleElement
     * @param array $options
     * @return string
     */
    public function sectionCommentButton($toggleElement, array $options = []): string
    {
        return '<button type="button" ' .
            'class="button-comment button-toggle button-toggle-' . (int)empty($options['empty']) . '" ' .
            'data-toggle-element="' . $toggleElement . '">' .
            '</button>';
    }

    public function sectionHelpButton($section) {
        $help_iri = false;

        if (isset($section->type->config['help'])) {
            $help_iri = $section->type->config['help'];
        }
        elseif ($section->root->type && $section->type) {
            $help_iri = 'articletype-' . $section->root->articletype . '-sectiontype-' . $section->sectiontype;
        }

        if ($help_iri !== false) {
            $options = [
                'data-name' => $section->root->type->caption . ': ' . __('Section') . ' ' . $section->type->caption,
                'data-category' => Configure::read('Pages.contexthelp',  'Sections')
            ];
            return $this->Link->helpLink($help_iri, $options);
        }
        return '';
    }

    public function sectionDetachButton($section) {
        return $this->Element->outputHtmlElement(
            'button', "\u{f35d}", // up-right-from-square
            [
                'type' => 'button',
                'title' => __('Open in popup'),
                'area-label' => __('Open in popup'),
                'class' => 'button-section-detach tiny icon',
                'data-target' => 'doc-section-content-' . $section->id
            ]
        );
    }

    /**
     * Output a section toggle button
     *
     * @return string
     */
    public function sectionToggleButton(): string
    {
        return $this->Element->outputHtmlElement(
            'button', '',
            [
                'type' => 'button',
                'class' => 'button-section-content widget-switch-button',
                'title' => __('Toggle section'),
                'area-label' => __('Toggle section')
            ]
        );
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
     * Close a section tag
     *
     * @return string
     */
    public function sectionEnd()
    {
        return '</div>' . $this->Element->closeHtmlElement('div');
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
        $out = $this->itemStart($item, $options);

        $itemType = is_string($item) ? $item : $item['itemtype'];
        $action = ($options['edit'] ?? false) ? 'edit' : 'view';
        $itemFields = $this->Types->getFields('items', $itemType, $options);
        unset($options['defaultFields']);

        foreach ($itemFields as $fieldName => $fieldConfig) {

            if ((($fieldConfig['force'] ?? true) || !empty($item[$fieldName]))) {
                $out .= $this->itemField(
                    $item,
                    $fieldName,
                    array_merge(
                        [
                            'edit' => $options['edit'] ?? false,
                            'mode' => $options['mode'] ?? $action,
                            'caption'=> $fieldConfig['showcaption'] ?? true ? ($fieldConfig['caption'] ?? false) : false,
                        ],
                        $options['templates'] ?? []
                    )
                );
            }
        }


        // Add service buttons
        if ($options['edit'] ?? false) {
            foreach ($item->type->merged['services'] ?? [] as $serviceName => $serviceOptions) {
                $out .= $this->itemServiceButton($item, $serviceName, $serviceOptions);
            }
        }

        $out .= $this->itemEnd();
        return $out;
    }

    public function itemServiceButton($item, $serviceName, $serviceOptions)
    {
        $out = '';
        $serviceOptions['database'] = $item->databaseName;
        $out .= $this->Element->openHtmlElement(
            'div',
            [
                'class' => 'widget-service-button',
                'data-service-name' => $serviceName,
                'data-service-item' => $item->id ?? '{id}',
                'data-service-data' => json_encode($serviceOptions),
            ]
        );

        $out .= $this->Html->link(
            $serviceOptions['caption'] ?? $serviceName,
            '#',
            [
                'class' => 'button tiny',
            ]
        );

        $out .= $this->Element->closeHtmlElement('div');
        return $out;
    }

    /**
     * Open an item element
     *
     * @param Item|array|string $item The item data as an object or array.
     *                                Alternatively, the itemtype as a string.
     *                                Missing fields (id, sections_id, itemtype) will create
     *                                placeholders in curly brackets.
     * @param $options
     *
     * @return string
     */
    public function itemStart($item, $options = []): string
    {
        if (is_string($item)) {
            $item = ['itemtype' => $item];
        }
        $itemId = $item['id'] ?? '{id}';
        $sectionId = $item['sections_id'] ?? '{sections-id}';
        $itemType = $item['itemtype'] ?? '{itemtype}';

        $classes = ['doc-section-item', $options['class'] ?? ''];
        $classes[] = $this->Types->getDisplay('items', $itemType) === 'highlight' ? 'doc-item-highlight' : '';
        $classes[] = $this->Types->getDisplay('items', $itemType) === 'addendum' ? 'doc-item-addendum' : '';
        $classes[] = !$this->Types->getDisplay('items', $itemType, true) ? 'doc-item-hide' : '';

        $divAttributes = [
            'class' => implode(' ', array_filter($classes)),
            'data-row-table' => 'items',
            'data-row-id' => $itemId,
            'data-row-type' => $itemType,
        ];

        if ($options['draggable'] ?? false) {
            $divAttributes['draggable'] = "true";
        }

        if ($options['preview'] ?? false) {
            $divAttributes['data-preview'] = true;
        }

        $out = $this->Element->openHtmlElement('div', $divAttributes);

        if ($options['edit'] ?? false) {
            $out .= $this->Form->hidden('sections[' . $sectionId . '][items][' . $itemId . '][id]',
                ['value' => $itemId]);
            $out .= $this->Form->hidden('sections[' . $sectionId . '][items][' . $itemId . '][itemtype]',
                ['value' => $itemType]);
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
        // Get IDs
        $item_id = $item->id ?? '{id}';
        $table = $item->table_name;
        $section_id = $item->sections_id ?? '{sections-id}';
        $fieldNameParts = explode('.', $fieldName);

        // Get config
        $format = $item->getFieldFormat($fieldNameParts);
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

        // Merge edit settings
        $options['edit'] = ($options['edit'] ?? false) && (
                $options['template_item']['fields'][$fieldNameParts[0]]['keys'][$fieldNameParts[1] ?? '']['edit'] ??
                $options['template_item']['fields'][$fieldNameParts[0]]['edit'] ??
                $options['template_item']['edit'] ??
                $options['template_section']['edit'] ??
                $options['template_article']['edit'] ??
                true
            );

        $edit = ($item->type['merged']['edit'] ?? true) && ($fieldConfig['edit'] ?? true) && ($options['edit'] ?? false);

        // Merge options
        $options = array_merge($options, compact(
            'item_id','section_id', 'table',
             'inputPath', 'inputField',
             'format', 'empty', 'fieldConfig'
        ));

        // Container classes and attributes
        $classes = ['doc-field', 'doc-fieldname-' . implode('-', $fieldNameParts)];
        $classes[] = $empty ? 'doc-field-empty' : '';
        $classes[] = ($options['class'] ?? false) ? $options['class'] : '';

        if ($fieldNameParts[0] === 'itemgroup') {
            $classes[] = 'widget-fieldgroup';
        }

        // Hide field in view mode if display is set to false
        if ((!($fieldConfig['display'] ?? true) && !($options['edit'] ?? false)) ) {
            $classes[] = 'doc-field-hide';
        }

        // Add additional visual classes (highlight, addendum, more, hide)
        if (is_string($fieldConfig['display'] ?? true)) {
            $classes[] = 'doc-field-' . $fieldConfig['display'];
        }
        $classes = implode(' ', array_filter($classes));

        $itemAttrs = [
            'class' => $classes,
            'data-row-field' => $fieldName,
            'data-row-format' => $format
        ];
        $itemAttrs = array_filter($itemAttrs);

        // Open container
        $out = '<div ' . Attributes::toHtml($itemAttrs) . '>';

        // Caption
        $caption = $options['caption'] ?? null;
        $showcaption = ($caption !== false) && ($edit || (($caption !== null) && !$empty)) &&
            ($format !== 'check') && ($format !== 'position');

        if ($showcaption) {
            $out .= "<span class=\"doc-field-caption\">{$caption}</span>";
        }

        // Errors
        $errors = !empty($fieldNameParts[0]) ? $item->getError($fieldNameParts[0]) : [];
        if (!empty($errors)) {
            $errors = implode(' ', $errors);
            $out .= "<div class=\"doc-field-error\">{$errors}</div>";
        }

        // JSON
        if ($format === 'json') {
            $out .= $this->itemFieldJson($item, $fieldNameParts, $edit, $options);
        }

        // XML
        elseif ($format === 'xml') {
            $out .= $this->itemFieldXml($item, $fieldNameParts, $edit, $options);
        }

        // Property
        elseif ($format === 'property') {
            $out .= $this->itemFieldProperty($item, $fieldNameParts, $edit, $options);
        }

        // Property: Auto fill from the caption selector
        elseif ($format === 'sectionname') {
            $out .= $this->itemFieldSectionname($item, $fieldNameParts, $edit, $options);
        }

        // Property with unit
        elseif ($format === 'unit') {
            $out .= $this->itemFieldUnit($item, $fieldNameParts, $edit, $options);
        }

        // Linked record
        elseif ($format === 'record') {
            $out .= $this->itemFieldRecord($item, $fieldNameParts, $edit, $options);
        }

        // Linked record
        elseif ($format === 'relation') {
            $out .= $this->itemFieldRecord($item, $fieldNameParts, $edit, $options);
        }

        // Date
        elseif ($format === 'date') {
            $out .= $this->itemFieldDate($item, $fieldNameParts, $edit, $options);
        }

        // Checkbox
        elseif ($format === 'check') {
            $out .= $this->itemFieldCheck($item, $fieldNameParts, $edit, $options);
        }

        // Select
        elseif ($format === 'select') {
            $out .= $this->itemFieldSelect($item, $fieldNameParts, $edit, $options);
        }

        // Published
        elseif ($format === 'published') {
            $out .= $this->itemFieldPublished($item, $fieldNameParts, $edit, $options);
        }

        // Position in grid
        elseif ($format === 'position') {
            $out .= $this->itemFieldPosition($item, $fieldNameParts, $edit, $options);
        }

        // File
        elseif (($format === 'file') || ($format === 'image')) {
            $out .= $this->itemFieldFile($item, $fieldNameParts, $edit, $options);
        }

        // Image URL
        elseif ($format === 'imageurl') {
            $out .= $this->itemFieldImageurl($item, $fieldNameParts, $edit, $options);
        }

        // Hyperlink
        elseif ($format === 'link') {
            $out .= $this->itemFieldLink($item, $fieldNameParts, $edit, $options);
        }

        // Number
        elseif ($format === 'number') {
            $out .= $this->itemFieldNumber($item, $fieldNameParts, $edit, $options);
        }

        // Raw values
        else {
            $out .= $this->itemFieldRaw($item, $fieldNameParts, $edit, $options);
        }

        // Close container
        $out .= "</div>";

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
        if ($edit) {
            $content = $this->Form->input($options['inputField'], ['value' => $content]);
        }
        if (is_array($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
        return "<div class=\"doc-field-content\">{$content}</div>";
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
        if ($edit) {
            $content = $this->Form->input($options['inputField'], ['type' => 'number', 'value' => $content]);
        }
        if (is_array($content)) {
            $content = json_encode($content, JSON_PRETTY_PRINT);
        }
        return "<div class=\"doc-field-content\">{$content}</div>";
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
        $value = $item->getValueFormatted($fieldNameParts);
        $value = Attributes::toList($value, true);
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
    public function itemFieldXml($item, $fieldNameParts, $edit, $options=[])
    {
        if(is_array($item)) {
            $placeHolder = '{'.$fieldNameParts[0].'}';
            $content =  $item[$fieldNameParts[0]] ?? $placeHolder;
            $contentAttr = $content === $placeHolder ? ('{'.$fieldNameParts[0].'|attr}') : $content;
        } else {
            $content = $item->getValueFormatted($fieldNameParts);
            $contentAttr = $content;
        }

        $out = '';

        if ($edit) {
            $inputOptions = ['value' => $contentAttr];
            $formId = $options['form'] ?? null;
            if ($formId) {
                $inputOptions['form'] = $formId;
            }
            $out .= $this->Form->hidden($options['inputField'], $inputOptions);


            $styles = [];
            if (!empty($options['fieldConfig']['width'])) {
                $styles['min-width'] = $options['fieldConfig']['width'] . 'em';
            }
            if (!empty($options['fieldConfig']['height'])) {
                $styles['min-height'] = $options['fieldConfig']['height'] . 'em';
            }

            $out .= $this->Element->outputHtmlElement(
                'div',
                $content,
                ['class' => 'doc-field-content widget-xmleditor', 'style' => empty($styles) ? null : $styles]
            );
        }
        else {
            $out .= "<div class=\"doc-field-content\">{$content}</div>";
        }
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
        return $item->getValueFormatted($fieldNameParts);
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
        // TODO: what if the field is not named date_value, date_add?
        $fieldName = implode('.', $fieldNameParts);
        return $item[$fieldName . '_value'] ?? '';
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
        $inputField = $options['inputField'];

        $content = $item->getValueFormatted($fieldNameParts);
        if ($edit) {
            $content = $this->Form->checkbox($inputField, ['checked' => $content, 'id' => $inputField]);
            $content .= "<label for=\"$inputField\">" . $item->getFieldCaption($fieldName) . '</label>';

            return "<div class=\"doc-field-content\">$content</div>";
        }
        else {
            $content = empty($content) ? '' : $item->getFieldCaption($fieldName);
            return "<div class=\"doc-field-content\">$content</div>";
        }
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
        $inputField = $options['inputField'];

        if ($edit) {
            $value = $item->getValueRaw($fieldNameParts);
            $codes = $options['fieldConfig']['options'] ?? [];

            $content = $this->Form->select($inputField, $codes, ['value' => $value, 'empty' => $options['empty'] ?? false]);
            return "<div class=\"doc-field-content\">$content</div>";
        }
        else {
            $content = $item->getValueFormatted($fieldNameParts);
            return "<div class=\"doc-field-content\">$content</div>";
        }
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
        $content = '';

        if ($edit) {
            $inputPath = $options['inputPath'];
            $inputField = $inputPath . '[pos_x]';

            $content .= $this->Form->input($inputField,
                [
                    'type' => 'number',
                    'min' => 0,
                    'max' => $item->container->layout_cols ?? 1,
                    'value' => $item['pos_x'],
                    'data-row-field' => 'pos_x'
                ]);
            $inputField = $inputPath . '[pos_y]';

            $content .= $this->Form->input($inputField,
                [
                    'type' => 'number',
                    'min' => 0,
                    'max' => $item->container->layout_rows ?? 1,
                    'value' => $item['pos_y'],
                    'data-row-field' => 'pos_y'
                ]);
            $inputField = $inputPath . '[pos_z]';

            $content .= $this->Form->input($inputField,
                [
                    'type' => 'number',
                    'min' => 0,
                    'max' => 100,
                    'value' => $item['pos_z'],
                    'data-row-field' => 'pos_z'
                ]);
        }
        else {
            $content = implode('/', [$item['pos_x'], $item['pos_y'], $item['pos_z']]);
        }

        return "<div class=\"doc-field-content\">{$content}</div>";
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
        $format = $options['format'];

        $valueFilename = $item[$fieldName . '_name'] ?? '';
        $valueFilepath = $item[$fieldName . '_path'] ?? '';
        $valueFullname = empty($valueFilepath) ? $valueFilename : ($valueFilepath . '/' . $valueFilename);

        // Assemble path from base folder and file_path
        $fullpath = trim($item->file_basepath . $valueFilepath, '/');
        $selectPath = empty($valueFilename) ? trim($item->file_basepath . $item->file_defaultpath, '/') : $fullpath;

        // Output the image
        if ($format === 'image') {
            if (!empty($item->file_name)) {
                $url = Router::url(['action' => 'view', $item->articles_id, '#' => 'items-' . $item->id]);
                $image = $this->Files->outputImage($item, true, $url);
            } else {
                $image = '';
            }
            $out .= "<div class=\"doc-field-image\">{$image}</div>";
        }

        // Input for file name
        if ($edit) {
            //TODO: alternative for non-online files
            //TODO: open image viewer in edit mode and view mode, provide inputs in image viewer

            $inputField = $inputPath . '[' . $fieldName . '_name]';
            $pathField = $inputPath . '[' . $fieldName . '_path]';

            $content = $item->getValueFormatted($fieldNameParts);
            $content .= $this->Form->control(
                $inputField,
                [
                    'type' => 'choose',
                    'options' => [
                        'controller' => 'Files',
                        'action' => 'select',
                        '?' => [
                            'path' => $selectPath,
                            'basepath' => trim($item->file_basepath, '/')
                        ]
                    ],
                    'label' => false,
                    'value' => $valueFullname,
                    'path' => $valueFilepath,
                    'pathField' => $pathField,
                    'itemtype' => 'file'
                ]);

            // Fix IDs in template string
            //TODO: obsolete?
            if ($options['item_id'] === '{id}') {
                $content = str_replace('items-items-id', 'items-{id}', $content);
            }
            if ($options['section_id'] === '{sections-id}') {
                $content = str_replace('sections-sections-id', 'sections-{sections-id}', $content);
            }

            $out .= "<div class=\"doc-field-content\">{$content}</div>";
        }

        // View path
        elseif ($format !== 'image') {
            if ($item->file_properties['exists']) {
                $out .= "<div class=\"doc-field-content\" data-path=\"{$fullpath}\" data-file-name=\"{$valueFilename}\">{$valueFullname}</div>";
            }
            else {
                $out .= "<div class=\"doc-field-content\">{$valueFullname}</div>";
            }
        }

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

        $url = ($options['fieldConfig']['baseurl'] ?? '') . $path . '/' . $value;
        $content = "<a href=\"$url\" target=\"_blank\">$value</a>";

        return "<div class=\"doc-field-content\">{$content}</div>";
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
        $fieldName = implode('.', $fieldNameParts);

        $content = $item->getValueFormatted($fieldNameParts);
        if ($edit) {
            $content = $this->Form->input($options['inputField'], ['value' => $content, 'data-row-field' => $fieldName]);
        }
        else {
            $content = "<a href=\"$content\" target=\"_blank\">$content</a>";
        }

        return "<div class=\"doc-field-content\">{$content}</div>";
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

        if ($edit) {
            $inputField = $options['inputPath'] . '[properties_id]';
            $propertyType = $options['fieldConfig']['types'] ?? null;

            $options = [
                'caption' => false,
                'type' => 'reference',
                'url' => [
                    'controller' => 'Properties',
                    'action' => 'index',
                    $propertyType,
                    '?' => [
                        'manage' => $options['fieldConfig']['manage'] ?? false,
                        'append' => $options['fieldConfig']['append'] ?? false,
                        'empty' => $options['fieldConfig']['empty'] ?? false,
                        'template' => 'choose',
                        'show' => 'content',
                        'references' => false
                    ]
                ],
                'param' => 'find',
                //'paneId' => 'pane_' . Attributes::cleanIdentifier($inputField),
                'paneSnippet' => 'rows',
                'listValue' => 'id',
                //<- which attribute do the items carry? data-id (for trees) or data-value (everything else)
                'value' => $value,
                'text' => $content,
                'error' => $error,
                'rowType' => $propertyType
            ];

            return $this->Form->input($inputField, $options);
        }
        else {
            $fieldClasses = ['doc-field-content'];
            if ($error) {
                $fieldClasses[] = 'field-problem';
            }

            return $this->Element->outputHtmlElement(
                'div',
                $content,
                [
                    'class'=> $fieldClasses,
                    'data-row-field' => 'properties_id',
                    'data-row-value' => $value
                ]
            );
        }
    }

    /**
     * Output a property field that holds the section name options
     *
     * @param Item $item
     * @param array $fieldNameParts
     * @param boolean $edit
     * @param array $options
     * @return string
     */
    public function itemFieldSectionname($item, $fieldNameParts, $edit, $options=[])
    {
        if ($edit) {
            $inputField = $options['inputPath'] . '[properties_id]';

            $options = [
                'caption' => false,
                'type' => 'hidden',
                'value' => $item['properties_id']
            ];

            return $this->Form->input($inputField, $options);
        }
        else {
            return '';
        }
    }

    /**
     * Linked image caption in thumbnail grids
     *
     * @param $image
     *
     * @return mixed
     */
    public function itemImageCaption($image)
    {
        $caption = trim($image['name'] ?? $image['file_name']);
//        if ($image->file_properties['exists']) {
//            $out = $this->Html->link(
//                $caption,
//                $image->file_properties['url_display'] ?? '',
//                ['target'=>'_blank', 'title'=>$image->file_name],
//            );
//        }
//        else {
//            $out = $image->file_name;
//        }

        $out = isset($image['sortno']) ? __('{0} {1}', $image['sortno'], $caption) : $caption;

        return $out;
    }

    /**
     * Render a button that show the full item in a popup
     *
     * @param string $sectionId The section ID
     * @param string $itemId The item ID or {id} as a placeholder
     * @param boolean $edit Whether show an edit button or a more button
     * @return string
     */
    public function itemMoreButton($sectionId, $itemId, $edit=false)
    {
        // Pen or dots
        $icon = $edit ? "\u{f304}" : "\u{f141}";

        return '<div class="doc-field doc-field-more-button">'
            . '<button class="doc-item-more icon tiny" type="button"
                title="' . __('Show more fields') . '"
                aria-label="' . __('Show more fields') . '">'. $icon . '</button>'
            . '</div>';
    }

    /**
     * Close an item element
     *
     * @return string
     */
    public function itemEnd()
    {
        return '</div>';
    }


    /**
     * Output the annotations
     *
     * ### Options
     * - edit true|false
     * - mode
     * - lists Array with the items 'links' and 'footnotes', by default both are output
     * - wrap true|false Whether to wrap the output in a div
     * - toggle true|false Whether to output a toggle button
     *
     * @param RootEntity $entity
     * @param Entity[] $items
     * @param array $options
     * @return string
     */
    public function annoLists($entity, $items, $options)
    {
        if (($options['mode'] ?? false) === 'default') {
            unset($options['mode']);
        }
        $options['mode'] = $options['mode'] ?? (($options['edit'] ?? false) ? 'edit' : 'view');

        $lists = $options['lists'] ?? ['links', 'footnotes'];
        $empty = true;


        $out = '';

        // Toggle button
        if ($options['toggle'] ?? true) {
            $out .= $this->Element->outputHtmlElement(
                'button', "Toggle", // Toggle on
                [
                    'type' => 'button',
                    'class' => 'widget-switch widget-switch-icon button-links-toggle tiny',
                    'title' => __('Toggle annotations'),
                    'area-label' => __('Toggle annotations'),
                    'data-switch-element' => '.doc-section-links',
                    'data-switch-closest' => '1',
                    'data-switch-reverse' => '1',
                    'data-switch-class' => 'doc-section-links-hidden'
                ]
            );
        }

        // Link annotations
        if (in_array('links', $lists)) {
            $annos = $this->annoExtract('links', $entity, $items);
            $empty = $empty && (count($annos) == 0);
            $out .= $this->annoLinksList($entity, $annos, $options);
        }

        // Footnote annotations
        if (in_array('footnotes', $lists)) {
            $annos = $this->annoExtract('footnotes', $entity, $items);
            $empty = $empty && (count($annos) == 0);
            $out .= $this->annoFootnoteList($entity, $annos, $options);
        }

        if ($options['wrap'] ?? true) {
            $out = $this->Element->outputHtmlElement('div',
                $out,
                [
                    'class' => [
                        "doc-section-links",
                        $empty ? 'doc-section-links-empty' : null,
                        ($options['toggle'] ?? true) ? 'doc-section-links-hidden' : null
                    ]
                ]
            );
        }

        return $out;
    }

    /**
     * Output footnote annotations
     *
     * ### Options
     * - edit true|false
     * - mode
     *
     * @param RootEntity $entity
     * @param CollectionInterface $annos
     * @param array $options
     * @return string
     */
    public function annoFootnoteList($entity, $annos, $options)
    {
        $out = '';
        foreach ($annos as $anno) {
            $typeName = $anno->from_tagname;
            $tagRenderer = $entity->table->getDatabase()->types['footnotes'][$typeName]['merged']['fields']['name']['counter'] ?? 'numeric';

            $number = $entity->getCounter($anno->from_tagname)[$anno->from_tagid] ?? null;
            $problems = [];
            if ($number === null) {
                $problems[] = __('Tag number is missing.');
            }

            $number = $number ?? '*';
            $number = is_numeric($number) ? Numbers::numberToString($number, $tagRenderer) : $number;

            $out .= $this->annoTemplate(
                'footnotes',
                [
                    'scope' => 'footnotes',
                    'id' => $anno->id,
                    'type' => $anno->from_tagname,

                    'root_id' => $anno->root_id,
                    'root_tab' => $anno->root_tab,
                    'from_id' => $anno->from_id,
                    'from_tab' => $anno->from_tab,
                    'from_field' => $anno->from_field,
                    'from_tagid' => $anno->from_tagid,
                    'from_tagname' => $anno->from_tagname,

                    'name' => $number,
                    'problems' => $problems
                ],
                $entity,
                $options
            );
        }

        return $out;
    }

    /**
     * Output the annotations
     *
     * ### Options
     * - edit true|false
     * - mode
     *
     * @param RootEntity $entity
     * @param CollectionInterface $annos
     * @param array $options
     * @return string
     */
    public function annoLinksList($entity, $annos, $options)
    {
        $out = '';

        foreach ($annos as $anno) {
            $number = $entity->getCounter('tags')[$anno->from_tagid] ?? null;
            $problems = $anno->problems ?? [];
            if ($number === null) {
                $problems[] = __('Tag is missing.');
            }

            //$to_id = str_replace($link->to_tab.'-','', $link->to_id); // necessary?
            // TODO: reduce redundancy between divs and inputs
            $out .= $this->annoTemplate(
                'links',
                [
                    'scope' => 'links',
                    'idx' => $anno->id,
                    'id' => $anno->id,
                    'type' => $anno->from_tagname,
                    'root_id' => $anno->root_id,
                    'root_tab' => $anno->root_tab,
                    'from_id' => $anno->from_id,
                    'from_tab' => $anno->from_tab,
                    'from_field' => $anno->from_field,
                    'from_tagid' => $anno->from_tagid,
                    'from_tagname' => $anno->from_tagname,
                    'to_id' => $anno->to_id ?? '',
                    'to_tab' => $anno->to_tab ?? '',
                    'to_value' => $anno->to_value,
                    'deleted' => $anno->deleted,
                    'problems' => $problems
                ],
                $entity,
                $options
            );
        }

        return $out;
    }

    /**
     * Get annotation items (links and footnotes)
     *
     * @param string $scope footnotes or links
     * @param RootEntity $root
     * @param array $sources Filter links belonging to the given items
     *
     * @return \Cake\Collection\CollectionInterface
     */
    public function annoExtract($scope, $root, $sources = [])
    {
        $links = collection($sources)
            ->map(function ($source) use ($root, $scope) {

                if ($scope === 'links') {
                    return empty($source->id) ? [] : $root->getLinksFrom(
                        $source->table->getTable(),
                        $source->id
                    );
                }
                elseif ($scope === 'footnotes') {
                    return empty($source->id) ? [] : $root->getFootnotesFrom(
                        $source->table->getTable(),
                        $source->id
                    );
                }
            })
            ->reduce(
                function ($accumulated, $source) {
                    return array_merge($accumulated, $source);
                },
                []
            );

        $links = Arrays::orderAlong($links, 'from_tagid', $root->getCounter('tags'));

        return $links;
    }

    /**
     * Get script tag templates for both links and footnotes
     *
     * ## Options
     * - mode: view|add|edit|code
     *
     * @param array $data If not empty, placeholders in annoTemplate() will be replaced with the data
     * @param Entity $root The root entity
     * @param array $options
     *
     * @return string
     */
    public function annoTemplates($data, $root, $options)
    {
        $out = '<div class="templates">';
        $out .= '<script type="text/template" class="template template-annotation-links">';
        $out .= $this->annoTemplate('links', $data, $root, $options);
        $out .= '</script>';

        $out .= '<script type="text/template" class="template template-annotation-footnotes">';
        $out .= $this->annoTemplate('footnotes', $data, $root, $options);
        $out .= '</script>';
        $out .= '</div>';

        return $out;
    }

    /**
     * Get a template for the link div with unicorn styled placeholders (curly brackets)
     *
     * ## Options
     * - mode: view|add|edit|code
     *
     * @param string $scope 'links' or 'footnotes'
     * @param array $data If not empty, the placeholders will be replaced with the data
     * @param Entity $root The root entity
     * @param array $options Options for the template
     *
     * @return string
     */
    public function annoTemplate($scope, $data, $root, $options)
    {
        $edit = $options['edit'] ?? false;

        $data['root_tab'] = $data['root_tab'] ?? $root['table_name'] ?? null;
        $data['root_id'] = $data['root_id'] ?? $root['id'] ?? null;

        $action = Attributes::cleanOption($options['mode'], ['edit', 'add'], 'edit');
        $formId = 'form-' . $action . '-'
            . ($data['root_tab'] ?? '{rootTab}')
            . '-'
            . ($data['root_id'] ?? '{rootId}');

        // Open div
        $annoFields = ['from_id', 'from_tab', 'from_field', 'from_tagid', 'from_tagname'];
        if ($scope === 'links') {
            $annoFields[] = 'to_id';
            $annoFields[] = 'to_tab';
        }

        $labelName = $scope === 'links' ? 'to_value' : 'name';
        $labelValue = $data[$labelName] ?? ('{'. Inflector::variable($labelName) . '}');

        $linkClasses = ['doc-section-link'];
        if (!empty($data['problems'])) {
            $linkClasses[] = 'tag-problem';
        }

        $linkAttributes = [
            'class'=>$linkClasses,
            'data-row-table'=> $data['scope'] ?? '{scope}',
            'data-row-id'=>$data['id'] ?? '{id}',
            'data-row-type'=>$data['type'] ?? '{type}',
            'title' => $labelValue
        ];

        $linkAttributes = array_merge($linkAttributes, Attributes::toTemplateAttributes($annoFields, $data));

        $out = $this->Element->openHtmlElement('div', $linkAttributes);

        // Label
        $out .= '<span class="doc-section-link-text">' . $labelValue . '</span>';

        // Hidden inputs
        if (($scope === 'links') && $edit) {

            $out .= implode(' ', array_map(
                fn($x) => $this->Form->hidden(
                    'links[' . ($data['idx'] ?? '{idx}') . '][' . $x . ']',
                    [
                        'value' => ($data[$x] ?? ('{' . Inflector::variable($x) . '}')),
                        'data-row-field' => $x,
                        'form' => $formId
                    ]
                ),
                [
                    'id',
                    'deleted',
                    'root_id',
                    'root_tab',
                    'from_id',
                    'from_tab',
                    'from_field',
                    'from_tagid',
                    'from_tagname',
                    'to_id',
                    'to_tab'
                ]
            ));

        }

        // Close div
        $out .= $this->Element->closeHtmlElement('div');

        return $out;
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
        $out = $this->Element->openHtmlElement('div',[
            'class' => 'doc-article-footnotes widget-document-satellite ' . ($options['edit'] ? 'widget-document-edit' : 'widget-document-view'),
            'data-root-table' => $article->table->getTable(),
            'data-root-id' => $article->id
            ]
        );
        foreach ($article->footnoteTypes as $typeName => $typeConfig) {
            $out .= $this->footnoteSection($article, $typeName, $typeConfig, $options);
        }
        $out .= $this->Element->closeHtmlElement('div');

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
        $mode = $options['mode'] ?? ($edit ? 'edit' : 'view');

        $items = $article->getOrderedFootnotes($typeName);
        $uniqueSectionId = Attributes::uuid('footnotes-');

        $editFootnotes = $edit && ($typeConfig['merged']['edit'] ?? true);
        $divAttributes = [
            'class' => ['doc-section', 'doc-section-footnotes'],
            'data-row-type' => $typeName,
            'data-root-table' => $article->table->getTable(),
            'data-root-id' => $article->id
        ];

        if (empty($items)) {
            $divAttributes['class'][] = 'doc-section-empty';
        }
        $out .= $this->Element->openHtmlElement('div',$divAttributes);

        $out .= $this->Element->openHtmlElement(
            'div',
            [
                'class'=>['doc-section-head','widget-switch widget-switch-active'],
                'data-switch-element' => '#' . $uniqueSectionId,
                'data-switch-class' => 'toggle-hide',
                'data-switch-reverse' => '1'
            ]
        );
        $out .= $this->Element->outputHtmlElement(
            'div',
            $this->Types->getCaption('footnotes', $typeName, __('Footnotes')),
            ['class' => 'doc-section-name']
        );
        $out .= $this->sectionToggleButton();
        $out .= $this->Element->closeHtmlElement('div');


        $classes = ['doc-section-content', 'doc-section-content-' . $typeName];
        $classes = implode(" ", $classes);

        $out .= '<div id="' . $uniqueSectionId . '" class="' . $classes . '">';

        $out .= '<div class="doc-section-items">';
        $out .= '<div class="doc-footnotes">';
        foreach ($items as $key => $item) {
            $out .= $this->footnoteContent($item, $article, ['edit' => $editFootnotes], $typeConfig);
        }
        $out .= '</div>';
        $out .= '</div>';

        $out .= $this->annoLists($article, $items, ['edit' => $editFootnotes, 'mode' => $mode, 'lists' => ['links']]);

        $out .= '</div>';

        if ($editFootnotes) {
            $out .= '<script type="text/template" class="template template-footnote-' . $typeName . '">';
            $footnoteTemplate = new Footnote(
                    [
                        'idx' => '{idx}',
                        'id' => '{id}',
                        'deleted' => false,

                        'name' => '{name}',
                        'content' => '{content}',
                        'segment' => '{segment}', //TODO: scope?

                        'root_id' => '{rootId}', // $article->id
                        'root_tab' => '{rootTab}', // $article->table
                        'from_id' => '{fromId}',
                        'from_tab' => '{fromTab}',
                        'from_field' => '{fromField}',
                        'from_tagid' => '{fromTagid}',
                        'from_tagname' => $typeName
                    ],
                    [
                        'source' => 'Epi.footnotes',
                        'useSetters' => false,
                        'markClean' => true,
                        'markNew' => true
                    ]
                );
            //$footnoteTemplate->container = $section; //TODO: what's the container of a footnote?
            $footnoteTemplate->root = $article;

            $out .= $this->footnoteContent($footnoteTemplate, $article, ['edit' => $editFootnotes], $typeConfig);
            $out .= '</script>';
        }

        $out .= '</div>';

        return $out;
    }


    /**
     * Output a footnote
     *
     * @param false|Footnote $footnote The footnote entity or empty
     * @param Entity $root The root entity
     * @param array $options Keys: edit (true|false)
     * @param array $typeConfig The footnote's type configuration
     * @return mixed
     */
    public function footnoteContent($footnote, $root, $options=[], $typeConfig = [])
    {
        // TODO: Use the empty footnote's properties, don't construct a new data array
        if (!$footnote->isNew()) {

            // Get number
            $number = $root->getCounter($footnote->from_tagname)[$footnote->from_tagid] ?? null;
            $options['problems'] = [];
            if ($number === null) {
                $options['problems'][] = __('Tag number is missing.');
            }
            $number = $number ?? '*';
            $tagRenderer = $typeConfig['merged']['fields']['name']['counter'] ?? 'numeric';
            $number = is_numeric($number) ? Numbers::numberToString($number, $tagRenderer) : $number;

            // Populate data
            // @deprecated, directly use the properties below
            $data = [
                'idx' => $footnote->id,
                'id' => $footnote->id,
                'deleted' => $footnote->deleted,
                'type' => $footnote->from_tagname,

                'name' => $number,
                'content' => $footnote->getValueFormatted('content', $options),
                'segment' => $footnote->getValueFormatted('segment', $options), //TODO: scope?

                'root_id' => $footnote->root_id,
                'root_tab' => $footnote->root_tab,
                'from_id' => $footnote->from_id,
                'from_tab' => $footnote->from_tab,
                'from_field' => $footnote->from_field,
                'from_tagid' => $footnote->from_tagid,
                'from_tagname' => $footnote->from_tagname,

                'edit' => $options['edit'] ?? false,
                'problems' => $options['problems'] ?? []
            ];
        } else {
            $data = [];
        }

        $data['root_tab'] = $data['root_tab'] ?? $root['table_name'] ?? null;
        $data['root_id'] = $data['root_id'] ?? $root['id'] ?? null;

        return $data;
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

        $out = $this->Element->openHtmlElement('div', [
            'class' => 'doc-field doc-fieldname-' . $fieldname,
            'data-row-field' => $fieldname
        ]);

        $options['inputField'] = 'footnotes[' . ($footnote['idx'] ?? '{idx}') . '][' . $fieldname . ']';
        $fieldNameParts = explode('.', $fieldname);
        $out .= $this->itemFieldXml($footnote, $fieldNameParts, $options['edit'] ?? false, $options);

        $out .= $this->Element->closeHtmlElement('div');

        return $out;
    }
    /**
     * Render a button that removes a footnote from the footnote list
     *
     * @param string $footnoteId The footnote ID or {id} as a placeholder
     * @return string
     */
    public function footnoteRemoveButton($footnoteId, $formId)
    {
        return  $this->Form->hidden(
                'footnotes[' . $footnoteId . '][deleted]',
                ['value' => 0, 'data-row-field' => 'deleted', 'form' => $formId]
                )
            . '<button class="doc-item-remove tiny"
                title="' . __('Remove footnote') . '"
                aria-label="' . __('Remove footnote') . '">-</button>';
    }

    /**
     * Output section notes
     *
     * @param $article
     * @param array $options Array with the keys 'article', 'edit', 'mode' and 'template_article'.
     *
     * @return string
     */
    public function notesList($article, $options = [])
    {
        $out = $this->Element->openHtmlElement('div',[
                'class' => [
                    'doc-article-notes',
                    'widget-document-satellite',
                    ($options['edit'] ?? false) ? 'widget-document-edit' : null
                ],
                'data-root-table' => $article->table->getTable(),
                'data-root-id' => $article->id,
            ]
        );
        foreach ($article->sections as $section) {
            $out .= $this->notesItem($section, $options);
        }

        $out .= $this->Element->closeHtmlElement('div');
        return $out;
    }


    /**
     * Output a single section notes field
     *
     * @param $section
     * @param array $options Array with the keys 'edit', 'mode', 'article', 'template_article'
     *
     * @return string
     */
    public function notesItem($section, $options = [])
    {
        $formId = 'form-edit-articles-' . ($section['articles_id'] ?? '{articles-id}');

        if (empty($section)) {
            return '';
        }

        // For guest users, filter out comments and nonpublic sections
        if (($section->currentUserRole === 'guest') || !$section->getEntityIsVisible($options)) {
            return '';
        }

        $sectionId = ($section['id'] ?? '{sections-id}');

        // Update options
        //TODO: remove unnecessary stuff
        $options['article'] = $section->container;
        $options['section'] = $section;

        // Get template
        $options['mode'] = $options['mode'] ?? 'view';
        $mode = $options['mode'];

        $sectionConfig = $section->type['merged'] ?? [];
        $sectionConfig['view'] = Arrays::stringToArray($sectionConfig['view'] ?? 'stack', 'name', 'stack');
        $options['template_section'] = $sectionConfig;

        $empty = $section->getValueIsEmpty('comment');

        // Comment
        $divAttributes = [
            'class' => [
                'doc-section-note',
                $empty ? 'doc-section-note-empty' : null
            ],
            'data-section-id' => 'sections-' . $sectionId,
            'data-row-table' => 'sections',
            'data-row-id' => $sectionId,
            'data-row-type' => $section->sectiontype
        ];
        $out =  $this->Element->openHtmlElement('div', $divAttributes);

        $out .= '<div class="doc-section-items">';
        $itemOptions = ['edit' => $options['edit'], 'form'=> $formId];
        $itemOptions['caption'] = $section->captionPath;

        $out .= $this->itemField($options['section'], 'comment', $itemOptions);
        $out .= '</div>';

        $out .= $this->annoLists($options['article'], [$options['section']], $options);

        $out .= $this->Element->closeHtmlElement('div');


        return $out;
    }

    /**
     * Output references to the document
     *
     * @param RootEntity $entity
     * @param array $options
     *
     * @return string
     */
    public function referencesList($entity, $options = [])
    {
        $out = $this->Table->filterTable(
            'references',
            $entity->toReferences,
            [
                'columns' => [
                    'caption' => ['caption' => __('Caption'), 'default' => true],
                    'norm_iri' => ['caption' => __('IRI fragment'), 'default' => true],
                    'modified' => ['caption' => __('Modified'), 'default' => true]
                ],
                'actions' => ['view' => true],
                'more' => [
                    'controller' => 'Articles',
                    'action' => 'index',
                    '?' => ['references' => $entity->id]
                ]
            ]);

        return $out;
    }

    /**
     * Assemble the data attributes for an entity
     *
     * @param Entity $entity
     * @return array
     */
    public function entityDataAttributes($entity)
    {
        $data = [
            'data-row-table' => $entity->table_name ?? '',
            'data-row-id' => $entity->id ?? $entity->newId,
            'data-row-type' => $entity->type->name ?? $entity->scope ?? '',
            'data-row-model' => str_replace('.','_', strtolower($entity->getSource() ?? '')),
            'data-root-table' => $entity->table_name ?? '',
            'data-root-id' => $entity->id ?? $entity->newId,
            'data-file-basepath' => trim($entity->file_basepath ?? '', '/'),
            'data-file-defaultpath' => trim($entity->file_defaultpath ?? '', '/')
        ];

        if (!empty($entity->databaseName)) {
            $data['data-database'] = Databank::removePrefix($entity->databaseName);
        }

        if (!empty($entity->deleted)) {
            $data['data-deleted'] = $entity->deleted;
        }

        if (!empty($entity->merged_ids)) {
            $data['data-merged-ids'] = $entity->merged_ids;
        }

        if (!empty($entity->moved)) {
            $data['data-moved'] = $entity->moved;
        }

        return $data;
    }


    /**
     * Output a delete form
     *
     * @param RootEntity $entity
     * @return string
     */
    public function deleteForm($entity)
    {
        if ($entity instanceof \Epi\Model\Entity\BaseEntity) {
            $entity->prepareRoot();
        }

        $divAttributes = ['class' => 'widget-entity'];
        $out = $this->Element->openHtmlElement('div',$divAttributes);

        $mode = 'delete';
        $options['id'] = trim('form-' . $mode . '-' . $entity->table_name . '-' . $entity->id,'-');
        if (!$entity->deleted) {
            $options['data-cancel-url'] = $this->Url->build(['action' => 'view', $entity->id]);
            $options['type'] = 'delete';
        }

        $out .= $this->Form->create($entity, $options);
        if ($entity->deleted) {
            $out .= $this->Form->hidden('deleted');
        }
        $out .= $this->Form->end();

        $out .= $this->Element->closeHtmlElement('div');

        return $out;
    }

    /**
     * Output a vertical table with inputs
     *
     * // TODO: Can we output all the forms with $doc = true?
     * // TODO: Use entityForm() for article rendering
     *
     * @param RootEntity $entity
     * @param string $action view|edit|add|delete|merge
     * @param array $options Options passed to the form helper
     * @param boolean $doc Wrap the table in document markup. Set to true if you need annotations.
     * @return string
     */
    public function entityForm($entity, $action = 'edit', $options = [], $doc = false)
    {
        if ($entity instanceof \Epi\Model\Entity\BaseEntity) {
            $entity->prepareRoot();
        }

        $edit = in_array($action, ['edit', 'add', 'delete', 'move']);
        $confirmDelete = ($action === 'delete') && !$entity->deleted;
        $divAttributes = ['class' => 'widget-entity'];

        // For document markup, add classes and data attributes on the widget-entity level
        // Otherwise data attributes are set in entityTable()
        if ($doc) {
            $divAttributes['class'] .= ' widget-document' . ($edit ? ' widget-document-edit' : '');
            $divAttributes['data-edit-mode'] = $edit;
            $divAttributes = array_merge($divAttributes, $this->entityDataAttributes($entity));
        } elseif ($confirmDelete) {
            $divAttributes = array_merge($divAttributes, $this->entityDataAttributes($entity));
        }

        $out = $this->Element->openHtmlElement('div',$divAttributes);

        // For document markup, wrappers grouping the annotations are necessary
        if ($doc) {
            $out .= '<div class="doc-section">';
            $out .= '<div class="doc-section-content">';
            $out .= '<div class="doc-section-items">';
        }

        if (in_array($action, ['edit', 'add', 'delete', 'move'])) {
            // Note: new entities (add mode) have no id yet
            $entityId = $entity->id ?? $entity->newId;
            $options['id'] = trim('form-' . $action . '-' . $entity->table_name . '-' . $entityId,'-');
            $options['autocomplete'] = 'off';

            if (!$entity->isNew() && !$entity->deleted) {
                $options['data-cancel-url'] = $this->Url->build(['action' => 'view', $entityId]);
                $options['data-delete-url'] = $this->Url->build(['action' => 'delete', $entityId]);
            }

            if (($action === 'delete') && !$entity->deleted) {
                $options['type'] = 'delete';
                if (!$this->_View->getRequest()->is('ajax')) {

                    $proceedUrl = ['action' => 'index'];
                    if (!empty($entity->fieldsScope)) {
                        $proceedUrl[] = $entity[$entity->fieldsScope] ?? null;
                    }
                    $options['data-proceed-url'] = $this->Url->build($proceedUrl);
                }
            }

            $out .= $this->Form->create($entity, $options);
            if ($doc && ($action === 'add')) {
                $out .= $this->Form->hidden('id',['value' => $entityId]);
            }

            if (!empty($entity->lockid)) {
                $out .= $this->Form->hidden(
                    'lock',
                    [
                        'value' => $entity->lockid ?? '',
                        'data-lock-url' => $this->lockUrl($entity),
                        'data-unlock-url' => $this->unlockUrl($entity)
                    ]
                );
            }

            if ($confirmDelete) {
                $out .= $this->entityDeleteConfirmation($entity, $options);
            } else {
                $out .= $this->entityTable($entity, $action, !$doc);
            }
            $out .= $this->Form->end();
        }
        else {
            $out .= $this->entityTable($entity, $action, !$doc);
        }

        if ($doc) {
            $out .= '</div>';
            $out .= $this->annoLists($entity, [$entity], ['edit' => $edit, 'mode' => $action]);
            $out .= '</div>';
            $out .= '</div>';

            if ($edit) {
                $out .= $this->annoTemplates([], $entity, ['edit' => $edit, 'mode' => $action]);
            }
        }

        $out .= $this->Element->closeHtmlElement('div');

        return $out;
    }

    /**
     * Get a URL to lock the entity
     *
     * @param LockTrait $entity
     * @return string URL
     */
    public function lockUrl($entity)
    {
        return Router::url(['action' => 'lock', $entity->id,'_ext' => 'json']);
    }


    /**
     * Get a URL to unlock the entity
     *
     * @param LockTrait $entity
     * @return string URL
     */
    public function unlockUrl($entity)
    {
        return Router::url(['action' => 'unlock', $entity->id,'_ext' => 'json']);
    }

    public function entityDeleteConfirmation($entity, $options)
    {
        $out = '';
        $out .= '<fieldset><p class="delete">';
        $out .=  $options['confirm'] ?? __('Are you sure to delete the entity?');
        $out .= '</p><div class="confirm">';
        $out .= $this->Link->getConfirmDeleteButtons($entity);
        $out .= '</div></fieldset>';
        return ($out);
    }

    /**
     * Output multiple table rows
     *
     * The entity needs a htmlFields property with an array of fields.
     * The keys are field names, the values are passed as options to $this->Form->control.
     *
     * In addition, the following optional options are supported:
     * - caption Label
     * - action An array of modes. If it contains "edit" or "view" the row is only rendered when it matches the $mode parameter.
     * - cellClass A class to add to the td element
     * - extract A Hash class compatible string used to extract the value
     * - format url, filesize, time, json
     * - baseUrl (in combination with format=>"url")
     * - help A help text displayed below the input field
     *
     * @param BaseEntity $entity
     * @param string $action view|edit|add|delete|merge
     * @param boolean $wrap Wrap the table in a widget-entity div and add entity data attributes to the table
     * @return string|null
     */
    public function entityTable($entity, $action = 'edit', $wrap=false)
    {
        $classes = ['vertical-table'];
        $tableAttributes = [
            'class' => $classes
        ];
        if ($wrap) {
            $tableAttributes = array_merge(
              $tableAttributes,
              $this->entityDataAttributes($entity)
            );
        }
        $out = $this->Element->openHtmlElement('table', $tableAttributes);
        if (!$entity->deleted) {
            foreach ($entity->htmlFields ?? [] as $fieldName => $fieldOptions) {
                // Check action parameter
                $modes = !isset($fieldOptions['action']) ?
                    [$action] :
                    (
                    is_string($fieldOptions['action']) ?
                        [$fieldOptions['action']] :
                        $fieldOptions['action']
                    );

                if (!in_array($action, $modes)) {
                    continue;
                }

                $out .= $this->entityRow($entity, $fieldName, $fieldOptions, $action);
            }

        }
        $out .= $this->Element->closeHtmlElement('table');

        return $out;
    }

    /**
     * Output a table row
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param array $fieldOptions
     * @param string $action view|edit|add|delete|merge
     *
     * @return string
     */
    public function entityRow($entity, $fieldName, $fieldOptions, $action = 'edit')
    {
        $caption = $fieldOptions['caption'] ?? $fieldName;

        $type = $fieldOptions['type'] ?? 'text';

        $rowClasses = [];
        $rowClasses[] = in_array($action, ['edit', 'add', 'move']) ? ('table-input-' . $type) : '';
        $rowClasses = array_filter($rowClasses);

        $cellClass = empty($fieldOptions['cellClass']) ? '' : (' class="' . $fieldOptions['cellClass'] . '"');

        $edit = in_array($action, ['edit', 'add', 'move']);

        $fieldLayout = $fieldOptions['layout'] ?? 'row';
        unset($fieldOptions['layout']);
        $fieldContent = $this->entityField($entity, $fieldName, $fieldOptions, $action);

        $out = '';
        if ($edit || !empty($fieldContent)) {
            $out = '<tr class="' . implode(' ', $rowClasses) . '" data-row-field="' . $fieldName . '">';

            // Use the whole table width
            if ($fieldLayout === 'stacked') {

                $out .= '<td colspan="2"' . $cellClass . '>';
                if (!empty($caption)) {
                    $out .= '<h2>' . $caption . '</h2>';
                }
                $out .= $fieldContent;
                $out .= '</td>';
            }

            // Caption left, content right
            else {
                $out .= '<th scope="row">' . $caption . '</th>';

                $out .= '<td' . $cellClass . '>';
                $out .= $fieldContent;
                $out .= '</td>';
            }

            $out .= '</tr>';
        }
        return $out;
    }

    /**
     * Output an input / view field inside of tables or sections
     *
     * The formatting depends on:
     *
     * - $fieldOptions is passed to the Form helper which creates the inputs in edit/add mode
     *
     * - $fieldOptions['format'] comes from the _getHtmlFields() method of the entity.
     *   It determines formatting in view mode. The following options are implemented:
     *   json, url, image, filesize, time, typed
     *
     * - $fieldFormat is determined be the getFieldFormat() method of the entity
     *
     * - $fieldOptions['type'] comes from the _getHtmlFields() method of the entity
     *   and determines whether values for checkboxes and selects are created in view mode
     *
     * - $fieldOptions['options'] contains select options in edit/add and view mode
     *
     * @param BaseEntity $entity
     * @param array|string $fieldName
     * @param array $fieldOptions
     * @param string $action view|edit|add|delete|merge
     *
     * @return string
     */
    public function entityField($entity, $fieldName, $fieldOptions = [], $action = 'view')
    {
        $inputFormat = $fieldOptions['format'] ?? null;
        $fieldFormat = is_object($entity) ? $entity->getFieldFormat($fieldName) : 'raw';
        $inputType = $fieldOptions['type'] ?? 'text';

        $edit = $fieldOptions['edit'] ?? in_array($action, ['edit', 'add', 'move']);
        $services = $fieldOptions['services'] ?? [];
        unset($fieldOptions['services']);

        // Edit fields
        if ($edit) {
            // Restrict to accessible fields
            $fieldNameParts = explode('.',$fieldName);
            $enabled = $entity->isAccessible($fieldNameParts[0]);

            // Map widget
            if ($inputFormat === 'widget') {
                $value = $entity->getValueRaw($fieldName);
                $out = $this->entityWidgets($entity, $fieldName, $edit, $fieldOptions, $value);
            }

            // XML fields
            elseif ($fieldFormat === 'xml') {
                $out = $this->itemField($entity, $fieldName, ['edit' => true, 'mode' => $enabled ? $action : 'view']);
            }

            // Everything else
            else {
                $stripOptions = ['caption', 'action', 'cellClass', 'extract', 'help', 'format', 'baseUrl', 'autofill'];
                $inputOptions = array_diff_key($fieldOptions, array_combine($stripOptions, $stripOptions));
                $inputOptions['label'] = false;

                // Paths for file/folder choosers
                if (in_array($inputOptions['itemtype'] ?? '', ['file','folder'])) {
                    $inputOptions['options']['?']['basepath'] = rtrim($entity->file_basepath, '/');
                    $inputOptions['options']['?']['path'] = rtrim($entity->file_downloadpath, '/');
                }

                // Autofill options
                $autofill = $fieldOptions['autofill'] ?? [];
                $autofill = !empty($autofill) && (!empty($autofill['force']) || $entity->getValueIsEmpty($fieldName)) ? $autofill : false;

                if (!empty($autofill)) {
                    $inputOptions['data-autofill'] = '1';
                    if (!empty($autofill['force'])) {
                        $inputOptions['readonly'] = true;
                    }
                }

                // Read-only
                if (!$enabled) {
                    $inputOptions['readonly'] = true;
                }

                // Nested values
                if (count($fieldNameParts) > 1) {
                    $inputOptions['value'] = $entity->getValueRaw($fieldName);
                }

                $out = $this->Form->control($fieldName, $inputOptions);
            }

            // Help
            if (isset($fieldOptions['help'])) {
                $out .= '<p class="table-help">' . $fieldOptions['help'] . '</p>';
            }

            // Add service buttons
            $out .= $this->entityFieldServiceButton($entity, $services);

        }

        // View fields
        else {
            // Extract value
            if (isset($fieldOptions['extract'])) {
                // TODO: is it still necessary to support multiple keys here?

                $keys = $fieldOptions['extract'];
                $keys = is_array($keys) ? $keys : [$keys];
                $value = [];

                foreach ($keys as $key) {

                    $keyValue = $entity instanceof BaseEntity ?
                        $entity->getValueNested($key, ['aggregate' => false]) :
                        Objects::extract($entity, $key, false);

                    if (is_array($keyValue)) {
                        $value = array_merge($value, $keyValue);
                    } else {
                        $value[] = $keyValue;
                    }
                }
            } else {
                $value = $entity->getValueRaw($fieldName);
            }

            // Links
            if (($value === '') || is_null($value)) {
                $out = '';
            }
            elseif ($inputFormat === 'url') {
                $out = $this->entityFieldUrl($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            // Images
            elseif (($inputFormat === 'image') && $entity->file_exists) {
                $out = $this->entityFieldImage($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            // Norm data
            elseif ($inputFormat === 'normdata') {
                $out = $this->entityFieldNormdata($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            // IRI link
            elseif ($inputFormat === 'iri') {
                $out = $this->entityFieldIri($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            // Filesize, time, select options, checkbox options
            elseif ($inputFormat === 'filesize') {
                $out = $this->entityFieldFilesize($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            // Time
            elseif ($inputFormat === 'time') {
                $out = $this->entityFieldTime($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            // Select
            elseif ($inputType === 'select') {
                $out = $this->entityFieldSelect($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            // Checkbox
            elseif ($inputType === 'checkbox') {
                $out = $this->entityFieldCheckbox($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            // JSON
            elseif ($inputFormat === 'json') {
                $out = $this->entityFieldJson($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            //Passwords
            elseif ($fieldFormat === 'token') {
                $out = $this->entityFieldToken($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            // Map widget
            elseif ($inputFormat === 'widget') {
                $out = $this->entityWidgets($entity, $fieldName, $edit, $fieldOptions, $value);
            }
            // XML fields
            elseif ($fieldFormat === 'xml') {
                $out = $this->itemField($entity, $fieldName, ['edit' => false]);
            }
            // Get from types
            elseif ($inputFormat === 'typed') {
                $out = $this->itemField($entity, $fieldName, ['edit' => false]);
            }
            // Everything else
            else {
                $value = is_array($value) ? implode(', ', $value) : $value;
                $out = ($fieldOptions['escape'] ?? true) ? h($value) : $value;
            }
        }

        return $out;
    }


    /**
     * A URL field for entities
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @return string
     */
    public function entityFieldUrl($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $value = array_map(function ($x) use ($fieldOptions) {
            $url = $fieldOptions['targetUrl'] ?? false;
            if (empty($url)) {
                $url = ($fieldOptions['baseUrl'] ?? '') . $x;
            }

            return $this->Link->authLink(h($x), $url, ['escape' => false]);
        }, $value);

        return implode("<br>", $value);
    }

    /**
     * A image field for entities
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @return string
     */
    public function entityFieldImage($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        $out = '<div class="doc-image-frame">';
        $out .= $this->Files->outputThumb(
            $entity->file_downloadname,
            $entity->file_downloadpath,
            Databank::removePrefix($entity->table->getDatabaseName())
        );
        $out .= '</div>';
        return $out;
    }

    /**
     * A norm data field for entities
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @return string
     */
    public function entityFieldNormdata($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        $out = '';
        if (!empty($entity->norm_data)) {
            $normdata = [];
            foreach ($entity->normDataParsed as $item) {
                if (empty($item)) {
                    continue;
                }
                $normdata[] = is_array($item) ?
                    $this->Html->link($item['value'] ?? '', $item['url'] ?? '', ['target' => '_blank']) :
                    $item;
            }
            $out = implode('<br>', $normdata);
        }
        return $out;
    }

    /**
     * A IRI field for entities
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @return string
     */
    public function entityFieldIri($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        $out = '';
        if (!empty($entity->iri_path)) {
            if (Databank::removePrefix($entity->databaseName) === DATABASE_PUBLIC) {
                $out .= $this->Html->link($entity->iri_path, 'iri/' . $entity->iri_path);
            }
            else {
                $out .= $entity->iri_path;
            }
        }
        return $out;
    }

    /**
     * A filesize field for entities
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @return string
     */
    public function entityFieldFilesize($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        return Files::formatFileSize($value);
    }

    /**
     * A time field for entities
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @return string
     */
    public function entityFieldTime($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        return $value->i18nFormat(null, 'Europe/Paris');
    }

    /**
     * A select field for entities
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @return string
     */
    public function entityFieldSelect($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        $options = $fieldOptions['options'];
        $options = $options instanceof QueryInterface ? $options->toArray() : $options;
        return $options[$value] ?? $value;
    }

    /**
     * A checkbox field for entities
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @return string
     */
    public function entityFieldCheckbox($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        $options = $fieldOptions['options'] ?? [];
        $options = array_merge([false => __('No'), true => __('Yes')], $options);
        return $options[!empty($value)];
    }

    /**
     * A JSON field for entities
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @return string
     */
    public function entityFieldJson($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        if (is_string($value)) {
            try {
                $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
                $value = ['' => $value];
            }
        }

        if (!empty($value)) {
            return $this->Table->nestedTable($value);
        } else {
            return '';
        }
    }

    /**
     * A token field for entities
     *
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @return string
     */
    public function entityFieldToken($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        $fieldId = Attributes::uuid('field-');

        $out = $this->Element->outputHtmlElement(
            'button',
            '',
            [
                'class' => 'widget-switch widget-switch-icon widget-switch-token',
                'data-switch-element' => '#' . $fieldId,
                'data-switch-content' => $value
            ]
        );

        $out .= $this->Element->outputHtmlElement(
            'span',
            str_repeat("*", 20),
            [
                'id' => $fieldId,
                'class' => 'field-token'
            ]
        );

        return $out;
    }


    public function entityFieldServiceButton($entity, $services) {
        $out = '';

        foreach ($services ?? [] as $serviceName => $serviceOptions) {
            // TODO: Think about the naming scheme, widget-service-button would be better but is already taken
            $out .= $this->Element->openHtmlElement(
                'div',
                [
                    'class' => 'widget-reconcile-button',
                    'data-service-name' => $serviceName,
                    'data-service-data' => json_encode($serviceOptions)
                ]
            );

            $out .= $this->Html->link(
                $serviceOptions['caption'] ?? $serviceName,
                '#',
                [
                    'class' => 'button tiny widget-shortcut',
                    'data-shortcuts' => 'Alt+R',
                    'area-keyshortcuts' => 'Alt+R'
                ]
            );

            $out .= $this->Element->closeHtmlElement('div');
        }

        if ($out !== '') {
            $out = $this->Element->outputHtmlElement('div', $out, ['class' => 'field-buttons']);
        }
        return $out;
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
     * @param BaseEntity $entity
     * @param string $fieldName
     * @param boolean $edit
     * @param array $fieldOptions
     * @param mixed $value
     * @param array $options
     * @return string
     */
    public function entityWidgets($entity, $fieldName, $edit, $fieldOptions, $value)
    {
        $out = '';
        if (!empty($fieldOptions['widgets']['map'] ?? false)) {

            // Skip empty map in view mode
            if (!$edit) {
                try {
                    $value = json_decode($entity->getValueRaw($fieldName), true);
                } catch (Exception $e) {
                    $value = [];
                }

                if ((($value['lat'] ?? '') === '') || (($value['lng'] ?? '') === '')) {
                    return $out;
                }
            }

            $options = [
                'entities' => [$entity],
                'rowTable' => $entity->tableName,
                'rowType' => $entity->type->name,
                'searchText' => $entity->captionPath,
                'fieldName' => $fieldName,
                'sortno' => false,
                'edit' => $edit
            ];
            $out .= $this->getView()->element('../Properties/widget_map', $options);
        }
//        if (!empty($options['template_section']['view']['widgets']['thumbs'] ?? false)) {
//            $out .= $this->getView()->element('../Sections/widget_thumbs', $options);
//        }
//        if (!empty($options['template_section']['view']['widgets']['grid'] ?? false)) {
//            $out .= $this->getView()->element('../Sections/widget_grid', $options);
//        }
        return $out;
    }


    public function taskContent($task, $options = [])
    {
        // Init values
        $task['caption'] = $task['caption'] ?? $options['caption'] ?? $task['type'] ?? '';

        //Section start
        $out = $this->taskStart($task, $options);

        // Section content
        $element = "../Tasks/task_" . $task['type'];
        if (!$this->getView()->elementExists($element)) {
            $element = "../Tasks/task";
        }
        $out .= $this->getView()->element($element, ['task' => $task,'options'=>$options]);

        // Section end
        $out .= $this->taskEnd($task, $options);

        return $out;
    }

    public function taskStart($task, $options=[])
    {
        $sectionName = $task['caption'] ?? '';
        $sectionNumber = $task['number'] ?? 1;

        $out = $this->Element->openHtmlElement(
            'div',
            [
                'id' => 'sections-' . $sectionNumber,
                'class' => [
                    'doc-section',
                    'widget-scrollsync-section'
                ],
                'data-row-id' =>  $sectionNumber,
                'data-row-type' =>  $task['type'],
                'data-row-config' => json_encode([
                    'config' => ['name' => ['number'=>true,'postfix' => ' ' . $sectionName]]
                ])
            ]
        );

        $out .= '<div class="doc-section-head">';

        $out .= '<div class="doc-section-name" data-row-name="' . $sectionNumber . '">';

        if ($options['edit'] ?? false) {
            $out .= $this->Form->hidden(
                'Tasks.' . $sectionNumber . '.number',
                ['value' => $sectionNumber, 'data-row-field' => 'sortno'] // Map sortno (set in models.js) to number
            );
            $out .= $this->Form->hidden(
                'Tasks.' . $sectionNumber . '.type',
                ['value' => $task['type']]
            );
        }

        $out .= '<span data-value="name">' . $sectionNumber . ' ' . $sectionName . '</span>';

        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="element_content">';
        $out .= '<table class="input-set"><tbody>';

        if ($options['edit'] ?? false) {

             // TODO: move into header but make sure renumbering (in models.js) does not interfere
            if ($options['customcaption'] ?? true) {
                $out .= '<tr>';
                $out .= '<td>' . __('Task caption') . '</td>';
                $out .= '<td>';
                $out .= $this->Form->input('Tasks.' . $sectionNumber . '.caption', [
                    'type' => 'text',
                    'value' => $task['caption'] ?? ''
                ]);
                $out .= '</td></tr>';
            }

            if ($options['canskip'] ?? true) {
                $out .= '<tr><td></td><td>';
                $out .= $this->Form->control('Tasks.' . $sectionNumber . '.canskip', [
                    'type' => 'checkbox',
                    'label' => __('Task can be skipped by user'),
                    'checked' => !empty($task['canskip'] ?? false)
                ]);
                $out .= '</td></tr>';
            }


            if ($options['inputfile'] ?? true) {
                $out .= '<tr>';
                $out .= '<td>' . __('Input file') . '</td>';
                $out .= '<td>';
                $out .= $this->Form->input('Tasks.' . $sectionNumber . '.inputfile', [
                    'type' => 'text',
                    'value' => $task['inputfile'] ?? '',
                    'placeholder' => __('Leave empty to use default')
                ]);
                $out .= '</td></tr>';
            }
        } else {
            if (!empty($task['canskip'] ?? false)) {
                $out .=  '<tr><td colspan="2">' . __('Task can be skipped by user') . '</td></tr>';
            }
            if (!empty($task['inputfile'] ?? false)) {
                $out .=  '<tr><td>' . __('Input file') . '</td><td>' . $task['inputfile']. '</td></tr>';
            } elseif (!empty($options['inputfile'])) {
                $out .=  '<tr><td>' . __('Input file') . '</td><td><i>default</i></td></tr>';
            }
        }

        return $out;
    }

    public function taskTable($task, $fields = [], $options = [])
    {
        $out = '';

        foreach ($fields as $fieldKey => $fieldConfig) {
            $out .= '<tr>';

            if (($fieldConfig['type'] ?? '') === 'radio') {
                $out .= '<td>' . ($fieldConfig['label'] ?? '') . '</td>';

                $out .= '<td>';
                if ($options['edit'] ?? true) {
                    $out .= '<div class="radio-group">';
                    $out .=  $this->Form->radio(
                        'Tasks.' . $task['number'] . '.' . $fieldKey,
                        $fieldConfig['options'] ?? [],
                        ['value' => $task[$fieldKey] ?? $fieldConfig['default']  ?? '']
                        );
                    $out .= '</div>';

                } else {
                    $out .= $fieldConfig['options'][$task[$fieldKey] ?? $fieldConfig['default'] ?? ''] ?? '';
                }

                $out .= '</td>';

            }
            elseif (($fieldConfig['type'] ?? '') === 'checkbox') {

                if ($options['edit'] ?? true) {
                    $out .= '<td colspan="2">';
                    $out .= $this->Form->control(
                        'Tasks.' . $task['number'] . '.' . $fieldKey,
                        $fieldConfig
                    );
                    $out .= '</td>';
                }
                else {
                    $out .= '<td>' . ($fieldConfig['label'] ?? '') . '</td>';
                    $out .= '<td>' . (empty($task[$fieldKey]) ? '0' : '1') . '</td>';
                }

            }

            else {
                $out .= '<td>' . ($fieldConfig['label'] ?? '') . '</td>';
                $fieldConfig['label'] = false;

                $out .= '<td>';
                if ($options['edit'] ?? true) {
                    $out .= $this->Form->control(
                        'Tasks.' . $task['number'] . '.' . $fieldKey,
                        $fieldConfig
                    );

                    if (!empty($fieldConfig['help'])) {
                        $out .= '<i>' . $fieldConfig['help'] . '</i>';
                    }
                } else {
                    $value =  $task[$fieldKey] ?? '';
                    if (!empty($fieldConfig['escape'])) {
                        $value = h($value);
                    }
                    $out .=  $value;
                }
                $out .= '</td>';
            }
        }
        $out .= '</tr>';
        return $out;
    }

    public function taskEnd($task, $options=[])
    {
        $sectionNumber = $task['number'] ?? 1;
        $out = '';

        if ($options['outputfile'] ?? true) {
            $out .= '<tr>';
            $out .= '<td>' . __('Outputfile') . '</td>';
            if ($options['edit'] ?? false) {
                $out .= '<td>';
                $out .= $this->Form->input(
                    'Tasks.' . $sectionNumber . '.outputfile',
                    [
                        'type' => 'text',
                        'value' => $task['outputfile'] ?? '',
                        'placeholder' => __('Leave empty to use the default file name')
                    ]);
                $out .= '</td>';
            }
            else {
                $out .= '<td>' . (empty($task['outputfile']) ? '<i>default</i>' : $task['outputfile']) . '</td>';
            }
            $out .= '</tr>';
        }

        $out .= '</tbody></table>';
        $out .= '</div>';

        $out .= '</div>';
        return $out;
    }

    public function searchForm()
    {
        $action = ['action' => 'index'];
        $term = $this->_View->getConfig('options')['params']['term'] ?? null;

        $out =  $this->Form->create(null, ['type'=>'get','id' => 'search','url' => $action]);
        $out .= $this->Form->hidden(
            'database',
            ['value' => $this->_View->getRequest()->getQuery('database')]
        );
        $out .= $this->Form->control(
            'term',
            ['type' => 'text', 'label' => false, 'placeholder' => __('Search'), 'value' => $term]
        );
        $out .= $this->Form->end();
        return $out;


    }

}

