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
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;
use Cake\Core\Configure;

/**
 * Save transformed file
 */
class TaskSave extends BaseTask
{

    /**
     * Use the caption as current output file name
     *
     * @return string
     */
    public function getCurrentOutputFileName()
    {
        if (empty($this->config['outputfile']) && !empty($this->job->id)) {
            $filename = $this->job->caption. '.' . $this->getCurrentOutputExtension();
            return Files::cleanFilename($filename);
        }

        return parent::getCurrentOutputFileName();

    }

    /**
     * Save transformed file
     *
     * The output file name can contain placeholders that refer to the job.
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {

        if ($this->job->status !== 'finish') {
            $inputfile = $this->getCurrentInputFilePath();
            $outputfile = $this->getCurrentOutputFilePath();

            if ($inputfile != $outputfile) {
                copy($inputfile, $outputfile);
            }

            if (!empty($this->config['bom'] ?? false)) {
                Files::addBom($outputfile);
            }
        }

        // Get filename
        $filename = $this->getCurrentOutputFileName();

        $current = $this->config;
        $current['files'] = $filename;
        $this->job->updateCurrentTaskConfig($current);

        // Set filename and Force download option
        $this->job->config['download'] = $filename;
        $this->job->config['force'] = !empty($current['download']);
        $this->job->status = 'finish';

        return true;
    }

}
