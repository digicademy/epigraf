<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Utilities\Files;

use App\Utilities\Converters\Objects;
use App\Utilities\XmlParser\XmlMunge;
use App\View\CsvView;
use Cake\Filesystem\Folder;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\FrozenTime;
use Cake\Utility\Hash;
use FilesystemIterator;
use Psr\Http\Message\UploadedFileInterface;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;
use SplFileInfo;
use SplFileObject;
use ZipArchive;
use GMagick;
use IMagick;
use XMLReader;

class Files
{


    /**
     * Definition of file types
     *
     * @var string[]
     */
    static public $thumbtypes = ['pdf', 'png', 'jpg', 'jpeg', 'tif', 'svg'];

    /**
     * Get a temporary file name
     *
     * @param string $prefix Temporary filename prefix
     * @return false|string
     */
    static function getTempFilename($prefix = 'temp', $extension = 'tmp')
    {
        return tempnam(sys_get_temp_dir(), $prefix) . '.' . $extension;
    }

    static function createTempFile($content)
    {
        $temp_filename = tempnam(sys_get_temp_dir(), 'content');
        file_put_contents($temp_filename, $content);
        return $temp_filename;
    }

    static function prependToFile($filename, $content)
    {
        $context = stream_context_create();
        $orig_file = fopen($filename, 'r', 1, $context);

        $temp_filename = tempnam(sys_get_temp_dir(), 'php_prepend_');
        file_put_contents($temp_filename, $content);
        file_put_contents($temp_filename, $orig_file, FILE_APPEND);

        fclose($orig_file);
        unlink($filename);
        rename($temp_filename, $filename);
    }

    /**
     * Append the content of one file to another
     *
     * @param string $filename The resulting file
     * @param string $newfile The file that should be appended
     * @param string $sep A separator added to the end
     * @return void
     */
    static function concatFiles($filename, $newfile, $sep = '')
    {
        $content = file_get_contents($newfile) . $sep;
        Files::appendToFile($filename, $content);
    }


    /**
     * Add content to an existing file or create a new file
     *
     * @param string $filename The file name
     * @param string $content The content to append
     * @param boolean $createFolder Whether to create the folder if it doesn't exist
     * @return void
     */
    static function appendToFile($filename, $content, $createFolder = false)
    {
        if (strpos($filename, '..') !== false) {
            throw new BadRequestException('The requested path contains `..` which is not allowed.');
        }

        if ($createFolder) {
            if (!is_dir(dirname($filename))) {
                mkdir(dirname($filename), 0777, true);
            }
        }

        $file = fopen($filename, "a");
        fwrite($file, $content);
        fclose($file);
    }

    static function replaceFile($filename, $content)
    {
        $file = fopen($filename, "w");
        fwrite($file, $content);
        fclose($file);
    }

    static function pruneFiles($path)
    {
        $files = glob($path . "*");
        $now = time();

        foreach ($files as $file) // 1 day old files and folders
        {
            if ($now - filemtime($file) >= 60 * 60 * 24 * 1) {
                if (is_file($file)) {
                    unlink($file);
                }
                elseif (is_dir($file)) {
                    Files::removeFolder($file);
                }
            }
        }
    }

    /**
     * Extract content from an XML file using a path expression
     *
     * @param string $inputFile The name of a XML file.
     * @param string $path Path expression (e.g., "root/item[@type='special']").
     * @return array An array of XML strings, one for each matched element.
     */
    static function extractXmlContent($inputFile, $path)
    {
        $result = [];
        $reader = new XMLReader();
        try {
            $reader->open($inputFile);

            // Parse the path expression into components
            $pathComponents = Objects::parsePath($path);

            $stack = [];

            // Process the XML file node by node
            while ($reader->read()) {
                if ($reader->nodeType === XMLReader::ELEMENT) {
                    $currentDepth = count($stack);

                    // Check if this level matches the path expression
                    $isMatch = isset($pathComponents[$currentDepth]) &&
                        $reader->localName === $pathComponents[$currentDepth]['tag'];

                    // Inline attribute matching
                    if ($isMatch && $pathComponents[$currentDepth]['attr'] && $pathComponents[$currentDepth]['value']) {
                        $attrName = str_replace('@', '', $pathComponents[$currentDepth]['attr']);
                        $attrValue = $reader->getAttribute($attrName);
                        $isMatch = $attrValue === $pathComponents[$currentDepth]['value'];
                    }

                    $stack[] = $isMatch;

                    if ($isMatch && $currentDepth + 1 === count($pathComponents)) {
                        $result[] = $reader->readOuterXML();
                    }
                }
                elseif ($reader->nodeType === XMLReader::END_ELEMENT) {
                    array_pop($stack);
                }
            }

        } finally {
            $reader->close();
        }

        return $result;
    }

    /**
     * Delete a folder and its content
     *
     * //TODO: merge with Files::delete
     *
     * See https://stackoverflow.com/questions/1653771/how-do-i-remove-a-directory-that-is-not-empty
     * @param $dirname
     */
    static function removeFolder($dirname)
    {
        if (!is_dir($dirname)) {
            throw new Exception('This is not a directory');
        }

        Files::delete($dirname);
    }

    /**
     * Create a folder if it doesn't exist
     *
     * @param string $dirname
     * @param bool $allowExisting Throw an exceptions, if the folder exists
     */
    static function createFolder($dirname, $allowExisting = false)
    {
        $dirname = rtrim($dirname, DS);
        if (!$allowExisting && file_exists($dirname)) {
            throw new BadRequestException(__('The folder already exists, please choose another name.'));
        }
        elseif (!file_exists($dirname)) {
            return mkdir($dirname, 0777, true);
        }
        return true;
    }

