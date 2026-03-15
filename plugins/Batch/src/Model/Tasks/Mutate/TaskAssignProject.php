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
 * Move articles to a project
 */
class TaskAssignProject extends BaseTaskMutate
{

    static public $caption = 'Assign project';
    static public $allowed = ['author', 'editor'];

    public static $taskModels = ['Epi.Articles'];

    /**
     * @var array|string[] A list of finders that are used to find the entities to mutate.
     */
    protected array $finders = ['hasParams'];

    /**
     * @var array|array[] Options for the saveMany method when saving the mutated entities.
     */
    protected array $saveOptions = [];

    protected $taskParameters = ['target'];

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
        return $params;
    }

    /**
     * Mutate entities: Assign entities to another project
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1): array
    {
        return $this->mutateMany(
            function($entity) use ($taskParams) {
                $entity->projects_id = (int)$taskParams['target'];
            },

            $model,
            $dataParams,
            ['offset' => $offset, 'limit' => $limit]
        );
    }

}
