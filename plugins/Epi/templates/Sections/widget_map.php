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
    $itemConfig = $template_section['view']['widgets']['map']['itemtypes'] ?? [];
    $editTypes = array_keys(array_filter($itemConfig, fn($itemType) => $itemType['edit'] ?? false));
    $itemTypes = array_keys($itemConfig);

    $segmentNames = array_combine(
        $itemTypes,
        array_map(fn($itemType) => $itemConfig[$itemType]['segment'] ?? $itemType, $itemTypes)
    );
    $segmentNos = array_flip($itemTypes);
    $geoItems =  array_filter($section->items, fn($item) => in_array($item->itemtype, $itemTypes));

    $geoJson = [];
    foreach ($geoItems as $item) {
        $fieldName = $itemConfig[$item->itemtype]['field'] ?? 'value';
        $editMarker = ($edit ?? false) && ($itemConfig[$item->itemtype]['edit'] ?? false);
        $extraData = [
            'id' => $item->id,
            'segment' => $segmentNos[$item->itemtype] + 1,
            'sortno' => $item->sortno ?? 0,
            'edit' => $editMarker
        ];
        $geoJsonValue = $item->getValueNested($fieldName, ['format' => 'html', 'geodata' => $extraData]);
        if (!empty($geoJsonValue)) {
            $geoJson[] = $geoJsonValue;
        }
    }

    $hasGeodata = !empty($geoJson);
?>
<?php if ($hasGeodata || ($edit ?? false)): ?>
    <div class="widget-map-container<?= $hasGeodata ? '' : ' widget-map-container-empty'?>">
        <?= $this->Element->outputHtmlElement(
            'div','',
            [
                'class' => 'widget-map',
                'data-mode' => 'view',
                'data-row-types' => implode(',', $itemTypes),
                'data-edit-types' => implode(',', $editTypes),
                'data-segments' => implode(',', $segmentNames)
            ],
        ) ?>
        <script class="widget-map-data" type="application/json" data-snippet="map-data">
            [<?= implode(',',$geoJson) ?>]
            </script>
    </div>
<?php endif; ?>
