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
use App\Utilities\Converters\Attributes;

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

    <?php
      if ((BaseTable::$requestMode ?? MODE_DEFAULT) !== MODE_DEFAULT) {
        $url = ['?' => ['mode' => BaseTable::$requestMode]];
      } else {
        $url = [];
      }
    ?>

    <?php $this->Html->script('Widgets.plotly/plotly-3.6.0.min.js', ['block' => true]); ?>

    <?= $this->Element->outputHtmlElement(
        'div','',
        [
            'class' => 'widget-plot widget-filter-item widget-filter-item-plot widget-filter-item-template',
            'data-filter-group' => 'epi_articles',
            'data-filter-template' => 'timeline',
            'data-filter-mode' => $this->getConfig('options')['params']['mode'] ?? '',
            'data-plot-zoom' => $this->getConfig('options')['params']['zoom'] ?? '100',
            'data-api-url' => $this->Link->itemsUrl([
                'controller' => 'items',
                'action' => 'groups',
                'timeline',
                '?' => ['itemtypes' => implode(",", $this->getConfig('options')['filter']['date']['itemtypes'] ?? [])],
                '_ext' => 'json'
            ]),
            'data-index-url' => $this->Url->build(
                array_replace_recursive(
                    [
                        'action' => 'index',
                        '?' => array_replace(
                            Attributes::paramsToQueryString($this->getConfig('options')['params'], ['action']),
                            [
                                'template' => 'tiles', 'show' => 'content', 'flow' => 'frame',
                                'datestart' => '{datestart}',
                                'properties.{propertytype}.selected' => '{properties}',
                                'properties.{propertytype}.flags' => ''
                            ]
                        )
                    ],
                    $url
                ),
                ['escape' => false]
            ),
            'data-view-url' => $this->Url->build(
                array_replace_recursive(
                    [
                        'action'=>'view', '{rootId}'
                    ],
                     $url
                 ),
                ['escape' => false]
            ),
            'data-scope' => 'timeline'
        ])
    ?>

</div>
