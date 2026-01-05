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

use ArrayObject;
use Cake\Database\StatementInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Association;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use RuntimeException;

/**
 * Version behavior
 *
 * Implements versioning and soft deletion.
 *
 * Add a numeric field deleted to your database table, with default value 0.
 * In any table with a deleted field, records will be soft deleted:
 * - Instead of deleting the record, deleted is set to 1.
 * - When finding records, the condition deleted = 0 is added to queries.
 *
 * Additionally, add a numeric field version_id to your database table, with default value NULL.
 * In any table with deleted and version_id fields, records will be versioned.
 * When saving a record, a copy is created and saved, in the copy:
 * - deleted is set to 2
 * - version_id is set to the ID of the original record
 *
 * Make sure to understand how other behaviors change values
 * for deleted or versioned entities.
 * - If you have the TimestampBehavior or the ModifierBehavior on your table,
 *   they will affect the fields of the versioned or deleted record,
 *   which is usually desired.
 * - Changing the tree properties is usually not desired for versioned records.
 *   See the VersionedTreeBehavior as a replacement for TreeBehavior.
 */
class VersionBehavior extends Behavior
{
    /**
     * Use callbacks
     *
     * @var bool
     */
    protected $_enableVersions = true;

    /**
     * @var bool
     */
    protected $_findDeleted = false;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Initialize hook
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {

    }

    /**
     * Before find method
     *
     * @param EventInterface $event
     * @param Query $query
     * @param ArrayObject $options
     * @param $primary
     *
     * @return void
     */
    public function beforeFind(EventInterface $event, Query $query, ArrayObject $options, $primary)
    {
        if ($this->table()->hasField('deleted')) {
            $deleted = $options['deleted'] ?? 0;
            if (is_array($deleted)) {
                $query->where([$this->table()->getAlias() . '.deleted IN' => $deleted]);
            }
            else {
                $query->where([$this->table()->getAlias() . '.deleted' => (int)$deleted]);
            }
        }
    }

