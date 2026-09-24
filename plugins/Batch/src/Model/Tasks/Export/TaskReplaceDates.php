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

use App\Utilities\Converters\HistoricDates;
use App\Utilities\Files\Files;

/**
 * Replace patterns in file
 */
class TaskReplaceDates extends TaskReplace
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

        //Replace
        foreach ($todo as $inputFile) {

            $outputFile = $outputPath;
            if ($inputMode === 'folder') {
                Files::createFolder($outputPath, true);
                $outputFile .= DS . basename($inputFile);
            }

            $filecontent = file_get_contents($inputFile);

            $tagMap = [
                'datekey' => 'encode',
                'datestart'     => 'minyear',
                'dateend'       => 'maxyear',
            ];

            foreach ($tagMap as $tag => $method) {
                $filecontent = preg_replace_callback(
                    '/<' . $tag . '>([^<]+)<\/' . $tag . '>/',
                    fn($matches) => '<' . $tag . '>' . HistoricDates::$method($matches[1]) . '</' . $tag . '>',
                    $filecontent
                );
            }

            Files::replaceFile($outputFile, $filecontent);
            $out[] = $outputFile;
        }
        return $out;
    }
}
