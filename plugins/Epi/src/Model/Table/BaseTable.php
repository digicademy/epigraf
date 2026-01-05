<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Table;

use App\Model\Behavior\ImportBehavior;
use App\Model\Entity\InvalidTaskException;
use App\Model\Entity\Jobs\JobMutate;
use App\Model\Interfaces\MutateTableInterface;
use App\Utilities\Converters\Objects;
use ArrayObject;
use Cake\Collection\CollectionInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query;
use Cake\Utility\Inflector;
use Epi\Model\Behavior\XmlStylesBehavior;
use Epi\Model\Entity\BaseEntity;
use Exception;

/**
 * Base table
 *
 * # Behaviors
 * @mixin XmlStylesBehavior //only for some child classes (articles, items...)
 * @mixin ImportBehavior
 */
class BaseTable extends \App\Model\Table\BaseTable implements MutateTableInterface
{
    /**
     * Default database connection
     *
     * @var string
     */
    public static $defaultConnection = 'projects';

    /**
     * ID of the user in the project database
     *
     * @var null|integer
     */
    public static $databaseUserId = null;

    /**
     * Overwrite in subclasses. Necessary for IRI handling and tree data.
     * Type field: name of the field containing the article type, section type etc. Used as prefix for IRIs.
     */

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = null;

    /**
     * Plugin prefix for the model name
     *
     * @var string
     */
    protected $_pluginName = 'epi';

    /**
     * Initialize hook
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Import');
        $this->behaviors()->get('Modifier')->setConfig(['user' => 'databaseUserId']);
    }

    /**
     * beforeMarshal callback
     *
     * @param EventInterface $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options)
    {
        /** @var $entityClass BaseEntity */
        $entityClass = $this->getEntityClass();
        foreach ($entityClass::getIdFields() as $fieldName) {
            if (!preg_match('/^[0-9]*$/', $data[$fieldName] ?? '')) {
                $data['_import_ids'][$fieldName] = $data[$fieldName];
                unset($data[$fieldName]);
            }
        }

