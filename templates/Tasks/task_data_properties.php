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
        'database' => [
            'type' => 'text',
            'label' => __('Database'),
            'value' => $task['database'] ?? '',
            'placeholder' => 'Database with the requested property data. Leave empty to use the default.'
        ],

        'propertytype' => [
            'type' => 'text',
            'label' => __('Property type'),
            'value' => $task['propertytype'] ?? '',
            'placeholder' => 'The property type.'
        ],
        'iris' => [
            'type' => 'checkbox',
            'label' => __('Output IRIs instead of IDs'),
            'checked' => $task['iris'] ?? false
        ]

    ], $options)
?>
