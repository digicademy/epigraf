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
 * @var array $task
 * @var string $service
 */

?>


<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('LLM service')) ?>

<?php
    $attr = [
        'class' => 'widget-service content-tight',
        'data-snippet' => 'service',
        'data-service-name' => $service,
        'data-service-task-id' => $task['task_id'] ?? null,
        'data-service-task-state' => $task['state'] ?? null,
    ];
    if (!empty($task['task_id'])) {
        $attr['data-service-refresh-url'] = $this->Url->build(['action' => 'get', $service, $task['task_id']]);
    }
?>
<?= $this->Element->openHtmlElement('div', $attr) ?>


    <?php if (empty($task['task_id'])): ?>

        <?= $this->Form->create(null, ['id' => 'form-service', 'type' => 'post']) ?>

            <h2>Task selection</h2>
            <?= $this->Form->control('task', [
                'type' => 'select',
                'label' => __('Task type'),
                'options' => [
                    'summarize' => __('Summarize'),
                    'triples' => __('Extract Triples'),
                    'coding' => __('Coding'),
                ],
            ]) ?>

        <?= $this->Form->control('prompts', [
            'type' => 'text',
            'label' => __('Prompt template'),
            'placeholder' => 'default'
        ]) ?>

        <?= $this->Form->control('multinomial', [
            'type' => 'checkbox',
            'label' => __('Multinomial coding')
        ]) ?>


        <h2>Option 1: Text input</h2>
        <?= $this->Form->control('input', [
                'type' => 'textarea',
                'label' => __('Input text'),
                'rows' => 5,
                'cols' => 30
            ]) ?>

        <h2>Option 2: Database input</h2>
        <?= $this->Form->control('database', [
            'type' => 'text',
            'label' => __('Database'),
            'placeholder' => 'e.g. playground'
        ]) ?>

        <?= $this->Form->control('record', [
            'type' => 'text',
            'label' => __('Article (table-id format)'),
            'placeholder' => 'e.g. articles-162'
        ]) ?>

        <?= $this->Form->control('sectiontypes', [
            'type' => 'text',
            'label' => __('Section types (comma separated)'),
            'placeholder' => 'e.g. text'
        ]) ?>

        <?= $this->Form->control('propertytype', [
            'type' => 'text',
            'label' => __('Property type for coding tasks'),
            'placeholder' => 'e.g. topics'
        ]) ?>

        <?= $this->Form->end() ?>

        <!-- Actions -->
        <?php
            $this->Link->beginActionGroup ('bottom');
            $this->Link->addSubmitAction(
                __('Query'),
                [
                    'form' => 'form-service',
                    'data-role' => 'add',
                    'shortcuts' => ['Ctrl+M']
                ]
            );
        ?>


    <?php else: ?>

         <?= $this->Table->nestedTable($task) ?>

        <?php if ($task['state'] === 'PENDING'): ?>
            <b>Stand by! Depending on the payload, it may take a minute to finish the task.</b>
        <?php endif; ?>

        <?php
            $this->Link->beginActionGroup('bottom-right');

            $this->Link->addAction(__('JSON'),
                ['action' => 'get', $service, $task['task_id'], '_ext' => 'json'],
                ['rel' => 'nofollow', 'group' => 'export']
            );

            $this->Link->beginActionGroup ('bottom');
//            if ($task['state'] !== 'SUCCESS') {
//                $this->Link->addAction(__('Refresh'), ['action' => 'get', $service, $task['task_id']]);
//            }

            $this->Link->addAction(
                __('New task'),
                ['action' => 'get', $service],
                [
                    'data-role' => 'add',
                    'shortcuts' => ['Ctrl+M']
                ]
            );
        ?>
    <?php endif; ?>

<?= $this->Element->closeHtmlElement('div') ?>
