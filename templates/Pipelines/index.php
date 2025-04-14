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
 * @var \App\Model\Entity\Pipeline[] $entities
 */
?>

<!-- Right sidebar -->
<?php $this->setSidebarConfig(['left' => ['size' =>0], 'right' => ['size' => 5]]); ?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Pipelines')); ?>

<!-- Search bar -->
<?php //TODO: search name, description and norm_iri. See Articles -> index_search.php ?>
<div class="content-searchbar">
    <?= $this->Table->filterBar('pipelines') ?>
</div>

<!-- Content area -->
<div class="content-main widget-scrollbox">
    <?= $this->Table->filterTable('pipelines', $entities, ['select'=>true, 'actions'=>['view'=>true]]) ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addCreateAction(__('Create pipeline'));
    $this->Link->addAction(__('Show jobs'),['controller' => 'Jobs', 'action' => 'index']);

    $this->Link->downloadButtons (null, 'id', 'pipelines');
?>
