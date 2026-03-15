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

use App\Model\Table\BaseTable;

/**
 * Publish entities
 *
 */
class TaskBatchPublish extends BaseTaskMutate
{

    static public $caption = 'Publish articles';
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

    /* TODO: use common sub key for task parameters, not 'set_'. */
    protected $taskParameters = ['set_published'];


    /**
     * Get options for the configuration form
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {

        $fields = [];

        $fields['config.params.set_published'] =
            [
                'caption' => __('Publication state'),
                'type' => 'select',
                'empty' => false,
                'value' => $this->job->config['params']['set_published'] ?? PUBLICATION_SEARCHABLE,
                'options' => $this->job->publishedOptions
            ];

        return $fields;
    }

    /**
     * Mutate entities.
     *
     * @param BaseTable $model
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        return $this->mutateMany(
            function($entity) use ($taskParams) {
                $entity->published = (int)$taskParams['set_published'];
            },

            $model,
            $dataParams,
            ['offset' => $offset, 'limit' => $limit]
        );
    }

}
