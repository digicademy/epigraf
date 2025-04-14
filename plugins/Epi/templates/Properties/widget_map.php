<?php
/**
 * @var App\Model\Entity\Databank $database
 *
 * @var array $entities Passed from BaseEntityHelper::entityWidgets()
 * @var string $rowTable Passed from BaseEntityHelper::entityWidgets()
 * @var string $rowType Passed from BaseEntityHelper::entityWidgets()
 * @var string $fieldName Passed from BaseEntityHelper::entityWidgets()
 * @var boolean $edit Passed from BaseEntityHelper::entityWidgets()
 */
?>

<div class="widget-map-container<?= empty($entities) ? ' widget-map-container-empty' : ''?>">
    <?= $this->Element->outputHtmlElement(
        'div', '',
        [
            'class' => 'widget-map',
            'data-mode' => 'view',
            'data-row-table' => $rowTable,
            'data-row-types' => $rowType,
            'data-field-name' => $fieldName,
            'data-search-text' => $searchText ?? '',
            'data-show-number' => '0'
        ]
    ) ?>
    <script class="widget-map-data" type="application/json" data-snippet="map-data">
        <?php
            $geoJson = [];
            foreach ($entities as $entity) {
                $geoJson[] = $entity ? $entity->getValueFormatted($fieldName, ['format' => 'html', 'default' => true]) : [];
            }
        ?>
        [<?= implode(',',$geoJson) ?>]
        </script>
</div>

