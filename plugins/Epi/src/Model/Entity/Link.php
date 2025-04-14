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

/**
 * Link Entity
 *
 * # Database fields (without inherited fields)
 * @property int $root_id
 * @property string $root_tab
 * @property int $from_id
 * @property string $from_tab
 * @property string $from_field
 * @property string $from_tagname
 * @property string $from_tagid
 * @property int $to_id
 * @property string $to_tab
 * @property string $to_field
 * @property string $to_tagid
 *
 * # Virtual fields
 * @property string|null $toArticlesValue
 * @property integer|null $toArticlesId
 * @property mixed $toValue
 * @property mixed $toIriPath
 * @property string $toCaption
 *
 * @property array $problems
 * @property string $caption
 */
class Link extends BaseEntity
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
        'root_id' => true,
        'root_tab' => true,
        'from_id' => true,
        'from_tab' => true,
        'from_field' => true,
        'from_tagid' => true,
        'from_tagname' => true,
        'to_id' => true,
        'to_tab' => true,
        'to_field' => true,
        'to_tagid' => true,
        'norm_iri' => true
    ];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'id',
        'to_id',
        'from_id',
        'from_field',
        'from_tagid',
        'from_tagname',
        'property',
        'section',
        'article',
        'footnote',
        'to_value',
        'to_articles_id',
        'to_articles_value',
        'root_id',
        'norm_iri'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'to_id',
        'to_value',
        'to_articles_id',
        'to_articles_value',
        'from_id',
        'from_field',
        'from_tagname',
        'from_tagid',
        'root_id',
        'norm_iri'
    ];

    /**
     * Snippets for export
     * Each snippet contains a list of fields that is added to $_serialize_fields.
     *
     * @var array[]
     */
    protected $_serialize_snippets = [
        'deleted' => ['deleted', 'version_id', 'created', 'modified'],
        'editors' => ['creator', 'modifier', 'created', 'modified'],
        'problems' => ['problems']
    ];

    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     *
     * @var string[]
     */
    public static $_fields_ids = [
        'id',
        'root_id' => ['root_tab', 'root_id'],
        'from_id' => ['from_tab', 'from_id'],
        'to_id' => ['to_tab', 'to_id'],
        'articles_id' => 'articles', // why?
        'to_articles_id' => 'articles', // why?
    ];

    protected $_fields_formats = [
        'id' => 'id',
        'to_id' => 'id',
        'from_id' => 'id',
        'root_id' => 'id',
        'to_articles_id' => 'id', // Why??
        'articles_id' => 'id', // Why??
        'created_by' => 'id',
        'modified_by' => 'id',
        'published' => 'published'
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
        'root_id' => ['root_tab', 'root_id'],
        'from_id' => ['from_tab', 'from_id'],
        'from_field',
        'from_tagname',
        'from_tagid',
        'to_id' => ['to_tab', 'to_id'],
        'to_value',
        'to_type',
        'iri' => 'norm_iri' //TODO: rename in database
    ];

    /**
     * Create a new property if the annotation links to a non-existing property
     *
     * Set to_id, to_tab, to_value and to_type for creating a new property
     *
     * @return void
     */
    public function createNewProperty()
    {
        $propertyId = $this->_fields['to_id'] ?? '';
        $propertyType = $this->_fields['to_type'] ?? '';
        $propertyValue = $this->_fields['to_value'] ?? '';

        if (($propertyValue !== '') && ($propertyId !== '') && ($propertyType !== '')) {
            $propertyData = [
                'id' => $propertyId,
                'lemma' => $propertyValue,
                'propertytype' => $propertyType
            ];
            $entities = $this->table->Properties->toEntities([$propertyData], $this->root->getIndex());
            $result = $this->table->Properties->saveEntities($entities);
        }
    }

    /**
     * In case an annotation links to external articles, get the articles caption
     *
     * @return string|null
     */
    protected function _getToArticlesValue()
    {
        $default = null;

        if ($this->section->article ?? false) {
            if (($this['root_tab'] !== 'articles') || ($this['root_id'] !== $this['section']['articles_id'])) {
                return $this->section->article->caption ?? $default;
            }
        }

        elseif ($this->footnote->article ?? false) {
            if (($this['root_tab'] !== 'articles') || ($this['root_id'] !== $this['footnote']['root_id'])) {
                return $this->footnote->article->caption ?? $default;
            }
        }

        return $default;
    }

    /**
     * In case an annotation links to external articles, get the articles id
     *
     * @return integer|null
     */
    protected function _getToArticlesId()
    {
        $default = null;

        if ($this->section->article ?? false) {
            if (($this['root_tab'] !== 'articles') || ($this['root_id'] !== $this['section']['articles_id'])) {
                return $this->section->article->id ?? $default;
            }
        }
        elseif ($this->footnote->article ?? false) {
            if (($this['root_tab'] !== 'articles') || ($this['root_id'] !== $this['footnote']['root_id'])) {
                return $this->footnote->article->id ?? $default;
            }
        }

        return $default;
    }

    /**
     * Get the annotation caption based on its target or type
     *
     * // TODO: unify notation in other entities: define the  differences between
     *          caption, captionPath, captionExt, name, display_name, displayName, to_value, to_articles_value
     *
     */
    protected function _getToValue()
    {
        $default = $this->type->caption ?? null;

        if (($this['to_tab'] == 'properties') && !empty($this->property)) {
            return $this->property->caption ?? $default;
        }
        elseif (($this['to_tab'] == 'articles') && !empty($this->article)) {
            return $this->article->caption ?? $default;
        }
        elseif (($this['to_tab'] == 'sections') && !empty($this->section)) {
            // TODO: include external article caption (captionExt)
            return $this->section->namePath ?? $default;
        }
        elseif (($this['to_tab'] == 'footnotes') && !empty($this->footnote)) {
            // TODO: include external article caption (captionExt)
            return $this->footnote->caption ?? $default;
        }
        else {
            return $default;
        }
    }

    /**
     * Get the annotation IRI based on its target or type
     *
     * // TODO: unify notation in other entities: define the  differences between
     *          caption, captionPath, captionExt, name, display_name, displayName, to_value, to_articles_value
     *
     */
    protected function _getToIriPath()
    {
        $default = null;

        if (($this['to_tab'] == 'properties') && !empty($this->property)) {
            return $this->property->iriPath ?? $default;
        }
        elseif (($this['to_tab'] == 'articles') && !empty($this->article)) {
            return $this->article->iriPath ?? $default;
        }
        elseif (($this['to_tab'] == 'sections') && !empty($this->section)) {
            return $this->section->iriPath ?? $default;
        }
        elseif (($this['to_tab'] == 'footnotes') && !empty($this->footnote)) {
            return $this->footnote->iriPath ?? $default;
        }
        else {
            return $default;
        }
    }

    /**
     * Get the link target caption for XML injection,
     * including the article caption for external links
     *
     * @return string
     */
    protected function _getToCaption()
    {
        $value = $this->toValue ?? '';

        $article = $this->toArticlesValue;
        if (!empty($article)) {
            $value = $article . '[' . $value . ']';
        }

        return $value;
    }

    /**
     * Find missing link targets
     *
     * @return array
     */
    protected function _getProblems()
    {
        $problems = [];
        if (!is_null($this['to_id']) && ($this['to_tab'] === 'properties') && (empty($this['property']))) {
            $problems[] = __('Missing target property in annotation links-{id}. Your personal SQL hacker can help you.',
                ['id' => $this->id]);
        }
        elseif (!is_null($this['to_id']) && ($this['to_tab'] === 'articles') && (empty($this['article']))) {
            $problems[] = __('Missing target article in annotation links-{id}. Your personal SQL hacker can help you.',
                ['id' => $this->id]);
        }
        elseif (!is_null($this['to_id']) && ($this['to_tab'] === 'sections') && (empty($this['section']))) {
            $problems[] = __('Missing target section in annotation links-{id}. Your personal SQL hacker can help you.',
                ['id' => $this->id]);
        }
        elseif (!is_null($this['to_id']) && ($this['to_tab'] === 'footnotes') && (empty($this['footnote']))) {
            $problems[] = __('Missing target footnote in annotation links-{id}. Your personal SQL hacker can help you.',
                ['id' => $this->id]);
        }

        if (empty($this['from_tab']) || empty($this['from_id']) || empty($this['from_field']) || empty($this['from_tagid'])) {
            $problems[] = __('Missing source data in annotation links-{id}. Your personal SQL hacker can help you.',
                ['id' => $this->id]);
        }

        if (empty($this->from_tagid)) {
            $problems[] = __('Missing tag ID in annotation links-{id}. Your personal SQL hacker can help you.',
                ['id' => $this->id]);
        }
        // TODO: Why can a from_tagid not be present? A warning comes up when saving an article.
        elseif (count($this->root->links_by_tagid[$this->from_tagid] ?? []) > 1) {
            $problems[] = __('Duplicate tag ID {tagid} in annotation links-{id}. Your personal SQL hacker can help you.',
                ['id' => $this->id, 'tagid' => $this->from_tagid]);
        }

        return $problems;
    }

    /**
     * Get an annotation label
     *
     * @return string
     */
    protected function _getCaption()
    {
        return ($this->type['caption'] ?? $this->from_tagname);
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
            elseif ($fieldName[0] === 'to_id') {
                return $this->toIriPath;
            }
        }
        return parent::getIdFormatted($fieldName, $options);
    }

    public function getEntityIsVisible($options = [])
    {
        if (!empty($this->root) && !empty($this->root->_filterAnnos) && method_exists($this->root, 'getTree')) {
            $tree = $this->root->getTree(['articles'=>true,'sections'=>true,'items'=>true,'footnotes'=>true]);
            if (!isset($tree[$this->from_tab . '-' . $this->from_id])) {
                return false;
            }
        }

        return parent::getEntityIsVisible($options);
    }
}
