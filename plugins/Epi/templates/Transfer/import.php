<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

use Cake\Routing\Router;

?>

<?php
/**
 * @var App\View\AppView $this
 * @var App\Model\Entity\Jobs\JobImport $job
 * @var App\Model\Entity\Databank $database
 * @var array $pipelines
 * @var string $scope
 * @var string $stage
 * @var array $preview
 */
?>

<!-- Breadcrumbs -->
<?php if( !empty($source)): ?>
    <?php $this->Breadcrumbs->add(__('Import {0} into {1}' , [$source, $database->caption])) ?>
<?php else: ?>
    <?php $this->Breadcrumbs->add(__('Import')); ?>
<?php endif; ?>

<!-- Content area -->
<?php if ($stage === 'select'): ?>

    <div class="content-tight">
        <?= $this->Form->create(null,['id'=>'form-import','type'=>'get']) ?>


        <table class="vertical-table">

            <tr>
                <th scope="row"><?= __('Filename') ?></th>
                <td>
                    <?= $this->Form->control(
                    'filename',
                    [
                        'type' => 'choose',
                        'itemtype'=>'file,folder',
                        'external' => Router::url([
                            'controller' => 'Files',
                            'action' => 'index',
                            '?' => ['path' => 'import']
                        ]),
                        'label' => false,
                        'options' => [
                            'controller' => 'Files', 'action' => 'select','?'=>['path'=>'import']
                        ]
                    ]); ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?= __('Pipeline') ?></th>
                <td><?= $this->Form->select('pipeline_id', $pipelines, ['empty' => true]) ?></td>
            </tr>
        </table>

        <?= $this->Form->end() ?>

        <?php foreach ($job->dataParams as $key => $value): ?>
            <?= $this->Form->hidden($key, ['value' => $value]) ?>
        <?php endforeach; ?>

        <?php
            $this->Link->beginActionGroup ('content');
            $this->Link->addCancelAction(['action' => 'index', $job->config['scope'] ?? null,'?' => ['load'=>true]]);
            $this->Link->addSubmitAction(__('Preview'), ['autofocus' => 'true', 'form' => 'form-import']);
        ?>
    </div>

<?php elseif (isset($preview)): ?>

    <?= $this->element('../Transfer/preview') ?>

<?php endif; ?>
