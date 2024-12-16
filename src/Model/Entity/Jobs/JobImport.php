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

namespace App\Model\Entity\Jobs;

use App\Model\Entity\Databank;
use App\Model\Entity\Job;
use App\Utilities\Files\Files;
use Cake\Core\Configure;
use Cake\Datasource\FactoryLocator;
use Cake\Log\Log;
use Cake\Utility\Inflector;
use Exception;

/**
 * Import data from an uploaded file
 *
 * The preview task performs the following steps:
 * 1. _loadEntities
 *    a) _loadData
 *    b) _toEntities
 *
 * The import task performs the following steps
 * 1. _loadEntities
 *    a) _loadData
 *    b) _toEntities
 * 2. _saveEntities
 *    a) clearEntities (model method)
 *    b) saveMany (model method)
 *    c) _solveLinks
 *      (collectIds / _addToIndex -> getSolvedIds / _canSolve -> saveMany
 *
 * 3. _fillEntities
 *
 * TODO: refactor task_*-methods using BaseTask-Class derivates (see TaskDataArticles.php as an example)
 */
class JobImport extends Job
{

    /**
     * Default limit value
     *
     * @var int
     */
    public int $limit = 1000;

    /**
     * Number of rows currently loaded
     *
     * @var int
     */
    protected int $_loadedRows = 0;

    /**
     * Default job name
     *
     * @var string
     */
    public $jobName = 'Import CSV data';


    /**
     * Reconnect model
     *
     * Get a new instance of the model since the connection is not updated in the model.
     *
     * @return void
     *
     * @deprecated
     */
    protected function _reconnectModel()
    {
        $modelname = 'Epi.' . Inflector::camelize($this->config['table']);
        $this->model = FactoryLocator::get('Table')->get($modelname);

        if ($this->config['scope'] ?? false) {
            $this->model->setScope($this->config['scope']);
        }

        $this->entityclass = $this->model->getEntityClass();
    }

    /**
     * Load data and convert it to entities
     *
     * 1. Load data from the source (csv file or database)
     * 2. Converts the data to entities
     *
     * @param $options
     *
     * @return array
     */
    protected function _loadEntities($options)
    {
        $data = [];
        $this->_loadedRows = 0;
        try {
            $options = array_merge($this->config, $options);
            $data = $this->_loadData($options);
            $this->_loadedRows = count($data);

            $this->activateDatabank($this->config['database']);

            /** @var \Epi\Model\Table\BaseTable $model */
            $tableName = $this->config['table'] ?? null;
            $model = $this->getModel($tableName, 'Epi');
            $importBehavior = $model->getBehavior('Import');
            $importBehavior->setConfig('skip', $this->config['skip'] ?? []);

            $data = $model->toEntities($data, $this->getIndex(), ['job_id' => $this->id ?? null]);
            $this->addTaskErrors($importBehavior->getErrors());

        } catch (Exception $e) {
            $msg = __('Error loading data from source "{source}": {error}.',
                ['source' => $options['source'] ?? '', 'error' => $e->getMessage()]);
            $this->addTaskError($msg, ['source' => $options['source'] ?? ''], $e);
        }
        return $data;
    }

    /**
     * Import modes depend on the source: csv file, xml file or xml folder
     *
     * @return string
     * @throws Exception
     */
    protected function _getMode()
    {
        $fileName = $this->config['source'] ?? null;
        if (empty($fileName)) {
            return '';
        }

        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        // CSV
        if (is_file($fileName) && ($ext === 'csv')) {
            return 'csvfile';
        }

        // XML folder
        elseif (is_dir($fileName)) {
            return 'xmlfolder';
        }

        // XML file
        if (is_file($fileName) && ($ext === 'xml')) {
            return 'xmlfile';
        }


        else {
            return '';
        }
    }

    /**
     * Get count of csv rows or xml files for the progress bar
     *
     * @param array $options
     *
     * @return int
     */
    protected function _getCount($options)
    {
        $fileName = $this->config['source'] ?? null;
        if (empty($fileName)) {
            throw new Exception('Missing file name');
        }

        // CSV file
        if ($this->_getMode() === 'csvfile') {
            return Files::countCsv($this->config['source'] ?? null);
        }

        // XML folder
        elseif ($this->_getMode() === 'xmlfolder') {
            return count(Files::getFiles($fileName, '.xml'));
        }

        // XML file
        elseif ($this->_getMode() === 'xmlfile') {
            return 1;
        }

        else {
            return 0;
        }
    }

    /**
     * Load csv or xml data and convert to array
     *
     * @param array $options
     *
     * @return array
     */
    protected function _loadData($options)
    {
        $fileName = $this->config['source'] ?? null;
        if (empty($fileName)) {
            throw new Exception('Missing file name');
        }

        // CSV
        if ($this->_getMode() === 'csvfile') {
            return Files::loadCsv($fileName, $options);
        }

        // XML folder
        elseif ($this->_getMode() === 'xmlfolder') {
            // Recalculate offset from page
            $options['limit'] = $this->limit;
            $options['page'] = $options['page'] ?? 1;
            $options['offset'] = ($options['limit']) * ((int)$options['page'] - 1);

            return Files::loadXmlFolder($fileName, $options);
        }

        // XML file
        elseif ($this->_getMode() === 'xmlfile') {
            // Only one file
            if ((($options['page'] ?? 1) !== 1) || (($options['offset'] ?? 0) !== 0)) {
                return [];
            }
            else {
                return Files::loadXml($fileName, $options);
            }
        }

        else {
            return [];
        }
    }

