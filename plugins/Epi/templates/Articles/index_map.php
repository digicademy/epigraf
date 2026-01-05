<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

use Epi\Model\Table\BaseTable;

?>
<?php
/**
 * @var App\View\AppView $this
 * @var \Epi\Model\Entity\Article[] $entities
 */

?>
<?php
    // The item types are passed to map.js to query the correct items
    $itemTypes = $this->getConfig('options')['filter']['geodata'] ?? [];
    $properties = $this->getConfig('options')['params']['properties'] ?? [];
    $propertyTypes = array_keys($properties);

    if ((BaseTable::$requestMode ?? MODE_DEFAULT) !== MODE_DEFAULT) {
        $url = ['?' => ['mode' => BaseTable::$requestMode]];
    } else {
        $url = [];
    }

?>

<div class="content-main">
    <div class="widget-map widget-filter-item widget-filter-item-map widget-filter-item-template"
         data-filter-group="epi_articles"
         data-filter-template="map"
         data-filter-mode="<?= $this->getConfig('options')['params']['mode'] ?? '' ?>"
         data-filter-properties="<?= implode(',', $propertyTypes) ?>"
         data-row-types = "<?= implode(',', array_keys($itemTypes)) ?>"
         data-mode="search"
         data-index-url="<?= $this->Url->build(array_replace_recursive(['action'=>'index', '?'=>['template' => 'tiles','show'=>'content','flow'=>'frame','id' => '{rootId}']], $url)) ?>"
         data-view-url="<?= $this->Url->build(array_replace_recursive(['action'=>'view', '{rootId}'], $url)) ?>"
    ></div>

    <script class="widget-map-data"
            type="application/json"
            data-snippet="map-data"
            data-hasmore="<?=  $this->Paginator->hasNext() ? 'true' : 'false' ?>"
            data-url="<?= $this->Link->nextPageUrl(null, true) ?>"
            data-group-url="<?= $this->Url->build(array_replace_recursive(['controller'=>'items','action'=>'groups','tiles'], $url)) ?>"
            data-location-lat="<?= $this->getConfig('options')['params']['lat'] ?? '' ?>"
            data-location-lng="<?= $this->getConfig('options')['params']['lng'] ?? '' ?>"
            data-location-zoom="<?= $this->getConfig('options')['params']['zoom'] ?? '' ?>"
    >
        <?php
            $geoJson = [];
            foreach ($entities as $article) {
                $geoJson = array_merge($geoJson, $article->getExportGeoData(['format' => 'html','setRoot' => true, 'properties' => $properties]));
            }
        ?>
        [<?= implode(',',$geoJson) ?>]
    </script>
</div>
