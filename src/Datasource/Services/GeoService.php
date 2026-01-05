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

use App\Utilities\Converters\Geo;

class GeoService extends BaseService
{
    public string $serviceKey = 'geo';

    public array $config = [
        'base_url' => 'https://nominatim.openstreetmap.org/',
        'useragent' => 'Epigraf/5.0 (github.com/digicademy/epigraf)',
        'accept' => 'application/json',
    ];

    public array $queryOptions = ['q', 'limit', 'format', 'addressdetails', 'extratags'];

    public $proxyMode = 'json';

    /**
     * Query the service
     *
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function query($path = 'search', array $options = []): array
    {
        if (!empty($this->proxyMode)) {
            return parent::query($path, $options);
        }

        return $this->reconcile($path, $options);
    }

    /**
     * Translate the query and the result to reconciliation format
     *
     * TODO: Implement this method for all service classes?

     * See BaseEntity::reconcile() for details.
     *
     * Note the limitation from the docs:
     *   "Place IDs are assigned sequentially during Nominatim data import.
     *   The ID for a place is different between Nominatim installation (servers) and
     *   changes when data gets reimported. Therefore it cannot be used as a
     *   permanent id and shouldn't be used in bug reports."
     *   (https://nominatim.org/release-docs/latest/api/Details/; 2025-05-18)
     *
     * @param string $path
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function reconcile($path, array $options = []): array
    {
        $path = 'search';
        $options = [
            'q' => $options['q'] ?? '',
            'limit' => $options['limit'] ?? 5,
            'format' => 'json',
            'addressdetails' => 1,
            'extratags' => 1
        ];

        $response =  parent::query($path, $options);

        $data = ['state' => $response['status'] === 200 ? 'SUCCESS' : 'ERROR'];
        if ($data['state'] === 'SUCCESS') {
            $candidates = [];
            foreach ($response['response'] as $key => $value) {
                $coord = [
                    'lat' => $value['lat'],
                    'lng' => $value['lon'],
                    'radius' => round(Geo::boxToRadius($value['boundingbox']))
                ];

                $normdata = [
                    'wd' => $value['extratags']['wikidata'] ?? null,
                    'osm' => ($value['osm_type'] ?? '') . '/' . ($value['osm_id'] ?? '')
                ];

                // TODO: add adress to description?
                $candidates[] = [
                    'id' => $value['place_id'] ?? null,
                    'name' => $value['display_name'] ?? null,
                    'value' => json_encode($coord),
                    'ids' => $normdata,
                    'score' => $value['importance'] ?? 0,
                    'match' => true,
                ];
            }
            $data['result'] = ['answers' => [0 => ['candidates' => $candidates]]];
        }
        return $data;
    }

}
