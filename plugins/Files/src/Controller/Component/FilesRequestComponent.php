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

namespace Files\Controller\Component;

use App\Controller\AppController;
use App\Model\Table\FilesTable;
use App\Utilities\Converters\Attributes;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Files\Model\Entity\FileRecord;
use Cake\Controller\Component;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use App\Utilities\Files\Files;

/**
 * FilesRequest component
 *
 * TODO: Implement locks
 *
 */
class FilesRequestComponent extends Component
{
    /**
     * Default configuration.
     *
     * Options:
     * - 'mounts' Array of mounts to be used as roots. Keys define root name, values the path in the filesystem.
     *            This serves as a mapper of root names exposed in the URLs and internal paths on the server.
     * - 'createfolder' If true and the folder is not present, redirect to newfolder action
     *
     * @var array
     */
    protected $_defaultConfig = [
        'createfolder' => false,
    ];

    // Other components used
    public $components = ['Answer'];

    /**
     * @var AppController
     */
    protected AppController $controller;

    /**
     * @var ServerRequest
     */
    protected $request;

    /**
     * @var FilesTable
     */
    protected $model;

    /**
     * Startup method
     *
     * Parses the request parameters and sets variables in the controller and the view
     *
     * ### Request parameters
     * - root Mount name, mapped to root folder
     * - path Folder path relative to the root
     * - filename File name
     * - find Instead of providing a filename, set find=latest to find the latest file in the folder
     *
     * ### Controller properties
     * - rootFolder           Absolute root folder path
     * - currentFolder        Absolute folder path
     *
     * - currentFolderSegment Relative folder path, relative to root (or '')
     * - parentFolderSegment  Relative parent folder path (used to add ..) (or '')
     * - currentFolderName    Name of the Folder (or '' in case of files)
     *
     * - currentFilePath      Absolute file name (or '' in case of folders)
     * - currentFilename      Filename without path (or '' in case of folders)
     * - currentFileExtension The extension of the file (or '' in case of folders)
     *
     * - root                 Mount name used as root (mapped to folder paths in the component config).
     * - mounts Array of available mount names that can be used as root (mapped to folder paths in the component config).
     *
     * ### View variables
     * - mounts Array of available mount names that can be used as root (mapped to folder paths in the component config).
     * - root Name of the mount used as root (mapped to folder paths in the component config).
     * - path Name of the path relative to root.
     * - parent_path Parent path, used to provide a navigation link
     *
     * @param \Cake\Event\EventInterface $event Event instance
     *
     * @return \Cake\Http\Response|null|void
     */
    public function startup(EventInterface $event)
    {
        // Init properties
        $this->controller = $this->getController();
        $this->request = $this->controller->getRequest();
        $this->model = $this->controller->Files;

        // Behavior
        $behavior = $this->model->getBehavior('FileSystem');
        $behavior->setConfig('mounts', $this->getConfig('mounts'));

        // TODO: remove access by folder and file names -> use IDs instead / redirect to IDs
        // TODO: path, file, and folder should be initialized as empty strings (?), see problems.
        // TODO: Init controller properties in a trait or interface
        // Absolute paths
        $this->controller->rootFolder = false;
        $this->controller->currentFolder = false;
        $this->controller->currentFilepath = false;

        // Relative paths
        $this->controller->currentFilename = false;
        $this->controller->currentFolderSegment = false;
        $this->controller->parentFolderSegment = false;
        $this->controller->currentFolderName = false;
        $this->controller->currentFileExtension = false;

        // Set root dir
        $this->controller->mounts = $this->getConfig('mounts');
        $this->controller->root = $this->request->getQuery('root', $this->getConfig('root', 'root'));

        $this->updatePropertiesRoot($this->controller->root);

        // Get current dir
        $path = str_replace('\\', '/', $this->request->getQuery('path', ''));
        $path = (substr(trim($path), -1) == '/') ? substr(trim($path), 0, -1) : $path;

        if (strpos($path, '..') !== false) {
            throw new NotFoundException('The requested path contains `..` and will not be read.');
        }

        // Get current filename
        $filename = $this->request->getQuery('filename', '');
        if (strpos($filename, '..') !== false) {
            throw new NotFoundException('The requested path contains `..` and will not be read.');
        }


        if (basename($filename) != $filename) {
            $dirName = dirname($filename);
            $path = $path . DS . $dirName;

            $filename = basename($filename);
        }

        // Set folder
        $this->updatePropertiesFolder($path);

        //Redirect to create folder method if folder not present
        $autocreatedir = $this->getConfig('createfolder', false);

        $action = $this->request->getParam('action');
        if (
            $autocreatedir &&
            in_array($action, ['index', 'select']) &&
            !is_dir($this->controller->currentFolder) &&
            !empty($this->controller->currentFolderSegment)
        ) {
            $this->controller->Flash->error(__('The folder does not exist yet. Create the folder before adding files.'));
            $this->redirectCreateFolder();
        }

        if (!is_dir($this->controller->currentFolder)) {
            throw new NotFoundException('The requested path does not exist.');
        }

        // Find latest file
        $find = $this->request->getQuery('find');
        if (empty($filename) && (!empty($find) && ($find == 'latest'))) {
            $filename = Files::getLatestFile(
                $this->controller->rootFolder . DS .
                $this->controller->currentFolderSegment
            );
            if ($filename === '') {
                throw new NotFoundException('No file found.');
            }
        }

        $this->updatePropertiesFile($filename);

        $this->setViewVars();
    }

