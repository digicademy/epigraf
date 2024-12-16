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

use Cake\ORM\Query;
use Epi\Model\Traits\TreeTrait;

/**
 * Property Entity
 *
 * # Database fields
 * @property string $propertytype
 * @property string $lemma
 * @property string $name
 * @property int $sortno
 * @property string $sortkey
 * @property string $signature
 *
 * @property int $parent_id
 * @property int $level
 * @property int $lft
 * @property int $rght
 *
 * @property string $file_name
 * @property int $properties_id
 * @property string $unit
 * @property string $comment
 * @property string $content
 * @property string $elements
 * @property string $keywords
 * @property string $source_from
 * @property int $ishidden
 * @property int $iscategory
 *
 * @property string $norm_data
 * @property string $norm_iri
 * @property string $norm_type
 *
 * @property string $import_db
 * @property string $import_id
 *
 * @property int $related_id
 * @property int $mergedto_id
 * @property int $splitfrom_id
 *
 * # Virtual fields
 * @property bool $hasFromReferences
 * @property bool $hasBaseProperties
 * @property bool $hasChildren
 * @property bool $hasArticles
 * @property Query $baseProperties
 * @property Query $homonyms
 * @property Query $duplicates
 * @property Query $merged
 *
 * @property string $leaf
 * @property string $indentation
 *
 * @property string $captionPath
 * @property string $caption
 * @property string $shortname
 *
 * @property int $referenceId
 * @property string $referencePos
 * @property null|Property $reference
 * @property null|Property $preceding
 * @property array $positionOptions
 * @property array $htmlFields
 *
 * # Relations
 * @property Query $articles
 * @property Property[] $children
 * @property Item[] $items
 */
class Property extends RootEntity
{
    use TreeTrait;

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
        'propertytype' => true,
        'sortno' => true,
        'sortkey' => true,
        'signature' => true,
        'file_name' => true,
        'file_path' => true,
        'file_type' => true,
        'properties_id' => true,

        'lemma' => true,
        'name' => true,
        'unit' => true,
        'comment' => true,
        'content' => true,
        'elements' => true,
        'keywords' => true,
        'source_from' => true,
        'ishidden' => true,
        'iscategory' => true,

        'norm_type' => true,
        'norm_data' => true,
        'norm_iri' => true,
        'import_db' => true,
        'import_id' => true,

