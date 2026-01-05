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

use App\Model\Entity\DefaultType;
use App\Utilities\Converters\HistoricDates;
use App\Utilities\Files\Files;

/**
 * Item Entity
 *
 * # Database fields (without inherited fields)
 * @property string $itemtype
 * @property string $itemgroup
 * @property int $sortno
 * @property int $properties_id
 * @property string $value
 * @property string $content
 * @property string $translation
 * @property bool $flagged
 *
 * @property int $links_id
 * @property string $links_tab
 * @property string $links_field
 * @property string $links_tagid
 *
 * @property string $file_name
 * @property string $file_type
 * @property string $file_path
 * @property string $file_source
 * @property string $file_copyright
 * @property string $file_meta
 * @property bool $file_online
 *
 * @property string $date_sort Virtual property with database field
 * @property string $date_value
 * @property string $date_add
 * @property int $date_start Virtual property with database field
 * @property int $date_end Virtual property with database field
 *
 * @property string $source_autopsy
 * @property string $source_from
 * @property string $source_addition
 *
 * @property int $pos_x
 * @property int $pos_y
 * @property int $pos_z
 *
 * @property int $articles_id
 * @property int $sections_id
 *
 * # Virtual fields (without inherited fields)
 * @property DefaultType $defaultType
 * @property string $caption
 * @property array $tree
 * @property array $problems
 * @property bool $empty
 *
 * @property null|string $sectiontype
 * @property null|string $sectionname
 * @property null|string $sectionnumber
 * @property null|int $sectionsiblings
 *
 * @property string|null $dateNorm
 * @property array $dateParsed
 *
 * # Relations
 * @property \Epi\Model\Entity\Property $property
 * @property \Epi\Model\Entity\Article $article
 * @property \Epi\Model\Entity\Section $section
 * @property \Epi\Model\Entity\BaseEntity $link
 */
