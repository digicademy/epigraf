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

use App\Utilities\Files\Files;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Trait FilesRequestTrait
 *
 * @property FilesRequestComponent $FilesRequest
 */
trait FilesRequestTrait
{

    /**
     * Retrieve a list of files and folders
     *
     * If id provided current folder is set according to database record.
     * File system and database are compared, database is updated if neccessary.
     *
     * @param string|null $id Folder id
     *
     * @return \Cake\Http\Response|null|void
     * @throws \Cake\Http\Exception\NotFoundException if no folder record or folder not found
     * @throws RecordNotFoundException If record not found
     */
    public function index($id = null)
    {
        $this->FilesRequest->index($id);
    }

    /**
     * Show a file.
     *
     * @param $id
     *
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException if file or folder not given or path contains `..`
     */
    public function view($id = null)
    {
        $this->FilesRequest->view($id);
    }


    /**
     * Delivers file for download.
     *
     * @param $id
     *
     * @return \Cake\Http\Response File for download
     */
    public function display($id = null)
    {
        return $this->FilesRequest->download($id, false);
    }


    /**
     * Delivers file for download.
     *
     * @param $id
     *
     * @return \Cake\Http\Response File for download
     */
    public function download($id = null)
    {
        return $this->FilesRequest->download($id);
    }


    /**
     * Select a folder or file
     *
     * @param $id
     *
     * @return \Cake\Http\Response|null|void
     */
    public function select($id = null)
    {
        $this->FilesRequest->select($id);
    }

    /**
     * Edit or replace a file
     *
     * @param $id
     *
     * @return \Cake\Http\Response|void
     */
    public function edit($id = null)
    {
        $this->FilesRequest->edit($id);
    }


    /**
     * Delete a file
     *
     * @param integer $id File Id
     *
     * @return \Cake\Http\Response|null|void redirect
     * @throws \Cake\Http\Exception\NotFoundException if file or folder not given or path contains `..`
     */
    public function delete($id = null)
    {
        $this->FilesRequest->delete($id);
    }

    /**
     * Move a file
     *
     * @param $id
     * @param $scope
     *
     * @return \Cake\Http\Response|void
     */
    public function move($id = null, $scope = 'content')
    {
        $this->FilesRequest->move($id, $scope);
    }

    /**
     * Add a folder
     *
     * @return \Cake\Http\Response|void redirects on successful folder creation, renders view otherwise
     *
     * //TODO: rename to add action
     */
    public function newfolder()
    {
        $path = $this->FilesRequest->newfolder();
    }

    /**
     * Upload a file
     *
     * Multiupload files via AJAX.
     *
     * @return \Cake\Http\Response|void redirects to index method if no AJAX request, uploads files otherwise
     */
    public function upload()
    {
        $this->FilesRequest->upload();
    }


    /**
     * Unzip file
     *
     * @param $id
     *
     * @return \Cake\Http\Response File for download
     */
    public function unzip($id = null)
    {
        return $this->FilesRequest->unzip($id);
    }

    /**
     * Clean file and folder names
     *
     * Lowercase all files and folders.
     *
     * @return \Cake\Http\Response|null
     */
    public function clean()
    {
        return $this->FilesRequest->clean();
    }

    /**
     * Sync file system and database
     *
     * @return \Cake\Http\Response|null
     */
    public function sync()
    {
        return $this->FilesRequest->sync();
    }


    /**
     * Fetch method
     *
     * Get a file from a URL.
     *
     * @return \Cake\Http\Response|void
     */
    public function fetch()
    {
        $this->FilesRequest->fetch();
    }

    /**
     * Pull from origin
     *
     * @param string $id Folder id
     * @return \Cake\Http\Response|void
     */
    public function pull($id = null)
    {
        $this->FilesRequest->pull($id);
    }

    /**
     * Clear thumbs
     *
     * @return \Cake\Http\Response|null|mixed redirects to index
     * @throws \Cake\Http\Exception\NotFoundException if file or folder not given or path contains `..`
     */
    public function clearthumbs()
    {
        if (Files::clearThumbs()) {
            $this->Flash->success(__('The thumb cache has been cleared.'));
        }
        else {
            $this->Flash->error(__('The thumb cache could not be cleared. Please try again.'));
        }

        return $this->redirect([
            'action' => 'index',
            '?' => ['root' => $this->root, 'path' => $this->currentFolderSegment]
        ]);
    }

}
