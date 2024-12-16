<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Files\Model\Entity;

use App\Model\Entity\BaseEntity;
use App\Utilities\Converters\Attributes;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use App\Utilities\Files\Files;
use Cake\Routing\Router;


/**
 * File Entity
 *
 * # Database fields
 * @property string $name
 * @property string $description
 * @property string $config
 * @property string $type Virtual property with database field behind
 * @property int $size
 * @property string $root
 * @property string $path
 * @property bool $isfolder
 *
 * # Virtual fields
 * @property bool $isFile
 * @property bool $isFolder
 * @property int $isRootFolder
 * @property bool $isInBasepath
 * @property mixed $rootFolder
 * @property mixed $baseFolder
 * @property string $basedFolder
 * @property string $absoluteFolder
 * @property string $relativeFolder
 * @property string $fullPath
 * @property string $relativePath
 * @property string $captionPath
 * @property array $xmp
 * @property bool $missing
 * @property string $filePermissions
 * @property mixed|string $fileOwner
 * @property string[] $folders
 * @property array[] $files
 * @property string|mixed $downloadurl
 * @property string|mixed $displayurl
 * @property array[] $htmlFields
 *
 */
class FileRecord extends BaseEntity
{

    /**
     * Default limit
     *
     * @var int
     */
    public $limit = 15;

    /**
     * The basepath defines a navigation border:
     * it is removed from _getFiles() and _getFolders() results
     *
     * @var string
     */
    public $basepath = '';

    /**
     * Permissions to access fields
     *
     * Fields that can be mass assigned using newEntity() or patchEntity().
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * Get the Table of the Entity
     *
     * @return \Cake\ORM\Table|mixed
     */
    public function getTable()
    {
        return $this->fetchTable($this->getSource());
    }

    /**
     * Magic property is_file
     *
     * @return bool
     */
    protected function _getIsFile()
    {
        return empty($this['isfolder']);
    }

    /**
     * Whether the entity is a folder
     *
     * @return bool
     */
    protected function _getIsFolder()
    {
        return !empty($this['isfolder']);
    }

    /**
     * Whether the entity is a root folder
     *
     * @return int
     *
     * //todo bitwise and correct?
     */
    protected function _getIsRootFolder()
    {
        return (empty($this->name) & empty($this->path));
    }

    /**
     * Check whether the path is inside the basepath
     *
     * @return bool
     */
    protected function _getIsInBasepath()
    {
        return ($this->basepath === '') || (str_starts_with($this->relativeFolder, $this->basepath));
    }


    /**
     * Get the absolute root folder path
     *
     * @return mixed
     */
    protected function _getRootFolder()
    {
        return $this->getTable()->getRootFolder($this['root']);
    }

    /**
     * Get the base folder path, including the root
     *
     * @return mixed
     */
    protected function _getBaseFolder()
    {
        $path = $this->getTable()->getRootFolder($this['root']);
        if ($this->basepath !== '') {
            $path .= DS . $this->basepath;
        }
        return $path;
    }

    /**
     * Get the folder relative from the base folder,
     * i.e. the base path is removed from the relative path
     *
     * @return string
     */
    protected function _getBasedFolder()
    {
        if ($this->basepath === '') {
            return $this->relativePath;
        }

        $basepath = $this->basepath . '/';
        $relativepath = $this->relativePath . '/';

        if (substr($relativepath, 0, strlen($basepath)) !== $basepath) {
            throw new NotFoundException('Base path not found inside the folder');
        }

        return rtrim(substr($relativepath, strlen($basepath)), '/');
    }

    /**
     * Get virtual field absoluteFolder
     *
     * Contains the absolute path of the entity.
     *
     * @return string
     */
    protected function _getAbsoluteFolder()
    {
        $fullpath = $this->rootFolder;

        if ($this->path !== '') {
            $fullpath .= DS . $this->path;
        }

        if (($this->name !== '') && ($this->isfolder)) {
            $fullpath .= DS . $this->name;
        }

        return rtrim($fullpath, DS);
    }

    /**
     * Get the folder path starting from root, without trailing or leading slashes
     *
     * @return string
     */
    protected function _getRelativeFolder()
    {
        $fullpath = '';

        if ($this->path !== '') {
            $fullpath = $this->path;
        }

        if (($this->name !== '') && ($this->isfolder)) {
            $fullpath .= DS . $this->name;
        }

        $fullpath = trim($fullpath, DS);

        return $fullpath;
    }

