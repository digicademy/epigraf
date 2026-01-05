<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity\Tasks;

use App\Model\Entity\BaseTask;
use App\Model\Entity\Databank;
use App\Model\Interfaces\ExportTableInterface;
use App\Model\Table\BaseTable;
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;
use App\View\ApiView;
use App\View\CsvView;
use App\View\GeoJsonView;
use App\View\HtmlView;
use App\View\JsonldView;
use App\View\JsonView;
use App\View\MarkdownView;
use App\View\RdfView;
use App\View\TtlView;
use App\View\XmlView;
use App\View\XlsxView;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\TableRegistry;
use Epi\Model\Entity\RootEntity;

/**
 * Output data in the export pipeline
 */
class BaseTaskData extends BaseTask
{

    /** @var string The model name that will be used to find the table. */
    public $model = 'Properties';

    /** @var string The wrapper or an empty array. */
    public $wrap = [];

    /** @var bool Row-wise output */
    public $rowwise = false;

    /** @var array Settings to copy images and other files */
    public $copySettings = [];

    /**
     * Init view according to output format
     *
     * @return ApiView
     */
    protected function getView()
    {
        $format = Attributes::cleanOption($this->config['format'] ?? 'xml', API_EXTENSIONS, 'xml');
        $viewClasses = [
            'json' => JsonView::class,
            'csv' => CsvView::class,
            'xlsx' => XlsxView::class,
            'xml' => XmlView::class,
            'md' => MarkdownView::class,
            'plain' => HtmlView::class,
            'ttl' => TtlView::class,
            'jsonld' => JsonldView::class,
            'rdf' => RdfView::class,
            'geojson' => GeoJsonView::class,
        ];

        $viewClass = $viewClasses[$format] ?? XmlView::class;
        $view = new $viewClass();
        $view->set('database', $this->job->databank);

        // Add wrapping tags only for XML
        if (!in_array($format, ['xml', 'rdf'])) {
            $this->wrap = [];
        }

        return $view;
    }

    /**
     * Rendering options passed to the renderContent method of the view class
     *
     * @return array[]
     */
    public function getRenderOptions($table = null)
    {
        $options = [];
        $options['level'] = 0;
        $options['wrap'] = $this->config['wrap'] ?? false;

        if (!empty($this->config['iris'])) {
            $options['params']['idents'] = 'iri';
        }

        if (!empty($this->config['snippets'])) {
            $options['params']['snippets'] = Attributes::commaListToStringArray($this->config['snippets']);
        }

        if (isset($this->job->config['params']['published'])) {
            $options['published'] = Attributes::commaListToIntegerArray($this->job->config['params']['published']);
        }

        if (!empty($table) && isset($this->config['expand']) && Attributes::isFalse($this->config['expand'])) {
            $columnParams = $table->parseRequestParameters(['columns' => $this->config['columns'] ?? '']);
            $options['columns'] = $table->getColumns($columnParams['columns'] ?? []);
        }

        $options['params']['preset'] = $this->config['preset'] ?? 'default';

        return $options;
    }

    /**
     * Copy files
     *
     * @param array|ResultSetInterface $rows Root entities with a copyFiles() function
     * @return void
     */
    protected function copyFiles($rows)
    {
        // General files
        if (!isset($this->copySettings['files'])) {
            $copyFiles = !empty($this->config['copyfiles'] ?? false);
        }

        if ($copyFiles) {
            $targetFolder = $this->job->jobPath;
            if (!empty($this->config['targetfolder'])) {
                $targetFolder .= $this->config['targetfolder'] . DS;

                $this->copySettings['files'] = [
                    'copy' => $copyFiles,
                    'folder' => $targetFolder
                ];
            }
            else {
                $this->copySettings['files'] = [];
            }
        }

        // Copy image configuration
        if (!isset($this->copySettings['images'])) {
            $imageTypes = Attributes::commaListToStringArray($this->config['imagetypes'] ?? '');
            $imageFolder = $this->config['imagefolder'] ?? 'images';
            $imageFolder = $this->job->jobPath . $imageFolder;
            $copyImages = !empty($this->config['copyimages'] ?? false) && !empty($imageTypes) && !empty($imageFolder);

            if ($copyImages) {
                $metadataConfig = json_decode($this->config['metadata'] ?? '{}', true);
                $this->copySettings['images'] = [
                    'imagetypes' => $imageTypes,
                    'folder' => $imageFolder,
                    'metadata' => $metadataConfig
                ];
            }
            else {
                $this->copySettings['images'] = [];
            }
        }

        if (empty($this->copySettings['files']) && empty($this->copySettings['images'])) {
            return;
        }

        foreach ($rows as $entity) {
            if ($entity instanceof RootEntity) {

                if (!empty($this->copySettings['files'])) {
                    $entity->copyFiles($targetFolder);
                }

                if (!empty($this->copySettings['images'])) {
                    $entity->copyImages(
                        $this->copySettings['images']['imagetypes'],
                        $this->copySettings['images']['folder'],
                        $this->copySettings['images']['metadata']
                    );
                }

            }
        }
    }

