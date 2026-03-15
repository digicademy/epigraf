<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Batch\Model\Tasks\Mutate;

use App\Utilities\Converters\Attributes;
use Cake\Utility\Hash;
use Epi\Model\Entity\Article;
use InvalidArgumentException;

/**
 * Move articles to a collection
 */
class TaskAssignProperty extends BaseTaskMutate
{

    static public $caption = 'Assign category';
    static public $allowed = ['author', 'editor'];

    public static $taskModels = ['Epi.Articles'];

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
     * Get parameters that are passed to the mutate method
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

    /**
     * Mutate entities: Patch an item with a property for each article in the result set
     *
     * Supported task parameters:
     * - target: The property ID to assign (required).
     * - sectiontype: The section type (default: collection). If no section of this type exists, it will be created.
     * - itemtype: The item type (default: collection). If no item of this type exists in the section, it will be created.
     * - irifragment: The IRI fragment to use for the item (default: {propertytype}~{propertyiriFragment}).
     * - counter: Whether to set the article number and item value to a counter value (default: false)
     *           The counter value is calculated by adding the offset to the index of the article in
     *           the result set and padding it to 3 digits, example: 001, 002, etc.
     * - position: Set to 'first' to move the patched item to the first position (default: false).
     * - clear: Whether to delete other items with the same item type in the section (default: false).
     *
     * // TODO: don't update timestamps?
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1)
    {
        $propertyId = $taskParams['target'];
        $property = $model->Sections->Items->Properties->get($propertyId);
        if (empty($property)) {
            throw new InvalidArgumentException('Invalid property: ' . $propertyId);
        }

        $sectionType = $taskParams['sectiontype'] ?? SECTIONTYPE_COLLECTION;
        if (empty($model->getDatabase()->types['sections'][$sectionType])) {
            throw new InvalidArgumentException('Invalid section type: ' . $sectionType);
        }
        $sectionName = $model->getDatabase()->types['sections'][$sectionType]['caption'] ?? __('Collection');

        $itemType = $taskParams['itemtype'] ?? ITEMTYPE_COLLECTION;
        if (empty($model->getDatabase()->types['items'][$itemType])) {
            throw new InvalidArgumentException('Invalid item type: ' . $itemType);
        }

        $setCounter = $taskParams['counter'] ?? false;
        $itemPosition = $taskParams['position'] ?? false;
        $clearSection = $taskParams['clearsection'] ?? false;

        $entities = [];

        $articles = $model->getExportData($dataParams, ['limit' => $limit, 'offset' => $offset]);

        /** @var Article $article */
        foreach ($articles as $idx => $article) {

            $articlesId = 'articles-' . $article->id;

            // Get or create section by type
            $targetSection = array_values($article->getSections($sectionType))[0] ?? [];
            if (empty($targetSection)) {
                $sectionId = 'sections/' . $sectionType . '/' . $article->iriFragment;
                $entities[] = [
                    'id' => $sectionId,
                    'articles_id' => $articlesId,
                    'name' => $sectionName
                ];
            }
            else {
                $sectionId = 'sections-' . $targetSection['id'];
            }

            // Get or create item by type
            $propertiesId = 'properties-' . $property->id;

            $targetItems = !empty($targetSection) ? $targetSection->getItemsByType($itemType) : [];
            $targetItems = array_filter($targetItems, fn($item) => $item['properties_id'] === $property->id);
            if (!empty($targetItems)) {
                $targetItem = array_values($targetItems)[0];
                $itemId = 'items-' . $targetItem->id;
                $targetItemId = $targetItem->id;
            }
            else {
                $iriItemFragment = $taskParams['irifragment'] ?? ($property->propertytype . '~' . $property->iriFragment);
                $itemId = 'items/' . $itemType . '/' . $article->iriFragment . '~' . $iriItemFragment;
                $targetItemId = null;
            }

            $itemEntity = [
                'id' => $itemId,
                'sections_id' => $sectionId,
                'articles_id' => $articlesId,
                'properties_id' => $propertiesId,
            ];

            // Set article number and item value to the counter value
            // TODO: make length configurable
            $counterNumber = $offset + $idx + 1;
            $counterValue = empty($setCounter) ? false : str_pad($counterNumber, 3, '0', STR_PAD_LEFT);
            if (!empty($setCounter)) {

                // Update item
                $itemEntity['value'] = $counterValue;

                // Update article
                $articleEntity = [
                    'id' => $articlesId,
                    'sortno' => $counterNumber,
                    'signature' => $counterValue
                ];
                $entities[] = $articleEntity;
            }

            // Delete existing items
            if ($clearSection) {
                $otherItems = $targetSection->getItemsByType($itemType);
                foreach ($otherItems as $itemIdx => $otherItem) {
                    if (empty($targetItemId) || ($otherItem['id'] !== $targetItemId)) {
                        $entities[] = ['id' => 'items-' . $otherItem->id, 'deleted' => 1];
                    }
                }
            }

            // Reorder items
            else if (!empty($itemPosition) && ($itemPosition === 'first')) {
                $itemEntity['sortno'] = 1;

                $otherItems = $targetSection->getItemsByType($itemType);
                $otherItems = Hash::sort($otherItems, '{n}.sortno');
                $itemNo = 2;
                foreach ($otherItems as $itemIdx => $otherItem) {
                    if (empty($targetItemId) || ($otherItem['id'] !== $targetItemId)) {
                        $entities[] = ['id' => 'items-' . $otherItem->id, 'sortno' => $itemNo];
                        $itemNo += 1;
                    }
                }
            }

            $entities[] = $itemEntity;
        }

        $entities = $model->toEntities($entities);
        $result = $model->saveEntities($entities);

        return $articles;
        //TODO show errors
        //$this->addTaskErrors($importBehavior->getErrors());
    }
}
