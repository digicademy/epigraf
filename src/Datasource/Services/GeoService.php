<?php

namespace App\Datasource\Services;

/*
 * Geocoding service
 *
* ### Example
 *
* https://epigraf.inschriften.net/services/geo?q=Greifswald+%E2%80%BA+Baderstr.+1&limit=5&format=json&addressdetails=1
* translates to
* https://nominatim.openstreetmap.org/search?q=Greifswald+%E2%80%BA+Baderstr.+1&limit=5&format=json&addressdetails=1
*
* ### Query Parameters
* - q: Query string
* - limit: Limit the number of results
* - format: Output format (json)
* - addressdetails: Include address details
*
*/
class GeoService extends BaseService
{
    public string $serviceKey = 'geo';

    public array $config = [
        'base_url' => 'https://nominatim.openstreetmap.org/',
        'useragent' => 'Epigraf/5.0 (github.com/digicademy/epigraf)'
    ];

    public array $queryOptions = ['q', 'limit', 'format', 'addressdetails'];

    public $proxyMode = 'json';


}
