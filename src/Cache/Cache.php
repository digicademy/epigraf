<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Cache;

/**
 * Init cache configurations
 *
 */
class Cache extends \Cake\Cache\Cache
{
    /**
     * Derives a cache configuration from a default cache configuration.
     *
     * @param string $name
     * @param string $scope Name of the cache configuration to use as a base.
     * @return void
     */
    static public function initCache($name, $scope = 'views')
    {
        $cacheConfig = Cache::getConfig($name);
        if (empty($cacheConfig)) {
            $cacheConfig = array_merge(
                Cache::getConfig($scope),
                ['prefix' => $name . '_']
            );
            Cache::setConfig($name, $cacheConfig);
        }
    }

    /**
     * Clears the cache for a given cache configuration.
     *
     * @param string $name
     * @return void
     */
    static public function clearCache($name)
    {
        $cacheConfig = Cache::getConfig($name);
        if (!empty($cacheConfig)) {
            Cache::clear($name);
        }

    }

}
