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
 * Set the article number
 */
class TaskAssignNumber extends TaskAssignProperty
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

//        $database = $this->job->activateDatabank($this->job->config['database']);
        $propertyType = PROPERTYTYPE_LITERATURE; // TODO: make configurable


        $fields['config.params.target'] =
            [
                'caption' => __('Literature'),
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
        $params['target'] = $this->job->config['params']['target'] ?? null;
        $params['sectiontype'] = SECTIONTYPE_SIGNATURES; // TODO: get from config
        $params['sectionname'] = SECTIONNAME_SIGNATURES; // TODO: get from config
        $params['itemtype'] = ITEMTYPE_SIGNATURES;       // TODO: get from config
        $params['counter'] = true;
        $params['position'] = 'first';
        return $params;
    }
}
