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

/**
 * Interface that entities must implement in order to mutate entities via the job system
 */
interface MutateEntityInterface
{

    /**
     * Mutate all entities matched by the params
     *
     * @param array $taskParams
     * @return void
     */
    public function mutateEntity(array $taskParams): void;
}