    /**
     * Reset the task progress
     *
     * @return true
     */
    public function init()
    {
        $this->config['offset'] = 0;
        return true;
    }

    /**
     * Export data
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $databank = $this->activateDatabank();

        $this->config['offset'] = $this->config['offset'] ?? 0;

        // TODO: add index option to each task configuration
        $indexkey = empty($this->job->config['options']['index'] ?? true) ? false : ($this->job->index_key . '-export');
        $dataParams = $this->getDataParams();
        $pagingParams = $this->getPagingParams();

        /** @var ExportTableInterface $table */
        $table = TableRegistry::getTableLocator()->get($databank->plugin . '.' . $this->model);
        $rows = $table->getExportData($dataParams, $pagingParams, $indexkey);

        // Wrapper (getView() must be called before to update the wrap settings)
        $view = $this->getView();
        $view->attachIndex($this->job->getIndex());
        $filenameTemplate = $this->getCurrentOutputFilePath();
        $wrap = $this->config['wrap'] ?? false;

        if (!$this->rowwise && !empty($this->wrap['prefix']) && ($this->config['offset'] === 0)) {
            Files::appendToFile($filenameTemplate, $this->wrap['prefix']);
        }

        // Render content
        $options = $this->getRenderOptions($table);
        $options['wrap'] = false;

        $count = count($rows);
        if (!empty($rows)) {
            $this->config['offset'] += $count;
            $this->job->updateCurrentTaskConfig($this->config);

            // Activate preset
            $beforePreset = BaseTable::$requestPreset ?? 'default';
            BaseTable::$requestPreset  = $this->config['preset'] ?? $beforePreset;

            // All rows at once
            if (!$this->rowwise) {

                $rendered = $view->renderDocument($rows, $options);
                $rendered = str_replace("\r", "", $rendered);
                $rendered = $view::$batchSeparator . $rendered;

                Files::appendToFile($filenameTemplate, $rendered, true);
                $this->copyFiles($rows);
            }

            // Row by row
            else {
                $number = 0;
                foreach ($rows as $row) {
                    $filename = Files::cleanPath(
                        Attributes::replacePlaceholders($filenameTemplate, $row),
                        false
                    );

                    // Make job accessible for triple generation
                    $row->job = $this->job;
                    $row->task = $this;

                    $rendered = $view->renderDocument($row, $options);
                    $rendered = str_replace("\r", "", $rendered);

                    $number++;

                    // Separate the entities
                    if (($number !== $count) && ($filename === $filenameTemplate)) {
                        $rendered .= $view::$separator;
                    }

                    Files::appendToFile($filename, $rendered, true);
                    $this->copyFiles([$row]);
                }
            }

            // Reset preset
            $beforePreset = BaseTable::$requestPreset ?? 'default';
            BaseTable::$requestPreset  = $this->config['preset'] ?? $beforePreset;

        }

        // Wrapper
        if (!$this->rowwise && !empty($this->wrap['postfix']) && (count($rows) < $this->job->limit)) {
            Files::appendToFile($filenameTemplate, $this->wrap['postfix']);
        }

        //Wrapper
        if ($wrap) {
            if ( (count($rows) < $this->job->limit)) {
                Files::prependToFile($filenameTemplate, $view->renderProlog([], $options));
                Files::appendToFile($filenameTemplate, $view->renderEpilog([], $options));
            }
        }

        //Finish pipeline element
        $finished = ($count < $this->job->limit);

        if ($finished) {
            $view->postProcess($filenameTemplate);
        }

        return $finished;
    }

    /**
     * How many calls of execute will be needed to finish the task?
     *
     * @return int
     */
    public function progressMax()
    {
        $databankName = empty($this->config['database']) ? $this->job->config['database'] : $this->config['database'];

        /** @var Databank $databank */
        $databank = $this->job->activateDatabank($databankName);

        $table = TableRegistry::getTableLocator()->get($databank->plugin . '.' . $this->model);

        $dataparams = $this->getDataParams();
        $count = $table->getExportCount($dataparams);
        $calls = max(1, ceil($count / $this->job->limit));

        return $calls;

    }

}
