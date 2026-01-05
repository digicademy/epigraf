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
 * @var App\Model\Entity\Pipeline $entity
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Pipelines'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity['name']); ?>

<!-- Content area -->
<?php $edit = $this->Link->getEdit();?>

<div class="doc-article widget-document <?= $edit ? 'widget-document-edit' : 'widget-document-view' ?>"
     data-row-table="pipelines" data-row-id="<?= $entity->id ?>"
     data-root-table="pipelines" data-root-id="<?= $entity->id ?>"
     data-edit-mode="<?= $edit ?>">

    <?php
          $options =  ['id'=>'form-edit-pipelines-' . $entity->id];
        if (!$entity->isNew() && !$entity->deleted) {
            $options['data-cancel-url'] = $this->Url->build(['action' => 'view', $entity->id]);
            $options['data-delete-url'] = $this->Url->build(['action' => 'delete', $entity->id]);
        }
    ?>
    <?= $this->Form->create($entity, $options) ?>
    <?= $this->Form->button('', ['type'=>'submit', 'disabled' => 'disabled', 'style' => 'display:none;','area-hidden'=>true]);  ?>
        <div class="doc-content">
            <?= $this->EntityInput->entityTable($entity, 'edit', true); ?>
        </div>

        <?php foreach ($entity->tasks as $task): ?>
            <?php
                $taskOptions =  $entity->tasksConfig[$task['type'] ?? ''] ?? [];
                $taskOptions['edit'] = true;
            ?>
            <?= $this->EntityInput->taskContent($task, $taskOptions) ?>

        <?php endforeach; ?>

    <?= $this->Form->end() ?>
</div>
<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addSaveCancelDeleteButton($entity);
?>
