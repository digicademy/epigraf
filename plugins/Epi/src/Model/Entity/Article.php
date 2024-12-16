<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Entity;

use App\Model\Behavior\TreeCorruptException;
use App\Model\Entity\Databank;
use App\Utilities\Converters\Attributes;
use App\Utilities\Converters\Objects;
use App\Utilities\Files\Files;
use Cake\ORM\Query;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Epi\Model\Behavior\PositionBehavior;
use Epi\Model\Table\ArticlesTable;
use App\Utilities\Converters\Arrays;

/**
 *Article Entity
 *
 * # Database fields (without inherited fields)
 * @property string $articletype
 * @property string $articlenumber
 * @property string $title
 * @property string $status
 * @property string $norm_data
 * @property string $norm_iri
 * @property string $norm_type
 * @property int $lastopen_id
 * @property string $lastopen_tab
 * @property string $lastopen_field
 * @property string $lastopen_tagid
 *
 * # Virtual fields (without inherited fields)
 * @property string|null $caption
 * @property string|null $captionPath
 * @property string|null $captionExt
 *
 * @property array $tree
 * @property bool $hasToReferences
 * @property mixed $toReferences
 * @property string $problems
 * @property string $missingTypes
 * @property int $amountOfAnnotations
 * @property float|int $amountOfText
 * @property string $contentState
 * @property bool|int|null $publishedState
 *
 * @property string $internalUrl
 * @property mixed|string $externalUrl
 * @property mixed $url
 *
 * @property string $preview
 * @property array $summary
 *
 * @property mixed $itemsBySectionsId
 * @property mixed $itemsByType
 *
 * @property array $htmlFields
 *
 * # Relations
 * @property ArticlesTable $table
 * @property Epi\Model\Entity\Section[] $sections
 * @property array $items
 * @property \Epi\Model\Entity\Project $project
 */
