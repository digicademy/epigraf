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

}
