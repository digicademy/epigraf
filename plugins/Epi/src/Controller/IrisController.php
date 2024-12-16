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

use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

/**
 * Iris Controller
 *
 * Redirect IRIs to properties and articles.
 *
 * @property \Epi\Model\Table\PropertiesTable $Properties
 * @property \Epi\Model\Table\ArticlesTable $Articles
 */
class IrisController extends AppController
{

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

    /**
     * Resolves a IRI and redirects to the corresponding entity
     *
     * TODO: Handle renamed types / IRIs
     * TODO: Handle IRIs of merged properties
     * TODO: Resolve footnotes and links IRIs
     * TODO: Resolve IRIs for files, users, notes
     *
     * Resolves different types of IRIs:
     * - Typed public IRIs: articles/epi-article/mv~168
     * - Typed database IRIs: articles/epi-article/mv~168?database=epi_mv
     * - Prefixed public IRIs: articles-168
     * - Prefixed database IRIs: articles-168?database?epi_mv
     * - Typed ad hoc IRIs: articles/epi-article/mv~168
     *   If no article with the iri fragment exists, first,
     *   splits the IRI path into table, type and IRI fragment,
     *   then splits the IRI fragment into ID and source.
     *   Second, looks up the ID in the public database
     *   and as fallback in the source database.
     *
     * @param string $table The database table (types, properties, articles)
     * @param string $type The type's scope, property type or article type
     * @param string $irifragment The IRI fragment
     *
     * @return ?Response
     */
    public function show($table, $type=null, $irifragment=null)
    {
        // Redirect to other databases in the query parameter
        $dbQuery = $this->request->getQuery('database');
        $dbDefault = $this->request->getParam('database');

        if (!empty($dbQuery) && ($dbQuery !== $dbDefault)) {
            return $this->redirect([
                'plugin' => 'Epi',
                'database' => $dbQuery,
                'controller' => 'Iris',
                'action' => 'show',
                $table, $type, $irifragment
            ],
            303);
        }


        $id = null;

        // When table prefixed IDs are passed as IRI
        if (is_null($type) && is_null($irifragment)) {
            $table = explode('-', $table);
            $id = $table[1] ?? null;
            $table = $table[0] ?? '';
        }

        // TODO: Derive from tables / entities which have properties
        //       such as $_field_iri. Make them static.
        $tableMap = [
            'types' => [
                'controller' => 'Types',
                'scopeField' => 'scope',
                'iriField' => 'name'
            ],
            'properties' => [
                'controller' => 'Properties',
                'scopeField' => 'propertytype',
            ],
            'projects' => [
                'controller' => 'Projects',
                'scopeField' => 'projecttype',
                'iriField' => 'signature'
            ],
            'articles' => [
                'controller' => 'Articles',
                'scopeField' => 'articletype',
            ],
            'sections' => [
                'controller' => 'Sections',
                'scopeField' => 'sectiontype',
            ],
            'items' => [
                'controller' => 'Items',
                'scopeField' => 'itemtype',
            ]
        ];

        $controller = $tableMap[$table]['controller'] ?? null;
        $scopeField = $tableMap[$table]['scopeField'] ?? null;

        // Quick fix. TODO: handle renamed types / IRIs
        if (($table === 'articles') && ($type=='object')) {
            $type = 'epi-article';
        }

        if (empty($id) && !empty($controller) && !empty($scopeField)) {
            $modelTable = $this->fetchTable('Epi.'. $controller);
            $item = $modelTable
                ->find('all')
                ->where([$scopeField => $type, 'norm_iri' => $irifragment])
                ->first();

            // Parse ad hoc IRIs, which are constituted by source, tilde, and a value from
            // the field used to generate ad hoc IRIs. This field is defined in the entity's
            // $_fieldIri property. See the $tableMap above.
            if (!$item) {
                $fragmentParts = explode('~', $irifragment, 2);
                if (count($fragmentParts) == 2) {
                    // Redirect to the source database if it is different from the current database
                    if ( ('epi_' . $fragmentParts[0]) !== $dbDefault) {

                        return $this->redirect([
                            'plugin' => 'Epi',
                            'database' => 'epi_' . $fragmentParts[0],
                            'controller' => 'Iris',
                            'action' => 'show',
                            $table, $type, $irifragment
                        ],303);
                    }

                    // Find record
                    else {
                        $iriField = $tableMap[$table]['iriField'] ?? 'id';
                        $item = $modelTable
                            ->find('all')
                            ->where([$scopeField => $type, $iriField => $fragmentParts[1]])
                            ->first();
                    }
                }
            }
            $id = $item ? $item->id : null;
        }

        if (empty($controller) || empty($id) || empty($dbDefault))
        {
            throw new NotFoundException('The IRI was not found.');
        }

        return $this->redirect([
            'plugin' => 'Epi',
            'database' => $dbDefault,
            'controller' => $controller,
            'action' => 'view',
            $id
        ],303);
    }
}
