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

use App\Utilities\Converters\Attributes;

/**
 * Output article data in the export pipeline
 */
class TaskDataArticles extends BaseTaskData
{

    /** @var string The model name that will be used to find the table. */
    public $model = 'Articles';

    /** @var string The wrapper or an empty array. */
    public $wrap = [];

    /** @var bool Row-wise output */
    public $rowwise = true;

    /**
     * Update job data parameters with task data parameters
     *
     * @return array
     */
    public function getDataParams()
    {
        if (!empty($this->config['matchprojects'])) {
            $dataparams = [
                'projects' => $this->job->config['params']['projects'] ?? '-1'
            ];
        }
        else {
            $dataparams = $this->job->dataParams;
        }
        $dataparams['articletypes'] = Attributes::commaListToStringArray($this->config['articletypes'] ?? '');
        $dataparams['snippets'] = array_merge($dataparams['snippets'] ?? [],
            ['indexes', 'paths', 'editors', 'comments']);

        return $dataparams;
    }

    /**
     * Get paging parameters
     *
     * @return array An array with the keys offset and limit, and optionally sort
     */
    public function getPagingParams()
    {
        $paging = [
            'offset' => $this->config['offset'],
            'limit' => $this->job->limit
        ];


        if (!empty($this->job->config['params']['sort'])) {
            $sortField = $this->job->config['params']['sort'];
            $paging['sort'] = [$sortField => 'ASC'];
        }

        return $paging;
    }

    /**
     * Rendering options passed to the renderContent method of the view class
     *
     * @return array[]
     */
    public function getRenderOptions()
    {
        $options = parent::getRenderOptions();
        $options['params']['snippets'] = array_merge($options['params']['snippets'] ?? [],
            ['indexes', 'paths', 'editors', 'comments']);
        return $options;
    }

}
