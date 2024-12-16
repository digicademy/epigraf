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

/**
 * Transfer data from one database into another
 */
class JobTransfer extends JobImport
{

    /**
     * Job name
     *
     * @var string
     */
    public $jobName = 'Transfer data between databases';

    /**
     * Number of records to transfer in each iteration
     *
     * @var int
     */
    public int $limit = 1000;

    protected function _getOptions()
    {
        return [
            'versions' => __('Init versioning'),
            'tree' => __('Recover trees'),
            //'fulltext' => __('Recreate fulltext index'),
            //'dates' => __('Reindex dates'),
            'files' => __('Copy files'),
            'snippets' => [
                'caption' => __('Snippets'),
                'type' => 'multi',
                'key' => 'params',
                'options' => [
                    'iris' => 'iris',
                    'published' => 'published',
                    'search' => 'search',
                    'editors' => 'editors',
                    'comments' => 'comments'
                ]
            ],
            'published' => [
                'caption' => __('Publication states'),
                'type' => 'multi',
                'key' => 'params',
                'prefix' => 'val_',
                'options' => $this->_getPublishedOptions()
            ]
        ];
    }

    /**
     * Load the data from the source database
     *
     * @param $options
     *
     * @return array
     */
    protected function _loadData($options)
    {
        //TODO: transfer rows by specific query params (done?)
        $databank = $this->activateDatabank($this->config['source']);
        $model = $this->getModel($this->config['table'], $databank->plugin);

        // Recalculate offset from page
        $options['limit'] = $this->limit;
        $options['page'] = $options['page'] ?? 1;
        $options['offset'] = ($options['limit']) * ((int)$options['page'] - 1);

        $params = $this->config['params'] ?? [];
        $params['scope'] = $options['scope'] ?? null;

        $data = $model->getExportData($params, $options, null);

        // TODO: Why not pass $params directly? Some side effects of parameters?
        $transferOptions = [
            'snippets' => $params['snippets'] ?? [],
            'published' => $params['published'] ?? [],
            'copy' => $params['copy'] ?? false,
            'files' => $params['files'] ?? false,
        ];

        $rows = [];
        array_walk($data, function (&$row, $key) use ($transferOptions, &$rows) {
            //Unnest
            $unnested = $row->getDataForTransfer($transferOptions);

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
     * @param $options
     *
     * @return int
     */
    protected function _getCount($options)
    {
        $this->activateDatabank($this->config['source']);
        $model = $this->getModel($this->config['table'], 'Epi');

        $params = $this->config['params'] ?? [];
        $count = $model->getExportCount($params);

        return $count;

    }

}
