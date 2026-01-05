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
 * @var \App\View\AppView $this
 * @var \Epi\Model\Entity\Property $entity
 */
?>

<?php // $entity->prepareRoot(); ?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Categories'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity->type['caption'] ?? h($entity->propertytype)) ?>

<!-- Content area -->
<?php $this->append('css', $this->Types->getTagStyles()); ?>

<div class="content-tight">

    <?= $this->EntityHtml->docProblems($entity) ?>
    <?= $this->EntityHtml->entityForm($entity, 'view', [], true) ?>

    <?php if (empty($entity->related_id)): ?>
        <?php $related = $entity->merged; ?>
        <?php if ($related->count()): ?>
            <h2><?= __('Merged from') ?></h2>
            <?= $this->Table->filterTable(
                'merged',
                $related,
                [
                    'columns' => [
                        'path'=>['caption'=>__('Path'), 'default'=>true],
                        'norm_iri'=>['caption'=>__('IRI fragment'), 'default'=>true],
                        'modified'=>['caption'=>__('Modified'), 'default'=>true]
                        // TODO: merge button (example URL: http://127.0.0.1/epi/epi_all/properties/merge/20843?target=22309)
                    ],
                    'actions' => ['view'=>false]
                ])
            ?>
        <?php endif; ?>

        <?php $related = $entity->homonyms; ?>
        <?php if ($related->count()): ?>
            <h2><?= __('Similar lemmata') ?></h2>
            <?= $this->Table->filterTable(
                'homonyms',
                $related,
                [
                    'columns' => [
                        'path'=>['caption'=>__('Path'), 'default'=>true],
                        'norm_iri'=>['caption'=>__('IRI fragment'), 'default'=>true],
                        'modified'=>['caption'=>__('Modified'), 'default'=>true]
                    ]
                    // TODO: merge button (example URL: http://127.0.0.1/epi/epi_all/properties/merge/20843?target=22309)
                    // 'actions' => ['merge' => [...]] See filterTable.
                ])
            ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php $related = $entity->versions; ?>
    <?php if ($related->count()): ?>
        <h2><?= __('Versions') ?></h2>
        <?= $this->Table->filterTable(
            'versions',
            $related,
            [
                'columns' => [
                    'path'=>['caption'=>__('Path'), 'default'=>true],
                    'norm_iri'=>['caption'=>__('IRI fragment'), 'default'=>true],
                    'modified'=>['caption'=>__('Modified'), 'default'=>true]
                    // TODO: merge button (example URL: http://127.0.0.1/epi/epi_all/properties/merge/20843?target=22309)
                ],
                'actions' => ['view'=>false]
            ])
        ?>
    <?php endif; ?>

    <?php $related = $entity->articles; ?>
    <?php if ($related->count()): ?>
        <h2><?= __('Used in articles') ?></h2>
        <?= $this->Table->filterTable(
            'epi.articles.related',
            $related,
            [
                'columns' => [
                    'project'=>['caption'=>__('Project'), 'key' => 'project.signature', 'width'=>80, 'default'=>true],
                    'caption'=>['caption'=>__('Caption'), 'default'=>true],
                    'norm_iri'=>['caption'=>__('IRI fragment'), 'default'=>true],
                    'modified'=>['caption'=>__('Modified'), 'default'=>true]
                ],
                'actions' => ['view'=> ['controller' => 'Articles', 'action' => 'view', '{id}']],
                'flow' => 'tab',
                'more' => [
                    'controller' => 'Articles',
                    'action' => 'index',
                    '?' => ['properties.' . $entity->propertytype . '.selected' => $entity->id]
                ]
            ])
        ?>
    <?php endif; ?>

    <?php $related = $entity->baseProperties; ?>
    <?php if ($related->count()): ?>
        <h2><?= __('Base properties') ?></h2>
        <?= $this->Table->filterTable(
            'epi.properties.base',
            $related,
            [
                'columns' => [
                    'caption'=>['caption'=>__('Caption'), 'default'=>true],
                    'norm_iri'=>['caption'=>__('IRI fragment'), 'default'=>true],
                    'modified'=>['caption'=>__('Modified'), 'default'=>true]
                ],
                'actions' => ['view'=>false],
                //TODO: implement meta query parameter, see PropertiesController->index()
                'more' => [
                    'controller' => 'Properties',
                    'action' => 'index',
                    '?' => ['meta' => $entity->id]
                ]
            ])
        ?>

    <?php endif; ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->addEditButtons($entity);

    if (empty($entity->related_id)) {
        $this->Link->addActionGroupLabel(__('Context Actions'));
        $this->Link->addAction(
            __('Merge'),
            [
                'controller' => 'Properties',
                'action' => 'merge',
                $entity->id,
                '?' => ['preview' => true, 'concat' => true]
            ],
            ['roles' => ['author', 'editor']]
        );

        $this->Link->addAction(
            __('Move'),
            [
                'controller' => 'Properties',
                'action' => 'move',
                $entity->id
            ],
            ['roles' => ['author', 'editor']]
        );
    }


    $this->Link->downloadButtons();
?>
