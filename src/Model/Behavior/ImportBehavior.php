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
use App\Utilities\Converters\Arrays;
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

    /**
     * Keep track of errors
     *
     * @var array
     */
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
        $this->_config['implementedMethods']['solveLinks'] = 'solveLinks';

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
     * ### Options
     * - job_id: The job ID
     * - skipUpdates: Array of table names to skip (create new records, skip updating existing ones)
     *
     * @param array $data
     * @param array $index Pass the index by reference
     * @param array $options
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
                $this->solveIris($tableName, $rows, $options);
            } catch (Exception $e) {
                $msg = __(
                    'Could not find IRIs for records linked in table {table}: {error}.',
                    ['table' => $tableName, 'error' => $e->getMessage()]
                );
                $this->addError($msg, ['table' => $tableName], $e);
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

                    if (!$entity->isNew() && (in_array($tableName, $options['skipUpdates'] ?? []))) {
                        continue;
                    }

                    if (!$entity->isNew() && ($entity->_import_action === 'link')) {
                        continue;
                    }

                    if (empty($entity->fieldsImport)) {
                        continue;
                    }

                    // Undelete
                    $entity['deleted'] = $row['deleted'] ?? 0;

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
     * ### Options
     * - skipUpdates Array of table names. Records from those tables will not be revived if deleted.
     *
     * @param array $scopedIris Array of scoped IRIs. If the table has a type-field (e.g. articletype),
     *                    a scoped IRI matches the following schema <type>/<norm_iri>. Otherwise
     *                    the scoped IRI matches <norm_iri>.
     * @param array $options
     * @return array  Array of table IDs indexed by scoped IRIs
     */
    public function collectIris($scopedIris, $options = [])
    {

        /** @var BaseTable $model */
        $model = $this->table();
        if (count($scopedIris) && $model->hasField('norm_iri')) {

            // Revitalise deleted records, but take undeleted if possible, by descendant ordering.
            // The list finder will use the last value in the list.
            // Records that will be skipped should not be used for IRI lookup, as they will not be revived.
            $deleted = 0;
            if (!in_array($model->getTable(), $options['skipUpdates'] ?? [])) {
                $deleted = [0,1];
            }

            $query = $model
                ->find('list', [
                    'keyField' => 'scoped_iri',
                    'valueField' => 'id',
                    'deleted' => $deleted
                ])
                ->order(['deleted' => 'desc']);


            $typeField = $model->typeField ?? null;
            if ($typeField !== null) {
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

        return [];
    }

    /**
     * Stores an index of imported IDs and database IDs that need to be linked.
     *
     * @param array $entities Array of entities that were imported and saved.
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
     * ### Options
     * - skipUpdates Array of table names. Records from those tables will not be revived.
     *              Thus, IRIs are not searched in the deleted records.
     *
     * @param string $tableName
     * @param array $rows
     * @param array $options
     * @return void
     */
    protected function solveIris($tableName, $rows, $options = [])
    {
        // Get potential IRI fields -> all id fields
        $model = $this->table()->getModel($tableName, 'Epi');
        // TODO: move to BaseTable class
        $entityClass = $model ? $model->getEntityClass() : null;

        if ($entityClass) {
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
            $ids = $model->collectIris($scopedIris, $options);

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
     * @param array $index
     * @param bool $fast Use saveManyFast() instead of saveMany()
     * @return bool
     */
    public function solveLinks(&$index, $fast = true)
    {
        $result = true;
        while ($links = $this->getSolvedIds($index)) {
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
                    if ($fast) {
                        $result = $result && $sourceModel->saveManyFast($rows);
                    }
                    else {
                        // TODO: add errors to the entity
                        $result = $result && $sourceModel->saveMany($rows);
                    }
                } catch (Exception $e) {
                    $msg = __(
                        'Error linking records in model {model}: {error}.',
                        ['model' => $modelName, 'error' => $e->getMessage()]
                    );
                    $this->addError($msg, ['model' => $modelName], $e);
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * Check dependencies: only can solve the parent_id if the scope fields have been solved
     *
     * @param array $index
     * @param array $sourceLink
     * @param array $targetLink
     * @return bool
     */
    public static function canSolve($index, $sourceLink, $targetLink)
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
        foreach ($index['sources'] as $source) {
            foreach ($source as $record) {
                $unsolvedRows[] = $record['model'] . '-' . $record['id'];
                $unsolvedFields[] = $record['model'] . '-' . $record['id'] . '-' . $record['field'];
            }
        }

        $targetRow = $targetLink['model'] . '-' . $targetLink['id'];
        if (in_array($targetRow, $unsolvedRows)) {
            return false;
        }

        $sourceField = $sourceLink['model'] . '-' . $sourceLink['id'] . '-' . $sourceLink['scope_field'];
        if (in_array($sourceField, $unsolvedFields)) {
            return false;
        }

        return true;
    }

    /**
     * Get an array of sources and their respective targets that are solved.
     * Solved sources are removed from the index.
     *
     * @return array Each item in the array has a source and a target key
     *               containing the item from the index
     */
    protected function getSolvedIds(&$index)
    {
        $links = [];

        $solvedTargets = array_intersect_key($index['targets'] ?? [], $index['sources'] ?? []);

        foreach ($solvedTargets as $targetId => $targetLink) {

            $solvedSources = $index['sources'][$targetId];
            foreach ($solvedSources as $sourceNo => $sourceLink) {

                if (self::canSolve($index, $sourceLink, $targetLink)) {
                    $links[] = [
                        'source' => $sourceLink,
                        'target' => $targetLink
                    ];

                    unset($index['sources'][$targetId][$sourceNo]);
                }
            }
            if (empty($index['sources'][$targetId])) {
                unset($index['sources'][$targetId]);
            }
        }
        return $links;
    }


    /**
     * Save the entities to the database
     *
     * 1. Clears entities if the action-field equals "clear".
     * 2. Saves table by table
     * 3. Resolves links
     *
     * ### Import options
     * - tree (default true): Whether to recover trees after finishing the save operation.
     * - versions (default false): Whether to create versions of the entities.
     * - timestamps (default true): Whether to set the created and modified timestamps.
     * - job_id (options): The job ID added to the entities.
     * - skipUpdates: Array of table names to skip (create new records, skip updating existing ones)
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

        $tableOrder = ['users', 'properties', 'projects', 'articles', 'sections', 'items', 'footnotes', 'links', 'files'];
        $tables = Arrays::array_group($entities, 'import_table', false, $tableOrder);

        foreach ($tables as $tableName => $entities) {
            try {
                $connection->begin();
                $model = $this->table()->getModel($tableName, 'Epi');

                $result = $result && $model->clearEntities($entities);

                $entities = array_filter($entities, function ($x) {
                    return $x->_import_action !== 'skip';
                });

                if (!empty($entities)) {
                    $result = $result && $model->saveManyFast($entities, $config);
                }

                // Update links between entities
                $this->collectIds($entities);
                $result = $result && $this->solveLinks($this->_index);

                try {
                    $connection->commit();
                } catch (NestedTransactionRollbackException $e) {
                    $msg = __(
                        'Error in commit for table {table}: {error}.',
                        ['table' => $tableName, 'error' => $e->getMessage()]
                    );
                    $this->addError(
                        $msg, [
                            'table' => $tableName,
                            'ids' => implode(' ', array_column($entities, 'id'))
                        ],
                        $e
                    );
                }
            } catch (Exception $e) {
                $connection->rollback();

                $msg = __(
                    'Error importing records into table {table}: {error}.',
                    ['table' => $tableName, 'error' => $e->getMessage()]
                );

                $this->addError(
                    $msg,
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
        $options = ['checkExisting' => false, 'checkRules' => false];
        $result = $model->saveMany($entities, $options);

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

    /**
     * Get solved IDs
     *
     * @return array An array of table prefixed IDs indexed by source IDs (IRIs, temporary IDs etc.)
     */
    public function getSolved()
    {
       $result = [];
       $modelClasses = array_flip($this->tableClasses);
       foreach ($this->_index['targets'] ?? [] as $key => $value) {
           $valueModel = $modelClasses[$value['model'] ?? ''] ?? $value['model'];
           $value = $valueModel . '-' . ($value['id'] ?? '');
           $result[$key] = $value;
       }
       return $result;
    }

    /** Add an import error
     *
     * @param string $msg
     * @param mixed $data
     * @param Exception $exception
     */
    protected function addError($msg, $data, $exception)
    {
        $this->_errors[] = [
            'message' => $msg,
            'data' => $data,
            'exception' => $exception
        ];
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

}