    /**
     * Save the entities to the database.
     *
     * 1. Clears entities if the action-field equals "clear".
     * 2. Saves table by table
     * 3. Resolves links
     *
     * @param $entities
     *
     * @return bool
     */
    protected function _saveEntities($entities)
    {
        /** @var \Epi\Model\Table\BaseTable $model */
        $tableName = $this->config['table'] ?? null;
        $model = $this->getModel($tableName, 'Epi');
        $importBehavior = $model->getBehavior('Import');
        $importConfig = [
            'skip' => $this->config['skip'] ?? [],
            'tree' => $this->config['tree'] ?? true,
            'versions' => $this->config['versions'] ?? false,
            //'dates' => $this->config['dates'] ?? false,
            //'fulltext' => $this->config['fulltext'] ?? false,
            'timestamps' => $this->config['timestamps'] ?? true,
            'job_id' => $this->id,
        ];

        $result = $model->saveEntities($entities, $importConfig, $this->getIndex());
        $this->addTaskErrors($importBehavior->getErrors());

        return $result;
    }

    /**
     * Copy files in transfer job
     *
     * @param $enitites
     * @return void
     */
    protected function _copyFiles($enitites)
    {
        if ($this->config['files'] ?? false) {
            $rootSource = Configure::read('Data.databases') . Databank::addPrefix($this->config['source']) . DS;
            $rootTarget = Configure::read('Data.databases') . Databank::addPrefix($this->config['database']) . DS;

            foreach ($enitites as $entity) {
                $fileName = $entity->_import_copyfile;
                if (!empty($fileName)) {
                    $success = Files::copyFile($fileName, $rootSource, $rootTarget, true);
                    if (!$success) {
                        Log::write('error', __('Could not copy file {0}.', $fileName));
                    }
                }
            }
        }
    }

    /**
     * Create missing entitities
     *
     * @return void
     */
    protected function _fillEntities()
    {
        $unsolved = count($this->_index['sources'] ?? []);
        if ($unsolved) {
            $msg = __('{0} unsolved references.', $unsolved);
            Log::write('warning', $msg, ['scope' => 'jobs']);
        }

        //TODO: Create dummy entities for unresolved references?
    }


    /**
     * Extract all column names from an array of entities
     *
     * @param $rows
     *
     * @return array
     */
    protected function _extractCols($rows)
    {
        $cols = collection($rows)
            ->reduce(
                function ($accumulated, $item) {
                    $accumulated['ids'] = array_unique(array_merge($accumulated['ids'], $item->getIdFields()));
                    $accumulated['content'] = array_unique(array_merge($accumulated['content'], $item->fieldsImport));
                    return $accumulated;
                },
                ['ids' => ['id', 'norm_iri'], 'content' => []]
            );
        return array_unique(array_merge($cols['ids'], $cols['content']));
    }

    /**
     * Preview
     *
     * @param $options
     *
     * @return array
     */
    public function task_preview($options)
    {
        // Tune limit
        if ($this->_getMode() === 'xmlfolder') {
            $this->limit = 1;
        }
        elseif ($this->config['table'] === 'articles') {
            // Limit preview because Articles are huge
            // TODO: define limit in model
            $this->limit = 1;
        }

        $this->activateDatabank($this->config['database']);
        $rows = $this->_loadEntities($options);
        $cols = $this->_extractCols($rows);
        $count = $this->_getCount($options);

        return [
            'rows' => $rows,
            'cols' => $cols,
            'count' => $count
        ];
    }

    /**
     * Initialize task
     *
     * @return bool True if the task is finished
     */
    protected function task_init()
    {
        // Tune limit
        if ($this->_getMode() === 'xmlfolder') {
            $this->limit = 1;
        }
        elseif ($this->config['table'] === 'articles') {
            // Limit preview because Articles are huge
            // TODO: define limit in model
            $this->limit = 100;
        }

        $options = $this->config;

        // TODO: use Job->initPipeline()
        $options['pipeline_name'] = $this->jobName;
        $options['pipeline_tasks'] = [['number' => 1, 'type' => 'import']];
        $options['pipeline_progress'] = 0;

        // TODO: use Job->initProgress()
        $count = $this->_getCount($options);
        $this->progressmax += ceil($count / $this->limit) + 1;
        $this->progress = 0;

        $this->_clearIndex();
        $this->_index = ['sources' => [], 'targets' => []];

        $this->config = $options;

        $this->activateDatabank($this->config['database']);

        return true;
    }

    /**
     * Imports data
     *
     * 1. Load entities from source (csv or database)
     * 2. Saves entities to the target
     *
     * @return bool whether the task is finished
     */
    public function task_import()
    {
        // Tune limit
        if ($this->_getMode() === 'xmlfolder') {
            $this->limit = 1;
        }
        elseif ($this->config['table'] === 'articles') {
            // Limit because Articles are huge
            // TODO: define limit in model
            $this->limit = 100;
        }

        // Activate databank
        $this->activateDatabank($this->config['database']);

        // Load cache
        //TOOD: already loaded in Job->work, don't double load
        $this->_loadIndex();

        // Get current task
        // (csv import uses offset, transfer uses page)
        $current = $this->getCurrentTask();
        $current['offset'] = $current['offset'] ?? 0;
        $current['page'] = $current['page'] ?? 1;

        // Load entities
        $data = $this->_loadEntities([
            'offset' => $current['offset'],
            'page' => $current['page'],
            'limit' => $this->limit
        ]);

        // Save entities
        $this->_saveEntities($data);

        // Copy files
        $this->_copyFiles($data);

        // Create missing entities
        if ($this->_loadedRows < $this->limit) {
            $this->_fillEntities();
        }

        // Store cache
        $this->_saveIndex();

        // Update task
        $current['page'] += 1;
        $current['offset'] += $this->limit;
        $this->updateCurrentTask($current);

        return ($this->_loadedRows < $this->limit);
    }

}
