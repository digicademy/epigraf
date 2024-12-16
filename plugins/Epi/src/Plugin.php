<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\MiddlewareQueue;

class Plugin extends BasePlugin
{
    /**
     * Add middleware callback
     *
     * @param MiddlewareQueue $middleware
     * @return MiddlewareQueue
     */
    public function middleware(MiddlewareQueue $middleware): MiddlewareQueue

    {
        // Add middleware here.
        $middleware = parent::middleware($middleware);

        return $middleware;
    }

    /**
     * Get console command callback
     *
     * @param CommandCollection $commands
     * @return CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        // Add console commands here.
        $commands = parent::console($commands);

        return $commands;
    }

    /**
     * bootstrap callback
     *
     * Add constants, load configuration defaults
     * By default will load `config/bootstrap.php` in the plugin.
     *
     * @param PluginApplicationInterface $app
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);
    }

    /**
     * routes callback
     *
     * By default will load `config/routes.php` in the plugin.
     *
     * @param $routes
     * @return void
     */
    public function routes($routes): void
    {
        parent::routes($routes);
    }
}
