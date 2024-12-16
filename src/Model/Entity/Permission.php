<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity;

use App\Model\Table\PermissionsTable;
use App\Model\Table\UsersTable;

/**
 * Permission Entity
 *
 * # Database fields (without inherited fields)
 * @property int $user_id
 * @property int $user_session
 * @property string $user_role
 * @property string $user_request
 * @property string $entity_type
 * @property string $entity_name
 * @property string $entity_id
 * @property string $permission_type
 * @property string $permission_name
 * @property string $permission_expires
 *
 * # Virtual fields (without inherited fields)
 * @property string $username
 *
 * # Relations
 * @property PermissionsTable $table
 * @property User $user
 */
class Permission extends BaseEntity
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
        '*' => true,
        'id' => false
    ];

    /**
     * Get username
     *
     * @return string
     */
    protected function _getUsername()
    {
        return empty($this->user) ? '' : $this->user->username;
    }

    /**
     * Return fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        $users = $this->table->Users
            ->find('list')
            ->orderAsc('username')
            ->toArray();

        $endpoints = array_merge(['*'], PermissionsTable::getEndpointOptions());
        $endpoints = array_combine($endpoints, $endpoints);

        $fields = [
            'user_id' => [
                'caption' => __('User'),
                'id' => 'user_id',
                'type' => 'select',
                'options' => $users,
                'empty' => true,
            ],

            'user_role' => [
                'caption' => __('Role'),
                'id' => 'user_role',
                'type' => 'select',
                'empty' => true,
                'options' => PermissionsTable::$userRoles
            ],

            // TODO: rename to endpoint_request ?
            'user_request' => [
                'caption' => __('Requested by'),
                'id' => 'user_request',
                'type' => 'select',
                'options' => PermissionsTable::$requestTypes
            ],

            // TODO: rename to permissiontype?
            'permission_type' => [
                'caption' => __('Permission Type'),
                'id' => 'permission_type',
                'type' => 'select',
                'options' => PermissionsTable::$permissionTypes,
            ],

            // TODO: rename to endpoint_name ?
            'permission_name' => [
                'caption' => __('Endpoint name'),
                'empty' => true,
                'options' => $endpoints
            ],

            'entity_type' => [
                'caption' => __('Entity Type'),
                'id' => 'entity_type',
                'type' => 'select',
                'empty' => true,
                'options' => PermissionsTable::$entityTypes
            ],

            'entity_name' => [
                'caption' => __('Entity Name')
            ],

            'entity_id' => [
                'caption' => __('Entity ID'),
                'type' => 'text'
            ],

            'created' => [
                'caption' => __('Created on'),
                'action' => 'view'
            ],

            'modified' => [
                'caption' => __('Last modified'),
                'action' => 'view'
            ],

            // TODO: rename to 'expires'
            'permission_expires' => [
                'caption' => __('Expires'),
                'id' => 'permission_expires'
            ]
        ];

        return $fields;
    }

}
