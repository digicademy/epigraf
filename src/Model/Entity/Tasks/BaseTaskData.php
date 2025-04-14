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
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;
use App\View\ApiView;
use App\View\CsvView;
use App\View\GeoJsonView;
use App\View\JsonldView;
use App\View\JsonView;
use App\View\MarkdownView;
use App\View\RdfView;
use App\View\TtlView;
use App\View\XmlView;
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
            'xml' => XmlView::class,
            'md' => MarkdownView::class,
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
    public function getRenderOptions()
    {
        $options = [];
        $options['level'] = 0; //(!$this->rowwise && empty($this->wrap)) ? 0 : 0;
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
     * Export data
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $databank = $this->activateDatabank();

        $this->config['offset'] = $this->config['offset'] ?? 0;

        // TODO: add index option to each task configuration
        $indexkey = empty($this->job->config['tasks']['index'] ?? true) ? false : ($this->job->index_key . '-export');
        $dataParams = $this->getDataParams();
        $pagingParams = $this->getPagingParams();

        /** @var ExportTableInterface $table */
        $table = TableRegistry::getTableLocator()->get($databank->plugin . '.' . $this->model);
        $rows = $table->getExportData($dataParams, $pagingParams, $indexkey);

        // Wrapper (getView() must be called before to update the wrap settings)
        $view = $this->getView();
        $filenameTemplate = $this->job->getCurrentOutputFilePath();

        if (!$this->rowwise && !empty($this->wrap['prefix']) && ($this->config['offset'] === 0)) {
            Files::appendToFile($filenameTemplate, $this->wrap['prefix']);
        }

        $count = count($rows);
        if (!empty($rows)) {
            $this->config['offset'] += $count;
            $this->job->updateCurrentTask($this->config);

            // Render content
            $options = $this->getRenderOptions();

            // All rows at once
            if (!$this->rowwise) {

                $rendered = $view->renderDocument($rows, $options);
                $rendered = str_replace("\r", "", $rendered);
                $rendered = "\n" . $rendered;

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

                    $rendered = $view->renderDocument($row, $options);
                    $rendered = str_replace("\r", "", $rendered);

                    $number++;
                    if (($number !== $count) && ($filename === $filenameTemplate)) {
                        $rendered .= $view::$separator;
                    }
                    Files::appendToFile($filename, $rendered, true);
                    $this->copyFiles([$row]);
                }
            }
        }

        // Wrapper
        if (!$this->rowwise && !empty($this->wrap['postfix']) && (count($rows) < $this->job->limit)) {
            Files::appendToFile($filenameTemplate, $this->wrap['postfix']);
        }

        //Finish pipeline element
        return ($count < $this->job->limit);
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
