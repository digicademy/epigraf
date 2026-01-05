<?php

namespace App\Datasource\Services;

use App\Cache\Cache;
use App\Utilities\Converters\Attributes;
use Cake\Http\Client;

/**
 * Reconcilation Service
 *
 * Supports services following the Reconciliation Service API v0.2 format.
 *
 * See https://www.w3.org/community/reports/reconciliation/CG-FINAL-specs-0.2-20230410/
 * See https://www.w3.org/community/reports/reconciliation/CG-FINAL-specs-0.2-20230410/
 *
 * To add a service configuration, see https://reconciliation-api.github.io/testbench/#/.
 *
 * Configute the service URL as base_url.
 * Find out the view URL and the preview URL.
 * They should be documented on the page that opens when you access the base_url.
 * Configure the view_url and preview_url using {{id}} as placeholder for the id.
 *
 */
class ReconcileService extends BaseService
{

    public string $serviceKey = 'reconcile';

    /**
     * Each service configuration supports the following keys:
     * - base_url: The base URL of the reconciliation service, receiving requests with the queries parameter
     * - type: The type send to the reconciliation service.
     * - view_url: The URL to view the item, using {{id}} as placeholder for the id.
     *             Will be used to generate the link to the provider in the result.
     *             If no specific view_url is available, you should use the namespace as view URL.
     *            Example: https://www.wikidata.org/wiki/{{id}}
     * - preview_url: The endpoint that returns an HTML preview, using {{id}} as placeholder for the id.
     * - strip: If the endpoint returns IDs including the namespace
     *          (e.g. aat/1234 for relative IDs or https://iconclass.org/1234 for absolute IDs),
     *          provide the namespace in the strip parameter to remove it from the ID.
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

        'useragent' => 'Epigraf/5.0 (github.com/digicademy/epigraf)',
        'accept' => 'application/json'
    ];

    public array $queryOptions = ['provider','q', 'limit', 'preview','cache','type'];

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
     * ### Options
     * - q: (string) Query string
     * - limit: (integer) Limit the number of results
     * - provider: (string) The reconciliation provider as configured in the service
     *
     * @param string $path Not used
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function query($path = null, array $options = []): array
    {
        if (!isset($options['provider']) || !isset($this->config[$options['provider']])) {
            throw new \Exception('Unknown service provider');
        }

        $provider = $options['provider'];
        $baseUrl = $this->config[$provider]['base_url'];
        if (empty($baseUrl)) {
            throw new \Exception('No base URL configured');
        }


        $options = $this->sanitizeParameters($options);

        if (empty($options['q'])) {
            return [];
        }

        // Try reading from cache
        $data = ['q1' => ['query' => $options['q'] ?? '', 'limit' => 5]];
        $type = $options['type'] ?? $this->config[$provider]['type'] ?? '';
        if ($type !== '') {
            $data['q1']['type'] = $type;
        }

        $data = json_encode($data);
        $data = 'queries=' . urlencode($data);
        $preview = Attributes::isTrue($options['preview'] ?? false);
        $cacheKey = $baseUrl . '?' . $data . '&preview=' . $preview;
        $cacheKey = md5($cacheKey);

        if (Attributes::isTrue($options['cache'] ?? true)) {
            $task = Cache::read($this->serviceKey .':' . $cacheKey,'services');
            if (is_array($task) && (($task['state'] ?? 'ERROR') === 'SUCCESS')) {
                return $task;
            }
        }

        // Query the service
        $response = $this->client->post($baseUrl, $data);
        $state = $response->getStatusCode() === 200 ? 'SUCCESS' : 'ERROR';
        $task = [
            'state' => $state,
            'provider' => $provider
        ];

        $responseJson =  $this->postProcess($response->getJson(), $options) ?? [];
        $viewUrl = $this->config[$provider]['view_url'] ?? '';
        $previewUrl = $this->config[$provider]['preview_url'] ?? '';

        $answers = [];
        foreach($responseJson as $key => $result) {
            foreach (($result["result"] ?? []) as $itemIdx => $item) {

                if (empty($item['id'])) {
                    continue;
                }

                // Get value
                $id = $item['id'];
                $strip = $this->config[$provider]['strip'] ?? '';
                if ($strip !== '') {
                    $id = str_replace($strip, '', $id);
                }
                $value = $provider . ':' .  $id;

                $answer = [
                    'id' => $id,
                    'value' => $value,
                    'name' => $item['name'] ?? null,
                    'description' => $item['description'] ?? null,
                    'match' => $item['match'] ?? null,
                    'score' => $item['score'] ?? null
                ];

                if (!empty($viewUrl)) {
                    $answer['url'] = str_replace('{{id}}', $id, $viewUrl);
                }

                if ($preview && !empty($previewUrl)) {
                    $previewResponse = $this->previewClient->get(
                        str_replace('{{id}}', $id, $previewUrl)
                    );
                    $previewHtml = $this->postProcessPreview($previewResponse->getStringBody(), $options);
                    if ($previewResponse->getStatusCode() === 200) {
                        $answer['preview'] = $previewHtml;
                    }
                }

                $answers[] = $answer;
            }
        }

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
