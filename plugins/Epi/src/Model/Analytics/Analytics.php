<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Analytics;

use App\Utilities\Statistics\Statistics;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Routing\Router;
use Exception;


/**
 * Analytics class
 */
class Analytics
{

    /* Map table names to Model names*/
    public $models = [
        'Epi.Articles' => [
            'table' => 'articles',
            'label' => 'Articles',
            'notice' => 'Summary (min, mean, max) is based on articles by project (=group)',
            'typefield' => 'articletype',
            'groupfield' => 'projects_id'

        ],
        'Epi.Sections' => [
            'table' => 'sections',
            'label' => 'Sections',
            'notice' => 'Summary (min, mean, max) is based on sections by article (=group)',
            'typefield' => 'sectiontype',
            'groupfield' => 'articles_id'
        ],
        'Epi.Items' => [
            'table' => 'items',
            'label' => 'Items',
            'notice' => 'Summary (min, mean, max) is based on items by article (=group)',
            'typefield' => 'itemtype',
            'groupfield' => 'articles_id'
        ],
        'Epi.Properties' => [
            'table' => 'properties',
            'label' => 'Properties',
            'typefield' => 'propertytype'
        ],
        'Epi.Footnotes' => [
            'table' => 'footnotes',
            'label' => 'Footnotes',
            'typefield' => 'from_tagname'
        ],
        'Epi.Links' => [
            'table' => 'links',
            'label' => 'Links',
            'typefield' => 'from_tagname'
        ],
        'Epi.Files' => [
            'table' => 'files',
            'label' => 'Files',
            'typefield' => 'root'
        ]
    ];

    /* Get model name from table name */
    public function _getModelName($tableName)
    {
        $tables = array_combine(
            Hash::extract($this->models, '{*}.table'),
            array_keys($this->models)
        );

        if (!isset($tables[$tableName])) {
            throw new BadRequestException('The table parameter is invalid.');
        }

        return $tables[$tableName];
    }

    /**
     * Get Query
     *
     * @param string $modelName
     * @return Query
     */
    public function _getQuery($modelName)
    {
        $query = TableRegistry::getTableLocator()->get($modelName);
        return $query->find();
    }

    /**
     * Mark unwanted constellations and create links
     * to get respective items and file records
     *
     * @param array $data Cross table from findCompleteness
     * @param string $table Table name
     * @return array
     */
    public function _decorateCompleteness($data, $table)
    {
        $data = collection($data)
            ->map(function ($row) use ($table) {

                // Init error, warning and link arrays
                $row['classes'] = [];
                $row['link'] = [];

                // Link numbers
                $row['link']['n_missing'] = Router::url(
                    [
                        'controller' => 'Analytics',
                        'action' => 'filesCases',
                        $table,
                        '?' => [
                            'type' => $row['file_type'] ?? $row['type'],
                            'online' => $row['file_online'],
                            'missing' => true
                        ]
                    ]
                );

                $row['link']['n_available'] = Router::url(
                    [
                        'controller' => 'Analytics',
                        'action' => 'filesCases',
                        $table,
                        '?' => [
                            'type' => $row['file_type'] ?? $row['type'],
                            'online' => $row['file_online'],
                            'missing' => false
                        ]
                    ]
                );

                // Add errors: Missing files marked as os being online
                if ($row['file_online'] & ($row['n_missing'] > 0)) {
                    $row['classes']['n_missing'][] = 'status_notok';
                }

                // Add warning: Available files not marked as being online
                if (!$row['file_online'] & ($row['n_available'] > 0)) {
                    $row['classes']['n_available'][] = 'status_warn';
                }

                // Wanted file types that are missing
                if ($row['wanted'] & ($row['n_missing'] > 0)) {
                    $row['classes']['n_missing'][] = 'status_warn';
                }

                // Unwanted file types that are online
                if (!$row['wanted'] & ($row['n_available'] > 0)) {
                    $row['classes']['n_available'][] = 'status_warn';
                }

                // Recode variables
                if (isset($row['file_online'])) {
                    $row['file_online'] = $row['file_online'] ? __('yes') : __('no');
                    $row['classes']['file_online'][] = $row['file_online'] ? 'status_yes' : 'status_no';
                }

                if (isset($row['wanted'])) {
                    $row['wanted'] = $row['wanted'] ? __('yes') : __('no');
                    $row['classes']['wanted'][] = $row['wanted'] ? 'status_yes' : 'status_no';
                }

                return $row;
            })
            ->toArray();

        return $data;
    }

