<?php

use Cake\Routing\Router;
use Cake\Utility\Hash;
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

    <?= $this->Table->filterSearch(
        $tableGroup,
        "articles.",
        "term",
        $this->getConfig('options')['params']['term'] ?? '',
        [
            'field'=> $this->getConfig('options')['params']['field'] ?? '',
            'options' => $this->getConfig('options')['filter']['search'] ?? []
        ],
        [
            "class" => "content-searchbar-item-main",
            "label" => __('Search'),
            'placeholder' => __('Search articles'),
            'autofocus' => true,
            'form' => Router::url(['controller' => 'Articles', 'action' => 'index'])
        ]
    ) ?>

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
