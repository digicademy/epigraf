<?php
/*
 *  Epigraf 4.0
 *
 * @author     Epigraf team
 * @contact    jakob.juenger@adwmainz.de
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace App\Model\Entity\Tasks;

use App\Model\Entity\BaseTask;
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;

/**
 * Show a download list
 */
class TaskExportDownload extends BaseTask
{

    /**
     * Copy files to a download destination and show a download list
     *
     * Supports the following config keys:
     * - root One of database, job, shared. The root of the download destination.
     * - target A folder within the root. Can contain placeholders in curly brackets that refer to job data.
     *   For example, give the job a name "awesomebook113" and then copy the result to "data/books/job-{name}"
     *   within the shared folder. You must make sure the folder name is valid. Thus, usually you should not rely
     *   on placeholders alone, add at least some prefix.
     * - files Each filename on a new line.
     *         Files may be prefixed with captions, separated by an equal sign.
     *         Example: Final Book=book.xml.
     *         The filename can contain placeholders in curly brackets that refer to job data.
     *         All files must already exist in the job folder.
     *
     * When running a pipeline, jumps to the job finish stage.
     *
     * @return bool Return true because the task is always finished in one step.
     */
    public function execute()
    {
        $current = $this->config;

        // Folders
        $root = $this->config['root'] ?? null;
        if ($root === 'database') {
            $rootPath = $this->job->databasePath;
        }
        elseif ($root === 'shared') {
            $rootPath = $this->job->sharedPath;
        }
        else {
            $rootPath = $this->job->jobPath;
        }

        $sourcePath =  $this->job->jobPath;
        $target = $this->config['target'] ?? '';
        $target = Files::cleanPath(Attributes::replacePlaceholders($target, $this->job));

        $targetPath = Files::addSlash(Files::addSlash($rootPath) . $target);

        // File names
        $downloads = str_replace("\r\n", "\n", $current['files'] ?? '');
        $downloads = explode("\n", $downloads);

        foreach ($downloads as $idx => $item) {
            $item = explode('=', $item);

            $downloadFilename = $item[1] ?? $item[0];
            $downloadFilename = Files::cleanPath(
                Attributes::replacePlaceholders($downloadFilename, $this->job),
                false
            );

            $downloadCaption = sizeof($item) > 1 ? $item[0] : $downloadFilename;

            $downloads[$idx] = [
                'filename' => $downloadFilename,
                'caption' => $downloadCaption
            ];
        }

        // Copy files to destination
        if (!empty($target) && ($root !== 'job')) {
            foreach ($downloads as $item) {
                Files::copyFiles($item['filename'], null, $sourcePath, $targetPath, true);
            }
        }

        // Create download array
        $downloads = array_map(function ($item) use ($root, $target) {
            return [
                'caption' => $item['caption'],
                'name' => $item['filename'],
                'root' => $root,
                'target' => $target,
                'url' => '/jobs/execute/' . $this->job->id . '?download=' . ($item['filename'])
            ];
        }, $downloads);

        $this->job->result = ['downloads' => $downloads];
        $this->job->status = 'finish';
        return true;
    }

}
