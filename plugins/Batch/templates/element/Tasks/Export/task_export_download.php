<?php
/*
 *  Epigraf 4.0
 *
 * @author     Epigraf team
 * @contact    jakob.juenger@adwmainz.de
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
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
<?= $entityHelper->taskTable($task, [
        'files' => [
            'type' => 'textarea',
            'escape' => true,
            'label' => __('Files'),
            'placeholder'=>__('Add one filename per line.'),
            'value' => $task['files'] ?? ''
        ],
        'root' => [
            'type' => 'radio',
            'label' => __('Download root folder'),
            'options' => ['job' => __('Job folder'), 'shared'=>__('Shared folder'),'database'=>__('Current database folder')],
            'value' => empty($task['root']) ? '' : $task['root'],
            'default' => 'shared'
        ],
        'target' => [
            'type' => 'text',
            'label' => __('Download folder within the root folder'),
            'placeholder' => __('For example, "data/ttl"'),
            'value' => $task['target'] ?? ''
        ]
    ], $options)
?>

