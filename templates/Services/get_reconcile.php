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

<?php $params = $this->getConfig('options')['params'] ?? []; ?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Reconciliation service')) ?>

<?php
    $attr = [
        'class' => 'content-main content-tight widget-service',
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
    <div class="service-searchbar">
        <?= $this->Form->create(null, ['id' => 'form-service', 'type' => 'post','url'=>['action'=>'reconcile']]) ?>

        <div class="input-group input-group-filter first">
            <div class="input-group-field">
                <?=  $this->Form->control('q', [
                    'type' => 'text',
                    'label' => false,
                    'value' => $params['q'] ?? ''
                ]) ?>
            </div>

            <?php if (empty( $params['provider'])): ?>
            <div class="input-group-button">
                <?= $this->Form->control('provider', [
                    'type' => 'select',
                    'label' => false,
                    'options' => [
                        'aat' => __('AAT'),
                        'wd' => __('WikiData')
                    ],
                    'value' => $params['provider'] ?? ''
                ]) ?>
            </div>
            <?php else: ?>
                <?= $this->Form->hidden('provider', ['value' => $params['provider'] ?? '']) ?>
            <?php endif; ?>
        </div>

        <?= $this->Form->hidden('type', ['value' => $params['type'] ?? '']) ?>
        <?= $this->Form->hidden('preview', ['value' => $params['preview'] ?? '']) ?>
        <?= $this->Form->hidden('cache', ['value' => $params['cache'] ?? '']) ?>

        <?= $this->Form->end() ?>
    </div>

    <div class="service-answers">
    <?php foreach($task['result']['answers'] ?? [] as $answer): ?>
        <div class="service-answer">
            <?php foreach($answer['candidates'] as $candidate): ?>
                <div class="service-answer-candidate">
                    <div class="service-answer-candidate-header">
                        <?= $this->Html->link($candidate['name'] . ' (' . $candidate['value'] . ')', $candidate['url'] ?? '#',['target'=>'_blank']) ?>
                        <button class="service-answer-select tiny" data-value="<?= $candidate['value'] ?? $candidate['id'] ?>" data-list-itemof="service-answers"><?= __('Apply') ?></button>
                    </div>
                    <?php if (!empty($candidate['preview'])): ?>
                        <div class="service-answer-candidate-preview">
                            <?= $candidate['preview'] ?>
                        </div>
                    <?php elseif (!empty($candidate['description'])): ?>
                        <div class="service-answer-candidate-description">
                            <?= $candidate['description'] ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    </div>

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

<?= $this->Element->closeHtmlElement('div') ?>