    /**
     * Empty folder if exists, then create if not exists
     *
     * @param $dirname
     * @throws Exception
     */

    static function recreateFolder($dirname)
    {
        if (is_dir($dirname)) {
            Files::removeFolder($dirname);
        }

        return mkdir($dirname, 0777, true);
    }

    static function fetchUrl($url, $filename)
    {

        if (file_exists($filename)) {
            throw new BadRequestException(__('The file already exists, please choose another filename.'));
        }

        // Open file
        $fp = fopen($filename, 'wb');
        try {
            // Initialize the cURL session
            $ch = curl_init($url);
            try {

                // Set options
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);

                // Perform download
                curl_exec($ch);

            } finally {
                // Close cURL session
                curl_close($ch);
            }

        } finally {
            // Close file
            fclose($fp);
        }

        return true;
    }

    /**
     * Return file permissions in -rwxrwxrwx format
     *
     * See https://www.php.net/manual/de/function.fileperms.php
     *
     * @param $filename
     * @return string
     */
    static public function filePermissions($filename)
    {

        if (!file_exists($filename)) {
            return '';
        }

        $perms = fileperms($filename);

        switch ($perms & 0xF000) {
            case 0xC000: // Socket
                $info = 's';
                break;
            case 0xA000: // Symbolischer Link
                $info = 'l';
                break;
            case 0x8000: // Regulär
                $info = 'r';
                break;
            case 0x6000: // Block special
                $info = 'b';
                break;
            case 0x4000: // Verzeichnis
                $info = 'd';
                break;
            case 0x2000: // Character special
                $info = 'c';
                break;
            case 0x1000: // FIFO pipe
                $info = 'p';
                break;
            default: // unbekannt
                $info = 'u';
        }

        // Besitzer
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x') :
            (($perms & 0x0800) ? 'S' : '-'));

        // Gruppe
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x') :
            (($perms & 0x0400) ? 'S' : '-'));

        // Andere
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x') :
            (($perms & 0x0200) ? 'T' : '-'));

        return $info;

    }

    /**
     * Clean filename by removing special characters
     *
     * @param string $filename
     * @param bool $lower Convert to lower case
     * @return string A clean filename
     */
    public static function cleanFilename($filename, $lower = true)
    {
        $filename = trim($filename);
        if ($lower) {
            $filename = mb_strtolower($filename);
        }

        $replacements = [
            'ü' => 'ue',
            'ä' => 'ae',
            'ö' => 'oe',
            'ß' => 'ss',
            '(' => '-',
            ')' => ''
        ];

        $filename = str_replace(array_keys($replacements), array_values($replacements), $filename);
        $filename = preg_replace('/[^a-zA-Z0-9+.~_-]/', '-', $filename);
        $filename = preg_replace('/((\.-)|(-\.))+/', '.', $filename);
        $filename = preg_replace('/\.+/', '.', $filename);
        $filename = preg_replace('/-+/', '-', $filename);

        return $filename;
    }

    /**
     * Get a clean path by removing special characters
     *
     * @param string $path
     * @param bool $lower Convert to lower case
     * @return string A clean path
     */
    public static function cleanPath($path, $lower = true)
    {
        $segments = explode('/', $path);
        $segments = array_map(fn($x) => Files::cleanFilename($x, $lower), $segments);
        return implode('/', $segments);
    }

    /**
     * Clean filenames in a folder by renaming them
     *
     * @param $path
     * @return integer Number of rename errors
     */
    public static function cleanFolder($path)
    {

        $path = rtrim($path, '/');

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $dir = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

        $errors = 0;
        foreach ($files as $name => $file) {
            $newname = $file->getPath() . DIRECTORY_SEPARATOR . Files::cleanFilename($file->getFilename());
            $errors += (int)(rename((string)$name, $newname) != true);
        }

        return $errors;
    }

    /**
     * Prepend a path segment if not empty
     *
     * @param string $prefix
     * @param string $path
     * @return string
     */
    public static function prependPath($prefix, $path)
    {
        $prefix = rtrim($prefix, '/');
        if (($prefix !== '') && ($path !== '')) {
            return $prefix . '/' . $path;
        }
        else {
            return $prefix . $path;
        }
    }

    /**
     * Unzip file
     *
     * @param string $filename The zip file
     * @param string $targetFolder The folder where the zip file will be extracted. Leave empty to use the folder of the zip file.
     * @return bool
     */
    static public function unzipFile($filename, $targetFolder = '')
    {
        if ($targetFolder === '') {
            $targetFolder = pathinfo(realpath($filename), PATHINFO_DIRNAME);
        }

        if (!is_dir($targetFolder)) {
            throw new BadRequestException('The target folder does not exist.');
        }


        $zip = new ZipArchive;
        if ($zip->open($filename) === true) {
            $zip->extractTo($targetFolder);
            $zip->close();
            return true;
        }

        return false;
    }

    /**
     * Zip folder
     *
     * @param string $path
     * @param string $outputfile The output filename. Leave empty to use the folder name as file name.
     * @return false|string
     */
    static public function zipFolder($path, $outputfile = '')
    {
        if (strpos($path, '..') !== false) {
            throw new BadRequestException('The requested path contains `..` which is not allowed.');
        }

        if (strpos($outputfile, '..') !== false) {
            throw new BadRequestException('The requested path contains `..` which is not allowed.');
        }

        $path = rtrim($path, '/');

        if (empty($outputfile)) {
            $zipname = TMP . 'zip' . DS . trim($path, '/') . '.zip';
        }
        else {
            $zipname = $outputfile;
        }

        if (!is_dir(dirname($zipname))) {
            mkdir(dirname($zipname), 0755, true);
        }

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($zipname, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if (is_dir($path)) {

            // Create recursive directory iterator
            /** @var SplFileInfo[] $files */
            $directory = new RecursiveDirectoryIterator($path);
            $files = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($files as $name => $file) {

                // Skip directories (they would be added automatically)
                if (!$file->isDir()) {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = ltrim(substr($filePath, strlen($path)), '/');

                    if (!is_file($filePath)) {
                        continue;
                    }

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
        return $zip->numFiles ? $zipname : false;
    }

    /**
     * Zip file
     *
     * @param string $path
     * @param string $outputfile The output filename. Leave empty to use the folder name as file name.
     * @return false|string
     */
    static public function zipFile($path, $outputfile = '')
    {

        if (empty($outputfile)) {
            $zipname = TMP . 'zip' . DS . basename($path) . '.zip';
        }
        else {
            $zipname = $outputfile;
        }

        if (!is_dir(dirname($zipname))) {
            mkdir(dirname($zipname), 0755, true);
        }

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open($zipname, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Add current file to archive
        if (is_file($path)) {
            $zip->addFile($path);
        }

        // Zip archive will be created only after closing the object
        $zip->close();

        return $zip->numFiles ? $zipname : false;
    }

    /**
     * File uploads
     *
     * @param $val
     * @return bool
     */
    static public function isUploadedFile($val)
    {
        return (!empty($val) && ($val->getError() == UPLOAD_ERR_OK));
    }


    /**
     * After uploading, store the file in $path. The filename will be derived from the
     * client filename.
     *
     * @param string $path Folder where to file will be stored
     * @param UploadedFileInterface $data Data from a post request
     * @param bool $replace
     * @param string $targetfile Filename used instead of the client filename.
     * @return array
     */
    static public function saveUploadedFile($path, $data, $replace = false, $targetfile = false)
    {
        $error = false;

        if (Files::isUploadedFile($data)) {
            $clientname = $data->getClientFilename();
        }
        else {
            $error = 'No file selected.';
            $clientname = '';
        }

        // Derive a distinct filename from the clientname
        if (empty($targetfile) && !empty($clientname)) {
            $targetfile = basename($clientname);
            $targetfile = Files::cleanFilename($targetfile);


            $no = 1;
            $path_parts = pathinfo($targetfile);
            while (!$replace && file_exists($path . DS . $targetfile) && ($no < 100)) {
                $targetfile = $path_parts['filename'] . '_' . $no . '.' . $path_parts['extension'];
                $no++;
            }
        }

        if (!$replace && !empty($targetfile) && file_exists($path . DS . $targetfile)) {
            $error = 'The file already exists.';
        }

        if (empty($error)) {
            try {
                $data->moveTo($path . DS . $targetfile);
            } catch (Exception $e) {
                $error = 'The file could not be moved to destiny.';
            }
        }

        return ['filename' => $targetfile, 'clientname' => $clientname, 'error' => $error, 'path' => $path];
    }

    static public function saveUploadedFiles($path, $multidata)
    {
        $files = ['uploaded' => [], 'renamed' => [], 'error' => []];

        foreach ($multidata as $data) {
            if (!Files::isUploadedFile($data)) {
                continue;
            }

            $file = Files::saveUploadedFile($path, $data);

            $files['filenames'][] = $file['filename'];
            if (!empty($file['error'])) {
                $files['error'][] = $file['error'];
            }
        }

        return $files;
    }

    /**
     * Copy file
     *
     * @param string $filepath File name and path, relative to the $rootSource,
     *                         without leading slash
     * @param string $rootSource Source folder with trailing slash
     * @param string $rootTarget Target folder with trailing slash
     * @param boolean $overwrite Overwrite existing target file
     * @return bool
     */
    static public function copyFile($filepath, $rootSource, $rootTarget, $overwrite = false)
    {

        $rootSource = Files::addSlash($rootSource);
        $rootTarget = Files::addSlash($rootTarget);
        $targetFolder = dirname($rootTarget . $filepath);

        if ($filepath === '') {
            return false;
        }
        elseif (strpos($filepath, '..') !== false) {
            throw new NotFoundException('The path contains `..` and will not be read.');
        }
        elseif (strpos($rootSource, '..') !== false) {
            throw new NotFoundException('The path contains `..` and will not be read.');
        }
        elseif (strpos($rootTarget, '..') !== false) {
            throw new NotFoundException('The path contains `..` and will not be read.');
        }
        elseif (!$overwrite && file_exists($rootTarget . $filepath)) {
            return false;
        }
        elseif (!Files::createFolder($targetFolder, true)) {
            return false;
        }
        else {
            return copy($rootSource . $filepath, $rootTarget . $filepath);
        }
    }

    /**
     * Copy multiple files or folders
     *
     * @param string $sourcePath File or folder to copy within the source root.
     * @param array|null $sourceFiles Only copy files in the list. Set to null to copy all files.
     * @param string $rootSource Source root folder
     * @param string $rootTarget Target root folder
     * @param boolean $overwrite Overwrite existing files
     * @return boolean
     */
    static public function copyFiles($sourcePath, $sourceFiles, $rootSource, $rootTarget, $overwrite = false)
    {
        // Prepare paths
        if (strpos($sourcePath, '..') !== false) {
            throw new NotFoundException('The path contains `..` and will not be read.');
        }
        elseif (strpos($rootSource, '..') !== false) {
            throw new NotFoundException('The path contains `..` and will not be read.');
        }
        elseif (strpos($rootTarget, '..') !== false) {
            throw new NotFoundException('The path contains `..` and will not be read.');
        }

        // File or folder?
        $sourceFolder = $rootSource . $sourcePath;
        if (is_file($sourceFolder)) {
            $sourceFiles = [basename($sourcePath)];
            $sourcePath = dirname($sourcePath);
            $sourceFolder = $rootSource . $sourcePath;
        }

        $targetFolder = $rootTarget;
        if (!Files::createFolder($targetFolder, true)) {
            return false;
        }

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $directory = new RecursiveDirectoryIterator($sourceFolder);
        $files = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $name => $file) {
            // Skip directories
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($sourceFolder));
                $targetPath = rtrim($targetFolder, DS) . DS . $relativePath;

                // Copy
                $matches = is_null($sourceFiles) || in_array($relativePath, $sourceFiles);
                $matches = $matches && ($overwrite || !file_exists($targetPath));
                if ($matches) {
                    Files::createFolder(dirname($targetPath), true);
                    copy($filePath, $targetPath);
                }
            }
        }
        return true;
    }

    /**
     * Rename file
     *
     * @param string $path Path with trailing DS
     * @param string $oldname
     * @param string $newname
     * @return bool
     */
    static public function renameFile($path, $oldname, $newname)
    {
        if (strpos($path . DS . $oldname . $newname, '..') !== false) {
            return false;
        }
        elseif (file_exists($path . DS . $newname)) {
            return false;
        }
        else {
            return rename($path . DS . $oldname, $path . DS . $newname);
        }
    }

    /**
     * Rename folder
     *
     * @param $path
     * @param $newname
     *
     * @return bool
     */
    static public function renameFolder($path, $newname)
    {
        $parent_dir = dirname($path) . DS;
        $child_dir = basename($path);
        return Files::renameFile($parent_dir, $child_dir, $newname);
    }

    /**
     * Move or rename file or folder
     *
     * @param string $root Common root of the file or folder
     * @param string $oldname Relative path of the file or folder
     * @param string $newname New relative path of the file or folder
     *
     * @return bool
     */
    static public function moveFileOrFolder($rootfolder, $oldname, $newname)
    {
        if (strpos($oldname . $newname, '..') !== false) {
            return false;
        }
        else {
            if (file_exists($rootfolder . DS . $newname)) {
                return false;
            }
            else {
                return rename($rootfolder . DS . $oldname, $rootfolder . DS . $newname);
            }
        }
    }

    /**
     * Move files and folders to new folder
     *
     * @param string $source Absolute source folder path
     * @param string $target Absolute target folder path
     * @param bool $overwrite Overwrite files
     * @param bool $copy Copy files
     * @return bool
     *
     * @var SplFileInfo[] $files
     */
    static public function moveContent($source, $target, $overwrite = false, $copy = false): bool
    {

        if (!file_exists($source)) {
            throw new BadRequestException('The source folder does not exist.');
        }

        if (!file_exists($target)) {
            throw new BadRequestException('The target folder does not exist.');
        }

        // Recurse files
        $directory = new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $sourcePath = $file->getRealPath();
                $relativePath = substr($sourcePath, strlen($source));
                $targetPath = rtrim($target, DS) . DS . $relativePath;

                // Skip existing files
                if (file_exists($targetPath) && !$overwrite) {
                    continue;
                }

                // Create target folder
                if (!is_dir(dirname($targetPath))) {
                    mkdir(dirname($targetPath), 0777, true);
                }

                // Move file
                if ($copy) {
                    copy($sourcePath, $targetPath);
                }
                else {
                    rename($sourcePath, $targetPath);
                }
            }
        }

        // Remove empty folders
        $emptyfolders = [];
        $directory = new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS);
        $items = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($items as $item) {
            if ($item->isDir()) {
                $isDirEmpty = new FilesystemIterator($item->getRealPath());
                if (!$isDirEmpty->valid()) {
                    $emptyfolders[] = $item->getRealPath();
                }
            }
        }

