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
    public static function create($service, $config=[])
    {
        switch ($service) {
            case 'llm':
                return new LlmService($config);
            case 'geo':
                return new GeoService($config);
            case 'reconcile':
                return new ReconcileService($config);
            default:
                throw new \InvalidArgumentException("Unknown service type: {$service}");
        }
    }
}
