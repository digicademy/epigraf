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
use Cake\Core\Configure;

/**
 * Save transformed file
 */
class TaskSave extends BaseTask
{

    /**
     * Save transformed file
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        // Force download option
        $current = $this->job->getCurrentTask();
        $this->job->config['download'] = $current['download'] ?? 0;


        if ($this->job->status !== 'download') {
            $inputfile = $this->job->getCurrentInputFile();
            $outputfile = $this->job->getCurrentOutputFile();

            if ($inputfile != $outputfile) {
                copy($inputfile, $outputfile);
            }
        }

        $this->job->status = 'download';
        return true;
    }

}
