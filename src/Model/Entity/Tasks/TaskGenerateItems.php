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

        $fields['config.params.systemprompt'] = [
            'caption' => __('System prompt'),
            'type' => 'textarea',
            'help' => __('Leave empty to use a prompt template. Alternatively, insert the system prompt.'),
            'value' => $this->job->config['params']['systemprompt'] ?? ''
        ];

        $fields['config.params.prompts'] = [
            'caption' => __('Prompt template'),
            'type' => 'text',
            'help' => __('Leave empty for default prompts. Alternatively provide the name of a prompt template that is supported by the server.'),
            'value' => $this->job->config['params']['prompts'] ?? 'default'
        ];

        $database = $this->job->activateDatabank($this->job->config['database']);

        // Section and item types
        $sectionTypes = $database->types['sections'] ?? [];
        $sectionTypes = Hash::combine($sectionTypes, '{*}.name', '{*}.caption'); // , '{*}.category'

        $fields['config.params.sectiontype'] =
            [
                'caption' => __('Section type'),
                'type' => 'select',
                'empty' => true,
                'options' => $sectionTypes,
                'default' => $this->job->config['params']['sectiontype'] ?? 'summary',
                'value' => $this->job->config['params']['sectiontype'] ?? 'summary'
            ];


        $fields['config.params.sectionname'] = [
            'caption' => __('Section name'),
            'type' => 'text',
            'help' => __('The section name in case a new section has to be generated.'),
            'default' => $this->job->config['params']['sectionname'] ?? __('Summary'),
            'value' => $this->job->config['params']['sectionname'] ?? __('Summary')

        ];

        $itemTypes = $database->types['items'] ?? [];
        $itemTypes = Hash::combine($itemTypes, '{*}.name', '{*}.caption'); // , '{*}.category'

        $fields['config.params.itemtype'] =
            [
                'caption' => __('Item type'),
                'type' => 'select',
                'empty' => true,
                'options' => $itemTypes,
                'default' => $this->job->config['params']['itemtype'] ?? 'summary',
                'value' => $this->job->config['params']['itemtype'] ?? 'summary'
            ];

        $fields['config.params.irifragment'] = [
            'caption' => __('Item name'),
            'type' => 'text',
            'help' => __('IRI suffix of the new item. Existing items with this suffix will be updated.'),
            'default' => $this->job->config['params']['irifragment'] ?? 'summary',
            'value' => $this->job->config['params']['irifragment'] ?? 'summary'

        ];

        // Property types
        $propertyTypes = $database->types['properties'] ?? [];
        $propertyTypes = Hash::combine($propertyTypes, '{*}.name', '{*}.caption'); // , '{*}.category'

        $propertyType = $this->job->config['params']['propertytype'] ?? null;
        //$this->job->config['params']['propertytype'] = $propertyType;

        $fields['config.params.propertytype'] =
            [
                'caption' => __('Property type'),
                'type' => 'select',
                'empty' => true,
                'value' => $propertyType,
                'options' => $propertyTypes,
                'help' => __('Leave empty to generate text. Optionally select a property type if you want to assign properties that match the result.'),
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

        // Section
        $params['sectiontype'] = Attributes::nonEmptyOption($this->job->config['params']['sectiontype'] ?? null, 'summary');
        $params['sectionname'] = Attributes::nonEmptyOption($this->job->config['params']['sectionname'] ?? null, __('Summary'));

        // Item
        $params['itemtype'] = Attributes::nonEmptyOption($this->job->config['params']['itemtype'] ?? null, 'summary');
        $params['irifragment'] = Attributes::nonEmptyOption($this->job->config['params']['irifragment'] ?? null,'summary');

        // Fields
        $params['resultfield'] = $this->job->config['params']['itemfield'] ?? 'content';
        $params['statefield'] = $this->job->config['params']['statefield'] ?? 'value';
        $params['propertytype'] = $this->job->config['params']['propertytype'] ?? null;

        // Prompt
        if (!empty($this->job->config['params']['systemprompt'])) {
            $params['prompts'] = [
                'user' => '{{text}}',
                'system' => $this->job->config['params']['systemprompt']
            ];
        } else {
            $params['prompts'] = $this->job->config['params']['prompts'] ?? 'default';
        }

        return $params;
    }

    /**
     * Update parameters that redirect to the mutated entitites
     *
     * @param array $params The parameters to be changed
     * @return array The updated parameters
     */
    public function updateRedirectParams($params)
    {
        return $params;
    }


}
