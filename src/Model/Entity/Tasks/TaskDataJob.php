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

use App\Utilities\Files\Files;
use Cake\Chronos\Chronos;
use Cake\Routing\Router;

/**
 * Output job parameters in the export pipeline
 */
class TaskDataJob extends BaseTaskData
{

    /**
     * Export articles
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {

        $data = array_intersect_key(
            $this->job->config ?? [],
            array_flip(['pipeline_id', 'pipeline_name', 'database', 'model'])
        );

        $data['created'] = Chronos::now()->toIso8601String();
        $data['server'] = Router::url('/', true);
        $data['baseiri'] = Router::url([
                'plugin' => 'Epi',
                'controller' => 'Iris',
                'action' => 'show',
                'database' => DATABASE_PUBLIC,
                '_http' => false
            ], true) . '/';
        $data['folder'] = $this->job->jobPath;
        $data['_xml_attributes'] = ['server', 'database', 'model', 'baseiri', 'pipeline_name', 'pipeline_id', 'folder'];
        $data['params'] = $this->job->dataParams;

        $view = $this->getView();
        $content = $view->renderContent($data, ['tagname' => 'job'], 1);

        $outputfile = $this->getCurrentOutputFilePath();
        Files::appendToFile($outputfile, $content);

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
