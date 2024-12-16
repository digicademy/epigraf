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
        'scopes' => [
            'type' => 'text',
            'label' => __('Scopes'),
            'value' => $task['scopes'] ?? '',
            'placeholder' => 'A comma separated list of type scopes, e.g. "articles",links" or "footnotes".'
        ],
        'categories' => [
            'type' => 'text',
            'label' => __('Categories'),
            'value' => $task['categories'] ?? '',
            'placeholder' => 'A comma separated list of type catetories, e.g. "DIO".'
        ],
        'iris' => [
            'type' => 'checkbox',
            'label' => __('Output IRIs instead of IDs'),
            'checked' => $task['iris'] ?? false
        ],
    ], $options)
?>
