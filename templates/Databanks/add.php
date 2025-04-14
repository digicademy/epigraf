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
 * @var \App\Model\Entity\Databank $entity
 * @var array $presets
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Databanks'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(__('Create database')); ?>

<!-- Content area -->
<div class="content-tight">
    <?= $this->Form->create($entity,['id'=> 'form-add-databanks-' . $entity->newId]) ?>

    <?= $this->Form->control(
        'name',
        [
            'type' => 'choose',
            'itemtype'=>'databank',
            'options' => ['controller' => 'Databanks', 'action' => 'select']
        ]
    ); ?>

    <?= $this->Form->control(
        'category'
    ); ?>

    <?= $this->Form->control(
        'preset',
        [
            'type' => 'select',
            'options' => $presets
        ]
    ); ?>
    <?= $this->Form->end() ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addAddCancelButton($entity);
?>
