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

use App\Model\Entity\BaseEntity;
use App\Model\Table\BaseTable;
use App\Model\Table\SaveManyException;

/**
 * Publish entities
 *
 * TODO: This class is on the way to a new task system that keeps all task related code in one place. Implement a task registry. Implement a plugin. Refactor all task classes.
 *
 */
class TaskBatchPublish extends BaseTaskMutate
{

    public static $taskModels = ['Epi.Articles'];

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
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        $dataParams = $model->parseRequestParameters($dataParams);
        $entities = $model
            ->find('hasParams', $dataParams)
            //->find('containAll', $dataParams)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        /** @var BaseEntity $entity */
        foreach ($entities as $entity) {
            $entity->published = (int)$taskParams['set_published'];
        }

        if (empty($entities)) {
            return $entities;
        }

        if (!$model->saveMany($entities)) {
            throw new SaveManyException('Could not save entities.');
        }

        return $entities;
    }

}
