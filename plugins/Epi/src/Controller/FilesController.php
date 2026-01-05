<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Controller;

use App\Model\Entity\Databank;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\UnauthorizedException;
use Files\Controller\Component\FilesRequestTrait;

/**
 * Files Controller
 *
 * TODO: use ActionsComponent
 * TODO: implement locks
 *
 * Manage database specific files, e.g. images.
 * The file system is reflected in the database to handle metadata.
 * Files are located in subdirectories named after the database connection
 * See the FileRequestComponent in the FilesPlugin, logic is imported from there
 *
 * @property \Epi\Model\Table\FilesTable $Files
 * @property \Files\Controller\Component\FilesRequestComponent $FilesRequest;
 */
class FilesController extends AppController
{

    // Include crud methods (index, view, download...)
    use FilesRequestTrait;

    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'api' => [
            'guest' => [],
            'reader' => [],
            'desktop' => ['download', 'display'],
            'coder' => ['download', 'display'],
            'author' => ['download', 'display'],
            'editor' => ['download', 'display'],
            'admin' => ['download', 'display'],
            'devel' => ['download', 'display']
        ],
        'web' => [
            'guest'  => ['download', 'display'],
            'reader' => ['download', 'display'],
            'coder' => ['download', 'display'],
            'desktop' => [
                'download','display','index','view','select',
                'upload','delete','edit','newfolder',
                'unzip','move'
            ],
            'author' => [
                'download','display','index','view','select',
                'upload','delete','edit','newfolder',
                'unzip','move'
            ],
            'editor' => [
                'download','display','index','view','select',
                'upload','delete','edit','newfolder',
                'unzip','move'
            ]
        ]
    ];

    public $help = 'introduction/files';

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\Event $event
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException If the folder is not found or the path contains `..`
     * @throws \Cake\Http\Exception\BadRequestException If no name for a database folder is provided
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->loadFilesRequest();
    }

    /**
     * Load the FilesRequest component
     *
     * Called by beforeFilter().
     *
     * @return void
     * @throws UnauthorizedException
     * @throws BadRequestException
     */
    protected function loadFilesRequest()
    {

        // Load component with database dir as root
        if (empty($this->activeDatabase['name'])) {
            throw new BadRequestException('No valid root folder.');
        }

        $root = Configure::read('Data.databases') . Databank::addPrefix($this->activeDatabase['name']) . DS;
        $mounts = ['root' => $root];

        $this->mounts = $mounts;
        $this->root = 'root';

        $this->loadComponent(
            'Files.FilesRequest',
            [
                'mounts' => $mounts,
                'createfolder' => true
            ]
        );
    }

}
