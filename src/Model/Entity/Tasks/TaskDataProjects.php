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
 * Output project data in the export pipeline
 */
class TaskDataProjects extends BaseTaskData
{

    /** @var string The model name that will be used to find the table. */
    public $model = 'Projects';

    /**
     * Only export project data if the project parameter is set
     *
     * @return array
     */
    public function getDataParams()
    {
        $dataparams = parent::getDataParams();
        if (empty($dataparams['projects'])) {
            $dataparams['projects'] = -1;
        }
        return $dataparams;
    }
}
