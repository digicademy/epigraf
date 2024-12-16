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
use App\Model\Entity\Databank;
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;
use App\View\XmlView;
use Cake\ORM\TableRegistry;

/**
 * Output type configuration in the export pipeline
 */
class TaskDataTypes extends BaseTaskData
{

    /** @var string The model name that will be used to find the table. */
    public $model = 'Types';

    /** @var string The wrapper or an empty array. */
    public $wrap = ['prefix' => "\n  <types>", 'postfix' => "\n  </types>\n"];

    /**
     * Get the data query conditions
     *
     * @return array
     */
    public function getDataParams()
    {
        $dataparams = [
            'scopes' => Attributes::commaListToStringArray($this->config['scopes'] ?? ''),
            'categories' => Attributes::commaListToStringArray($this->config['categories'] ?? '')
        ];

        return $dataparams;

    }

}
