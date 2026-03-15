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

/**
 * Set the article number
 */
class TaskAssignNumber extends TaskAssignProperty
{

    static public $caption = 'Assign article signature';

    public static $taskModels = ['Epi.Articles'];

    /**
     * Get options for the configuration form
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {

        $fields = [];

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
        $params['sectiontype'] = SECTIONTYPE_SIGNATURES;   // TODO: get from config
        $params['itemtype'] = ITEMTYPE_SIGNATURES;         // TODO: get from config
        $params['propertytype'] = PROPERTYTYPE_LITERATURE; // TODO: get from config
        $params['counter'] = true;
        $params['position'] = 'first';
        return $params;
    }

    /**
     * Mutate entities: Assign article number
     *
     * Add an article number to the signatures section and to the article itself.
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        $taskParams['irifragment'] = 'articlenumber';
        return parent::mutate($model, $taskParams, $dataParams, $offset, $limit);
    }
}
