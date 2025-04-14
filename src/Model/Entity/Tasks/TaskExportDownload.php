<?php
/*
 *  Epigraf 4.0
 *
 * @author     Epigraf team
 * @contact    jakob.juenger@adwmainz.de
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace App\Model\Entity\Tasks;

use App\Model\Entity\BaseTask;

/**
 * Show a download list
 */
class TaskExportDownload extends BaseTask
{

    /**
     * Jump to the result state which shows a download list and handles single file downloads
     *
     * @return bool Return true because the task is finished
     */
    public function execute()
    {
        $current = $this->job->getCurrentTask();
        $downloads = str_replace("\r\n", "\n", $current['files'] ?? '');
        $downloads = explode("\n", $downloads);

        $downloads = array_map(function ($item) {
            $item = explode('=', $item);
            return [
                'caption' => $item[0],
                'name' => $item[1] ?? $item[0],
                'url' => '/jobs/execute/' . $this->job->id . '?download=' . ($item[1] ?? $item[0])
            ];
        }, $downloads);

        $this->job->config['downloads'] = $downloads;

        $this->job->status = 'finish';
        return true;
    }

}
