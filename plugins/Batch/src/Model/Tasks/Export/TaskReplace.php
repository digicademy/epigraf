<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Batch\Model\Tasks\Export;

use App\Utilities\Files\Files;
use Cake\Core\Configure;
use Batch\Model\Tasks\BaseTask;

/**
 * Replace patterns in file
 */
class TaskReplace extends BaseTask
{

    /**
     * Transform files
     *
     * @param array $todo Array of input file paths
     * @param string $outputPath In file mode, the output file name. In folder mode, the output folder path.
     * @param string $inputMode Set to 'file' to process a single file or to 'folder to process all files in a folder.
     * @return array Array of output file paths.
     */
    protected function processFiles($todo, $outputPath, $inputMode)
    {
        $out = [];
        $current = $this->config;

        //Get patterns
        $regexes = file_get_contents(Configure::read('Data.shared') . $current['replacefile']);
        $regexes = preg_split('/\R/', $regexes);
        if (!is_array($regexes)) {
            return $out;
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
        foreach ($todo as $inputFile) {

            $outputFile = $outputPath;
            if ($inputMode === 'folder') {
                Files::createFolder($outputPath, true);
                $outputFile .= DS . basename($inputFile);
            }

            $filecontent = file_get_contents($inputFile);
            $filecontent = preg_replace($regexSearch, $regexReplace, $filecontent);
            $filecontent = str_replace("\r", "", $filecontent);
            Files::replaceFile($outputFile, $filecontent);
            $out[] = $outputFile;
        }
        return $out;
    }

    /**
     * Preview transformation
     *
     * @param array $options
     * @return array
     */
    public function preview($options = [])
    {
        // Only one file
        if ((($options['page'] ?? 1) !== 1) || (($options['offset'] ?? 0) !== 0)) {
            return $options;
        }

        $inputFiles = [$this->getCurrentInputFilePath()];
        $outputPath = $this->getCurrentOutputFilePath();
        $out = $this->processFiles($inputFiles, $outputPath, 'file');

        // Pass first result file to next task
        if (empty($out[0])) {
            return [];
        }
        return ['inputpath' => $out[0]];

    }

    /**
     * Replace patterns in file
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $inputFiles = [$this->getCurrentInputFilePath()];
        $outputPath = $this->getCurrentOutputFilePath();
        $out = $this->processFiles($inputFiles, $outputPath, 'file');

        return count($out) > 0;
    }

}
