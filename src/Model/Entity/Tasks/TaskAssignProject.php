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
 * Move articles to a project
 */
class TaskAssignProject extends BaseTaskMutate
{

    /**
     * Get options for the configuration form
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {
        $projects = $this->job->getModel('projects', 'Epi')
            ->find('list')
            ->orderAsc('name');

        $fields = [
            'config.params.target' => [
                'caption' => __('Project'),
                'id' => 'projects_id',
                'type' => 'select',
                'empty' => true,
                'options' => $projects
            ],
        ];

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
        $params['projects'] = $this->job->config['params']['target'] ?? null;
        unset($params['target']);
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
        return $params;
    }
}
