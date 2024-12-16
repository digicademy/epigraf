<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Exception;
use HttpException;

/**
 * An authentication adapter for AuthComponent
 *
 * Provides the ability to authenticate using Token
 *
 * ```
 *    $this->Auth->setConfig('authenticate', [
 *        'FOC/Authenticate.Token' => [
 *            'parameter' => '_token',
 *            'header' => 'X-MyApiTokenHeader',
 *            'userModel' => 'Users',
 *            'scope' => ['User.active' => 1]
 *            'fields' => [
 *                'token' => 'public_key',
 *            ],
 *             'continue' => true
 *        ]
 *    ]);
 * ```
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * see https://github.com/FriendsOfCake/Authenticate
 */
class TokenAuthenticate extends BaseAuthenticate
{

    /**
     * Constructor
     *
     * Initialize settings for this object
     *
     * - `parameter` The url parameter name of the token.
     * - `header` The token header value.
     * - `userModel` The model name of the User, defaults to Users.
     * - `fields` The fields to use to identify a user by. Make sure `'token'` has
     *    been added to the array
     * - `scope` Additional conditions to use when looking up and authenticating users,
     *    i.e. `['Users.is_active' => 1].`
     * - `contain` Extra models to contain.
     * - `continue` Continue after trying token authentication or just throw the
     *   `unauthorized` exception.
     * - `unauthorized` Exception name to throw or a status code as an integer.
     *
     * @param \Cake\Controller\ComponentRegistry $registry The Component registry used on this request
     * @param array $config
     *
     * @throws Exception If header is not present
     */
    public function __construct(ComponentRegistry $registry, $config)
    {
        $this->_registry = $registry;

        $this->setConfig([
            'parameter' => '_token',
            'header' => 'X-ApiToken',
            'fields' => ['token' => 'token', 'password' => 'password'],
            'continue' => false,
            'unauthorized' => 'Cake\Http\Exception\UnauthorizedException'
        ]);

        $this->setConfig($config);

        if (empty($this->_config['parameter']) &&
            empty($this->_config['header'])
        ) {
            throw new Exception(__d(
                'authenticate',
                'You need to specify token parameter and/or header'
            ));
        }
    }

    /**
     * Implemented because CakePHP forces you to.
     *
     * @param ServerRequest $request The request object
     * @param Response $response The response object
     *
     * @return false|mixed always false
     */
    public function authenticate(ServerRequest $request, Response $response)
    {
        return $this->getUser($request);
    }

    /**
     * If unauthenticated, try to authenticate and respond.
     *
     * @param ServerRequest $request The request object
     * @param Response $response The response object
     *
     * @return false
     * @throws HttpException Or the one specified using $settings['unauthorized']
     */
    public function unauthenticated(ServerRequest $request, Response $response)
    {
        if ($this->_config['continue']) {
            return false;
        }
        if (is_string($this->_config['unauthorized'])) {
            // @codingStandardsIgnoreStart
            throw new $this->_config['unauthorized'];
            // @codingStandardsIgnoreEnd
        }
        $message = __d('authenticate', 'You are not authenticated.');
        throw new HttpException($message, $this->_config['unauthorized']);
    }

    /**
     * Get token information from the request.
     *
     * @param ServerRequest $request The request object
     *
     * @return false|mixed Either false or an array of user information
     */
    public function getUser(ServerRequest $request)
    {
        if (!empty($this->_config['header'])) {

            $token = $request->getHeaderLine($this->_config['header']);
            if ($token) {
                return $this->_findUser($token);
            }
        }
        if (!empty($this->_config['parameter']) &&
            !empty($request->getQuery($this->_config['parameter']))
        ) {
            $token = $request->getQuery($this->_config['parameter']);
            return $this->_findUser($token);
        }
        return false;
    }

    /**
     * Delete session token.
     *
     * @param Event $event
     * @param array $user
     * @return false
     */
    public function logout(Event $event, array $user)
    {
        if ($user) {
            $tokentable = TableRegistry::getTableLocator()->get('Epi.Token');
            if ($tokentable) {
                return $tokentable->deleteSessionToken($user->accesstoken);
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }

    }

    /**
     * Find a user record by access token
     *
     * @param string $username The access token
     * @param string $password Unused
     * @return array|false Either false on failure, or an array containing the user record data
     */
    protected function _findUser($username, $password = null)
    {
        $userModel = $this->_config['userModel'];
        list($plugin, $model) = pluginSplit($userModel);
        $fields = $this->_config['fields'];

        $conditions = [$model . '.' . $fields['token'] => $username];
        if (!empty($this->_config['scope'])) {
            $conditions = array_merge($conditions, $this->_config['scope']);
        }
        $table = TableRegistry::getTableLocator()->get($userModel)->find('auth');
        if ($this->_config['contain'] ?? []) {
            $table = $table->contain($this->_config['contain']);
        }

        $result = $table
            ->where($conditions)
            ->enableHydration(false)
            ->first();

        if (empty($result)) {
            return false;
        }

        unset($result[$fields['password']]);
        return $result;
    }
}

