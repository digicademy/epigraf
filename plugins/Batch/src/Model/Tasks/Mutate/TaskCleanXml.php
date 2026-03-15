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

use InvalidArgumentException;
use App\Utilities\Converters\Attributes;

/**
 * Remove XML tags from articles
 */
class TaskCleanXml extends BaseTaskMutate
{

    static public $caption = 'Clean XML content';

    public static $taskModels = ['Epi.Articles'];

    /**
     * @var array|string[] A list of finders that are used to find the entities to mutate.
     */
    protected array $finders = ['hasParams', 'containAll'];

    /**
     * @var array|array[] Options for the saveMany method when saving the mutated entities.
     */
    protected array $saveOptions = ['associated' => ['Sections', 'Sections.Items', 'Links', 'Footnotes']];


    /**
     * Get options for the configuration form
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields): array
    {

        $fields['config.params.tags'] =
            [
                'caption' => __('Tags'),
                'placeholder' => __('comma separated element names, e.g. "note,ref"')
            ];

        $fields['config.params.steps'] =
            [
                'caption' => __('Cleaning step'),
                'type' => 'select',
                'empty' => false,
                'value' => $this->job->config['params']['steps'] ?? 'unnest',
                //'data-form-update' => 'steps',
                'options' => [
                    'unnest' => __('Remove empty and nested child tags'),
                    'remove' => __('Remove tags, keep content'),
                ]
            ];

        return $fields;
    }

    /**
     * Get parameters that are passed to the mutate method
     *
     * @return array
     */
    public function getTaskParams(): array
    {
        $params = parent::getTaskParams();
        $params['tagnames'] = Attributes::commaListToStringArray(
            $this->job->config['params']['tags'] ?? null
        );
        $params['steps'] = Attributes::commaListToStringArray(
            $this->job->config['params']['steps'] ?? null
        );
        return $params;
    }

    /**
     * Mutate entities: Remove XML tags from items
     *
     * @param array $taskParams
     * @param array $dataParams
     * @param int $offset First entity to mutate
     * @param int $limit Number of entities to mutate
     * @return array The mutated entities
     */
    protected function mutate($model, $taskParams, $dataParams, $offset = 0, $limit = 1): array
    {

        $tagNames = Attributes::commaListToStringArray($taskParams['tagnames'] ?? []);
        $steps = Attributes::commaListToStringArray($taskParams['steps'] ?? []);

        if (empty($tagNames)) {
            throw new InvalidArgumentException(__('Tag names are missing.'));
        }

        if (empty($steps)) {
            throw new InvalidArgumentException(__('Processing steps missing.'));
        }

        return $this->mutateMany(

            function ($entity) use ($tagNames, $steps) {
                $entity->cleanXmlTags($tagNames, $steps, true);
            },

            $model, $dataParams,
            ['offset' => $offset, 'limit' => $limit]
        );
    }
}
