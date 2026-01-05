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

use App\Model\Entity\Databank;
use Cake\Http\Exception\NotFoundException;

/**
 * Articles Controller
 *
 * Full text search in different databases
 */
class ArticlesController extends AppController
{

    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'web' => [
            'reader' => [],
            'desktop' => ['show', 'search'],
            'author' => ['show', 'search'],
            'editor' => ['show', 'search']
        ]
    ];

    public $help = 'introduction/articles';

    /**
     * Search method
     *
     * @return \Cake\Http\Response|void
     * @throws \Cake\Http\Exception\NotFoundException if no valid database is provided in the request
     * @deprecated Remove with EpiDesktop or implement cross-database search
     *
     */
    public function search()
    {
        $db_name = $this->request->getQuery('database');

        /** @var Databank $databank */
        $databank = $this->fetchTable('Databanks')->find('all')->where(['name' => Databank::addPrefix($db_name)])->first();
        if (empty($databank)) {
            throw new NotFoundException('Database not found.');
        }

        return $this->redirect([
            'plugin' => $databank->plugin, //epi
            'database' => $db_name,
            'controller' => 'Articles',
            'action' => 'index',
            'token' => $this->request->getQuery('token'),
            '?' => ['projects' => $this->request->getQuery('project')]
        ]);
    }

    /**
     * Redirect to plugin
     *
     * @return \Cake\Http\Response|null
     * @deprecated Use IrisController
     */
    public function show()
    {
        $db_name = $this->request->getQuery('database');

        /** @var Databank $databank */
        $databank = $this->fetchTable('Databanks')->find('all')->where(['name' => Databank::addPrefix($db_name)])->first();
        if (empty($databank)) {
            throw new NotFoundException('Database not found.');
        }

        return $this->redirect([
            'plugin' => $databank->plugin, //epi
            'database' => $db_name,
            'controller' => 'Articles',
            'action' => 'view',
            'token' => $this->request->getQuery('token'),
            $this->request->getQuery('article')
        ]);
    }

}