    /**
     * Render default
     *
     * @return void
     */
    public function renderDefault()
    {
//        $this->controller->setRequest($this->request);
        if ($this->getConfig('render', true)) {
            $this->controller->render('Files.Files/' . $this->request->getParam('action'));
        }
    }

    /**
     * Get file by ID or query parameters
     *
     * Updates controller properties
     *
     * @param int $id Id of the file record or null
     * @param string|null $expect 'file' or 'folder' or null if nothing is expected
     * @param boolean $redirect If true, redirect to the view action if the file is not found
     *
     * @return FileRecord
     */
    public function getFileEntity($id = null, $expect = null, $redirect = false, $checkPermissions = false)
    {
        // Get file record
        if ($id === null) {
            $file = $this->getFileFromQuery();
        }
        else {
            $file = $this->model->get($id, ['contain' => []]);
        }

        $basepath = $this->request->getQuery('basepath', '');
        $file->basepath = $basepath;

        // Checks
        if (empty($file)) {
            throw new NotFoundException('File or folder not found');
        }


        if ($checkPermissions && !$file->isPermitted($this->controller->getPermissionMask())) {
            throw new UnauthorizedException('You are not authorized to access the file or folder');
        }

        if (!in_array($file['root'], array_keys($this->controller->mounts))) {
            throw new UnauthorizedException('Mount access not authorized');
        }

        if (!$file->isInBasepath) {
            throw new UnauthorizedException('Folder is outside the base path');
        }

        if (($expect === 'folder') && empty($file['isfolder'])) {
            throw new NotFoundException('Folder not found');
        }

        if (($expect === 'file') && !empty($file['isfolder'])) {
            throw new NotFoundException('File not found');
        }

        // Redirect
        if (($id === null) && $redirect) {
            $redirectAction = $this->request->getParam('action', 'view');

            // Carry on the close paramter - used in JS popups to determine whether to close window
            $flowParams = ['close' => $this->request->getQuery('close', null)];
            $flowParams = array_filter($flowParams, fn($x) => !is_null($x));

            $flowParams['basepath'] = $basepath;
            $this->controller->Answer->redirect(
                [
                    'action' => $redirectAction,
                    $file->id,
                    '?' => $flowParams
                ]
            );
        }

        // Update properties
        $this->updateProperties($file);

        return $file;
    }

