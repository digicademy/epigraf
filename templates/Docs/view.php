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
 * @var \App\Model\Entity\Doc $entity
 * @var string $user_role
 */
?>

<?php $this->setSidebarConfig(['right' => ['size' => 3]]); ?>

<!-- Breadcrumbs -->
<?php if ($user_role !== 'guest'): ?>
    <?php $this->setSidebarConfig(['left'=>['init' => 'expanded']]); ?>
    <?php $this->Breadcrumbs->add($title ?? __('Pages'), ['action' => 'index']); ?>
    <?php $this->Breadcrumbs->add($entity->captionPath); ?>
<?php endif; ?>

<!-- TOC -->
<?php if (!empty($entity->toc) && (count($entity['toc']) > 1)): ?>
    <?php $this->setSidebarConfig(['right'=>['init' => 'expanded']]); ?>
    <?php $this->beginTabsheet(__('Content'),'content','right'); ?>
    <?= $this->Menu->renderMenu(
        array_merge(['scrollbox' => true, 'tree' => 'fixed'], $entity->toc),
        [
            'class' => 'widget-scrollsync side-nav'
        ]
    ) ?>
    <?php $this->endTabsheet(); ?>
<?php else: ?>
    <?php $this->setSidebarConfig(['right'=>['init' => 'collapsed']]); ?>
<?php endif; ?>


<?= $this->Element->outputHtmlElement(
    'div',
    $entity->html,
    [
        'class' => 'widget-codeblocks widget-highlight',
        'data-row-table' => 'docs',
        'data-row-id' => $entity->id,
        'data-root-table' => $entity->table->getTable(),
        'data-root-id' => $entity->id,
        'data-highlight' => $this->getRequest()->getQuery('highlight','')
    ]
) ?>

<?php if ($user_role !== 'guest'): ?>
    <!-- Actions -->
    <?php
        $this->setShowBlock(['footer']);
        $this->Link->addEditButtons($entity);
        $this->Link->addCreateAction(__('Create page'), ['data-role'=>'secondary']);
    ?>
<?php endif; ?>
