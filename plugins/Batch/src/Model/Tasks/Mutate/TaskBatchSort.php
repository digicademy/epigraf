<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Batch\Model\Tasks\Mutate;

use App\Model\Table\BaseTable as AppBaseTable;
use Cake\Http\Exception\MethodNotAllowedException;

/**
 * Sort tree
 */
class TaskBatchSort extends BaseTaskMutate
{

    static public $caption = 'Sort properties';
    static public $allowed = ['author' , 'editor'];


    public static $taskModels = ['Epi.Properties'];

    /**
     * Get options for the configuration form
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {

        $fields = [];

        if ($this->job->config['table'] === 'properties') {
            $sortField = $this->job->config['params']['sortby'] ?? 'sortkey';

            $model = $this->job->getModel('properties', 'Epi');

            $sortFields = [
                'sortkey' => __('Sort key'),
                'lemma' => __('Lemma'), //TODO: not working correctly for nested trees
                'name' => __('Name'),
                //'norm_iri' => __('IRI fragment'),
                'sortno' => __('Sort number'),
                'lft' => __('Tree order')
            ];

            // TODO: Implement better permission system for tasks
            if (!in_array($model::$userRole, ['admin', 'devel'])) {
                $sortFields = array_intersect_key($sortFields,['sortkey' => true, 'lemma' => true,]); ;
            }

            $fields['config.params.sortby'] =
                [
                    'caption' => __('Sort field'),
                    'type' => 'select',
                    'empty' => false,
                    'value' => $sortField,
                    'data-form-update' => 'sortby',
                    'options' => $sortFields
                ];

        }

        return $fields;
    }

    /**
     * Sort all properties of a type
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        $dataParams = $model->parseRequestParameters($dataParams);
        $propertytype = $dataParams['propertytype'] ?? $dataParams['scope'] ?? '';

        // Sort / recover a single propertytype
        $sortBy = $taskParams['sortby'] ?? 'sortkey';

        if (!in_array(AppBaseTable::$userRole, ['admin', 'devel']) && !in_array($sortBy, ['sortkey', 'lemma'])) {
            throw new MethodNotAllowedException('You have no permission for using the selected sort field.');
        }

        $model->setSortField($sortBy);
        $model->setScope($propertytype);
        $model->recover();

        return [$propertytype];
    }
}
