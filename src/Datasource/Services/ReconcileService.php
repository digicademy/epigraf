<?php

namespace App\Datasource\Services;

use App\Cache\Cache;
use App\Utilities\Converters\Attributes;
use App\Utilities\Converters\Objects;
use Cake\Http\Client;

/**
 * Reconcilation Service
 *
 * Supports services following the Reconciliation Service API v0.2 format
 * as well as custom API providers (e.g. Zotero) configured via
 * method, params, headers, and result_map.
 *
 * See https://www.w3.org/community/reports/reconciliation/CG-FINAL-specs-0.2-20230410/
 * See https://www.w3.org/community/reports/reconciliation/CG-FINAL-specs-0.2-20230410/
 *
 * To add a service configuration, see https://reconciliation-api.github.io/testbench/#/.
 *
 * Configure the service URL as base_url.
 * Find out the view URL and the preview URL.
 * They should be documented on the page that opens when you access the base_url.
 * Configure the view_url and preview_url using {{id}} as placeholder for the id.
 *
 */
class ReconcileService extends BaseService
{
    public string $serviceKey = 'reconcile';

    /**
     * Provider configuration
     *
     * Reconciliation providers use the default POST queries format.
     * Custom providers define their own request/response mapping:
     *
     * - method: HTTP method ('get' or 'post'). Default: 'post' (reconciliation format).
     * - headers: Extra headers sent with the request.
     * - params: Query/body parameters. Use {{q}}, {{limit}}, {{type}} as placeholders.
     *           If omitted, the Reconciliation Service API query format is used.
     *           You can condition the parameters on the type parameter by using a nested array with the type as key.
     * - result_array: Whether the servise returns an array of answers (default) or a single answer.
     * - result_map: Dot-notation mapping from response items to standard answer fields.
     *               If omitted, the Reconciliation Service API response format is expected.
     *               Supported keys: id, name, description, preview, match, score.
     * - strip_html: Array of result_map keys whose values should be stripped of HTML tags.
     *
     * Common keys for all providers:
     * - base_url: The endpoint URL.
     * - view_url: URL template with {{id}} placeholder for viewing the item.
     *             Will be used to generate the link to the provider in the result.
     *             If no specific view_url is available, you should use the namespace as view URL.
     *             Example: https://www.wikidata.org/wiki/{{id}}
     * - preview_url: URL template with {{id}} for fetching an HTML preview (reconciliation only).
     * - strip: Prefix to strip from returned IDs.
     *          Used to strip the namespace from API responses, e.g.
     *         (e.g. aat/1234 for relative IDs or https://iconclass.org/1234 for absolute IDs).
     * - type: Default type parameter for reconciliation queries.
     *
     * @var array
     */
    public array $config = [

        // Example: https://services.getty.edu/vocab/reconcile?queries={"q1":{"query":"Glocke","type":"/aat"}}
        'aat' => [
            'base_url' => 'https://services.getty.edu/vocab/reconcile/',
            'view_url' => 'http://vocab.getty.edu/page/aat/{{id}}',
            'preview_url' => 'https://services.getty.edu/vocab/reconcile/preview?id=aat/{{id}}',
            'strip' => 'aat/',
            'type' => '/aat'
        ],

        // Example: https://wikidata.reconci.link/en/api?queries={"q1":{"query":"Gedenkstein","type":"Q18783400"}}
        'wd' => [
            'base_url' => 'https://wikidata.reconci.link/en/api',
            'view_url' => 'https://www.wikidata.org/wiki/{{id}}',
            'preview_url' => 'https://wikidata.reconci.link/en/preview?id={{id}}',
            'type' => 'Q18783400'
        ],

        'gnd' => [
            'base_url' => 'https://lobid.org/gnd/reconcile/',
            'view_url' => 'https://lobid.org/gnd/{{id}}',
            'preview_url' => 'https://lobid.org/gnd/{{id}}.preview',
            'type' => 'Q5'
        ],

        'ic' => [
            'base_url' => 'https://termennetwerk-api.netwerkdigitaalerfgoed.nl/reconcile/https://iconclass.org',
            'view_url' => 'https://iconclass.org/{{id}}',
            'preview_url' => 'https://termennetwerk-api.netwerkdigitaalerfgoed.nl/preview/https://iconclass.org/{{id}}',
            'strip' => 'https://iconclass.org/'
        ],

        // Example for reverse geocoding: http://127.0.0.3/services/reconcile.json?q=61.24492,16.55639&provider=nominatim&preview=1&cache=false
        'nominatim-reverse' => [
            'base_url' => 'https://nominatim.openstreetmap.org/reverse',
            'method' => 'get',
            'prefix' => 'osm',
            'params' => [
                'lat' => '{{q|split:,|first}}',
                'lon' => '{{q|split:,|last}}',
                'format' => 'jsonv2',
                'addressdetails' => 1,
                'extratags' => 1
            ],
            'result_array' => false,
            'result_map' => [
                'id' => 'place_id',
                'name' => 'display_name',
//                'description' => 'address',
                'lat' => 'lat',
                'lon' => 'lon',
                'score' => 'importance'
            ],
            'view_url' => 'https://www.openstreetmap.org/?mlat={{lat}}&mlon={{lon}}#map=18/{{lat}}/{{lon}}'
        ],

        // Example for geocoding:  http://127.0.0.3/services/reconcile.json?q=Hanebo&provider=nominatim&preview=1&cache=false
        'nominatim-search' => [
            'base_url' => 'https://nominatim.openstreetmap.org/search',
            'method' => 'get',
            'prefix' => 'osm',
            'params' => [
                'q' => '{{q}}',
                'format' => 'jsonv2',
                'addressdetails' => 1,
                'extratags' => 1
            ],
            'result_map' => [
                'id' => 'place_id',
                'name' => 'display_name',
//                'description' => 'address',
                'lat' => 'lat',
                'lon' => 'lon',
                'score' => 'importance'
            ],
            'view_url' => 'https://www.openstreetmap.org/?mlat={{lat}}&mlon={{lon}}#map=18/{{lat}}/{{lon}}'
        ],

        'zotero' => [
            'base_url' => 'https://api.zotero.org/groups/{{type}}/items', # {{type}} is the group ID
            'method' => 'get',
            'headers' => [
                'Zotero-API-Key' => '{{accesstoken}}',
                'Zotero-API-Version' => '3',
            ],
            'params' => [
                'q' => '{{q}}',
                'qmode' => 'titleCreatorYear',
                'include' => 'citation,bib,data',
                'style' => 'apa', //or: chicago-note-bibliography
                'format' => 'json',
                'limit' => 5,
            ],
            'view_url' => 'https://www.zotero.org/groups/{{groupid}}/items/{{id}}',
            'result_map' => [
                'id' => 'data.key',
                'name' => 'citation',
                'description' => 'bib',
                'preview' => 'bib',
                'groupid' => 'library.id'
            ],
            'strip_html' => ['name', 'description', 'preview'],
        ],

        'useragent' => 'Epigraf/5.0 (github.com/digicademy/epigraf)',
        'accept' => 'application/json'
    ];

