<?php
/**
 * @var App\View\AppView $this
 * @var Epi\Model\Entity\Article[] $entities
 * @var Epi\Model\Entity\Property[] $lanes
 * @var App\Model\Entity\Databank $database
 */

use App\Model\Table\BaseTable;

?>

<div class="content-main widget-scrollbox" data-snippet="rows">

    <?php
      if ((BaseTable::$requestMode ?? MODE_DEFAULT) !== MODE_DEFAULT) {
        $url = ['?' => ['mode' => BaseTable::$requestMode]];
      } else {
        $url = [];
      }
    ?>

    <?php $this->Html->script('Widgets.plotly/plotly-2.8.3.min.js', ['block' => true]); ?>
    <?= $this->Element->outputHtmlElement(
        'div','',
        [
            'class' => 'widget-plot widget-filter-item widget-filter-item-template',
            'data-filter-group' => 'epi_articles',
            'data-filter-template' => 'timeline',
            'data-filter-mode' => $this->getConfig('options')['params']['mode'] ?? '',
            'data-api-url' => $this->Link->itemsUrl([
                'controller' => 'items',
                'action' => 'groups',
                'timeline',
                // TODO: Make itemtypes configurable
                '?' => ['itemtypes' => 'conditions'],
                '_ext' => 'json'
            ]),
            'data-index-url' => $this->Url->build(
                array_replace_recursive(
                    [
                        'action' => 'index',
                        '?' => ['template' => 'tiles', 'show' => 'content', 'flow' => 'frame', 'id' => '{rootId}']
                    ],
                    $url
                )
            ),
            'data-view-url' => $this->Url->build(
                array_replace_recursive(
                    [
                        'action'=>'view', '{rootId}'
                    ],
                     $url
                 )
            ),
            'data-scope' => 'timeline'
        ])
    ?>

</div>
