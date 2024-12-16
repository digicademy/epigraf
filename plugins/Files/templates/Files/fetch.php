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
 * @var Databank $database
 * @var string $root
 * @var string $path
 *
 */
?>
<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Files'), ['action' => 'index']); ?>
<?php if (!empty($root)) $this->Breadcrumbs->add($root); ?>
<?php if (!empty($path)) $this->Breadcrumbs->add($path); ?>

<?php $this->Breadcrumbs->add('Fetch file from URL'); ?>

<!-- Content area -->
<div class="content-extratight">
    <?= $this->Form->create(null,['id'=>'form-fetch']) ?>
    <fieldset>
        <?= $this->Form->control('File.url', ['type' => 'text', 'autofocus' => 'autofocus']); ?>
		<?= $this->Form->control('File.name', ['type' => 'text']); ?>
        <?= $this->Form->control('File.path', ['type' => 'hidden', 'value' => $path]); ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<!-- Actions -->
<?php $this->Link->beginActionGroup ('content'); ?>
<?php $this->Link->addAction(
    __('Cancel'),
    ['controller' => 'Files', 'action' => 'index','?' => ['root' => $root, 'path' => $path]],
    ['class' => 'button button_cancel']
);
?>
<?php
$this->Link->addAction(
    __('Fetch'),
    [],
    ['linktype' => 'submit', 'form' => 'form-fetch']
);
?>
