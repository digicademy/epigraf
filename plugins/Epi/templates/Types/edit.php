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
 * @var \Epi\Model\Entity\Type $entity
 */

use Cake\Utility\Inflector;

?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Types'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add(Inflector::humanize($entity->scope), ['action' => 'index','?'=>['scopes'=>$entity->scope]]); ?>
<?php $this->Breadcrumbs->add($entity->caption); ?>

<!-- Content area -->
<div class="content-main widget-scrollbox">
    <?= $this->EntityInput->entityForm($entity, 'edit') ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addSaveCancelDeleteButton($entity);
?>
