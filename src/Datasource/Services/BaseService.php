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

use App\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Client;

abstract class BaseService {

    /**
     * A key to identify the service
     *
     * Used for caching
     *
     * @var string
     */
    public string $serviceKey = 'base';

    /**
     * Default configuration
     *
     * @var array
     */
    public array $config = [
        'useragent' => 'Epigraf/5.0 (github.com/digicademy/epigraf)'
    ];


    /**
     * Allowed query parameters
     *
     * @var array
     */
    public array $queryOptions = [];

    /**
     * In proxy mode, the query answer is returned as is.
     *
     * @var bool|string $proxyMode The return format (json)
     */
    public $proxyMode = false;

    /**
     * The client to query the service
     *
     * @var Client
     */
    public $client = null;


    public function __construct($config) {

        $serviceConfig = Configure::read('Services.' . $this->serviceKey, []);
        $config = array_merge($this->config, $config);
        $this->config = array_merge($config, $serviceConfig);

        $this->client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => ($this->config['useragent'])
            ]
        ]);
    }

    /**
     * Remove all keys not listed in $this->parameters
     *
     * @param array $options
     * @return array
     */
    public function sanitizeParameters(array $options): array {
        return array_intersect_key($options, array_flip($this->queryOptions));
    }

    /**
     * Query the service
     *
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function query($path = 'search', array $options = []): array
    {
        $baseUrl = $this->config['base_url'];
        if (empty($baseUrl)) {
            throw new \Exception('No base URL configured');
        }
        $endpointUrl = rtrim($baseUrl, '/') . '/' . $path;

        $options = $this->sanitizeParameters($options);

        // Try reading from cache
        $cacheKey = $endpointUrl . '?' . http_build_query($options, '', '&', PHP_QUERY_RFC3986);
        $answer = Cache::read($this->serviceKey . ':' . $cacheKey,'services');
        if (is_array($answer) && (($answer['status'] ?? 500) === 200)) {
            return $answer;
        }

        // Query the service
        $response = $this->client->get($endpointUrl, $options);
        $answer = [
            'response' => $response->getJson(),
            'status' => $response->getStatusCode()
        ];

        // Save to cache
        if ($response->getStatusCode() === 200) {
            Cache::write($this->serviceKey . ':' . $cacheKey, $answer, 'services');
        }

        return $answer;
    }

}
