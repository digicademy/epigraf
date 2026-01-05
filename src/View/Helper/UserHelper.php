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

namespace App\View\Helper;

use App\Model\Table\PermissionsTable;
use Cake\View\Helper;

/**
 * User helper
 *
 * Get user role and access permissions
 *
 */
class UserHelper extends Helper
{

    /**
     * Load helpers
     *
     * @var string[]
     */
    public $helpers = [];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];


    /**
     * Check whether the current user has the role
     *
     * @param array $roles Allowed roles
     * @return bool
     */
    public function hasRole($roles = []): bool
    {
        $role = $this->_View->get('user_role') ?? 'guest';
        $allowed = array_merge(($roles ?? ['*']), ['admin', 'devel']);
        return in_array($role, $allowed) || in_array('*', $allowed);
    }

    /**
     * Check whether the active user has permission to the endpoint
     * on the selected project database
     *
     * TODO: implement a plugin
     * TODO: check entity_id
     *
     * ### Options
     * - roles The roles that are allowed to access the endpoint.
     *         By default, all roles are allowed.
     * - entity_name The name of the entity to check permissions for.
     *               Used for view permissions (e.g. '*.template.map').
     *
     *
     * @param array $url
     * @param array $options
     * @return bool
     */
    public function hasPermission($url, $options = []) {
        // Check role option
        $role = $this->_View->get('user_dbrole') ?? 'guest';
        $allowed = array_merge(($options['roles'] ?? ['*']), ['admin', 'devel']);
        if (!(in_array($role, $allowed) | in_array('*', $allowed))) {
            return false;
        }

        if (!is_array($url)) {
            return true;
        }

        // Check hardwired permissions ($authorized property of controllers)
        $request = $this->_View->getRequest();
        $endpoint = [
            'plugin' => strtolower($request->getParam('plugin') ?? ''),
            'controller' => strtolower($request->getParam('controller') ?? ''),
            'action' => strtolower($request->getParam('action') ?? '')
        ];
        $endpoint = array_merge($endpoint, $url);

        $endpoint['plugin'] = is_string($endpoint['plugin']) ? $endpoint['plugin'] : '';
        $endpoint['scope'] = strtolower($endpoint['plugin']) === 'epi' ? 'epi' : 'app';
        unset($endpoint['plugin']);
        unset($endpoint['?']);
        if (empty($options['entity_name']) && PermissionsTable::getEndpointHasRole($endpoint, $role, 'web')) {
            return true;
        }

        // Check database permissions
        // 3. Check permission database
        if ($endpoint['scope'] === 'epi') {
            $database = $request->getParam('database');
        } else {
            $database = null;
        }

        $permissionOptions = [];
        if (!empty($options['entity_name'])) {
            $permissionOptions['entity_name'] = $options['entity_name'];
        }

        return PermissionsTable::hasGrantedPermission(
            $this->_View->get('user', []),
            $database,
            $endpoint['controller'],
            $endpoint['action'],
            'web',
            $permissionOptions
        );
    }
}
