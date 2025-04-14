<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace App\Datasource\Paging;

use App\Utilities\Converters\Attributes;
use Cake\Database\Expression\ComparisonExpression;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\Paging\Exception\PageOutOfBoundsException;
use Cake\Datasource\Paging\NumericPaginator;
use Cake\Datasource\QueryInterface;
use Cake\Datasource\RepositoryInterface;
use Lampager\Cake\ORM\Query;

/**
 * Adds a total parameter, multiple sort fields and cursor based tree pagination to the paginator.
 *
 * The total parameter can be used to limit the total number of records.
 * The parameters seek and cursor can be used to jump into the list of records.
 *
 */
class TotalPaginator extends NumericPaginator
{

    protected $_defaultConfig = [
        'page' => 1,
        'limit' => 20,
        'maxLimit' => 100,
        'lft' => 'lft',  // Lft field name TODO: init with null
        'rght' => 'rght', // Rght field name TODO: init with null
        'level' => 'level', // Level field name
        'seek' => null,
        'cursor' => null,
//        'sort' => 'id',
//        'direction' => 'ASC',
        'children' => true,
        'collapsed' => false,
        'sortableFields' => null,
        'allowedParameters' => [
            'limit',
            'sort',
            'page',
            'direction',
            'total',
            'cursor',
            'seek',
            'children',
            'collapsed'
        ],
    ];


    /**
     * Add "prevPage" and "nextPage" params and set the cursor params.
     *
     * @param array $params Paginator params.
     * @param array $data Paging data.
     * @return array Updated params.
     */
    protected function addPrevNextParams(array $params, array $data): array
    {
        $params['prevPage'] = $params['page'] > 1;
        if ($params['count'] === null) {
            $params['nextPage'] = true;
        }
        elseif (isset($data['options']['seek'])) {
            $params['seek'] = $data['options']['seek'];
            $params['nextPage'] = true;
        }
        elseif (isset($data['options']['cursor'])) {
            $params['cursor'] = $data['options']['cursor'];
            $params['nextPage'] = $params['count'] >= $params['perPage'];
        }
        else {
            $params['nextPage'] = $params['count'] > $params['page'] * $params['perPage'];
        }

        // Limit total
        if (isset($data['options']['total']) && (isset($params['end']))) {
            $params['nextPage'] = $params['nextPage'] && ((int)$data['options']['total'] > $params['end']);
        }

        $params['level'] = $data['options']['cursornode']['level'] ?? null;

        return $params;
    }

