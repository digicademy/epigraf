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
 * @var \Epi\Model\Entity\Property $entity
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Delete property')); ?>

<!-- Content area -->
<?= $this->EntityInput->entityForm(
    $entity,
    'delete',
    ['confirm' => __('Are you sure you want to delete the property "{0}[#{1}][{2}]"?', $entity->captionPath,  $entity->id, $entity->norm_iri)]
) ?>