    /**
     * Get a file record by query parameters
     *
     * Looks up the file identified by the query parameters in the database
     *
     * @return array|\Cake\Datasource\EntityInterface|mixed
     */
    public function getFileFromQuery()
    {
        //Folder
        if ($this->controller->currentFilename == '') {
            return $this->model->getFile(
                $this->controller->root,
                $this->controller->parentFolderSegment,
                $this->controller->currentFolderName
            );
        }

        //File
        else {
            return $this->model->getFile(
                $this->controller->root,
                $this->controller->currentFolderSegment,
                $this->controller->currentFilename
            );
        }
    }

    /**
     * Update properties root folder
     *
     * @param $mount
     *
     * @return void
     */
    public function updatePropertiesRoot($mount)
    {
        $this->controller->root = $mount;

        if (empty($this->controller->mounts[$this->controller->root])) {
            throw new BadRequestException('No valid root folder.');
        }

        $this->controller->rootFolder = rtrim($this->controller->mounts[$mount], DS);
    }

    /**
     * Update current properties folder
     *
     * @param $path
     *
     * @return void
     */
    public function updatePropertiesFolder($path)
    {
        $this->controller->currentFolder = empty($path) ? $this->controller->rootFolder : $this->controller->rootFolder . DS . $path;
        $this->controller->currentFolderSegment = trim($path, '/');
        $this->controller->parentFolderSegment = dirname($path);
        $this->controller->currentFolderName = basename($path);
        if ($this->controller->parentFolderSegment == '.') {
            $this->controller->parentFolderSegment = '';
        }
    }

    /**
     * Update properties file
     *
     * @param $filename
     *
     * @return void
     */
    public function updatePropertiesFile($filename)
    {
        if (!empty($filename)) {
            $this->controller->currentFilepath = $this->controller->currentFolder . DS . $filename;
            $this->controller->currentFilename = $filename;
            $this->controller->currentFileExtension = pathinfo($filename, PATHINFO_EXTENSION);
        }
        else {
            $this->controller->currentFilepath = '';
            $this->controller->currentFilename = '';
            $this->controller->currentFileExtension = '';
        }
    }

    /**
     * Update the controller properties based on a file record from the database
     *
     * @param $file
     *
     * @return void
     */
    public function updateProperties($file)
    {
        if (!empty($file)) {
            // Folders
            if (!empty($file['isfolder'])) {
                $root = $file->root;
                $path = empty($file->path) ? $file->name : $file->path . DS . $file->name;
                $filename = '';
            }

            // Files
            else {
                $root = $file->root;
                $path = $file->path;
                $filename = $file->name;
            }

            $this->updatePropertiesRoot($root);
            $this->updatePropertiesFolder($path);
            $this->updatePropertiesFile($filename);

            $this->setViewVars();
        }
    }

    /**
     * Set view variables
     *
     * @return void
     */
    public function setViewVars()
    {
        $this->controller->set('mounts', array_keys($this->controller->mounts));
        $this->controller->set('root', $this->controller->root);
        $this->controller->set('path', $this->controller->currentFolderSegment);
        $this->controller->set('parent_path', $this->controller->parentFolderSegment);
        $this->controller->set('thumbtypes', Files::$thumbtypes);
    }


    /**
     * Redirect create folder
     *
     * @return void
     * @throws \Exception redirect exception
     */
    public function redirectCreateFolder()
    {
        $parent_dir = dirname($this->controller->currentFolderSegment);
        $child_dir = basename($this->controller->currentFolderSegment);
        $parent_dir = ($parent_dir == '.') ? '' : $parent_dir;

        while (!empty($parent_dir) && !is_dir($this->controller->rootFolder . DS . $parent_dir)) {
            $child_dir = basename($parent_dir) . DS . $child_dir;
            $parent_dir = dirname($parent_dir);
            $parent_dir = ($parent_dir == '.') ? '' : $parent_dir;
        }

        $basepath = $this->request->getQuery('basepath', '');

        // Redirect
        // todo: define redirect url in plugin/component configuration
        $successAction = $this->request->getParam('action');
        $this->controller->Answer->redirect(
            [
                'plugin' => $this->request->getParam('plugin'),
                'action' => 'newfolder',
                'database' => $this->request->getParam('database'),
                '?' => [
                    'root' => $this->controller->root,
                    'path' => $parent_dir,
                    'foldername' => $child_dir,
                    'redirect' => $successAction,
                    'basepath' => $basepath
                ]
            ]
        );
    }

