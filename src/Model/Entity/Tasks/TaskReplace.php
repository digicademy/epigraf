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
 * Replace patterns in file
 */
class TaskReplace extends BaseTask
{

    /**
     * Replace patterns in file
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $current = $this->config;

        //Get patterns
        $regexes = file_get_contents(Configure::read('Data.shared') . $current['replacefile']);
        $regexes = preg_split('/\R/', $regexes);
        if (!is_array($regexes)) {
            return true;
        }

        /** @var array $regexes */
        $regexSearch = range(0, count($regexes) - 2, 2);
        $regexSearch = array_values(array_intersect_key($regexes, array_combine($regexSearch, $regexSearch)));
        $regexSearch = array_map(function ($x) {
            return (substr($x, 0, 1) === "/") ? $x : ('/' . $x . '/');
        }, $regexSearch);

        $regexReplace = range(1, count($regexes) - 1, 2);
        $regexReplace = array_values(array_intersect_key($regexes, array_combine($regexReplace, $regexReplace)));

        //Replace
        $inputfile = $this->getCurrentInputFilePath();
        $filecontent = file_get_contents($inputfile);

        $filecontent = preg_replace($regexSearch, $regexReplace, $filecontent);
        $filecontent = str_replace("\r", "", $filecontent);

        $outputfile = $this->getCurrentOutputFilePath();
        Files::replaceFile($outputfile, $filecontent);

        return true;
    }

}