    /**
     * Cascade soft delete
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity)
    {
        if ($entity->version_id) {
            return true;
        }

        if ($this->table()->hasField('deleted') && ($entity->deleted)) {
            $this->cascadeDelete($entity);
        }
    }

    /**
     * Callback to create versions of a record
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     *
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($this->_enableVersions && $this->table()->hasField('version_id') && $this->table()->hasField('deleted')) {

            if (!$this->createVersion($entity, $options->getArrayCopy())) {
                $msg = __('Versioning failed');

                // TODO: implement function
                if ($entity->hasErrors()) {
                    $errors = $entity->getErrors();
                    $errors = Hash::flatten($errors);
                    $errors = Hash::flatten($errors);
                    $errors = array_map(fn($x, $y) => $x . ': ' . $y, array_keys($errors), $errors);
                    $msg .= ' (' . implode('; ', $errors) . ')';
                }

                throw new RuntimeException($msg);
            }
        }
    }

    /**
     * Callback to soft delete a record
     *
     * @param \Cake\Event\Event $event The beforeDelete event that was fired.
     * @param \Cake\Datasource\EntityInterface $entity The entity to be deleted.
     * @param \ArrayObject $options Options.
     *
     * @return true
     * @throws RuntimeException if fails to mark entity as deleted.
     *
     * @see https://github.com/UseMuffin/Trash
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($this->table()->hasField('deleted')) {

            $event->stopPropagation();

            if (!$entity->deleted) {

                if (!$this->softDelete($entity, $options->getArrayCopy())) {
                    throw new RuntimeException("Soft delete failed");
                }

                /** @var \Cake\ORM\Table $table */
                $table = $event->getSubject();
                $table->dispatchEvent('Model.afterDelete', [
                    'entity' => $entity,
                    'options' => $options,
                ]);
            }
        }
        return true;
    }

    /**
     * Save a versioned copy of a record.
     *
     * - The deleted field will be set to 2, it indicates record versions.
     * - The field version_id will contain the ID of the current record.
     * - The modified field will contain the timestamp of the version
     *   (automatically updated by the  TimestampBehavior).
     * Skips already versioned records and deleted records
     *
     * @param \Cake\Datasource\EntityInterface $entity EntityInterface.
     * @param array $options Options.
     *
     * @return bool
     */
    protected function createVersion(EntityInterface $entity, array $options = []): bool
    {
        if ($entity->version_id) {
            return true;
        }
        elseif ($entity->deleted) {
            return true;
        }
        else {
            if ($this->table()->hasBehavior('XmlStyles')) {
                $this->table()->disableRendering();
            }

            $data = $this->extractData($entity);
            $version = $this->table()->newEntity($data, ['associated' => []]);

            // Why?
            $version->root = $entity->root;
            $version->container = $entity->container;
            $version->parent = $entity->parent;

            $version->set(['deleted' => 2], ['guard' => false]);
            $version->set(['version_id' => $entity->id], ['guard' => false]);

            $result = (bool)$this->table()->save($version, ['associated' => false, 'checkRules' => false]); //

            if ($version->hasErrors()) {
                $entity->setErrors($version->getErrors());
            }

            if ($this->table()->hasBehavior('XmlStyles')) {
                $this->table()->enableRendering();
            }

            return $result;
        }
    }

    /**
     * Save a copy of an entity
     *
     * - The modified field will contain the timestamp of the version
     *   (automatically updated by the  TimestampBehavior).
     *
     * @param EntityInterface $entity EntityInterface.
     * @param array $options Options.
     *
     * @return bool|EntityInterface
     */
    public function createCopy(EntityInterface $entity, array $options = [])
    {
        if ($this->table()->hasBehavior('XmlStyles')) {
            $this->table()->disableRendering();
        }


        if ($this->table()->hasBehavior('VersionedTree')) {
            $this->table()->disableTreeBehavior();
        }

        $data = $this->extractData($entity);
        $newEntity = $this->table()->newEntity($data, ['associated' => []]);

        // Why?
        $newEntity->root = $entity->root;
        $newEntity->container = $entity->container;
        $newEntity->parent = $entity->parent;

        $result = (bool)$this->table()->save($newEntity, ['associated' => false, 'checkRules' => false]);

        if ($this->table()->hasBehavior('VersionedTree')) {
            $this->table()->enableTreeBehavior();
        }

        if ($this->table()->hasBehavior('XmlStyles')) {
            $this->table()->enableRendering();
        }

        if (!$result) {
            return false;
        }
        else {
            return $newEntity;
        }
    }

    /**
     * Extract data to be versioned from an entity
     *
     * @param EntityInterface $entity
     * @return array
     */
    protected function extractData($entity)
    {
        // Only get database fields
        // (makes sure associations are not included)
        $databaseFields = $this->table()->getSchema()->columns();

        // Only get visible fields
        // (excludes for example the published field in docs,
        // which cannot be NULL and has a default value in the database)
        $visibleFields = $entity->getVisible();

        $result = [];
        foreach ($databaseFields as $field) {
            if (in_array($field, $visibleFields)) {
                $result[$field] = $entity->get($field);
            }
        }
        unset($result['id']);
        return $result;
    }

    /**
     * Soft delete given entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity EntityInterface.
     * @param array $options Operation options.
     *
     * @return bool
     */
    protected function softDelete(EntityInterface $entity, array $options = []): bool
    {
        $this->cascadeDelete($entity, $options);

        if ($this->table()->hasField('deleted')) {
            $entity->set(['deleted' => 1], ['guard' => false]);
            return (bool)$this->table()->save($entity, $options);
        }
        else {
            return true;
        }
    }


    /**
     * Create a copy of a record and update the version ID field
     *
     * @param array $conditions
     * @return StatementInterface
     */
    public function createVersionCopy($conditions): StatementInterface
    {
        $table = $this->table();
        $tableName = $table->getTable();

        // Get columns
        $columns = $this->table()->getSchema()->columns();
        $columns = array_diff($columns, ['id', 'version_id', 'deleted', 'modified']);
        $driver = $table->getConnection()->getDriver();

        $escapedColumns = array_map(fn($column) => $driver->quoteIdentifier($column), $columns);
        $escapedColumns = implode(', ', $escapedColumns);
        $sqlInsertColumns = $escapedColumns . ", `version_id`, `deleted`";
        $sqlSelectColumns = $escapedColumns . ", `id`, 2";

        // Get conditions
        $query = $table->selectQuery()->where($conditions);
        $sqlConditions = $query->newExpr($conditions)->sql($query->getValueBinder());

        $sqlBindings = $query->getValueBinder()->bindings();
        $sqlParams = array_map(function ($binding) {
            $p = $binding['value'];

            if ($p === null) {
                return 'NULL';
            }
            if (is_bool($p)) {
                return $p ? '1' : '0';
            }

            if (is_string($p)) {
                $replacements = [
                    '$' => '\\$',
                    '\\' => '\\\\\\\\',
                    "'" => "''",
                ];

                $p = strtr($p, $replacements);

                return "'$p'";
            }

            return $p;
        }, $sqlBindings);


        // Execute SQL
        $sql = "INSERT INTO {$tableName} ({$sqlInsertColumns}) SELECT {$sqlSelectColumns} FROM {$tableName} WHERE {$sqlConditions}";
        return $table->getConnection()->execute($sql, $sqlParams);
    }

    /**
     * Create a version copy of all records that match the conditions
     * and then update the values in the original records
     *
     * @param array $values Values to update
     * @param array $conditions Query conditions
     * @return int The number of affected rows
     */
    public function updateAllWithVersion($values, $conditions)
    {
        $this->createVersionCopy($conditions);
        return $this->table()->updateAll($values, $conditions);
    }

    /**
     * Cascade the delete operation
     *
     * @param $entity
     * @param $options
     *
     * @return void
     */
    protected function cascadeDelete($entity, $options = [])
    {
        $entity->set(['deleted' => 1], ['guard' => false]);
        $table = TableRegistry::getTableLocator()->get($entity->getSource());

        //TODO: implement cascading, see https://github.com/UseMuffin/Trash/blob/803feea7dbcef74591ef071804081d460931d637/src/Model/Behavior/TrashBehavior.php#L368
        foreach ($table->associations() as $association) {
            if ($this->_isRecursable($association, $entity->table)) {
                $dependent = $entity[$association->getProperty()] ?? [];
                if ($dependent instanceof EntityInterface) {
                    $this->cascadeDelete($dependent, $options);
                }
                elseif (is_array($dependent)) {
                    foreach ($dependent as $child) {
                        $this->cascadeDelete($child, $options);
                    }
                }

                if (!$entity->isNew()) {
                    $association->cascadeDelete($entity, ['_primary' => false] + $options);
                }
            }
        }
    }

    /**
     * To find deleted or versioned records
     *
     * ### Options
     * - deleted 1 (soft deleted), 2 (versions) or an array with both values
     *
     * @param Query $query
     * @param array $options
     *
     * @return Query
     */
    public function findDeleted(Query $query, array $options)
    {
        $deleted = $options['deleted'] ?? false;
        if ($deleted) {
            $this->disableDeletedFilter();
            foreach ($query->getContain() as $association => $containOptions) {
                $this->_table->{$association}->disableDeletedFilter();
            }

            if (is_numeric($deleted)) {
                $query = $query->where([$this->table()->getAlias() . '.deleted' => $deleted]);
            }
            else {
                if (is_array($deleted)) {
                    $query = $query->where([$this->table()->getAlias() . '.deleted IN' => $deleted]);
                }
            }

        }

        return $query;
    }

    public function findVersions(Query $query, array $options)
    {
        $reference = $options['version_id'] ?? null;
        if (empty($reference)) {
            throw new Exception('Missing reference node');
        }

        return $query
            ->find('deleted', ['deleted' => 2])
            ->where([
                $this->table()->getAlias() . '.version_id' => $reference,
            ]);
    }

    /**
     * Find out if an associated Table has the Trash behaviour and it's records can be trashed
     *
     * @param \Cake\ORM\Association $association The table association
     * @param \Cake\ORM\Table $table The table instance to check
     *
     * @return bool
     */
    protected function _isRecursable(Association $association, Table $table): bool
    {
        if ($association->isOwningSide($table)
            && $association->getDependent()
            && $association->getCascadeCallbacks()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Activate callbacks
     *
     * @return void
     */
    public function enableVersionBehavior()
    {
        $this->_enableVersions = true;
    }

    /**
     * Deactivate callbacks
     *
     * @return void
     */
    public function disableVersionBehavior()
    {
        $this->_enableVersions = false;
    }

    /**
     * Enables filtering out soft deleted records,
     *
     * @return void
     */
    public function enableDeletedFilter()
    {
        $this->_findDeleted = false;
//        if ($recurse) {
//            foreach ($this->getTable()->associations() as $association) {
//                if ($this->_isRecursable($association, $this->_table)) {
//                    $association->enableDeletedFilter(true);
//                }
//            }
//        }
    }

    /**
     * Disables filtering out soft deleted records
     *
     * @return void
     */
    public function disableDeletedFilter()
    {
        $this->_findDeleted = true;
//        if ($recurse) {
//            foreach ($this->getTable()->associations() as $association) {
//                if ($this->_isRecursable($association, $this->_table)) {
//                    $association->disableDeletedFilter(true);
//                }
//            }
//        }
    }

}
