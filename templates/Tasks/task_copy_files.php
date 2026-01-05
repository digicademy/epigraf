<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
?>
<?php
/**
 * @var array $task
 * @var array $options
 */
?>
<?php $entityHelper =($options['edit'] ?? false) ? $this->EntityInput : $this->EntityHtml; ?>
<?=
    $entityHelper->taskTable(
        $task,
        [
            'root' => [
                'type' => 'radio',
                'label' => __('Source root folder'),
                'options' => ['shared'=>__('Shared folder'),'database'=>__('Current database folder'),'job' => __('Job folder')],
                'value' => empty($task['root']) ? '' : $task['root'],
                'default' => 'shared'
            ],
            'source' => [
                'type' => 'text',
                'label' => __('Source folder or file within the root folder'),
                'placeholder' => __('For example, "pipelines/templates/odt"'),
                'value' => $task['source'] ?? ''
            ],
            'filter' => [
                'type' => 'text',
                'label' => __('Text file containing a list of file names relative to the source folder'),
                'placeholder' => __('Leave empty to copy a single file or a complete folder'),
                'value' => $task['filter'] ?? ''
            ],
            'target' => [
                'type' => 'text',
                'label' => __('Target folder in the current job folder'),
                'placeholder' => __('For example, "odt"'),
                'value' => $task['target'] ?? ''
            ]
        ],
        $options
    )
?>
