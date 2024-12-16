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
 * @var \App\Model\Entity\User $entity
 */
?>

<!-- Breadcrumbs -->
<?php
$this->Breadcrumbs->add(__('Permission'), ['action' => 'index']);
$this->Breadcrumbs->add($entity->name);
?>

<!-- Content area -->
<?= $this->EntityInput->entityForm(
    $entity,
    'delete',
    ['confirm' => __('Are you sure you want to delete the selected permission for user "{0}"?', $entity->name)]
) ?>

