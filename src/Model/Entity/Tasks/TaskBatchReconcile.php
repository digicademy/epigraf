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

/**
 * Reconcile norm data and geo data
 */
class TaskBatchReconcile extends BaseTaskMutate
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

        $fields['config.params.targetfield'] =
            [
                'caption' => __('Target Field'),
                'type' => 'select',
                'empty' => false,
                'value' => $this->job->config['params']['targetfield'] ?? 'norm_data',
                'data-form-update' => 'targetfield',
                'options' => [
                    'norm_data' => __('Norm data field'),
                    'content' => __('Content field'),
                ]
            ];

        $fields['config.params.onlyempty'] =
            [
                'caption' => __('Only empty fields'),
                'type' => 'checkbox',
                'data-form-update' => 'onlyempty',
                'value' => '1',
                'checked' => $this->job->config['params']['onlyempty'] ?? false,
            ];

        return $fields;
    }

    /**
     * Get parameters that are passed to the mutateEntities method
     *
     * @return array
     */
    public function getTaskParams()
    {
        $params = parent::getTaskParams();
        $params['targetfield'] = $this->job->config['params']['targetfield'] ?? null;

        $params['onlyempty'] = $this->job->config['params']['onlyempty'] ?? false;
        return $params;
    }
}