    /**
     * Count Table Types: Calculates the amount of types in the table.
     *
     * @return array $data with label of table (label)
     *        and array (typeCound) with type and number of entries
     */
    public function countTypes()
    {
        $data = [];

        foreach ($this->models as $modelName => $modelDef) {
            $query = $this->_getQuery($modelName);
            $typeField = $modelDef['typefield'];

            // Add Count
            $typesCount = $query
                ->select([
                    $typeField,
                    'n' => $query->func()->count('*'),
                ])
                ->group([$typeField])
                ->order([
                    'n' => 'DESC'
                ]);

            // Get five point summary
            if (!empty($modelDef['groupfield'])) {
                $typesSummary = $this->typesPerGroup($modelName);
                $cols = ['groups', 'min', 'mean', 'max', 'count'];
            }
            else {
                $typesSummary = [];
                $cols = ['count'];
            }

            $rows = [];
            foreach ($typesCount as $typeData) {
                $typeName = $typeData[$typeField];
                $rows[$typeName] = ['count' => $typeData['n']];

                if (!empty($typesSummary[$typeName])) {
                    $rows[$typeName] = array_merge($rows[$typeName], $typesSummary[$typeName]);
                };
            }

            $total = ['count' => array_sum(Hash::extract($rows, '{*}.count'))];

            $data[] =
                [
                    'label' => $modelDef['label'],
                    'notice' => $modelDef['notice'] ?? '',
                    'tableId' => '',
                    'cols' => $cols,
                    'rows' => $rows,
                    'total' => $total

                ];
        }
        return $data;
    }


    /**
     * Descriptive statistical parameters per group
     *
     * E.g., how many items of type text do articles have?
     * Calculates Mean, SD, Median, Quartiles
     */
    public function typesPerGroup($modelName)
    {
        $modelDef = $this->models[$modelName];
        $stats = [];

        $query = $this->_getQuery($modelName);
        $groupField = $modelDef['groupfield'];
        $typeField = $modelDef['typefield'];

        $data = $query
            ->select([
                $typeField,
                $groupField,
                'n' => $query->func()->count('*')
            ])
            ->group([$typeField, $groupField])
            ->disableHydration()
            ->toArray();

        $data = collection($data)->groupBy(fn($x) => $x[$typeField] ?? '');
        foreach ($data as $typeName => $rows) {

            $n = Hash::combine($rows, '{*}.' . $groupField, '{*}.n');
            $stats[$typeName] =
                [
                    'groups' => count(array_unique(array_keys($n))),
                    'min' => min($n),
                    'mean' => round(Statistics::mean($n), 1),
                    'max' => max($n)
                ];
        }

        return $stats;
    }


    /**
     * Count Table Entries, grouped by variable.
     * ** Not in use yet **
     *
     * @param array $models array of models. (Format: label => Epi.Modelname)
     * @param string $entries variable used for calculation
     * @param string $group variables used for grouping
     * @return array $data
     *
     */
    public function countTableEntriesPerGroup($models, $entries = '', $group = '')
    {
        $data = [];

        foreach ($models as $label => $modelName) {

            if ($entries == $label) {
                $query = $this->_getQuery($modelName);
                $CountPerGroup = $query
                    ->select([
                        $group,
                        'count' => $query->func()->count('*')
                    ])
                    ->group([$group]);

                $data[] =
                    [
                        'label' => $label,
                        'typeCount' => $CountPerGroup
                    ];
            }
        }
        return $data;
    }


    function getCompleteness()
    {
        $result = [];

        foreach ($this->models as $modelName => $modelDef) {
            $model = TableRegistry::getTableLocator()->get($modelName);
            if ($model->hasFinder('completeness')) {
                $summary = $model->find('completeness')->toArray();

                $summary = $this->_decorateCompleteness($summary, $modelDef['table']);
                $result[$modelDef['table']] = $summary;

            }
        }

        return $result;
    }

