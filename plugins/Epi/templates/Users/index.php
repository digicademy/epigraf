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
 * @var \App\View\AppView $this
 * @var Cake\ORM\ResultSet $entities
 */
?>
<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Users')); ?>

<!-- Filter -->
<?php // $this->Table->filterBar('epi.users') ?>

<!-- Content area -->
<div class="content-main widget-scrollbox">
    <?= $this->Table->filterTable('epi.users', $entities, ['select'=>false, 'actions'=>['view'=>true]]) ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addCounter();

    $this->Link->addCreateAction(__('Create user'));

    $this->Link->downloadButtons (null, 'users', 'epi_users');
?>
