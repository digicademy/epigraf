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
 * @var \App\Model\Entity\Permission $entity
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Permission'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity->captionPath); ?>

<!-- Content area -->
<div class="content-tight">
    <?= $this->EntityInput->entityForm($entity, 'edit') ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addSaveCancelDeleteButton($entity);
?>
