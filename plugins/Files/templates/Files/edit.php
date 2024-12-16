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
 * @var \App\Model\Entity\FileRecord $entity
 *
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Files'), ['action' => 'index']); ?>
<?php if (!empty($root)) $this->Breadcrumbs->add($root); ?>
<?php if (!empty($path)) $this->Breadcrumbs->add($path); ?>

<?php if (!empty($entity['name'])) $this->Breadcrumbs->add($entity['name']); ?>

<!-- Content area -->

<div class="content-tight">
    <?= $this->EntityInput->entityForm($entity, 'edit', ['type'=>'file']) ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addSaveCancelDeleteButton($entity);
?>
