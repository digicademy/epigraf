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
 * @var \Epi\Model\Entity\Project $entity
 **/
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Categories'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity['type']['caption'] ?: $entity->propertytype) ?>


<!-- Content area -->
<?php $entity->prepareRoot(); ?>

<?php $this->append('css', $this->Types->getTagStyles()); ?>
<div class="content-large">
    <?= $this->EntityInput->entityForm($entity, 'edit', [], true) ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup('bottom');
    $this->Link->addSaveCancelDeleteButton($entity);
?>
