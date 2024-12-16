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
 * Bundle files in the export pipeline
 */
class TaskBundle extends BaseTask
{

    /**
     * Concatenate files in a folder
     *
     * Has the following options:
     * - source The source folder relative to the job folder
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {

        $outputfile = $this->job->getCurrentOutputFile();
        $sourcePath = rtrim($this->job->jobPath, DS);

        // Find all files in the source folder
        $source = $this->config['source'] ?? '';
        if ($source !== '') {
            $sourcePath .= DS . $source;
        }
        $sourceFiles = Files::getFiles($sourcePath);

        // Concat
        foreach ($sourceFiles as $sourceFile) {
            $sourceFile = $sourcePath . DS . $sourceFile;
            if (($outputfile !== $sourceFile) && (is_file($sourceFile))) {
                Files::concatFiles($outputfile, $sourceFile, "\n\n");
            }
        }

        // Wrap
        Files::prependToFile($outputfile, str_replace("\r", "", $this->config['prefix'] ?? ''));
        Files::appendToFile($outputfile, str_replace("\r", "", $this->config['postfix'] ?? ''));

        return true;
    }


}