class Item extends BaseEntity
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
        'itemtype' => true,
        'itemgroup' => true,
        'norm_iri' => true,
        'sortno' => true,
        'properties_id' => true,
        'property' => true,
        'value' => true,
        'content' => true,
        'translation' => true,
        'flagged' => true,
        'to_id' => true,
        'links_id' => true,
        'links_tab' => true,
        'links_field' => true,
        'links_tagid' => true,
        'file_name' => true,
        'file_path' => true,
        'file_source' => true,
        'file_copyright' => true,
        'file_meta' => true,
        'file_online' => true,
        'date_value' => true,
        'date_add' => true,
        'date_start' => true,
        'date_end' => true,
        'date_sort' => true,
        'source_autopsy' => true,
        'source_from' => true,
        'source_addition' => true,
        'pos_x' => true,
        'pos_y' => true,
        'pos_z' => true,
        'articles_id' => true,
        'sections_id' => true,
    ];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'id',
        'to_id',
        'itemtype',
        'itemgroup',
        'norm_iri',
        'sortno',
        'sections_id',
        'articles_id',

        'value',
        'content',
        'translation',
        'flagged',
        'file_name',
        'file_type',
        'file_path',
        'file_source',
        'file_copyright',
        'file_meta',
        'file_online',
        'date_sort',
        'date_value',
        'date_add',
        'date_start',
        'date_end',
        'source_autopsy',
        'source_from',
        'source_addition',
        'pos_x',
        'pos_y',
        'pos_z',
        'property',
        'published'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'to_id',
        'itemtype',
        'itemgroup',
        'published',
        'sortno',
        'sections_id',
        'articles_id'
    ];

    /**
     * Snippets for export
     *
     * Each snippet contains a list of fields that is added to $_serialize_fields.
     *
     * @var array[]
     */
    protected $_serialize_snippets = [
        'published' => ['published'],
        'deleted' => ['deleted', 'version_id', 'created', 'modified'],
        'editors' => ['creator', 'modifier', 'created', 'modified'],
        'paths' => ['sectionpath'],
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
        'type' => 'itemtype', //TODO: rename in database
        'group' => 'itemgroup',
        'iri' => 'norm_iri',
        'sortno',
        'value',
        'content',
        'translation',
        'property' => 'properties_id',
        'properties_id',
        'to_id' => ['links_tab', 'links_id'],
        'flagged',
        'file_name',
        'file_type',
        'file_path',
        'file_source',
        'file_copyright',
        'file_online',
        'file_meta',
        'date_sort',
        'date' => 'date_value',
        'date_add',
        'date_start',
        'date_end',
        'source_autopsy',
        'source_from',
        'source_addition',
        'pos_x',
        'pos_y',
        'pos_z',
        'sections_id', // TODO: alias with section? -> Changed, does it work? Necessary for copy, the later value has the prefixed ID and overwrited the former?
        'section' => 'sections_id',
        'articles_id', // TODO: alias with article? > Changed, does it work? Necessary for copy, the later value has the prefixed ID and overwrited the former?
        'article' => 'articles_id',
    ];

    protected $_fields_formats = [
        'id' => 'id',
        'articles_id' => 'id',
        'sections_id' => 'id',
        'properties_id' => 'id',
        'to_id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id',
        'pos_x' => 'position',
        'pos_y' => 'position',
        'published' => 'published',
        'property' => 'property',
        'sortno' => 'number'
    ];

    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     *
     * @var string[]
     */
    public static $_fields_ids = [
        'id',
        'sections_id' => 'sections',
        'articles_id' => 'articles',
        'properties_id' => 'properties',
        'to_id' => ['links_tab', 'links_id']
    ];

    /**
     * Check whether another entity depends on the entity
     *
     * @param \App\Model\Entity\BaseEntity $entity
     * @return bool
     */
    public function hasRoot($entity)
    {
        if (!empty($entity) && ($entity instanceof Article) && ($entity->id === $this->articles_id)) {
            return true;
        }
        return false;
    }

    /**
     * Get the default type for the entity, if no type configuration is available in the types table
     *
     * @return DefaultType
     */
    protected function _getDefaultType()
    {
        $type = new DefaultType([
            'scope' => 'items',
            'mode' => MODE_DEFAULT,
            'name' => 'default',
            'norm_iri' => 'default',
            'config' => []
        ]);
        return $type;
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
            elseif (($fieldName[0] === 'sections_id') && !empty($this->container)) {
                return $this->container->iriPath;
            }
            elseif (($fieldName[0] === 'articles_id') && !empty($this->root)) {
                return $this->root->iriPath;
            }
        }
        return parent::getIdFormatted($fieldName, $options);
    }

    /**
     * Convert flagged to boolean
     *
     * @param $content
     * @param $options
     * @return array
     */
    public function importData($content, $options)
    {

        // Todo: implement generic converter for all entities
        if (isset($content['flagged'])) {
            $content['flagged'] = !empty($content['flagged']);
        }

        if (isset($content['source_autopsy'])) {
            $content['source_autopsy'] = !empty($content['source_autopsy']);
        }

        if (isset($content['file_online'])) {
            $content['file_online'] = !empty($content['file_online']);
        }

        if (($content['sortno'] ?? '') === '') {
            unset($content['sortno']);
        }

        return parent::importData($content, $options);
    }

    /**
     * Get export fields
     *
     * @param $options
     * @return array|string[]
     */
    public function getExportFields($options)
    {

        // Filter out content in link targets
        if ($this->container instanceof Link) {
            $fields = [
                'id',
                'itemtype',
                'norm_iri',
                'sortno',
                'sections_id',
                'articles_id',
            ];
        }

        else {
            $fields = parent::getExportFields($options);

            // TODO: merge into getExportFields?
            $template = $options['template'] ?? 'view';
            $columns = $options['columns'] ?? [];

            if (empty($columns) && ($template !== 'table') && empty($this->container)) {
                $fields = array_merge($fields, ['section', 'article']);
            }
        }
        return $fields;
    }

    /**
     * Update the fields date_start, date_end and date_sort
     *
     * @return void
     */
    public function updateDate($onlyDirty = false)
    {
        if ($onlyDirty && !$this->isDirty('date_value')) {
            return;
        }
        $this->date_start = $this->_getDateStart();
        $this->date_end = $this->_getDateEnd();
        $this->date_sort = $this->_getDateSort();
//        $this->date_value = $this->_getDateNorm();
    }


    public function updateFile($onlyDirty = false)
    {
        if ($onlyDirty && !$this->isDirty('file_name')) {
            return;
        }

        if ($this->file_name) {
            // TODO: Do we really need to split URLs ?
//            $fieldFormat = $entity->type->merged['fields']['file']['format'] ?? '';
//            if ($fieldFormat !== 'imageurl') {
            $path = pathinfo($this->file_name);
            $path['dirname'] = $path['dirname'] === '.' ? '' : $path['dirname'];

            $this['file_path'] = Files::prependPath($this['file_path'] ?? '', $path['dirname']);
            $this['file_name'] = $path['basename'] ?? '';
            $this['file_type'] = $path['extension'] ?? '';
//            } else {
//                $entity['file_name'] = '';
//                $entity['file_path'] = '';
//                $entity['file_type'] = null;
//            }
        }
        // Clear path
        else {
            $this['file_name'] = '';
            $this['file_path'] = '';
            $this['file_type'] = null;
        }
    }

    /**
     * Get an item label
     *
     * @return string
     */
    protected function _getCaption()
    {
        // Full text search sets a caption in findHasText()
        if (isset($this->_fields['caption'])) {
            return $this->_fields['caption'];
        }
        return ($this->type['caption'] ?? $this->itemtype) . ' #' . $this->sortno;
    }

    /**
     * Get the item node including its footnotes and annotations
     *
     * @param array $targets Optionally, filter the tree by providing a list of types for each level,
     *                       e.g. ['items'=>'brands', 'footnotes'=>['app1']]
     * @return array
     */
    public function getTree($targets = [])
    {
        if (isset($this->_lookup['tree'])) {
            return $this->_lookup['tree'];
        }

        $nodes = [];

        if (!$this->getEntityIsVisible()) {
            return $nodes;
        }

        // Item footnotes
        if (empty($targets) || isset($targets['footnotes'])) {
            foreach ($this->footnotes as $child) {
                if (
                    !empty($targets['footnotes']) &&
                    (is_array($targets['footnotes']) && !in_array($child->from_tagname, $targets['footnotes']))
                ) {
                    continue;
                }

                $nodes[] = [
                    'id' => $child->table_name . '-' . $child->id,
                    'parent_id' => 'items-' . $this->id,
                    'caption' => __('Footnote') . ' ' . $child->caption,
                    'caption_path' => $child->captionPath ?? $child->caption,
                    'caption_ext' => $child->captionExt ?? $child->caption,
                    'data_table' => $child->table_name,
                    'data_id' => $child->id
                ];
            }
        }

        // Item annotations
        if (empty($targets) || isset($targets['annotations'])) {
            foreach ($this->annotations as $child) {
                if (
                    !empty($targets['annotations']) &&
                    (is_array($targets['annotations']) && !in_array($child->from_tagname, $targets['annotations']))
                ) {
                    continue;
                }

                $nodes[] = [
                    'id' => $child->table_name . '-' . $child->id,
                    'parent_id' => 'items-' . $this->id,
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
            $tags = $this->extractXmlTags(null, ['content' => true]);
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

        // The item itself
        if (empty($targets) || !empty($nodes) || ($targets['items'] ?? false === true) || in_array($this->type->name,
                $targets['items'] ?? [])) {
            $nodes = array_merge(
                [
                    [
                        'id' => 'items-' . $this->id,
                        'parent_id' => 'sections-' . $this->sections_id,
                        'caption' => __('Content') . ' ' . $this->caption,
                        'caption_path' => $this->captionPath ?? $this->caption,
                        'caption_ext' => $this->captionExt ?? $this->caption,
                        'data_table' => 'items',
                        'data_id' => $this->id
                    ]
                ],
                $nodes
            );
        }

        $this->_lookup['tree'] = $nodes;
        return $nodes;
        //return collection($nodes)->groupBy('id')->toArray();
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
     * Find all fields with invalid targets
     *
     * @return array
     */
    protected function _getProblems()
    {
        $errors = [];
//        $errors = array_merge($errors, $this->parsingErrors);
//        $errors = array_merge($errors, $this->linkErrors);

        $missing = [];

        if (!empty($this->properties_id) && empty($this->property)) {
            $missing[] = [
                'to_tab' => 'properties',
                'to_id' => $this->properties_id,
                'from_tab' => 'items',
                'from_id' => $this->id,
                'from_field' => 'properties_id'
            ];
        }

        if (!empty($this->links_id) || !empty($this->links_tab)) {
            if (empty($this->links_article) && empty($this->links_section)) {
                $missing[] = [
                    'to_tab' => $this->links_tab,
                    'to_id' => $this->links_id,
                    'from_tab' => 'items',
                    'from_id' => $this->id,
                    'from_field' => 'links_id'
                ];
            }
        }

        $missing = array_map(
            fn($x) => __(
                'Missing property {to_tab}-{to_id} in field {from_tab}-{from_id}.{from_field}.',
                $x
            ),
            $missing
        );

        $errors = array_merge($errors, $missing);
        return $errors;
    }

    /**
     * Check whether the item has content
     *
     * @return bool
     */
    protected function _getEmpty()
    {
        return empty($this->value)
            && empty($this->content)
            && empty($this->translation)
            && empty($this->properties_id)
            && empty($this->flagged)
            && empty($this->file_name)
            && empty($this->file_source)
            && empty($this->file_copyright)
            && empty($this->date_value)
            && empty($this->date_add)
            && empty($this->source_from)
            && empty($this->source_addition)
            && empty($this->links_id);
    }

    /**
     * Get current section type
     *
     * @return null|string
     */
    protected function _getSectiontype()
    {
        return empty($this->sections_path) ?
            null :
            $this->sectionpath[count($this->sectionpath) - 1]['sectiontype'];
    }

    /**
     * Get current section name
     *
     * @return null|string
     */
    protected function _getSectionname()
    {
        return empty($this->sections_path) ?
            null :
            $this->sectionpath[count($this->sectionpath) - 1]['name'];
    }

    /**
     * Get current section number
     *
     * @return null|string
     */
    protected function _getSectionnumber()
    {
        return empty($this->sections_path) ?
            null :
            $this->sectionpath[count($this->sectionpath) - 1]['number'];
    }

    /**
     * Get siblings of current section
     *
     * @return mixed|null
     */
    protected function _getSectionsiblings()
    {
        return empty($this->sections_path) ?
            null :
            $this->sectionpath[count($this->sectionpath) - 1]['siblings'];
    }

    /**
     * Get normalized date
     *
     * @return String|null
     */
    protected function _getDateNorm()
    {
        if (!empty($this->date_value)) {
            return HistoricDates::normalize($this->date_value);
        }
        else {
            return null;
        }
    }

    /**
     * Get sort key of date
     *
     * @return String|null
     */
    protected function _getDateSort()
    {
        if (!empty($this->date_value)) {
            return HistoricDates::encode($this->date_value);
        }
        else {
            return null;
        }
    }

    /**
     * Get first year of the date range
     *
     * @return integer|null
     */
    protected function _getDateStart()
    {
        if (!empty($this->date_value)) {
            return HistoricDates::minyear($this->date_value);
        }
        else {
            return null;
        }
    }

    /**
     * Get last year of the date range
     *
     * @return integer|null
     */
    protected function _getDateEnd()
    {
        if (!empty($this->date_value)) {
            return HistoricDates::maxyear($this->date_value);
        }
        else {
            return null;
        }
    }

    /**
     * Get all parsed dates
     *
     * @return array An array of dates contained in the date value.
     *               Each date is an array composed of date components.
     */
    protected function _getDateParsed()
    {
        return HistoricDates::parseHistoricDateList($this->date_value);
    }
}
