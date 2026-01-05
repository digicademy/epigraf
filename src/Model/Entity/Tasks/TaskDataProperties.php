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

use App\Model\Entity\BaseTask;
use App\Utilities\Files\Files;
use App\View\XmlView;
use Cake\ORM\TableRegistry;

/**
 * Output property data in the export pipeline
 */
class TaskDataProperties extends BaseTaskData
{

    /** @var string The model name that will be used to find the table. */
    public $model = 'Properties';

    /** @var string The wrapper or an empty array. */
    public $wrap = ['prefix' => "\n  <properties>", 'postfix' => "\n  </properties>\n"];

    /**
     * Get the data query conditions
     *
     * @return array
     */
    public function getDataParams()
    {
        $dataparams = parent::getDataParams();
        $dataparams['scope'] = $this->config['propertytype'] ?? $this->config['scope'] ?? '';
        return $dataparams;

    }
}
