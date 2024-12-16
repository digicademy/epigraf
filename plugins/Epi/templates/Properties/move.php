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
 * @var \Epi\Model\Entity\Project $entity
 **/
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Categories'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__('Move')) ?>


<!-- Content area -->
<?php $entity->prepareRoot(); ?>

<div class="content-large">
    <?= $this->EntityInput->entityForm($entity, 'move', [], false) ?>
</div>

<!-- Actions -->
<?php
$this->setShowBlock(['footer']);
$this->Link->beginActionGroup('bottom');
$this->Link->addSaveCancelDeleteButton($entity, ['action' => 'move']);
?>
