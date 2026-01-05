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
use App\Utilities\Converters\Csv;
use App\Utilities\Files\Files;
use Exception;

/**
 * Convert csv to xlsx file
 */
class TaskXlsx extends BaseTask
{

    /**
     * Convert csv to xlsx file
     *
     * The task has one option:
     * - source: The file within the job folder to be converted
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $source = $this->config['source'] ?? null;
        if (empty($source)) {
            $source = $this->getCurrentInputFilePath();
        }
        else {
            $source = Files::joinPath([$this->job->jobPath, $source]);
        }

        $outputfile = $this->getCurrentOutputFilePath();

        try {
            Csv::csvToxlsx($source, $outputfile);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
