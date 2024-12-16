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
 * @var App\Model\Entity\Databank $database
 * @var array $orphans
 */
?>

<?php $this->Breadcrumbs->add('Verweise zwischen Datensätzen'); ?>

<!-- Actions -->
<?php $this->Link->beginActionGroup ('bottom'); ?>
<?php
$this->Link->addAction(
    __('Clear orphans'),
    ['action' => 'integrityClear', 'plugin' => 'Epi'],
    ['linktype' => 'post', 'confirm' => __('Are you sure you want to clear all orphans?')]
);
?>

<div class="message danger">
    <?= __('Danger zone! Operations on this page may severely damage content.<br> Data can get lost forever!') ?>
</div>

<!-- Content area -->
<div class="content-main widget-scrollbox">
<table class="recordlist actions-show">
    <thead>
    <tr>
        <th scope="col"><?= __('Number') ?></th>
        <th scope="col"><?= __('Current') ?></th>
        <th scope="col"><?= __('Target') ?></th>
        <th scope="col"><?= __('Type') ?></th>
        <th scope="col"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($orphans as $id => $orphan): ?>
        <tr>
            <td><?= ($orphan['no']) ?></td>
            <td class="status_<?= $orphan['status'] ?>"><?= $orphan['number'] ?></td>
            <td><?= $orphan['target'] ?></td>
            <td><?= $orphan['type'] ?></td>
            <td class="actions">
                <?php if (!empty($orphan['sql_records'])): ?>
                    <?= $this->Html->link(__('Show'), ['action' => 'integrityCases', $id, 'plugin' => 'Epi'], ['class' => 'button tiny']) ?>
                <?php endif; ?>
                <?php if (!empty($orphan['clear'])): ?>
                    <?= $this->Form->postLink(__('Clear'), ['action' => 'integrityClear', $id, 'plugin' => 'Epi'],
                        ['confirm' => __('Are you sure you want to clear the selected orphans?'), 'class' => 'button tiny alert']) ?>
                <?php endif; ?>

            </td>

        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
