<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace Files\Model\Table;


use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;

/**
 * Trait FilesTableTrait
 *
 */
trait FilesTableTrait
{

    /**
     * Extract search parameters from request parameters
     *
     * @param array $requestParameters Request parameters
     * @param string|null $requestPath The property type
     * @param string $requestAction
     * @return array
     */
    public function parseRequestParameters(array $requestParameters = [], $requestPath = '', $requestAction = ''): array
    {
        $params = Attributes::parseQueryParams($requestParameters, $this->parameters);
        $params['folder_id'] = $params['folder_id'] ?? $requestPath;
        $params['action'] = $requestAction;
        return Arrays::array_remove_empty($params);
    }

    protected function parsePathComponents($options)
    {
        // Get current folder
        $path = str_replace('\\', '/', $options['path'] ?? '');
        $path = (substr(trim($path), -1) == '/') ? substr(trim($path), 0, -1) : $path;
        if (strpos($path, '..') !== false) {
            throw new NotFoundException('The requested path contains `..` and will not be read.');
        }

        // Get current filename
        $filename = $options['filename'] ?? '';
        if (strpos($filename, '..') !== false) {
            throw new NotFoundException('The requested path contains `..` and will not be read.');
        }

        // Combine folder and filename parameters
        if (basename($filename) != $filename) {
            $path = $path . DS . dirname($filename);
            $filename = basename($filename);
        }

        $pathRoot = $options['root'] ?? $this->defaultMount;
        $pathCurrent = basename($path);
        $pathParent = ($pathCurrent === $path) ? '' : dirname($path);

        return [$pathRoot, $pathParent, $pathCurrent, $filename];
    }

    /**
     * Get pagination parameters
     *
     * @param array $params Parsed request parameters
     * @param array $columns
     * @return array
     */
    public function getPaginationParams(array $params = [], array $columns = [])
    {
        $pagination = parent::getPaginationParams($params, $columns);

        return [
                'order' => ['name' => 'asc'],
                'limit' => 50
            ] + $pagination;
    }

    /**
     * Get folder entity
     *
     * @param array $options
     * @return FileRecord
     */
    public function getFolderEntity($options)
    {
        if (empty($options['folder_id'])) {
            [$pathRoot, $pathParent, $pathCurrent, $filename] = $this->parsePathComponents($options);
            $entity = $this->getFile($pathRoot, $pathParent, $pathCurrent);
        }
        else {
            $entity = $this->get($options['folder_id'], ['contain' => []]);
        }

        $entity->basepath = $options['basepath'] ?? '';
        return $entity;
    }

    /**
     * Find properties by query parameters
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findHasParams(Query $query, array $options): Query
    {
        $default = [];
        $params = $options + $default;

        // Get folder
        $folder = $this->getFolderEntity($options);

        // Get folder content
        $query = $query
            ->find('files', ['root' => $folder->root, 'path' => $folder->relative_folder])
            ->where(['name <>' => '']);

        if (!empty($params['id'])) {
            $query = $query->where(['Files.id' => $params['id']]);
        }

        return $query;
    }


    /**
     * Get absolute root folder from mount name, without right slash
     *
     * @param string $mount The mount name, e.g. 'root'
     * @return string
     */
    public function getRootFolder($mount)
    {
        $mounts = $this->mounts ?? [];
        if (empty($mounts[$mount])) {
            throw new BadRequestException('No valid root folder.');
        }

        return rtrim($mounts[$mount], DS);
    }

    /**
     * Create records for the folder and all subfolders
     *
     * @param $rootfolder
     * @param $root
     * @param $folder
     *
     * @return true
     * @return bool|\Cake\Datasource\EntityInterface[]|\Cake\Datasource\ResultSetInterface|iterable
     * @throws \Exception
     */
    public function createFolder($rootfolder, $root, $folder)
    {
        if (empty($folder)) {
            return true;
        }

        $folder = explode('/', $folder);
        $path_segments = [];
        $missing = [];
        while (!empty($folder)) {
            $name = array_shift($folder);
            $path = implode('/', $path_segments);

            if (!$this->find('all')
                ->where(['root' => $root, 'path' => $path, 'name' => $name, 'isfolder' => 1])
                ->count()) {
                $missing[] = [
                    'name' => $name,
                    'root' => $root,
                    'path' => $path,
                    'isfolder' => 1
                ];
            }

            $path_segments[] = $name;
        }

        if (!empty($missing)) {
            return $this->saveMany($this->newEntities($missing));
        }
        else {
            return true;
        }

    }

    /**
     * Delete folder method
     *
     * @param $root
     * @param $folder
     *
     * @return int
     */
    public function deleteFolder($root, $folder)
    {
        $out = 0;

        $parentfolder = dirname($folder);
        $parentfolder = $parentfolder == '.' ? '' : $parentfolder;
        $childfolder = basename($folder);

        //delete folder records
        $out += $this->deleteAll(['root' => $root, 'path' => $parentfolder, 'name' => $childfolder]);

        //delete child records
        $out += $this->deleteAll(['root' => $root, 'path' => $folder]);
        $out += $this->deleteAll(['root' => $root, 'path LIKE' => $folder . '/%']);

        return $out;
    }


