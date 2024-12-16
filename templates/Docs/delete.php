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

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add($title ?? __('Pages'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity->captionPath); ?>
<?php $this->Breadcrumbs->add(__('Delete')); ?>

<!-- Content area -->
<?= $this->EntityInput->entityForm(
    $entity,
    'delete',
    ['confirm' => __('Are you sure you want to delete the page "{0}"?', $entity->captionPath)]
) ?>
