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
use Cake\Routing\Router;

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
     * Parse query parameters to options field
     *
     * @deprecated See TransferComponent::export()
     *
     * @param $queryparams
     * @return Job
     */
    public function patchExportOptions($queryparams = [])
    {
        $config = [];

        // Pipeline
        $config['pipeline_id'] = $queryparams['pipeline'] ?? null;

        // Database
        $config['database'] = $queryparams['database'] ?? null;

        // Server
        $config['server'] = Router::url('/', true);

        // Search conditions
        $config['model'] = 'articles';
        $config['table'] = 'articles';

        $params = $queryparams;
        unset($params['database']);
        unset($params['pipeline']);
        unset($params['columns']);
        unset($params['template']);
        unset($params['save']);
        if (empty($params['term'])) {
            unset($params['field']);
        }

        // Rename project to projects
        // @deprecated Used for export from epidesktop. Change in EpiDesktop and then remove.
        if (isset($params['project'])) {
            $params['projects'] = $params['project'];
            unset($params['project']);
        }

        $params = array_filter($params);
        $config['params'] = $params;

        // Selection
        $config['selection'] = $queryparams['selection'] ?? 'selected';
        $this['selection'] = $config['selection'];

        // Update field
        $this->config = $config;
        return $this;
    }

    /**
     * Transfer options from the pipeline tasks and from the post request data to the job options
     *
     * @param Pipeline $pipeline
     * @param array $requestData
     *
     * @return $this
     */
    public function patchOptions($pipeline = null, $requestData = [])
    {

        // Get options of the job
        // TODO: rename "tasks" to "options" and "options" to "custom"
        $jobOptions = $this->config['options'] ?? [];
        $jobOptions['enabled'] = [];
        $jobOptions['options'] = [];

        //
        // 1. Transfer task options to job options
        //
        foreach (($pipeline['tasks'] ?? []) as $taskNo => $taskConfig) {

            // Enable / disable
            if (!empty($taskConfig['canskip'])) {
                $jobOptions['enabled'][$taskNo]['caption'] = $taskConfig['caption'] ?? $taskNo;
                $jobOptions['enabled'][$taskNo]['enabled'] = intval($requestData['config']['options']['enabled'][$taskNo]['enabled'] ?? true);
            }

            // Index
            if (($taskConfig['type'] ?? '') === 'data_index') {
                $jobOptions['index'] = $jobOptions['enabled'][$taskNo]['enabled'] ?? 1;
            }

            // Options
            if (($taskConfig['type'] ?? '') === 'options') {
                $jobOptions['options'] = $taskConfig['options'] ?? [];
            }
        }

        //
        // 2. Merge options from the request data
        //

        foreach ($jobOptions['options'] as $key => $option) {
            if (($option['type'] ?? '') === 'check') {
                $option['output'] = intval($requestData['config']['options'][$option['key']] ?? $option['output'] ?? 0);
            }
            elseif (($option['type'] ?? '') === 'radio') {
                if (isset($requestData['config']['options'][$option['key']])) {
                    $radioSelected = ($option['value'] ?? '') == ($requestData['config']['options'][$option['key']] ?? '');
                } else {
                    $radioSelected = $option['output'] ?? false;
                }
                $option['output'] = (int)$radioSelected;
            }
            elseif (($option['type'] ?? '') === 'text') {
                $option['output'] = $option['value'] ?? '';
                if (!empty($requestData['config']['options']['text'])) {
                    $option['output'] = $requestData['config']['options'][$option['key']] ?? [$option['output']] ?? '';
                }
            }
            $jobOptions['options'][$key] = $option;
        }

        //
        // 3. Transfer to job config
        //
        $this->config['options'] = $jobOptions;
        $this->config['pipeline_name'] = $pipeline['name'] ?? '';

        return $this;
    }

}
