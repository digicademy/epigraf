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
        'extension' => ['type' => 'text', 'label' => __('File extension'), 'value' => empty($task['extension']) ? '' : $task['extension']],
        'bom' => ['type' => 'checkbox', 'label' => __('Add UTF-8 BOM'), 'checked' => $task['bom'] ?? 0],
        'download' => ['type' => 'checkbox', 'label' => __('Force download'), 'checked' => $task['download'] ?? 0]
    ], $options)
?>

