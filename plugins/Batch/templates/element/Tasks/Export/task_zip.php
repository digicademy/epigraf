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
$entityHelper->taskTable($task, [
    'source' => [
        'type' => 'text',
        'label' => __('Folder or file to be zipped, relative to the job folder'),
        'placeholder' => __('Leave empty to use the current xml file'),
        'value' => $task['source'] ?? ''
    ]
], $options)
?>
