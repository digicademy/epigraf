<?php
/**
 * @var App\View\AppView $this
 * @var Epi\Model\Entity\Article[] $entities
 * @var Epi\Model\Entity\Property[] $lanes
 * @var App\Model\Entity\Databank $database
 */
?>

<?php $this->append('css', $this->Types->getTagStyles()); ?>

<div class="content-main widget-scrollbox" data-snippet="rows">

    <?php if (!empty($this->getConfig('options')['params']['lanes'])): ?>
        <?= $this->element('../Articles/lanes') ?>
    <?php else: ?>
        <?php $nexturl = $this->Link->nextPageUrl(); ?>
        <div class="recordlist tiles-list widget-filter-item widget-filter-item-template"
             data-filter-group="epi_articles"
             data-sortdir="<?= $this->Paginator->sortDir() ?>"
             data-sortkey="<?= $this->Paginator->sortKey() ?>"
             data-filter-template="tiles"
             data-filter-mode="<?= $this->getConfig('options')['params']['mode'] ?? '' ?>"
             data-list-action-next="<?= $nexturl ?>"
             data-list-name="tiles">

            <?php foreach ($entities as $entity): ?>
                <?= $this->element('../Articles/tile',['entity' => $entity]) ?>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>
</div>
