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
 * Set IRI or remove not allowed characters from the IRI
 */
class TaskAssignIris extends BaseTaskMutate
{

    static public $caption = 'Assign IRIs';

    public static $taskModels = ['Epi.Articles'];

    /**
     * @var array|string[] A list of finders that are used to find the entities to mutate.
     */
    protected array $finders = ['hasParams', 'containAll'];

    /**
     * @var array|array[] Options for the saveMany method when saving the mutated entities.
     */
    protected array $saveOptions = ['associated' => ['Sections', 'Sections.Items', 'Links', 'Footnotes']];

    /**
     * Mutate entities: Save virtual IRIs to the database
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        return $this->mutateMany(

            function($entity) {
                $entity->callRecursively('setIri', true);
            },

            $model,
            $dataParams,
            ['offset' => $offset, 'limit' => $limit]
        );
    }
}
