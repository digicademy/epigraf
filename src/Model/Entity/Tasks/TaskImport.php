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
use App\Utilities\Files\Files;
use Cake\Core\Configure;
use Cake\Log\Log;
use Exception;

/**
 * Import data in the import pipeline
 */
class TaskImport extends BaseTask
{

    /**
     * Number of rows currently loaded
     *
     * @var int
     */
    protected int $_loadedRows = 0;

    /**
     * Get the number of entities to process in one batch
     *
     * @param boolean $preview Set to true in preview mode to reduce the number
     * @return int
     * @throws Exception
     */
    protected function _getLimit($preview = false)
    {

        if ($this->getCurrentInputMode() === 'folder') {
            return $preview ? 1: 50;
        }
        elseif ($this->job->config['table'] === 'articles') {
            return $preview ? 1: 100;
        }
        else {
            return $this->job->limit;
        }

    }

    /**
     * Load csv or xml data and convert to array
     *
     * @param array $options
     * @param boolean $preview Set to true to reduce the number of loaded rows in preview mode
     *
     * @return array
     */
    protected function _loadData($options, $preview = false)
    {
        $inputFile = $this->getCurrentInputFilePath();

        if (empty($inputFile)) {
            throw new Exception('Missing file name');
        }

        $inputMode = $this->getCurrentInputMode();

        // CSV
        if ($inputMode === 'csv') {
            $rows = Files::loadCsv($inputFile, $options);
            $this->_loadedRows += count($rows);
            return($rows);
        }

        // XML file
        elseif ($inputMode === 'xml') {
            // Only one file
            if ((($options['page'] ?? 1) !== 1) || (($options['offset'] ?? 0) !== 0)) {
                return [];
            }
            else {
                $this->_loadedRows += 1;
                return Files::loadXml($inputFile, $options);
            }
        }

        // XML folder
        elseif ($inputMode === 'folder') {
            // Recalculate offset from page
            $options['limit'] = $this->_getLimit($preview);
            $options['page'] = $options['page'] ?? 1;
            $options['offset'] = ($options['limit']) * ((int)$options['page'] - 1);

            $this->_loadedRows += Files::getXmlFolderCount($inputFile, $options);
            return Files::loadXmlFolder($inputFile, $options);
        }

        else {
            return [];
        }
    }

