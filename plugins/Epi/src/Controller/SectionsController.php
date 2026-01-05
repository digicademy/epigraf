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
 * Sections Controller
 *
 * @property \Epi\Model\Table\SectionsTable $Sections
 *
 */
class SectionsController extends AppController
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
            'author' => ['view', 'add', 'move'],
            'editor' => ['view', 'add', 'move'],
            'admin' => ['view', 'add', 'move']
        ]
    ];

    /**
     * Get a section template
     *
     * Used for inserting sections into articles in the frontend (by AJAX)
     *
     * @param string $articleId The root article
     * @param string $type The section key as defined in the config
     * @return void
     */
    public function add($articleId, $sectionKey=null)
    {
        /** @var Article $entity */
        $entity = $this->Sections->SectionArticles->get($articleId);

        if ($sectionKey) {
            $entity = $entity->addSection($sectionKey, false);
        }

        $this->set(compact('entity'));
    }


    /**
     * Redirect to the article
     *
     * Used for the IRI resolver
     *
     * @param string $id The section ID
     * @return ?Response
     */
    public function view(string $id)
    {
        /** @var Article $entity */
        $entity = $this->Sections->get($id);

        return $this->redirect([
            'controller' => 'Articles',
            'action' => 'view',
            $entity['articles_id'],
            '#' => 'sections-' . $id
        ]);

    }

    /**
     * Move sections to a new position
     *
     * This endpoint supports only POST requests containing
     * either a single move operation or a batch of move operations.
     *
     * ## Single move operation
     * The payload contains the following keys:
     * - reference_id The ID of a reference node
     * - reference_pos The position of the reference node: 'parent' or 'preceding'
     *
     * ### Batch move operations
     * The moves field contains an array of all moves.
     * A single call supports only moving sections within the same article.
     * Each move is an array with the following keys:
     * - id The ID of the section to move
     * - parent_id The ID of the new parent
     * - preceding_id The ID of the preceding sibling
     *
     * @param string $id For single moves the section ID, for batch moves the article ID
     * @return \Cake\Http\Response|null|void
     */
    public function move($id)
    {
        $this->Actions->move($id);
    }

}
