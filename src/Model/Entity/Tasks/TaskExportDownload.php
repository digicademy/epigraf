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
use App\Utilities\Files\Files;

/**
 * Show a download list
 */
class TaskExportDownload extends BaseTask
{

    /**
     * Copy files to a download destination and show a download list
     *
     * Supports the following config keys:
     * - root One of database, job, shared. The root of the download destination.
     * - target A folder within the root.
     * - files Each filename on a new line.
     *         Files may be prefixed with captions, separated by an equal sign.
     *         Example: Final Book=book.xml
     *
     * Jumps to the job finish stage.
     *
     * @return bool Return true because the task is finished
     */
    public function execute()
    {
        $current = $this->config;

        $root = $this->config['root'] ?? null;
        if ($root === 'database') {
            $rootPath = $this->job->databasePath;
        }
        elseif ($root === 'shared') {
            $rootPath = $this->job->sharedPath;
        }
        else {
            $rootPath = $this->job->jobPath;
        }

        $sourcePath =  $this->job->jobPath;
        $target = $this->config['target'] ?? '';
        $targetPath = Files::addSlash(Files::addSlash($rootPath) . $target);

        $downloads = str_replace("\r\n", "\n", $current['files'] ?? '');
        $downloads = explode("\n", $downloads);

        // Copy files to destination
        if (!empty($target) && ($root !== 'job')) {
            foreach ($downloads as $item) {
                $item = explode('=', $item);
                Files::copyFiles($item[1] ?? $item[0], null, $sourcePath, $targetPath, true);
            }
        }

        // Create download array
        $downloads = array_map(function ($item) use ($root, $target) {
            $item = explode('=', $item);
            return [
                'caption' => $item[0],
                'name' => $item[1] ?? $item[0],
                'root' => $root,
                'target' => $target,
                'url' => '/jobs/execute/' . $this->job->id . '?download=' . ($item[1] ?? $item[0])
            ];
        }, $downloads);

        $this->job->result = ['downloads' => $downloads];
        $this->job->status = 'finish';
        return true;
    }

}
