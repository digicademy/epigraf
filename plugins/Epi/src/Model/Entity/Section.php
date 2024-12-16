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

namespace Epi\Model\Entity;

use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use Cake\Utility\Hash;

/**
 * Section Entity
 *
 * A section can have different types of titles, depending on the types config:
 * - A numeric section number stored in the number field
 * which counts the sections within the group of the same section type
 * contained in a parent.
 * - A custom title as stored in the name field. Depending on the config, this
 * may be the section number in its numerical (1) or alphabetic form (A).
 * - An alias stored in the alias field
 *
 * To get titles, use the appropriate properties:
 * - name The name as it is stored in the database ("A")
 * - caption The name prefixed by a value from the config which is not stored in the database ("Inscription A").
 * - namePath: The path, combined from the section names including ancestor nodes ("A.1")
 * - captionPath: Same as namePath, but prefixed by a value from the config ("Part A.1").
 * - nameExt: A combination of the article caption and the path ("signature1[A.1]")
 * - captionExt: Same as nameExt, but including the prefix from the config ("signature1[Inscription A]").
 *
 * # Database fields (without inherited fields)
 * @property int $sortno
 * @property int $layout_cols
 * @property int $layout_rows
 * @property string $sectiontype
 * @property int $number
 * @property string $name
 * @property string $nalias
 * @property string $comment
 * @property int $status
 *
 * @property int $parent_id
 * @property int $level Virtual property with database field
 * @property int $lft
 * @property int $rght
 * @property int $articles_id
 *
 * # Virtual fields
 * @property array $problems
 * @property bool $empty
 * @property string $statusText
 * @property null|bool $complete
 * @property mixed|null $children
 * @property mixed|null $siblings
 * @property string $caption
 * @property string $namePath
 * @property string $captionPath
 * @property string $nameExt
 * @property string $captionExt
 * @property array $tree
 * @property array $htmlFields
 *
 * # Relations
 * @property \Epi\Model\Entity\Item[] $items
 */
class Section extends BaseEntity
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
        'deleted' => true,
        'sortno' => true,
        'layout_cols' => true,
        'layout_rows' => true,

        'sectiontype' => true,
        'number' => true,
        'name' => true,
        'alias' => true,

        'comment' => true,
        'status' => true,

        'parent_id' => true,
        'level' => true,
        'lft' => true,
        'rght' => true,

        'norm_iri' => true,
        'articles_id' => true,
        'items' => true,

        '_import_ids' => true
    ];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'id',
        'parent_id',
        'articles_id',
        'sortno',
        'level',
        'children',
        'number',
        'siblings',
        'sectiontype',
        'name',
        'alias',
        'layout_cols',
        'layout_rows',
