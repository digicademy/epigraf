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
 * @var string $optionNumber
 * @var string $category
 * @var string $label
 * @var string $key
 * @var string $value
 * @var string $type
 * @var string $output
 */
?>

<tr class="doc-section-item" data-row-type="option">
    <td class="first">

        <?= $this->Form->hidden(
            'Tasks.' . $task['number'] . '.options.' . $optionNumber . '.number',
            ['value' => $optionNumber, 'class' => 'options_number', 'data-row-field' => 'sortno']
        ); ?>

        <?php if ($edit ?? false): ?>
            <?= $this->Form->control(
                'Tasks.' . $task['number'] . '.options.' . $optionNumber . '.category',
                ['type' => 'text', 'label' => false, 'value' => empty($category) ? '' : $category]
            ); ?>
        <?php else: ?>
            <?= $category ?>
        <?php endif; ?>
    </td>

    <td>
        <?php if ($edit ?? false): ?>
            <?= $this->Form->control(
                'Tasks.' . $task['number'] . '.options.' . $optionNumber . '.label',
                ['type' => 'text', 'label' => false, 'value' => empty($label) ? '' : $label]
            ); ?>
        <?php else: ?>
            <?= $label ?>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($edit ?? false): ?>
            <?= $this->Form->control(
                'Tasks.' . $task['number'] . '.options.' . $optionNumber . '.key',
                ['type' => 'text', 'label' => false, 'value' => empty($key) ? '' : $key]
            ); ?>
        <?php else: ?>
            <?= $key ?>
        <?php endif; ?>
    </td>
    <td>
        <?php if ($edit ?? false): ?>
            <?= $this->Form->control(
                'Tasks.' . $task['number'] . '.options.' . $optionNumber . '.value',
                ['type' => 'text', 'label' => false, 'value' => empty($value) ? '' : $value]
            ); ?>
        <?php else: ?>
            <?= $value ?>
        <?php endif; ?>
    </td>
    <td>
        <?= $this->Form->control(
            'Tasks.' . $task['number'] . '.options.' . $optionNumber . '.type',
            ['type' => 'select', 'label' => false, 'value' => $type ?? '', 'options'=>['check' => 'Check', 'radio' => 'Radio','text' =>'Text'], 'disabled' => !($edit ?? false)]
        ); ?>
    </td>
    <td>
        <?= $this->Form->control(
            'Tasks.' . $task['number'] . '.options.' . $optionNumber . '.output',
            ['type' => 'checkbox', 'label' => false, 'checked' => !empty($output), 'disabled' => !($edit ?? false)]
        ); ?>
    </td>
    <?php if ($edit ?? false): ?>
        <td>
            <button class="doc-item-remove tiny"
                    title="<?= __('Remove item') ?>"
                    type="button"
                    aria-label="<?= __('Remove item') ?>">-</button>
        </td>
    <?php endif; ?>

</tr>

