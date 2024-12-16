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
 * Sort tree
 */
class TaskBatchSort extends BaseTaskMutate
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

        if ($this->job->config['table'] === 'properties') {
            $sortField = $this->job->config['params']['sortby'] ?? 'sortkey';
            $sortFields = [
                'sortkey' => __('Sort key'),
                'lemma' => __('Lemma'), //TODO: not working correctly for nested trees
                'name' => __('Name'),
                //'norm_iri' => __('IRI fragment'),
                'sortno' => __('Sort number'),
                'lft' => __('Tree order')
            ];

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
}
