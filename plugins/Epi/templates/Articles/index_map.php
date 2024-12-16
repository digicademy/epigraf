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
 * @var App\View\AppView $this
 * @var \Epi\Model\Entity\Article[] $entities
 */

use Cake\Routing\Router;

?>

<div class="content-main">
    <div class="widget-map widget-filter-item widget-filter-item-map widget-filter-item-template"
         data-filter-group="epi_articles"
         data-filter-template="map"
         data-filter-mode="<?= $this->getConfig('options')['params']['mode'] ?? '' ?>"
         data-filter-lanes="<?= $this->getConfig('options')['params']['lanes'] ?? '' ?>"
         data-mode="search"
    ></div>

    <script class="widget-map-data"
            type="application/json"
            data-snippet="map-data"
            data-hasmore="<?=  $this->Paginator->hasNext() ? 'true' : 'false' ?>"
            data-url="<?= $this->Link->nextPageUrl(null, true) ?>"
            data-location-lat="<?= $this->getConfig('options')['params']['lat'] ?? '' ?>"
            data-location-lng="<?= $this->getConfig('options')['params']['lng'] ?? '' ?>"
            data-location-zoom="<?= $this->getConfig('options')['params']['zoom'] ?? '' ?>"
    >

        <?php
        $geoJson = [];
        foreach ($entities as $article) {
            foreach ($article->getItemsByType(['geolocations']) ?? [] as $item) {
                $geoJson[] = $item ? $item->getValueFormatted('value') : [];
            }
        }
        ?>
        [<?= implode(',',$geoJson) ?>]
    </script>
</div>
