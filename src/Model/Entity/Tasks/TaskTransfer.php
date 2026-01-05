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
 * Transfer data from one database into another
 */
class TaskTransfer extends TaskImport
{

    /**
     * Get the number of entities to process in one batch
     *
     * @param boolean $preview Set to true in preview mode to reduce the number
     * @return int
     * @throws Exception
     */
    protected function _getLimit($preview = false)
    {
        if ($this->job->config['table'] === 'articles') {
            return $preview ? 1: 20;
        }
        else {
            return $this->job->limit;
        }
    }


    /**
     * Load the data from the source database
     *
     * @param array $options
     * @param boolean $preview Set to true to reduce the number of loaded rows in preview mode
     *
     * @return array
     */
    protected function _loadData($options, $preview = false)
    {
        //TODO: transfer rows by specific query params (done?)
        $databank = $this->activateDatabank($this->job->config['source']);
        $model = $this->job->getModel($this->job->config['table'], $databank->plugin);

        // Recalculate offset from page
        $options['limit'] = $this->_getLimit($preview);
        $options['page'] = $options['page'] ?? 1;
        $options['offset'] = ($options['limit']) * ((int)$options['page'] - 1);

        $params = $this->job->config['params'] ?? [];
        $params['scope'] = $options['scope'] ?? null;
        $params['copy'] = $options['copy'] ?? $params['copy'] ?? false;

        $data = $model->getExportData($params, $options, null);

        // TODO: Why not pass $params directly? Some side effects of parameters?
        $transferOptions = [
            'snippets' => $params['snippets'] ?? ['deprecated'],
            'published' => $params['published'] ?? [],
            'copy' => $params['copy'] ?? false,
            'files' => $params['files'] ?? false,
            'clear' => $params['clear'] ?? true
        ];

        $rows = [];
        array_walk($data, function (&$row, $key) use ($transferOptions, &$rows) {
            //Unnest
            $unnested = $row->getDataForTransfer($transferOptions);
            $this->_loadedRows += 1;

            // Merge
            $rows = array_merge($rows, $unnested);
        });

        // Inject row number
        $idx = 0; // ($options['offset'] ?? 0);
        array_walk($rows, function (&$row) use (&$idx) {
            $idx += 1;
            $row['#'] = $idx;
        });

        return $rows;
    }

    /**
     * Get count for the progress bar
     *
     * @return int
     */
    protected function _getCount()
    {
        $this->activateDatabank($this->job->config['source']);
        $model = $this->job->getModel($this->job->config['table'], 'Epi');

        $params = $this->job->config['params'] ?? [];
        $count = $model->getExportCount($params);

        return $count;
    }
}
