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
use App\View\AppView;
use Epi\Model\Entity\Property;
?>

<?php
/**
 * @var \App\View\AppView $this
 * @var \Epi\Model\Entity\Property $entity
 **/
?>

<!-- Breadcrumbs -->
<?php
    $this->Breadcrumbs->add(__('Categories'), ['action' => 'index']);
    $this->Breadcrumbs->add($entity['type']['caption'] ?: $entity->propertytype);
?>


<!-- Content area -->
<div class="content-large">
    <?= $this->EntityInput->entityForm($entity, 'add', [], true) ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addAddCancelButton($entity);
?>

