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
 * @var  \App\Model\Entity\Jobs\JobMutate $job
 * @var \App\Model\Entity\Databank $database
 * @var \App\View\AppView $this
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Mutate records')); ?>

<!-- Content area -->
<div class="content-tight widget-formupdate" data-snippet="form-mutate">
    <?= $this->Form->create($job, ['id' => 'form-mutate', 'valueSources' => ['query'], 'message' => __('Create job') ]) ?>

        <table class="vertical-table">
            <tr>
                <th scope="row"><?= __('Records') ?></th>
                <td>
                    <?= $this->Form->control(
                        'selection',
                        ['type' => 'select', 'label'=>false, 'options' => $job->selectionOptions,'data-form-update'=>'selection']
                    ) ?>
                </td>
            </tr>

            <tr>
                <th scope="row"><?= __('Task') ?></th>
                <td>
                    <?= $this->Form->control(
                    'task',
                    ['type' => 'select', 'label'=>false, 'empty'=>true, 'options' => $job->tasks,'data-form-update'=>'task']
                    ) ?>
                </td>
            </tr>

            <?php if (!empty($job->config['task'])): ?>
                <?php  foreach ($job->htmlFields ?? [] as $fieldName => $fieldOptions): ?>
                    <tr>
                        <th scope="row"><?= $fieldOptions['caption'] ?? $fieldName ?></th>
                        <td>
                            <?= $this->EntityInput->entityField($job, $fieldName, $fieldOptions, 'edit') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr>
                <th scope="row"><?= __('Parameters') ?></th>
                <td>

                    <?=	$this->Table->nestedTable($job->dataParams, ['header'=>false]);?>
                    <?php foreach ($job->dataParams as $key => $value): ?>
                        <?= $this->Form->hidden($key,['value'=>$value]) ?>
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>

    <?= $this->Form->end() ?>

    <?php
        $this->Link->beginActionGroup ('content');

        $this->Link->addCancelAction(['action' => 'index', $job->config['scope'] ?? null]);

        if (!empty($job->config['task'])) {
            $this->Link->addSubmitAction(__('Start'), ['autofocus' => 'true', 'form' => 'form-mutate']);
        }
    ?>
</div>

