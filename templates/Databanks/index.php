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
 * @var App\Model\Entity\Databank[] $entities
 * @var array $columns
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Project databases')); ?>

<div class="content-searchbar">
    <?= $this->Table->filterBar('databanks') ?>
</div>

<!-- Content area -->
<div class="content-main widget-scrollbox">
    <?=
      $this->Table->filterTable(
          'databanks',
          $entities,
          [
              'actions' => [
                  'view' => true,
                  'open' => [
                      'title' => __('Show articles'),
                      'plugin'=>'epi',
                      'controller' => 'Articles',
                      'database' =>'{caption}',
                      'action' => 'index',
                      '?'=>['load'=>true]
                  ]
              ]
          ]
      )
    ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup('bottom');
    $this->Link->addCreateAction(__('Create database'));
    $this->Link->downloadButtons (null, 'id', 'databanks');
?>
