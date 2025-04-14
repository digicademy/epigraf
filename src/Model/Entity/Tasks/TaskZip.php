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
 * Zip a file or folder
 */
class TaskZip extends BaseTask
{

    /**
     * Zip a folder or file.
     *
     * The task has one option:
     * - source: The folder or file within the job folder to be zipped
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $source = $this->config['source'] ?? null;
        if (empty($source)) {
            $source = $this->job->getCurrentInputFilePath();
        }
        else {
            $source = Files::joinPath([$this->job->jobPath, $source]);
        }

        $outputfile = $this->job->getCurrentOutputFilePath();

        if (is_dir($source)) {
            $result = Files::zipFolder($source, $outputfile);
        }
        else {
            $result = Files::zipFile($source, $outputfile);
        }

        return true;
    }
}
