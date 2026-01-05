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
<tr><td colspan="2">
    <?php if (($options['edit'] ?? true) || !empty($task['options'])): ?>

        <table class="options_list">
            <thead>
            <tr>
                <th><?= __('Category') ?></th>
                <th><?= __('Label') ?></th>
                <th><?= __('Key') ?></th>
                <th><?= __('Value') ?></th>
                <th><?= __('Type') ?></th>
                <th><?= __('Select') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach (array_values($task['options'] ?? []) as $optionNumber => $option): ?>
                    <?php
                        $option['task'] = $task;
                        $option['optionNumber'] = $optionNumber + 1;
                        $option['edit'] = $options['edit'] ?? false;
                        if (($option['radio'] ?? '') === '1') {
                            $option['type'] = 'radio';
                        } elseif (($option['radio'] ?? '') === '0') {
                            $option['type'] = 'check';
                        } elseif (empty($option['type'])) {
                            $option['type'] = 'check';
                        }
                    ?>
                    <?= $this->element('../Tasks/task_options_custom', $option); ?>
                <?php endforeach; ?>


                <?php if ($options['edit'] ?? false): ?>
                    <tr class="doc-section-item">
                        <td class="first"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button class="doc-item-add tiny"
                                    title="<?= __('Add item') ?>"
                                    aria-label="<?= __('Add item') ?>">+</button></td>
                            <script type="text/template" class="template template-doc-section-item">
                                <?php
                                $option = [
                                    'edit' => $options['edit'] ?? false,
                                    'task' => $task,
                                    'optionNumber' => '{id}'
                                ];
                                ?>
                                <?= $this->element('../Tasks/task_options_custom', $option); ?>
                            </script>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</td> </tr>

<?php $entityHelper =($options['edit'] ?? false) ? $this->EntityInput : $this->EntityHtml; ?>
<?=
$entityHelper->taskTable($task, [

    'format' => [
        'type' => 'select',
        'label' => __('Output format'),
        'options' => [
            '' => __('No output'),
            'xml' => __('XML'),
            'json' => __('JSON'),
            'csv' => __('CSV'),
            'md' => __('Markdown')
        ],
        'value' => $task['format'] ?? 'xml'
    ]
], $options)
?>
