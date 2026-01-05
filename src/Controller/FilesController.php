<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\UnauthorizedException;
use Files\Controller\Component\FilesRequestTrait;
use Cake\Http\Exception\BadRequestException;

/**
 * Files Controller
 *
 * Manage application wide files, e.g. database export templates.
 * File system is reflected in database to provide access for EpigrafDesktop.
 *
 * @property \Files\Controller\Component\FilesRequestComponent $FilesRequest
 * @property \App\Model\Table\FilesTable $Files
 */
class FilesController extends AppController
{

    // Include common methods such as index, view, download....
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
            'coder' => ['download', 'display', 'view'],
            'desktop' => ['download', 'display', 'index', 'view'],
            'author' => ['download', 'display', 'view'],
            'editor' => ['download', 'display', 'index', 'view'],
            'admin' => ['download', 'display', 'index', 'view'],
            'devel' => ['download', 'display', 'index', 'view']
        ],
        'web' => [
            'guest' => ['download', 'display'],
            'reader' => ['download', 'display', 'view'],
            'coder' => ['download', 'display', 'view'],
            'desktop' => [
                'download',
                'display',
                'index',
                'view',
                'upload',
                'delete',
                'edit',
                'newfolder',
                'select',
                'move'
            ],
            'author' => [
                'download',
                'display',
                'view',
                'select'
            ],
            'editor' => [
                'download',
                'display',
                'index',
                'view',
                'upload',
                'delete',
                'edit',
                'newfolder',
                'select',
                'move'
            ],
            'admin' => [
                'download',
                'display',
                'index',
                'view',
                'upload',
                'delete',
                'edit',
                'newfolder',
                'select',
                'move'
            ]
        ]
    ];

    public $help = 'administration/files';

    /**
     * beforeFilter callback
     *
     * @param \Cake\Event\EventInterface $event
     *
     * @return \Cake\Http\Response|void|null
     * @throws \Cake\Http\Exception\NotFoundException if folder not found or path contains `..`
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->loadFilesRequest();
    }

    /**
     * Load FilesRequest component
     *
     * called by beforeFilter
     *
     * @return void
     * @throws UnauthorizedException
     * @throws BadRequestException
     */
    protected function loadFilesRequest()
    {
        // Set root dir and mounts
        $mounts = [
            'shared' => Configure::read('Data.shared'),
            'root' => Configure::read('Data.root')
        ];

        // TODO: check mount of file if requested by ID (view, edit, select...)
        //       Update: the permission to access the mount is checked in getFileEntity()
        $user = $this->Auth->user();
        $allowedMounts = $this->getMounts($user);
        $allowedMounts = array_intersect_key($mounts, array_flip($allowedMounts));
        $this->mounts = $allowedMounts;

        $this->root = $this->request->getQuery('root', 'shared');

        if (!in_array($this->root, array_keys($allowedMounts))) {
            throw new UnauthorizedException('Access to mount not allowed.');
        }

        // Load FilesRequest component for parsing of query parameters
        $this->loadComponent('Files.FilesRequest',
            [
                'mounts' => $allowedMounts,
                'createfolder' => true
            ]);
    }

    /**
     * Get mounts based on user authorization
     *
     * //TODO: use permission system, introduce permissions with prefix mount
     * @param $user
     *
     * @return array|string[]
     */
    protected function getMounts($user = null)
    {
        if (empty($user)) {
            // Public downloads for guests
            $mounts = ['shared'];
        }
        elseif (in_array($this->request->getParam('action'), ['download', 'display'])) {
            // Download for all
            $mounts = ['shared', 'root'];
        }
        elseif (in_array($this->userRole, ['author', 'editor', 'desktop', 'coder', 'bot'])) {
            // Author
            $mounts = ['shared'];
        }
        elseif (in_array($this->userRole, ['admin', 'devel'])) {
            //Admin
            $mounts = ['shared', 'root'];
        }
        else {
            $mounts = [];
        }

        return $mounts;

    }


}
