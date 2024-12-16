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
 * @var \App\Model\Entity\Doc $entity
 */
?>

<?php $this->Breadcrumbs->add($title ?? __('Pages'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity->captionPath); ?>

<!-- Content area -->
<?= $this->EntityInput->entityForm($entity, 'edit') ?>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addSaveCancelDeleteButton($entity);
?>
