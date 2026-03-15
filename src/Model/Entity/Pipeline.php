<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity;

use App\Model\Table\PipelinesTable;

/**
 * Pipeline Entity
 *
 * # Database fields (without inherited fields)
 * @property string $name
 * @property string $description
 * @property array $tasks
 * @property string $type The pipeline type, one of 'import' or 'export'
 *
 */
class Pipeline extends BaseEntity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * Tasks that can be added to a pipelines
     *
     * // TODO move to batch plugin
     * // TODO: translate captions, use the caption in Pipeline->getMenu()
     *
     * @var string[]
     */
    public $tasksConfig = [
        'options' => [
            'caption' => 'Options',
            'type' => 'data',
            'inputfile' => false,
            'canskip' => false,
            'customcaption' => false
        ],
        'data_job' => [
            'caption' => 'Job data',
            'type' => 'data',
            'inputfile' => false
        ],
        'data_projects' => [
            'caption' => 'Project data',
            'type' => 'data',
            'inputfile' => false
        ],
        'data_articles' => [
            'caption' => 'Article data',
            'type' => 'data',
            'inputfile' => false
        ],
        'data_properties' => [
            'caption' => 'Property data',
            'type' => 'data',
            'inputfile' => false
        ],
        'data_index' => [
            'caption' => 'Index data',
            'type' => 'data',
            'inputfile' => false
        ],
        'data_types' => [
            'caption' => 'Types data',
            'type' => 'data',
            'inputfile' => false
        ],
        'bundle' => [
            'caption' => 'Bundle files',
            'type' => 'data',
            'inputfile' => false
        ],
        'transformxsl' => [
            'caption' => 'Transform with XSL',
            'type' => 'transform'
        ],
        'copy_files' => [
            'caption' => 'Copy files',
            'type' => 'files',
            'outputfile' => false,
            'inputfile' => false
        ],
        'zip' => [
            'caption' => 'Zip a file or folder',
            'type' => 'files',
            'outputfile' => true,
            'inputfile' => false
        ],
        'replace' => [
            'caption' => 'Search and replace',
            'type' => 'transform'
        ],
        'export_extract' => [
            'caption' => 'Extract content',
            'type' => 'export_extract',
            'inputfile' => true,
            'outputfile' => true,
            'canskip' => false,
            'customcaption' => true
        ],
        'export_download' => [
            'caption' => 'Show downloads',
            'type' => 'export_download',
            'inputfile' => false,
            'outputfile' => false,
            'canskip' => false,
            'customcaption' => false
        ],
        'save' => [
            'caption' => 'Save to file',
            'type' => 'save',
            'outputfile' => false,
            'canskip' => false,
            'customcaption' => false
        ],
        'import' => [
            'caption' => 'Import into database',
            'type' => 'import',
            'inputfile' => true,
            'outputfile' => false,
            'canskip' => false,
            'customcaption' => false
        ],
    ];

    /**
     * Rearrange the tasks in the pipeline: first input, then transformation, then output tasks.
     *
     * @param array $tasks The tasks array
     * @return array The rearranged tasks array
     */
    public function arrangeTasks($tasks)
    {
        // Sort by number
        if (is_array($tasks)) {
            usort($tasks, function ($a, $b) {
                return (intval($a['number']) - intval($b['number']));
            });
        }

        // Extract fixed tasks
        $groups = ['input' => [], 'throughput' => [], 'output' => []];

        foreach ($tasks as $task) {
            $taskType = $task['type'] ?? '';
            $taskConfigType = $this->tasksConfig[$taskType]['type'] ?? '';

            if ($taskType === 'options') {
                $options = $task['options'] ?? [];
                if (is_array($options)) {
                    usort($options, function ($a, $b) {
                        return (intval($a['number']) - intval($b['number']));
                    });
                    $task['options'] = $options;
                }
            }

            if ($taskConfigType === 'data') {
                $task['fixed'] = true;
                $groups['input'][] = $task;
            }
            elseif ($taskType === 'save') {
                $task['fixed'] = true;
                $groups['output'][] = $task;
            }
            else {
                $groups['throughput'][] = $task;
            }
        }

        // Reassemble
        $tasks = array_merge(
            array_values($groups['input']),
            array_values($groups['throughput']),
            array_values($groups['output'])
        );

        // Renumber
        foreach ($tasks as $key => $task) {
            $tasks[$key]['number'] = $key + 1;
        }

        return $tasks;
    }

    /**
     * Return fields to be rendered in entity tables
     *
     * See BaseEntityHelper::entityTable() for the supported options.
     *
     * @return array[] Field configuration array.
     */
    protected function _getHtmlFields()
    {
        $fields = [
            'name' => [
                'caption' => __('Name'),
                'action' => ['edit', 'add']
            ],
            'type' => [
                'caption' => __('Type'),
                'type' => 'select',
                'options' => PipelinesTable::$pipelineTypes,
                'action' => ['edit', 'add']
            ],
            'description' => [
                'layout' => 'stacked',
                'caption' => __('Description')
            ],
            'iri_path' => [
                'caption' => __('IRI path'),
                'format' => 'iri',
                'action' => 'view'
            ],
            'norm_iri' => [
                'caption' => __('IRI fragment'),
                'action' => ['edit', 'add']
            ]
        ];

        return $fields;
    }

    /**
     * Get the pipeline tasks
     *
     * @return array
     */
    protected function _getTasks()
    {
        if (empty($this->_fields['tasks'])) {
            $this->_fields['tasks'] = [];
        }
        return $this->_fields['tasks'];
    }

    /**
     * Create a section menu
     *
     * @return array
     */
    public function getMenu()
    {
        $sectionmenu = [
            'caption' => __('Tasks'),
            'activate' => false,
            'scrollbox' => true,
            'tree' => 'fixed',
            'data' => ['data-list-add' => '/pipelines/add_task/' . $this->id],
            'class' => 'widget-scrollsync menu-sections'
        ];

        foreach ($this->tasks as $task) {
            $task['caption'] = $this->tasksConfig[$task['type'] ?? '']['caption'] ?? $task['type'] ?? '';
            $sectionmenu[] = [
                'label' => $task['number'] . ' ' . $task['caption'],
                'url' => '#sections-' . $task['number'],
                'data' => [
                    'data-list-itemof' => "menu-left",
                    'data-section-id' => 'sections-' . $task['number'],
                    'data-id' => $task['number']
                ]
            ];
        }

        // Template
        $sectionmenu[] = [
            'template' => true,
            'label' => '{sectionname}',
            'url' => '#sections-{id}',
            'data' => [
                'data-list-itemof' => "menu-left",
                'data-section-id' => 'sections-{id}',
                'data-id' => '{id}'
            ],
        ];

        return $sectionmenu;
    }

}
