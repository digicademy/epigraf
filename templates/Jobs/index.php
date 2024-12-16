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
 * @var \App\Model\Entity\Job[] $entities
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Jobs')); ?>

<!-- Content area -->
<div class="content-main widget-scrollbox">
    <?= $this->Table->filterTable('jobs', $entities, ['select'=>true, 'actions'=>['view'=>true]]) ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');

    $this->Link->addCounter();
    $this->Link->downloadButtons (null, 'id', 'jobs');
?>
