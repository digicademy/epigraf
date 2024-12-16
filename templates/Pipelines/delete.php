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
 * @var \App\Model\Entity\Pipeline $entity
 */
?>

<!-- Breadcrumbs -->
<?php
    $this->Breadcrumbs->add(__('Pipelines'), ['action' => 'index']);
    $this->Breadcrumbs->add(__('Delete pipeline'));
?>

<!-- Content area -->
<?= $this->EntityInput->entityForm(
    $entity,
    'delete',
    ['confirm' => __('Are you sure you want to delete the pipeline "{0}"?', $entity->captionPath)]
) ?>
