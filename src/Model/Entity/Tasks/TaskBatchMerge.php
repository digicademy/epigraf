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
 * Build fulltext index
 */
class TaskBatchMerge extends BaseTaskMutate
{

    /**
     * Get parameters that are passed to the mutateEntities method
     *
     * @return array
     */
    public function getTaskParams()
    {
        $params = parent::getTaskParams();
        $params['cursor'] = $this->config['cursor'] ?? null;
        return $params;
    }
}
