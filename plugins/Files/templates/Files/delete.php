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
 * @var \App\View\AppView $this
 * @var \Files\Model\Entity\FileRecord $entity
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Files'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity->root); ?>
<?php $this->Breadcrumbs->add($entity->path); ?>
<?php $this->Breadcrumbs->add($entity->name); ?>

<!-- Content area -->
<div class="message danger">
    <?= __('Danger zone! Operations on this page may severely damage content. Data can get lost forever!') ?>
</div>

<?php
    if (!$entity->deleted) {
        $formOptions = [
            'confirm' => __(
                'This will delete the file or folder "{0}" on the server. All data will be deleted forever. Are you sure? Are you really sure?',
                $entity->captionPath
            )
        ];
    }
    else {
        $formOptions = [];
    }
?>
<?= $this->EntityInput->entityForm($entity, 'delete', $formOptions) ?>

