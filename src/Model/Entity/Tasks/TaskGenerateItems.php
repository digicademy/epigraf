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

use App\Utilities\Converters\Attributes;
use Cake\Utility\Hash;

/**
 * Summarize an article
 */
class TaskGenerateItems extends BaseTaskMutate
{


    /**
     * Get options for the configuration form
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {

        $fields = [];

        $database = $this->job->activateDatabank($this->job->config['database']);
        $task = Attributes::cleanOption($this->job->config['params']['llmtask'] ?? 'summarize', ['summarize', 'coding', 'annotate'], 'summarize');
        $mode = Attributes::cleanOption($this->job->config['params']['mode'] ?? 'single', ['single', 'multi'], 'single');

        $fields['config.params.llmtask'] =
            [
                'caption' => __('Workflow'),
                'type' => 'select',
                'empty' => false,
                'options' => [
                    'summarize' => __('Summarize'),
                    'coding' => __('Coding'),
                    'annotate' => __('Annotate')
                ],
                'data-form-update' => 'llmtask',
                'default' => $task,
                'value' => $task
            ];

        if (in_array($task, ['summarize','coding'])) {
            $fields['config.params.mode'] =
                [
                    'caption' => __('Mode'),
                    'type' => 'select',
                    'empty' => false,
                    'options' => [
                        'single' => __('Single value output'),
                        'multi' => __('Multi value output'),
                    ],
                    'data-form-update' => 'mode',
                    'default' => $mode,
                    'value' => $mode
                ];
        }

        // Property types
        if (!(($task === 'summarize') && ($mode == 'single'))) {
            $propertyTypes = $database->types['properties'] ?? [];
            $propertyTypes = Hash::combine($propertyTypes, '{*}.name', '{*}.caption');
            $propertyType = $this->job->config['params']['propertytype'] ?? null;

            $fields['config.params.propertytype'] =
                [
                    'caption' => __('Rules'),
                    'type' => 'select',
                    'empty' => false,
                    'value' => $propertyType,
                    'options' => $propertyTypes,
                    'help' => __('The property type used to generate rules.'),
                ];
        }

        $fields['config.params.input'] = [
            'caption' => __('Input value'),
            'type' => 'textarea',
            'help' => __('Define the input using placeholder strings. Placeholder strings can contain extraction keys to get data from the selected article in curly brackets.'),
            'placeholder' => '{sections.*.items.*.content}',
            'value' => $this->job->config['params']['input'] ?? '{sections.*.items.*.content}',
        ];

        $fields['config.params.systemprompt'] = [
            'caption' => __('System prompt'),
            'type' => 'textarea',
            'help' => __('Leave empty to use the default prompt for the workflow. Alternatively, insert the system prompt. The placeholder {{text}} will be replaced by the input value. The placeholder {{rules}} will be replaced by the rules generated from the selected property type.'),
            'value' => $this->job->config['params']['systemprompt'] ?? '',
        ];

        $fields['config.params.userprompt'] = [
            'caption' => __('User prompt'),
            'type' => 'textarea',
            'help' => __('Leave empty to use the default prompt for the workflow. Alternatively, insert the system prompt. The placeholder {{text}} will be replaced by the input value. The placeholder {{rules}} will be replaced by the rules generated from the selected property type.'),
            'value' => $this->job->config['params']['userprompt'] ?? ''
        ];


        // Section and item types
        $sectionTypes = $database->types['sections'] ?? [];
        $sectionTypes = Hash::combine($sectionTypes, '{*}.name', '{*}.caption'); // , '{*}.category'

        $fields['config.params.sectiontype'] =
            [
                'caption' => __('Output section type'),
                'type' => 'select',
                'options' => $sectionTypes,
                'default' => $this->job->config['params']['sectiontype'] ?? 'summary',
                'value' => $this->job->config['params']['sectiontype'] ?? 'summary'
            ];


        $fields['config.params.sectionname'] = [
            'caption' => __('Output section name'),
            'type' => 'text',
            'help' => __('The section name in case a new section has to be generated.'),
            'default' => $this->job->config['params']['sectionname'] ?? __('Summary'),
            'value' => $this->job->config['params']['sectionname'] ?? __('Summary')

        ];

        $itemTypes = $database->types['items'] ?? [];
        $itemTypes = Hash::combine($itemTypes, '{*}.name', '{*}.caption'); // , '{*}.category'

        $fields['config.params.itemtype'] =
            [
                'caption' => __('Output item type'),
                'type' => 'select',
                'options' => $itemTypes,
                'default' => $this->job->config['params']['itemtype'] ?? 'summary',
                'value' => $this->job->config['params']['itemtype'] ?? 'summary'
            ];

        $fields['config.params.irifragment'] = [
            'caption' => __('Output item identifier'),
            'type' => 'text',
            'help' => __('IRI suffix of the new item. Existing items with this suffix will be updated.'),
            'default' => $this->job->config['params']['irifragment'] ?? 'summary',
            'value' => $this->job->config['params']['irifragment'] ?? 'summary'

        ];

        return $fields;
    }

    /**
     * Get parameters that are passed to the mutateEntities method
     *
     * @return array
     */
    public function getTaskParams()
    {

        $params = parent::getTaskParams();

        // LLM task
        $params['task'] = $this->job->config['params']['task'] ?? null;
        $params['mode'] = $this->job->config['params']['mode'] ?? null;

        // Rules
        $params['propertytype'] = $this->job->config['params']['propertytype'] ?? null;

        // Input
        $params['input'] = $this->job->config['params']['input'] ?? null;

        // Prompts
        $params['prompts'] = [];
        if (!empty($this->job->config['params']['systemprompt'])) {
            $params['prompts']['system'] = $this->job->config['params']['systemprompt'];
        }
        if (!empty($this->job->config['params']['userprompt'])) {
            $params['prompts']['user'] = $this->job->config['params']['userprompt'];
        }
        if (empty($params['prompts'])) {
            unset($params['prompts']);
        }

        // Output section
        $params['sectiontype'] = Attributes::nonEmptyOption($this->job->config['params']['sectiontype'] ?? null, 'summary');
        $params['sectionname'] = Attributes::nonEmptyOption($this->job->config['params']['sectionname'] ?? null, __('Summary'));

        // Output item
        $params['itemtype'] = Attributes::nonEmptyOption($this->job->config['params']['itemtype'] ?? null, 'summary');
        $params['irifragment'] = Attributes::nonEmptyOption($this->job->config['params']['irifragment'] ?? null,'summary');

        // Fields
        $params['resultfield'] = $this->job->config['params']['resultfield'] ?? 'content';
        $params['statefield'] = $this->job->config['params']['statefield'] ?? 'value';

        return $params;
    }

}
