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
 * @var  \App\Model\Entity\Jobs\JobMutate $job
 * @var \App\Model\Entity\Databank $database
 * @var array $pipelines
 */

use App\Utilities\Converters\Arrays;
use Cake\Utility\Hash;
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Export')); ?>

<!-- Content area -->
<div class="content-tight widget-formupdate" data-snippet="form-export-jobs">
    <?= $this->Form->create($job, ['id' => 'form-export-jobs', 'message' => __('Create job') ]) ?>
    <fieldset>
        <?= $this->Form->hidden('jobtype', ['value' => 'export']) ?>
        <?php $entitySelection = ($job->config['selection'] ?? '') === 'entity'; ?>

        <table class="vertical-table">

            <?php if (!$entitySelection) : ?>
            <tr>
                <th scope="row"><?= __('Records') ?></th>
                <td>
                    <?= $this->Form->control(
                        'config.selection',
                        [
                            'type' => 'select',
                            'label' => false,
                            'options' => $job->selectionOptions,
                            'data-form-update' => 'selection'
                        ]
                    ) ?>
                </td>
            </tr>
            <?php endif; ?>

            <tr>
                <th scope="row"><?= __('Pipeline') ?></th>
                <td><?= $this->Form->select(
                    'config.pipeline_id', $pipelines,
                        ['data-form-update' => 'pipeline', 'empty' => $entitySelection ? true : __('Data')])
                ?></td>
            </tr>

            <?php if (empty($job->config['pipeline_id']) && !$entitySelection) : ?>
                <?=
                    $this->EntityInput->taskTable($job->config, [
                        'format' => [
                            'type' => 'select',
                            'label' => __('Output format'),
                            'options' => [
                                'xml' => __('XML'),
                                'json' => __('JSON'),
                                'csv' => __('CSV'),
                                'xlsx' => __('Excel (xlsx)'),
                                'md' => __('Markdown'),
                                'plain' => __('HTML'),
                                'ttl' => __('Turtle'),
                                'rdf' => __('RDF/XML'),
                                'jsonld' => __('JsonLd'),
                                'geojson' => __('GeoJSON')
                            ],
                            'value' => $job->config['format'] ?? 'xml'
                        ],
                        'expand' => [
                            'type' => 'checkbox',
                            'label' => __('Full entity data'),
                            'category' => __('Fields'),
                            'checked' => $job->config['expand'] ?? false
                        ],
                        'snippets' => [
                            'type' => 'text',
                            'label' => __('Snippets'),
                            'placeholder' => 'default',
                            'value' => $job->config['snippets'] ?? ''
                        ],
                        'preset' => [
                            'type' => 'text',
                            'label' => __('Preset'),
                            'placeholder' => 'default',
                            'value' => $job->config['preset'] ?? ''
                        ]
                    ], ['edit' => true, 'vertical' => true])
                ?>
            <?php elseif (!empty($job->config['pipeline_id'])): ?>
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


                <?php foreach ($job->htmlOptions as $group => $options): ?>
                    <tr>
                        <th scope="row"><?= empty($group) ? __('Tasks') : $group ?></th>
                        <td>

                            <?php foreach ($options as $key => $option): ?>
                                <?php if ($option['type'] === 'radio'): ?>
                                    <?= $this->Form->radio(
                                        'config.options.' . $option['key'],
                                        $option['options'],
                                        [
                                            'value' => $job->config['options']['custom'][$option['key']] ?? null,
                                            'hiddenField' => false
                                        ]
                                    ); ?>
                                <?php elseif ($option['type'] === 'text'): ?>
                                    <?= $this->Form->control(
                                        'config.options.' . $option['key'],
                                        [
                                            'type' => 'text',
                                            'value' => $job->config['options']['custom'][$option['key']] ?? '',
                                            'label' => empty($option['label']) ? false : $option['label']
                                        ]
                                    ); ?>
                                <?php else: ?>
                                    <?= $this->Form->control(
                                        'config.options.' . $option['key'],
                                        [
                                            'type' => 'checkbox',
                                            'checked' => !empty($job->config['options']['custom'][$option['key']]),
                                            'label' => $option['label'],
                                            'templateVars' => ['wrapperClass' => ' checkbox-horizontal']
                                        ]
                                    ); ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <tr>
                <th scope="row"><?= __('Job name') ?></th>
                <td><?= $this->Form->input(
                    'name',
                        [
                            'placeholder' => 'default',
                            'value' => $job->name ?? ''
                        ]
                    ) ?></td>
            </tr>

        </table>
    </fieldset>
    <?= $this->Form->end() ?>

    <?php foreach ($job->dataParams as $key => $value): ?>
        <?= $this->Form->hidden($key, ['value' => $value]) ?>
    <?php endforeach; ?>

    <?php
        $this->Link->beginActionGroup ('content');
        $this->Link->addCancelAction(['action' => 'index', $job->config['scope'] ?? null,'?' => ['load'=>true]]);

        if (!empty($job->config['pipeline_id']) || !empty($job->config['pipeline_tasks'])) {
            $this->Link->addSubmitAction(__('Start'), ['autofocus' => 'true', 'form' => 'form-export-jobs']);
        }
    ?>
</div>

