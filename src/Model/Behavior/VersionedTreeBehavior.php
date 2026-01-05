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

use App\Model\Interfaces\ScopedTableInterface;
use Cake\Collection\Collection;
use Cake\Collection\CollectionInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior\TreeBehavior;
use Cake\ORM\Query;
use Exception;

/**
 * Versioned tree behavior
 *
 * Overrides the callbacks of the Tree behavior to disable tree modifications
 * for versioned and soft deleted records.
 *
 * Supports positioning of rows based on a reference and a position.
 * For new entities, set the following fields:
 * - reference_id: ID of a reference row
 * - reference_pos: The role of the reference row with regard to the new row: 'parent' or 'preceding'.
 *
 * Alternatively, set the parent_id and the preceding_id fields:
 * - preceding_id: ID of the preceding row or null
 * - parent_id: ID of the parent or null
 *
 *
 * To use the behavior, add the properties _recoverQueue and _moveQueue to the table.
 */
class VersionedTreeBehavior extends TreeBehavior
{

    /**
     * @var bool
     */
    public $_enableTree = true;

    /**
     * Initialize hook
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->_config['implementedMethods']['disableTreeBehavior'] = 'disableTreeBehavior';
        $this->_config['implementedMethods']['enableTreeBehavior'] = 'enableTreeBehavior';
        $this->_config['implementedMethods']['setSortField'] = 'setSortField';
        $this->_config['implementedMethods']['moveTo'] = 'moveTo';
        $this->_config['implementedMethods']['moveToReference'] = 'moveToReference';

        $this->_config['implementedFinders']['containAncestors'] = 'findContainAncestors';

        parent::initialize($config);
    }

    /**
     * Disable tree modification for versioned or soft deleted records.
     *
     * TODO: Always calls _setChildrenLevel() in the parent method, can this be handled more efficiently?
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     *
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity)
    {
        if ($this->table()->hasField('version_id') && ($entity->version_id)) {
            return;
        }
        elseif ($this->table()->hasField('deleted') && ($entity->deleted)) {
            // Shift tree
            $table = $this->table();
            if ($table instanceof ScopedTableInterface) {
                $table->setScope($entity);
            }

            $rght = $entity->rght;
            $lft = $entity->lft;
            if (!is_null($rght) && !is_null($lft)) {
                $diff = ($rght - $lft) + 1;
                $this->_sync($diff, '-', "> {$rght}");
            }
        }
        else {
            $table = $this->table();
            if ($table instanceof ScopedTableInterface) {
                $table->setScope($entity);
            }

            if ($this->_enableTree) {
                if ($entity->isNew() && $entity->preceding_id) {
                    $this->moveTo($entity->id, $entity->parent_id, $entity->preceding_id);
                }
                elseif ($entity->isNew() && !empty($entity->reference_id)) {
                    $this->moveToReference($entity);
                }
                parent::afterSave($event, $entity);
            }
        }
    }

    /**
     * Disable tree modification for versioned or soft deleted records.
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity)
    {
        if ($this->table()->hasField('version_id') && ($entity->version_id)) {
            return;
        }
        elseif ($this->table()->hasField('deleted') && ($entity->deleted)) {
            return;
        }
        else {
            $table = $this->table();
            if ($table instanceof ScopedTableInterface) {
                $table->setScope($entity);
            }

            if ($this->_enableTree) {
                parent::beforeSave($event, $entity);
            }
            else {
                $scope = $table->getScope();
                $table->_recoverQueue[$scope] = $scope;
            }

        }
    }

    /**
     * Disable tree modification for versioned or soft deleted records
     *
     * @param \Cake\Event\Event $event The beforeDelete event that was fired.
     * @param \Cake\Datasource\EntityInterface $entity The entity to be deleted.
     *
     * @return void
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity)
    {
        if ($this->table()->hasField('version_id') && ($entity->version_id)) {
            return;
        }
        elseif ($this->table()->hasField('deleted') && ($entity->deleted)) {
            return;
        }
        else {
            $table = $this->table();
            if ($table instanceof ScopedTableInterface) {
                $table->setScope($entity);
            }

            if ($this->_enableTree) {
                parent::beforeDelete($event, $entity);
            }
            else {
                $scope = $table->getScope();
                $table->_recoverQueue[$scope] = $scope;
            }
        }
    }

    /**
     * Disable the callbacks
     *
     * To recover trees and perform move operations,
     * store then in the _recoverQueue and _moveQueue properties of the table.
     *
     * @return void
     */
    public function disableTreeBehavior()
    {
        $this->_enableTree = false;
        $table = $this->table();
        $table->_recoverQueue = [];
        $table->_moveQueue = [];
    }

    /**
     * Activates the callbacks
     *
     * If recover is set to true, recovers all scopes stored
     * in the _recoverQueue property of the table and
     * performs all move operations stored in the _moveQueue property of the table.
     *
     * @param boolean $recover Recover all trees that were changed in between.
     * @return void
     */
    public function enableTreeBehavior($recover = false)
    {
        $this->_enableTree = true;
        $table = $this->table();
        if ($recover) {
            foreach ($table->_recoverQueue as $scope) {
                $table->setScope($scope);
                $table->recover();
            }
            foreach ($table->_moveQueue as $move) {
                $this->moveTo($move['id'], $move['parent_id'], $move['preceding_id']);
            }
        }
        $table->_recoverQueue = [];
        $table->_moveQueue = [];
    }