    /**
     * Add conditions for cursored pagination
     *
     * Set the cursor or seek option.
     *
     * For models without tree behavior, this limits the results to
     * the cursor or seek id.
     *
     *
     * @param RepositoryInterface $object
     * @param QueryInterface $query
     * @param array $options
     * @return QueryInterface
     */
    protected function addCursorConditions(
        RepositoryInterface $object,
        QueryInterface $query,
        array $options
    ): QueryInterface {
        $cursor = $options['cursor'] ?? null;
        $cursor = is_numeric($cursor) ? (int)$cursor : null;
        $cursorNode = $options['cursornode'] ?? null;
        $seek = $options['seek'] ?? null;
        $seek = is_numeric($seek) ? (int)$seek : null;
        $dir = strtolower($options['direction'] ?? 'asc') ?: 'asc';


        // Add query into the tree
        if ($object->hasBehavior('VersionedTree') || $object->hasBehavior('Tree')) {

            $children = !empty(($options['children'] ?? false) ?: false);
            $collapsed = !empty(($options['collapsed'] ?? false) ?: false);

            $lftField = $options['lft'] ?? 'lft';
            $rghtField = $options['rght'] ?? 'rght';
            $levelField = $options['level'] ?? 'level';
            $sortField = ($options['sort'] ?? $lftField) ?: $lftField;

            try {
                if ($cursor) {
                    $cursor = $cursorNode ?? $object->get($cursor);

                    if ($dir === 'desc') {
                        $query = $query->andWhere([$object->getAlias() . '.' . $sortField . ' <' => $cursor->{$lftField}]);
                    }
                    else {
                        if ($children) {
                            $query = $query->andWhere([$object->getAlias() . '.' . $sortField . ' >' => $cursor->{$lftField}]);
                        }
                        else {
                            $query = $query->andWhere([$object->getAlias() . '.' . $sortField . ' >' => $cursor->{$rghtField}]);
                        }

                        if ($collapsed) {
                            $parentCursor = $children ? $cursor : $cursor->parentNode;
                            if ($parentCursor) {
                                $query = $query->andWhere([$object->getAlias() . '.' . $rghtField . ' <' => $parentCursor->{$rghtField}]);
                            }

                            $collapsedLevel = $parentCursor ? $parentCursor->level + 1 : 0;
                            $expand = $query->getOptions()['expand'] ?? false;
                            if (!empty($expand)) {
                                // See PropertiesTable::findWithAncestors()
                                $query = $query->applyOptions(['collapsedLevel' => $collapsedLevel]);
                            }
                            else {
                                $query = $query->andWhere([$object->getAlias() . '.' . $levelField => $collapsedLevel]);
                            }
                        }
                    }
                }
                elseif ($seek) {
                    $cursor = $cursorNode ?? $object->get($seek);

                    // Jump $limit records to the beginning
                    $cleanQuery = clone $query;
                    $limit = $options['limit'] ?? 1;

                    if ($dir === 'desc') {
                        $offsetCursor = $cleanQuery
                            // Disable result formatters since they might add ancestor properties
                            ->formatResults(null, Query::OVERWRITE)
                            ->andWhere([$object->getAlias() . '.' . $sortField . ' >=' => $cursor->{$lftField}])
                            ->order([$object->getAlias() . '.' . $sortField => 'ASC'], true)
                            ->offset($limit) //TODO: what if the offset is too large?
                            ->limit(1)
                            ->first();

                        $lft = $offsetCursor ? $offsetCursor->{$lftField} : $cursor->{$lftField};
                        $query = $query
                            ->andWhere([$object->getAlias() . '.' . $sortField . ' <=' => $lft])
                            ->limit($limit * 2);
                    }
                    else {
                        $offsetCursor = $cleanQuery
                            // Disable result formatters since they might add ancestor properties
                            ->formatResults(null, Query::OVERWRITE)
                            ->andWhere([$object->getAlias() . '.' . $sortField . ' <=' => $cursor->{$lftField}])
                            ->order([$object->getAlias() . '.' . $sortField => 'DESC'], true)
                            ->offset($limit)
                            ->limit(1)
                            ->first();

                        $lft = $offsetCursor ? $offsetCursor->{$lftField} : 0;
                        $query = $query
                            ->andWhere([$object->getAlias() . '.' . $sortField . ' >=' => $lft])
                            ->limit($limit * 2);
                    }
                }
                elseif ($collapsed) {
                    $collapsedLevel = 0;
                    $expand = $query->getOptions()['expand'] ?? false;
                    if (!empty($expand)) {
                        // See PropertiesTable::findWithAncestors()
                        $query = $query->applyOptions(['collapsedLevel' => $collapsedLevel]);
                    }
                    else {
                        $query = $query->andWhere([$object->getAlias() . '.' . $levelField . ' <=' => $collapsedLevel]);
                    }

                }
            } catch (RecordNotFoundException $e) {
                $query = $query->andWhere([$object->getAlias() . '.' . $sortField => VALUE_INVALID_LFT]);
            }
        }

        // Get the cursor record
        elseif ($cursor) {
            $query = $query->andWhere([$object->getAlias() . '.id' => $cursor]);
        }

        // Get the seeked record
        elseif ($seek) {
            $query = $query->andWhere([$object->getAlias() . '.id' => $seek]);
        }

//        else {
//            $query = $query->andWhere([$object->getAlias() . '.id' => VALUE_INVALID_ID]);
//        }

        return $query;
    }

    /**
     * Remove the seek conditions to count total number of records
     *
     * @param QueryInterface $query
     * @param array $options
     * @return QueryInterface
     */
    protected function removeSeekConditions(QueryInterface $query, array $options)
    {
        $seek = $options['seek'] ?? null;
        $collapsed = $options['collapsed'] ?? null;

        if ($seek || $collapsed) {

            $fields = array_filter([
                $options['lft'] ?? 'lft',
                $options['rght'] ?? 'rght',
                $options['level'] ?? 'level',
                $options['sort'] ?? null
            ]);

            // Change all lft comparison conditions to 1=1 conditions
            // Change all level comparison conditions to 1=1 conditions
            $query->traverse(function ($expression, $name) use ($fields) {
                if ($expression instanceof \Cake\Database\Expression\QueryExpression) {
                    $expression->traverse(function ($condition) use ($expression, $fields) {
                        if ($condition instanceof ComparisonExpression) {
                            $field = explode('.', $condition->getField());
                            $field = end($field);
                            if (in_array($field, $fields)) {
                                $condition->setField('1');
                                $condition->setOperator('=');
                                $condition->setValue('1');
                            }
                        }
                    });
                }
            });
        }
        return $query;
    }

