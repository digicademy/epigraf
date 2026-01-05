<?php
/**
 * @var App\View\AppView $this
 * @var Epi\Model\Entity\Article[] $entities
 * @var App\Model\Entity\Databank $database
 */
?>


    <div class="recordlist widget-tiles widget-filter-item widget-filter-item-template"
         data-filter-group="epi_articles"
         data-sortdir="<?= $this->Paginator->sortDir() ?>"
         data-sortkey="<?= $this->Paginator->sortKey() ?>"
         data-filter-template="tiles"
         data-filter-mode="<?= $this->getConfig('options')['params']['mode'] ?? '' ?>"
         data-list-name="lanes">

        <?php $lanes = $this->getConfig('options')['filter']['lanes'] ?? [] ?>
        <?php foreach ($lanes as $laneProperty): ?>

            <div class="widget-lane" data-list-itemof="lanes">
                <div class="lane-title">
                    <?= $laneProperty->path ?>
                </div>

                <?php $laneNextPageUrl = $this->Link->laneNextPageUrl(['lane' => $laneProperty->id], empty(($this->getConfig('options')['params']['lane'] ?? [])) ); ?>

                <div class="lane-container widget-scrollbox widget-scrollbox-horizontal">
                    <div class="lane-list"
                         data-list-name="lane_<?= $laneProperty->id ?>"
                         data-list-action-next="<?= $laneNextPageUrl ?>">

                        <?php if ($laneProperty->file_exists): ?>
                            <div class="lane-tile lane-tile-first">
                                <div class="lane-tile-image">
                                    <?= $this->Files->outputThumb(
                                        $laneProperty->file_downloadname,
                                        $laneProperty->file_downloadpath,
                                        $database['name'],
                                        ''
                                    ); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php foreach ($entities as $entity): ?>
                            <?php if ($entity->hasProperty($laneProperty->id)): ?>
                                <?= $this->element('../Articles/tile',['entity' => $entity, 'datalist' => 'lane_' . $laneProperty->id]) ?>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
