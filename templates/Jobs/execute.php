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

<div class="content-tight">
    <table class="vertical-table">
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

    <div class="content-tight widget-job" data-job-nexturl="<?= $job->nexturl ?>" data-job-redirect="<?= $job->redirect ?>">

            <?php if (!empty($job->error)): ?>
                <div class="widget-job-message error">
                    Error: <?= $job->error ?? '' ?>
                </div>
            <?php elseif ($job->status === 'error'): ?>
            <div class="widget-job-message error">
                <?= __('Error. See the logs for error details.') ?>
            </div>
            <?php elseif (empty($job->nexturl)): ?>
                <div class="widget-job-message success">
                   <?= __('Finished') ?>
                </div>
            <?php else: ?>
                <div class="widget-job-message"></div>
            <?php endif; ?>


        <?php $this->Link->beginActionGroup ('content'); ?>
        <?php if (empty($job->error) && !empty($job->nexturl)): ?>
            <div class="widget-job-bar"></div>
        <?php endif; ?>


        <?php
            $this->Link->addAction(
                empty($job->nexturl) ? __('Close') : __('Cancel'),
                '#',
                ['class'=>'widget-job-cancel button','data-role'=>'cancel', 'data-target'=>'main']
            );
        ?>

        <?php
            if (empty($job->error)) {
                $this->Link->addAction(
                    $job->status === 'download' ? __('Download') : __('Proceed'),
                    $job->redirect ?? $job->nexturl ?? '#',
                    ['class' => 'widget-job-proceed button', 'data-role' => 'proceed', 'data-target'=>'main']
                );
            }
        ?>
    </div>

</div>