    /**
     * Find files method
     *
     * ### Options
     * - root: The mount name
     * - path: The folder path
     *
     * @param Query $query
     * @param array $options
     *
     * @return mixed
     */
    public function findFiles(Query $query, array $options)
    {
        // Get database content
        $query = $query
            ->find('all')
            ->where(['root' => ($options['root'] ?? ''), 'path' => ($options['path'] ?? '')])
            ->order(['isfolder' => 'DESC']);

        return $query;
    }

    /**
     * Get missing files and folders
     *
     * Compare files and folders in the database (db) and in the filesystem (os).
     *
     * @param $files
     * @param $rows
     *
     * @return array[]
     */
    protected function _getDifferences($files, $rows)
    {

        // Extract folders and files
        $db_content = [
            'folders' => array_filter(array_map(fn($x) => !empty($x['isfolder']) ? $x['name'] : null, $rows)),
            'files' => array_filter(array_map(fn($x) => empty($x['isfolder']) ? $x['name'] : null, $rows))
        ];

        $files = [
            'folders' => array_filter(array_map(fn($x) => !empty($x['isfolder']) ? $x['name'] : null, $files)),
            'files' => array_filter(array_map(fn($x) => empty($x['isfolder']) ? $x['name'] : null, $files))
        ];

        // Compare
        $missing = [
            'db' => [
                'folders' => array_diff($files['folders'], $db_content['folders']),
                'files' => array_diff($files['files'], $db_content['files'])
            ],
            'os' => [
                'folders' => array_diff($db_content['folders'], $files['folders']),
                'files' => array_diff($db_content['files'], $files['files'])
            ]
        ];

        return $missing;
    }

    /**
     * Add or remove file and folder records from database
     *
     * @param string $root Name of the mount
     * @param string $folder
     * @param boolean $recent Only recently modified files
     *
     * @return array[]
     * @throws \Exception
     */
    public function syncDatabase($root, $folder, $recent = true)
    {
        $rootFolder = $this->getRootFolder($root);
        $fullFolder = rtrim($rootFolder . DS . $folder, DS);

        // Check for changes
        if ($recent) {
            $lastDbFile = $this
                ->find('files', ['root' => $root, 'path' => $folder])
                ->where(['name <>' => ''])
                ->order(['created' => 'DESC'])
                ->first();

            $lastFsFile = Files::getLatestFile($fullFolder);
            $lastFsFileCreated = Files::getFileInfo($fullFolder, '', $lastFsFile);
            $lastFsFolderModified = Files::getFolderInfo($rootFolder, '', $folder);

            $nullTime = new FrozenTime('0000-00-00 00:00:00');
            $fileNewer = ($lastFsFileCreated['modified'] ?? $nullTime) > ($lastDbFile['created'] ?? $nullTime);
            $folderNewer = ($lastFsFolderModified['modified'] ?? $nullTime) > ($lastDbFile['created'] ?? $nullTime);
            if (!$fileNewer && !$folderNewer) {
                return [];
            }
        }

        // Get database content
        // TODO: convert isfolder database field to boolean
        $files = $this
            ->find('files', ['root' => $root, 'path' => $folder]);

        // Get folder content
        $folder_content = Files::getFolderContent($fullFolder);

        // Compare
        $missing = $this->_getDifferences($folder_content, $files->toArray());

        //Create missing in database
        $newfolders = array_map(function ($x) use ($folder, $root) {
            return [
                'name' => $x,
                'root' => $root,
                'path' => $folder,
                'isfolder' => 1
            ];
        }, $missing['db']['folders']);

        $newfiles = array_map(function ($x) use ($folder, $fullFolder, $root) {
            return [
                'name' => $x,
                'root' => $root,
                'path' => $folder,
                'isfolder' => 0,
                'type' => pathinfo($x, PATHINFO_EXTENSION),
                'size' => filesize($fullFolder . DS . $x)
            ];
        }, $missing['db']['files']);

        $new = array_merge($newfolders, $newfiles);

        if (!empty($new)) {
            $this->saveMany($this->newEntities($new));
        }

        // Delete missing in database
        if (!empty($missing['os']['folders'])) {
            $this->deleteAll([
                'root' => $root,
                'path' => $folder,
                'isfolder' => 1,
                'name IN' => $missing['os']['folders']
            ]);
        }
        if (!empty($missing['os']['files'])) {
            $this->deleteAll([
                'root' => $root,
                'path' => $folder,
                'isfolder' => 0,
                'name IN' => $missing['os']['files']
            ]);
        }

        return $missing;
    }

    /**
     * Get file or folder record from the database
     *
     * TODO: refactor as finder
     *
     * @param string $root
     * @param string $path
     * @param string $filename
     *
     * @return \Cake\Datasource\EntityInterface|array|mixed
     */
    public function getFile($root, $path, $filename)
    {
        $file = $this
            ->find('all')
            ->where(['root' => $root, 'path' => $path, 'name' => $filename])
            ->first();

        if (empty($file)) {

            // Try to create root folder
            if (!$path && !$filename) {
                $file = $this->newEntity([
                    'root' => $root,
                    'path' => $path,
                    'name' => $filename,
                    'isfolder' => 1
                ]);
                if (!$this->save($file)) {
                    throw new BadRequestException('Could not create root record.');
                }

            }

            // Sync database and try again
            $this->syncDatabase($root, $path);
            $file = $this
                ->find('all')
                ->where(['root' => $root, 'path' => $path, 'name' => $filename])
                ->first();
        }

        if (empty($file)) {
            throw new NotFoundException('File record not found.');
        }

        return $file;
    }

}
