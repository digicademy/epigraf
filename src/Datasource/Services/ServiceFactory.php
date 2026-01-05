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

namespace App\Datasource\Services;

class ServiceFactory
{
    protected static $_instances = [];

    public static function create($service, $config=[])
    {
        switch ($service) {
            case 'llm':
                return new LlmService($config);
            case 'geo':
                return new GeoService($config);
            case 'reconcile':
                return new ReconcileService($config);
            case 'http':
                return new HttpService($config);
            case 'img':
                return new ImageService($config);
            default:
                throw new \InvalidArgumentException("Unknown service type: {$service}");
        }
    }

    /**
     * Get a service instance
     *
     * Service instances are cached in the static $_instances array.
     * The method will only create a new instance if it does not exist.
     *
     * @param string $service The service identifier
     * @param boolean $proxyMode Enable or disable proxy mode
     * @return BaseService
     */
    public static function get($service, $proxyMode = false)
    {
        if (empty(self::$_instances[$service])) {
            self::$_instances[$service] = ServiceFactory::create($service);
        }
        $apiService = self::$_instances[$service];
        $apiService->proxyMode = $proxyMode;

        return $apiService;
    }
}
