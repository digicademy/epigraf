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
 * Extract content from a file
 */
class TaskExportExtract extends BaseTask
{

    /**
     * Replace patterns in file
     *
     * @return bool Return true if the task is finished
     */
    public function execute(): bool
    {
        $current = $this->job->getCurrentTaskConfig();

        $path = $current['path'] ?? '';
        if ($path !== '') {
            $inputfile = $this->getCurrentInputFilePath();
            $matches = Files::extractXmlContent($inputfile, $path);
            $filecontent = implode("\n", $matches);

            // Save
            $outputfile = $this->getCurrentOutputFilePath();
            Files::replaceFile($outputfile, $filecontent);

            // Wrap
            Files::prependToFile($outputfile, str_replace("\r", "", $this->config['prefix'] ?? ''));
            Files::appendToFile($outputfile, str_replace("\r", "", $this->config['postfix'] ?? ''));
        }

        return true;
    }

}
