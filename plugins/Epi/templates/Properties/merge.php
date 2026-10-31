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
 * @var string $stage The current stage of the merge process: 'select', 'preview', or 'result'
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Categories'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__('Merge')) ?>

<?php $this->append('css', $this->Types->getTagStyles()); ?>

<!-- Content area -->
<?php  $divAttributes = ['class' => 'widget-entity']; ?>
<?= $this->Element->openHtmlElement('div',$divAttributes) ?>

<?php $stage = \App\Utilities\Converters\Attributes::cleanOption($stage, ['select', 'preview', 'result'], 'select'); ?>
<?php if (!empty($stage)): ?>
    <?= $this->element('../Properties/merge_' . $stage) ?>
<?php endif; ?>

<?= $this->Element->closeHtmlElement('div') ?>
