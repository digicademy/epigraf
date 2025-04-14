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
use App\Utilities\Converters\Attributes;
?>

<?php
/**
 * @var App\View\AppView $this
 * @var Epi\Model\Entity\Article[] $entities
 * @var \App\Model\Entity\Databank $database
 */
?>

<!-- Sidebar setup -->
<?php $this->setSidebarConfigByMode(); ?>

<!-- Left sidebar -->
<?= $this->element('../Articles/index_facets') ?>

<!-- Search -->
<?= $this->element('../Articles/index_search') ?>

<!-- Content area -->
<?php if ($this->request->getQuery('template') === 'lanes'): ?>
    <?= $this->element('../Articles/index_lanes') ?>
<?php elseif ($this->request->getQuery('template') === 'map'): ?>
    <?= $this->element('../Articles/index_map') ?>
<?php elseif ($this->request->getQuery('template') === 'tiles'): ?>
    <?= $this->element('../Articles/index_tiles') ?>
<?php else: ?>
    <?= $this->element('../Articles/index_table') ?>
<?php endif; ?>

<?php if (!empty($summary)): ?>
    <?php $this->start('footer'); ?>
    <div class="recordlist-summary" data-snippet="summary">
        <?= implode(' ', $summary) ?>
    </div>
    <?php $this->end(); ?>
<?php endif; ?>

<!-- Actions -->
<?php $queryparams = Attributes::paramsToQueryString($this->getConfig('options')['params'], ['action']); ?>

<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup('bottom');
    $this->Link->addCounter();

    // TODO: article type selection does not work yet
    $this->Link->addCreateAction(
        __('Create article'),
        ['action'=>'add','?'=> [
            'projects_id' => $this->getConfig('options')['params']['projects'][0] ?? '',
            'articletype' => $this->getConfig('options')['params']['articletypes'][0] ??
                $this->getConfig('options')['params']['articles.articletypes'][0] ?? '',
        ]]
    );
    $this->Link->addAction(__('Import'), ['controller' => 'Articles', 'action' => 'import']);

    $this->Link->addAction(
        __('Transfer'),
        ['controller' => 'Articles', 'action' => 'transfer', '?' => $queryparams],
        [
            'data-list-select' => 'epi_articles',
            'data-list-param' => 'articles', //id?
            'class' => 'popup',
            'data-popup-modal' => true
        ]
    );

    $this->Link->addAction(__('Mutate'),
        ['controller' => 'Articles', 'action' => 'mutate', '?' => $queryparams],
        [
            'data-list-select' => 'epi_articles',
            'data-list-param' => 'articles', //id?
            'class' => 'popup',
            'data-popup-modal' => true
        ]
    );
?>

<?php
    $this->Link->beginActionGroup('bottom-right');

    $this->Link->toggleTemplates($queryparams, 'articles');
    $this->Link->toggleModes($queryparams);
    $this->Link->exportButtons($queryparams);
    $this->Link->downloadButtons(null,'articles', 'epi_articles', ['triples'=>true]);
?>