//        $isDirEmpty   = new FilesystemIterator($source);
//        if (!$isDirEmpty ->valid())
//            $emptyfolders[] = $source;

        foreach ($emptyfolders as $path) {
            rmdir($path);
        }

        return true;
    }

    /**
     * Remove a file or folder and all its content
     *
     * See https://stackoverflow.com/questions/1653771/how-do-i-remove-a-directory-that-is-not-empty
     *
     * @param string $file The path
     * @return bool
     */
    static public function delete($file)
    {
        if (!file_exists($file)) {
            throw new BadRequestException('The file or folder does not exist.');
        }

        if (is_dir($file)) {
            $objects = scandir($file);
            foreach ($objects as $object) {
                if ($object != "." && $object != ".." && !is_link($file . DS . $object)) {
                    Files::delete($file . DS . $object);
                }
            }
            return rmdir($file);

        }
        else {
            return unlink($file);
        }
    }

    /**
     * Remove all content from a folder
     *
     * @param string $path Folder path
     * @return bool Whether the operation completed without errors
     */
    static public function clearFolder($folder)
    {
        if (!is_dir($folder)) {
            throw new BadRequestException('The folder does not exist.');
        }

        $errors = 0;
        $objects = scandir($folder);
        foreach ($objects as $object) {
            if ($object != "." && $object != ".." && !is_link($folder . DS . $object)) {
                if (!Files::delete($folder . DS . $object)) {
                    $errors += 1;
                }
            }
        }

        return ($errors === 0);
    }

    /**
     * Save array data to a csv file
     *
     * @param string $path Folder where to file will be stored, without a trailing slash
     * @param array $data Data from a post request
     * @param bool $replace If false and the file exists, a new filename suffices with a number up to 99 is generated.
     * @param string $targetfile Filename
     * @return array
     */
    static public function saveCsv($path, $data, $replace = false, $targetfile = 'uploaded.csv')
    {
        $error = false;

        // Derive a distinct filename from the target file name
        if (!empty($targetfile)) {
            $targetfile = basename($targetfile);
            $targetfile = Files::cleanFilename($targetfile);


            $no = 1;
            $path_parts = pathinfo($targetfile);
            while (!$replace && file_exists($path . DS . $targetfile) && ($no < 100)) {
                $targetfile = $path_parts['filename'] . '_' . $no . '.' . $path_parts['extension'];
                $no++;
            }
        }

        if (!$replace && !empty($targetfile) && file_exists($path . DS . $targetfile)) {
            $error = 'The file already exists.';
        }

        if (empty($error)) {
            try {
                $view = new CsvView();
                $view->renderToFile($data, $path . DS . $targetfile);

            } catch (Exception $e) {
                $error = 'The data could not be saved to a csv file.';
            }
        }

        return ['filename' => $targetfile, 'error' => $error, 'path' => $path];
    }

    /**
     * Load CSV function
     *
     * @param string $filename filename
     * @param array $options to set
     *  - offset: the line number counting from 0, where 0 is the first row after the header line
     *  - limit: the maximum number of lines to read
     *  - fields: Array of fields to import. If empty, will be taken from first line.
     *  - rownumbers (default '#'): Column where rownumber should be stored or empty to omit rownumbers.
     *
     * @return array of all data from the csv file in [Model][field] format
     * @author Dean Sofer, Jakob Jünger
     */
    static public function loadCsv($filename, $options = [])
    {
        if (!is_file($filename)) {
            throw new Exception('No valid csv file');
        }

        $default = [
            'length' => 0,
            'delimiter' => ';',
            'enclosure' => '"',
            'escape' => '\\',
            'excel_bom' => false,
            'alias' => '',
            'rownumbers' => '#',
            'limit' => 100,
            'offset' => 0
        ];

        $options = array_merge($default, $options);
        if (isset($options['page'])) {
            $options['offset'] = $options['limit'] * ((int)$options['page'] - 1);
        }

        $data = [];

        $file = new SplFileObject($filename, 'r');
        $file->setFlags(
            SplFileObject::READ_CSV |
            SplFileObject::READ_AHEAD |
            SplFileObject::SKIP_EMPTY
        );
        $file->setCsvControl(
            $options['delimiter'],
            $options['enclosure'],
            $options['escape']
        );

        // Get fields from first line
        $fields = $options['fields'] ?? [];
        if (empty($fields)) {
            $fields = $file->fgetcsv() ?? [];

            foreach ($fields as $key => $field) {
                $field = trim($field);
                if (empty($field)) {
                    continue;
                }
                $fields[$key] = mb_strtolower($field);
            }
        }

        $file->seek($options['offset']);

        // Row counter
        $r = 0;

        // read each data row in the file
        while ($row = $file->fgetcsv()) {
            // for each header field
            foreach ($fields as $f => $field) {
                $row[$f] = trim($row[$f] ?? '');

                // get the data field from Model.field
                if (strpos($field, '.')) {
                    $keys = explode('.', $field);
                    if ($keys[0] == $options['alias']) {
                        $field = $keys[1];
                    }
                    if (!isset($data[$r])) {
                        $data[$r] = [];
                    }
                    $data[$r] = Hash::insert($data[$r], $field, $row[$f]);

                }
                else {
                    $data[$r][$field] = $row[$f];
                }

            }

            if (!empty($options['rownumbers'])) {
                $data[$r][$options['rownumbers']] = ($options['offset'] ?? 0) + $r + 1;
            }

            $r++;

            if ($r >= $options['limit']) {
                break;
            }
        }

        // close the file, return the data
        $file = null;
        return $data;
    }

    /**
     * Get the number of rows in a csv file
     *
     * @param $filename
     * @return int
     */
    static public function countCsv($filename, $includeHeader = false)
    {
        if (!is_file($filename)) {
            throw new Exception('No valid csv file');
        }

        $file = new SplFileObject($filename, 'r');
        $file->setFlags(
            SplFileObject::READ_CSV |
            SplFileObject::READ_AHEAD |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE
        );

        $file->seek(PHP_INT_MAX);

        return $file->key() + (int)$includeHeader;

    }

    /**
     * Get folder content
     *
     * @param string $path
     * @return array
     */
    static public function getFolderContent($path)
    {
        if (!is_dir($path)) {
            return [];
        }

        $folder_content = scandir($path);
        $folder_content = array_filter($folder_content, function ($x) {
            return $x !== '..';
        });
        $folder_content = array_map(function ($x) use ($path) {
            $x = $x === '.' ? '' : $x;
            return (['name' => $x, 'isfolder' => is_dir($path . DS . $x)]);
        }, $folder_content);

        return $folder_content;
    }

    /**
     * Get relative path
     *
     * @param $from
     * @param $to
     *
     * @return string
     */
    static public function getRelativePath($from, $to)
    {
        $dir = explode(DS, is_file($from) ? dirname($from) : rtrim($from, DS));
        $file = explode(DS, $to);

        while ($dir && $file && ($dir[0] == $file[0])) {
            array_shift($dir);
            array_shift($file);
        }
        return str_repeat('..' . DS, count($dir)) . implode(DS, $file);
    }

    /**
     * Get subfolders
     *
     * //TODO: replace by getFolderContent()
     *
     * @param string $basepath
     * @return string[]
     */
    static public function getFolders($basepath)
    {
        $dir = new Folder($basepath);

        $folders = $dir->tree();
        $folders = $folders[0];
        $folders = array_map(function ($x) use ($basepath) {
            return Files::getRelativePath($basepath, $x);
        }, $folders);
        $folders = array_map(function ($x) {
            return str_replace('\\', '/', $x);
        }, $folders);
        array_shift($folders);
        return $folders;
    }

    /**
     * Get all files in a folder
     *
     * @param string $path The folder name
     * @param string|null $ext Filter by file extension
     * @return array
     */
    static public function getFiles($path, $ext = null)
    {
        if (!is_dir($path)) {
            return [];
        }

        $files = scandir($path);
        $files = array_filter($files, function ($x) use ($path, $ext) {
            $isFile = !is_dir($path . DS . $x) && ($x !== '..') && ($x !== '.');
            $hasExt = ($ext !== null) ? (substr($x, -strlen($ext)) === $ext) : true;
            return $isFile && $hasExt;
        });

        return $files;
    }

    /**
     * Get files and folders contained in a folder
     *
     * @param string $basepath The basepath is removed from the resulting paths
     * @param string $path The folder to scan within the basepath
     *
     * @return array[]
     */
    static public function getBaseContent($basepath, $path)
    {
        $path = str_replace('\\', '/', $path);
        $dir = new Folder($basepath . DS . $path);
        $content = $dir->read();

        $files = array_map(function ($x) use ($basepath, $path) {
            return Files::getFileInfo($basepath, $path, $x);
        }, $content[1]);

        $folders = array_map(function ($x) use ($basepath, $path) {
            return Files::getFolderInfo($basepath, $path, $x);
        }, $content[0]);

        if (!empty($path)) {
            $path_segments = explode(DS, $path);
            array_pop($path_segments);
            $path_parent = implode(DS, $path_segments);

            array_unshift($folders, [
                'name' => '..',
                'isfolder' => true,
                'fullname' => $path_parent,
                'created' => false,
                'modified' => false,
                'filesize' => false,
                'permissions' => ''
            ]);
        }

        return array_merge($folders, $files);
    }


    /**
     * Get the name of the last created file
     *
     * @param string $path The absolute file path on the server
     * @return string|false
     */
    static public function getLatestFile($path)
    {
        $path = rtrim(str_replace('\\', '/', $path), 'DS') . DS;

        $files = glob($path . '*.*');
        $files = array_combine($files, array_map('filemtime', $files));
        arsort($files);

        return basename(key($files) ?? '');
    }

    /**
     * Get file info
     *
     * @param string $basepath
     * @param string $path Can be an empty string
     * @param string $name
     *
     * @return array
     */
    static public function getFileInfo($basepath, $path, $name)
    {
        $filepath = $basepath;
        $filepath .= empty($path) ? '' : DS . $path;
        $filepath .= DS . $name;

        $fullName = empty($path) ? $name : ($path . '/' . $name);

        if (!is_file($filepath)) {
            return [];
        }
        else {
            return [
                'name' => $name,
                'isfolder' => false,
                'path' => $path,
                'fullname' => $fullName,
                'type' => pathinfo($filepath, PATHINFO_EXTENSION),
                'modified' => FrozenTime::createFromTimestamp(filemtime($filepath)),
                'size' => Files::formatFileSize(filesize($filepath)),
                'permissions' => sprintf('%o', fileperms($filepath))
            ];
        }
    }

    /**
     * Get folder info
     *
     * @param $basepath
     * @param $path
     * @param $name
     *
     * @return array
     */
    static public function getFolderInfo($basepath, $path, $name)
    {
        $names = str_replace('\\', '/', $name);
        $names = explode('/', $names);
        $name = array_pop($names);

        $names = implode('/', $names);
        $path = empty($names) ? $path : $path . '/' . $names;
        $path = trim($path, '/');

        $fullName = empty($path) ? $name : ($path . '/' . $name);

        return [
            'name' => $name,
            'isfolder' => true,
            'path' => $path,
            'type' => false,
            'fullname' => $fullName,
            'modified' => FrozenTime::createFromTimestamp(filemtime($basepath . DS . $path . DS . $name)),
            'size' => count(glob($basepath . DS . $path . DS . $name . DS . '*.*')),
            'permissions' => sprintf('%o', fileperms($basepath . DS . $path . DS . $name))
        ];
    }

    /**
     * Strips the filename or the last folder segment from the path
     *
     * @param string $path
     * @return string
     */
    static public function getFolder($path)
    {
        $path = dirname($path);
        return $path === '.' ? '' : $path;
    }

    /**
     * Add a trailing slash to a path
     *
     * @param string $path
     * @return string
     */
    static public function addSlash($path)
    {
        if ($path !== '') {
            $path = rtrim($path, '/') . '/';
        }
        return $path;
    }

    /**
     * Concat path components
     *
     * @param array $segments The path segments.
     *                        Empty segments will be skipped.
     *                        The last delimiter will be removed in each segment.
     * @param string $separator The separator that glues together the segments
     * @return string The path, concatenated by the separator. Trailing separators will be removed.
     */
    static public function joinPath($segments, $separator = DS)
    {
        $segments = array_filter($segments, fn($x) => !empty($x));
        $segments = array_map(fn($x) => rtrim($x, $separator), $segments);
        return implode($separator, $segments);
    }

    /**
     * Format file size
     *
     * @param $size
     * @param $precision
     *
     * @return string
     */
    static public function formatFileSize($size, $precision = 1)
    {
        if ($size == 0) {
            return '0 B';
        }
        $base = log($size) / log(1024);
        $suffix = array("B", "kB", "MB", "GB", "TB")[floor($base)];
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffix;
    }

    /**
     * Get classes in path
     *
     * @param $path
     * @param $namespace
     * @param $ignoreList
     * @return string[]
     */
    public static function getClassesInPath($path, $namespace, $ignoreList = null)
    {
        if ($ignoreList === null) {
            $ignoreList = [
                '.',
                '..',
                'Component',
                'AppController.php',
            ];
        }
        $files = scandir($path);
        $files = array_diff($files, $ignoreList);

        $classes = array_map(function ($file) use ($namespace) {
            return $namespace . '\\' . basename($file, '.php');
        }, $files);

        return array_filter($classes, function ($possibleClass) {
            return class_exists($possibleClass);
        });
    }

    /**
     * Load Epigraf data from a XML file
     *
     * @param string $filename filename
     * @param array $options
     * @return array Data from the XML file in [Model][field] format
     */
    static public function loadXml($filename, $options = [])
    {
        $recordNames = ['project', 'article', 'section', 'item', 'footnote', 'property'];

        $rows = [];
        $elements = [];
        $currentElement = null;
        $currentField = null;
        $currentFieldLevel = 0;
        $annotations = [];

        $convertToRows = static function (&$element, &$parser)
        use (&$rows, &$elements, &$currentElement, &$currentFieldLevel, &$currentField, &$annotations, &$recordNames) {

            // Open records
            if (($element['position'] === 'open') &&
                (in_array($element['name'], $recordNames))) {

                // IDs
                $currentElement = $element['name'];
                $currentFieldLevel = 0;
                $elements[$currentElement] = $element['attributes'] ?? [];

                // belongsTo IDs / hasMany IDs
                foreach ($elements as $name => $value) {
                    if ($name !== $currentElement) {
                        $idField = $name === 'property' ? 'properties_id' : ($name . 's_id');
                        $elements[$currentElement][$idField] = $value['id'] ?? '';
                    }
                }

                // Link project
                if (($currentElement === 'project') && !empty($elements['article'])) {
                    $elements['article']['projects_id'] = $value['id'];
                }

                // Link footnote
                if ($currentElement === 'footnote') {
                    $elements[$currentElement] = array_merge(
                        $elements[$currentElement],
                        $annotations[$element['attributes']['from_tagid']] ?? []
                    );
                }
            }

            // Close records
            elseif (($element['position'] === 'close') && (in_array($element['name'], $recordNames))) {
                $rows[] = $elements[$element['name']] ?? [];
                unset($elements[$element['name']]);
                $currentElement = null;
                $currentFieldLevel = 0;
            }

            // Skip containers
            elseif ((in_array($element['name'], ['sections', 'items', 'footnotes']))) {
                $currentElement = null;
                $currentField = null;
                $currentFieldLevel = 0;
            }

            //Collect values
            elseif (($element['position'] === 'open') && ($currentElement !== null)) {

                $currentFieldLevel += 1;

                if ($currentFieldLevel === 1) {
                    $currentField = $element['name'];
                    $elements[$currentElement][$currentField] = $parser->parseCurrentElement();
                    $currentField = null;
                    $currentFieldLevel -= 1;
                }

                // Collect annotations
                if ($currentField) {

                    // Inject ID
                    if (empty($element['attributes']['id'])) {
                        $element['attributes']['id'] = $parser->uniqueid();
                    }

                    $currentId = $element['attributes']['id'];
                    $annotations[$currentId] = [
                        'from_id' => $elements[$currentElement]['id'] ?? null,
                        'from_field' => $currentField,
                        'from_tagname' => $element['name'],
                        'from_tagid' => $currentId
                    ];
                }
            }
            else {
                return false;
            }

            return true;
        };

        XmlMunge::parseXmlFile($filename, $convertToRows);

        //TODO: add sortno to sections and items

        return array_reverse($rows);
    }

    /**
     * Load XML files in a folder
     *
     * In the options array, the keys offset and limit can be used
     * to limit the files.
     *
     * @param string $path filename
     * @param array $options
     *
     * @return array of all data from the XML file in [Model][field] format
     */
    static public function loadXmlFolder($path, $options = [])
    {
        $default = [
            'rownumbers' => '#',
            'limit' => 100,
            'offset' => 0,
            'page' => 1
        ];

        $options = $options + $default;

        if (isset($options['page'])) {
            $options['offset'] = $options['limit'] * ((int)$options['page'] - 1);
        }

        $files = Files::getFiles($path, '.xml');
        $files = array_splice($files, $options['offset'], $options['limit']);

        $rows = [];
        foreach ($files as $fileName) {
            $newrows = Files::loadXml($path . DS . $fileName, $options);
            $rows = array_merge($rows, $newrows);
        }

        // Inject row number
        if (!empty($options['rownumbers'])) {
            $idx = 0;
            array_walk($rows, function (&$row) use (&$idx, $options) {
                $idx += 1;
                $row[$options['rownumbers']] = $idx;
            });
        }

        return $rows;
    }

    /**
     * Get thumb name
     *
     * @param $imagefile
     * @param $size
     * @param $placeholder
     *
     * @return mixed|string
     * @throws \GmagickException
     */
    static public function getThumb($imagefile, $size = 100, $placeholder = false)
    {
        if (!is_file($imagefile) && $placeholder) {
            $thumbname = TMP . 'thumbs' . DS . $imagefile . '_' . $size . '_notfound.png';
            if (!is_dir(dirname($thumbname))) {
                mkdir(dirname($thumbname), 0755, true);
            }

            if (extension_loaded('imagick')) {
                $image = new IMagick();
            }
            else {
                $image = new GMagick();
            }
            $image->newimage($size, $size, 'red'); //new GMagickPixel('red')
            $image->setImageFormat("png24");
            $image->writeImage($thumbname);
            $image->clear();
            $image->destroy();
        }
        else {
            if (is_file($imagefile)) {

                $thumbname = TMP . 'thumbs' . $imagefile . '_' . filemtime($imagefile) . '_' . $size . '.png';
                if (!is_dir(dirname($thumbname))) {
                    mkdir(dirname($thumbname), 0755, true);
                }

                //throw new NotFoundException('Unsupported imageformat');
                if (!is_file($thumbname)) {
                    try {

                        $file_parts = pathinfo($imagefile);
                        if ($file_parts['extension'] == 'svg') {
                            exec("rsvg-convert -a -b white -w " . $size . " -h " . $size . " " . $imagefile . ' > ' . $thumbname);
                        }
                        else {
                            if (extension_loaded('imagick')) {
                                $image = new IMagick($imagefile);
                            }
                            else {
                                $image = new GMagick($imagefile);
                            }

                            $image->thumbnailimage($size, $size, true);
                            $image->setImageFormat("png24");
                            $image->writeImage($thumbname);
                            $image->clear();
                            $image->destroy();
                        }

                    } catch (Exception $e) {
                        $thumbname = $imagefile;
                    }
                }
            }
            else {
                $thumbname = '';
            }
        }

        return $thumbname;
    }

    /**
     * Clear thumbs
     *
     * TODO: replace deprecated Folder-class
     *
     * @return mixed
     */
    static public function clearThumbs()
    {
        $folder = new Folder(TMP . 'thumbs' . DS);
        return ($folder->delete());
    }

    /**
     * Check whether a shell command is available on the current platform
     *
     * Example: Files::commandAvailable('exiftool')
     *
     * @param string $command
     * @return bool
     */
    static public function commandAvailable($command): bool
    {
        $windows = strpos(PHP_OS, 'WIN') === 0;
        $test = $windows ? 'where' : 'command -v';
        return is_executable(trim(shell_exec("$test $command")));
    }

    /**
     * Add metadata to the xmp section of a file
     *
     * Depends on exiftool on the command line.
     *
     * @param string $filename
     * @param array $metadata
     * @param bool $overwrite Exiftool preserves the original file by adding the suffix "_original".
     *                        Set to true to disable this feature, but make sure to have backups of your files.
     * @return bool
     */
    static public function updateXmp($filename, $metadata, $overwrite = false)
    {
        if (empty($metadata) || !Files::commandAvailable('exiftool')) {
            return false;
        }

        $command = 'exiftool';
        if ($overwrite) {
            $command .= ' -overwrite_original';
        }
        foreach ($metadata as $key => $value) {
            $command .= ' -' . $key . '=' . escapeshellarg($value);
        }
        $command .= ' ' . $filename;
        exec($command, $output, $return);
        return ($return === 0);
    }

    /**
     * Get metadata from the xmp section of a file
     *
     * Depends on exiftool on the command line.
     *
     * @param string $filename
     * @return array
     */
    static public function getXmp($filename)
    {
        $fileExt = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($fileExt, ['jpg', 'jpeg']) || !Files::commandAvailable('exiftool')) {
            return [];
        }

        //$command = 'exiftool -g0 -json -struct "'. $filename .'"';
        $command = 'exiftool -g0 -XMP:all -json -struct "' . $filename . '"';
        exec($command, $output, $return);
        $metadata = implode('', $output);
        $metadata = json_decode($metadata, true);
        $metadata = $metadata[0]['XMP'] ?? [];
        return $metadata;
    }

    /**
     * Load content of file specified by filepath
     *
     * Loads the file content in Chunks of 1024 characters.
     * Use the page parameter to get more chunks.
     *
     * @param string $filepath The file name including the absolute path
     * @param int $page The chunk number, starting with 1
     * @param int $limit The chunk size
     *
     * @return string|boolean The content or false if an error occured
     */
    static public function loadContent($filepath, $page = 1, $limit = 1024)
    {
        try {
            $content = '';
            $handle = fopen($filepath, "r");

            $offset = ($page - 1) * $limit;
            fseek($handle, $offset);
            $i = 0;
            while (!feof($handle) and ($i < 1)) {
                $content .= fread($handle, $limit);
                $i++;
            }
            fclose($handle);

            //$content = '<b>This file is to big to preview.</b>';

        } catch (Exception $e) {
            $content = false;
        }

        return $content;
    }

    /**
     * Replace content in a file
     *
     * Example patterns:
     * ['/folder="[^"]+"/' => 'folder="job_3"']
     *
     * @param string $filename
     * @param array $patterns List of regexes as keys and replacements as values
     */
    static public function replaceContent($filename, $patterns)
    {
        if (!file_exists($filename)) {
            throw new Exception("File not found: " . $filename);
        }

        $content = file_get_contents($filename);
        if ($content === false) {
            throw new Exception("Could not read the file: " . $filename);
        }

        foreach ($patterns as $regex => $replacement) {
            $content = preg_replace($regex, $replacement, $content);
        }

        $result = file_put_contents($filename, $content);
        if ($result === false) {
            throw new Exception("Could not write to the file: " . $filename);
        }
    }
}