        'related_id' => true,
        'parent_id' => true,
        'preceding_id' => true,
        'reference_id' => true,
        'reference_pos' => true,
        'level' => true,
        'lft' => true,
        'rght' => true,
        'links' => true
    ];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'id',
        'parent_id',
        'propertytype',
        'level',
        'lft',
        'rght',
        'related_id',
        'sortno',
        'sortkey',

        'lemma',
        'name',
        'unit',
        'comment',
        'content',
        'elements',
        'iscategory',
        'ishidden',
        'signature',
        'keywords',

        'norm_type',
        'norm_data',
        'norm_iri',

        'file_name',
        'source_from',

        'property',
        'ancestors',
        'links'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'parent_id',
        'sortno',
        'sortkey',
        'related_id',
        'propertytype',
        'level',
        'lft',
        'rght',
        'iscategory',
        'ishidden'
    ];

    protected $_serialize_snippets = [
        'published' => ['published'],
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
        'parent_id',
        'related_id',
        'properties_id'
    ];

    protected $_fields_formats = [
        'id' => 'id',
        'parent_id' => 'id',
        'related_id' => 'id',
        'properties_id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id',
        'published' => 'published'
    ];

    public static $_tables_ids = [
        'links' => Link::class
    ];

    /**
     * The field used to create an IRI
     * @var string
     */
    protected $_field_iri = 'id';

    /**
     * Field for TreeTrait
     *
     * @var string
     */
    protected $_path_field = 'lemma';

    /**
     * Field for TreeTrait
     *
     * @var string
     */
    protected $_path_separator = ' › ';

    /**
     * Fields used for data import
     *
     * @var string[]
     */
    protected $_fields_import = [
        'id',
        'created',
        'modified',
        'parent_id',
        'type' => 'propertytype', //TODO: rename in database
        'related_id',
        'sortno',
        'sortkey',

        'lemma',
        'name',
        'unit',
        'comment',
        'content',
        'elements',
        'iscategory',
        'ishidden',
        'keywords',
        'signature',
        'norm_type',
        'norm_data',
        'iri' => 'norm_iri', //TODO: rename in database

        'file_name',
        'file_type', // TODO: extract automatically
        'file_path',
        'source_from',

        'property' => 'properties_id'
    ];

    /**
     * Get export fields
     *
     * @param $options
     * @return array
     */
    public function getExportFields($options)
    {
        $fields = parent::getExportFields($options);
        if ($this->container instanceof Property) {
            $fields = array_diff_key($fields, ['ancestors' => false]);
        }

        return $fields;
    }

    protected function _getHasFromReferences()
    {
        return $this->table->find('referencesFrom', ['nodes' => [$this->id]])->count() > 0;
    }

    /**
     * Get properties that are linked to the current meta property by properties_id
     *
     * @return bool
     */
    protected function _getHasBaseProperties()
    {
        $baseProperties = $this->table->find('all')
            ->where(['properties_id' => $this->id]);

        return $baseProperties->count() > 0;
    }

    protected function _getHasChildren()
    {
        return ($this->rght - $this->lft) > 1;
    }

    protected function _getHasArticles()
    {
        return $this->table->find('articles', ['reference' => $this->id])->count() > 0;
    }

    /**
     * Check whether other entities depend on this entity
     *
     * @return bool
     */
    protected function _getHasDependencies(): bool
    {
        return $this->hasChildren || $this->hasArticles || $this->hasBaseProperties || $this->hasFromReferences;
    }

    /**
     * Get articles that use the property
     *
     * @return Query
     */
    protected function _getArticles()
    {
        return $this->table->find('articles', ['reference' => $this->id]);
    }

    /**
     * Get articles that use the property
     *
     * @return Query
     */
    protected function _getBaseProperties()
    {
        return $this->table->find('all')
            ->where(['properties_id' => $this->id])
            ->limit(10);
    }

    /**
     * Get properties with the same path elements
     *
     * @return Query
     */
    protected function _getHomonyms()
    {
        return $this->table->find('homonyms', ['reference' => $this]);
    }

    /**
     * Get properties with the same path elements
     *
     * @return Query
     */
    protected function _getDuplicates()
    {
        return $this->table->find('homonyms', ['reference' => $this, 'samelevel' => true]);
    }

    /**
     * Get properties where the property was merged from
     *
     * @return Query
     */
    protected function _getMerged()
    {
        return $this->table->find('merged', ['mergedto_id' => $this->id]);
    }

    /**
     * Get the caption for tree views
     *
     * @return string
     */
    protected function _getLeaf()
    {
        $value = $this->lemma ?? '';
        return $value === '' ? $this->shortname : $value;
    }

    /**
     * Get indentation
     *
     * @return string
     */
    protected function _getIndentation()
    {
        return str_repeat($this->_path_separator, $this->level);
    }

    /**
     * Get display name of current property
     *
     * @return string
     */
    protected function _getCaptionPath()
    {
        $segments = [];

        $segments[] = $this->type->caption ?? null;
        $segments[] = $this->path ?? null;
        $segments[] = $this->lemma;

        return implode($this->_path_separator, array_filter($segments));
    }

    /**
     * Get the label injected into xml attributes, see Link->_getToValue()
     *
     * @return string
     */
    protected function _getCaption()
    {
        return $this[$this->type['merged']['displayfield'] ?? 'lemma'] ?? $this['name'];
    }

    /**
     * Get the combination of display name and, optionally, unit
     *
     * @return string
     */
    protected function _getShortname()
    {
        $value = $this[$this->type['merged']['displayfield'] ?? 'lemma'] ?? $this['name'];
        $unit = $this->unit ?? '';
        if ($unit !== '') {
            $value .= ' (' . $unit . ')';
        }
        return $value;
    }

    /**
     * Get the base file path
     *
     * All property related files are located  within the 'properties' folder,
     * in subfolders named after the property type. Additional path segments can
     * be stored in the file_name field.
     *
     * @return mixed|string
     */
    protected function _getFileBasepath()
    {
        return $this->table_name . DS . $this->propertytype . DS;
    }

    /**
     * Get the reference ID
     *
     * @return null|int
     */
    protected function _getReferenceId()
    {
        if (!isset($this->_fields['reference_id'])) {
            $preceding = $this->preceding;
            if (empty($preceding)) {
                $this['reference_id'] = $this->parent_id;
                $this['reference_pos'] = 'parent';
            }
            else {
                $this['reference_id'] = $preceding->id;
                $this['reference_pos'] = 'preceding';
            }
        }
        return $this->_fields['reference_id'] ?? null;
    }

    /**
     * Get the reference position
     *
     * @return null|string
     */
    protected function _getReferencePos()
    {
        if (!isset($this->_fields['reference_pos'])) {
            $preceding = $this->preceding;
            if (empty($preceding)) {
                $this['reference_id'] = $this->parent_id;
                $this['reference_pos'] = 'parent';
            }
            else {
                $this['reference_id'] = $preceding->id;
                $this['reference_pos'] = 'preceding';
            }
        }
        return $this->_fields['reference_pos'] ?? null;
    }

    /**
     * Get the reference entity
     *
     * @return null|Property
     */
    protected function _getReference()
    {
        $referenceId = $this->referenceId;
        if (!empty($referenceId) && (($this->_fields['reference']['id'] ?? null) !== intval($referenceId))) {
            $referenceNode = $this->table
                ->find('containAncestors')
                ->find('all')
                ->where(['id' => $referenceId])
                ->first();

            $this->_fields['reference'] = $referenceNode;

            //$property->ancestors = $referenceNode->ancestors;
        }

        return $this->_fields['reference'] ?? null;
    }


    /**
     * Get the preceding sibling
     *
     * @return null|Property
     */
    protected function _getPreceding()
    {
        if (!isset($this->_fields['preceding'])) {
            $conditions = ['rght' => $this->lft - 1];
            if (is_null($this->propertytype)) {
                $conditions['propertytype IS'] = null;
            }
            else {
                $conditions['propertytype'] = $this->propertytype;
            }

            if (!empty($this->parent_id)) {
                $conditions['parent_id'] = $this->parent_id;
            }
            else {
                $conditions['parent_id IS'] = null;
            }

            $referenceNode = $this->table
                ->find('all')
                ->where($conditions)
                ->first();

            $this->_fields['preceding'] = $referenceNode ?? null;
        }

        return $this->_fields['preceding'] ?? null;
    }

    /**
     * Get position options
     *
     * @return array
     */
    protected function _getPositionOptions()
    {
        return [
            'preceding' => __('After node ...'),
            'parent' => __('First child of ...')
        ];
    }

    /**
     * Return fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {

        // All fields
        $fields = [
            'file_name' => [
                'caption' => __('Image'),
                'type' => 'choose',
                'itemtype' => 'file',
                'options' => [
                    'controller' => 'Files',
                    'action' => 'select'
                ],
                'format' => 'image',
                'action' => ['edit', 'view', 'merge']
            ],

            'parent_id' => [
                'caption' => __('Parent'),
                'type' => 'reference',
                'url' => [
                    'controller' => 'Properties',
                    'action' => 'index',
                    $this->propertytype,
                    '?' => ['template' => 'choose', 'references' => false]
                ],
                'paneSnippet' => 'rows',
                'listValue' => 'id',
                //<- which attribute do the items carry? data-id (for trees) or data-value (everything else)
                'param' => 'find',
                // TODO: careful, the parent property (->) and the parent field ([]) are not the same
                //       because the parent property is defined in the base entity. Change name in base entity?
                'text' => $this['parentPath'] ?? '',
                'action' => ['edit']
            ],

            'caption' => [
                'caption' => __('Lemma'),
                'extract' => 'caption',
                'edit' => false,
                'action' => ['move']
            ],

            'reference_pos' => [
                'caption' => __('Position'),
                'id' => 'position',
                'type' => 'select',
                'options' => $this->positionOptions,
                'action' => ['add', 'move']
            ],

            'reference_id' => [
                'caption' => __('Reference'),
                'type' => 'reference',
                'url' => [
                    'controller' => 'Properties',
                    'action' => 'index',
                    $this->propertytype,
                    '?' => ['template' => 'choose', 'show' => 'content', 'references' => false, 'empty' => true]
                ],
                'paneSnippet' => 'rows',
                'listValue' => 'id',
                'param' => 'find',
                'text' => $this->reference['path'] ?? $this->reference['caption'] ?? '',
                'extract' => 'reference.path',
                'action' => ['add', 'move']
            ],

            'ancestors' => [
                'caption' => __('Ancestors'),
                //'extract' => 'ancestors.{*}.lemma',
                'extract' => 'parentPath',
                'action' => ['view', 'merge']
            ],

            'lemma' => [
                'caption' => __('Lemma'),
                'format' => 'typed',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'related_id' => [
                'caption' => __('Related'),
                'type' => 'reference',
                'url' => [
                    'controller' => 'Properties',
                    'action' => 'index',
                    $this->propertytype,
                    '?' => ['template' => 'choose', 'show' => 'content', 'references' => false, 'empty' => true]
                ],
                'paneSnippet' => 'rows',
                'listValue' => 'id',
                'param' => 'find',
                'text' => $this->lookup_to['path'] ?? '',
                'extract' => 'lookup_to.path',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'name' => [
                'caption' => __('Name'),
                'format' => 'typed',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'properties_id' => [
                'caption' => __('Meta property'),
                'type' => 'reference',
                'url' => [
                    'controller' => 'Properties',
                    'action' => 'index',
                    $this->type['merged']['fields']['properties_id']['types'] ?? null,
                    '?' => ['template' => 'choose', 'show' => 'content', 'references' => false]
                ],
                'param' => 'find',
                'paneSnippet' => 'rows',
                'listValue' => 'id',
                //<- which attribute do the items carry? data-id (for trees) or data-value (everything else)
                'text' => $this->property['path'] ?? '',
                'extract' => 'property.path',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'sortkey' => [
                'caption' => __('Sort Key'),
                'action' => ['view', 'add', 'edit', 'merge']
            ],

            'unit' => [
                'caption' => __('Unit'),
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'content' => [
                'caption' => 'Content',
                'type' => 'textarea',
                'format' => 'typed',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'elements' => [
                'caption' => __('Elements'),
                'format' => 'typed',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'source_from' => [
                'caption' => __('Source'),
                'format' => 'typed',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'iscategory' => [
                'caption' => __('Category'),
                'type' => 'checkbox',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'ishidden' => [
                'caption' => __('Hidden'),
                'type' => 'checkbox',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'comment' => [
                'caption' => __('Comment'),
                'type' => 'textarea',
                'format' => 'typed',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'keywords' => [
                'caption' => __('Keywords'),
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'signature' => [
                'caption' => __('Number'),
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'published' => [
                'caption' => __('Progress'),
                'type' => 'select',
                'options' => $this->publishedOptions,
                'action' => ['edit', 'view', 'merge']
            ],

            'articles_count' => [
                'caption' => __('Articles'),
                'action' => ['view']
            ],

            'norm_data' => [
                'caption' => __('Norm data'),
                'type' => 'textarea',
                'action' => ['add', 'edit', 'view', 'merge']
            ],

            'norm_iri' => [
                'caption' => __('IRI fragment'),
                'action' => ['add', 'edit', 'view', 'merge']
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
            ],
            'id' => [
                'caption' => __('ID'),
                'public' => false,
                'action' => ['view']
            ]
        ];

        // Default fields
        $fields = $this->type->getHtmlFields($fields, ['lemma'], ['caption', 'reference_pos', 'reference_id']);

        // Remove conditional fields
        if (empty($fields['parent_id']) || empty($this->ancestors)) {
            unset($fields['ancestors']);
        }

        // TODO: the related ID field vanished?
        if (!empty($this->related_id)) {
            foreach ($fields as $key => $config) {
                if (!in_array($key, ['ancestors', 'lemma', 'related_id'])) {
                    $fields[$key]['action'] = array_diff($config['action'] ?? ['edit'], ['view']);
                }
            }
        }

        if (($this->type['merged']['type'] ?? 'tree') !== 'tree') {
            unset($fields['reference_pos']['options']['parent']);
        }

        if (empty($fields['file_name'])) {
            unset($fields['image']);
        }

        return $fields;
    }

}
