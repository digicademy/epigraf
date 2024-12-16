<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Databank $database
 * @var string $user_role
 */

use Cake\Utility\Hash;

?>
<?php
    $selectTemplate = $this->request->getQuery('template') === 'choose';
    $tableGroup = 'epi_articles';
    if ($selectTemplate) {
        $tableGroup .= '_choose';
    }
?>
<!-- Project facets -->
<?php
    $this->beginTabsheet(
        __('Projects'),
        'filter-projects',
        'left',
        ['role'=>'fixed']
    );
?>

    <div class="widget-filter-item widget-filter-item-projects widget-filter-item-facets-container"
         data-filter-group="<?= $tableGroup ?>"
         data-filter-action="projects/index?template=select"
         data-filter-caption="<?= __('Projects') ?>"
         data-filter-selected="<?= implode(',',$this->getConfig('options')['params']['projects'] ?? []) ?>"
    >

        <div class="widget-filter-facets-results widget-scrollbox">
            <ul class="widget-tree" data-snippet="rows"></ul>
        </div>

        <div class="widget-filter-facets-filter">
            <input class="widget-filter-facets-term" type="text" placeholder="<?= __('Filter projects') ?>">
            <button class="widget-filter-facets-reset" type="button" title="<?= __('Reset filter') ?>" aria-label="<?= __('Reset filter') ?>">&#xe17b;</button>
        </div>

        <?= $this->Table->filterSelector(
            $tableGroup,
            "articles.articletypes",
            //TODO: only types occuring in the filtered set of articles
            Hash::combine($database->types, 'articles.{*}.name', 'articles.{*}.caption'),
            $this->getConfig('options')['params']['articletypes'] ?? [],
            [
                'label' =>__('Article types'),
                'reset' => true,
                'checkboxlist' => true
            ]
        ) ?>

        <?php if (in_array($user_role, ['devel', 'admin'])): ?>
        <?= $this->Table->filterSelector(
                $tableGroup,
            "articles.published",

            $database->publishedOptions,

            $this->getConfig('options')['params']['published'] ?? [],
            [
                'label' =>__('Article state'),
                'reset' => true,
                'checkboxlist' => true
            ]
        ) ?>
        <?php endif; ?>
    </div>

<?php $this->endTabsheet(); ?>


<!-- Property facets -->
<?php
    $this->addTabsheetSelector('left',
        $database->getGroupedTypes('properties', 'name', 'caption', 'category')
    );

    // Get selected properties
    $propertyType = $this->getConfig('options')['params']['propertytype'] ?? '';
    $properties = $this->getConfig('options')['params']['properties'] ?? [];
?>

<?php $firstSheet = true; ?>
<?php foreach ($properties as $propertyType => $propertiesIds): ?>

    <?php if (!is_string($propertyType)) { continue; } ?>

    <?php
        $propertyCaption = $database->types['properties'][$propertyType]['caption'] ?? $propertyType;
        $propertyDescent = ($this->getConfig('options')['params']['descent'][$propertyType] ?? false) ? 'checked' : null;

        // TODO: init with number
        //$propertyCaption .= '(' . count($propertiesIds) . ')';

        $this->beginTabsheet(
            $propertyCaption,
            'filter-properties-' . $propertyType,
            'left',
            ['active' => $firstSheet]
        );
        $firstSheet = false;
    ?>

        <div class="widget-filter-item widget-filter-item-properties widget-filter-item-facets-container"
             data-filter-group="<?= $tableGroup ?>"
             data-filter-action="properties/index/<?= $propertyType ?>?template=select"
             data-filter-propertytype="<?= $propertyType ?>"
             data-filter-caption="<?= $propertyCaption ?>"
             data-filter-selected="<?= implode(',', is_array($propertiesIds) ? $propertiesIds : []) ?>"
        >

            <div class="widget-filter-facets-results widget-scrollbox">
                <ul class="widget-tree" data-snippet="rows"></ul>
            </div>

            <div class="widget-filter-facets-filter">
                <input class="widget-filter-facets-term" type="text" placeholder="<?= __('Filter properties') ?>">
                <button class="widget-filter-facets-reset" type="button" title="<?= __('Reset filter') ?>" aria-label="<?= __('Reset filter') ?>">&#xe17b;</button>
            </div>

            <div class="widget-filter-facets-filter">
                <label class="widget-filter-facets-descent" title="<?= __('Include descendants') ?>">
                    <?= $this->Element->outputHtmlElement(
                        'input',
                        __('Include descendants'),
                        [
                            'type' => 'checkbox',
                            'checked' => $propertyDescent
                        ])
                    ?>
                </label>
            </div>
        </div>

    <?php $this->endTabsheet(); ?>

<?php endforeach; ?>

<?php
    $this->beginTabsheet(
        '{caption}',
        'filter-properties-template',
        'left',
        ['role'=>'template']
    );
?>

    <div class="widget-filter-item widget-filter-item-properties widget-filter-item-facets-container"
         data-filter-group="<?= $tableGroup ?>"
         data-filter-action="properties/index/{name}?template=select"
         data-filter-propertytype="{name}"
         data-filter-caption="{caption}"
         data-filter-selected=""
    >
        <div class="widget-filter-facets-results widget-scrollbox">
            <ul class="widget-tree" data-snippet="rows"></ul>
        </div>


        <div class="widget-filter-facets-filter">
            <input class="widget-filter-facets-term" type="text" placeholder="<?= __('Filter properties') ?>">
            <button class="widget-filter-facets-reset" type="button" title="<?= __('Reset filter') ?>" aria-label="<?= __('Reset filter') ?>">&#xe17b;</button>
        </div>

        <div class="widget-filter-facets-filter">
            <label class="widget-filter-facets-descent" title="<?= __('Include descendants') ?>">
                <input type="checkbox"><?= __('Include descendants') ?>
            </label>
        </div>
    </div>

<?php $this->endTabsheet(); ?>
