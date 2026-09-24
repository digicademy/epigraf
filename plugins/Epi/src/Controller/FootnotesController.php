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
use Epi\Model\Entity\Footnote;

/**
 * Footnotes Controller
 *
 * @property \Epi\Model\Table\FootnotesTable $Footnotes
 *
 */
class FootnotesController extends AppController
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
     * Redirect to the article that contains the footnote
     *
     * Used for the IRI resolver.
     *
     * @param string $id The footnote ID.
     * @return ?Response | void
     */
    public function view(string $id)
    {
        /** @var Footnote $entity */
        $entity = $this->Footnotes->get($id);

        if ($entity['root_tab'] === 'articles') {
            return $this->redirect([
                'controller' => 'Articles',
                'action' => 'view',
                $entity['root_id'],
                '#' => 'footnotes-' . $entity['id']
            ]);
        }

        $this->Answer->error(__('Could not resolve footnote: No redirect route configured.'));
    }

}