    /**
     * Redirect to folder of current file
     *
     * @return mixed
     */
    public function redirectToCurrentFolder()
    {
        return $this->controller->redirect([
            'action' => 'index',
            '?' => [
                'root' => $this->controller->root,
                'path' => $this->controller->currentFolderSegment
            ],
        ]);
    }

    /**
     * Redirect to the action from the redirect query parameter
     *
     * @param string $message
     * @param string $root
     * @param string $path
     * @param string $basepath
     * @return void
     */
    public function redirectOnSuccess($message, $root, $path, $basepath, $filename = '')
    {
        $successAction = ($filename !== '') ? 'view' : 'index';
        $successAction = $this->request->getQuery('redirect', $successAction);
        if (!in_array($successAction, ['index', 'select', 'view'])) {
            $successAction = 'index';
        }

        $fileParams = [
            'root' => $root,
            'path' => $path,
            'basepath' => $basepath,
            'close' => false
        ];

        if ($filename !== '') {
            $fileParams['filename'] = $filename;
        }

        return $this->controller->Answer->success(
            $message,
            [
                'action' => $successAction,
                '?' => $fileParams
            ]
        );
    }

    /**
     * Index logic
     *
     * Compare database record with filesystem.
     * Set viewvar files.
     *
     * @param integer $id files record id or null
     *
     * @return void
     * @throws \Exception
     */
    public function index($id = null)
    {

        // Get base folder
        $folder = $this->getFileEntity($id, 'folder', false);

        // Update database if neccessary, but only on first page requests
        if ($this->request->getQuery('page', '1') === '1') {
            $this->model->syncDatabase($folder->root, $folder->relativeFolder);
        }

        // Get files and folders
        $files = $this->model
            ->find('files', ['root' => $folder->root, 'path' => $folder->relativeFolder])
            ->where(['name <>' => '']);

        $id = $this->request->getQuery('id');
        if ($id) {
            $files = $files->where(['id' => $id]);
        }

        $this->controller->paginate = [
            'order' => ['name' => 'asc'],
            'limit' => 50
        ];

        $files = $this->controller->paginate($files);

//        // Count missing files and folders
//        $missing = array_sum(array_map(function ($x) {
//            return $x['missing'];
//        }, $files->toArray()));
//        if ($missing)
//            $this->controller->Flash->error(__('{0} of the files and folders listed in the database are missing.', $missing));

        $this->controller->set(compact('files', 'folder'));
        $this->renderDefault();
    }


    /**
     * Show file details.
     *
     * @param string|null $id file id
     *
     * @return \Cake\Http\Response|void File for download
     * @throws RecordNotFoundException If record not found
     * @throws \Cake\Http\Exception\NotFoundException if no file record or file not found
     */
    public function view($id = null)
    {
        // Get file/folder record
        $entity = $this->getFileEntity($id, null, true);

        if ($entity->isFolder ?? false) {
            $this->controller->Answer->redirect(
                [
                    'action' => 'index',
                    $entity->id,
                    '?' => ['root' => $entity->root, 'path' => $entity->relativeFolder]
                ]
            );
        }

        // Preview data
        if (in_array($entity->type, ['md', 'xsl', 'txt', 'xml', 'html', 'log'])) {
            $page = (int)$this->request->getQuery('page', 1);
            $entity->loadContent($this->controller->rootFolder, $page);
        }

        $this->controller->Answer->addAnswer(compact('entity'));
        $this->renderDefault();
    }


    /**
     * Select method
     *
     * List folders and files in selected path.
     *
     * @param $id
     *
     * @return void
     *
     * @var FileRecord $folder
     */
    public function select($id = null)
    {
        $folder = $this->getFileEntity($id, 'folder', true);
        $this->controller->set(compact('folder'));
        $this->renderDefault();
    }

