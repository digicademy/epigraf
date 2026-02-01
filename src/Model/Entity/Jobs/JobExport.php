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
use App\Utilities\Exceptions\DeprecatedException;
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
     * @param array $queryparams
     * @return Job
     */
    public function patchExportOptions($queryparams = [])
    {
        throw new DeprecatedException('Deprecated.');

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
     * @param array $requestData The values in the options array are merged into the options key of the entity's `config.options` field.
     * @return $this
     */
    public function patchOptions($requestData = [])
    {

        if (empty($this->pipeline)) {
            return $this;
        }

        $this->config['pipeline_name'] = $this->pipeline['name'] ?? '';

        // Get options of the job
        $jobOptions = $this->config['options'] ?? [];
        $jobOptions['enabled'] = [];
        $jobOptions['custom'] = [];

        $customOptions = [];

        //
        // 1. Transfer task options to job options
        //
        foreach (($this->pipeline['tasks'] ?? []) as $taskNo => $taskConfig) {

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
                $customOptions = $taskConfig['options'] ?? [];
            }
        }

        //
        // 2. Merge options from the request data
        //
        foreach ($customOptions as $customOption) {
            $customKey = $customOption['key'] ?? '_';
            $requestValue = $requestData['config']['options'][$customKey] ?? null;

            if (($customOption['type'] ?? '') === 'check') {
                $customValue = intval($requestValue ?? $customOption['output'] ?? 0);
            }
            elseif (($customOption['type'] ?? '') === 'radio') {
                if (is_null($requestValue) && !empty($customOption['output'])) {
                    $customValue = $customOption['value'] ?? '';
                } else {
                    $customValue = $requestValue ?? '';
                }
            }
            elseif (($customOption['type'] ?? '') === 'text') {
                $customValue = $customOption['value'] ?? '';
                if (($requestValue ?? '') !== '') {
                    $customValue = $requestValue;
                }
            }
            else {
                continue;
            }

            $jobOptions['custom'][$customKey] = $customValue;
        }

        //
        // 3. Transfer to job config
        //
        $this->config['options'] = $jobOptions;

        return $this;
    }

}
