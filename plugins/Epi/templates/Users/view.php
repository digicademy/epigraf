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
 * @var Epi\Model\Entity\User $entity
 */
?>
<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Users'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity->captionPath); ?>

<!-- Content area -->
<?= $this->EntityHtml->entityForm($entity, 'view') ?>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->addEditButtons($entity);
    $this->Link->downloadButtons();
?>