    /**
     * Gets cases identified by checkCompleteness function
     * @param string $table table for the completeness check
     * @param array $options parameters to link to the respective cases
     * @return Query $count
     */
    public function getIncompleteCases($table, $options)
    {
        $model = TableRegistry::getTableLocator()->get($this->_getModelName($table));
        $cases = $model->find('incomplete', $options);

        return $cases;
    }

    /**
     * Count groups of incomplete cases (articles, paths)
     *
     * @param $table
     * @param Query $query A query returned from getIncompleteCases
     * @return string A summary string.
     */
    public function getIncompleteSummary($table, $query)
    {
        if ($table == 'items') {
            $countGroup = $this->countQueryPerGroup($query, 'Items.articles_id');
            $summary = __('{0} articles', $countGroup);
        }
        elseif ($table == 'files') {
            $countGroup = $this->countQueryPerGroup($query, 'path');
            $summary = __('{0} paths', $countGroup);
        }
        else {
            $summary = '';
        }

        return $summary;
    }

    /**
     * Get number of cases in a query-object by group
     * @return int $count
     */
    public function countQueryPerGroup($query, $group)
    {
        $clone = $query->cleanCopy();

        $count = $clone
            ->group([$group])
            ->count();

        return $count;
    }


    /**
     * Get SQL statement specified by id
     *
     * @param $id
     *
     * @return false|mixed
     */
    public function getHealthQuery($id = false)
    {
        $queries = $this->getHealthQueries();
        return $queries[$id] ?? [];
    }

