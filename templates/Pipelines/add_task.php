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

<!-- Content area -->
<?php if (empty($entity->tasks)): ?>

    <div id="sections-selector" data-snippet="widget-dropdown-pane">
            <ul>
                <?php foreach ($entity->tasksConfig as $taskKey => $taskOptions): ?>

                    <?php $liAttributes = ['data-value ' => $taskKey] ?>
                    <?= $this->Element->openHtmlElement('li', $liAttributes); ?>
                        <label class="text" title="<?= $taskOptions['caption'] ?>">
                            <?= $taskOptions['caption'] ?>
                        </label>
                    <?= $this->Element->closeHtmlElement('li') ?>

                <?php endforeach; ?>

            </ul>

    </div>

<?php else: ?>

    <div class="tasks pipeline_tasks">
        <div class="tasks_list sortable" data-list-name="tasks">
            <?php foreach ($entity->tasks as $task): ?>
                <?php
                    $taskOptions =  $entity->tasksConfig[$task['type'] ?? ''] ?? [];
                    $taskOptions['edit'] = true;
                ?>

                <?= $this->EntityInput->taskContent($task, $taskOptions) ?>

            <?php endforeach; ?>
        </div>
    </div>

<?php endif;?>
