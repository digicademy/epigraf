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

use App\Model\Entity\Job;
use App\Model\Entity\Pipeline;
use Cake\ORM\TableRegistry;

/**
 * Export data to files
 */
class JobExport extends Job
{

    /**
     * Default limit
     *
     * // TODO: move to tasks
     *
     * @var int
     */
    public int $limit = 25;

    /**
     * Setup the tasks from the selected pipeline
     *
     * @return void
     */
    protected function initPipeline()
    {
        $options = $this->config;

        if (!empty($options['pipeline_id'])) {
            //TableRegistry::getTableLocator()->clear();
            $pipelines = $this->fetchTable('Pipelines');

            /** @var Pipeline $pipeline */
            $pipeline = $pipelines->get($options['pipeline_id']);
        }
        else {
            $pipeline = [];
        }

        $options['pipeline_name'] = $pipeline['name'] ?? '';
        $options['pipeline_tasks'] = $options['pipeline_tasks'] ?? [];
        $options['pipeline_progress'] = 0;

        foreach (($pipeline['tasks'] ?? []) as $taskNo => $taskConfig) {

            // Skip disabled tasks
            if (!empty($taskConfig['canskip']) && empty($this->config['tasks']['enabled'][$taskNo]['enabled'])) {
                continue;
            }

            $options['pipeline_tasks'][] = $taskConfig;
        }

        $this->config = $options;
    }
}
