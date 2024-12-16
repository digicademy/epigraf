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
 * @var \Epi\Model\Entity\Property $entity The merged entity
 * @var \Epi\Model\Entity\Property[] $propertySources
 * @var \Epi\Model\Entity\Property $propertyTarget
 * @var \App\Model\Entity\Databank $database
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Categories'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__('Merge')) ?>

<?php $this->append('css', $this->Types->getTagStyles()); ?>

<!-- Content area -->
<?php  $divAttributes = ['class' => 'widget-entity']; ?>
<?= $this->Element->openHtmlElement('div',$divAttributes) ?>

<?php if (!empty($entity) && ($entity instanceof \Epi\Model\Entity\Property)
    && !empty($entity->merged_ids) && empty($propertySources)): ?>
    <?= $this->element('../Properties/merge_result') ?>
<?php elseif (!empty($entity) && !empty($propertySources)): ?>
    <?= $this->element('../Properties/merge_preview') ?>
<?php elseif (!empty($propertySources)): ?>
    <?= $this->element('../Properties/merge_select') ?>
<?php endif; ?>

<?= $this->Element->closeHtmlElement('div') ?>
