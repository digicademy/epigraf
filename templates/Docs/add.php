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
<?php $this->Breadcrumbs->add(__('Create page')); ?>

<!-- Content area -->
<?php if (!empty($entity->norm_iri)): ?>
    <div class="notice">
        <?= __('There is no page on this topic yet. We would be glad if you could help out.') ?>
    </div>
<?php endif; ?>

<?= $this->EntityInput->entityForm($entity, 'add') ?>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addAddCancelButton($entity);
?>