    /**
     * Get the path of a file or folder,
     * including root, relative path and
     * file or folder name
     *
     * @return string
     */
    protected function _getFullPath()
    {
        $fullpath = $this->rootFolder;

        if ($this->path !== '') {
            $fullpath .= DS . $this->path;
        }

        if (($this->name !== '')) {
            $fullpath .= DS . $this->name;
        }

        return $fullpath;
    }

    /**
     * Get the path of a file or folder starting from the root
     *
     * In case of files, to get only the folder, use _getRelativeFolder()
     *
     * @return string
     */
    protected function _getRelativePath()
    {
        $fullpath = '';

        if ($this->path !== '') {
            $fullpath .= $this->path;
        }

        if ($this->name !== '') {
            $fullpath .= DS . $this->name;
        }

        return trim($fullpath, DS);
    }

    /**
     * Get the display path
     *
     * @return string
     */
    protected function _getCaptionPath()
    {
        return $this->relativePath;
    }

    /**
     * Get virtual field xmp
     *
     * @return array
     */
    protected function _getXmp()
    {
        return Files::getXmp($this->fullPath);
    }

    /**
     * Get virtual field missing
     *
     * @return bool
     */
    protected function _getMissing()
    {
        if ($this->isfolder) {
            return !is_dir($this->fullPath);
        }
        else {
            return !is_file($this->fullPath);
        }
    }

    /**
     * The file permissions
     *
     * @return string
     */
    protected function _getFilePermissions()
    {
        return Files::filePermissions($this->fullPath);
    }

    /**
     * Get file owner
     *
     * @return mixed|string
     */
    protected function _getFileOwner()
    {
        if (!file_exists($this->fullPath)) {
            return '';
        }

        return posix_getpwuid(fileowner($this->fullPath))['name'] ?? '';
    }

    /**
     * For folder entities, get the subfolders
     *
     * @return string[]
     */
    protected function _getFolders()
    {
        $folders = Files::getFolders($this->rootFolder);
        $folders = array_combine($folders, $folders);
        return $folders;
    }

    /**
     * For folder entities, get the files and folders in the folder.
     *
     * @return array[] An array of file information items
     */
    protected function _getFiles()
    {
        return Files::getBaseContent($this->baseFolder, $this->basedFolder);
    }

    /**
     * Get download url
     *
     * @return string|mixed Download url
     */
    protected function _getDownloadurl()
    {
        return Router::url([
            'plugin' => false,
            'controller' => 'Files',
            'action' => 'download',
            $this->id,
            'database' => false
        ]);
    }

    /**
     * Get display url
     *
     * @return string|mixed Display url
     */
    protected function _getDisplayurl()
    {
        return Router::url([
            'plugin' => false,
            'controller' => 'Files',
            'action' => 'display',
            $this->id,
            '?' => ['format' => 'thumb', 'size' => '600'],
            'database' => false
        ]);
    }

