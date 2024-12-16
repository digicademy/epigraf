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
<div class="message danger">
	<?= __('Danger zone! Operations on this page may severely damage content. Data can get lost forever!') ?>
</div>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Databanks'), ['action' => 'index', 'controller' => 'Databanks', 'database' => $this->request->getParam('database')]); ?>
<?php $this->Breadcrumbs->add($entity->captionPath); ?>

<!-- Content area -->
<div class="content-extratight">
    <?= $this->Form->create($entity, ['id' => 'form-init-databanks-' . $entity->id]) ?>
    <fieldset>
        <?= $this->Form->hidden('id'); ?>
        <p>
            <?= __('This will init the database {0} on the server with a blank table structure. Are you sure?', $entity->name) ?>
        </p>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<!-- Actions -->
<?php $this->Link->beginActionGroup ('content'); ?>
<?php
$this->Link->addAction(
    __('Cancel'),
    ['controller' => 'Databanks', 'action' => 'view', $entity->id],
    ['class' => 'button button_cancel']
);

$this->Link->addAction(
    __('Yes'),
    [],
    ['linktype' => 'submit', 'form' => 'form-init-databanks-' . $entity->id]
);
?>
