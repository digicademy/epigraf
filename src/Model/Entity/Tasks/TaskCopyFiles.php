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
use App\Utilities\Files\Files;

/**
 * Copy files in the export pipeline
 */
class TaskCopyFiles extends BaseTask
{

    /**
     * Copy folder or files.
     *
     * The task has three options:
     * - root: 'database' or 'shared' or 'job'
     * - source: The folder or file to be copied
     * - target: The target folder
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $root = $this->config['root'] ?? null;
        if ($root === 'database') {
            $rootPath = $this->job->databasePath;
        }
        elseif ($root === 'job') {
            $rootPath = $this->job->jobPath;
        }
        else {
            $rootPath = $this->job->sharedPath;
        }

        $source = $this->config['source'] ?? null;
        if (empty($source)) {
            throw new \Exception(__('Empty source path'));
        }

        $filterFile = $this->config['filter'] ?? null;
        $filterFile = empty($filterFile) ? null : ($this->job->jobPath . $filterFile);

        if (empty($filterFile) || !is_file($filterFile)) {
            $filter = null;
        }
        else {
            $filter = file($filterFile, FILE_IGNORE_NEW_LINES);
        }

        $target = $this->config['target'] ?? '';
        $targetPath = $this->job->jobPath . $target;

        // TODO: test whether all folder and file options work as expected, adjust accordingly
        Files::copyFiles($source, $filter, $rootPath, $targetPath);

        return true;
    }
}
