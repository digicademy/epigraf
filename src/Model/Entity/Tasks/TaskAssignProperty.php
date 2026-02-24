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

/**
 * Move articles to a collection
 */
class TaskAssignProperty extends BaseTaskMutate
{

    protected $taskParameters = [
        'target',
        'sectiontype',
        'itemtype',
        'propertytype',
        'clearsection'
    ];

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

        // First step: Section type
        $sectionTypes = array_column($database->types['sections'] ?? [], 'caption', 'name');
        $sectionType = $this->job->config['params']['sectiontype'] ?? SECTIONTYPE_COLLECTION;
        if (!isset($sectionTypes[$sectionType])) {
            $sectionType = array_keys($sectionTypes)[0] ?? null;
        }
        $this->job->config['params']['sectiontype'] = $sectionType;

        if (!empty($sectionTypes)) {
            $fields['config.params.sectiontype'] = [
                'caption' => __('Section type'),
                'type' => 'select',
                'empty' => true,
                'value' => $sectionType,
                'data-form-update' => 'sectiontype',
                'options' => $sectionTypes
            ];
        }

        // Second step: Item type
        $itemTypes = [];
        $itemType = null;
        if (!empty($sectionType)) {
            foreach ($database->types['sections'][$sectionType]['merged']['items'] ?? [] as $itemTypeKey => $itemTypeValue ) {
                if (is_array($itemTypeValue)) {
                    $itemTypeKey = $itemTypeValue['type'] ?? $itemTypeKey;
                }
                if (is_numeric($itemTypeKey) && is_string($itemTypeValue)) {
                    $itemTypeKey = $itemTypeValue;
                }
                $itemTypeValue = $database->types['items'][$itemTypeKey]['caption'] ?? $itemTypeKey;
                $itemTypes[$itemTypeKey] = $itemTypeValue;
            }

            $itemType = $this->job->config['params']['itemtype'] ?? ITEMTYPE_COLLECTION;
            if (!isset($itemTypes[$itemType])) {
                $itemType = array_keys($itemTypes)[0] ?? null;
            }
        }

        $this->job->config['params']['itemtype'] = $itemType;

        if (!empty($itemTypes)) {
            $fields['config.params.itemtype'] = [
                'caption' => __('Item type'),
                'type' => 'select',
                'empty' => true,
                'value' => $itemType,
                'data-form-update' => 'itemtype',
                'options' => $itemTypes
            ];
        }

        // Third step: Property type
        $propertyTypes = [];
        $propertyType = null;
        if (!empty($itemType)) {
            $propertyTypes = $database->types['items'][$itemType]['merged']['fields']['property']['types'] ?? [];
            $propertyTypes = is_array($propertyTypes) ? $propertyTypes : [$propertyTypes];
            $propertyTypes = array_intersect_key($database->types['properties'] ?? [], array_flip($propertyTypes));
            $propertyTypes = array_column($propertyTypes, 'caption', 'name');
            $propertyType = $this->job->config['params']['propertytype'] ?? PROPERTYTYPE_COLLECTION;
            if (!isset($propertyTypes[$propertyType])) {
                $propertyType = array_keys($propertyTypes)[0] ?? null;;
            }
        }

        $this->job->config['params']['propertytype'] = $propertyType;

        if (!empty($propertyTypes)) {
            $fields['config.params.propertytype'] = [
                'caption' => __('Category type'),
                'type' => 'select',
                'empty' => true,
                'value' => $propertyType,
                'data-form-update' => 'propertytype',
                'options' => $propertyTypes
            ];
            $fields['config.params.clearsection'] = [
                'caption' => __('Clear section'),
                'type' => 'checkbox',
                'checked' => Attributes::isTrue($this->job->config['params']['clearsection'] ?? false)
            ];
        }

        if ($propertyType !== null) {
            $fields['config.params.target'] = [
                'caption' => __('Category'),
                'type' => 'reference',
                'url' => [
                    'controller' => 'Properties',
                    'action' => 'index',
                    $propertyType,
                    '?' => ['template' => 'choose','manage' => 1]
                ],
                'param' => 'find',
                'paneSnippet' => 'rows',
                'listValue' => 'id',
            ];
        }

        return $fields;
    }

    /**
     * Update parameters that redirect to the mutated entitites
     *
     * @param array $params The parameters to be changed
     * @return array The updated parameters
     */
    public function updateRedirectParams($params)
    {
        $params['properties.' . ($params['propertytype'] ?? '') . '.selected'] = $params['target'] ?? null;
        return $params;
    }

    /**
     * Get parameters that are passed to the mutateEntities method
     *
     * @return array
     */
    public function getTaskParams()
    {
        $params = parent::getTaskParams();

        // TODO: Handle booleans for $taskParameters
        $params['clearsection'] = Attributes::isTrue($this->job->config['params']['clearsection'] ?? false);

        $params['counter'] = false;
        $params['position'] = null;

        return $params;
    }
}