class Article extends RootEntity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'published' => true,
        'projects_id' => true,
        'articletype' => true,
        FIELD_ARTICLES_SIGNATURE => true,
        'sortno' => true,
        'name' => true,
        'status' => true,
        'norm_data' => true,
        'norm_iri' => true,
        'project' => true,
        'sections' => true,
        'links' => true,
        'footnotes' => true
    ];

    /**
     * Expose database fields (export in array)
     * @var string[]
     */
    protected $_virtual = ['database'];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'id',
        'database',
        'sortno',
        'norm_iri',
        'norm_data',
        'created',
        'modified',
        'articletype',
        FIELD_ARTICLES_SIGNATURE,
        'name',
        'project',
        'sections',
        'footnotes',
        'links',
        'search'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'database',
        'sortno',
        'norm_iri',
        'articletype',
        'published'
    ];

    // TODO: only export editor fields (modifier, creator, status) if requested
    /**
     * Snippets for export
     * Each snippet contains a list of fields that is added to $_serialize_fields.
     *
     * @var array[]
     */
    protected $_serialize_snippets = [
        'published' => ['published'],
        'comments' => ['status'],
        'problems' => ['problems'],
        'editors' => ['creator', 'modifier', 'created', 'modified']
    ];

    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     *
     * @var string[]
     */
    public static $_fields_ids = [
        'id',
        'projects_id' => 'projects',
        'created_by' => 'users',
        'modified_by' => 'users'
    ];


    protected $_fields_formats = [
        'id' => 'id',
        'published' => 'published',
        'created_by' => 'id',
        'modified_by' => 'id',
        'projects_id' => 'id'
    ];

    public static $_tables_ids = [
        'sections' => Section::class,
        'links' => Link::class,
        'footnotes' => Footnote::class
    ];

    /**
     * Fields used for data import
     *
     * @var string[]
     */
    protected $_fields_import = [
        'id',
        'created',
        'modified',
        'published',
        'type' => 'articletype', //TODO: rename in database
        'iri' => 'norm_iri', //TODO: rename in database
        'sortno',
        FIELD_ARTICLES_SIGNATURE,
        'name',
        'status',
        'norm_data',
        'creator' => 'created_by', // TODO: remove alias?
        'modifier' => 'modified_by', // TODO: remove alias?
        'project' => 'projects_id' // TODO: remove alias?
    ];

    /**
     * @var string The property name of child entities. Used for triple generation.
     */
    public $_children = 'sections';


    /**
     * Get whether the field is public
     *
     * @param string|array $fieldName
     * @param array $options
     * @return string
     */
    public function getFieldIsVisible($fieldName, $options = [])
    {
        $userRole = $this->root->currentUserRole ?? 'guest';
        $requestPublished = $options['published'] ?? \App\Model\Table\BaseTable::$requestPublished ?? [];

        // Don't show backlinks and notes to guests
        if (in_array($fieldName, ['backlinks', 'notes', 'status'])) {
            return ($userRole !== 'guest') && empty($requestPublished);
        }

        return parent::getFieldIsVisible($fieldName);
    }

    /**
     * Get the first of the following fields:
     * - signature
     * - title
     * - id
     *
     * @return string|null
     */
    protected function _getCaption()
    {
        return $this->signature ?: $this->name ?: $this->id;
    }

    /**
     * The path of articles is the same as the caption
     *
     * @return string|null
     */
    protected function _getCaptionPath()
    {
        return $this->_getCaption();
    }

    /**
     * The external name of an article is the same as the caption
     *
     * @return string|null
     */
    protected function _getCaptionExt()
    {
        return $this->_getCaption();
    }

    /**
     * Prepare the tree properties of the entity
     *
     *
     *  TODO: This is necessary for using "parent" in triple placeholders.
     *        Is this redundant with the routine in findCollectItems() ?
     *
     * @return void
     */
    public function prepareTree()
    {
        if ($this->_prepared_tree) {
            return;
        }
        if (!empty($this->sections)) {
            $this->sections = PositionBehavior::addTreePositions($this->sections);
        }
        $this->_prepared_tree = true;
    }

    /**
     * Create a section menu
     *
     * @param boolean|string $meta first|last|false Include a meta item at the first or last position
     * @return array
     */
    public function getMenu($meta = false)
    {
        $this->prepareRoot();
        $guest = ($this->root->currentUserRole ?? 'guest') === 'guest';

        // Menu settings
        $sectionmenu = [
            'caption' => __('Sections'),
            'activate' => false,
            'scrollbox' => true,
            'tree' => 'foldable',
            'data' => ['data-list-add' => '/epi/' . Databank::removePrefix($this->databaseName) . '/sections/add/' . $this->id],
            'class' => 'widget-scrollsync menu-sections'
        ];

        // Article item
        // TODO: implement function that creates menu items
        if (!empty($meta)) {
            $metaitem = [
                'label' => __('Metadata'),
                'fixed' => true,
                'url' => '#doc-' . $this->id . '-content',
                'data' => [
                    'data-list-itemof' => "menu-left",
                    'data-section-id' => 'doc-' . $this->id . '-content',
                    'data-id' => 'doc-' . $this->id . '-content',
                    'data-parent' => '',
                    'data-level' => '0',
                    'data-tree-parent' => '',
                    'data-children' => '0',
                    'data-last' => '',
                    'data-first' => ''
                ],
                'tree_level' => 0,
                'tree_children' => '0',
                'tree_last' => '',
                'tree_first' => '',
                'tree_parent' => '',
                'tree-comment' => 0,
                'tree-published' => 0
            ];
        }

        if ($meta === 'first') {
            $sectionmenu[] = $metaitem;
        }

        // Add sections
        try {
            $this->prepareTree();
        } catch (TreeCorruptException $e) {
            $this->setParsingError('sections', $e->getMessage());
        }

        foreach ($this->sections as $section) {
            if (!$section->getEntityIsVisible()) {
                continue;
            }

            $sectionmenu[] = [
                'label' => ($section->type['merged']['name']['prefix'] ?? '')
                    . ($section->name ?? $section->type->caption ?? $section->sectiontype ?? __('Section')),
                'url' => '#sections-' . $section->id,
                //'url' => false,
                'data' => [
                    'data-list-itemof' => "menu-left",
                    'data-section-id' => 'sections-' . $section->id,
                    'data-id' => $section['id'],
                    'data-parent' => $section['parent_id'],
                    'data-level' => $section['level'],
                    'data-tree-parent' => $section['parent_id'],
                    'data-children' => $section['tree_children'],
                    'data-last' => $section['tree_last'] ?? false,
                    'data-first' => $section['tree_first'] ?? false
                ],
                'tree_level' => $section['tree_level'] ?? 0,
                'tree_children' => $section['tree_children'],
                'tree_last' => $section['tree_last'] ?? false,
                'tree_first' => $section['tree_first'] ?? false,
                'tree_parent' => $section->parent ?? null,
                'tree-comment' => $guest ? null : (int)!empty($section['comment']),
                'tree-published' => $guest ? null : $section['published'] ?? PUBLICATION_DRAFTED,
                'class' => $section->empty ? 'menu-section-empty' : ''
            ];
        }

        if ($meta === 'last') {
            $sectionmenu[] = $metaitem;
        }

        // Template
        $sectionmenu[] = [
            'template' => true,
            'label' => '{name}',
            'url' => '#sections-{id}',
            'data' => [
                'data-list-itemof' => "menu-left",
                'data-section-id' => 'sections-{id}',
                'data-id' => '{id}',
                'data-parent' => '{parent_id}',
                'data-level' => '{level}',
                'data-tree-parent' => '{parent_id}',
                'data-children' => '0',
                'data-last' => '',
                'data-first' => ''
            ],
            'tree_level' => 0,
            'tree_children' => '0',
            'tree_last' => '',
            'tree_first' => '',
            'tree_parent' => '', //'{parent_id}'
            'tree-comment' => 0,
            'tree-published' => 0
        ];

        return $sectionmenu;
    }

    /**
     * Get all nodes of the article, including:
     *
     * - the article
     * - its sections
     * - contained items
     * - all footnotes
     * - all links/annotations
     * - TODO: files
     * - TODO: properties
     *
     *
     * @param array $targets Optionally, filter the tree by providing a list of types for each level,
     *                       e.g. ['sections'=>['inscription','inscriptionpart'],'footnotes'=>['app1']]
     * @return array
     */
    public function getTree($targets = [])
    {
        $nodes = [];

        // TODO: Output search results in the tree?
        //        foreach ($this->search ?? [] as $item) {
        //            if (!$item->getEntityIsVisible()) {
        //                continue;
        //            }
        //            $nodes = array_merge($nodes, $item->tree);
        //        }

        // Sections
        if (empty($targets) || isset($targets['sections']) || isset($targets['footnotes'])) {
            foreach ($this->sections as $section) {
                $nodes = array_merge($nodes, $section->getTree($targets));
            }
        }

        // Footnotes on article level
        if (isset($targets['footnotes'])) {
            foreach ($this->footnotes as $child) {
                if (!empty($targets['footnotes']) && !in_array($child->from_tagname, $targets['footnotes'])) {
                    continue;
                }

                $nodes[] = [
                    'id' => $child->table_name . '-' . $child->id,
                    'parent_id' => 'articles-' . $this->id,
                    'caption' => __('Footnote') . ' ' . $child->caption,
                    'caption_path' => $child->captionPath ?? $child->caption,
                    'caption_ext' => $child->captionExt ?? $child->caption,
                    'data_table' => $child->table_name,
                    'data_id' => $child->id,
                ];
            }
        }

        // The article itself
        if (empty($targets) || !empty($nodes) || in_array($this->type->name, $targets['articles'] ?? [])) {
            $nodes = array_merge(
                [
                    [
                        'id' => 'articles-' . $this->id,
                        'parent_id' => null,
                        'caption' => __('Article') . ' ' . $this->caption,
                        'caption_path' => $this->captionPath,
                        'caption_ext' => $this->captionExt,
                        'data_table' => 'articles',
                        'data_id' => $this->id
                    ]
                ],
                $nodes
            );
        }

        return PositionBehavior::addTreePositions($nodes);
    }

    /**
     * Get the unfiltered tree as a virtual property
     *
     * @return array
     */
    protected function _getTree()
    {
        return $this->getTree();
    }

    /**
     * Check whether other articles refer to this article
     *
     * @return bool
     */
    protected function _getHasToReferences()
    {
        return $this->table->find('hasReferences', ['references' => [$this->id]])->count() > 0;
    }

    /**
     * Get other articles that refer to this article
     *
     * @return Query
     */
    protected function _getToReferences()
    {
        return $this->table->find('hasReferences', ['references' => [$this->id]]);
    }

    /**
     * Get the file path for image uploads, without the base path
     *
     * All article related files are located within the base path 'articles',
     * preferably in subfolders named after the project signature and the article signature.
     *
     * Subfolder paths inside 'articles' are stored in the file_path field, including the base path.
     * The file_name field only stores the file name without any path.
     *
     * @return mixed|string
     */
    protected function _getFileDefaultpath()
    {
        $path = '';

        $subfolder = Files::cleanPath($this->project->signature ?? '');
        if (!empty($subfolder)) {
            $path .= $subfolder . DS;
        }

        $subfolder = Files::cleanPath($this->signature ?? '');
        if (!empty($subfolder)) {
            $path .= $subfolder . DS;
        }

        return $path;
    }


    /**
     * Check whether the article is well-formed
     *
     * Returns missing types, orphaned items, missing annotations or tags
     *
     * TODO: write tests
     * TODO: include more integrity checks...
     *  - unresolved links, footnotes and properties
     *  - differences between image items and images in folder
     *  - not configured fields
     *  - Yield problems from generator?
     *
     * @return string[] Error messages
     */
    protected function _getProblems()
    {
        $errors = [];
        $this->prepareRoot();


        // Missing types
        $errors = array_merge($errors, $this->missingTypes);
        $errors = array_merge($errors, $this->parsingErrors);
        $errors = array_merge($errors, $this->linkErrors);

        if ($this->container === null) {

            // Items outside a section
            $section_ids = array_column($this->sections, 'id');
            $items_section_ids = Objects::extract($this->getItemsByType(), '*.sections_id');
            $orphaned = array_diff($items_section_ids, $section_ids);
            if ($orphaned) {
                $errors[] = __(
                    'The article contains {0} items outside of a section. Ask your personal SQL hacker for help.',
                    count($orphaned)
                );
            }
        }

        // Missing annotation tags
        $errors = array_merge(
            $errors,
            array_map(fn($x) => $x['problem'], $this->missing_xml_tags)
        );

        // Collect problems from links
        // TODO: detect duplicate tagids
        foreach ($this->links ?? [] as $link) {
            $errors = array_merge($errors, $link->problems);
        }

        // Collect problems from sections
        foreach ($this->sections ?? [] as $section) {
            $errors = array_merge($errors, $section->problems);
        }

        return $errors;
    }

    /**
     * Find all records with empty or unconfigured types:
     * project, article, sections, items, links, and footnotes are analyzed
     *
     * TODO: move to the specific entities (implement getProblems() for each entity)?
     *
     * @return string[] A list of error messages
     */
    protected function _getMissingTypes()
    {
        $errors = [];

        // Only for root items
        if ($this->container !== null) {
            return $errors;
        }

        // Undefined types
        $checks = [
            'projects' => ['fieldname' => 'projecttype', 'rows' => (empty($this->project) ? [] : [$this->project])],
            'articles' => ['fieldname' => 'articletype', 'rows' => [$this]],
            'sections' => ['fieldname' => 'sectiontype', 'rows' => $this->sections],
            'items' => ['fieldname' => 'itemtype', 'rows' => $this->getItemsByType()],
            'links' => ['fieldname' => 'from_tagname', 'rows' => $this->links],
            'footnotes' => ['fieldname' => 'from_tagname', 'rows' => $this->footnotes]
        ];

        $types = $this->table->getDatabase()->types;
        foreach ($checks as $scope => $check) {
            if (empty($types) || empty($check['rows'])) {
                continue;
            }

            // Empty types
            $emptyTypes = array_filter(
                $check['rows'] ?? [],
                fn($x) => empty($x[$check['fieldname']])
            );

            if ($emptyTypes) {
                $errors[] = __(
                    'The article contains {0} with empty types. '
                    . 'Ask your personal SQL hacker for help.',
                    $scope
                );
            }

            // Unconfigured types
            $missingTypes = array_diff(
                array_map(fn($x) => $x[$check['fieldname']] ?? '', $check['rows'] ?? []),
                array_keys($types[$scope] ?? [])
            );

            if ($missingTypes) {
                $errors[] = __(
                    'The article contains undefined {0} types {1}. ',
                    $scope,
                    implode(', ', $missingTypes)
                );
            }
        }
        return $errors;
    }

    /**
     * Get number of annotations
     *
     * @return int
     */
    protected function _getAmountOfAnnotations()
    {
        $items = $this->getItemsByType(); //pull items out of each section
        $items = Hash::filter($items, function ($item) {
            return !empty($item['properties_id']);
        });

        $links = $this->links ?? [];
        $links = Hash::filter($links, function ($link) {
            return ($link['to_tab'] === 'properties') && !empty($link['to_id']);
        });

        return count($items) + count($links);
    }

    /**
     * Get number of characters in search items (without comment)
     *
     * @return float|int
     */
    protected function _getAmountOfText()
    {
        $items = $this->getItemsByType("search"); //pull items out of each section
        $textlength = array_map(
            function ($item) {
                return $item['value'] === 'comment' ? 0 : mb_strlen($item['content']);
            },
            $items
        );

        return array_sum($textlength);
    }

    /**
     * Get state of content
     *
     * @return string
     */
    protected function _getContentState()
    {
        //TODO: use types definition instead of hard coded state
        $sections = $this->getSections(['text', 'locations', 'conditions', 'inscriptiontext']);

        $sections = collection($sections)
            ->groupBy('sectiontype')
            ->map(
                fn($sectiongroup) => array_sum(
                        array_map(fn($section) => $section->complete, $sectiongroup)
                    ) > 0
            )
            ->toArray();

        return array_sum($sections) . '/4';
    }

    /**
     * Check whether an article is published
     *
     * Overrides the method of the BaseEntity.
     *
     * @return bool|int|null
     */
    protected function _getPublishedState()
    {
        return $this->published ?? $this->project->published;
    }

    /**
     * Get the URL of the epigraf article
     *
     * @return string
     */
    protected function _getInternalUrl()
    {
        return '/epi/' . Databank::removePrefix($this->databaseName) . '/articles/view/' . $this->id;
    }

    /**
     * Get the URL to an external resource (e.g. on DIO)
     *
     * Return the first URL in the norm_data field
     *
     * @return mixed|string
     */
    protected function _getExternalUrl()
    {
        $link = $this->norm_data_parsed[0] ?? [];
        return $link['url'] ?? '';
    }

    /**
     * Get an URL for search results
     *
     * If available, return an external URL,
     * otherwise falls back to an internal URL
     *
     * @return mixed
     */
    protected function _getUrl()
    {
        $url = $this->external_url;
        return $url ? $url : Router::url($this->internal_url, true);
    }

    /**
     * Get an image for the article preview
     *
     * In the articles config set preview.images to the list of itemtypes with
     * thumbnail images to speed up the process (e.g. ['dio-images-raw', 'images']).
     * Otherwise all items will be scanned
     *
     *  In the config of the items set fields.file.baseurl.
     *  For example, to load images for dio-images-raw,
     * set the base URL to 'https://inschriften.net/fileadmin' in the item config.
     *
     * @return array
     */
    protected function _getThumb()
    {
        $guest = ($this->currentUserRole ?? 'guest') === 'guest';
        $published = $guest ? [PUBLICATION_PUBLISHED, PUBLICATION_SEARCHABLE] : null;
        $itemtypes = $this->type->config['preview']['images'] ?? [];

        $imageItems = $this->getItemsByType($itemtypes, ['published' => $published, 'sort' => ['published', 'sortno']]);
        $imageItems = array_values(
            array_filter(
                array_map(fn($item) => $item->thumb, $imageItems),
                fn($thumb) => (($thumb['exists'] ?? false))
            )
        );

        $imageItem = $imageItems[0] ?? [];
        $imageData = [
            'src' => $imageItem['thumburl'] ?? '',
            'caption' => $imageItem['caption'] ?? ''
        ];

        return (array_filter($imageData));
    }

    /**
     * Extract text for preview tiles
     *
     * In the article config, set the key preview.text to the itemtypes
     * that should be considered, for example ['dio-inscriptions-raw','transcriptions','di-description'].
     * Otherweise all items are scanned for content.
     *
     * @return string
     */
    protected function _getPreview()
    {
        //$guest = ($this->currentUserRole ?? 'guest') === 'guest';
        $published = null; // $guest ? PUBLICATION_PUBLISHED : null;
        $itemtypes = $this->type->config['preview']['text'] ?? [];

        $items = $this->getItemsByType($itemtypes,
            ['onlyVisible' => true, 'minPublished' => $published, 'sort' => ['published', 'sortno']]);
        foreach ($items as $item) {
            $text = $item->getValueFormatted('content');
            if (!empty($text)) {
                return $text;
            }
        }

        return '';
    }

    /**
     * Create an array of summary values
     *
     * @return array
     */
    protected function _getSummary()
    {
        $summary = [];

        $selected = $this->type['merged']['preview']['summary'] ?? [];
        $columns = $this->getColumns($selected);

        foreach ($columns as $columnName => $columnConfig) {
            $columnConfig['format'] = 'html';
            $columnConfig['aggregate'] = $columnConfig['aggregate'] ?? 'collapse';
            $value = $this->getValueNested($columnConfig['key'] ?? 'name', $columnConfig);
            $value = is_array($value) ? json_encode($value) : $value;

            $summary[$columnName] = [
                'icon' => $columnConfig['icon'] ?? '',
                'value' => $value
            ];
        }

        $summary = array_filter($summary, fn($x) => ($x['value'] !== '') & ($x['value'] !== false));
        return $summary;

    }

    /**
     * Get items by section id
     *
     * @return mixed
     */
    protected function _getItemsBySectionsId()
    {
        if (!isset($this->_lookup['items']['sections_id'])) {
            $this->_lookup['items']['sections_id'] = collection($this->items ?? [])
                ->groupBy(fn($item) => $item['sections_id'] ?? '')
                ->toArray();
        }
        return $this->_lookup['items']['sections_id'];
    }

    /**
     * Get items by type
     *
     * TODO: revise _getItemsByType, getItemsByType, _getItemsBySectionsId, getItemsBySectionsId
     *
     * @return mixed
     */
    protected function _getItemsByType()
    {
        if (!isset($this->_lookup['items']['type'])) {
            $this->_lookup['items']['type'] = collection($this->items)
                ->groupBy('itemtype')
                ->toArray();
        }
        return $this->_lookup['items']['type'];
    }

    /**
     * Group and reorder sections for article view
     *
     * Define the sections in the article config by using the section types as keys.
     * Different sections of the same type can be distinguished using keys following the scheme `sectiontype[<name>]`.
     * To reorder sections, give the sections a weight. Positive weights move the section down, negative weights move them up.
     * Nonpublic sections will be filtered out for guest users.
     *
     * @param array $options
     * @return Article The article itself
     */
    public function regroupSections($options = [])
    {
        $sectionsTemplate = $this->type->merged['sections'] ?? [];
        $sections = [];

        foreach ($this->sections ?? [] as $section) {
            $configKey = $section['sectiontype'];
            $configKeyName = $configKey . '[' . $section['name'] . ']';

            $typeConfig = $sectionsTemplate[$configKeyName] ?? $sectionsTemplate[$configKey] ?? [];

            $section['weight'] = $typeConfig['weight'] ?? 0;
            $section['hide'] = $typeConfig['hide'] ?? false;

            // TODO: App or Epi level?
            $section['collapsed'] = !empty($typeConfig['collapsed'] ?? false);

            $section->container = $this;
            $section->clean();

            $sections[] = $section;
        }

        // Sort by weight
        $this->sections = PositionBehavior::sortTree($sections ?? [], 'weight', 'asc');

        return $this;
    }

    public function getSectionTypes()
    {
        $sectionsConfig = $this->table->getDatabase()->types['sections'] ?? [];
        $sectionTypes = $this->type['merged']['sections'] ?? [];

        // TODO: better config key?
        $sectionTypes = array_filter($sectionTypes, fn($type) => $type['count'] ?? false);

        $sectionTypes = array_map(
            function ($type) use ($sectionsConfig) {
                $type['caption'] = $sectionsConfig[$type['type'] ?? '']['caption'] ?? $type['type'] ?? $type['caption'] ?? '';
                return $type;
            },
            $sectionTypes
        );

        return $sectionTypes;
    }

    /**
     * Add a new section (and all its default children)
     *
     * @param string $sectionKey The section key as defined in the config
     * @param boolean $import Set IDs as they come (false) or pipe them through the import procedure (true)
     * @return $this
     */
    public function addSection($sectionKey, $import = true)
    {
        $sectionConfigTree = collection($this->type['config']['sections'])
            ->map(function ($value, $key) {
                $value = Arrays::stringToArray($value, 'caption', $value);
                $value['key'] = $key;
                $value['parent'] = $value['parent'] ?? '';
                return $value;
            })
            ->groupBy('parent')
            ->toArray();

        $sectionQueue = [
            ['key' => $sectionKey, 'parent' => null]
        ];
        while (!empty($sectionQueue)) {

            $newSection = array_shift($sectionQueue);

            $sectionId = Attributes::uuid('new');
            $sectionConfig = $this->type['config']['sections'][$newSection['key']] ?? [];
            $sectionConfig['parent'] = $newSection['parent'];
            $this->createSection($sectionId, $sectionConfig, $import);

            $childSections = collection($sectionConfigTree[$newSection['key']] ?? [])
                ->map(function ($value) use ($sectionId) {
                    return ['key' => $value['key'], 'parent' => $sectionId];
                })
                ->toArray();
            $sectionQueue = array_merge($sectionQueue, $childSections);
        }

        $this->numberSections();

        return $this;
    }

    /**
     * Reset the sortno field of all sections
     *
     * @return void
     */
    public function numberSections()
    {
        foreach ($this->sections as $key => $section) {
            $section->sortno = $key + 1;
        }
    }

    /**
     * Create the default sections of an article
     * (based on the articletype)
     *
     * @param boolean $import Set IDs as they come (false) or pipe them through the import procedure (true)
     * @return $this
     */
    public function addDefaultSections($import)
    {
        $this->_lookup['index'] = [];

        $this->sections = [];
        foreach ($this->type['config']['sections'] ?? [] as $sectionKey => $sectionConfig) {
            if ($sectionConfig['default'] ?? true) {
                $this->createSection($sectionKey, $sectionConfig, $import);
            }
        }
        $this->numberSections();

        return $this;
    }

    /**
     * Create a section by its config
     *
     * @param string|integer $sectionId The temporary section ID
     * @param string|array $sectionConfig = [ // The section type (string) or an array with the following keys
     *    'type' => '',     // The section type
     *    'caption' => '',  // The caption of the new section
     *    'parent' => ''    // The temporary ID of the parent section
     * ]
     *
     * @param boolean $import Set IDs as they come (false) or pipe them through the import procedure (true)
     * @return Section
     */
    public function createSection($sectionId, $sectionConfig, $import = true)
    {
        $sectionTypes = $this->table->getDatabase()->types['sections'];
        $sectionTypeName = is_string($sectionConfig) ? $sectionConfig : ($sectionConfig['type'] ?? '');
        $sectionType = $sectionTypes[$sectionTypeName] ?? [];
        $sectionConfig = is_array($sectionConfig) ? $sectionConfig : [];

        // Caption
        $sectionCaption = $sectionConfig['caption'] ?? $sectionType['caption'] ?? __('Section');

        // Parent
        $sectionParent = $sectionConfig['parent'] ?? null;

        $data = [
            'id' => $sectionId,
            'parent_id' => $sectionParent,
            'articles_id' => $this->id,
            'sectiontype' => $sectionTypeName,
            'name' => $sectionCaption
        ];

        // The import options make sure that temporary IDs (section keys and section parent keys are treated as such are replaced on save
        $options = [
            'source' => 'Epi.Sections',
            'import' => $import,
            'table_name' => 'sections',
            'type_field' => 'sectiontype',
            'index' => &$this->_lookup
        ];

        /** @var Section $section */
        $section = $this->table->Sections->newEntity($data, $options);
        $section->prepareRoot($this, $this);

        // Get property ID of the caption. The ID is not saved directly into the section.
        // It is saved in the first item containing a properties_id field set to field format 'sectionname'.
        // See addDefaultItems().
        $propertyType = $sectionType['merged']['fields']['name']['options'] ?? null;
        if (!empty($propertyType)) {
            $property = $this->table->Sections->Items->Properties
                ->find('all')
                ->where(['propertytype' => $propertyType, 'lemma' => $sectionCaption])
                ->first();
            if (!empty($property)) {
                $section->properties_id = $property->id;
            }
        }

        if (!$import) {
            $section->id = $sectionId;
            $section->parent_id = $sectionParent;
            $section->articles_id = $this->id;
        }
        $section->addDefaultItems($import);

        $this->_fields['sections'][] = $section;
        $this->setDirty('sections', true);

        return $section;
    }

    /**
     * Clear contained sections.
     *
     * * // TODO: clear items, links and footnotes
     *
     * @return boolean
     */
    public function clear()
    {
        if ($this->id !== null) {
            $items = $this->table->Sections->find('all')->where(['articles_id' => $this->id]);
            return $this->table->Sections->deleteMany($items);
        }
        return false;
    }

    /**
     * Get export fields
     *
     * @param $options
     *
     * @return array
     */
    public function getExportFields($options)
    {
        // Filter out content in link targets
        if ($this->container instanceof Link) {
            $fields = [
                'id',
                'norm_iri',
                'articletype',
                FIELD_ARTICLES_SIGNATURE,
                'name'
            ];
        }

        else {
            $fields = parent::getExportFields($options);
            if ($this->container !== null) {
                $fields = array_diff($fields, ['database']);
            }
        }

        return $fields;
    }

    /**
     * Get export attributes
     *
     * @param $options
     *
     * @return array|mixed
     */
    public function getExportAttributes($options)
    {
        $fields = parent::getExportAttributes($options);
        if ($this->container !== null) {
            unset($fields['database']);
        }

        return $fields;
    }

    /**
     * Copy article images to a target folder
     *
     * @param array $itemtypes The itemtypes to be considered
     * @param string $targetFolder The target folder, absolute path on the server
     * @param array $metadataConfig If a metadata configuration is provided, metadata is written to the files.
     *                              Each key is a metadata field in the file,
     *                              each value is an extraction key from the perspective of an image item.
     *                              Example: ["licence" => "file_licence","copyright" => "file_copyright"]
     * @return Item[]
     */
    public function copyImages($itemtypes, $targetFolder, $metadataConfig)
    {
        $imageItems = $this->getItemsByType($itemtypes,
            ['published' => [PUBLICATION_PUBLISHED, PUBLICATION_SEARCHABLE]]);
        foreach ($imageItems as $imageItem) {

            $imageItem->copyImage($targetFolder, $metadataConfig);
        }
        return $imageItems;
    }

    /**
     * Get section
     *
     * @param $id
     * @return mixed
     */
    public function getSection($id)
    {
        return collection($this->sections ?? [])->firstMatch(['id' => $id]);
    }

    /**
     * Get all items of the article.
     *
     * Depending on the used finder, items are either attached
     * to the article or nested into the sections. This function
     * collects them all.
     *
     * @return Item[]
     */
    protected function _getItems()
    {
        $items = [];

        // Items are attached to the article
        if (isset($this->_fields['items'])) {
            $items = $this->_fields['items'];
        }
        // Items are attached to the sections
        elseif (!empty($this->sections)) {
            foreach ($this->sections as $section) {
                $items = array_merge($items ?? [], $section->items);
            }
        }
        return $items;
    }

    /**
     * Get items by itemtype and optionally filter only published items
     *
     * ### Options
     * - published An array of publication states
     *
     * @param array $itemtypes
     * @param array $options
     *
     * @return Item[]
     */
    public function getItemsByType($itemtypes = [], $options = [])
    {
        $itemtypes = is_string($itemtypes) ? [$itemtypes] : $itemtypes;

        $items = [];
        foreach ($this->items as $item) {
            if (!empty($itemtypes) && !in_array($item['itemtype'], $itemtypes)) {
                continue;
            }

            if (!$item->getEntityIsVisible($options)) {
                continue;
            }

            $item->root = $this;
            $items[] = $item;
        }

        // Sort the items
        $sortFields = $options['sort'] ?? [];
        if (!empty($sortFields)) {
            usort($items, function ($a, $b) use ($sortFields) {
                foreach ($sortFields as $sortField) {
                    $diff = ($a[$sortField] > $b[$sortField]) ? 1
                        : ($a[$sortField] < $b[$sortField] ? -1 : 0);
                    if ($diff !== 0) {
                        return $diff;
                    }
                }
                return $diff;
            });
        }

        //if (!$generator) {
        return $items;
        //}
    }

    /**
     * Get sections after setting their container and root property
     *
     * TODO: check where we can get rid of prepareRoot()-calls
     *
     * @return array
     */
    protected function _getSections()
    {
        $this->prepareRoot();
        return $this->_fields['sections'] ?? [];
    }

    /**
     * Get all sections filtered by section type
     *
     * @param array|string $sectiontypes A section type, a list of section types or an empty list
     */
    public function getSections($sectiontypes = [])
    {
        $sectiontypes = is_string($sectiontypes) ? [$sectiontypes] : $sectiontypes;

        $sections = $this->sections ?? [];

        if (!empty($sectiontypes)) {
            $sections = Hash::filter($sections, function ($section) use ($sectiontypes) {
                return in_array($section['sectiontype'], $sectiontypes);
            });
        }

        return $sections;
    }

    /**
     * Searches all items and returns whether one of the items links to the given property
     *
     * @param $propertyId
     * @return bool
     */
    public function hasProperty($propertyId)
    {
        $this->prepareRoot();
        return collection($this->getItemsByType())->filter(fn($item
            ) => ($item->properties_id === $propertyId))->count() > 0;
    }

    /**
     * Return fields to be rendered in view/add/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        $default = [
            'articletype' => [
                'caption' => __('Article type'),
                'id' => 'articletype',
                'type' => 'select',
                'options' => $this->typeOptions,
                'action' => ['add']
            ],

            'projects_id' => [
                'caption' => __('Project'),
                'id' => 'projects_id',
                'type' => 'select',
                'options' => $this->table->Projects->find('list')->orderAsc('name'),
                'action' => ['edit', 'add', 'view']
            ],

            'name' => [
                'caption' => __('Title'),
                'action' => ['edit', 'add', 'view'],
                'autofocus' => true
            ],

            FIELD_ARTICLES_SIGNATURE => [
                'caption' => __('Article number'),
                'help' => __('A number or string used to identify articles, e.g. a signature.'),
                'action' => ['edit', 'add', 'view']
            ],

//            'norm_iri' => [
//                'caption' => __('IRI fragment'),
//                'action' => ['edit']
//            ],

            'iri_path' => [
                'caption' => __('IRI path'),
                'help' => __('A global identifier, automatically generated from the ID.'),
                'format' => 'iri',
                'enabled' => false,
                'action' => ['view', 'edit']
            ],

            'status' => [
                'caption' => __('Status'),
                'help' => __('Your personal status markers for the article.'),
                'public' => false,
                'action' => ['view', 'edit']
            ],

            'sortno' => [
                'caption' => __('Sorting'),
                'help' => __('A number used to sort articles.'),
                'public' => false,
                'action' => ['view', 'edit']
            ],

            'published' => [
                'caption' => __('Progress'),
                'help' => __('Non-published articles are not visible to guest users.'),
                'type' => 'select',
                'options' => $this->publishedOptions,
                'empty' => true,
                'public' => false,
                'action' => ['view', 'edit']
            ],

            'norm_data' => [
                'caption' => __('Norm data'),
                'help' => __('Each line is interpreted as a prefixed identifier, e.g. a URN.'),
                'format' => 'normdata',
                'type' => 'textarea',
                'rows' => 3,
                'action' => ['view', 'edit']
            ],

            'created' => [
                'caption' => __('Created on'),
                'public' => false,
                'action' => ['view']
            ],

            'created_by' => [
                'caption' => __('Created by'),
                'extract' => 'creator.name',
                'public' => false,
                'action' => ['view']
            ],

            'modified' => [
                'caption' => __('Last modified'),
                'action' => ['view']
            ],

            'modified_by' => [
                'caption' => __('Modified by'),
                'extract' => 'modifier.name',
                'public' => false,
                'action' => ['view']
            ]
        ];

        // If no type configuration exists: Select some default fields
        if (empty($this->type['merged']['fields'])) {
            $fields = array_intersect_key(
                $default,
                [
                    'articletype' => true,
                    'projects_id' => true,
                    'name' => true,
                    'norm_iri' => true,
                    'iri_path' => true,
                    'created' => true,
                    'modified' => true
                ]
            );
        }

        // If a type configuration exists: Merge config into defaults
        else {

            $config = $this->type['merged']['fields'] ?? [];
            $fields = [];
            foreach ($default as $key => $defaultValue) {
                $configValue = $config[$key] ?? [];
                if (!empty($configValue)) {
                    if (is_bool($configValue)) {
                        $fields[$key] = $defaultValue;
                    }
                    else {
                        $configValue = Arrays::valueToArray($configValue, 'caption');
                        $fields[$key] = array_merge($defaultValue, $configValue);
                    }
                }
                elseif (in_array($key, ['articletype', 'projects_id'])) {
                    $fields[$key] = $defaultValue;
                }
            }
        }

        return $fields;
    }

    /**
     * Recover the section tree
     *
     * @return void
     */
    public function recoverTree()
    {
        $this->table->Sections->setScope($this->id);
        $this->table->Sections->recover();
    }

    /**
     * Loop all sections and update their search items
     *
     * @return Item[] List of updated search items
     */
    public function updateSearchItems()
    {
        $updatedItems = [];
        $this->prepareRoot();
        if (!$this->deleted && $this->sections) {

            /** @var Section $section */
            foreach ($this->sections as $section) {
                $updatedItems = array_merge($updatedItems, $section->updateSearchItems());
            }
            if (!empty($updatedItems)) {
                $this->setDirty('sections', true);
            }
        }
        return $updatedItems;
    }


}
