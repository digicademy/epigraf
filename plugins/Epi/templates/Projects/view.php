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
 * @var \Epi\Model\Entity\Project $entity
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Projects'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity->captionPath);?>

<!-- Content area -->
<?= $this->EntityHtml->entityForm($entity, 'view', [], true) ?>


<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addAction(
        __('Show articles'),
        ['plugin'=>'epi','controller'=>'articles','action' => 'index','?'=>['projects'=>$entity->id,'load'=>true]],
        ['data-target'=>'main','data-role' => 'open']
    );

    $this->Link->addEditButtons($entity);
    $this->Link->downloadButtons();
?>
