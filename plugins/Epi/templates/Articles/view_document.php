<?php /**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */ ?>

<?php
use App\Model\Entity\Databank;
use App\View\AppView;
?>

<?php
/**
 * @var AppView $this
 * @var Epi\Model\Entity\Article $entity
 * @var boolean $edit
 * @var string $mode
 * @var array $templateArticle
 */
?>
<?php
    $options = ['edit' => $edit, 'mode' => $mode, 'template_article' => $templateArticle];
    $params = $this->getConfig('options')['params']; // Attributes::paramsToQueryString($this->getConfig('options')['params']);
    $docContentToTop = (!empty($options['edit']) && ($options['mode'] !== 'code'));
    $this->append('css', $this->Types->getTagStyles());
    $entityHelper = $edit ? $this->EntityInput : $this->EntityHtml;
?>

<!-- Breadcrumbs -->
<?php $this->start('header'); ?>
    <?= $entityHelper->docHeader($entity, $options) ?>
<?php $this->end(); ?>

<?php $this->Breadcrumbs->add($entity->project->name ?? '<i>No project</i>', [
    'controller' => 'Articles',
    'action' => 'index',
    '?' => ['projects' => $entity->project->id ?? null],
    'database' => $this->request->getParam('database')
]); ?>
<?php $this->Breadcrumbs->add($entity->name); ?>


<?php $this->initToolbar($options['edit'] && ($entity->type['merged']['toolbar'] ?? false)); ?>

<!-- Article container -->
<div class="doc-article widget-document <?= $options['edit'] ? 'widget-document-edit' : 'widget-document-view' ?> widget-highlight"
     data-row-table="articles"
     data-row-id="<?= $entity->id ?>"
     data-row-type="<?= $entity->type->name ?? '' ?>"
     data-root-table="articles" data-root-id="<?= $entity->id ?>"
     data-file-basepath="<?= trim($entity->file_basepath, '/') ?>"
     data-file-defaultpath="<?= trim($entity->file_defaultpath, '/') ?>"
     data-database="<?= Databank::removePrefix($entity->databaseName) ?>"
     data-edit-mode="<?= $options['edit'] ?>"
     data-highlight="<?= $params['highlight'] ?? '' ?>"
