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
use Cake\Routing\Router;
?>

<?php
/**
 * @var App\View\AppView $this
 * @var \App\Model\Entity\Databank $database
 */
?>

<?php if ($this->getShowBlock('searchbar')): ?>

<?php
    $selectTemplate = $this->request->getQuery('template') === 'choose';
    $tableGroup = 'epi_articles';
    if ($selectTemplate) {
        $tableGroup .= '_choose';
    }
?>
<div class="content-searchbar">

    <?php $fullTextDefault = $this->User->hasRole(['guest']); ?>
    <?= $this->Table->filterSearch(
        $tableGroup,
        "articles.",
        "term",
        $this->getConfig('options')['params']['term'] ?? '',
        [
            'field'=> $this->getConfig('options')['params']['field'] ?? ($fullTextDefault ? 'text' : ''),
            'options' => $this->getConfig('options')['filter']['search'] ?? []
        ],
        [
            "class" => "content-searchbar-item-main",
            "label" => __('Search'),
            'placeholder' => $fullTextDefault ?  __('Search articles, e.g. "grandin"') :  __('Search articles'),
            'autofocus' => true,
            'form' => Router::url(['controller' => 'Articles', 'action' => 'index'])
        ]
    ) ?>

    <!--div class="content-searchbar-item content-searchbar-item-sub">
        <div class="widget-filter-item widget-filter-item-timeline"
             data-filter-group="<?= $tableGroup ?>"
             data-filter-url="items/groups/periods?template=select&itemtypes=dates"
             data-filter-caption="<?= __('Timeline') ?>"
             data-filter-selected="<?= implode(',',$this->getConfig('options')['params']['years'] ?? []) ?>"
        >
            <div class="widget-filter-item-timeline-results" data-snippet="items">
            </div>
        </div>
    </div-->

    <?php if (!empty($this->getConfig('options')['filter']['date']['itemtypes'])): ?>
    <?= $this->Table->filterSearch(
        $tableGroup,
        "articles.",
        "date",
        $this->getConfig('options')['filter']['date']['normalized'] ?? '',
        false,
        [
            'class' => 'content-searchbar-item',
            'data' => ['data-snippet' => 'datesearch'],
            'label' => __('Date'),
            'placeholder' => __('Search by date range, e.g. 16. Jh. or 1430-1450'),
            'autofocus' => true
        ]
    ) ?>
    <?php endif; ?>

    <div class="content-searchbar-item content-searchbar-item-sub show-small">
        <div class="input-group">
            <button  class="accordion-toggle" data-toggle-accordion="sidebar-left">
                <?= __('Filter') ?>
            </button>
        </div>
    </div>

    <?php // $this->Table->filterReset("articles") ?>

</div>
<?php endif; ?>

<?php if ($this->getShowBlock('legend')): ?>
    <div class="content-legend" data-snippet="legend">
        <?php $properties =  $this->getConfig('options')['filter']['facets'] ?? []; ?>
        <?php foreach ($properties ?? [] as $property): ?>
            <?=
                $this->Element->outputHtmlElement(
                    'div',
                    h($property['path']),
                    [
                        'class' => 'content-legend-item',
                        'style' => empty($property['color']) ? ''  : ('background-color: ' . $property['color'] . ';'),
                        'data-legend-color' => $property['color'] ?? null,
                        'data-legend-id' => $property['id'] ?? null
                    ]
                )
            ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
