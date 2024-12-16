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
use App\Model\Entity\Permission;
use App\View\AppView;
?>
<?php
/**
 * @var AppView $this
 * @var Permission $entity
 */
?>

<!-- Breadcrumbs -->
<?php
    $this->Breadcrumbs->add('Permissions');
    $this->Breadcrumbs->add(__('Create permission'));
?>

<!-- Content area -->
<div class="content-tight">
<?= $this->EntityInput->entityForm($entity, 'add') ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addAddCancelButton($entity);
?>