    /**
     * Create SQL statements for testing structural integrity
     *
     * @return array|false
     */
    public function getHealthQueries()
    {
        $conn = ConnectionManager::get('projects');
        $queries = [];

        /**
         *  articles -> projects
         */

        $queries[] = [
            'sql' => 'FROM articles WHERE projects_id IS NULL  AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "articles", id, "articles", articles.id',
            'type' => 'Articles with null Project',
            'target' => 0
        ];

        $queries[] = [
            'sql' => 'FROM articles WHERE deleted = 0 AND projects_id NOT IN (SELECT id FROM projects WHERE deleted = 0)',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "articles", id, "articles", articles.id',
            'type' => 'Articles with invalid Project',
            'target' => 0
        ];

        /**
         * sections -> articles
         */

        $queries[] = [
            'sql' => 'FROM sections WHERE articles_id IS NULL  AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "sections", id, "articles", NULL',
            'clean' => 'UPDATE sections SET deleted = 3 WHERE articles_id IS NULL  AND deleted = 0',
            'type' => 'Sections with null Article',
            'target' => 0
        ];

        $queries[] = [
            'sql' => 'FROM sections WHERE deleted = 0 AND articles_id NOT IN (SELECT id FROM articles WHERE deleted = 0)',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "sections", id, "articles", articles_id',
            'type' => 'Sections with invalid Articles',
            'target' => 0
        ];

        /**
         * items -> sections + articles
         */

        $queries[] = [
            'sql' => 'FROM items WHERE sections_id IS NULL AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "items", id, "articles", articles_id',
            'clear' => 'UPDATE items SET deleted = 3 WHERE sections_id IS NULL AND deleted = 0',
            'type' => 'Items with null Section',
            'target' => 0
        ];

        $queries[] = [
            'sql' => 'FROM items WHERE deleted = 0 AND sections_id NOT IN (SELECT id FROM sections WHERE deleted = 0)',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "items", id, "articles", articles_id',
            'clear' => 'UPDATE items SET deleted = 3 WHERE deleted = 0 AND sections_id NOT IN (SELECT id FROM sections WHERE deleted = 0)',
            'type' => 'Items with invalid Section',
            'target' => 0
        ];

        $queries[] = [
            'sql' => 'FROM items WHERE articles_id IS NULL AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "items", id, "articles", articles_id',
            'type' => 'Items with null Article',
            'target' => 0
        ];

        $queries[] = [
            'sql' => 'FROM items WHERE deleted = 0 AND articles_id NOT IN (SELECT id FROM articles WHERE deleted = 0)',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "items", id, "articles", articles_id',
            'type' => 'Items with invalid Article',
            'target' => 0
        ];


        $queries[] = [
            'sql' => 'FROM articles WHERE (articletype IS NULL OR articletype = "") AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "articles", id, NULL, NULL',
            'type' => 'Articles: empty articletype',
            'target' => 0
        ];

        $queries[] = [
            'sql' => 'FROM sections WHERE (sectiontype IS NULL OR sectiontype = "") AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "sections", id, NULL, NULL',
            'type' => 'Sections: empty sectiontype',
            'target' => 0
        ];

        $queries[] = [
            'sql' => 'FROM properties WHERE (propertytype IS NULL OR propertytype = "") AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "properties", id, NULL, NULL',
            'type' => 'Properties: empty propertytype',
            'target' => 0
        ];

        $queries[] = [
            'sql' => 'FROM items WHERE (itemtype IS NULL OR itemtype = "") AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "items", items.id, "articles", articles_id',
            'type' => 'Items: empty itemtype',
            'target' => 0
        ];

        /**
         * properties -> items (itemtype)
         */
        $itemTypes = $conn->execute(
            'SELECT DISTINCT itemtype FROM items WHERE properties_id IS NULL AND deleted = 0'
        )->fetchAll();

        // Get items that should contain properties
        $itemTypesConfig = TableRegistry::getTableLocator()->get('Epi.Types')
            ->find('all')
            ->where(['scope' => 'items'])
            ->toArray();

        $itemTypesConfig = collection($itemTypesConfig)
            ->indexBy('name')
            ->map(function ($type) {
                return $type['merged']['fields']['property']['types'] ?? '';

            })
            ->toArray();

        $itemTypesConfig = array_filter($itemTypesConfig);

        foreach ($itemTypes as $itemType) {
            $itemType = $itemType[0];
            if (empty($itemType)) {
                continue;
            }

            if ($itemTypesConfig[$itemType] ?? false) {
                $queries[] = [
                    'sql' => 'FROM items WHERE itemtype="' . $itemType . '" AND properties_id IS NULL AND deleted = 0',
                    'sql_count' => 'SELECT COUNT(*)',
                    'sql_records' => 'SELECT "items", items.id, "articles", articles_id',
                    'type' => 'Null items to properties by properties id (itemtype=' . $itemType . ')',
                    'target' => 0
                ];  //'itemtype'=>$itemType[0],'number'=>$itemType[1],
            }
            else {
                $queries[] = [
                    'sql' => 'FROM items WHERE itemtype="' . $itemType . '" AND properties_id IS NOT NULL AND deleted = 0',
                    'sql_count' => 'SELECT COUNT(*)',
                    'sql_records' => 'SELECT "items", items.id, "articles", articles_id',
                    'type' => 'Nonnull items to properties by properties id (itemtype=' . $itemType . ')',
                    'target' => 0
                ];  //'itemtype'=>$itemType[0],'number'=>$itemType[1],
            }
        }

        /**
         * properties
         */

        $relation = ['from' => 'items', 'to' => 'properties', 'by' => 'properties_id'];

        $queries[] = [
            'sql' => 'FROM ' . $relation['from'] . ' WHERE ' . $relation['by'] . ' IS NULL AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "' . $relation['from'] . '", id, "articles", articles_id',
            'type' => 'Null ' . $relation['from'] . ' to ' . $relation['to'] . ' by ' . $relation['by'],
            'target' => 0
        ];

        $queries[] = [
            'sql' => 'FROM ' . $relation['from'] . ' WHERE deleted = 0 AND ' . $relation['by'] . ' NOT IN (SELECT id FROM ' . $relation['to'] . ' WHERE deleted = 0)',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "' . $relation['from'] . '", id, "articles", articles_id',
            'type' => 'Invalid ' . $relation['from'] . ' to ' . $relation['to'] . ' by ' . $relation['by'],
            'target' => 0
        ];

        /**
         *  footnotes - root
         */
        $queries[] = [
            'sql' => 'FROM footnotes WHERE root_tab IS NULL AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "footnotes", id, root_tab, root_id',
            'clear' => 'UPDATE footnotes SET deleted = 3 WHERE root_tab IS NULL and root_id IS NULL and from_tab IS NULL and from_id IS NULL AND deleted = 0',
            'type' => 'Footnotes with null root_tab',
            'target' => 0
        ];

        $roottabs = $conn->execute('SELECT DISTINCT root_tab FROM footnotes WHERE deleted = 0')->fetchAll();

        foreach ($roottabs as $roottab) {
            $roottab = $roottab[0];
            if (empty($roottab)) {
                continue;
            }

            $queries[] = [
                'sql' => 'FROM footnotes WHERE root_id IS NULL AND root_tab = "' . $roottab . '" AND deleted = 0',
                'sql_count' => 'SELECT COUNT(*)',
                'sql_records' => 'SELECT "footnotes", id, root_tab, root_id',
                'type' => 'Footnotes with null root_id (root_tab = ' . $roottab . ')',
                'target' => 0
            ];

            $queries[] = [
                'sql' => 'FROM footnotes WHERE deleted = 0 AND root_tab = "' . $roottab . '" AND root_id NOT IN (SELECT id FROM ' . $roottab . ' WHERE deleted = 0)',
                'sql_count' => 'SELECT COUNT(*)',
                'sql_records' => 'SELECT "footnotes", id, root_tab, root_id',
                'type' => 'Footnotes with invalid root_id (root_tab = ' . $roottab . ')',
                'target' => 0
            ];
        }

        /**
         * footnotes - link
         */

        $queries[] = [
            'sql' => 'FROM footnotes WHERE from_tab IS NULL AND  deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "footnotes", id, root_tab, root_id',
            'type' => 'Footnotes with null from_tab',
            'target' => 0
        ];

        $roottabs = $conn->execute('SELECT DISTINCT from_tab FROM footnotes WHERE deleted = 0')->fetchAll();

        foreach ($roottabs as $roottab) {
            $roottab = $roottab[0];
            if (empty($roottab)) {
                continue;
            }

            $queries[] = [
                'sql' => 'FROM footnotes WHERE from_id IS NULL AND from_tab = "' . $roottab . '" AND deleted = 0',
                'sql_count' => 'SELECT COUNT(*)',
                'sql_records' => 'SELECT "footnotes", id, root_tab, root_id',
                'type' => 'Footnotes with null from_id (from_tab = ' . $roottab . ')',
                'target' => 0
            ];

            $queries[] = [
                'sql' => 'FROM footnotes WHERE deleted = 0 AND from_tab = "' . $roottab . '" AND from_id NOT IN (SELECT id FROM ' . $roottab . ' WHERE deleted = 0)',
                'sql_count' => 'SELECT COUNT(*)',
                'sql_records' => 'SELECT "footnotes", id, root_tab, root_id',
                'type' => 'Footnotes with invalid from_id (from_tab = ' . $roottab . ')',
                'target' => 0
            ];
        }

        /**
         * footnotes - tagid
         */

        $roottabs = $conn->execute(
            'SELECT DISTINCT from_tab, from_field FROM footnotes WHERE deleted = 0'
        )->fetchAll();

        foreach ($roottabs as $roottab) {
            $linktab = $roottab[0];
            $linkfeld = $roottab[1];
            if (empty($linktab)) {
                continue;
            }

            $sql = 'FROM footnotes LEFT JOIN ' . $linktab . ' AS source ON source.deleted = 0 AND footnotes.from_id = source.id AND ' .
                'source.' . $linkfeld . ' LIKE CONCAT("%",footnotes.from_tagid,"%") ' .
                'WHERE footnotes.deleted = 0 AND footnotes.from_tab = "' .
                $linktab . '" AND footnotes.from_field = "' . $linkfeld . '" AND source.id IS NULL';

            $clear = 'UPDATE footnotes LEFT JOIN ' . $linktab . ' AS source ON source.deleted = 0 AND footnotes.from_id = source.id AND ' .
                 'source.' . $linkfeld . ' LIKE CONCAT("%",footnotes.from_tagid,"%") SET footnotes.deleted = 3 ' .
                'WHERE footnotes.deleted = 0 AND footnotes.from_tab = "' . $linktab .
                '" AND footnotes.from_field = "' . $linkfeld . '" AND source.id IS NULL';

            $queries[] = [
                'sql' => $sql,
                'clear' => $clear,
                'sql_count' => 'SELECT COUNT(*)',
                'sql_records' => 'SELECT "footnotes", footnotes.id, root_tab, root_id',
                'type' => 'Footnotes with invalid from_tagid (from_tab = ' . $linktab . ', from_field = ' . $linkfeld . ')',
                'target' => 0
            ];
        }


        /**
         * links - root
         */

        $queries[] = [
            'sql' => 'FROM links WHERE root_tab IS NULL AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "links", id, root_tab, root_id',
            'clear' => 'UPDATE links SET deleted = 3 WHERE root_tab IS NULL and root_id IS NULL and from_tab IS NULL and from_id IS NULL AND deleted = 0',
            'type' => 'Links with null root_tab',
            'target' => 0
        ];

        $roottabs = $conn->execute('SELECT DISTINCT root_tab FROM links WHERE deleted = 0')->fetchAll();

        foreach ($roottabs as $roottab) {
            $roottab = $roottab[0];
            if (empty($roottab)) {
                continue;
            }

            $queries[] = [
                'sql' => 'FROM links WHERE root_tab = "' . $roottab . '" AND root_id IS NULL AND deleted = 0',
                'sql_count' => 'SELECT COUNT(*)',
                'sql_records' => 'SELECT "links", id, root_tab, root_id',
                'type' => 'Links with null root_id (root_tab = ' . $roottab . ')',
                'target' => 0
            ];

            $queries[] = [
                'sql' => 'FROM links WHERE deleted = 0 AND root_tab = "' . $roottab . '" AND root_id NOT IN (SELECT id FROM ' . $roottab . ' WHERE deleted = 0)',
                'sql_count' => 'SELECT COUNT(*)',
                'sql_records' => 'SELECT "links", id, root_tab, root_id',
                'type' => 'Links with invalid root_id (root_tab = ' . $roottab . ')',
                'target' => 0
            ];
        }

        /**
         * links.from_id
         */

        $queries[] = [
            'sql' => 'FROM links WHERE from_tab IS NULL AND deleted = 0',
            'sql_count' => 'SELECT COUNT(*)',
            'sql_records' => 'SELECT "links", id, root_tab, root_id',
            'type' => 'Links with null from_tab',
            'target' => 0
        ];

        $roottabs = $conn->execute('SELECT DISTINCT from_tab FROM links WHERE deleted = 0')->fetchAll();

        foreach ($roottabs as $roottab) {
            $roottab = $roottab[0];
            if (empty($roottab)) {
                continue;
            }

            $queries[] = [
                'sql' => 'FROM links WHERE from_id IS NULL AND from_tab = "' . $roottab . '" AND deleted = 0',
                'sql_count' => 'SELECT COUNT(*)',
                'sql_records' => 'SELECT "links", id, root_tab, root_id',
                'type' => 'Links with null from_id (from_tab = ' . $roottab . ')',
                'target' => 0
            ];

            $queries[] = [
                'sql' => 'FROM links WHERE deleted = 0 AND from_tab = "' . $roottab . '" AND from_id NOT IN (SELECT id FROM ' . $roottab . ' WHERE deleted = 0)',
                'sql_count' => 'SELECT COUNT(*)',
                'sql_records' => 'SELECT "links", id, root_tab, root_id',
                'type' => 'Links with invalid from_id (from_tab = ' . $roottab . ')',
                'target' => 0
            ];
        }

        /**
         * links.from_tagid
         */

        $roottabs = $conn->execute(
            'SELECT DISTINCT from_tab, from_field FROM links WHERE deleted = 0'
        )->fetchAll();

        foreach ($roottabs as $roottab) {
            $linktab = $roottab[0];
            $linkfeld = $roottab[1];
            if (empty($linktab)) {
                continue;
            }

            // Missing tags
            $sql = 'FROM links LEFT JOIN ' . $linktab . ' ON ' . $linktab . '.deleted = 0 AND links.from_id = ' . $linktab .
                '.id AND ' . $linktab . '.' . $linkfeld . ' LIKE CONCAT("%",links.from_tagid,"%") WHERE links.deleted = 0 AND links.from_tab = "' .
                $linktab . '" AND links.from_field = "' . $linkfeld . '" AND ' . $linktab . '.id IS NULL';

            $clear = 'UPDATE links LEFT JOIN ' . $linktab . ' ON ' . $linktab . '.deleted = 0 AND links.from_id = ' . $linktab .
                '.id AND ' . $linktab . '.' . $linkfeld . ' LIKE CONCAT("%",links.from_tagid,"%") ' .
                'SET links.deleted = 3 WHERE links.deleted = 0 AND links.from_tab = "' .
                $linktab . '" AND links.from_field = "' . $linkfeld . '" AND ' . $linktab . '.id IS NULL';

            $queries[] = [
                'sql' => $sql,
                'sql_count' => 'SELECT COUNT(*)',
                'clear' => $clear,
                'sql_records' => 'SELECT "links", links.id, links.root_tab, links.root_id',
                'type' => 'Links with invalid from_tagid (from_tab = ' . $linktab . ', from_field = ' . $linkfeld . ')',
                'target' => 0
            ];

            // Duplicate tag ids
            $where = ' WHERE links.deleted = 0 AND links.from_tab = "' . $linktab . '"'
            . ' AND links.from_field = "' . $linkfeld . '"'
            . ' AND links.id IN '
            .' (SELECT MAX(id) FROM links WHERE deleted=0 GROUP BY from_tagid having COUNT(*) > 1)';

            $sql = 'FROM links INNER JOIN ' . $linktab . ' ON ' . $linktab . '.deleted = 0'
                . ' AND links.from_id = ' . $linktab . '.id'
                . $where;

            $clear = 'UPDATE links INNER JOIN ' . $linktab . ' ON ' . $linktab . '.deleted = 0'
                . ' AND links.from_id = ' . $linktab . '.id'
                . ' SET links.deleted = 3'
                . $where;

            $queries[] = [
                'sql_count' => 'SELECT COUNT(*)',
                'sql_records' => 'SELECT "links", links.id, links.root_tab, links.root_id',
                'sql' => $sql,
                'clear' => $clear,
                'type' => 'Links with duplicate from_tagid (from_tab = ' . $linktab . ', from_field = ' . $linkfeld . ')',
                'target' => 0
            ];

        }

        /**
         * links.to, items.links
         */

        $sources = [
            ['tab' => 'links', 'field' => 'to', 'typefield' =>'from_tagname'],
            ['tab' => 'items', 'field' => 'links', 'typefield' => 'itemtype'],
        ];

        foreach ($sources as $source) {

            // Get all target tables by type
            $targets = $conn->execute(
                'SELECT DISTINCT ' . $source['typefield'] . ', ' . $source['field'] . '_tab FROM '
                . $source['tab'] . ' WHERE deleted = 0'
            )->fetchAll();

            foreach ($targets as $target) {

                $linkType = $target[0];
                $linktab = $target[1];

                if (empty($linktab)) {
                    continue;
                }

                // Empty targets
                $sql = 'FROM ' . $source['tab'] . ' AS fromtab '
                    .'WHERE ' . 'fromtab.deleted = 0 AND  fromtab.' . $source['field'] .  '_tab = "' . $linktab . '" '
                    . 'AND fromtab.' . $source['field'] . '_id IS NULL '
                    . 'AND ' . $source['typefield'] . ' = "' . $linkType .'" ';

                $queries[] = [
                    'sql' => $sql,
                    'sql_count' => 'SELECT COUNT(*)',
                    'sql_records' => 'SELECT "' . $source['tab'] . '", fromtab.id, NULL,NULL',
                    'type' => 'Links (' . $source['tab'] . ' of type ' . $linkType . ') with empty ' . $source['field'] . '_id (' . $source['field'] . '_tab = ' . $linktab . ')',
                    'target' => 0
                ];

                // Invalid targets
                $sql = 'FROM ' . $source['tab'] . ' AS fromtab LEFT JOIN ' . $linktab . ' ON ' . $linktab . '.deleted = 0 AND ' .
                    'fromtab.' . $source['field'] . '_id = ' . $linktab . '.id '
                    .'WHERE ' . 'fromtab.deleted = 0 AND  fromtab.' . $source['field'] .  '_tab = "' . $linktab . '" '
                    . 'AND fromtab.' . $source['field'] . '_id IS NOT NULL '
                    . 'AND fromtab.' . $source['typefield'] . ' = "' . $linkType .'" '
                    . 'AND ' . $linktab . '.id IS NULL';

                $queries[] = [
                    'sql' => $sql,
                    'sql_count' => 'SELECT COUNT(*)',
                    'sql_records' => 'SELECT "' . $source['tab'] . '", fromtab.id, fromtab.' . $source['field'] . '_tab, fromtab.' . $source['field'] . '_id',
                    'type' => 'Links (' . $source['tab'] . ' of type ' . $linkType . ') with invalid ' . $source['field'] . '_id (' . $source['field'] . '_tab = ' . $linktab . ')',
                    'target' => 0
                ];
            }
        }

        /**
         * Numbers
         */

        $keys = array_map(
            function ($x) {
                return (md5($x['sql']));
            },
            $queries
        );
        $vals = array_map(
            function ($x, $y) {
                $x['no'] = $y;
                return ($x);
            },
            $queries,
            range(1, count($queries))
        );
        $queries = array_combine($keys, $vals);
        return ($queries);
    }

