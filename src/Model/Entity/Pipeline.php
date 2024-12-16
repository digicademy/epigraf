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

use Epi\Model\Behavior\PositionBehavior;

/**
 * Pipeline Entity
 *
 * # Database fields (without inherited fields)
 * @property string $name
 * @property string $description
 * @property array $tasks
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
     * // TODO move to model, use the caption in Pipeline->getMenu()
     * // TODO: translate captions
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
        'data_job' => ['caption' => 'Job data', 'type' => 'data', 'inputfile' => false],
        'data_projects' => ['caption' => 'Project data', 'type' => 'data', 'inputfile' => false],
        'data_articles' => ['caption' => 'Article data', 'type' => 'data', 'inputfile' => false],
        'data_properties' => ['caption' => 'Property data', 'type' => 'data', 'inputfile' => false],
        'data_index' => ['caption' => 'Index data', 'type' => 'data', 'inputfile' => false],
        'data_types' => ['caption' => 'Types data', 'type' => 'data', 'inputfile' => false],
        'bundle' => ['caption' => 'Bundle files', 'type' => 'data', 'inputfile' => false],
        'transformxsl' => ['caption' => 'Transform with XSL', 'type' => 'transform'],
        'copy_files' => ['caption' => 'Copy files', 'type' => 'files', 'outputfile' => false, 'inputfile' => false],
        'zip' => ['caption' => 'Zip a file or folder', 'type' => 'files', 'outputfile' => true, 'inputfile' => false],
        'replace' => ['caption' => 'Search and replace', 'type' => 'transform'],
        'save' => [
            'caption' => 'Save to file',
            'type' => 'save',
            'outputfile' => false,
            'canskip' => false,
            'customcaption' => false
        ]
    ];

    /**
     * Patch the default pipeline
     *
     * @return void
     */
    public function patchDefault()
    {
        //Default pipeline
        $tasks = $this->tasks;

        // Extract fixed tasks
        $groups = ['data' => [], 'transform' => [], 'save' => []];
        // TODO: add button to add specific data snippets (e.g. 'data_types')
        foreach ($tasks as $element) {
            if (($this->tasksConfig[$element['type'] ?? '']['type'] ?? '') === 'data') {
                $element['fixed'] = true;
                $groups['data'][] = $element;
            }
            elseif (($element['type'] ?? '') === 'save') {
                $element['fixed'] = true;
                $groups['save'][] = $element;
            }
            else {
                $groups['transform'][] = $element;
            }
        }

        // Reassemble
        $tasks = array_merge(
            array_values($groups['data']),
            array_values($groups['transform']),
            array_values($groups['save'])
        );

        // Renumber
        foreach ($tasks as $key => $element) {
            $tasks[$key]['number'] = $key + 1;
        }
        $this->tasks = $tasks;
    }

    /**
     * Return fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        $fields = [
            'name' => [
                'caption' => __('Name'),
                'action' => ['edit', 'add']
            ],
            'description' => [
                'layout' => 'stacked',
                'caption' => __('Description')
            ],
            'norm_iri' => [
                'caption' => __('IRI-Fragment')
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
