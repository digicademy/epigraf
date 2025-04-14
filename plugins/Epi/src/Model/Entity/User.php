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

use App\Model\Entity\DefaultType;
use Epi\Model\Table\UsersTable;

/**
 * User Entity
 *
 * # Database fields (without inherited fields)
 * @property string $usertype
 * @property string $name
 * @property string $acronym
 * @property int $userrole
 * @property string $norm_iri
 *
 * # Virtual fields
 * @property mixed $defaultType
 * @property array $userroleOptions
 * @property array $usertypeOptions
 * @property array $iriOptions
 * @property array $htmlFields
 */
class User extends RootEntity
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
        'name' => true,
        'usertype' => true,
        'norm_iri' => true,
        'acronym' => true,
        'userrole' => true,
    ];

    /**
     * Fields used for serialization
     *
     * @var string[]
     */
    public $_serialize_fields = [
        'id',
        'name',
        'acronym',
        'usertype',
        'userrole',
        'norm_iri'
    ];

    /**
     * Attributes used for serialization
     *
     * @var string[]
     */
    public $_serialize_attributes = [
        'id',
        'usertype',
        'userrole'
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
    public static $_fields_ids = ['id'];

    protected $_field_iri = 'acronym';

    protected $_fields_formats = [
        'id' => 'id',
        'created_by' => 'id',
        'modified_by' => 'id',
        'userrole' => 'select'
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
        'name',
        'type' => 'usertype',
        'userrole',
        'acronym',
        'iri' => 'norm_iri'
    ];

    /**
     * Get the default type for the entity, if no type configuration is available in the types table
     *
     * @return DefaultType
     */
    protected function _getDefaultType()
    {
        $type = new DefaultType([
            'scope' => 'users',
            'mode' => MODE_DEFAULT,
            'name' => 'human',
            'norm_iri' => 'human',
            'config' => [
                'fields' => [
                    'name' => 'Name',
                    'acronym' => 'Acronym',
                    'userrole' => [
                        'caption' => __('User role'),
                        'type' => 'select',
                        'options' => $this->userroleOptions
                    ],
                    'usertype' => 'User type',
                    'norm_iri' => 'IRI fragment'
                ],
            ]
        ]);
        return $type;
    }

    /**
     * Get user roles
     *
     * @return array
     */
    protected function _getUserroleOptions()
    {
        // TODO: translate
        return UsersTable::$userRoles;
    }

    /**
     * Get user roles
     *
     * @return array
     */
    protected function _getUsertypeOptions()
    {
        return [
            'human' => __("Human")
        ];
    }

    /**
     * Get user roles
     *
     * @return array
     */
    protected function _getIriOptions()
    {
        return $this->table->GlobalUsers
            ->find('list', [
                'keyField' => fn($x) => $x['norm_iri'] ?? '',
                'valueField' => 'iri'
            ])
            ->orderAsc('norm_iri')
            ->where(['norm_iri IS NOT' => null])
            ->toArray();
    }

    /**
     * Return fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        $fields = [
            'name' => [
                'caption' => __('Name'),
                'help' => __('The full name of the user.')
            ],

            'acronym' => [
                'caption' => __('Acronym'),
                'help' => __('The first letters of the name in upper case.')
            ],

            'userrole' => [
                'caption' => __('User role'),
                'type' => 'select',
                'options' => $this->userroleOptions,
                'action' => ['edit', 'view', 'add']
            ],

            'usertype' => [
                'caption' => __('User type'),
                'action' => ['edit', 'add'],
                'type' => 'select',
                'options' => $this->usertypeOptions
            ],

            'norm_iri' => [
                'caption' => __('IRI fragment'),
                'action' => ['edit', 'add'],
                'type' => 'select',
                'empty' => true,
                'options' => $this->iriOptions,
                'help' => __('The IRI fragment is used to match project users to general Epigraf users. '
                    . ' The IRI identifies the user within the universe and should not be changed later.')

            ],

            'iri_path' => [
                'caption' => __('IRI path'),
                'action' => ['view'],
                'format' => 'iri'
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

        return $fields;
    }
}
