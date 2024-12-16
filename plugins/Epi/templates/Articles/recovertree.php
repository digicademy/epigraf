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
 *
 * @var \App\View\AppView $this
 * @var int $id

 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Recover tree')); ?>

<!-- Content area -->
<?= $this->Form->create(null, ['id' => 'form-recover-articles-' . $id, 'type' => 'post']) ?>
<fieldset>
    <?php if ($id !== null): ?>
    <p class="recover">
        <?= __('Do you want to recover the tree structure of record {0}?', $id) ?>
    </p>
    <?= $this->Html->link(__('Cancel'), ['action' => 'view', $id], ['class' => 'button button_cancel']) ?>
    <?php else: ?>
        <p class="recover">
            <?= __('Do you want to recover the tree structure of all articles?') ?>
        </p>
        <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'button button_cancel']) ?>
    <?php endif; ?>

    <?= $this->Form->button(__('Yes')) ?>
</fieldset>
<?= $this->Form->end() ?>