    /**
     * Get query for fetching paginated results.
     *
     * @param \Cake\Datasource\RepositoryInterface $object Repository instance.
     * @param \Cake\Datasource\QueryInterface|null $query Query Instance.
     * @param array $data Pagination data.
     * @return \Cake\Datasource\QueryInterface
     */
    protected function getQuery(RepositoryInterface $object, ?QueryInterface $query, array $data): QueryInterface
    {
        // Limit total
        if (isset($data['options']['total'])) {
            $current = (int)$data['options']['limit'] * ((int)$data['options']['page'] - 1);
            $remaining = (int)$data['options']['total'] - $current;
            if (($remaining > 0) && ($remaining <= (int)$data['options']['limit'])) {
                $data['options']['limit'] = $remaining;
                $data['options']['offset'] = $current;
                unset($data['options']['page']);
            }
            elseif (($remaining <= 0)) {
                throw new PageOutOfBoundsException([
                    'requestedPage' => $data['options']['page'],
                ]);
            }
        }

        // TODO: remove page parameter for cursored (seek, cursor) pagination?

        // Get Query
        if ($query === null) {
            $query = $object->find($data['finder'], $data['options']);
        }
        else {
            $query->applyOptions($data['options']);
        }


        $query = $this->addCursorConditions($object, $query, $data['options']);

        return $query;
    }


    /**
     * Get total count of records.
     *
     * @param \Cake\Datasource\QueryInterface $query Query instance.
     * @param array $data Pagination data.
     * @return int|null
     */
    protected function getCount(QueryInterface $query, array $data): ?int
    {
        // TODO: Remove columns that were left joined to order the table in BaseTable::findSortFields()
        $query = $this->removeSeekConditions($query, $data['options']);
        return parent::getCount($query, $data);
    }

    /**
     * Extend the base class to allow multiple sort fields.
     *
     * Each field is separated by a comma in the sort key.
     * In the direction key, add corresponding asc or desc values, separated by comma.
     *
     * @param \Cake\Datasource\RepositoryInterface $object Repository object.
     * @param array $options The pagination options being used for this request.
     * @return array An array of options with sort + direction removed and
     *               replaced with order if possible.
     */
    public function validateSort(RepositoryInterface $object, array $options): array
    {

        $cursor = $options['cursor'] ?? $options['seek'] ?? null;

        if (!empty($cursor)) {
            $options['cursornode'] = $object->get($cursor);
        }

        $order = isset($options['order']) && is_array($options['order']) ? $options['order'] : [];
        if (!empty($order)) {
            $order = $this->_removeAliases($order, $object->getAlias());
        }
        $options['order'] = $order;

        if (isset($options['sort'])) {
            $sort = Attributes::combineSortLists($options['sort'] ?? '', $options['direction'] ?? '');
            $options['order'] = $sort + $order;
        }
        else {
            $options['sort'] = null;
        }
//        unset($options['direction']);

//        if (empty($options['order'])) {
//            $options['order'] = [];
//        }
        if (!is_array($options['order'])) {
            return $options;
        }

        $sortAllowed = false;
        $allowed = $this->getSortableFields($options);
        if ($allowed !== null) {
            $options['sortableFields'] = $options['sortWhitelist'] = $allowed;
            $options['order'] = array_intersect_key($options['order'], array_flip($allowed));

            // Remove sort fields that are not allowed from the sort parameter
            $sort = Attributes::combineSortLists($options['sort'] ?? '', $options['direction'] ?? '', $allowed);
            if (empty($sort)) {
                $options['sort'] = null;
                $options['direction'] = null;
            }
            else {
                $options['sort'] = implode(',', array_keys($sort));
                $options['direction'] = implode(',', array_values($sort));
            }

            $sortAllowed = !empty($options['order']);
            if (!$sortAllowed) {
                $options['order'] = [];
                $options['sort'] = null;

                return $options;
            }
        }

        if (
            $options['sort'] === null
            && count($options['order']) === 1
            && !is_numeric(key($options['order']))
        ) {
            $options['sort'] = key($options['order']);
        }

        $options['order'] = $this->_prefix($object, $options['order'], $sortAllowed);

        return $options;
    }

}
