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
 * @var \App\Model\Entity\Help $help
 */
?>

<?php $this->setSidebarConfig(['right' => ['size' => 3]]); ?>

<!-- Breadcrumbs -->
<?php $this->setSidebarConfig(['left'=>['init' => 'expanded']]); ?>
<?php $this->Breadcrumbs->add($title ?? __('Help')); ?>
<?php if (($help->header['title'] ?? '') !== '') $this->Breadcrumbs->add($help->header['title']); ?>

<!-- TOC -->
<?php if (!empty($help->toc) && (count($help->toc) > 1)): ?>
    <?php $this->setSidebarConfig(['right'=>['init' => 'expanded']]); ?>
    <?php $this->beginTabsheet(__('Content'),'content','right'); ?>
    <?= $this->Menu->renderMenu(
        array_merge(['scrollbox' => true, 'tree' => 'fixed'], $help->toc),
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
    $help->html,
    [
        'class' => 'widget-codeblocks widget-highlight',
        'data-highlight' => $this->getRequest()->getQuery('highlight',''),
        'data-row-table' => 'help'
    ]
) ?>

