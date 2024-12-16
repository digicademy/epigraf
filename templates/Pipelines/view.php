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
<?php
$this->Breadcrumbs->add(__('Pipelines'), ['action' => 'index']);
$this->Breadcrumbs->add($entity->captionPath);
?>

<div class="content-large">
    <?= $this->EntityHtml->entityTable($entity, 'view', true); ?>

    <div class="pipeline_tasks">
        <div class="tasks_list">
            <?php foreach ($entity->tasks as $task): ?>
                <?php
                    $taskOptions =  $entity->tasksConfig[$task['type'] ?? ''] ?? [];
                    $taskOptions['edit'] = false;
                ?>
                <?= $this->EntityHtml->taskContent($task, $taskOptions) ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->addEditButtons($entity);
?>
