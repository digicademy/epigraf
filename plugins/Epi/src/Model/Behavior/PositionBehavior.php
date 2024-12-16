<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Behavior;

use App\Model\Behavior\TreeCorruptException;
use Cake\Collection\Collection;
use Cake\Collection\CollectionInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\Utility\Hash;
use Epi\Model\Entity\Property;
use Exception;

/**
 * Position behavior
 *
 * Prepare hierarchical data (trees)
 *
 * TODO: integrate into VersionedTreeBehavior
 *
 */
class PositionBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
    ];

    /**
     * Sort the tree
     *
     * @param array $rows The rows to sort.
     * @param string $field Name of the sort field.
     * @param string $order The sort order, 'asc' or 'desc'.
     * @return array
     */
    public static function sortTree($rows, $field = 'weight', $order = 'asc')
    {
        return Hash::sort($rows, '{n}.' . $field , $order);
    }

    /**
     * Sort an array by number and add lft/rght values
     *
     * //TODO: implement
     *
     * @param array $nodes An array of items with id, parent_id and number.
     * @return array
     * @throws Exception
     */
    public static function addMpttValues($nodes)
    {
//        $nodes = Hash::sort($nodes, '{n}.number', 'asc');
//
//        function addNestedSetValues(&$nodes) {
//            $stack = [];  // Stack to store the nodes
//            $index = 1;   // Starting index
//
//            foreach ($nodes as &$node) {
//                $node['lft'] = $index++; //Evaluates to $index and then increments
//
//                if (count($stack) > 0) {
//                    $parent = &$stack[count($stack) - 1];
//
//                    if ($node['parent'] == $parent['id']) {
//                        $parent['rght'] = $index;
//                    }
//                }
//
//                $stack[] = &$node;  // Push the current node to the stack
//            }
//
//            // Assign rght value to remaining nodes in the stack
//            foreach ($stack as &$node) {
//                $node['rght'] = $index++;
//            }
//        }
//
//        addNestedSetValues($nodes);

        return $nodes;
    }

    /**
     * Add tree properties given a list of nodes with id and one of parent_id or level
     *
     * Whether an item...
     * - is the first in the subtree
     * - is the last in the subtree,
     * - has children
     *
     * The result is saved in tree_* properties.
     *
     * @param array|ResultSet $results A list of nodes with at least id and one of parent_id or level fields
     * @param boolean $dataAttributes Create data-attributes?
     * @return Entity[] A list of entities
     * @throws TreeCorruptException
     */
    public static function addTreePositions($results, $dataAttributes = false)
    {

        // TODO: activate selected path (implement $selected parameter or a key $options array)

        // TODO: add lft & rght values. Useful, for example, to determine whether
        //       a node is the last child or has siblings

        // Relevel to zero
        if (!empty($results) && is_array($results)) {
            $levels = array_column($results, 'level');

            if (!empty($levels)) {
                $offset = min($levels);
                foreach ($results as &$item) {
                    if (isset($item['level'])) {
                        $item['tree_level'] = $item['level'] - $offset;
                    }
                }
            }
            unset($item);
        }


        $tree = [];
        $stack = [];

        $rootitem = new Entity(['id' => null, 'tree_children' => 0]);
        $stack[] = $rootitem;
        $cached = [];

        if (is_object($results) && method_exists($results, 'toArray') && is_callable([$results, 'toArray'])) {
            $results = $results->toArray();
        }

        if (!is_array($results)) {
            throw new TreeCorruptException('The tree could not be converted to an array.');
        }

        while (count($results) > 0) {

            $item = array_shift($results);

            // Convert to object
            if (!$item instanceof Entity) {
                $item = new Entity($item);
            }

            // Add id if missing
            if (!isset($item['id'])) {
                $item['id'] = count($tree) + 1;
            }

            // Reconstruct parent_id from level
            if (!isset($item['parent_id'])) {
                $item['parent_id'] = $stack[$item['tree_level'] ?? 0]['id'] ?? null;
            }

            // Cache items where parents are not in the stack yet
            if (!in_array($item['parent_id'], array_map(fn($x) => $x['id'],$stack))) {
                $cached[$item['parent_id']][] = $item;
                continue;
            }

            // Insert items from the cache as soon as the parent appears
            if (!empty($cached[$item['id']])) {
                $results = array_merge($cached[$item['id']], $results);
                unset($cached[$item['id']]);
            }

            // Preceding element
            if (!empty($stack) && $stack[count($stack) - 1]['parent_id'] === $item['parent_id']) {
                $item['preceding_id'] = $item['id'];
            }

            // Truncate stack to include only ancestors
            while (!empty($stack) && $stack[count($stack) - 1]['id'] !== $item['parent_id']) {
                array_pop($stack);
            }

            if (count($stack) == 0) {
                // TODO: add error to entity problems, don't throw exception
                throw new TreeCorruptException('The tree order is corrupt. Check the items are queried in the correct order and mptt fields are populated.');
            }

            $parent = $stack[count($stack) - 1];

            // Reconstruct level from stack
            if (!isset($item['level'])) {
                $item['level'] = count($stack);
                $item['tree_level'] = count($stack);
            }

            // Increase child count of parent
            $parent['tree_children'] += 1;

            // Add property to tree
            $item->parent = $parent;
            $item['tree_children'] = 0;
            //$item['tree_level'] = $item['tree_level'];
            $item['tree_position'] = $parent['tree_children'];
            $tree[] = $item;

            // Put property on stack
            $stack[] = $item;

            // Add references_to
            if ((isset($item['references_to'])) && count($item['references_to'])) {
                array_walk(
                    $item['references_to'],
                    function ($x) use ($item, &$tree) {
                        $x = ($x instanceof Entity) ? $x : new Entity($x);
                        $x->parent = $item;
                        $x['tree_position'] = $item['tree_children'];
                        $x['tree_level'] += 1;
                        $item['tree_children'] += 1;
                        $tree[] = $x;
                    }
                );
            }

            // Add references_from
            if (isset($item['references_from']) && count($item['references_from'])) {
                array_walk(
                    $item['references_from'],
                    function ($x) use ($item, &$tree) {
                        $x = $x instanceof Property ? $x : new Property($x);
                        $x->parent = $item;
                        $x->setSource($item->getSource());
                        $x['tree_position'] = $item['tree_children'];
                        $x['tree_level'] += 1;
                        $item['tree_children'] += 1;
                        $tree[] = $x;
                    }
                );
            }
        }

        if (!empty($cached)) {
            // TODO: add error to entity problems, don't throw exception
            throw new TreeCorruptException('The tree contains items with invalid parent IDs or with an invalid order.');
        }

        // Caluclate whether last item in tree,
        // and remove link to parent in order to avoid endless recursion in
        // methods such es debug
        array_walk(
            $tree,
            function ($x) use ($rootitem, $dataAttributes) {
                //$x['tree_loaded'] = $x->parent['rght'] === ($x['rght'] + 1);
                $x['tree_last'] = $x['tree_position'] === $x->parent['tree_children'];
                $x['tree_first'] = $x['tree_position'] === 1;

                if ($dataAttributes) {
                    $x['data'] = [
                        'data-id' => $x['id'],
                        'data-tree-parent' => $x->parent['id'],
                        'data-level' => $x['level'] // TODO: why though?
                    ];
                }

                if ($x->parent === $rootitem) {
                    $x->parent = null;
                }

                $x->clean();
            }
        );

        return $tree;
    }


    /**
     * Add tree positions
     * (see method  addTreePosition)
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findTreePositions(Query $query, array $options)
    {
        $query = $query->formatResults(
            function (CollectionInterface $results) use ($options) {
                return $this->addTreePositions($results);
            }
        );

        return $query;
    }
}
