<?php
// Layout: numbers or content (configure e.g. in the articletype 'di-article'
use Cake\Routing\Router;
use Cake\Utility\Inflector;

/**
 * @var \Epi\Model\Entity\Article $entity
 */
?>

<?php
    $tileLayout = $entity->type['merged']['preview']['layout'] ?? 'content';
    $previewText = $entity->preview;
    $previewImage = $entity->thumb;

    $columns = $entity->type['merged']['preview']['summary'] ?? [];
    $columns =  $entity->getColumns($columns);
    $columns = array_slice($columns,0,3);
?>

<div class="lane-tile lane-tile-<?= $entity->articletype ?>"
     data-id="<?= $entity->id ?>"
     data-list-itemof="tiles">

    <?php if ($this->request->getQuery('link', 'internal') === 'external'): ?>
        <a href="<?= $entity->url ?>" target="_blank">
    <?php else: ?>
        <a href="<?= Router::url($this->Link->openUrl([$entity->id])) ?>" class="frame">
    <?php endif; ?>

        <!-- Template with numbers, no content to show -->
        <?php if ($tileLayout === 'numbers'): ?>
            <?php if (!empty($previewImage)): ?>
                <div class="lane-tile-image">
                    <img src="<?= $previewImage['src'] ?? '' ?>" alt="<?= $previewImage['caption'] ?? '' ?>" >
                </div>
            <?php endif; ?>
            <div class="lane-tile-content">
                <div class="lane-tile-content-caption">
                    <?php foreach($columns as $column): ?>
                        <?php
                            $column['format'] = 'html';
                            $column['aggregate'] = $column['aggregate'] ?? 'collapse';
                            $value = $entity->getValueNested($column['key'], $column);
                        ?>
                        <?= $column['prefix'] ?? '' ?>
                        <?= h(is_array($value) ? json_encode($value) : $value) ?>
                        <br>
                    <?php endforeach; ?>
                </div>
                <div class="lane-tile-content-text">
                    <?= $previewText ?>
                </div>

            </div>

        <!-- Template with content -->
        <?php else: ?>
            <?php if (!empty($previewImage)): ?>
                <div class="lane-tile-image">
                    <img src="<?= $previewImage['src'] ?? '' ?>" alt="<?= $previewImage['caption'] ?? '' ?>" >
                </div>
            <?php elseif (!empty($previewText)): ?>
                <div class="lane-tile-text">
                    <?= $previewText ?>
                </div>
            <?php endif; ?>

            <div class="lane-tile-metadata">
                <?php foreach($columns as $column): ?>
                    <?php
                        $column['format'] = 'html';
                        $column['aggregate'] = $column['aggregate'] ?? 'collapse';
                        $value = $entity->getValueNested($column['key'], $column);
                    ?>
                    <?php if (!empty($value)): ?>
                        <span class="lane-tile-icon lane-tile-icon-<?= Inflector::underscore($column['name'] ?? '' ) ?>"><?= $column['icon'] ?? '' ?></span>
                        <?= h(is_array($value) ? json_encode($value) : $value) ?>
                        <br>
                    <?php endif; ?>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>
    </a>
</div>
