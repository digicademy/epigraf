<?php

namespace App\Datasource\Services;

/*
 * HTTP proxy service
 *
* Passes images through a proxy server, translating http to https.
 *
 * Example: /services/http/file?url=http://example.com/image.jpg
 *
*/

class HttpService extends BaseService
{
    public string $serviceKey = 'http';

    public array $config = [
        'accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
        'useragent' => 'Epigraf/5.0 (github.com/digicademy/epigraf)'
    ];

    public array $queryOptions = ['url'];

    public $proxyMode = 'file';

    /**
     * Query the service
     *
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function query($path = 'file', array $options = []): array
    {
        $options = $this->sanitizeParameters($options);
        $url = $options['url'] ?? null;

        $response = $this->client->get($url);
        $answer = [
            'type'  => $response->getHeaderLine('Content-Type'),
            'response' => $response->getBody(),
            'status' => $response->getStatusCode()
        ];

        return $answer;
    }

}
