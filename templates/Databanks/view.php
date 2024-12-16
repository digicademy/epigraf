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
 * @var Databank $databank
 */
?>
<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Databanks'), ['action' => 'index', 'controller' => 'Databanks', 'database' => $this->request->getParam('database')]); ?>
<?php $this->Breadcrumbs->add($databank['caption']); ?>

<!-- Content area -->
<?= $this->EntityHtml->entityForm($databank, 'view') ?>


<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    if ($databank->available) {
        $this->Link->addAction(
            __('Show articles'),
            ['plugin'=>'epi','controller'=>'articles','action' => 'index','database' => $databank->caption],
            ['data-target'=>'main','data-role' => 'open']
        );
    }
    $this->Link->addEditAction (
        ['action' => 'edit', $databank->id],
        'databanks-' . $databank->id
    );

    if (!$databank->available) {
        $this->Link->addAction(
            __('Create database'),
            ['action' => 'create', $databank->id],
            ['data-role'=>'add']
        );
    }
    if ($databank->available && $databank->isempty) {
        $this->Link->addAction(
            __('Init Database'),
            ['action' => 'init', $databank->id]
        );
    }
    if ($databank->available) {
        $this->Link->addAction(
            __('Backup Database'),
            ['action' => 'backup', $databank->id]
        );

        $this->Link->addAction(
            __('Drop Database'),
            ['action' => 'drop', $databank->id]
        );

        $this->Link->addAction(
            __('Import Script'),
            ['action' => 'import', $databank->id]
        );

        $this->Link->addAction(
            __('Import Data'),
            ['plugin' =>'epi','database'=>$databank->caption,'controller'=>'projects','action' => 'import']
        );
    }

    // TODO: implement download buttons
    //$this->Link->downloadButtons();
?>
