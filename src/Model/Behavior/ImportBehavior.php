<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Behavior;

use App\Model\Entity\BaseEntity;
use Cake\Database\Connection;
use Cake\Database\Exception\NestedTransactionRollbackException;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Epi\Model\Table\BaseTable;
use Exception;

/**
 * Import behavior
 *
 * Patch data by matching IRIs and IDs
 *
 */
class ImportBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
    ];

    protected $_errors = [];

    /**
     * List of table classes
     *
     * @var array|string[]
     */
    protected array $tableClasses = [
        'users' => 'Epi.Users',
        'projects' => 'Epi.Projects',
        'articles' => 'Epi.Articles',
        'sections' => 'Epi.Sections',
        'items' => 'Epi.Items',
        'links' => 'Epi.Links',
        'footnotes' => 'Epi.Footnotes',
        'properties' => 'Epi.Properties'
    ];

    /**
     * ID cache for solving IRIs
     *
     * //TODO: implement Index class
     *
     * @var array
     */
    protected array $index = ['sources' => [], 'targets' => []];

    //TODO: refactor _index
    protected array $_index = [];


    /**
     * Initialize hook
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->_config['implementedMethods']['toEntities'] = 'toEntities';
        $this->_config['implementedMethods']['saveEntities'] = 'saveEntities';
        $this->_config['implementedMethods']['collectIris'] = 'collectIris';
        $this->_config['implementedMethods']['saveManyFast'] = 'saveManyFast';

        parent::initialize($config);
    }

    /**
     * Convert array to entities
     *
     * The entity type is determined from the id, the field `table` or the job option `table`.
     * IDs are resolved:
     * 1. Qualified IRIs in all ID fields and in fields referencing records are looked up in the database.
     *    IRI-Fragments in the norm_iri-field are resolved to Qualified IRIs.
     *    Qualified IRIs follow the scheme "<table>/<type>/<id>".
     * 2. Database IDs follow the scheme "<table>-<id>" where <id> has to be a numeric value.
     * 3. Temporary IDs follow the scheme "<table>-tmp<id>" where <id> an be an alphanumeric value ([a-zA-Z0-9_-]+)
     *
     * @param array $data
     * @param array $index Pass the index by reference
     * @param array $options Add the job_id key
     *
     * @return array
     */
    public function toEntities($data, &$index = null, $options = [])
    {
        if (!empty($index)) {
            $this->index = &$index;
        }

        // Init entity array
        $entities = [];

        // Inject default table, type and iri fragment if missing
        $defaultmodel = $this->table();
        $defaulttable = $defaultmodel->getTable();

        $data = array_map(function ($row) use ($defaulttable) {
            // IRI path
            $iri = explode('/', $row['id'] ?? '');
            if (sizeof($iri) === 3) {
                $row['table'] = $iri[0];
                $row['type'] = $iri[1];
                $row['norm_iri'] = $iri[2];
            }

            // Table
            else {
                $table = (explode('-', str_replace('/', '-', $row['id'] ?? ''))[0]) ?? '';
                $table = empty($this->tableClasses[$table]) ? null : $table;
                $row['table'] = $table ?? $row['table'] ?? $row['table_name'] ?? $defaulttable;
            }

            return $row;
        }, $data);

        // Remove rows with missing table
        $data = array_filter($data, function ($row) {
            return !empty($row['table']);
        });

        // Group by tables
        $tables = collection($data)->groupBy('table');
        foreach ($tables as $tableName => $rows) {

            // Merge duplicates
            $rows = array_reduce($rows, function ($carry, $item) {
                if (isset($item['id'])) {
                    $carry[$item['id']] = array_merge($carry[$item['id']] ?? [], $item);
                }
                else {
                    $carry[] = $item;
                }
                return ($carry);
            }, []);

            // Solve IRIs (add them to the index)
            try {
                $this->solveIris($tableName, $rows);
            } catch (Exception $e) {
                $this->addError(
                    'Error findings IRIs for records linked in table {table}: {error}.',
                    ['table' => $tableName],
                    $e
                );
            }

            // Map job type to Entity
            $modelName = $defaultmodel->getModelName($tableName, 'Epi');
            $model = $defaultmodel->getModel($tableName, 'Epi');
            $entityClass = $model ? $model->getEntityClass() : null;
            $typeField = $model ? $model->typeField : null;

            if ($entityClass) {

                foreach ($rows as $idx => $row) {

                    if (!empty($row['id'])) {
                        $this->_index['imported'][] = $row['id'];
                    }

                    $importOptions = [
                        'source' => $modelName,
                        'import' => true,
                        'job_id' => $options['job_id'] ?? null,
                        'table_name' => $tableName,
                        'table_row' => $row['#'] ?? null,
                        'type_field' => $typeField,
                        'action' => $row['_action'] ?? null,
                        'fields' => isset($row['_fields']) ? array_map('trim', explode(',', $row['_fields'])) : null,
                        'index' => &$this->_index
                    ];

                    $entity = new $entityClass($row, $importOptions);

                    if (!$entity->isNew() && (in_array($tableName, $this->getConfig('skip', [])))) {
                        continue;
                    }

                    if (empty($entity->fieldsImport)) {
                        continue;
                    }

                    $entities[] = $entity;
                }
            }
        }

        // Solve links in current batch
        if (isset($this->_index['targets'])) {
            foreach ($entities as $entity) {
                $entity->solveIds($entity, $this->_index['targets']);
            }
        }

        // Sort by row number
        usort($entities, fn($a, $b) => (($a->row_number ?? 0) <=> ($b->row_number ?? 0)));

        return $entities;
    }

    /**
     * Lookup the ID of IRIs
     *
     * //TODO: refactor as finder
     *
     * @param array $iris Array of scoped IRIs. If the table has a type-field (e.g. articletype),
     *                    a scoped IRI matches the following schema <type>/<norm_iri>. Otherwise
     *                    the scoped IRI matches <norm_iri>.
     * @return array  Array of table IDs indexed by scoped IRIs
     */
    public function collectIris($scopedIris)
    {

        /** @var BaseTable $model */
        $model = $this->table();
        if (count($scopedIris) && $model->hasField('norm_iri')) {

            $query = $model
                ->find('list', [
                    'keyField' => 'scoped_iri',
                    'valueField' => 'id'
                ])
                ->where(['deleted' => 0]);


            $typeField = $model->typeField ?? null;
            if ($typeField !== null) {
//                $iriExpression = $query->func()->concat([
//                    $typeField => 'identifier',
//                    '/',
//                    'norm_iri' => 'identifier'
//                ]);

                $iriString = 'CONCAT(' . $typeField . ', "/", norm_iri)';
                $query = $query
                    ->select([
                        'id',
                        'scoped_iri' => $iriString
                    ])
                    ->where([$iriString . ' IN' => $scopedIris], [$iriString => 'string[]']);
            }
            else {
                $query = $query
                    ->select(['id', 'scoped_iri' => 'norm_iri'])
                    ->where(['norm_iri IN' => $scopedIris]);
            }

            return $query->toArray();
        }
        else {
            return [];
        }
    }

    /**
     * Collect IDs from imported and saved records
     *
     * Stores an index of imported IDs and database IDs that need to be linked.
     *
     * @param array $entities Array of entities that were imported and saved.
     *
     * @return void
     */
    protected function collectIds(array $entities)
    {
        foreach ($entities as $entity) {
            $entity->indexIds($this->_index);
        }
    }

    /**
     * Lookup all IRIs in the rows and add them to the index
     *
     * The IRIs must match the following scheme: <table>/<type>/<norm_iri>
     *
     * @param string $tableName
     * @param array $rows
     * @return void
     */
    protected function solveIris($tableName, $rows)
    {
        // Get potential IRI fields -> all id fields
        $model = $this->table()->getModel($tableName, 'Epi');
        // TODO: move to BaseTable class
        $entityClass = $model ? $model->getEntityClass() : null;

        if ($entityClass) {
            //$emptyEntity = new $entityClass();
            $idFields = $entityClass::getIdFields();
            $typeField = $model->typeField ?? null;
        }
        else {
            return;
        }

        // Reduce to IRIs, include iri path
        $iris = array_map(
            function ($row) use ($idFields, $tableName, $typeField) {
                $iris = array_filter(array_intersect_key($row, array_flip($idFields)));
                $iris = array_filter($iris, fn($id) => preg_match('/^[a-z]+\/([a-z0-9_-]+\/)?[a-z0-9_~-]+$/', $id));
                $iris = array_values($iris);

                // Add iri path
                if (($row['norm_iri'] ?? '') !== '') {
                    $norm_iri = $row['norm_iri'];
                }
                elseif (($row['iri'] ?? '') !== '') {
                    $norm_iri = $row['iri'];
                }
                else {
                    $norm_iri = null;
                }

                if ($norm_iri !== null) {
                    $typeName = ($typeField !== null) ? ($row[$typeField] ?? $row['type'] ?? null) : null;
                    $qualifiedIri = implode('/', array_filter([$tableName, $typeName, $norm_iri]));
                    $iris[] = $qualifiedIri;
                }

                return $iris;
            },
            $rows
        );

        $iris = array_reduce($iris, fn($carry, $item) => array_merge($carry, $item), []);
        $iris = array_unique($iris);
        $iris = array_diff($iris, array_keys($this->_index['targets'] ?? []));
        $iris = array_map(fn($iri) => explode('/', $iri, 2), $iris);

        // Group by table
        $iriTables = [];
        foreach ($iris as $iri) {
            $iriTables[$iri[0]][] = $iri[1];
        }

        // Lookup iris and add to index
        foreach ($iriTables as $iriTable => $scopedIris) {
            $model = $this->table()->getModel($iriTable, 'Epi');
            if (!$model->hasBehavior('Import')) {
                throw new Exception('The import behavior is not attached to the model.');
            }
            $ids = $model->collectIris($scopedIris);

            foreach ($ids as $scopedIri => $id) {
                $qualifiedIri = $iriTable . '/' . $scopedIri;
                $this->_index['targets'][$qualifiedIri] = [
                    'model' => $iriTable,
                    'id' => (int)$id
                ];
            }
        }
    }


    /**
     * Link records
     *
     * Updates foreign key fields with the respective IDs as soon as
     * a matching record occurs in the index.
     *
     * @return bool
     */
    protected function solveLinks()
    {
        $result = true;
        while ($links = $this->getSolvedIds()) {

            $jobId = $this->getConfig('job_id', null);
            $sourceModels = collection($links)->groupBy('source.model');
            foreach ($sourceModels as $modelName => $sourceLinks) {
                try {
                    $sourceModel = TableRegistry::getTableLocator()->get($modelName);
                    $sourceIds = collection($sourceLinks)->groupBy('source.id')->toArray();

                    $rows = $sourceModel->find('all')
                        ->where(['id IN' => array_keys($sourceIds)])
                        ->formatResults(function ($results) use ($sourceIds, $jobId) {
                            return $results->map(function ($row) use ($sourceIds, $jobId) {
                                foreach ($sourceIds[$row->id] as $sourceLink) {
                                    $row[$sourceLink['source']['field']] = $sourceLink['target']['id'];
                                    $row['job_id'] = $jobId;
                                }
                                return $row;
                            });
                        });
                    $result = $result && $sourceModel->saveManyFast($rows);
                } catch (Exception $e) {
                    $this->addError(
                        'Error linking records in model {model}: {error}.',
                        ['model' => $modelName],
                        $e
                    );
                    $result = false;
                }
            }
        }

        return $result;
    }


    /**
     * Check dependencies: only can solve the parent_id if the scope fields have been solved
     *
     * @param $sourceLink
     * @param $targetLink
     *
     * @return bool
     */
    protected function canSolve($sourceLink, $targetLink)
    {

        if (empty($sourceLink['scope_field'])) {
            return true;
        }

        if ($sourceLink['field'] != 'parent_id') {
            return true;
        }

        // Parent rows must be processed first
        $unsolvedRows = [];
        $unsolvedFields = [];
        foreach ($this->_index['sources'] as $source) {
            foreach ($source as $record) {
                $unsolvedRows[] = $record['model'] . '-' . $record['id'];
                $unsolvedFields[] = $record['model'] . '-' . $record['id'] . '-' . $record['field'];
            }
        }

        $targetRow = $targetLink['model'] . '-' . $targetLink['id'];
        if (in_array($targetRow, $unsolvedRows)) {
            return false;
        }

        // Scope of child records must be filled first
        $sourceField = $sourceLink['model'] . '-' . $sourceLink['id'] . '-' . $sourceLink['scope_field'];
        if (in_array($sourceField, $unsolvedFields)) {
            return false;
        }

        return true;
    }

    /**
     * Get an array of sources and their respective targets that are solved
     *
     * Solved sources are removed from the index.
     *
     * @return array Each item in the result array has a source and a target key containing the item from the index.
     */
    protected function getSolvedIds()
    {
        $links = [];

        $solvedTargets = array_intersect_key($this->_index['targets'] ?? [], $this->_index['sources'] ?? []);

        foreach ($solvedTargets as $targetId => $targetLink) {
            $solvedSources = $this->_index['sources'][$targetId];
            foreach ($solvedSources as $sourceNo => $sourceLink) {

                if ($this->canSolve($sourceLink, $targetLink)) {
                    $links[] = [
                        'source' => $sourceLink,
                        'target' => $targetLink
                    ];

                    unset($this->_index['sources'][$targetId][$sourceNo]);
                }
            }
            if (empty($this->_index['sources'][$targetId])) {
                unset($this->_index['sources'][$targetId]);
            }
        }
        return $links;
    }

    /**
     * Save the entities to the database.
     *
     * 1. Clears entities if the action-field equals "clear".
     * 2. Saves table by table
     * 3. Resolves links
     *
     * @param array $entities
     * @param array $config Import options
     * @param array $index Pass an index array by reference
     * @return bool
     */
    public function saveEntities($entities, $config = [], &$index = null)
    {
        if (!empty($config)) {
            $this->setConfig($config);
        }

        if (!empty($index)) {
            $this->index = &$index;
        }

        /** @var Connection $connection */
        $connection = $this->table()->getConnection();

        $result = true;
        $tables = collection($entities)->groupBy('import_table');
        foreach ($tables as $tableName => $entities) {
            try {
                $connection->begin();

                $result = $result && $this->table()->getModel($tableName, 'Epi')->clearEntities($entities);

                $entities = array_filter($entities, function ($x) {
                    return $x->_import_action !== 'skip';
                });

                if (!empty($entities)) {
                    $model = $this->table()->getModel($tableName, 'Epi');
                    $result = $result && $model->saveManyFast($entities, $config);
                }

                // Update links between entities
                $this->collectIds($entities);
                $result = $result && $this->solveLinks();

                try {
                    $connection->commit();
                } catch (NestedTransactionRollbackException $e) {

                }
            } catch (Exception $e) {
                $connection->rollback();
                $this->addError(
                    'Error importing records into table {table}: {error}.',
                    [
                        'table' => $tableName,
                        'ids' => implode(' ', array_column($entities, 'id'))
                    ],
                    $e
                );
            }
        }

        return $result;
    }

    /**
     * Save the entities
     *
     * Disables the tree behavior and recovers the tree afterwards.
     *
     * @param BaseEntity[] $entities
     * @param array $config Configuration for the Import behavior
     *
     * @return mixed
     */
    public function saveManyFast($entities, $config = [])
    {
        if (!empty($config)) {
            $this->setConfig($config);
        }

        $model = $this->table();

        // Disable behaviors
        if ($model->hasBehavior('VersionedTree')) {
            $model->disableTreeBehavior();
        }

        $versioning = $this->getConfig('versions', false);
        if (!$versioning && $model->hasBehavior('Version')) {
            $model->disableVersionBehavior();
        }

        $timestamps = $this->getConfig('timestamps', false);
        $hadTimestamps = $model->hasBehavior('Timestamp');
        if (!$timestamps && $hadTimestamps) {
            $model->removeBehavior('Timestamp');
        }

        if ($model->hasBehavior('XmlStyles')) {
            $model->disableRendering();
        }

        // Save
        $result = $model->saveMany($entities);

        // Enable behaviors
        if (!$versioning && $model->hasBehavior('Version')) {
            $model->enableVersionBehavior();
        }

        if ($model->hasBehavior('VersionedTree')) {
            $recover = $this->getConfig('tree', false);
            $model->enableTreeBehavior($recover);
        }
        if (!$timestamps && $hadTimestamps) {
            $model->addBehavior('Timestamp');
        }
        if ($model->hasBehavior('XmlStyles')) {
            $model->enableRendering();
        }

        return $result;
    }

    protected function addError($msg, $data, $exception)
    {
        $this->_errors[] = [
            'message' => $msg,
            'data' => $data,
            'exception' => $exception
        ];
    }

    public function getErrors()
    {
        return $this->_errors;
    }

}
