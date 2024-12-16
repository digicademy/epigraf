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
    use App\Model\Entity\Databank;
?>
<?php
/**
 * @var Databank $entity
 */
?>
<div class="message danger">
	<?= __('Danger zone! Operations on this page may severely damage content. Data can get lost forever!') ?>
</div>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Databanks'), ['action' => 'index', 'controller' => 'Databanks', 'database' => $this->request->getParam('database')]); ?>
<?php $this->Breadcrumbs->add($entity->captionPath); ?>


<!-- Content area -->
<div class="content-extratight">
    <?= $this->Form->create($entity,['id'=>'form-drop-databanks-' . $entity->id]) ?>
    <fieldset>
        <?= $this->Form->hidden('id'); ?>
        <p>
            <?= __('This will drop the database {0} on the server. All data will be deleted forever. Are you sure? Are you really sure?', $entity->name) ?>
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
    __('Yes'),
    [],
    ['linktype' => 'submit', 'form' => 'form-drop-databanks-' . $entity->id]
);
?>
