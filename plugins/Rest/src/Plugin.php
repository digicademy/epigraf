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

namespace Rest;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequest;
use Cake\Routing\RouteBuilder;
use Rest\Middleware\RateLimitMiddleware;

/**
 * REST plugin
 */
class Plugin extends BasePlugin
{
    /**
     * Load all the plugin configuration and bootstrap logic.
     *
     * The host application is provided as an argument. This allows you to load
     * additional plugin dependencies, or attach events.
     *
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        ServerRequest::addDetector(
            'api',
            function ($request) {
                $contentType = explode(';', $request->contentType() ?? '')[0] ?? '';
                $contentType = empty($contentType) ? $request->accepts()[0] ?? '' : $contentType;
                $ext = $request->getParam('_ext');

                return in_array($ext,API_EXTENSIONS)
                    || in_array($contentType, API_CONTENTTYPES);
            }
        );

        // Handle API redirects
//        $this->getEventManager()->on('Controller.beforeRedirect', function ($event) {
//
//            if ($this->request->is('api')) {
//                $event->stop();
//            }
//        });
    }

    /**
     * Add routes for the plugin.
     *
     * If your plugin has many routes and you would like to isolate them into a separate file,
     * you can create `$plugin/config/routes.php` and delete this method.
     *
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        parent::routes($routes);
    }

    /**
     * Add middleware for the plugin.
     *
     * @param \Cake\Http\MiddlewareQueue $middleware The middleware queue to update.
     * @return \Cake\Http\MiddlewareQueue
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {

        $middlewareQueue->add(new RateLimitMiddleware());
        return $middlewareQueue;
    }
}
