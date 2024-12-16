<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace App\Model\Entity\Jobs;

use App\Model\Entity\Databank;
use App\Model\Entity\Job;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

/**
 * Recover tree job entity
 *
 * @deprecated Use MutateJob in ArticlesController::recovertree()
 */
class JobRecoverTree extends Job
{

    /**
     * Initialize task
     *
     * @return bool true if the task is finished
     */
    protected function task_init()
    {
        $options = $this->config;

        $options['pipeline_name'] = 'Recover tree';
        $options['pipeline_tasks'] = [
            ['number' => 1, 'type' => 'recover']
        ];
        $options['pipeline_progress'] = 0;
        $this->progressmax = 1;
        $this->progress = 0;

        //Init output folder and file
        /** @var Databank $databank */
        $databank = $this->activateDatabank($options['database']);
        $items = $this->fetchTable($databank->plugin . '.' . $options['model']);

        $count = $items
            ->find('all')
            ->where([$options['scopefield'] . ' IS NOT' => null])
            ->distinct($options['scopefield'])
            ->count();

        $this->progressmax += ceil($count / $this->limit);

        $this->config = $options;

        return true;
    }

    /**
     * Recover task
     *
     * @return bool true if the task is finished
     */
    public function task_recover()
    {

        /** @var Databank $databank */
        $databank = $this->activateDatabank($this->config['database']);
        $current = $this->getCurrentTask();
        $options = $this->config;

        $items = $this->fetchTable($databank->plugin . '.' . $options['model']);

        $current['offset'] = $current['offset'] ?? 0;
        $limit = $this->limit;

        $scopes = $items
            ->find('list', ['valueField' => $options['scopefield']])
            ->where([$options['scopefield'] . ' IS NOT' => null])
            ->distinct($options['scopefield'])
            ->offset($current['offset'])
            ->limit($limit)
            ->toList();

        foreach ($scopes as $scope) {
            if (!empty($config['sort'])) {
                $items->setSortField($config['sort']);
            }

            $items->setScope($scope);
            $items->recover();
        }

        $current['offset'] += count($scopes);
        $this->updateCurrentTask($current);

        return (count($scopes) < $this->limit);
    }

}
