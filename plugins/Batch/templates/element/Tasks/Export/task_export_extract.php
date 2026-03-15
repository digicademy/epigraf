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
        'prefix' => [
            'type' => 'textarea',
            'escape' => true,
            'label' => __('Prefix'),
            'placeholder'=>__('The start of the output file.'),
            'value' => $task['prefix'] ?? ''
        ],
        'path' => [
            'type' => 'input',
            'escape' => true,
            'label' => __('Extraction path'),
            'placeholder'=>__('The path used to extract the data from the XML file, e.g  "book/page[@type=\'intro\']".'),
            'value' => $task['path'] ?? ''
        ],
        'postfix' => [
            'type' => 'textarea',
            'escape' => true,
            'label' => __('Postfix'),
            'placeholder'=>__('The end of the output file.'),
            'value' => $task['postfix'] ?? ''
        ]
    ], $options)
?>
