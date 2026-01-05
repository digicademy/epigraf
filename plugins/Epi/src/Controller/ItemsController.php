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

use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Epi\Model\Entity\Article;

/**
 * Items Controller
 *
 * @property \Epi\Model\Table\ItemsTable $Items
 *
 */
class ItemsController extends AppController
{

    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'web' => [
            'guest' => ['view'],
            'reader' => ['view'],
            'desktop' => ['view'],
            'author' => ['view'],
            'editor' => ['view'],
            'admin' => ['view']
        ]
    ];

    /**
     * Redirect to the article
     *
     * Used for the IRI resolver
     *
     * @param string $id The item ID
     * @return ?Response
     */
    public function view(string $id)
    {
        /** @var Article $entity */
        $entity = $this->Items->get($id);

        return $this->redirect([
            'controller' => 'Articles',
            'action' => 'view',
            $entity['articles_id'],
            '#' => 'sections-' . $entity['sections_id']
        ]);

    }

    /**
     * Get aggregated item statistics
     *
     * ## Scopes
     * - tiles: Geodata for maps
     * - timeline: Timeline data
     * - graph Network data
     *
     * @param string $scope The scope of the statistic.
     * @return void
     */
    public function groups($scope = 'timeline')
    {
        // Get search parameters from request
        [$params, $columns, $paging, $filter] = $this->Actions->prepareParameters();

        // Timeline
        if ($scope === 'timeline') {
            $groups = $this->Items->find('timeline', $params);
            $columns = $this->Items->augmentColumnSetup(
                ['x', 'y', 'z', 'y_id', 'y_label', 'y_type','grouptype', 'totals'],
                true, ['selected' => true, 'aggregate' => 'min']
            );
        }

        // Geodata tiles
        elseif ($scope === 'tiles') {
            $groups = $this->Items->find('tiles', $params)->all();
            $columns = $this->Items->augmentColumnSetup(
                ['x', 'y', 'z','totals','type'], true,
                ['selected' => true, 'aggregate' => 'min']
            );
        }

        // Property-article-graph
        elseif ($scope === 'graph') {
            $linksModel = $this->Items;
            $groups = $linksModel->find('graph', $params)->all();
            $columns = $linksModel->augmentColumnSetup(
                ['x', 'y', 'z', 'x_label', 'y_label','x_type','y_type', 'x_id','y_id', 'x_image', 'y_image', 'grouptype'], true,
                ['selected' => true, 'aggregate' => 'min']
            );
        }
        else {
            throw new BadRequestException(__('Invalid scope'));
        }

        $this->Answer->addOptions(compact('params', 'columns', 'filter', 'scope'));
        $this->Answer->addAnswer(compact('groups'));

    }
}
