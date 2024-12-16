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
<?php $this->Breadcrumbs->add(__('Permissions'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity->captionPath); ?>

<!-- Content area -->
<div class="content-tight">
<?= $this->EntityHtml->entityForm($entity, 'view') ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->addEditButtons($entity);
?>
