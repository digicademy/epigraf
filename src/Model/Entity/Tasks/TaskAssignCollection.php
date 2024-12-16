<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity\Tasks;

use Cake\Utility\Hash;

/**
 * Move articles to a collection
 */
class TaskAssignCollection extends BaseTaskMutate
{

    /**
     * Get options for the configuration form
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {

        $fields = [];

        $database = $this->job->activateDatabank($this->job->config['database']);
        $propertyTypes = Hash::combine($database->types['properties'] ?? [], '{*}.name', '{*}.caption', '{*}.category');
        $propertyType = $this->job->config['params']['propertytype'] ?? null;

        $fields['config.params.propertytype'] =
            [
                'caption' => __('Category'),
                'type' => 'select',
                'empty' => true,
                'value' => $propertyType,
                'data-form-update' => 'propertytype',
                'options' => $propertyTypes
            ];

        if ($propertyType !== null) {
            $fields['config.params.target'] =
                [
                    'caption' => __('Property'),
                    'type' => 'reference',
                    'url' => [
                        'controller' => 'Properties',
                        'action' => 'index',
                        $propertyType,
                        '?' => ['template' => 'choose']
                    ],
                    'param' => 'find',
                    'paneSnippet' => 'rows',
                    'listValue' => 'id',
                ];
        }

        return $fields;
    }

    /**
     * Update parameters that redirect to the mutated entitites
     *
     * @param array $params The parameters to be changed
     * @return array The updated parameters
     */
    public function updateRedirectParams($params)
    {
        $params['properties.' . ($params['propertytype'] ?? '')] = $params['target'] ?? null;
        unset($params['target']);
        unset($params['propertytype']);
        return $params;
    }

    /**
     * Get parameters that are passed to the mutateEntities method
     *
     * @return array
     */
    public function getTaskParams()
    {
        $params = parent::getTaskParams();
        $params['target'] = $this->job->config['params']['target'] ?? null;
        $params['sectiontype'] = SECTIONTYPE_COLLECTION;
        $params['sectionname'] = __('Collection');
        $params['itemtype'] = ITEMTYPE_COLLECTION;
        $params['counter'] = false;
        $params['position'] = null;
        return $params;
    }
}