    /**
     * Get orphaned records
     *
     * @return array
     */
    public function getOrphans()
    {
        // Execute queries
        $conn = ConnectionManager::get('projects');
        $queries = $this->getHealthQueries();

        $counts = [];
        foreach ($queries as $id => $query) {

            try {
                $sql = $query['sql_count'] . ' ' . $query['sql'];

                $result = $conn->execute($sql)->fetch();
                $query['number'] = $result[0];
                $counts[$id] = $query;

            } catch (Exception $e) {
                $query['number'] = $e->getMessage();
                $counts[$id] = $query;
            }
        }

        $databanksModel = TableRegistry::getTableLocator()->get('Databanks');
        $databank = $databanksModel->findByName($conn->config()['database'])->first();

        $itemTypes = $databank->types['items'] ?? [];
        $linkTypes = $databank->types['links'] ?? [];

        // Get item types with non-required properties
        $ignorePropertyIds = array_filter($itemTypes, fn($itemType) => ($itemType['config']['fields']['property']['required'] ?? true) === false);
        $ignorePropertyIds = array_map(
            fn($itemType) => 'Null items to properties by properties id (itemtype=' . $itemType . ')',
            array_keys($ignorePropertyIds)
        );
        $ignorePropertyIds[] = 'Null items to properties by properties_id';

        // Get links with non-required targets
        $ignoreEmptyLinks = array_filter($linkTypes, fn($linkType) =>
            !isset($linkType['config']['fields']['to']) ||
            (($linkType['config']['fields']['to']['required'] ?? true) === false)
        );
        $ignoreEmptyLinks = array_map(
            fn($linkType) => 'Links (links of type ' . $linkType . ') with empty to_id (to_tab = properties)',
            array_keys($ignoreEmptyLinks)
        );

        // Merge ignored statements
        $ignore = array_merge($ignorePropertyIds, $ignoreEmptyLinks);


        $counts = array_map(
            function ($x) use ($ignore) {
                if (isset($x['target'])) {
                    if ($x['target'] == $x['number']) {
                        $x['status'] = 'ok';
                    }
                    else {
                        if (in_array($x['type'], $ignore)) {
                            $x['status'] = 'warn';
                        }
                        else {
                            $x['status'] = 'notok';
                        }
                    }
                }
                else {
                    $x['status'] = '';
                    $x['target'] = '';
                }
                return ($x);
            },
            $counts
        );

        return $counts;
    }

    /**
     * Find records
     *
     * @param $query
     *
     * @return false
     */
    public function findHealthRecords($query)
    {
        $orphans = [];
        $conn = ConnectionManager::get('projects');
        if (!empty($query['sql_records'])) {
            $sql = $query['sql_records'] . ' ' . $query['sql'] . ' LIMIT 100';
            $orphans = $conn->execute($sql)->fetchAll();
        }

        return $orphans;
    }

    /**
     * Clear orphans
     *
     * @param $queryid
     *
     * @return array
     */
    public function clearOrphans($queryid = false)
    {
        $conn = ConnectionManager::get('projects');
        $queries = $this->getHealthQueries();

        $counts = [];
        foreach ($queries as $id => $query) {
            if (!empty($query['clear']) & (empty($queryid) | ($id == $queryid))) {
                $result = $conn->execute($query['clear']);
                $query['deleted'] = $result;
                $counts[$id] = $query;
            }
        }
        return $counts;
    }

}
