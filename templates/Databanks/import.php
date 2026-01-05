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
use App\View\AppView;
use App\Model\Entity\Databank;
?>
<?php
/**
 * @var AppView $this
 * @var Databank $entity
 */
?>

<div class="message danger ">
	<?= __('Danger zone! Operations on this page may severely damage content. Data can get lost forever!') ?>
</div>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Databanks'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity->captionPath); ?>
<?php $this->Breadcrumbs->add(__('Import SQL')); ?>

<!-- Content area -->
<div class="content-extratight">
    <?= $this->Form->create($entity, ['id' => 'form-import-databanks-' . $entity->id]) ?>
    <fieldset>
        <?= $this->Form->control(
            'filename',
            [
                'type' => 'choose',
                'itemtype'=>'file',
                'options' => [
                    'plugin' => 'Epi',
                    'database' => $entity->name,
                    'controller' => 'Files',
                    'action' => 'select',
                    '?' => ['root' => 'root', 'path' => 'backup']
                ]
            ]); ?>
        <p>
            The file format may be plain text or gzip. Multiline comments are not allowed.
        </p>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

    <!-- Actions -->
<?php $this->Link->beginActionGroup ('content'); ?>
<?php $this->Link->addAction(
    __('Cancel'),
    ['controller' => 'Databanks', 'action' => 'view', $entity->id],
    ['class' => 'button button_cancel']
);
?>
<?php
$this->Link->addAction(
    __('Importieren'),
    [],
    [
        'linktype' => 'submit',
        'form' => 'form-import-databanks-' . $entity->id,
        'confirm' => __('Are you sure about importing this script? This may alter the database.')
    ]
);
?>
