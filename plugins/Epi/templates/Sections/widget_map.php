<?php
/**
 * @var App\Model\Entity\Databank $database
 * @var Epi\Model\Entity\Article $article
 * @var Epi\Model\Entity\Section $section
 *
 * @var boolean $edit Passed from view.php
 * @var string $mode Passed from view.php
 *
 * @var array $template_article Passed from view.php
 * @var array $template_section Passed from view.php
 */
?>
<?php
  $itemtype = $template_section['view']['widgets']['map']['itemtype'] ?? 'geolocations';
  $geodata = collection($section->items)->filter(fn($item) => $item->itemtype === $itemtype );
  $hasGeodata = $geodata->count() > 0;
?>
<?php if ($hasGeodata|| ($edit ?? false)): ?>
    <div class="widget-map-container<?= $hasGeodata ? '' : ' widget-map-container-empty'?>">
        <div class="widget-map" data-mode="view" data-itemtype="<?= $itemtype ?>"></div>
        <script class="widget-map-data" type="application/json" data-snippet="map-data">
            <?php
                $geoJson = [];
                foreach ($geodata as $item) {
                    $geoJson[] = $item ? $item->getValueFormatted('value', ['format' => 'html']) : [];
                }
            ?>
            [<?= implode(',',$geoJson) ?>]
            </script>
    </div>
<?php endif; ?>