>

    <?php if ($options['edit']): ?>
        <?php
            $formOptions = [
                'id' => 'form-edit-articles-' . $entity->id,
                'url' => ['action' => 'edit', $entity->id,'_ext' => 'json', '?' => ['redirect' => 'edit']],
                'data-target' => 'articles-' . $entity->id,
                'data-format' => 'json'
            ];
            if (!$entity->isNew() && !$entity->deleted) {
                $queryParams = [];
                if ($mode !== 'default') {
                  $queryParams['mode'] = $mode;
                }
                if (!empty($params['published'])) {
                    $queryParams['published'] = implode(',', $params['published']);
                }

                $formOptions['data-cancel-url'] = $this->Url->build(['action' => 'view', $entity->id,'?'=>$queryParams]);
                $formOptions['data-delete-url'] = $this->Url->build(['action' => 'delete', $entity->id,'?'=>$queryParams]);
                $formOptions['data-proceed-url'] = $this->Url->build(['action' => 'edit', $entity->id,'?'=>$queryParams]);
            }
        ?>
        <?= $this->Form->create($entity, $formOptions) ?>
        <?php if (!empty($entity->lockid)): ?>
            <?= $this->Form->hidden(
                'lock',
                [
                    'value' => $entity->lockid ?? '',
                    'data-lock-url' => $entityHelper->lockUrl($entity),
                    'data-unlock-url' => $entityHelper->unlockUrl($entity)
                ]
            )
            ?>
        <?php endif; ?>
        <?= $this->Form->hidden('id') ?>
    <?php endif; ?>

    <!-- Article header-->
    <?php //In edit mode, article fields come above the content ?>
    <?php //TODO refactor docContent() to call it only once below.  ?>
    <?php if ($docContentToTop): ?>
        <?= $entityHelper->docContent($entity, $options) ?>
    <?php endif; ?>

    <!-- Sections -->
    <?=
        $entityHelper->sectionList(
            $entity,
            [
                'note' => $this->request->is('ajax')
            ] + $options
        )
    ?>

    <!-- Footnotes -->
    <?php if (!$this->request->is('ajax')): ?>
        <?php $this->beginTabsheet(__('Footnotes'), 'footnotes', 'right'); ?>
            <?= $entityHelper->footnoteList($entity, $options) ?>
        <?php $this->endTabsheet(); ?>
    <?php elseif (in_array('footnotes', $params['snippets'] ?? ['footnotes'])): ?>
        <?= $entityHelper->footnoteList($entity, $options) ?>
    <?php endif; ?>

    <!-- Notes -->
    <?php if (!$this->request->is('ajax') && ($entity->getFieldIsVisible('notes'))): ?>
        <?php $this->beginTabsheet(__('Notes'), 'notes', 'right'); ?>
        <?php if (in_array('notes', $params['snippets'] ?? ['notes'])): ?>
            <?= $entityHelper->notesList($entity, $options) ?>
        <?php endif; ?>
        <?php $this->endTabsheet(); ?>
    <?php endif ?>

    <!-- References -->
    <?php if (!$edit && ($entity->getFieldIsVisible('backlinks'))): ?>
        <?php $this->beginTabsheet(__('Backlinks'), 'backlinks', 'right'); ?>
        <?php if (in_array('backlinks', $params['snippets'] ?? [])): ?>
            <div data-snippet="backlinks">
                <?= $entityHelper->referencesList($entity, $options) ?>
            </div>
        <?php else: ?>
           <?=  $this->Element->ajaxContent('backlinks', 'rightsidebar') ?>
        <?php endif; ?>
        <?php $this->endTabsheet(); ?>
    <?php endif ?>

    <?php //In view actions and in coding mode, article fields come below the content ?>
    <?php if (!$docContentToTop): ?>
        <?= $entityHelper->docContent($entity, $options) ?>
    <?php endif; ?>

    <?php if ($options['edit']): ?>
        <?= $this->Form->end() ?>
    <?php endif; ?>

    <!-- Article metrics -->
    <?php if ($entity->currentUserRole !== 'guest'): ?>
        <?= $entityHelper->docProblems($entity) ?>
        <?php // $this->element('../Sections/view_metrics'); TODO: move to BaseEntityHelper ?>
    <?php endif; ?>

    <?php if ($options['edit']): ?>
        <?= $entityHelper->annoTemplates([], $entity, $options); ?>
    <?php endif; ?>
</div>

<!-- Right sidebar -->
<?php $this->sidebarInit(['left' => 'expanded', 'right' => 'expanded']); ?>
<?php $this->sidebarSize(['left' => 2, 'right' => 3]); ?>

<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup('bottom');
?>

<?php if (!$options['edit']): ?>

    <?php

        // TODO: generate link in App.openDetails()
        if ($this->request->is('ajax') && !in_array($mode, ['code'])) {
            $action = $this->Link->hasPermission(['action' => 'edit']) ? 'edit' : 'view';
            $url = ['action' => $action, $entity->id];
            if (!empty($params['published'])) {
                $url['?'] = ['mode' => 'stage'];
            }
            $this->Link->addAction(
                __('Open article'),
                $url,
                ['data-target'=>'external','data-role' => 'open']
            );
        }

        $this->Link->addEditButtons($entity);
    ?>

<?php else: ?>
    <?php $this->Link->addSaveCancelDeleteButton($entity, ['delete' => $templateArticle['edit'] ?? true, 'close' => true]); ?>
<?php endif; ?>

<?php
    $this->Link->beginActionGroup('bottom-right');
    // @deprecated: Remove with EpiDesktop
    if ($entity->type->merged['epidesktop'] ?? false) {
        $this->Link->addAction(
            __('EpiDesktop'),
            'epigraf://' . $this->request->host() . '/database/' .
            $entity->databaseName . '/article/' .
            $entity->id,
            ['roles' => ['author', 'editor', 'admin'], 'target' => '_blank']
        );
    }
    $queryParams = array_filter([
        'projects' => $entity->project->id ?? null,
        'articles' => $entity->id,
        'published' => empty($params['published']) ? null : implode(',', $params['published'])
    ]);
    $this->Link->exportButtons($queryParams, $entity);
    $this->Link->downloadButtons();
?>