    /**
     * Return fields to be rendered in view/edit table
     *
     * The config field is only rendered for folders
     * and for users with the devel role.
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        $fields = [
            'oldname' => [
                'value' => $this['name'],
                'type' => 'hidden',
                'action' => 'edit'
            ],

            'name' => [
                'caption' => __('Name'),
                'value' => $this['name'],
                'cellClass' => ($this['missing'] ? 'file-missing' : ''),
                'action' => ['view', 'edit']
            ],

            'description' => [
                'caption' => __('Description'),
                'value' => $this['description'],
                'type' => 'textarea',
                'layout' => 'stacked',
                'action' => ['view', 'edit']
            ],

            'config' => [
                'caption' => __('Config'),
                'id' => 'textarea_config',
                'rows' => 15,
                'format' => 'json',
                'type' => 'jsoneditor',
                'layout' => 'stacked'
            ],

// TODO: allow writing metadata to the file
//            'metadata' => [
//                'caption' => __('Metadata'),
//                'id' => 'textarea_metadata',
//                'rows' => 15,
//                'format' => 'json',
//                'type' => 'jsoneditor',
//                'layout' => 'stacked'
//            ],

            'published' => [
                'caption' => __('Published'),
                'checked' => $this->published,
                'type' => 'checkbox',
                'action' => ['view', 'edit']
            ],

            'downloadurl' => [
                'caption' => __('Download'),
                'format' => 'url',
                'action' => 'view'
            ],

            'displayurl' => [
                'caption' => __('Display'),
                'format' => 'url',
                'action' => 'view'
            ],

            'path' => [
                'caption' => __('Folder'),
                'format' => 'url',
                'targetUrl' => ['action' => 'index', '?' => ['root' => $this['root'], 'path' => $this['path']]],
                'action' => 'view'
            ],

            'type' => [
                'caption' => __('Type'),
                'action' => 'view'
            ],

            'size' => [
                'caption' => __('Size'),
                'format' => 'filesize',
                'action' => 'view'
            ],

            'created' => [
                'caption' => __('Uploaded'),
                'format' => 'time',
                'action' => 'view'
            ],

            'modified' => [
                'caption' => __('Modified'),
                'format' => 'time',
                'action' => 'view'
            ],

            'data' => [
                'caption' => __('Replace the file'),
                'action' => 'edit',
                'type' => 'file'
            ]
        ];

        // Conditional fields
        if ($this['isfolder']) {
            unset($fields['published']);
            unset($fields['data']);
            if (
                !$this->hasDatabaseField('config') ||
                (($this->currentUserRole ?? 'guest') !== 'devel')
            ) {
                unset($fields['config']);
            }
        }
        else {
            unset($fields['config']);
        }

        if (!in_array(strtolower($this->type ?? ''), Files::$thumbtypes)) {
            unset($fields['displayurl']);
        }

        return $fields;
    }

    /**
     * Pull folder content from origin
     *
     * The origin is a URL to a zip file, configured in the folder's origin config key.
     * Zip entries are configured in the folder's unzip config key.
     * In case of errors, you'll find them in the errors property.
     *
     * @return boolean Whether the pull operation was successful
     */
    public function pull()
    {

        // Get parameters
        $origin = $this->config['origin'] ?? '';
        $zipEntries = Attributes::commaListToStringArray($this->config['unzip'] ?? []);

        if (empty($origin)) {
            $this->setError('pull', __('No origin URL provided.'));
            return false;
        }

        if (empty($zipEntries)) {
            $this->setError('pull', __('The zip entries are invalid.'));
            return false;
        }

        // Download to temporary location
        $tempfile = Files::getTempFilename('origin');
        if (!Files::fetchURl($origin, $tempfile)) {
            $this->setError('pull', __('The file could not be fetched.'));
            return false;
        }

        // Unzip in temporary location
        $tempfolder = $tempfile . '.unzipped';
        Files::createFolder($tempfolder);
        if (!Files::unzipFile($tempfile, $tempfolder)) {
            $this->setError('pull', __('Origin file could not be unzipped.'));
            return false;
        }

        // Clear the target folder
        $targetFolder = $this->fullPath;
        if (!Files::clearFolder($targetFolder)) {
            $this->setError('pull', __('Target folder is not ready.'));
            return false;
        }

        // Move zip entries to target folder
        foreach ($zipEntries as $zipEntry) {
            if (strpos($zipEntry, '..') !== false) {
                $this->setError('pull', __('The zip entry is invalid.'));
                return false;
            }
            $sourceItem = $tempfolder . DS . $zipEntry;
            if (!Files::moveContent($sourceItem, $targetFolder, true)) {
                $this->setError('pull', __('Origin content could not be moved to target.'));
                return false;
            }
        }

        return true;
    }

    /**
     * Load file content
     *
     * @param $rootFolder
     * @return void
     */
    public function loadContent($rootFolder, $page = 1)
    {
        $filepath = $rootFolder . DS . $this->path . DS . $this->name;
        if (file_exists($filepath)) {
            $this->content = Files::loadContent($filepath, $page);
        }

    }

    /**
     * Rename file or folder
     *
     * @param $rootFolder
     * @return bool
     */
    public function rename($rootFolder)
    {
        if (empty($this->isfolder)) {
            return Files::renameFile(
                $rootFolder,
                $this->getOriginal('name'),
                $this->name
            );
        }
        else {
            return Files::renameFolder(
                $rootFolder,
                $this->name
            );
        }
    }

    /**
     * Delete method
     *
     * Delete current file.
     * The object is deleted from the file system and the database.
     *
     * @return bool
     */
    public function delete()
    {

        $success = Files::delete($this->fullPath);

        // Option 1: delete folder
        if ($this->isfolder) {
            $success = $success && $this->getTable()->deleteFolder($this->root, $this->relativeFolder);
        }
        else {
            // Option 2: Delete file
            $success = $success && $this->getTable()->delete($this);
        }

        return $success;
    }

    /**
     * Get the items that use the file.
     *
     * // TODO: implement
     *
     * @return array
     */
    public function getItems()
    {
        return [];
    }

    /**
     * Get the properties that use the file.
     *
     * // TODO: implement
     *
     * @return array
     */
    public function getProperties()
    {
        return [];
    }
}
