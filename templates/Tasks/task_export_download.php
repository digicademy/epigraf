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
    ], $options)
?>

