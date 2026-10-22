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
use App\Model\Entity\Databank;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Client;
use Cake\ORM\TableRegistry;
use Epi\Model\Table\BaseTable as EpiBaseTable;

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
        'accept' => 'application/json',
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
                'Accept' => $this->config['accept'] ?? $this->config['Accept'],
                'User-Agent' => $this->config['useragent'] ?? $this->config['User-Agent']
            ],
            'timeout' => $this->config['timeout'] ?? 7
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
     * Activate database
     *
     * This class does not check user permissions.
     * The permission check is performed in the AppController based on the database query parameter.
     *
     * @param string $dbname
     * @return Databank
     */
    public function setDatabank($dbname) : Databank
    {
        if (empty($dbname)) {
            throw new RecordNotFoundException(__('Database name is missing.'));
        }

        $databanks = TableRegistry::getTableLocator()->get('Databanks');

        $this->oldDatabaseName = EpiBaseTable::getDatabaseName();
//        BaseTable::setDatabase($dbname);
        return $databanks->activateDatabase($dbname);
    }

    public function resetDatabank()
    {
        if (!empty($this->oldDatabaseName)) {
            EpiBaseTable::setDatabase($this->oldDatabaseName);
        }
    }

    public function withDatabank($databaseName, $callback)
    {
        $databank = $this->setDatabank($databaseName);

        try {
            return $callback($databank);
        } finally {
            $this->resetDatabank();
        }
    }

    public function withTable($databaseName, $modelName, $callback)
    {
        return $this->withDatabank($databaseName,
            function($databank) use ($modelName, $callback) {
                $table = TableRegistry::getTableLocator()->get($modelName);
                if (!$table instanceof \App\Model\Interfaces\ExportTableInterface) {
                    throw new RecordNotFoundException(__('Table does not support data export.'));
                }
                return $callback($databank, $table);
            }
        );
    }

    /**
     * Get the merged configuration of a type from the databank
     *
     * ### Options
     * - database (string): The project database name
     * - scope (string): The type scope
     * - name (string): The type name
     *
     * @param array $options
     * @return array
     */
    public function getTypeConfiguration($options)
    {
        return $this->withDatabank(
            $options['database'] ?? '',
            function($databank) use ($options) {
                return $databank->types[$options['scope']][$options['name']]['merged'] ?? [];
            }
        );
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
        $cacheKey = md5($cacheKey);
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
