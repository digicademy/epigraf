<?php
declare(strict_types=1);

/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\View;

use Cake\Datasource\ResultSetInterface;

/**
 * A view class that is used for GeoJSON responses,
 * derived from the JsonView class.
 *
 */
class GeoJsonView extends JsonView
{

    static protected $_extension = 'geojson';

    public function initialize(): void
    {
        // Map extension to mime types
        $this->getResponse()->setTypeMap('geojson', ['application/geo+json']);
        parent::initialize();
    }

    public static function contentType(): string
    {
        return 'application/geo+json';
    }



    /**
     * Construct the array
     *
     * @param $data
     * @param integer $level Nesting level of _prepareData calls
     */
    protected function _prepareViewData($data, $options = [], $level = 0)
    {

        if (($level === 0) && is_array($data)) {
            $features = [];
            $options['properties'] = $this->getConfig('options')['params']['properties'] ?? [];
            foreach($data as $key => $value) {
                if ($value instanceof ResultSetInterface) {
                    unset($data[$key]);
                    foreach ($value as $entity) {
                        $entityData = $this->extractData($entity, $options);
                        $features = array_merge($features, $entityData['geodata'] ?? []);
                    }
                }
            }

            return [
                'type' => 'FeatureCollection',
                'features' => $features,
                'pagination' => $data['pagination'] ?? []
            ];
        }

        return $data;

    }

}
