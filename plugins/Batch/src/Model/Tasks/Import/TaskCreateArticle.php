<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Batch\Model\Tasks\Import;

use App\Utilities\Files\Files;
use Exception;

/**
 * Create an article from a file in the import pipeline
 */
class TaskCreateArticle extends TaskImport
{

    /**
     * Craft article data
     *
     * @param array $options Input data
     * @return array Data in RAM format to be imported into the database
     */
    protected function _craftArticle($options)
    {
        $data = [
            ['#' => 1, 'id' => 'articles-tmp1', 'type' => 'default-files', 'name' => $options['originalFilename'], 'signature' => $options['originalFilename']],
            ['#' => 2,'id' => 'sections-tmp1', 'type' => 'default-files', 'articles_id' => 'articles-tmp1','name' => 'Files']
        ];
        $data[] = [
            '#' => 3,'id' => 'items-tmp1',  'type' => 'default-files', 'articles_id' => 'articles-tmp1', 'sections_id' => 'sections-tmp1',
            'sortno' => 1, 'file_name' => $options['sourceFile'], 'file_path' => $options['articleFolder'],
        ];
        return $data;
    }
    /**
     * Create an article entity linking to a file
     *
     * ### Options
     * - offset
     * - limit
     * - page
     *
     * @param array $options
     * @param boolean $preview In preview mode, the file is not copied
     *
     * @return array
     */
    protected function _loadData($options, $preview = false)
    {
        // Only one file
        if ((($options['page'] ?? 1) !== 1) || (($options['offset'] ?? 0) !== 0)) {
            return [];
        }

        $inputFile = $this->getCurrentInputFilePath();
        if (empty($inputFile)) {
            throw new Exception('Missing file name');
        }

        // The article signature is based on the original file name,
        // the name of the file in the article is based on the last file name in the pipeline
        $originalFilename = basename($options['inputpath'] ?? $inputFile);
        $articleSignature = $originalFilename;
        $articleFolder = Files::cleanPath($articleSignature);
        $sourceFile = basename($inputFile);

        // Copy file to article folder
        if (empty($preview)) {
            $targetFolder = Files::joinPath([$this->job->databasePath, 'articles', $articleFolder]);
            $targetPath = $targetFolder . DIRECTORY_SEPARATOR . $sourceFile;
            Files::createFolder($targetFolder, true);

            copy($inputFile, $targetPath);
        }

        // Create entity
        $this->_loadedRows += 1;
        $data = $this->_craftArticle(compact('originalFilename', 'sourceFile', 'articleFolder'));
        return $data;
    }

}
