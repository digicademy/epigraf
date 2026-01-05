<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

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

use App\Model\Entity\Databank;
use Cake\Utility\Hash;
use App\Utilities\Converters\Arrays;

?>
<?php
/**
 * @var App\Model\Entity\Jobs\JobTransfer $job
 * @var App\Model\Entity\Databank $database
 * @var App\Model\Entity\Databank[] $databases
 * @var App\View\AppView $this
 * @var string $scope
 * @var string $stage
 * @var array $preview
 */
?>

<!-- Breadcrumbs -->
<?php if ($stage === 'preview'): ?>
    <?php
        $this->Breadcrumbs->add(
            __('Transfer from {0} into {1}',
            [$job->config['source'], $job->config['database']])
        );
    ?>
<?php else: ?>
    <?php $this->Breadcrumbs->add(__('Transfer')); ?>
<?php endif; ?>

<!-- Content area -->
<?php if ($stage === 'select'): ?>

    <?= $this->Form->create(null, ['id' => 'form-transfer', 'type' => 'get']) ?>
    <?php foreach ($job->config['params'] as $key => $value): ?>
        <?= isset($job->options[$key]) ? '' : $this->Form->hidden($key, ['value' => $value]) ?>
    <?php endforeach; ?>

    <div class="transfer-databases content-tight">

        <table class="vertical-table">
            <tr>
                <th scope="row"><?= __('Source database') ?></th>
                <td><?= $job->config['source'] ?></td>
            </tr>

            <tr>
                <th scope="row"><?= __('Target database') ?></th>
                <td>
                    <?php
                        $databases = Hash::filter($databases, function ($db) use ($database) {
                            return $db['version'] == $database['version'];
                        });
                        $default = Databank::removePrefix(DATABASE_STAGE);
                        $databases =  array_map(fn($x) => Databank::removePrefix($x), array_column($databases, 'name'));
                        $databases = array_combine($databases, $databases);
                    ?>
                    <?= $this->Form->select(
                            'target',
                            $databases,
                            ['value' => $default, 'empty' => 'Select database']
                        );
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row"><?= __('Table') ?></th>
                <td><?= $job->config['table'] ?></td>
            </tr>
            <?php if (!empty($job->config['scope'])): ?>
                <tr>
                    <th scope="row"><?= __('Scope') ?></th>
                    <td><?= $job->config['scope'] ?></td>
                </tr>
            <?php endif; ?>


            <?= $this->Table->getOptionRows($job->options ?? [], $job->config) ?>

            <tr>
                <td colspan="2">
                    <?= $this->Table->nestedTable(
                        Arrays::array_remove_keys($job->config['params'] ?? [], array_keys($job->options)),
                        ['key' => __('Parameters'), 'value' => '', 'tree' => true]);
                    ?>
                </td>
            </tr>

        </table>


    </div>
    <?= $this->Form->end() ?>

    <?php
        $this->Link->beginActionGroup('content');
        $this->Link->addCancelAction(['controller' => $this->request->getParam('controller'), 'action' => 'index', $scope]);
        $this->Link->addSubmitAction(__('Preview'), ['autofocus' => 'true', 'form' => 'form-transfer']);
    ?>

<?php elseif (isset($preview)): ?>

    <?= $this->element('../Transfer/preview') ?>

<?php endif; ?>
