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
 * @var \Files\Model\Entity\FileRecord $folder
 *
 */
?>
<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Files'), ['action' => 'index']); ?>
<?php if (!empty($root)) $this->Breadcrumbs->add($root); ?>
<?php if (!empty($path)) $this->Breadcrumbs->add($path); ?>

<?php $this->Breadcrumbs->add('Create folder'); ?>

<!-- Content area -->
<div class="content-extratight">
    <?= $this->Form->create($folder,['id'=>'form-add-files']) ?>
    <fieldset>
        <?= $this->Form->control('name', ['type' => 'text', 'autofocus' => 'autofocus']); ?>
        <?= $this->Form->control('path', ['type' => 'hidden', 'value' => $path]); ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<!-- Actions -->
<?php
    $this->Link->beginActionGroup ('content');
    $this->Link->addCancelAction(
        ['controller' => 'Files', 'action' => 'index','?' => ['root' => $root, 'path' => $path]]
    );
    $this->Link->addSaveButton('add', 'files');
 ?>