    /**
     * Load data and convert it to entities
     *
     * 1. Load data from the source (csv file or database)
     * 2. Converts the data to entities
     *
     * @param $options
     * @param boolean $preview Set to true to reduce the number of loaded rows in preview mode
     *
     * @return array
     */
    protected function _loadEntities($options, $preview = false)
    {
        $data = [];
        $this->_loadedRows = 0;
        try {
            $options = array_merge($this->job->config, $options);
            $data = $this->_loadData($options, $preview);

            $this->activateDatabank($this->job->config['database']);

            /** @var \Epi\Model\Table\BaseTable $model */
            $tableName = $this->job->config['table'] ?? null;
            $model = $this->job->getModel($tableName, 'Epi');

            $data = $model->toEntities(
                $data,
                $this->job->getIndex(),
                [
                    'job_id' => $this->job->id ?? null,
                    'skipUpdates' => $this->job->config['skip'] ?? []
                ]
            );

            $importBehavior = $model->getBehavior('Import');
            $this->job->addTaskErrors($importBehavior->getErrors());

        } catch (Exception $e) {
            $msg = __('Error loading data from source "{source}": {error}.',
                ['source' => $options['source'] ?? '', 'error' => $e->getMessage()]);
            $this->job->addTaskError($msg, ['source' => $options['source'] ?? ''], $e);
        }
        return $data;
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
        $tableName = $this->job->config['table'] ?? null;
        $model = $this->job->getModel($tableName, 'Epi');

        $importConfig = [
            'skipUpdates' => $this->job->config['skip'] ?? [],
            'tree' => $this->job->config['tree'] ?? true,
            'versions' => $this->job->config['versions'] ?? false,
            'timestamps' => $this->job->config['timestamps'] ?? true,
            'job_id' => $this->job->id,
            //'dates' => $this->job->config['dates'] ?? false,
            //'fulltext' => $this->job->config['fulltext'] ?? false,
        ];

        $result = $model->saveEntities($entities, $importConfig, $this->job->getIndex());

        $importBehavior = $model->getBehavior('Import');
        $this->job->addTaskErrors($importBehavior->getErrors());
        if ($this->job->config['solved'] ?? false) {
            $this->job->addResultData(['solved' => $importBehavior->getSolved()]);
        }

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
        if ($this->job->config['files'] ?? false) {
            $rootSource = Configure::read('Data.databases') . Databank::addPrefix($this->job->config['source']) . DS;
            $rootTarget = Configure::read('Data.databases') . Databank::addPrefix($this->job->config['database']) . DS;

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
        $unsolved = count($this->job->getIndex()['sources'] ?? []);
        if ($unsolved) {
            $msg = __('{0} unsolved references.', $unsolved);
            Log::write('warning', $msg, ['scope' => 'jobs']);
        }

        //TODO: Create dummy entities for unresolved references?
    }

    /**
     * Get count of csv rows or xml files for the progress bar
     * @return int
     */
    protected function _getCount()
    {
        $inputFile = $this->getCurrentInputFilePath();

        if (empty($inputFile)) {
            throw new Exception('Missing file name');
        }

        $inputMode = $this->getCurrentInputMode();

        // CSV file
        if ($inputMode === 'csv') {
            return Files::countCsv($inputFile);
        }

        // XML file
        elseif ($inputMode === 'xml') {
            return 1;
        }

        // XML folder
        elseif ($inputMode === 'folder') {
            return count(Files::getFiles($inputFile, '.xml'));
        }

        else {
            return 0;
        }
    }


    /**
     * Return extracted data for the preview
     *
     * @param array $options
     * @return array An array with the following keys:
     *               - 'rows': An array of entities loaded from the source.
     *               - 'cols': An array of column names extracted from the entities.
     *               - 'count': The total number of entities that would be processed.
     * @throws Exception
     */
    public function preview($options = [])
    {
        $this->activateDatabank($this->job->config['database']);
        $rows = $this->_loadEntities($options, true);
        $cols = $this->_extractCols($rows);
        $count = $this->_getCount();

        return [
            'rows' => $rows,
            'cols' => $cols,
            'count' => $count
        ];
    }

    /**
     * How many calls of execute will be needed to finish the task?
     *
     * @return int
     */
    public function progressMax()
    {
        $count = $this->_getCount();
        $calls = max(1, ceil($count / $this->_getLimit()));
        return $calls;
    }

    /**
     * Reset the task progress
     *
     * @return true
     */
    public function init()
    {
        $this->config['offset'] = 0;
        $this->config['page'] = 1;
        return true;
    }

    /**
     * Import data
     *
     * 1. Load entities from source (csv or database)
     * 2. Saves entities to the target
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        // Activate databank
        $this->activateDatabank($this->job->config['database']);

        // Get current task config
        // (csv import uses offset, transfer uses page)
        $current = $this->config;
        $current['offset'] = $current['offset'] ?? 0;
        $current['page'] = $current['page'] ?? 1;
        $current['limit'] = $this->_getLimit();

        // Load entities
        $data = $this->_loadEntities([
            'offset' => $current['offset'],
            'page' => $current['page'],
            'limit' => $current['limit']
        ]);

        // Save entities
        $this->_saveEntities($data);

        // Copy files
        $this->_copyFiles($data);

        // Create missing entities
        if ($this->_loadedRows < $current['limit']) {
            $this->_fillEntities();
        }

        // Update task
        $current['page'] += 1;
        $current['offset'] += $current['limit'];
        $this->job->updateCurrentTaskConfig($current);

        return ($this->_loadedRows < $current['limit']);
    }

}
