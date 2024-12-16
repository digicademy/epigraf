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
/**
 * @var array $preview
 * @var string $scope
 * @var $database \App\Model\Entity\Databank
 * @var $this \App\View\AppView
 */
?>

<!-- Breadcrumbs -->
<?php if( !empty($source)): ?>
    <?php $this->Breadcrumbs->add(__('Import {0} into {1}' , [$source, $database->caption])) ?>
<?php else: ?>
    <?php $this->Breadcrumbs->add(__('Import')); ?>
<?php endif; ?>

<!-- Content area -->
<?php if (!isset($preview)): ?>

    <div class="content-extratight">
        <?= $this->Form->create(null,['id'=>'form-import','type'=>'get']) ?>
        <fieldset>
            <?= $this->Form->control(
                'filename',
                [
                    'type' => 'choose',
                    'itemtype'=>'file',
                    'options' => ['controller' => 'Files', 'action' => 'select','?'=>['path'=>'import']]
                ]); ?>
        </fieldset>

        <?= $this->Form->button(__('Preview')) ?>
        <?= $this->Html->link(__('Cancel'), [ 'action' => 'index',$scope ?? null], ['class' => 'button button_cancel']) ?>
        <?= $this->Form->end() ?>
    </div>

<?php else: ?>

    <?= $this->element('../Transfer/preview') ?>

<?php endif; ?>
