<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Interfaces;

use App\Model\Entity\Jobs\JobMutate;

/**
 * Interface that tables must implement in order to mutate entities via the job system
 */
interface MutateTableInterface
{

    /**
     * Get record count (for progress bar)
     *
     * @param array $params Parameters from the query string
     * @param JobMutate $job The mutate job
     * @return int Number of rows matching the conditions
     */
    public function mutateGetCount($params, $job): int;

    /**
     * Mutate all entities matched by the params
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset
     * @param int $limit
     * @return array The mutated entities
     */
    public function mutateEntities($taskParams, $dataParams, $offset, $limit): array;

    /**
     * Get the list of supported tasks
     *
     * @return array
     */
    public function mutateGetTasks(): array;
}
