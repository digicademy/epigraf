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
        'replacefile' => [
            'type' => 'choose',
            'options' => [
                'controller' => 'Files',
                'action' => 'select',
                '?' => ['root' => 'shared', 'path' => dirname($task['replacefile'] ?? '')]
            ],
            'itemtype' => 'file',
            'label' => __('Replacement file'),
            'value' => empty($task['replacefile']) ? '' : $task['replacefile']
        ]
    ], $options)
?>