    /**
     * Reconfigure the field used for recovering the tree order
     *
     * @param $orderBy
     *
     * @return void
     */
    public function setSortField($orderBy)
    {
        $config = $this->getConfig();
        $config['recoverOrder'] = $orderBy;
        $this->setConfig($config);
    }

    /**
     * Move a node to a new position based on the fields reference_pos and reference_id
     *
     * @param EntityInterface $entity
     * @return bool Whether the move operation was successful
     * @throws Exception
     */
    public function moveToReference($entity)
    {
        $referenceId = $entity->reference_id;
        $referencePosition = $entity->reference_pos;

        if ($referencePosition === 'parent') {
            return $this->moveTo($entity->id, $referenceId, null);
        }
        else {
            $referenceEntity = $entity->reference;
            return !empty($referenceEntity) && $this->moveTo($entity->id, $referenceEntity->parent_id, $referenceId);
        }
    }

    /**
     * Move a node to a new position
     *
     * @param integer $id ID of the node
     * @param integer $parent_id Target parent ID
     * @param integer $preceding_id ID of the preceding target node.
     * @return boolean
     */
    public function moveTo($id, $parent_id, $preceding_id)
    {
        $table = $this->table();
        $id = (int)$id;
        $parent_id = (is_null($parent_id) || ($parent_id === '')) ? null : (int)$parent_id;
        $preceding_id = (is_null($preceding_id) || ($preceding_id === '')) ? null : (int)$preceding_id;

        $connection = $table->getConnection();
        $connection->begin();
        try {
            $node = $table->get($id);

            if ($table instanceof ScopedTableInterface) {
                $table->setScope($node);
            }

            // Move to new parent
            if ($node['parent_id'] !== $parent_id) {
                $node['parent_id'] = $parent_id;
                //$table->setScope($node);
                $node = $table->saveOrFail($node);
            }

            if ($preceding_id) {

                // Find number of preceding nodes
                $preceding = $table->get($preceding_id);
                $cond = [
                    'level' => $preceding->level,
                    'lft <=' => $preceding->lft,
                    'id !=' => $node->id
                ];
                if ($parent_id === null) {
                    $cond['parent_id IS'] = $parent_id;
                }
                else {
                    $cond['parent_id'] = $parent_id;
                }

                // Add scope
                $scope = $this->getConfig('scope');
                if (is_array($scope)) {
                    $cond = array_merge($cond, $scope);
                }

                $delta = $table->find('all')
                    ->where($cond)
                    ->count();

                // Move to position
                $node = $this->moveUp($node, true);
                $node = $node && ($delta > 0) && $this->moveDown($node, $delta);
            }
            else {
                $node = $this->moveUp($node, true);
            }

            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            throw new Exception(__('Could not move record: {0}', [$e->getMessage()]));
        }

        return !empty($node);
    }


    /**
     * Add ancestors to the ancestors property.
     *
     * Used in the PropertiesWithAncestors relation and
     * the SectionsWithAncestors relation
     *
     * @param Query $query
     * @param array $options
     * @return Query The query will add an ancestor property.
     */
    public function findContainAncestors(Query $query, array $options)
    {
        $withancestors = $query->formatResults(
            function ($results) use (&$query) {
                if (is_array($results)) {
                    $results = collection($results);
                }

                $hydrate = $query->isHydrationEnabled();

                $table = $this->table();
                /** @var string $scopeField */
                $scopeField = $table->scopeField;
                $tableAlias = $table->getAlias();

                // Conditions to fetch all ancestors for all nodes
                $conditions = $results->map(
                    function ($node) use ($tableAlias, $scopeField) {
                        if (empty($node['level'])) {
                            return [];
                        }
                        if (empty($node['lft'])) {
                            return [];
                        }
                        if (empty($node['rght'])) {
                            return [];
                        }
                        if (empty($node[$scopeField])) {
                            return [];
                        }

                        return [
                            $tableAlias . '.lft <' => $node['lft'],
                            $tableAlias . '.rght >' => $node['rght'],
                            $tableAlias . '.' . $scopeField => $node[$scopeField]
                        ];
                    }
                )->toList();

                $conditions = array_filter(
                    $conditions,
                    function ($x) {
                        return !empty($x);
                    }
                );

                // Fetch all ancestors for all nodes
                if (!empty($conditions)) {
                    $ancestors = $table
                        ->find()
                        //->contain(['ReferencesTo','ReferencesTo.LookupTo'])
                        //->contain(['ReferencesFrom','ReferencesFrom.LookupFrom'])
                        ->where(['or' => $conditions])
                        ->order($tableAlias . '.lft')
                        ->enableHydration($hydrate)
                        ->all();
                }
                else {
                    $ancestors = new Collection([]);
                }
                $ancestors = $ancestors->indexBy('id')->toArray();

                // Inject ancestors into nodes
                $results = $results->map(
                    function ($node) use ($ancestors) {
                        if ($node === null) {
                            return $node;
                        }
                        else {
                            $node['ancestors'] = new Collection([]);
                        }

                        $ancestor = $node;
                        while ($ancestor) {
                            $ancestor = $ancestors[$ancestor['parent_id']] ?? false;
                            if ($ancestor) {
                                //$node['ancestors'] = $node['ancestors']->prependItem($ancestor);
                                $node['ancestors'] = $node['ancestors']->appendItem($ancestor);
                            }
                        }

                        $node['ancestors'] = $node['ancestors']->toList();
                        return $node;
                    }
                );

                return $results;
            }
        );

        return $withancestors;
    }

}
