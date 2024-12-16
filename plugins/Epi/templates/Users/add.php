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
 * @var \Epi\Model\Entity\User $entity
 */
?>

<!-- Breadcrumbs -->
<?php
    $this->Breadcrumbs->add(__('Users'), ['action' => 'index']);
    $this->Breadcrumbs->add(__('Create user'));
?>

<!-- Content area -->
<div class="content-tight">
    <?= $this->EntityInput->entityForm($entity, 'add') ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addAddCancelButton($entity);
?>