        // Save temporary IDs
        // Because the marshaller tries to detect potential existing entities by ID,
        // the query would fail if the ID is not numeric. Therefore we save the ID in a
        // temporary property and remove it from the data array.
        // TODO: maybe implement as behavior or as a custom marshaller
        foreach ($entityClass::getIdTables() as $childKey => $childClass) {
            if (!empty($data[$childKey])) {
                foreach ($data[$childKey] as $key => $section) {
                    foreach ($childClass::getIdFields() as $fieldName) {
                        if (!preg_match('/^[0-9]*$/', $section[$fieldName] ?? '')) {
                            $data[$childKey][$key]['_import_ids'][$fieldName] = $section[$fieldName];
                            unset($data[$childKey][$key][$fieldName]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Transfer temporary IDs to the _import_ids property, so it can be handled in saveLinks()
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $data
     * @param ArrayObject $options
     */
    public function afterMarshal(
        EventInterface $event,
        EntityInterface $entity,
        ArrayObject $data,
        ArrayObject $options
    ) {
        $importIds = $data['_import_ids'] ?? [];
        if (!empty($importIds)) {
            $entity->_import_ids = $importIds;
        }
        parent::afterMarshal($event, $entity, $data, $options);
    }

    /**
     * Save an article in two steps: entity data and references between entities.
     *
     * Wraps the full saving operation in a transaction.
     *
     * @param EntityInterface $entity
     * @param array $options
     * @return bool Whether the save operation was successful
     */
    public function saveWithLinks(EntityInterface $entity, $options = [])
    {
        $connection = $this->getConnection();
        $connection->begin();
        try {
            // Save article data
            $hasSaved = $this->save($entity, $options);

            // Save link data
            if ($hasSaved && ($entity instanceof \Epi\Model\Entity\BaseEntity)) {
                $hasSaved = $entity->saveLinks();
            }

            if ($hasSaved) {
                $connection->commit();
            }
            else {
                $connection->rollback();
            }
        } catch (Exception $e) {
            $connection->rollback();
            $entity->setError('id', $e->getMessage());
            $hasSaved = false;
        }
        return $hasSaved;
    }


    /**
     * Validation method that checks if a value is a valid JSON string or already an array
     *
     * @param string|array $value The value to check
     * @param array $context
     * @return bool
     */
    public function isJson($value, array $context): bool
    {
        if (is_array($value)) {
            return true;
        }
        try {
            json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Create an array of new entities from the source entities
     * based on the IRI
     *
     * @param array $sources
     */
    public function cloneEntities($sources, $fields = [])
    {
        return collection($sources)->map(function ($value) use ($fields) {
            if (is_object($value) &&
                method_exists($value, 'toArray') &&
                is_callable([$value, 'toArray'])) {
                $value = $value->toArray();
            }

            return $this->cloneEntity($value, $fields);
        });
    }

    /**
     * Create a new entity from the source array
     * and set the IRI
     *
     * @param array $source An array with data from the source entity
     * @param array $fields Fields to be copied
     * @return EntityInterface|false
     *      *
     */
    public function cloneEntity($source, $fields = [])
    {
        // Find existing node by IRI and scope field...
        // TODO: find for multiple entities at once
        if (!empty($source['norm_iri'])) {
            $target = $this->find('all')
                ->where([
                    'norm_iri' => $source['norm_iri'],
                    $this->scopeField => $source[$this->scopeField]
                ])
                ->first();
        }
        else {
            $target = false;
        }

        // Keep source id
        $source['source_id'] = $source['id'];

        // Patch existing node or create new node
        // todo: match parent_id, related_id, property_id by norm_iri
        $fields = array_merge($fields, ['source_id', 'norm_iri', $this->scopeField]);
        $source = array_intersect_key($source, array_flip($fields));

        if ($target) {
            $target = $this->patchEntity($target, $source, ['accessibleFields' => ['source_id' => true]]);
        }
        else {
            $target = $this->newEntity($source, ['accessibleFields' => ['source_id' => true]]);
        }

        return $target;
    }

    /**
     * Save transferred nodes
     *
     * @param $data
     * @return EntityInterface[]|\Cake\Datasource\ResultSetInterface|false|iterable
     * @throws \Exception
     */
    public function transferNodes($data)
    {

        $nodes = $this->newEntities(
            $data,
            ['accessibleFields' => ['id' => true]]
        );

        $result = $this->saveMany($nodes);
        return $result;
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function setNormIri($entity)
    {
        if (empty($entity->norm_iri)) {
            $entity->norm_iri = $entity->local_iri;
        }

        return $entity;
    }

    /**
     * Update empty norm_iri fields.
     *
     * By default local IRIs will be generated.
     * Set $options['public'] to true if the database is published.
     *
     * @param Query $query
     * @param array $options
     * @return Query
     * @deprecated
     *
     */
    public function findIri(Query $query, array $options)
    {
        return $query
            ->formatResults(
                function (CollectionInterface $results) use (&$query, $options) {
                    return $results->map(
                        function ($row) use (&$options) {
                            if ($options['public'] ?? false) {
                                $row->norm_iri = $row->public_iri;
                            }
                            elseif (empty($row->norm_iri)) {
                                $row->norm_iri = $row->local_iri;
                            }
                            return $row;
                        });
                });
    }

    /**
     * Clear entities (delete section of article, items of section)
     *
     * Only entities containing a property `_import_action` with the value "clear" will be processed.
     * When importing data, the value is passed from the field `_action` in the source data to the `_import_action` field.
     *
     * @param array $entities Array of entities.
     * @return boolean success or failure?
     */
    public function clearEntities($entities)
    {
        foreach ($entities as $entity) {
            if ($entity->_import_action === 'clear') {
                $entity->clear();
            }
        }
        return true;
    }

    /**
     * Get columns to be rendered in table views
     *
     * See BaseTable->getColumns() for further information.
     *
     *  ### Options
     *  - type (string) Filter by type
     *  - join (boolean) Join the columns to the query
     *
     * @param array $selected
     * @param array $default
     * @param array $options
     * @return array
     */
    public function getColumns($selected = [], $default = [], $options = [])
    {
        // Get config from database
        $types = $this->getDatabase()->types[$this->getTable()] ?? [];
        $config = [];
        $type = $options['type'] ?? null;
        foreach ($types as $typeName => $typeData) {
            if (!empty($type) && ($type !== $typeName)) {
                continue;
            }
            $typeConfig = Objects::extract($typeData, 'merged.columns');
            $typeConfig = $this->augmentColumnSetup($typeConfig ?? [], $options['joined'] ?? false);

            foreach ($typeConfig as $typeColumn => $typeColumnConfig) {
                $typeColumnConfig['types'] = [$typeName => $typeColumnConfig] + ($config[$typeColumn]['types'] ?? []);
                $config[$typeColumn] = $typeColumnConfig;
            }
        }

        // Merge with defaults
        $default = $this->augmentColumnSetup($default, $options['joined'] ?? false);
        $config = $this->mergeColumnSetup($config, $default);

        // Filter non-public columns, mark selected columns,
        // add ad-hoc columns, add personalized column widths
        return parent::getColumns($selected, $config, $options);
    }

    /**
     * Get all search configuration for columns
     *
     * @return array Column captions, indexed by column keys prefixed with 'columns.'
     */
    public function getColumnFilters()
    {
        $filters = [];
        $columns = $this->getColumns();
        foreach ($columns as $columnName => $columnConfig) {
            if (!isset($columnConfig['sort'])) {
                continue;
            }

            if (empty($columnConfig['selectable'] ?? true)) {
                continue;
            }

            $isCount = is_array($columnConfig['sort']) && (($columnConfig['sort']['aggregate'] ?? false) === 'count');
            if ($isCount) {
                continue;
            }

            $filters['columns.' . $columnName] = '- ' . $columnConfig['caption'] ?? $columnName;
        }

        if (!empty($filters)) {
            $filters = array_merge(['columns.' => __('Columns')], $filters);
        }
        return $filters;
    }

    /**
     * Find entities by publication state
     *
     * @param \Cake\ORM\Query $query
     * @param array $options
     *
     * @return \Cake\Database\Query
     */
    public function findHasPublicationState(Query $query, array $options)
    {
        // Filter out nonpublic entities for guests
        $minPublished = PUBLICATION_DRAFTED;
        if ($this::$userRole === 'guest') {
            if ((($options['action'] ?? 'index') === 'index') && empty($options['published'])) {
                $minPublished = PUBLICATION_SEARCHABLE;
            }
            else {
                $minPublished = PUBLICATION_PUBLISHED;
            }
        }

        // Filter out non-finished entities for readers
        else if ($this::$userRole === 'reader') {
            $minPublished = PUBLICATION_COMPLETE;
        }

        if (!empty($minPublished)) {
            $query = $query
                ->where([
                    $this->getAlias() . '.published >=' => $minPublished
                ]);
        }

        // Match exact publication status
        if ($options['published'] !== null) {
            $query = $query
                ->where([
                    $this->getAlias() . '.published IN' => $options['published']
                ]);
        }

        return $query;
    }


    /**
     * Get the list of supported tasks
     *
     * @return string[]
     */
    public function mutateGetTasks(): array
    {
        return [];
    }

    /**
     * Called from the mutate job
     *
     * @implements MutateTableInterface
     *
     * @param array $params
     * @param JobMutate $job
     * @return int Number of articles for calculating the progress bar.
     */
    public function mutateGetCount($params, $job): int
    {
        $params = $this->parseRequestParameters($params);

        return $this
            ->find('hasParams', $params)
            ->count();
    }

    /**
     * Mutate all entities matched by the params
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset
     * @param int $limit
     * @return array The mutated entities
     */
    public function mutateEntities($taskParams, $dataParams, $offset, $limit): array
    {
        $tasks = $this->mutateGetTasks();
        if (!isset($tasks[$taskParams['task']])) {
            throw new InvalidTaskException('Task not found');
        }

        $method = 'mutateEntities' . Inflector::camelize($taskParams['task']);
        if (!method_exists($this, $method)) {
            throw new InvalidTaskException('Task not implemented');
        }
        return $this->{$method}($taskParams, $dataParams, ['offset' => $offset, 'limit' => $limit]);
    }
}
