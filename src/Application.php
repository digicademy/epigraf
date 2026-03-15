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

namespace App;

use App\Authentication\Identifier\TokenFallbackIdentifier;
use App\Policy\ControllerPolicy;
use Authentication\Identifier\PasswordIdentifier;
use Authorization\AuthorizationService;
use Authorization\AuthorizationServiceProviderInterface;
use Authorization\Middleware\AuthorizationMiddleware;
use Authorization\Policy\ResolverInterface;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Datasource\FactoryLocator;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\ORM\Locator\TableLocator;
use Cake\Http\Middleware\HttpsEnforcerMiddleware;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\Router;
use Rest\Error\Middleware\RestAnswerMiddleware;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication implements AuthenticationServiceProviderInterface,  AuthorizationServiceProviderInterface
{
    /**
     * Load all the application configuration and bootstrap logic.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        // Call parent to load bootstrap from files.
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        }
        else {
            FactoryLocator::add(
                'Table',
                (new TableLocator())->allowFallbackClass(false)
            );
        }

        $this->addPlugin('Authentication');
        $this->addPlugin('Authorization');

        /*
         * Only try to load DebugKit in development mode
         * Debug Kit should not be installed on a production system
         */
//        if (Configure::read('debug')) {
//            Configure::write('DebugKit.panels', ['DebugKit.History' => false, 'DebugKit.Variables' => false]);
//            $this->addPlugin('DebugKit');
//        }

        // Load more plugins here
        $this->addPlugin('Rest');
        $this->addPlugin('Files');
        $this->addPlugin('Widgets');
        $this->addPlugin('Batch');
        $this->addPlugin('Epi', ['bootstrap' => false, 'routes' => true, 'autoload' => true]);
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error'), $this))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]));

        // Redirect to https
        if (Configure::read('enforceHttps', true)) {
            $middlewareQueue
                ->add(
                    new HttpsEnforcerMiddleware([
                        'redirect' => true,
                        'disableOnDebug' => false,
                        'statusCode' => 302,
                    ])
                );
        }

        $middlewareQueue
            // Add routing middleware.
            // If you have a large number of routes connected, turning on routes
            // caching in production could improve performance. For that when
            // creating the middleware instance specify the cache config name by
            // using it's second constructor argument:
            // `new RoutingMiddleware($this, '_cake_routes_')`
            ->add(new RoutingMiddleware($this))

            // Catch RestAnswerExceptions and create an API response or redirect to target
            ->add(new RestAnswerMiddleware(Configure::read('Error')))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/4/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware())

            // Authentication and authorization
            // https://book.cakephp.org/authentication
            // https://book.cakephp.org/authorization
            ->add(new AuthenticationMiddleware($this))
            ->add(new AuthorizationMiddleware($this));

        return $middlewareQueue;
    }

    /**
     * Register application container services.
     *
     * @param \Cake\Core\ContainerInterface $container The Container to update.
     * @return void
     * @link https://book.cakephp.org/4/en/development/dependency-injection.html#dependency-injection
     */
    public function services(ContainerInterface $container): void
    {
    }

    /**
     * Returns a service provider instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @param \Psr\Http\Message\ResponseInterface $response Response
     * @return \Authentication\AuthenticationServiceInterface
     */
    public function getAuthenticationService(ServerRequestInterface $request) : AuthenticationServiceInterface
    {
        $service = new AuthenticationService();

        $loginConfig = Configure::read('Logins', ['token' => true, 'form' => true]);

        // Login via a username provided in an environment variable (mod_auth_openidc)
        if (!empty($loginConfig['remote'])) {

            // For non-token requests, save to session
            if (!$request->is('token')) {

                $service->loadIdentifier(
                    'Authentication.Password',
                    [
                        PasswordIdentifier::CREDENTIAL_USERNAME => 'username',
                        PasswordIdentifier::CREDENTIAL_PASSWORD => 'password',
                        'identityClass' => \App\Model\Entity\User::class,
                    ]
                );

                $service->loadAuthenticator('Authentication.Session', [
                    'identifiers' => ['Authentication.Password'],
                ]);
            }

            // Configure a token identifier that maps an env var to the username column
            $service->loadIdentifier('Authentication.Token', [
                'tokenField' => 'username',
                'dataField' => $loginConfig['remote'],
                'identityClass' => \App\Model\Entity\User::class,
            ]);

            // Choose which environment variables exposed by the
            // authentication provider are used to authenticate.
            $service->loadAuthenticator('Authentication.Environment', [
                'fields' => [
                    $loginConfig['remote']
                ]
            ]);
        }

        // Access token authentication for API requests
        if (!empty($loginConfig['token']) && $request->is('token')) {

            $service->loadIdentifier(TokenFallbackIdentifier::class, [
                'tokenField' => 'accesstoken',
                'hashAlgorithm' => 'sha256',
                'identityClass' => \App\Model\Entity\User::class,
            ]);

            $service->loadAuthenticator('Authentication.Token', [
                'identifiers' => ['Authentication.Token'],
                'queryParam' => 'token',
                'header' => 'Authorization',
                'tokenPrefix' => 'Token'
            ]);
        }

        // Form based authentication
        elseif (!empty($loginConfig['form'])) {

            // Configure unauthenticated redirect
            $service->setConfig([
                'unauthenticatedRedirect' => Router::url(['plugin'=>false, 'controller' => 'Users', 'action' => 'login']),
                'queryParam' => 'redirect'
            ]);

            // Load identifiers
            $service->loadIdentifier(
                'Authentication.Password',
                [
                    PasswordIdentifier::CREDENTIAL_USERNAME => 'username',
                    PasswordIdentifier::CREDENTIAL_PASSWORD => 'password',
                    'identityClass' => \App\Model\Entity\User::class,
                ]
            );

            $service->loadAuthenticator('Authentication.Session',  [
                'identifiers' => ['Authentication.Password'],
            ]);

            $service->loadAuthenticator('Authentication.Form', [
                'identifiers' => ['Authentication.Password'],
                'fields' => [
                    PasswordIdentifier::CREDENTIAL_USERNAME => 'username',
                    PasswordIdentifier::CREDENTIAL_PASSWORD => 'password'
                ],
                'loginUrl' => Router::url([
                    'prefix' => false,
                    'plugin' => null,
                    'controller' => 'Users',
                    'action' => 'login',
                ]),
            ]);

        }

        return $service;
    }

    public function getAuthorizationService(ServerRequestInterface $request): \Authorization\AuthorizationServiceInterface
    {
        // Custom policy resolver that returns ControllerPolicy for controller objects
        $policyResolver = new class implements ResolverInterface {

            /**
             * Resolve a policy instance for controllers
             *
             * @param mixed $resource
             * @return object|null
             */
            public function getPolicy($resource)
            {
                if (is_object($resource) && is_subclass_of($resource, \Cake\Controller\Controller::class)) {
                    return new ControllerPolicy($resource);
                }
                return null;
            }
        };

        return new AuthorizationService($policyResolver);
    }
    /**
     * Bootstrapping for CLI application.
     *
     * That is when running commands.
     *
     * @return void
     */
    protected function bootstrapCli(): void
    {
        $this->addOptionalPlugin('Bake');
//        $this->addPlugin('Migrations');
    }
}
