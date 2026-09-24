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
use App\Model\Table\BaseTable;
?>

<?php
/**
 * @var App\View\AppView $this
 * @var Epi\Model\Entity\Article[] $entities
 * @var Epi\Model\Entity\Property[] $lanes
 * @var App\Model\Entity\Databank $database
 */
?>

<div class="content-main widget-scrollbox" data-snippet="rows">


    <?= $this->Element->outputHtmlElement(
        'div','',
        [
            'class' => 'widget-plot widget-filter-item widget-filter-item-template',
            'data-filter-group' => 'epi_articles',
            'data-filter-template' => 'graph',
            'data-filter-mode' => $this->getConfig('options')['params']['mode'] ?? '',
            'data-image-base-url' => "/services/img/file?size=100&url=https://www.inschriften.net/fileadmin/",
            'data-api-url' => $this->Link->itemsUrl([
                'controller' => 'items',
                'action' => 'groups',
                'graph',
                '?' => ['images' => 'dio-images'],
                '_ext' => 'json'
            ]),
            'data-view-url' => $this->Url->build([
                'controller' => '{type}',
                'action' => 'view',
                '{dbid}',
                // TODO: Pass request mode in routing config?
                '?' => ['mode' => BaseTable::$requestMode]
            ]),
            'data-scope' => 'graph'
        ])
    ?>

</div>
