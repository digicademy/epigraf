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
        'xslfile' => [
            'type' => 'choose',
            'itemtype' => 'file',
            'options' => [
                'controller' => 'Files',
                'action' => 'select',
                '?' => ['root' => 'shared', 'path' => dirname($task['xslfile'] ?? '')]
            ],
            'label' => __('XSL-T template'),
            'value' => $task['xslfile'] ?? ''
        ],
        'processor' => [
            'type' => 'radio',
            'label' => __('Parser'),
            'options' => ['saxon' => 'Saxon', 'php' => 'PHP default'],
            'value' => empty($task['processor']) ? '' : $task['processor'],
            'default' => 'php'
        ]
    ], $options)
?>
