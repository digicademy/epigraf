<?php

namespace App\Datasource\Services;

/*
 * Image proxy service
 *
 * Passes images through a proxy server, creating thumbnails if needed.
 *
 * Example: /services/img/file?size=small&url=http://example.com/image.jpg
 *
*/

use App\Utilities\Files\Files;
use Cake\Http\Exception\NotFoundException;

class ImageService extends BaseService
{
    public string $serviceKey = 'img';

    public array $config = [
        'accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
        'useragent' => 'Epigraf/5.0 (github.com/digicademy/epigraf)'
    ];

    public array $queryOptions = ['url', 'size'];

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

        // TODO: Thumbnail
        $size = $options['size'] ?? null;
        if (!empty($size)) {
            $size = min(60, $size);

//            $thumbPath = Files::getThumb($url, $size);
//            if (empty($thumbPath)) {
//                throw new NotFoundException(__('File not found'));
//            }
        }

        $response = $this->client->get($url);
        $answer = [
            'type'  => $response->getHeaderLine('Content-Type'),
            'response' => $response->getBody(),
            'status' => $response->getStatusCode()
        ];

        return $answer;
    }

}
