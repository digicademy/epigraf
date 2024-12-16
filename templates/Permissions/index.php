<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

/**
 * @var \App\Model\Entity\Permission[] $entities
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Permissions')); ?>

<!-- Search bar -->
<div class="content-searchbar">
    <?= $this->Table->filterBar('permissions') ?>
</div>

<!-- Content area -->
<div class="content-main widget-scrollbox">
    <?= $this->Table->filterTable('permissions', $entities, ['select'=>true, 'actions'=>['view'=>true]]) ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addCounter();
    $this->Link->addCreateAction(__('Create permission'));
    $this->Link->addAction(__('Users'),	['controller'=>'users','action' => 'index']);
    $this->Link->addAction(__('Endpoints'),	['controller'=>'permissions','action' => 'endpoints']);

    $this->Link->downloadButtons (null, 'id', 'jobs');
?>