    /**
     * Deliver file for download.
     *
     * @param string|null $id file id
     * @param $download
     * @param $placeholder
     *
     * @return \Cake\Http\Response|mixed File for download
     * @throws RecordNotFoundException If record not found
     * @throws \Cake\Http\Exception\NotFoundException if no file record or file not found
     */
    public function download($id = null, $download = true, $placeholder = false)
    {
        // Allow published downloads for guests,
        // private downloads for all
        $allowPrivate = $this->controller->userRole != 'guest';
        $file = $this->getFileEntity($id);

        if (!$allowPrivate && !$file->published) {
            $this->Answer->redirectToLogin();
        }

        $this->response = $this->controller->getResponse();

        if ($file->isFile && ($this->request->getQuery('format') == 'thumb')) {
            $size = $this->request->getQuery('size');
            $size = empty($size) ? 100 : min(600, $size);

            $thumbPath = Files::getThumb(
                $file->fullPath,
                $size,
                $placeholder
            );
            if (empty($thumbPath)) {
                throw new NotFoundException(__('File not found'));
            }

            //Caching
            //$this->response = $this->response->withCache(filemtime($this->controller->currentFilepath),'+1 week');
            $this->response = $this->response
                ->withModified(filemtime($thumbPath))
                ->withExpires('+1 week')
                ->withSharable(false, 7 * 24 * 60 * 60);

            if ($this->response->isNotModified($this->request)) {
                return $this->response;
            }

            $this->response = $this->response->withFile($thumbPath, ['download' => false]);

        }
        elseif ($file->isFolder) {
            $downloadPath = Files::zipFolder($file->absoluteFolder);

            if (empty($downloadPath)) {
                throw new NotFoundException('Zip failed.');
            }

            $this->response = $this->response->withFile($downloadPath, ['download' => $download]);

        }

        else {
            if ($file->isFile) {
                //$this->response = $this->response->withCache(filemtime($this->controller->currentFilepath),'+1 week');
                $this->response = $this->response
                    ->withModified(filemtime($file->fullPath))
                    ->withExpires('+1 week')
                    ->withSharable(false, 7 * 24 * 60 * 60);

                if ($this->response->isNotModified($this->request)) {
                    return $this->response;
                }

                $this->response = $this->response->withFile(
                    $file->fullPath,
                    ['download' => $download]);
            }
            else {
                throw new BadRequestException(__('Invalid file type'));
            }
        }

        return $this->response;
    }


    /**
     * Upload files
     *
     * Multiupload files via AJAX.
     *
     * @return false redirect to index method if no AJAX request, upload files otherwise
     *
     * //todo Upload mit $id
     */
    public function upload()
    {
        if (!$this->request->is('ajax')) {
            $this->controller->Answer->redirect(
                [
                    'action' => 'index',
                    '?' => ['root' => $this->controller->root, 'path' => $this->controller->currentFolderSegment]
                ]
            );
        }

        $files = [];

        if ($this->request->is('post')) {
            $uploadpath = $this->controller->currentFolder;

            if (!empty($this->request->getData('FileData'))) {
                $this->request = $this->request->withData('File.data', $this->request->getData('FileData'));
            }

            $files = Files::saveUploadedFiles($uploadpath, $this->request->getData('File.data'));

            if (empty($files['error'])) {
                $this->controller->Flash->success(__('The files have been uploaded.'));

            }
            else {
                $this->controller->Flash->error(__('Some files could not be uploaded. Please check permissions and try again.'));
            }
        }

        $this->controller->set('files', $files);
        $this->controller->viewBuilder()->setOption('serialize', ['files']);

        return false;
    }

