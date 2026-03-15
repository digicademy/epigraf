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
            'label' => __('Source folder, relative to the job folder'),
            'placeholder' => __('Leave empty to concatenate all default files produced by the current job.'),
            'value' => empty($task['source']) ? '' : $task['source']
        ],
        'prefix' => [
            'type' => 'textarea',
            'escape' => true,
            'label' => __('Prefix'),
            'placeholder'=>__('The start of the output file. You can use placeholder strings to access the job data.'),
            'value' => $task['prefix'] ?? ''
        ],
        'postfix' => [
            'type' => 'textarea',
            'escape' => true,
            'label' => __('Postfix'),
            'placeholder'=>__('The end of the output file. You can use placeholder strings to access the job data.'),
            'value' => $task['postfix'] ?? ''
        ],
        'indent' => [
            'type' => 'text',
            'label' => __('Add indentation'),
            'placeholder' => '0',
            'value' => empty($task['indent']) ? '' : $task['indent']
        ],
    ], $options)
?>