    public array $queryOptions = [
        'provider', 'q', 'limit', 'preview', 'cache', 'type', 'style',
        'database', 'propertytype', 'field'
    ];

    public $previewClient = null;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->client->setConfig([
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => ($this->config['useragent'])
            ]
        ]);

        $this->previewClient = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => ($this->config['useragent'])
            ]
        ]);
    }

    /**
     * Build the HTTP request from provider configuration
     *
     * For providers with a `params` config key, resolves {{placeholders}}
     * and returns the configured method/headers/params.
     *
     * For providers without `params` (reconciliation services), builds
     * the standard Reconciliation Service API POST body.
     *
     * @param array $options Query options (provider, q, limit, type, style, ...)
     * @return array With keys: method, url, headers, data
     */
    protected function buildRequest(array $options): array
    {
        $provider = $options['provider'];
        $config = $this->config[$provider];

        // Merge options from the type configuration, to be used in the placeholders
        if (!empty($options['database']) && !empty($options['propertytype']) && !empty($options['field'])) {
            $typeConfig = $this->getTypeConfiguration(['database' => $options['database'], 'scope' => 'properties', 'name' => $options['propertytype']]);
            $typeConfig = $typeConfig['fields'][$options['field']]['services'][$provider] ?? [];
            $options = array_merge($options, $typeConfig);
        }

        $method = $config['method'] ?? 'post';
        $url = $config['base_url'];
        $headers = $config['headers'] ?? [];

        if (empty($url)) {
            throw new \Exception('No base URL configured');
        }

        // Custom params template: resolve placeholders
        if (isset($config['params'])) {
            $url = Attributes::replacePlaceholders($url, $options, true);
            $data = Attributes::replacePlaceholders($config['params'], $options, true);
            $headers = Attributes::replacePlaceholders($headers, $options, true);
        }
        // Default: Reconciliation Service API query format
        // TODO: Use result_map to build the query for reconciliation services as well, instead of hardcoding q, limit, and type.
        //       How: Implement a reconcile step in Objects::processValues(), then use Objects::replacePlaceholders().
        else {
            $query = [
                'q1' => [
                    'query' => $options['q'] ?? '',
                    'limit' => $options['limit'] ?? 5
                ]
            ];

            $type = $options['type'] ?? $config['type'] ?? '';
            if ($type !== '') {
                $query['q1']['type'] = $type;
            }

            $data = 'queries=' . urlencode(json_encode($query));
        }

        return compact('method', 'url', 'headers', 'data');
    }

    /**
     * Execute the HTTP request
     *
     * @param array $request From buildRequest(): method, url, headers, data
     * @return \Cake\Http\Client\Response
     */
    protected function executeRequest(array $request)
    {
        $options = ['redirect' => 2];
        if (!empty($request['headers'])) {
            $options['headers'] = $request['headers'];
        }

        if ($request['method'] === 'get') {
            return $this->client->get($request['url'], $request['data'], $options);
        }

        return $this->client->post($request['url'], $request['data'], $options);
    }

    /**
     * Map the provider response to the standard candidates format
     *
     * For providers with a `result_map`, iterates over the top-level JSON array
     * and extracts fields using dot-notation paths.
     *
     * For reconciliation providers (no result_map), parses the nested
     * {"q1": {"result": [...]}} format.
     *
     * @param \Cake\Http\Client\Response $response
     * @param array $options
     * @return array List of candidate answers
     */
    protected function mapResponse($response, array $options): array
    {
        if (!isset($options['provider'])) {
            throw new \Exception('Provider missing');
        }

        if (isset($this->config[$options['provider']]['result_map'])) {
            $answer = $this->mapCustomResponse($response, $options);
        } else {
            $answer = $this->mapReconcileResponse($response, $options);
        }

        return $answer;
    }

    /**
     * Map a custom API response using result_map dot-notation paths
     *
     * @param \Cake\Http\Client\Response $response
     * @param array $options
     * @return array
     */
    protected function mapCustomResponse($response, array $options): array
    {

        $provider = $options['provider'];
        $config = $this->config[$provider];
        $preview = Attributes::isTrue($options['preview'] ?? false);


        $items = $response->getJson() ?? [];
        if (empty($config['result_array'] ?? true)) {
            $items  = [$items];
        }
        $map = $config['result_map'];
        $stripHtml = $config['strip_html'] ?? [];
        $answers = [];

        foreach ($items as $item) {
            $id = Objects::extract($item, $map['id'] ?? 'id', false);
            if (empty($id)) {
                continue;
            }

            $strip = $config['strip'] ?? '';
            if ($strip !== '') {
                $id = str_replace($strip, '', $id);
            }

            $prefix = $config['prefix'] ?? $provider;
            $answer = ['id' => $id, 'value' => $prefix . ':' . $id];

            foreach ($map as $field => $path) {
                if (in_array($field, ['id', 'value'])) {
                    continue;
                }
                if (!$preview && $field === 'preview') {
                    continue;
                }
                $answer[$field] = Objects::extract($item, $path, false);
                if (in_array($field, $stripHtml) && ($answer[$field] !== null)) {
                    $answer[$field] = trim(strip_tags((string)$answer[$field]));
                }
            }

            if (!empty($config['view_url'])) {
                $answer['url'] = Attributes::replacePlaceholders($config['view_url'], $answer, true);
            }

            // Use a value based on the key configuration
            if (!empty($options['key'])) {
                $answer['value'] = $answer[$options['key']] ?? '';
            }

            $answers[] = $answer;
        }

        return $answers;
    }

    /**
     * Map a Reconciliation Service API response
     *
     * TODO: Merge with mapCustomResponse() and use result_map for the reconciliation format as well, with default paths.
     *
     * @param \Cake\Http\Client\Response $response
     * @param array $options Query options
     * @return array
     */
    protected function mapReconcileResponse($response, array $options): array
    {
        $provider = $options['provider'];
        $config = $this->config[$provider];
        $preview = Attributes::isTrue($options['preview'] ?? false);

        $responseJson = $this->postProcess($response->getJson(), $options) ?? [];
        $previewUrl = $config['preview_url'] ?? '';
        $answers = [];

        foreach ($responseJson as $key => $result) {
            foreach (($result['result'] ?? []) as $item) {
                if (empty($item['id'])) {
                    continue;
                }

                $id = $item['id'];
                $strip = $config['strip'] ?? '';
                if ($strip !== '') {
                    $id = str_replace($strip, '', $id);
                }

                $answer = ['id' => $id, 'value' => $provider . ':' . $id];

                $map = [
                    'name' => 'name',
                    'description' => 'description',
                    'match' => 'match',
                    'score' => 'score'
                ];
                $stripHtml = [];

                foreach ($map as $field => $path) {
                    if (in_array($field, ['id', 'value'])) {
                        continue;
                    }
                    if (!$preview && $field === 'preview') {
                        continue;
                    }
                    $answer[$field] = Objects::extract($item, $path, false);
                    if (in_array($field, $stripHtml) && ($answer[$field] !== null)) {
                        $answer[$field] = trim(strip_tags((string)$answer[$field]));
                    }
                }

                if (!empty($config['view_url'])) {
                    $answer['url'] = Attributes::replacePlaceholders($config['view_url'], $answer, true);
                }

                if ($preview && !empty($previewUrl)) {
                    $previewResponse = $this->previewClient->get(
                        str_replace('{{id}}', $id, $previewUrl)
                    );
                    if ($previewResponse->getStatusCode() === 200) {
                        $answer['preview'] = $this->postProcessPreview(
                            $previewResponse->getStringBody(),
                            $options
                        );
                    }
                }

                $answers[] = $answer;
            }
        }

        return $answers;
    }

    /**
     * Post process data from the reconciliation endpoint to make
     * it compatible with the Reconciliation Service API v0.2 format
     *
     * The optional preview key is an Epigraf extension.
     * Usually, the preview is fetched from the preview endpoint.
     *
     * Return format:
     * [
     *  'q1' => [
     *      'result' => [
     *         [
     *           'id' => 'THEID (string)',
     *           'name' => 'THE NAME (string)',
     *           'description' => 'optional: THE DESCRIPTION (string)',
     *           'preview' => 'optional: THE PREVIEW HTML (string)',
     *           'match' => 'optional: WHETHER IT MATCHES (true/false)',
     *           'score' => 'optional: THE SCORE (double)'
     *         ]
     *      ]
     *   ]
     * ]
     *
     * The first level key (q1) is an arbitrary query identifier.
     *
     *
     * @param array $data
     * @param array $options
     * @return array
     */
    public function postProcess($data, $options = [])
    {
        return $data;
    }

    /**
     * Post process the preview data
     *
     * Removes the HTML layout and returns the content.
     *
     * TODO: Hardcoded fixes for the AAT preview content, which is not well formatted. Find a better solution.
     *
     * @param string $data
     * @param array $options
     * @return string
     */
    public function postProcessPreview($data, $options = [])
    {
        $provider = $options['provider'] ?? 'aat';

        // The AAT preview content is not well formatted, fix it
        if ($provider === 'aat') {
            // Fix tags
            $data = str_replace('<bodystyle', '<body style', $data);
            $data = str_replace('<divstyle', '<div style', $data);
            $data = str_replace('</br>', '<br>', $data);
            $data = str_replace('<br/>', '<br>', $data);
            $data = str_replace('<b>', '', $data);
            $data = str_replace('</b>', '', $data);

            // Remove tags
            $data = preg_replace('/<font[^>]*>.*<\/font>\s*<br>/i', '', $data);
            $data = preg_replace('/<\/?(html|body|div)[^>]*>/i', '', $data);

            // Replace <br> tags that follow "Variant Terms" and before the first <i> after it
            $data = preg_replace_callback(
                '/Variant Terms<\/i>:<br>(.*)<i>/s',
                function ($matches) {
                    return 'Variant Terms</i>: ' . preg_replace('/<br>/', ', ', $matches[1]) . '<i>';
                },
                $data
            );

            // Fix fomatting
            $data = str_replace("<i><font size='+1'>Location in Hierarchy:</font></i>",
                '<br><br><i>Location in Hierarchy:</i>', $data);
            $data = str_replace(', ,', ',', $data);
            $data = str_replace(', <', '<', $data);
        }

        $data = preg_replace('/<\/?(html|body|meta)[^>]*>/i', '', $data);

        return $data;
    }

    /**
     * Query the service
     *
     * Example:
     * https://epigraf.inschriften.net/services/reconcile?q=Glocke
     *
     * Uses buildRequest() to construct the HTTP request from provider config,
     * executeRequest() to send it, and mapResponse() to normalize the result.
     *
     * ### Options
     * - provider: (string) The reconciliation provider as configured in the service
     * - q: (string) Search query
     * - limit: (integer) Max results (default: 5)
     * - type: (string) Type filter (reconciliation) or override
     * - style: (string) Citation style override (e.g. for Zotero)
     * - preview: (bool) Include preview content
     * - cache: (bool) Use cache (default: true)
     *
     * @param string $path Not used
     * @param array $options
     * @return array An array with the keys state, message, provider, result
     * @throws \Exception
     */
    public function query($path = null, array $options = []): array
    {
        if (!isset($options['provider']) || !isset($this->config[$options['provider']])) {
            throw new \Exception('Unknown service provider');
        }

        $options = $this->sanitizeParameters($options);
//        $options['cache'] = false;

        if (empty($options['q'])) {
            return [];
        }

        // Build request from config
        $request = $this->buildRequest($options);

        // Cache key
        $preview = Attributes::isTrue($options['preview'] ?? false);
        $cacheKey = md5($request['url'] . serialize($request['data']) . '&preview=' . $preview);

        if (Attributes::isTrue($options['cache'] ?? true)) {
            $task = Cache::read($this->serviceKey . ':' . $cacheKey, 'services');
            if (is_array($task) && (($task['state'] ?? 'ERROR') === 'SUCCESS')) {
                return $task;
            }
        }

        // Execute
        $response = $this->executeRequest($request);

        $state = $response->getStatusCode() === 200 ? 'SUCCESS' : 'ERROR';
        $task = [
            'state' => $state,
            'message' => $response->getReasonPhrase(),
            'provider' => $options['provider']
        ];

        // Map response to standard format
        $answers = $this->mapResponse($response, $options);

        $task['result']['answers'] = [
            [
                'query' => $options['q'],
                'candidates' => $answers
            ]
        ];

        // Save to cache
        if ($response->getStatusCode() === 200) {
            Cache::write($this->serviceKey . ':' . $cacheKey, $task, 'services');
        }

        return $task;
    }
}
