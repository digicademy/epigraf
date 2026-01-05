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
<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Databanks'), ['action' => 'index', 'controller' => 'Databanks', 'database' => $this->request->getParam('database')]); ?>
<?php $this->Breadcrumbs->add($entity['caption']); ?>

<!-- Content area -->
<?= $this->EntityHtml->entityForm($entity, 'view') ?>


<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    if ($entity->available) {
        $this->Link->addAction(
            __('Show articles'),
            ['plugin'=>'epi','controller'=>'articles','action' => 'index','database' => $entity->caption],
            ['data-target'=>'main','data-role' => 'open']
        );
    }
    $this->Link->addEditAction (
        ['action' => 'edit', $entity->id],
        'databanks-' . $entity->id
    );

    if (!$entity->available) {
        $this->Link->addAction(
            __('Create database'),
            ['action' => 'create', $entity->id],
            ['data-role'=>'add']
        );
    }
    if ($entity->available && $entity->isempty) {
        $this->Link->addAction(
            __('Init Database'),
            ['action' => 'init', $entity->id]
        );

    }

    if ($entity->available) {
        $this->Link->addAction(
            __('Backup Database'),
            ['action' => 'backup', $entity->id]
        );


        $this->Link->addAction(
            __('Import Backup'),
            ['action' => 'import', $entity->id]
        );

        $this->Link->addAction(
            __('Drop Database'),
            ['action' => 'drop', $entity->id]
        );

        $this->Link->addAction(
            __('Import Data'),
            ['plugin' =>'epi','database'=>$entity->caption,'controller'=>'projects','action' => 'import']
        );
    }

    // TODO: implement download buttons
    //$this->Link->downloadButtons();
?>
