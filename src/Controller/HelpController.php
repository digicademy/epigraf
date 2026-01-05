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

use App\Model\Entity\Help;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;

/**
 * Help Controller
 *
 * Help pages are stored as markdown files in the help folder.
 * The folder follows Jekyll conventions.
 *
 * When F1 is hit in EpigrafDesktop the show action of
 * docs controller is requested with the norm_iri in the query
 * parameter key. The corresponding help page is opened if it
 * exists.
 */
class HelpController extends AppController
{
    /**
     * Table, entity class and segment
     *
     * @var string $defaultTable The table model class name
     * @var string $modelClass The table model class name
     * @var string $segment The segment in the table
     */
    public $defaultTable = 'Docs';
    public $modelClass = 'Docs';
    public $segment = 'wiki';


    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'web' => [
            'guest' => ['show'],
            'reader' => ['show'],
            'coder' => ['show'],
            'desktop' => ['show'],
            'author' => ['show'],
            'editor' => ['show']
        ]
    ];

    public $help = '/';

    /**
     * Initialization hook method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * beforeFilter callback
     *
     * @param EventInterface $event
     *
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->set(['title' => __('Help')]);

        $this->_activateMainMenuItem(
            [
                'plugin' => false,
                'controller' => 'Help',
                'action' => 'show',
                'start'
            ]);

        $this->sidemenu = Help::getMenu();
    }

    /**
     * Show a help page
     *
     * @param string|null $path The document IRI
     * @return void
     * @throws RecordNotFoundException If record not found and unauthenticated user
     */
    public function show()
    {
        // Get path without '/help/'
        $path = $this->request->getPath();
        $path = substr($path, 6);

        // Deliver assets
        if (str_starts_with($path, 'assets/')) {
            $assetPath = Help::getHelpFolder() . $path;
            if (file_exists($assetPath)) {
                return $this->response->withFile($assetPath);
            }
        }

        // Load help file
        $help = new Help(['path' => $path]);

        // Redirect to wiki
        if (!$help->exists && !empty($path)) {
            $path = str_replace('/', '-', $path);
            $this->Answer->redirect(
                ['controller' => 'Wiki', 'action' => 'show', $path]
            );
        }

        $this->Answer->addAnswer(compact('help'));
    }

}
