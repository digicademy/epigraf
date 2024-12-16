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
 * @var $job \App\Model\Entity\Job
 */
?>

<!-- Breadcrumbs -->
<?php
$this->Breadcrumbs->add(__('Jobs'), 'jobs/index');
$this->Breadcrumbs->add($job->id);
?>

<!-- Content area -->
<table class="vertical-table">
    <tr>
        <th scope="row"><?= __('Typ') ?></th>
        <td><?= h($job->typ) ?></td>
    </tr>

    <tr>
        <th scope="row"><?= __('Status') ?></th>
        <td><?= h($job->status) ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Progress') ?></th>
        <td><?= h($job->progress) . '/' . h($job->progressmax) ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Created') ?></th>
        <td><?= h($job->created) ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Modified') ?></th>
        <td><?= h($job->modified) ?></td>
    </tr>

</table>

<?php if (!empty($job->config['pipeline_tasks'])): ?>
    <h2><?= __('Tasks') ?></h2>
    <table>
        <thead>
            <tr>
                <th><?= __('Done') ?></th>
                <th><?= __('Number') ?></th>
                <th><?= __('Type') ?></th>
                <th><?= __('Options') ?></th>
            </tr>
        </thead>

        <?php foreach ($job->config['pipeline_tasks'] as $step => $element): ?>

            <tr>
                <td>
                    <?php if ($step < (int)$job->config['pipeline_progress']): ?>
                    <span class="badge success">✓</span>
                    <?php endif; ?>
                </td>

                <td><?= h($element['number'] ?? '') ?></td>
                <td><?= h($element['type'] ?? '') ?></td>
                <td>
                    <?php
                        $options = array_diff_key($element,['number'=>false,'type'=>false]);
                        $options = array_filter($options, function($x) { return !is_array($x); });
                        $options = array_map(function($x, $y) {return ($x . ': ' . $y);}, array_keys($options),$options);
                        $options = implode('; ',$options);
                        echo $options;
                    ?>
                </td>

            </tr>

        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php if (!empty($job->config)): ?>
    <h2><?= __('Config') ?></h2>

    <?php $options = array_diff_key($job->config,['pipeline_tasks'=>false,'pipeline_progress'=>false]); ?>
    <?=	$this->Table->nestedTable($options);?>

<?php endif; ?>