    /**
     * Pull a file from origin URL, unzip and replace the folder content
     *
     * The folder entity must have a config field with the following JSON keys:
     *  - origin: URL of a zip file
     *  - unzip: An item within the zip file or an empty string
     *
     * Accepts only POST requests.
     *
     * @param string|null $id folder id
     *
     * @return \Cake\Http\Response|mixed File for download
     * @throws RecordNotFoundException If record not found
     * @throws \Cake\Http\Exception\NotFoundException if no file record or file not found
     */
    public function pull($id = null)
    {
        if ($this->request->is('post')) {
            // TODO: Log start of pull operation

            // Load folder entity
            $folder = $this->getFileEntity($id, 'folder', false, true);

            // Return success
            // TODO: Log end of pull operation (in the success method?)
            if ($folder->pull()) {
                $this->controller->Answer->success(
                    __('Folder updated from origin.'),
                    [
                        'action' => 'index',
                        $folder->id
                    ]
                );
            }
            else {
                $this->controller->Answer->error(
                    __('Pull failed: ') . implode(' ', $folder->getError('pull')),
                    ['action' => 'index', $folder->id]
                );
            }
        }
        else {
            // Return error
            $this->controller->Answer->error(
                __('No valid request.'),
                [
                    'action' => 'index',
                    $id
                ]
            );
        }
    }

    /**
     * Fetch a file
     *
     * @return void
     */
    public function fetch()
    {
        if ($this->request->is('post')) {
            $folder = $this->controller->currentFolder;

            $url = $this->request->getData('File.url');

            // Get filename
            $filename = $this->request->getData('File.name');
            $filename = mb_strtolower($filename);
            $filename = preg_replace('/[^a-zA-Z0-9_.\/-]/', '', $filename);
            $filename = empty($filename) ? basename($url) : $filename;

            if (!$filename) {
                throw new BadRequestException(__('No valid filename, please provide a valid filename'));
            }

            //TODO: use redirectToCurrentFolder method,  use RedirectException in all methods
            if (Files::fetchURl($url, $folder . DS . $filename)) {
                $this->controller->Answer->success(
                    __('The file was fetched.'),
                    [
                        'action' => 'index',
                        '?' => ['root' => $this->controller->root, 'path' => $this->controller->currentFolderSegment]
                    ]
                );

            }
            else {
                $this->controller->Answer->error(
                    __('The file could not be fetched.'),
                    [
                        'action' => 'index',
                        '?' => ['root' => $this->controller->root, 'path' => $this->controller->currentFolderSegment]
                    ]
                );
            }
        }

        $this->renderDefault();
    }

    /**
     * Unzip a file
     *
     * @param string|null $id file id
     *
     * @return \Cake\Http\Response|mixed File for download
     * @throws RecordNotFoundException If record not found
     * @throws \Cake\Http\Exception\NotFoundException if no file record or file not found
     */
    public function unzip($id = null)
    {
        if (!empty($id)) {
            $file = $this->model->get($id);
            $this->updateProperties($file);
        }

        if (empty($this->controller->currentFilepath)) {
            throw new NotFoundException(__('File not found'));
        }

        if ($this->controller->currentFileExtension != 'zip') {
            throw new BadRequestException(__('The file is not a zip archive.'));
        }

        if (Files::unzipFile($this->controller->currentFilepath)) {
            $this->controller->Flash->success(__('File unzipped.'));
        }
        else {
            $this->controller->Flash->error(__('File could not be unzipped.'));
        }

        return $this->redirectOnSuccess(
            false,
            $this->controller->root,
            $this->controller->currentFolderSegment,
            '',
            $this->controller->currentFilename,
        );
    }


