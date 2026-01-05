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
 * @var App\View\AppView $this
 * @var App\Model\Entity\Job $job
 * @var array $pipelines
 */

use App\Utilities\Converters\Arrays;
use Cake\Utility\Hash;

?>


<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Export')) ?>

<!-- Content area -->
<div class="widget-formupdate" data-snippet="export-form">
    <div class="content-tight">
        <?= $this->Form->create($job, ['id' => 'form-export-jobs', 'data-message'=>__('Creating job')]) ?>
        <fieldset>
            <?= $this->Form->hidden('jobtype', ['value' => 'export']) ?>

            <table class="vertical-table">
                <tr>
                    <th scope="row"><?= __('Records') ?></th>
                    <td>
                        <?= $this->Form->control(
                            'selection',
                            [
                                'type' => 'select',
                                'label'=>false,
                                'options' => $job->selectionOptions,
                                'data-form-update'=>'selection'
                            ]
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?= __('Pipeline') ?></th>
                    <td><?= $this->Form->select('config.pipeline_id', $pipelines,
                            ['data-form-update' => 'pipeline', 'empty' => true]) ?></td>
                </tr>

                <?php if (!empty($job->config['options']['enabled'])) : ?>
                    <tr>
                        <th scope="row"><?= __('Tasks') ?></th>
                        <td>
                            <?php foreach ($job->config['options']['enabled'] as $taskNo => $taskConfig) : ?>
                                <?= $this->Form->control('config.options.enabled.' . $taskNo . '.enabled', [
                                    'type' => 'checkbox',
                                    'checked' => !empty($job->config['options']['enabled'][$taskNo]['enabled'] ?? true),
                                    'label' => $taskConfig['caption'] ?? $taskNo,
                                    'templateVars' => ['wrapperClass' =>' checkbox-horizontal']
                                ]); ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php $categories = Arrays::array_nest($job->config['options']['options'] ?? []); ?>
                <?php foreach ($categories as $group => $options): ?>
                    <tr>
                        <th scope="row"><?= empty($group) ? __('Tasks') : $group ?></th>
                        <td>
                            <?php
                            // Get radio options
                            $radiooptions = array_values(array_filter($options, function ($x) {
                                return ($x['type'] ?? 'check') === 'radio';
                            }));
                            $radioSelected = array_values(array_filter($radiooptions, function ($x) {
                                return !empty($x['output']);
                            }));
                            ?>
                            <?php if (!empty($radiooptions)): ?>
                                <?= $this->Form->radio(
                                    'config.options.' . $radiooptions[0]['key'],
                                    Hash::combine(
                                        $radiooptions,
                                        '{n}.value',
                                        '{n}.label'
                                    ),
                                    ['value' => $radioSelected[0]['value'] ?? null, 'hiddenField' => false]
                                ); ?>
                            <?php else: ?>
                                <?php foreach ($options as $key => $option): ?>
                                    <?php if (($option['type'] ?? 'check') === 'text'): ?>
                                        <?= $this->Form->control(
                                            'config.options.' . $option['key'],
                                            [
                                                'type' => 'text',
                                                'value' => $option['value'] ?? '',
                                                'label' => empty($option['label']) ? false : $option['label']
                                            ]
                                        ); ?>
                                    <?php else: ?>
                                        <?= $this->Form->control(
                                            'config.options.' . $option['key'],
                                            [
                                                'type' => 'checkbox',
                                                'checked' => !empty($option['output']),
                                                'label' => $option['label'],
                                                'templateVars' => ['wrapperClass' =>' checkbox-horizontal']
                                            ]
                                        ); ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        </fieldset>
        <?= $this->Form->end() ?>

    </div>
</div>

<!-- Actions -->
<?php
    $this->Link->beginActionGroup ('content');
    $this->Link->addCancelAction(['plugin'=>'epi', 'database'=>$this->request->getQuery('database'),'controller'=>'articles','action' => 'index','?' => ['load'=>true]]);
    if (!empty($job->config['pipeline_id'])) {
        $this->Link->addSubmitAction(__('Start'), ['autofocus' => 'true', 'form' => 'form-export-jobs']);
    }
?>