//        'comment',
        'status',
        'items',
        'published'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'parent_id',
        'articles_id',
        'sortno',
        'published',
        'level',
        'children',
        'number',
        'siblings',
        'layout_cols',
        'layout_rows',
        'sectiontype',
        'name',
        'alias'
    ];

    /**
     * Snippets for export
     * Each snippet contains a list of fields that is added to $_serialize_fields.
     *
     * @var array[]
     */
    protected $_serialize_snippets = [
        'pulished' => ['published'],
        'iris' => ['norm_iri'],
        'comments' => ['comment'],
        'editors' => ['creator', 'modifier', 'created', 'modified'],
        'paths' => ['path'],
        'problems' => ['problems']
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
        'type' => 'sectiontype',
        'iri' => 'norm_iri',
        'sortno',

        'number',
        'signature' => FIELD_SECTIONS_SIGNATURE, // alias
        'name',
        'status', // Only used in EpiDesktop, deprecated

        'content' => 'comment', //TODO: rename in database
        'layout_cols',
        'layout_rows',

        'article' => 'articles_id', // TODO: Why not alias with article? -> Changed, does it work? Both necessary?
        'articles_id',
        'parent' => 'parent_id',    // TODO: Why not alias with parent? -> Changed, does it work? Both necessary?
        'parent_id'
    ];

    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     * See BaseEntity.
     *
     * @var string[]
     */
    public static $_fields_ids = [
        'id',
        'parent_id',
        'articles_id' => 'articles'
    ];

    public static $_tables_ids = [
        'items' => Item::class
    ];

    protected $_fields_formats = [
        'id' => 'id',
        'parent_id' => 'id',
        'articles_id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id',
        'published' => 'published',
        'comment' => 'xml'
    ];

    /**
     * @var string The property name of child entities. Used for triple generation.
     */
    public $_children = 'items';

    /**
     * Find all item fields with invalid targets
     *
     * @return array
     */
    protected function _getProblems()
    {
        $problems = [];
        foreach ($this->items as $item) {
            $problems = array_merge($problems, $item->problems);
        }

        return $problems;
    }


    /**
     * Check section for being empty
     *
     * @return bool
     */
    protected function _getEmpty()
    {
        return empty($this['status']) &&
            empty($this['comment']) &&
            empty($this['items']) &&
            empty($this['tree_children']);
    }

    /**
     * Get current status string
     *
     * @return string
     */
    protected function _getStatusText()
    {
        $status = [0 => 'new', 1 => 'in progress', 2 => 'finished', 3 => 'reviewed'];
        return $status[$this->status] ?? '';
    }

    /**
     * Check section for being complete
     *
     * @return null|bool
     */
    protected function _getComplete()
    {
        // TODO: use types definition instead of hard coded checks
        if ($this->sectiontype === 'locations') {
            $items = $this->getItemsByType('locations');
            $items = Hash::filter($items, fn($item) => !empty($item['properties_id']));
            return count($items) > 0;
        }

        elseif (($this->sectiontype === 'text') && ($this->name === 'Beschreibung')) {
            $items = $this->getItemsByType('text');
            $items = Hash::filter($items, fn($item) => !empty($item['content']));
            return count($items) > 0;
        }

        elseif ($this->sectiontype === 'conditions') {
            $items = $this->getItemsByType('conditions');
            $items = Hash::filter($items, fn($item) => !empty($item['date_value']));
            return count($items) > 0;
        }

        elseif ($this->sectiontype === 'inscriptiontext') {
            $items = $this->getItemsByType('transcriptions');
            $items = Hash::filter($items, fn($item) => !empty($item['content']));
            return count($items) > 0;
        }

        else {
            return null;
        }
    }

    /**
     * Clear contained items.
     * // TODO: test whether links and footnotes are correctly cleared, save job_id
     *
     * @return boolean
     */
    public function clear()
    {
        if ($this->id !== null) {
            $items = $this->table->Items->find('all')->where(['sections_id' => $this->id]);
            return $this->table->Items->deleteMany($items);
        }
        return false;
    }

    /**
     * Get export fields
     *
     * @param $options
     * @return array|string[]
     */
    public function getExportFields($options)
    {
        // Filter out content in link target
        if ($this->container instanceof Link) {
            $fields = [
                'id',
                'parent_id',
                'articles_id',
                'sortno',
                'number',
                'sectiontype',
                'name',
                'alias',
            ];
        }
        else {
            $fields = parent::getExportFields($options);
        }

        return $fields;
    }

    /**
     * Get all items of the section.
     *
     * Depending on the used finder, items are either attached
     * to the article or nested into the sections. This function
     * collects them all.
     *
     * @return Item[]
     */
    protected function _getItems()
    {
        // Items are attached to the section
        if (isset($this->_fields['items'])) {
//            $this->prepareRoot();
            $items = $this->_fields['items'];
//            array_walk($items, function (&$item) {
//                $item->root = $this;
//                $item->container = $this;
//            });
            return $items;
        }
        // Items are attached to the article
        elseif (!empty($this->container)) {
            return $this->container->itemsBySectionsId[$this->id] ?? [];
        }
        else {
            return [];
        }
    }

    /**
     * Get items in the section by itemtype and optionally filter only published items
     *
     * Depending on the used finder, items are either attached
     * to the article or nested into the sections. This function
     * collects them all and optionally filters by itemtype and publication state.
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
        foreach ($this->items ?? [] as $item) {
            if (!empty($itemtypes) && !in_array($item['itemtype'], $itemtypes)) {
                continue;
            }

            if (!$item->getEntityIsVisible($options)) {
                continue;
            }

            $item->root = $this;
            $items[] = $item;
        }

        return $items;
    }

    /**
     * Get items configured as visible in the types,
     * including public visibility based on the current user,
     * excluding search items,
     * and group them by itemtype
     *
     * ### Options
     * - published An array of publication states
     *
     * @param array $options
     * @return array The first element contains the grouped items, the second the item types' configuration
     */
    public function getGroupedItems($options = [])
    {
        $items = array_filter($this->items,
            fn($x) => $x->getEntityIsVisible($options) && ($x['itemtype'] !== ITEMTYPE_FULLTEXT));
        $groupedItems = Arrays::array_group($items, 'itemtype');

        $itemTypes = $this->type['merged']['items'] ?? array_keys($groupedItems);
        $itemTypes = array_filter($itemTypes, fn($x) => is_string($x) || is_array($x));
        $itemTypes = array_combine(array_map(fn($x) => is_string($x) ? $x : $x['type'], $itemTypes), $itemTypes);
        $itemTypes = array_filter($itemTypes, fn($x) => ($x['display'] ?? true));

        return [$groupedItems, $itemTypes];
    }

    /**
     * Get level of current section
     *
     * @return mixed|null
     */
    protected function _getLevel()
    {
        return empty($this->path) ?
            ($this->_fields['level'] ?? null) :
            $this->path[count($this->path) - 1]['level'];
    }

    /**
     * Get child count
     *
     * Warning: the findThreaded method uses the children key for a list
     * of children. The findTreeList helper internally uses findThreaded.
     * Therefore, these methods ar not compatible with $this->path method.
     *
     * @return mixed|null
     */
    protected function _getChildren()
    {
        return empty($this->path) ?
            $this->_fields['children'] ?? null :
            $this->path[count($this->path) - 1]['children'];
    }

    /**
     * Get number of siblings
     *
     * @return null|int
     */
    protected function _getSiblings()
    {
        return empty($this->path) ?
            $this->siblings ?? null :
            $this->path[count($this->path) - 1]['siblings'];
    }

    /**
     * Get a display label for the section without path
     *
     * @return string
     */
    protected function _getCaption()
    {
        return ($this->type['merged']['name']['prefix'] ?? '') . $this->name;
    }

    /**
     * Based on the type, construct a name including the path
     *
     * // TODO: get path even if no ancestors were loaded, lookup in the root
     * // TODO: use TreeTrait->_getPath()
     *
     * @return string
     */
    protected function _getNamePath()
    {
        if (!empty($this->path)) {
            $path = $this->getValueNested('path.{*}.name', ['aggregate' => false]);
        }
        elseif (!empty($this->ancestors)) {
            $path = $this->getValueNested('ancestors.{*}.name', ['aggregate' => false]);
            $path[] = $this->name;
        }
        else {
            $path = [$this->name];
        }

        return implode('.', $path);
    }

    /**
     * Based on the type, construct a caption including the path and prefix
     *
     * @return string
     */
    protected function _getCaptionPath()
    {
        $prefix = $this->type['merged']['name']['prefix'] ?? '';
        return $prefix . $this->namePath;
    }

    /**
     * In addition to the path, an external caption includes the article caption
     *
     * @return string
     */
    protected function _getNameExt()
    {
        if (!empty($this->root)) {
            $caption = $this->root->caption ?? '';
        }
        else {
            $caption = '';
        }

        return $caption . '[' . $this->namePath . ']';
    }

    /**
     * In addition to the path, an external caption includes the article caption
     *
     * @return string
     */
    protected function _getCaptionExt()
    {
        if (!empty($this->root)) {
            $caption = $this->root->caption ?? '';
        }
        else {
            $caption = '';
        }

        return $caption . '[' . $this->captionPath . ']';
    }

    /**
     * Get the section node including its items, footnotes and annotations
     *
     * @param array $targets Optionally, filter the tree by providing a list of types for each level,
     *                       e.g. ['items'=>'brands', 'footnotes'=>['app1']]
     * @return array
     */
    public function getTree($targets = [])
    {
        $nodes = [];

        if (!$this->getEntityIsVisible()) {
            return $nodes;
        }

        // Section footnotes
        if (empty($targets) || isset($targets['footnotes'])) {
            foreach ($this->footnotes as $child) {
                if (!empty($targets['footnotes']) && !in_array($child->from_tagname, $targets['footnotes'])) {
                    continue;
                }

                $nodes[] = [
                    'id' => $child->table_name . '-' . $child->id,
                    'parent_id' => 'sections-' . $this->id,
                    'caption' => __('Footnote') . ' ' . $child->caption,
                    'caption_path' => $child->captionPath ?? $child->caption,
                    'caption_ext' => $child->captionExt ?? $child->caption,
                    'data_table' => $child->table_name,
                    'data_id' => $child->id,
                ];
            }
        }

        // Section annotations
        if (empty($targets) || isset($targets['annotations'])) {
            foreach ($this->annotations as $child) {
                if (!empty($targets['annotations']) && !in_array($child->from_tagname, $targets['annotations'])) {
                    continue;
                }

                $nodes[] = [
                    'id' => $child->table_name . '-' . $child->id,
                    'parent_id' => 'sections-' . $this->id,
                    'caption' => __('Annotation') . ' ' . $child->caption,
                    'caption_path' => $child->captionPath ?? $child->caption,
                    'caption_ext' => $child->captionExt ?? $child->caption,
                    'data_table' => $child->table_name,
                    'data_id' => $child->id,
                ];
            }
        }

        // XML tags
        if (empty($targets) || isset($targets['tags'])) {
            $tags = $this->extractXmlTags(null, true);
            foreach ($tags as $tagId => $tag) {
                $nodes[] = [
                    'id' => 'tag-' . $tag['tagid'] ?? '',
                    'parent_id' => 'items-' . $this->id,
                    'caption' => __('Tag') . ' ' . $tag['tagname'] . '#' . $tag['tagid'],
                    'caption_path' => __('Annotation text'),
                    'caption_ext' => __('Annotation text')
                ];
            }
        }

        // Items
        if (empty($targets) || isset($targets['items'])) {
            foreach ($this->items as $item) {
                $nodes = array_merge($nodes, $item->getTree($targets));
            }
        }

        // The section itself
        if (empty($targets) || !empty($nodes) || (($targets['sections'] ?? false) === true) || in_array($this->type->name,
                $targets['sections'] ?? [])) {
            $nodes = array_merge(
                [
                    [
                        'id' => 'sections-' . $this->id,
                        'parent_id' => empty($this['parent_id']) ? 'articles-' . $this['articles_id'] : 'sections-' . $this['parent_id'],
                        'caption' => __('Section') . ' ' . $this->caption,
                        'caption_path' => $this->captionPath,
                        'caption_ext' => $this->captionExt,
                        'data_table' => 'sections',
                        'data_id' => $this->id
                    ]
                ],
                $nodes
            );
        }

        return $nodes;
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
     * Convert imported data
     *
     * @param $content
     * @param $options
     * @return array
     */
    public function importData($content, $options)
    {

        if (($content['sortno'] ?? '') === '') {
            unset($content['sortno']);
        }

        if (($content['layout_cols'] ?? '') === '') {
            unset($content['layout_cols']);
        }
        if (($content['layout_rows'] ?? '') === '') {
            unset($content['layout_rows']);
        }

        return parent::importData($content, $options);
    }


    /**
     * Based on the section type, create the default items of a section
     *
     * @param boolean $import Set IDs as they come (false) or pipe them through the import procedure (true)
     *
     * @return $this
     */
    public function addDefaultItems($import = true)
    {
        $type = $this->type;
        $itemTypes = $this->table->getDatabase()->types['items'];

        $this->items = [];
        foreach ($type['merged']['items'] ?? [] as $itemKey => $itemConfig) {
            $itemTypeName = is_string($itemConfig) ? $itemConfig : ($itemConfig['type'] ?? '');
            $itemType = $itemTypes[$itemTypeName] ?? [];
            $itemConfig = is_array($itemConfig) ? $itemConfig : [];
            if (($itemConfig['default'] ?? true) && (($itemConfig['count'] ?? '1') !== '*')) {

                $itemId = Attributes::uuid('new');
                $options = [
                    'source' => 'Epi.Items',
                    'import' => $import,
                    'table_name' => 'items',
                    'type_field' => 'itemtype',
                    'index' => &$this->root->_lookup
                ];

                $item = $this->table->Items->newEntity([
                    'id' => $itemId,
                    'sections_id' => $this->id,
                    'articles_id' => $this->articles_id,
                    'itemtype' => $itemTypeName,
                    'sortno' => 1
                ], $options);

                // Autofill properties_id from the section caption
                if (($itemType['merged']['fields']['property']['format'] ?? '') === 'sectionname') {
                    $item['properties_id'] = $this->properties_id;
                }

                $item->prepareRoot($this, $this->root);

                if (!$import) {
                    $item->id = $itemId;
                    $item->sections_id = $this->id;
                    $item->articles_id = $this->articles_id;
                }

                $this->_fields['items'][] = $item;
            }
        }

        $this->setDirty('items', true);
        return $this;
    }

    /**
     * Create a new item
     *
     * @param array $data
     * @param array|null $templateFields To create a template item with placeholder values,
     *                             provide an array with field names.
     *                             Template items are not added to the items field
     * @return \Cake\Datasource\EntityInterface
     */
    public function createItem($data = [], $templateFields = null)
    {
        // Create placeholders
        if (!empty($templateFields)) {
            foreach ($templateFields as $templateField) {
                $data[$templateField] = '{' . $templateField . '}';
            }
        }

        $data = array_merge(
            [
                'sections_id' => $this->id,
                'articles_id' => $this->articles_id
            ],
            $data
        );

        $item = $this->table->Items->newEntity($data, ['validate' => false]);

        $item->prepareRoot($this, $this->root);

        if ($templateFields === null) {
            $this->_fields['items'][] = $item;
            $this->setDirty('items', true);
        }

        return $item;
    }

    /**
     * Get the cleaned notes content for fulltext indexing
     *
     * @return \Generator
     */
    public function getSearchText()
    {
        $text = trim($this->getValueFormatted('comment', ['format' => 'txt']) ?? '');

        if ($text !== '') {
            yield [
                'index' => 'notes',
                'text' => $text,
                'published' => $this->published
            ];
        }
    }

    /**
     * Update fulltext search items
     *
     * Empty items will be skipped or deleted, nonempty items will be updated or created.
     *
     * @return Item[] List of updated items
     */
    public function updateSearchItems()
    {
        $updatedItems = [];

        if ($this->deleted) {
            return $updatedItems;
        }

        // Collect existing items
        $searchItems = [];
        $deleteItems = [];

        // Assemble new items
        $searchIndex = [];

        // Item content
        foreach ($this->items as $item) {
            // Skip deleted items
            if ($item->deleted) {
                continue;
            }

            // Cache search items
            if ($item->itemtype === ITEMTYPE_FULLTEXT) {
                $indexKey = $item->value ?? 'text';
                if (isset($searchItems[$indexKey])) {
                    $deleteItems[] = $item;
                }
                else {
                    $searchItems[$indexKey] = $item;
                }
                continue;
            }

            // Collect search text
            foreach ($item->getSearchText() as $searchItem) {
                $searchIndex[$searchItem['index']][] = $searchItem;
            }
        }

        // Notes
        foreach ($this->getSearchText() as $searchItem) {
            $searchIndex[$searchItem['index']][] = $searchItem;
        }

        // Create or update search items
        foreach ($searchIndex as $indexKey => $indexContent) {
            $content = [];
            $published = null;
            foreach ($indexContent as $searchItem) {
                $content[] = $searchItem['text'];

                if ($searchItem['published'] !== null) {
                    $published = min($searchItem['published'], $published ?? 0);
                }
            }

            $content = implode('. ', $content);

            // Update non-empty search items
            $indexItem = $searchItems[$indexKey] ?? $this->createItem(
                ['itemtype' => ITEMTYPE_FULLTEXT, 'value' => $indexKey]
            );
            $indexItem->content = $content;
            $indexItem->published = $published;
            $updatedItems[] = $indexItem;
            unset($searchItems[$indexKey]);
        }

        // Delete remaining old search items
        $deleteItems = array_merge($searchItems, $deleteItems);
        foreach ($deleteItems as $indexItem) {
            $indexItem['deleted'] = 1;
            $updatedItems[] = $indexItem;
        }

        if (!empty($updatedItems)) {
            $this->setDirty('items', true);
        }

        return $updatedItems;
    }

    /**
     * Return fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        // TODO: only for edit / lazy loading

        // Section types that can be added are defined in the articles config,
        // in the sections key.
        $sectionsConfig = $this->table->getDatabase()->types['sections'] ?? [];
        $sectionTypes = $this->article->type['merged']['sections'] ?? [];
        $sectionTypes = array_filter($sectionTypes, fn($type) => $type['count'] ?? false);
        $sectionTypes = array_combine(
            array_map(fn($type) => $type['type'] ?? '', $sectionTypes),
            array_map(fn($type) => $sectionsConfig[$type['type'] ?? '']['caption'] ?? $type['type'] ?? '',
                $sectionTypes)
        );

        $fields = [
            'sectiontype' => [
                'caption' => __('Section type'),
                'id' => 'sectiontype',
                'type' => 'select',
                'options' => $sectionTypes,
                'action' => 'add'
            ],

            'name' => [
                'caption' => __('Name'),
                'id' => 'name',
                'type' => 'hidden'
            ],

            'articles_id' => [
                'caption' => __('Article'),
                'id' => 'articles_id',
                'type' => 'hidden'
                //'action' => ['edit','add']
            ],

            'parent_id' => [
                'caption' => __('Parent section'),
                'id' => 'parent_id',
                'type' => 'hidden'
                //'action' => ['edit','add']
            ],

            'previous_id' => [
                'caption' => __('Previous section'),
                'id' => 'previous_id',
                'type' => 'hidden'
                //'action' => ['edit','add']
            ]
        ];

        // TODO: use field config

        return $fields;
    }


    /**
     * Get the ID as ID, prefixed ID or IRI pathc
     *
     * @param array $fieldName The field name as an array of one or two components
     * @param array $options
     * @return string|integer|null
     */
    public function getIdFormatted($fieldName, $options)
    {
        $prefix = $options['prefixIds'] ?? false;
        $iri = $options['iriIds'] ?? false;
        if (($prefix === false) && ($iri === true)) {
            if ($fieldName[0] === 'id') {
                return $this->iriPath;
            }
            elseif (($fieldName[0] === 'parent_id') && !empty($this->parent)) {
                return $this->parent->iriPath;
            }
            elseif (($fieldName[0] === 'articles_id') && !empty($this->root)) {
                return $this->root->iriPath;
            }
        }
        return parent::getIdFormatted($fieldName, $options);
    }
}
