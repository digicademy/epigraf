<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */

declare(strict_types=1);

namespace Epi\Model\Entity;

use App\Utilities\Converters\Attributes;

/**
 * Tag extracted from XML content
 *
 * TODO: lookup links / footnote record
 *
 * # Database fields (if it was a database entity)
 * @property string $tab
 * @property int $id
 * @property string $field
 * @property string $tagid
 * @property string $tagname
 * @property string $content
 *
 */
class Tag extends BaseEntity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'field',
        'tagid',
        'tagname',
        'caption',
        'content'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'field',
        'tagid',
        'tagname'
    ];

    /**
     * Default tag name used in the XmlView
     *
     * @var string
     */
    public $_xml_tag = 'tag';

    /**
     * Default table name
     *
     * @var string
     */
    public $_tablename = '';

    /**
     * Constructor
     *
     * @param array $data
     * @param array $options
     */
    public function __construct(array $data, array $options = [])
    {
        parent::__construct($data, $options);
    }

    /**
     * Get the type entity.
     *
     * @return array
     */
    protected function _getType()
    {
        if (empty($this->_fields['type'])) {

            $footnoteTypes = $this->table->getDatabase()->types['footnotes'] ?? [];
            $linkTypes = $this->table->getDatabase()->types['links'] ?? [];
            $type = $footnoteTypes[$this['tagname']] ?? $linkTypes[$this['tagname']] ?? null;

            $this->_fields['type'] = $type;
        }
        return $this->_fields['type'] ?? null;
    }

    /**
     * Get warnings related to the tag and assign each tag to its footnote or link entity
     *
     * @return array
     */
    protected function _getWarnings()
    {
        $warnings = [];

        $tagId = $this['tagid'] ?? null;
        $tagName = $this['tagname'] ?? null;

        $fieldKey = $this->tab . '-' . $this->field;

        // Missing type configuration
        if (empty($this->type)) {
            $warnings[$fieldKey][] = ['msg' =>  __('Missing configuration for tag {tagname}#{tagid} .', $this->_fields)];
        }

        // Missing footnote or annotation
        elseif ($this->type['scope'] === 'footnotes') {
            $annos = $this->root->footnotesByTagid[$tagId] ?? [];
            if (empty($annos)) {
                $warnings[$fieldKey][] = ['msg' =>  __('Missing footnote for tag {tagname}#{tagid} .', $this->_fields)];
            }

            foreach ($annos as $anno) {
                $anno->linkedTags[] = $this;
            }
        }
        elseif ($this->type['scope'] === 'links') {
            $annos = $this->root->linksByTagid[$tagId] ?? [];
            $toConfig = $this->type['merged']['fields']['to'] ?? []; // Allow missing annotations for non-required targets
            if (empty($annos) && isset($toConfig['required']) && !empty($toConfig['required']) ) {
                $warnings[$fieldKey][] = ['msg' =>  __('Missing annotation for tag {tagname}#{tagid} .', $this->_fields)];
            }
            foreach ($annos as $anno) {
                $anno->linkedTags[] = $this;
            }
        }

        // Wrong hieararchy
        if (!empty($this->type) && ($tagName !== TAGNAME_NL)) {


            if (!empty($this->container)) {
                $containerField = $this->container->type['config']['fields'][$this->field] ?? [];
            } else {
                $containerField = [];
            }

            // Tags allowed as children of other tags
            if (Attributes::isTrue($containerField['constrain'] ?? true)) {
                if (!empty($this->parent)) {
                    $allowed = $this->parent->type['config']['types'] ?? [];
                }
                // Tags allowed as root elements in the field
                elseif (!empty($this->container)) {
                    $allowed = $containerField['types'] ?? [];
                }
                else {
                    $allowed = [];
                }

                if (!$this->isAllowed($allowed)) {
                    $warnings[$fieldKey][] = [
                        'msg' => __('Child tag {tagname}#{tagid} not allowed in this position.', $this->_fields)
                    ];
                }
            }

        }

        return $warnings;
    }

    /**
     * Check if the tag is allowed in the current context based on its scope, name and group
     *
     * @param string[] $allowed Selectors: tag names, group names, or scoped
     * @return bool
     */
    public function isAllowed($allowed)
    {
        $tagName = $this['tagname'] ?? null;
        $tagScope = $this['scope'] ?? null;

        if (!empty($tagName)) {
            if (in_array($tagName, $allowed) ) {
                return true;
            }

            if (in_array($tagScope . '.' . $tagName, $allowed)) {
                return true;
            }
        }

        $tagGroup = $this->type['merged']['group'] ?? null;
        if (!empty($tagGroup)) {
            if (in_array($tagGroup, $allowed) ) {
                return true;
            }

            if (in_array($tagScope . '.' . $tagGroup, $allowed)) {
                return true;
            }
        }

        return false;
    }

}