    /**
     * Add a folder
     *
     * @return \Cake\Http\Response|bool|void redirects on successful folder creation, renders view otherwise
     * @throws \Exception
     *
     * //todo refactor using $id of parent folder
     */
    public function newfolder()
    {
        //$parentFolder = $this->getFileEntity($id, 'folder');

        $folder = $this->model->newEntity([
            'name' => $this->request->getQuery('foldername'),
            'root' => $this->controller->root,
            'path' => $this->controller->currentFolderSegment,
            'isfolder' => 1
        ]);

        if ($this->request->is('post')) {
            $folder = $this->model->patchEntity($folder, $this->request->getData());
            $newfolder = $folder['name'];
            $newfolder = preg_replace('/[^a-zA-Z0-9_.\/-]/', '', $newfolder);
            $success = !empty($newfolder);
            $message = null;

            if (!$success) {
                $message = __('The folder could not be created, please provide a valid name.');
            }
            else {
                // TODO: implement entity method
                $success = Files::createFolder($this->controller->currentFolder . DS . $newfolder);
            }

            if ($success) {
                $path = empty($this->controller->currentFolderSegment) ? $newfolder : $this->controller->currentFolderSegment . DS . $newfolder;
                $path = str_replace('\\', '/', $path);
                $basepath = $this->request->getQuery('basepath', '');
                $success = $this->model->createFolder($this->controller->rootFolder, $this->controller->root, $path);
            }

            if ($success) {
                $this->redirectOnSuccess(
                    __('The folder has been created.'),
                    $this->controller->root, $path, $basepath
                );
            }
            else {
                $message = $message ?? __('The folder could not be created. Please, try again.');
                $this->controller->Answer->error($message);
            }
        }

        $this->controller->set(compact('folder'));
        $this->renderDefault();
    }

    /**
     * Clean file and folder names
     *
     * @param $id
     *
     * @return \Cake\Http\Response|mixed redirects to index method
     *
     * @throws \Exception
     */
    public function clean($id = null)
    {
        if (!empty($this->controller->currentFolderSegment)) {
            $errors = Files::cleanFolder($this->controller->currentFolder);
            if (empty($errors)) {
                $this->controller->Flash->success(__('Cleaned file and folder names.'));
            }
            else {
                $this->controller->Flash->error(__('Cleaned file and folder names. {} errors occured.', $errors));
            }
        }

        $this->model->syncDatabase($this->controller->root, $this->controller->currentFolderSegment);

        return $this->controller->redirect([
            'action' => 'index',
            '?' => [
                'root' => $this->controller->root,
                'path' => $this->controller->currentFolderSegment
            ]
        ]);
    }

    /**
     * Recursively sync folder
     *
     * @param $id
     *
     * @return \Cake\Http\Response|mixed redirects to index method
     * @throws \Exception
     */
    public function sync($id = null)
    {

        $folders = [$this->controller->currentFolderSegment];
        $count = 1;

        //Find files and update database if neccessary
        while (!empty($folders)) {
            $folder = array_pop($folders);

            $this->model->syncDatabase($this->controller->root, $folder);

            $nextfolders = $this->model->find('files', ['root' => $this->controller->root, 'path' => $folder])
                ->where(['isfolder' => 1])
                ->map(function ($row) {
                    return $row->relativeFolder;
                })
                ->toArray();
            $count += count($nextfolders);

            $folders = array_merge($folders, $nextfolders);
        }

        $this->controller->Flash->error(__('{0} folders synchronized.', $count));

        return $this->controller->redirect([
            'action' => 'index',
            '?' => [
                'root' => $this->controller->root,
                'path' => $this->controller->currentFolderSegment
            ]
        ]);
    }

    /**
     * Rename or replace a file or folder.
     *
     * @param $id
     * @return void
     */
    public function edit($id = null)
    {
        $entity = $this->getFileEntity($id, '', true);

        if (empty($entity->name)) {
            throw new BadRequestException('File or folder name is missing');
        }

        // Edit
        if ($this->request->is(['patch', 'post', 'put'])) {
            /** @var FileRecord $entity */
            $entity = $this->model->patchEntity($entity, $this->request->getData());

            $proceed = true;

            //Rename
            if ($entity->isDirty('name')) {
                $entity->name = Files::cleanFilename($entity->name);
                $proceed = $entity->rename($this->controller->currentFolder);

                if (!$proceed) {
                    $this->controller->Answer->error(__('The file or folder could not be renamed.'));
                }
            }

            // Upload
            if ($proceed & Files::isUploadedFile($this->request->getData('data'))) {

                $this->request = $this->request->withData('name', $entity->name);
                $this->request = $this->request->withData('oldname', $entity->oldname);
                $data = $this->request->getData('data');

                $uploadpath = $this->controller->currentFolder;
                $files = Files::saveUploadedFile($uploadpath, $data, true, $entity->name);
                $proceed = empty($files['error']);

                if (!$proceed) {
                    $this->controller->Answer->error(__('The file could not be uploaded.'));
                }
            }

            //Save
            if ($proceed) {
                $proceed = $this->model->save($entity);
                if (!$proceed) {
                    $this->controller->Answer->error(__('The file record could not be saved.'));
                }
            }

            // Feedback
            if ($proceed) {
                $action = $entity->isFolder ? 'index' : 'view';
                $action = $this->request->getQuery('redirect', $action);
                $this->controller->Answer->success(
                    __('The file or folder has been saved.'),
                    [
                        'action' => $action,
                        $entity->id,
                        '?' => ['root' => $entity->root, 'path' => $entity->relativeFolder]
                    ]
                );
            }
        }

        $this->controller->set(compact('entity'));
        $this->renderDefault();
    }

