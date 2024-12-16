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
 * @var \Cake\ORM\ResultSet $entities;
 * @var array $connected;
 * @var array $activeusers;
 */
?>

<!-- Search bar -->
<div class="content-searchbar">
    <?= $this->Table->filterBar('users') ?>
</div>

<!-- Content area -->
<div class="content-main widget-scrollbox">
    <?= $this->Table->filterTable('users', $entities, ['select'=>true, 'actions'=>['view'=>true]]) ?>
</div>

<h1><?= __('Other SQL connections') ?> </h1>
<?= $this->Table->simpleTable($connected, ['username' => __('Username'), 'connections' => __('SQL connections')]) ?>

<?php if (!empty($summary)): ?>
    <?php $this->start('footer'); ?>
    <div class="recordlist-summary" data-snippet="summary">
        <?= implode(' ', $summary) ?>
    </div>
    <?php $this->end(); ?>
<?php endif; ?>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');

    $this->Link->addCounter();
    $this->Link->addCreateAction(__('Create user'));
    $this->Link->addAction(__('Permissions'),	['controller'=>'permissions','action' => 'index']);
    $this->Link->addAction(__('Endpoints'),	['controller'=>'permissions','action' => 'endpoints']);
    $this->Link->downloadButtons (null, 'id', 'users');
?>

