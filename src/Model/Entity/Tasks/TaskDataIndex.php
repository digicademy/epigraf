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

use App\Model\Interfaces\ExportTableInterface;
use App\Utilities\Files\Files;
use Cake\ORM\TableRegistry;

/**
 * Write index to file
 */
class TaskDataIndex extends BaseTaskData
{

    /** @var string The model name that will be used to find the table. */
    public $model = 'Articles';

    /**
     * Export index
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $databank = $this->activateDatabank();

        /** @var ExportTableInterface $table */
        $table = TableRegistry::getTableLocator()->get($databank->plugin . '.' . $this->model);

        $indexkey = $this->job->index_key . '-export';
        $index = $table->getIndexes($indexkey);

        if (!empty($index)) {
            $options = $this->getRenderOptions();

            $view = $this->getView();
            $rendered = $view->renderContent($index, $options);
            $rendered = str_replace("\r", "", $rendered);
            Files::appendToFile($this->getCurrentOutputFilePath(), $rendered);
        }
        $table->clearIndex($indexkey);

        return true;
    }

    /**
     * Get steps needed
     *
     * How many calls of execute will be needed to finish the task?
     *
     * @return int
     */
    public function progressMax()
    {
        return 1;
    }

}
