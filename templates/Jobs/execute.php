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
 */

use Cake\I18n\I18n;
use Cake\Utility\Inflector;

?>


<?php $this->Breadcrumbs->add(I18n::getTranslator()->translate(Inflector::humanize($job->typ))) ?>

<?= $this->Element->openHtmlElement(
    'div',
    [
        'class' => 'content-tight widget-job',
        'data-job-nexturl' => $job->nexturl,
        'data-job-cancel' => $job->cancelUrl,
        'data-job-redirect' => $job->redirect
    ]
) ?>


    <?php if (!empty($job->error)): ?>
        <div class="widget-job-message error">
            Error: <?= $job->error ?? '' ?>
        </div>
    <?php elseif ($job->status === 'error'): ?>
    <div class="widget-job-message error">
        <?= __('Error. See the logs for error details.') ?>
    </div>
    <?php elseif ($job->status === 'finished'): ?>
        <div class="widget-job-message success">
           <?= $job->message ?? __('Finished') ?>
        </div>
    <?php else: ?>
        <div class="widget-job-message"></div>
    <?php endif; ?>


    <?php if (empty($job->error) && !empty($job->nexturl)): ?>
        <div class="widget-job-bar"></div>
    <?php endif; ?>

    <?php $downloads = $job->config['downloads'] ?? []; ?>
    <table class="widget-job-results vertical-table<?= empty($downloads) ? ' empty' : '' ?>">
        <tr>
            <th scope="row"><?= __('Results') ?></th>
            <td>
                <?php foreach ($downloads as $download): ?>
                    <?= $this->Html->link($download['caption'], [$job->id, '?' => ['download' => $download['name']]],['target' => '_blank']) ?>
                    <br>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>

    <table class="vertical-table">
        <?php if (!empty($job->delay)): ?>
            <!--tr>
                <th scope="row"><?= __('Queue Status') ?></th>
                <td><?= h($job->queueStatus ?? '') ?></td>
            </tr-->
        <?php endif; ?>

        <tr>
            <th scope="row"><?= __('Pipeline') ?></th>
            <td><?= h($job->config['pipeline_name'] ?? '') ?></td>
        </tr>

        <tr>
            <th scope="row"><?= __('Database') ?></th>
            <td><?= h($job->config['database'] ?? '') ?></td>
        </tr>

        <?php if (!empty($job->config['model'])): ?>
            <tr>
                <th scope="row"><?= __('Model') ?></th>
                <td><?= h($job->config['model'] ?? '') ?></td>
            </tr>
        <?php endif; ?>

        <?php if (!empty($job->config['source'])): ?>
            <tr>
                <th scope="row"><?= __('Source') ?></th>
                <td><?= h($job->config['source'] ?? '') ?></td>
            </tr>
        <?php endif; ?>

        <tr>
            <th scope="row"><?= __('Parameters') ?></th>
            <td>
                <?=	$this->Table->nestedTable($job->config['params'] ?? [], ['header'=>false]);?>
            </td>
        </tr>

    </table>

    <?php $this->Link->beginActionGroup ('content'); ?>
    <?php
        $this->Link->addAction(
            empty($job->nexturl) ? __('Close') : __('Cancel'),
            '#',
            ['class' => 'widget-job-cancel button', 'data-role' => 'cancel', 'data-target' => 'main']
        );
    ?>

    <?php
        $url = $job->redirect ?? $job->nexturl ?? '#';
        if (empty($job->error) && !empty($url)) {
            $this->Link->addAction(
                !empty($job->config['download']) ? __('Download') : __('Proceed'),
                $url,
                ['class' => 'widget-job-proceed button', 'data-role' => 'proceed', 'data-target'=>'main']
            );
        }
    ?>

<?= $this->Element->closeHtmlElement('div') ?>
