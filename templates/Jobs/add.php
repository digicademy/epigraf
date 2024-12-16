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
 * @var array $datenbanken
 * @var array $projekte
 */

use Cake\Utility\Hash;

?>


<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Export')) ?>

<!-- Content area -->
<div class="widget-formupdate" data-snippet="export-form">
    <div class="content-tight">
        <?= $this->Form->create($job, ['id' => 'form-export-jobs', 'data-message'=>__('Creating job')]) ?>
        <fieldset>
            <?= $this->Form->hidden('typ', ['value' => 'export']) ?>

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

                <?php if (!empty($job->config['tasks']['enabled'])) : ?>
                    <tr>
                        <th scope="row"><?= __('Tasks') ?></th>
                        <td>
                            <?php foreach ($job->config['tasks']['enabled'] as $taskNo => $taskConfig) : ?>
                                <?= $this->Form->control('config.tasks.enabled.' . $taskNo . '.enabled', [
                                    'type' => 'checkbox',
                                    'checked' => !empty($job->config['tasks']['enabled'][$taskNo]['enabled'] ?? true),
                                    'label' => $taskConfig['caption'] ?? $taskNo,
                                    'templateVars' => ['wrapperClass' =>' checkbox-horizontal']
                                ]); ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <?php if (!empty($job->config['tasks']['options'])) : ?>
                    <?php
                        // TODO: implement Helper functions to prepare the options
                        // Nest categories
                        $categories = $job->config['tasks']['options'];
                        $categories = array_map(
                            function ($no, $val) { return ['option' => $val, 'no' => $no]; },
                            array_keys($categories), $categories
                        );
                        $categories = Hash::combine(
                            $categories,
                            '{n}.no', '{n}.option',
                            '{n}.option.category'
                        );
                    ?>
                    <?php foreach ($categories as $group => $options): ?>
                        <tr>
                            <th scope="row"><?= empty($group) ? __('Tasks') : $group ?></th>
                            <td>
                                <?php
                                    // Get type
                                // @deprecated, legacy code that maps the radio option to the type option
                                    $options = array_map(
                                        function ($option) {
                                            if (($option['radio'] ?? '') === '1') {
                                                $option['type'] = 'radio';
                                            }
                                            elseif (($option['radio'] ?? '') === '0') {
                                                $option['type'] = 'check';
                                            }
                                            elseif (empty($option['type'])) {
                                                $option['type'] = 'check';
                                            }

                                            return $option;
                                        },
                                        $options
                                    );
                                    // Nest radio options
                                    $radiooptions = array_filter($options, function ($x) {
                                        return $x['type'] === 'radio';
                                    });
                                    $radiooptions = Hash::combine(
                                        $radiooptions,
                                        '{n}.number',
                                        '{n}.label'
                                    );
                                ?>
                                <?php if (!empty($radiooptions)): ?>
                                    <?php
                                        $key = array_keys($radiooptions)[0];
                                        $radiochecked = array_values(array_filter($options, function ($x) {
                                            return !empty($x['output']);
                                        }));
                                        $radiochecked = empty($radiochecked) ? null : $radiochecked[0]['number'];
                                    ?>
                                    <?= $this->Form->radio(
                                        'config.tasks.radio.' . $key,
                                        $radiooptions,
                                        ['value' => $radiochecked]
                                    ); ?>
                                <?php else: ?>
                                    <?php foreach ($options as $key => $option): ?>
                                        <?php if ($option['type'] === 'text'): ?>
                                            <?= $this->Form->control(
                                                'config.tasks.text.' . $option['number'],
                                                [
                                                    'type' => 'text',
                                                    'value' => $option['value'] ?? '',
                                                    'label' => empty($option['label']) ? false : $option['label']
                                                ]
                                            ); ?>
                                        <?php else: ?>
                                            <?= $this->Form->control(
                                                'config.tasks.check.' . $option['number'],
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
                <?php endif; ?>
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


