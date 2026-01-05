<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\View;

use App\Model\Entity\BaseEntity;
use App\Model\Interfaces\ExportEntityInterface;
use App\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Entity;
use Cake\Utility\Hash;
use Cake\View\SerializedView;
use Epi\Model\Entity\Project;

/**
 * A view class that is used for creating API responses with entity data.
 * Do not use directly: This is the base class to be overwritten by JsonView, XmlView, RdfView, TtlView etc.
 *
 * Step 1:
 * _serialize() is called by the controller to serialize the view data.
 * The function determines the view variables to serialize and calls
 * prepareData(), renderContent(), renderProlog(), and renderEpilog().
 *
 * Step 2:
 * prepareData() is called to prepare the full data set before it is converted to an array.
 * The function can be overwritten in subclasses to change the data structure.
 *
 * Step 3:
 * renderContent() is called to convert the array to the serialized output.
 *  From within renderContent() the data is extracted from the entities
 *  by recursively calling extractData() for each array value. Within extractData(),
 *  entity data is requested by calling getDataForExport(), other objects are converted using toArray().
 *
 * Step 4:
 * renderProlog() and renderEpilog() are called to wrap the content, for example in a root tag.
 * They are called after renderContent() to allow collecting additional data
 * while processing the entities, for example, namespaces.
 *
 */
class ApiView extends SerializedView
{

    /**
     * @var string The file extension for the response.
     */
    static protected $_extension = '';

    /**
     * @var string The separator between serialized entities.
     */
    static public $separator = '';

    /**
     * @var string Separator between blocks of serialized data.
     */
    static public $batchSeparator = "\n";

    /**
     * @var array Data for wrapping the content .
     */
    static protected $_header = [];

    protected $_index = [];

    /**
     * Init the header variables (resets static variables)
     *
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->resetDocument();
    }

    /**
     * Attach an index to the view
     *
     * The index is used to collect data about the entities in jobs.
     * It is loaded in saved in the job model.
     *
     * @param array $index
     * @return void
     */
    public function attachIndex(&$index)
    {
        $this->_index = &$index;
    }

    /**
     * Reset the index attached to the view
     *
     * @return void
     */
    public function resetIndex()
    {
        $this->_index = [];
    }

    /**
     * Get the index attached to the view
     *
     * @return array|mixed
     */
    public function getIndex()
    {
        return $this->_index;
    }

    /**
     * Cache rendering
     *
     * @param string|null $template The template being rendered.
     * @param string|false|null $layout The layout being rendered.
     * @return string The rendered view.
     */
    public function render(?string $template = null, $layout = null): string
    {
        $cacheConfig = $this->getConfig('cacheConfig');
        if (!empty($cacheConfig)) {
            $return = Cache::remember($cacheConfig['key'], function () use ($template, $layout) {
                return parent::render($template, $layout);
            }, $cacheConfig['config']);
        }
        else {
            $return = parent::render($template, $layout);
        }

        return $return;
    }

    /**
     * Save serialized data to file
     *
     * @param array $data
     * @param string $filename
     * @return void
     */
    public function renderToFile($data, $filename)
    {
        $data = $this->renderToString($data);
        file_put_contents($filename, $data);
    }

    /**
     * Save serialized data to file
     *
     * @param array $data
     * @param boolean $wrap Whether to render prolog and epilog
     * @param array $options Additional options
     * @return string
     */
    public function renderToString($data, $wrap = true, $options = [])
    {
        $this->setConfig('wrap', $wrap);
        $this->setConfig('options', $options);
        $this->set(['rows' => $data]);
        return $this->_serialize('rows');
    }

    /**
     * Serialize view vars
     *
     * @param array|string $serialize The name(s) of the view variable(s) that need(s) to be serialized
     * @return string The serialized data
     */
    protected function _serialize($serialize): string
    {
        $data = $this->_dataToSerialize($serialize);

        $options = $this->getConfig('options', []);
        if (Configure::read('debug')) {
            $options['pretty'] = true;
        }

        if (!$this->getConfig('raw')) {
            $data = $this->_prepareViewData($data, $options, $options['level'] ?? 0);
        }

        $options['wrap'] = $this->getConfig('wrap', true);
        $content = $this->renderDocument($data, $options);
        return $content;
    }

    /**
     * Returns data to be serialized.
     *
     * @param array|string $serialize The name(s) of the view variable(s) that need(s) to be serialized.
     * @return mixed The data to serialize.
     */
    protected function _dataToSerialize($serialize)
    {
        if ($serialize === true) {
            $serialize = array_keys($this->viewVars);

            if (empty($serialize)) {
                $serialize = null;
            }
            elseif (count($serialize) === 1) {
                $serialize = current($serialize);
            }
        }

        if (is_array($serialize)) {
            $data = [];
            foreach ($serialize as $alias => $key) {
                if (is_numeric($alias)) {
                    $alias = $key;
                }
                if (array_key_exists($key, $this->viewVars)) {
                    $data[$alias] = $this->viewVars[$key];
                }
            }

            return !empty($data) ? $data : null;
        }
        else {
            $data = $this->viewVars[$serialize] ?? null;
            if (is_array($data) && Hash::numeric(array_keys($data))) {
                $data = [$serialize => $data];
            }
        }

        return $data;
    }

    /**
     * Prepare view data before it is passed to the rendering functions.
     *
     * Overwrite in subclasses to change the data structure
     *
     * @param Entity|array $data
     * @param array $options
     * @param integer $level
     * @return array
     */
    protected function _prepareViewData($data, $options = [], $level = 0)
    {
        return $data;
    }

    /**
     * Prepare entity data after calling getDataForExport()
     *
     * Overwrite in subclasses to change the data structure
     * and collect data on the way through the entities.
     *
     * @param Entity|array $data
     * @param array $options
     * @return array
     */
    public function _prepareEntityData($data, $options = [])
    {
        return $data;
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
        if (is_object($data) && ($data instanceof ExportEntityInterface)) {
            /** @var BaseEntity $data */
            $data = $data->getDataForExport($options, static::$_extension);
            $data = $this->_prepareEntityData($data, $options);
        }

        if (is_object($data) && method_exists($data, 'toArray') && is_callable([$data, 'toArray'])) {
            $data = $data->toArray();
        }
        return $data;
    }

    /**
     * Reset variables before a new document is rendered
     *
     * @param array $options
     * @return void
     */
    public function resetDocument()
    {
        static::$_header = ['namespaces' => [], 'base' => null];
    }

    /**
     * Render content before the entity data, e.g. open the XML root tag
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    public function renderProlog($data, $options)
    {
        return '';
    }

    /**
     * Render content after the entity data, e.g. close the XML root tag
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    public function renderEpilog($data, $options)
    {
        return '';
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
        return '';
    }

    /**
     * Render the full document
     *
     * ## Options
     * - wrap: Whether to wrap the content in prolog and epilog
     * - level: The level of indentation
     *
     * @param array|EntityInterface $data
     * @param array $options
     * @return string
     */
    public function renderDocument($data, $options)
    {

        // First collect the content
        // (which may add attributes to $_header to be rendered in the prolog)
        $content = $this->renderContent($data, $options, $options['level'] ?? 0);

        // Wrap the content in the header and footer
        if ($options['wrap'] ?? true) {
            $content = $this->renderProlog($data, $options) . $content . $this->renderEpilog($data, $options);
        }

        return $content;
    }

    /**
     * Post process the file after rendering
     *
     * @param string $filename
     * @return boolean Return true if post-processing was successful
     */
    public function postProcess($filename)
    {
        return true;
    }

}
