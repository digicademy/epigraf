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

use App\Model\Entity\Databank;
use App\Model\Entity\DefaultType;
use Cake\Routing\Router;

/**
 * Project Entity
 *
 * # Database fields (without inherited fields)
 * @property string $projecttype
 * @property int $sortno
 * @property string $name
 * @property string $signature
 * @property string $description
 * @property string $norm_data
 *
 * # Virtual fields
 * @property string $captionPath
 * @property string $shortname
 * @property string $fullName
 * @property string $internalUrl
 * @property string $externalUrl
 * @property string $url
 * @property array $htmlFields
 * @property mixed $defaultType
 *
 * # Relations
 * @property \Epi\Model\Entity\Article[] $articles
 */
class Project extends RootEntity
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
        'name' => true,
        FIELD_PROJECTS_SIGNATURE => true,
        'sortno' => true,
        'description' => true,
        'projecttype' => true,
        'norm_data' => true,
        'norm_iri' => true
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
        'sortno',
        'name',
        FIELD_PROJECTS_SIGNATURE,
        'norm_data',
        'norm_iri',
        'projecttype',
        'description',
        'database',
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'sortno',
        'projecttype',
        'database',
        'norm_iri'
    ];

    /**
     * Snippets for export
     * Each snippet contains a list of fields that is added to $_serialize_fields.
     * // TODO: export editor fields (modifier, creator) if requested
     *
     * @var array[]
     */
    protected $_serialize_snippets = [
        'published' => ['published'],
        'editors' => ['creator', 'modifier', 'created', 'modified'],
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
        'type' => 'projecttype',
        'iri' => 'norm_iri', //TODO: rename in database
        'sortno',
        FIELD_PROJECTS_SIGNATURE,
        'name',
        'content' => 'description',
        'norm_data'
    ];

    /**
     * Fields containing IDs that will be prefixed with the table name in getDataForExport.
     *
     * @var string[]
     */
    public static $_fields_ids = ['id'];

    protected $_fields_formats = [
        'id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id',
        'published' => 'published',
        'description' => 'json'
    ];

    protected $_field_iri = FIELD_PROJECTS_SIGNATURE;

    /**
     * Get short name of project
     *
     * @return string
     */
    protected function _getCaptionPath()
    {
        //return implode(' - ',array_filter([$this->signature,$this->name]));
        $label = $this->name . ' [' . $this->signature . ']';

        return $label;
    }

    protected function _getShortname()
    {
        return $this->captionPath;
    }

    protected function _getFullName()
    {
        return mb_strtoupper($this->signature) . ": " . $this->name;

    }


    /**
     * Get the URL of the epigraf article
     *
     * @return string
     */
    protected function _getInternalUrl()
    {
        return '/epi/' . Databank::removePrefix($this->databaseName) . '/projects/view/' . $this->id;
    }

    /**
     * Get the URL to an external resource (e.g. on DIO)
     *
     * Return the first URL in the norm_data field
     *
     * @return string
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
        $url = $this->externalUrl;
        return $url ? $url : Router::url($this->internalUrl, true);
    }

    /**
     * Return fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        $fields = [
            'projecttype' => [
                'caption' => __('Project type'),
                'type' => 'select',
                'options' => $this->typeOptions
            ],

            'name' => ['caption' => __('Name')],

            FIELD_PROJECTS_SIGNATURE => [
                'caption' => __('Shortname'),
                'help' => __('An abbreviation that only contains letters and numbers,'
                    . ' no whitespace or special characters.')
            ],

            'sortno' => [
                'caption' => __('Sorting'),
                'help' => __('A number used to sort projects.')
            ],

            'description' => [
                'caption' => __('Description'),
                'id' => 'textarea_config',
                'rows' => 15,
                'format' => 'json',
                'type' => 'jsoneditor'
                //'action' => 'edit'
            ],

            'norm_iri' => [
                'caption' => __('IRI fragment'),
                'help' => __('In combination with the project type, '
                    . ' identifies the project within the universe and should not be '
                    . ' changed later. Usually the same as the signature.'),
                'action' => ['edit']
            ],

            'iri_path' => [
                'caption' => __('IRI path'),
                'format' => 'iri',
                'action' => ['view']
            ],

            'norm_data' => [
                'caption' => __('Norm data'),
                'format' => 'normdata',
                'type' => 'textarea',
                'help' => __('Multiple identifiers or URLs can be entered on multiple lines. '
                    . 'Namespaces can be used if they are configured in the project type.'),
                'action' => ['view', 'edit']
            ],

            'published' => [
                'caption' => __('Progress'),
                'type' => 'select',
                'options' => $this->publishedOptions,
                'action' => ['edit', 'view']
            ],

            'created' => [
                'caption' => __('Created'),
                'action' => 'view'
            ],

            'modified' => [
                'caption' => __('Modified'),
                'action' => 'view'
            ]
        ];

        // Default fields
        $fields = $this->type->getHtmlFields($fields,
            ['projecttype', 'name', FIELD_PROJECTS_SIGNATURE, 'description', 'norm_iri']);

        return $fields;
    }

    /**
     * Get the default type for the entity, if no type configuration is available in the types table
     *
     * @return DefaultType
     */
    protected function _getDefaultType()
    {
        $type = new DefaultType([
            'scope' => 'projects',
            'mode' => 'default',
            'name' => 'default',
            'norm_iri' => 'default',
            'config' => []
        ]);
        return $type;
    }

}