    /**
     * Delete a file
     *
     * @param $id file id
     *
     * @return Response|void redirects to index when deleting was successful
     * @throws \Cake\Http\Exception\NotFoundException if file or folder not given or path contains `..`
     * @throws RecordNotFoundException If record not found
     */
    public function delete($id = null)
    {
        $entity = $this->getFileEntity($id, null, true);

        if ($entity->isRootFolder) {
            throw new BadRequestException('You must not delete the root.');
        }

        if ($this->request->is(['delete'])) {
            $success = $entity->delete();

            if ($success) {
                $this->controller->Answer->success(
                    __('The file or folder has been deleted.')
                );

            }
            else {
                $this->controller->Answer->error(
                    __('The file or folder could not be deleted. Please, try again.')
                );
            }
        }

        $this->controller->Answer->addAnswer(compact('entity'));
        $this->renderDefault();
    }

    /**
     * Move folder contents method
     *
     * @param null|int $id Optional ID of the FileRecord
     * @param string $scope Move content of folder (content) or file/folder itself (item).
     *
     * @return void
     */
    public function move($id = null, $scope = 'content')
    {
        // Get folder by ID or request parameters
        $item = $this->getFileEntity($id, '', true);

        // Get target
        if ($this->request->is(['patch', 'post', 'put'])) {
            $target = $this->request->getData('target');
            $target = trim($target, '/\\');
            $overwrite = (bool)$this->request->getData('overwrite', false);

            if (strpos($target, '..') !== false) {
                throw new BadRequestException('The requested path contains `..` and will not be read.');
            }

            if (($scope === 'content') && !$item->isFolder) {
                throw new BadRequestException('No valid folder.');
            }
            elseif (($scope === 'item') && ($item->name === '')) {
                throw new BadRequestException('No valid filename.');
            }

        }
        else {
            $target = $item->relativeFolder;
            $overwrite = false;
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($scope === 'item') {
                // Move file or folder
                $sourceName = $item->relativePath;
                $targetName = $target . DS . $item->name;

                if (Files::moveFileOrFolder($item->rootFolder, $sourceName, $targetName, $overwrite)) {
                    $this->controller->Answer->error(
                        __('The item could not be moved.')
                    );

                }
                else {
                    $item->movedto = $target;
                    $this->controller->Answer->success(
                        __('The item has been moved.'),
                        [
                            'action' => 'view',
                            $item->id,
                            '?' => ['root' => $item->root, 'path' => $item->movedto]
                        ]
                    );
                }

            }
            elseif ($scope === 'content') {

                // Move folder
                $target_abs = $item->rootFolder . DS . $target;
                $source_abs = $item->absoluteFolder;

                if (!Files::moveContent($source_abs, $target_abs, $overwrite)) {
                    $this->controller->Answer->error(
                        __('The folder content could not be moved.')
                    );

                }
                else {
                    $item->movedto = $target;
                    $this->controller->Answer->success(
                        __('The folder content has been moved.'),
                        [
                            'action' => 'index',
                            '?' => ['root' => $item->root, 'path' => $item->movedto]
                        ]
                    );
                }
            }
        }

        $this->controller->set(compact('item', 'target'));
        $this->renderDefault();
    }

}
