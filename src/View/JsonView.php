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

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         2.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\View;

use App\Model\Interfaces\ExportEntityInterface;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Entity;
use Cake\ORM\ResultSet;
use RuntimeException;

/**
 * A view class that is used for JSON responses.
 *
 * It allows you to omit templates if you just need to emit JSON string as response.
 *
 * In your controller, you could do the following:
 *
 * ```
 * $this->set(['posts' => $posts]);
 * $this->viewBuilder()->setOption('serialize', true);
 * ```
 *
 * When the view is rendered, the `$posts` view variable will be serialized
 * into JSON.
 *
 * You can also set multiple view variables for serialization. This will create
 * a top level object containing all the named view variables:
 *
 * ```
 * $this->set(compact('posts', 'users', 'stuff'));
 * $this->viewBuilder()->setOption('serialize', true);
 * ```
 *
 * The above would generate a JSON object that looks like:
 *
 * `{"posts": [...], "users": [...]}`
 *
 * You can also set `'serialize'` to a string or array to serialize only the
 * specified view variables.
 *
 * If you don't set the `serialize` opton, you will need a view template.
 * You can use extended views to provide layout-like functionality.
 *
 * You can also enable JSONP support by setting `jsonp` option to true or a
 * string to specify custom query string parameter name which will contain the
 * callback function name.
 */
class JsonView extends ApiView
{

    /**
     * The name of the layout file to render the view inside of. The name
     * specified is the filename of the layout in /src/Template/Layout without
     * the .php extension. The file is only rendered, if the serialize option is not set.
     *
     * @var string
     */
    public $layout = 'json';

    static protected $_extension = 'json';

    /**
     * @var string The separator between serialized entities.
     */
    static public $separator = ",\n";

    /**
     * Default config options.
     *
     * Use ViewBuilder::setOption()/setOptions() in your controller to set these options.
     *
     * - `serialize`: Option to convert a set of view variables into a serialized response.
     *   Its value can be a string for single variable name or array for multiple
     *   names. If true all view variables will be serialized. If null or false
     *   normal view template will be rendered.
     * - `jsonOptions`: Options for json_encode(). For e.g. `JSON_HEX_TAG | JSON_HEX_APOS`.
     * - `jsonp`: Enables JSONP support and wraps response in callback function provided in query string.
     *   - Setting it to true enables the default query string parameter "callback".
     *   - Setting it to a string value, uses the provided query string parameter
     *     for finding the JSONP callback name.
     *
     * @var array
     * @pslam-var array{serialize:string|bool|null, jsonOptions: int|null, jsonp: bool|string|null}
     */
    protected $_defaultConfig = [
        'cacheKey' => null,
        'serialize' => null,
        'jsonOptions' => null,
        'jsonp' => null,
    ];

    public static function contentType(): string
    {
        return 'application/json';
    }

    /**
     * Render a JSON view.
     *
     * @param string|null $template The template being rendered.
     * @param string|false|null $layout The layout being rendered.
     * @return string The rendered view.
     */
    public function render(?string $template = null, $layout = null): string
    {
        $return = parent::render($template, $layout);

        $jsonp = $this->getConfig('jsonp');
        if ($jsonp) {
            if ($jsonp === true) {
                $jsonp = 'callback';
            }
            if ($this->request->getQuery($jsonp)) {
                $return = sprintf('%s(%s)', h($this->request->getQuery($jsonp)), $return);
                $this->response = $this->response->withType('js');
            }
        }

        return $return;
    }

    /**
     * Convert a value to JSON
     *
     * @param Entity|array $data
     * @param array $options
     * @return string
     */
    public function renderArray(Entity|array $data, $options): string
    {
        $jsonOptions = $this->getConfig('jsonOptions');
        if ($jsonOptions === null) {
            $jsonOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR;
        }
        elseif ($jsonOptions === false) {
            $jsonOptions = 0;
        }

        if ($options['pretty'] ?? true) {
            $jsonOptions |= JSON_PRETTY_PRINT;
        }

        if (defined('JSON_THROW_ON_ERROR')) {
            $jsonOptions |= JSON_THROW_ON_ERROR;
        }

        $return = json_encode($data, $jsonOptions);
        if ($return === false) {
            throw new RuntimeException(json_last_error_msg(), json_last_error());
        }
        return $return;
    }

    /**
     * Prepare data for export by calling getDataForExport on an entity and toArray on other objects.
     *
     * @param Entity|array $data
     * @param array $options
     * @return array
     */
    public function extractData($data, $options = [])
    {
        // Single entities with the getDataForExport method
        if (is_object($data) && ($data instanceof ExportEntityInterface)) {
            $data = $data->getDataForExport($options, static::$_extension);
            $data = $this->_prepareEntityData($data, $options);
        }

        // Set of entities with the getDataForExport method
        elseif ($data instanceof ResultSet) {
            $rows = [];
            foreach ($data as $row) {
                if ((is_object($row) && ($row instanceof ExportEntityInterface))) {
                    $row = $row->getDataForExport($options, static::$_extension);
                    $row = $this->_prepareEntityData($row, $options);
                }
                if ($row !== null) {
                    $rows[] = $row;
                }
            }
            $data = $rows;
        }

        else {
            // Objects with toArray method
            if (is_object($data) && method_exists($data, 'toArray') && is_callable([$data, 'toArray'])) {
                $data = $data->toArray();
            }

            // Arrays
            if (is_array($data)) {
                foreach ($data as $key => &$value) {
                    $options['level'] = ($options['level'] ?? 0) + 1;
                    $value = $this->extractData($value, $options);
                }
            }
        }

        return $data;
    }

    /**
     * Render the entity content
     *
     * @param array|EntityInterface $data
     * @param array $options
     * @param int $level The level of indentation
     * @return string
     */
    public function renderContent($data, $options = [], $level = 0)
    {
        $data = $this->extractData($data, $options);
        return $this->renderArray($data, $options);
    }

}
