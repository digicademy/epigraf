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
use App\Utilities\Converters\Attributes;
use App\Utilities\Converters\Objects;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use Cake\ORM\TableRegistry;

/**
 * User Entity
 *
 * # Database fields (without inherited fields)
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $name
 * @property string $acronym
 * @property string $contact
 * @property string $accesstoken
 * @property string $settings
 * @property int $role
 * @property int $databank_id
 * @property int $pipeline_article_id
 * @property int $pipeline_book_id
 *
 * # Virtual fields (without inherited fields)
 * @property string $sqlUsername
 * @property string $problems
 *
 * # Relations
 * @property UsersTable $table
 * @property Databank $databank
 * @property array $grants
 * @property array $permissionsByRole
 * @property array $permissionsWithActions
 *
 */
class User extends BaseEntity
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
        'id' => false,
        'username' => false,
        'role' => false,
        'accesstoken' => false,
        'norm_iri' => false
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
        'accesstoken'
    ];

    protected $_fields_formats = [
        'accesstoken' => 'token',
        'lastaction' => 'timeago'
    ];

    /**
     * Setter for virtual password field
     *
     * @param $password
     *
     * @return false|string
     */
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }


    /**
     * Getter of virtual sql_username field
     *
     * @return string
     */
    protected function _getSqlUsername()
    {
        return 'epi_' . $this->username;
    }


    /**
     * Grant SQL database access
     *
     * @param $databasename
     *
     * @return \Cake\Database\StatementInterface
     */
    public function grantDesktopAccess($databasename)
    {
        $db = ConnectionManager::get('default');
        return $db->execute('GRANT SELECT,INSERT,UPDATE,DELETE,LOCK TABLES ON ' . $databasename . '.* TO "epi_' . $this->username . '" REQUIRE SSL');
    }

    /**
     * Revoke SQL access to all databases
     *
     * @return bool
     */
    public function revokeDesktopAccess()
    {
        $db = ConnectionManager::get('default');

        $grants = $db->execute('SELECT * FROM mysql.db WHERE User = "epi_' . $this->username . '"')->fetchAll('num');
        $databases = Hash::extract($grants, '{n}.1');

        $num = 0;
        foreach ($databases as $database) {
            $num += !empty($db->execute('REVOKE SELECT,INSERT,UPDATE,DELETE,LOCK TABLES ON `' . $database . '`.* FROM "epi_' . $this->username . '"'));
        }

        return ($num == count($databases));
    }

    /**
     * Remove web and api access of the user to the database
     *
     * @return mixed
     */
    public function removeDatabanksPermissions()
    {
        $permissionTable = $this->fetchTable('Permissions');
        $permission = ['user_id' => $this->id, 'entity_type' => 'databank'];
        return ($permissionTable->removePermission($permission));
    }

    /**
     * Get database access status of the user
     *
     * @return array
     */
    protected function _getPermissionsByRole()
    {
        $access = [];

        // Initialise full array for devel and admin users
//        if (in_array($this->currentUserRole, ['devel', 'admin'])) {
//            $databanks = $this->table->getDatabase()->table->find('list');
//            foreach ($databanks as $id => $name) {
//                $access[$name] = ['id' => $id, 'database' => $name];
//                foreach (PermissionsTable::$userRoles as $role => $roleCaption) {
//                    $access[$name][$role] = [];
//                }
//            }
//
//            if (in_array($this->userrole, ['admin', 'devel'] )) {
//                $access['*'] = ['id' => '*', 'database' => '*'];
//                foreach (PermissionsTable::$userRoles as $role => $roleCaption) {
//                    $access['*'][$role] = [];
//                }
//            }
//        }

        // Permissions table
        foreach ($this->permissions as $permission) {
            if (($permission->entity_type === 'databank') && ($permission->permission_type === 'access')) {
                // TODO: check 'permission_expires'
                // TODO: use * as default request?
                $entityName = $permission->entity_name ?? '*';
                $userRequest = $permission->user_request ?? 'web';
                $userRole = Attributes::cleanOption($permission->user_role ?? '',
                    array_keys(PermissionsTable::$userRoles), $this->role);

                $access[$entityName][$userRole][] = $userRequest;
                $access[$entityName]['database'] = $permission->entity_name;
                $access[$entityName]['id'] = $access[$permission->entity_name]['id'] ?? $permission->entityId ?? '*';
            }
        }

        // SQL grants
        foreach ($this->grants as $grant) {
            $entity_name = $grant['entity_name'];
            $userRole = $grant['user_role'];
            $access[$entity_name][$userRole][] = 'desktop';
            $access[$entity_name]['database'] = $entity_name;
        }

        return $access;
    }

    /**
     * Get SQL grants of the current user
     *
     * @return array An array of grants with the columns 'permission_name', 'entity_name', 'user_name', 'user_role'.
     */
    protected function _getGrants()
    {
        $db = ConnectionManager::get('default');

        $user = $db->execute('SELECT * from mysql.user WHERE user="epi_' . $this->username . '"')->fetchAll('assoc');
        if (empty($user)) {
            return [];
        }

        $grants = $db->execute('SHOW GRANTS FOR epi_' . $this->username)->fetchAll('num');
        $grants = Objects::extract($grants, '*.0');

        $grantTable = [];
        foreach ($grants as $grant) {
            if (preg_match('/GRANT (.+?) ON `([^`]+)`\.\* TO `([^`]+)`@`([^`]+)`/', trim($grant), $matches)) {
                $grantTable[] = [
                    'entity_type' => 'databank',
                    'entity_name' => $matches[2],
                    'user_name' => $matches[3],
                    'user_role' => ($matches[1] === 'SELECT, INSERT, UPDATE, DELETE, LOCK TABLES') ? 'author' : 'devel',
                    'user_request' => 'sql',
                    'permission_type' => 'access'
                ];
            }
        }
        return $grantTable;
    }

    protected function _getPermissionsWithActions()
    {
        $webPermissions = $this->permissions;
        foreach ($webPermissions as $i => $permission) {
            $webPermissions[$i]['actions'] = [
                'revoke' => [
                    'title' => 'Revoke',
                    'url' => ['controller' => 'permissions', 'action' => 'delete', $permission->id],
                    'options' => ['class' => 'doc-item-remove button tiny popup']
                ]
            ];
        }

        $sqlPermissions = $this->grants;
        foreach ($sqlPermissions as $i => $permission) {
            $sqlPermissions[$i]['actions'] = [
                'revoke' => [
                    'title' => __('Revoke'),
                    'url' => ['action' => 'revoke', $this->id, '{entity_name}', '{user_request}', '{user_role}'],
                    'options' => ['class' => 'doc-item-remove button tiny popup']
                ]
            ];
        }
        return array_merge($webPermissions, $sqlPermissions);
    }

    /**
     * Check whether the user has the right permissions
     *
     * @return string[] Error messages
     */
    protected function _getProblems()
    {
        $errors = [];

        if (empty($this->norm_iri)) {
            $errors[] = __('The IRI is missing.');
        }

        if (empty($this->databank_id)) {
            $errors[] = __('No default database selected');
        }
        else {
            // TODO: implement hasPermission method
            $dbPermissions = array_filter($this->permissions, function ($x) {
                return ($x->entity_type === 'databank') &&
                    ($x->entity_id === $this->databank_id) &&
                    ($x->permission_type === 'access');
            });
            if (empty($dbPermissions)) {
                $errors[] = __('No access to default database. Did you forget to grant access?');
            }
        }

        return $errors;
    }

    /**
     * Returns fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        if ($this->isNew()) {
            $databanks = $this->fetchTable('Databanks')
                ->find()
                ->all()
                ->combine('id', 'caption');
        }
        else {
            $databanks = $this->fetchTable('Databanks')
                ->find('allowedBy', ['user' => $this])
                ->all()
                ->combine('id', 'caption');
        }

        $pipelines = $this->fetchTable('Pipelines')->find('list')->toArray();

        $fields = [
            'username' => [
                'caption' => __('Username'),
                'help' => __('The username should contain the real name of the person. Only alphanumeric characters are allowed. Maximum length is 50 characters.')
            ],

            'role' => [
                'caption' => __('Role'),
                'options' => PermissionsTable::$userRoles
            ],

            'name' => [
                'caption' => __('Name'),
                'help' => __('First and last name.')
            ],

            'acronym' => [
                'caption' => __('Acronym'),
                'help' => __('First letters of the name.')
            ],

            'email' => [
                'caption' => __('Email address'),
                'help' => __('Please provide an email address to contact the user.')
            ],

            'contact' => [
                'caption' => __('Contact'),
                'help' => __('Please provide additional information about how to contact the user, for example the affiliation.')
            ],

            'password' => [
                'caption' => __('Password'),
                'autocomplete' => "off",
                'action' => ['edit', 'add'],
                'display' => 'password',
                'help' => __("Please enter a strong password and remember it well. The password can't be restored. If you loose it, you have to enter a new password.")
            ],

            'accesstoken' => [
                'caption' => 'Access token',
                'autocomplete' => "off",
                'action' => ['view'],
                'display' => 'password',
                'help' => __('The access token is used for API access and for access from EpiDesktop. Leave empty to automatically create an access token.')
            ],

            'databank_id' => [
                'options' => $databanks,
                'empty' => true,
                'caption' => __('Default Database'),
                'extract' => 'databank.caption',
                'help' => __('Select the database that should open after you login.')
            ],

            'pipeline_article_id' => [
                'options' => $pipelines,
                'empty' => true,
                'caption' => __('Default Article Pipeline'),
                'extract' => 'article_pipeline.name',
                'help' => __('Only Epigraf-Desktop users: Select the pipeline that exports single articles.')
            ],

            'pipeline_book_id' => [
                'options' => $pipelines,
                'empty' => true,
                'caption' => __('Default Book Pipeline'),
                'extract' => 'book_pipeline.name',
                'help' => __('Only Epigraf-Desktop users: Select the pipeline that exports a complete book.')
            ],

            'settings.ui.locale' => [
                'options' => UsersTable::$locales,
                'empty' => true,
                'caption' => __('Language'),
                'type' => 'select',
                'help' => __('The language in which Epigraf talks to you.')
            ],
            'settings.ui.theme' => [
                'options' => UsersTable::$themes,
                'empty' => __('Default'),
                'caption' => __('Theme'),
                'type' => 'select',
                'help' => __('The user interface appearance.')
            ],

            'norm_iri' => [
                'caption' => __('IRI fragment'),
                'action' => ['edit', 'add'],
                'help' => __(' Usually the IRI equals the username. The IRI is used to match users in the main database to users in the project databases.')
            ],

            'iri_path' => [
                'caption' => __('IRI path'),
                'format' => 'iri',
                'action' => ['view']
            ],

            'created' => [
                'caption' => __('Created'),
                'action' => 'view'
            ],

            'modified' => [
                'caption' => __('Modified'),
                'action' => 'view'
            ],
        ];

        return $fields;
    }
}
