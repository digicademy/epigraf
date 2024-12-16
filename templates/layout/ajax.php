<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>

<?php
/**
 * @var $this \Cake\View\View
 * @var array $sidemenu
 */
?>

<?= $this->fetch('css') ?>


<!-- Left sidebar -->
<?php if ($this->getShowBlock('leftsidebar')): ?>

    <?php if ($sidemenu): ?>
        <?php $sidebar_options['left']['init'] = 'expanded'; ?>
        <?php $this->beginTabsheet($sidemenu['caption'] ?? __('Menu'), 'sidebar-menu', 'left') ?>

        <div class="frame-title">
            <div class="frame-title-caption"><?= $sidemenu['caption'] ?? '' ?></div>
            <div class="frame-title-manage">
                <button class="btn-close"
                        title="<?= __('Close') ?>"
                        aria-label="<?= __('Close') ?>"></button>
            </div>
        </div>

        <div class="frame-content">
            <?= $this->Menu->renderMenu(
                $sidemenu,
                [
                    'class' => 'side-nav',
                    'data' => ['data-list-name' => 'menu-left']
                ]
            ) ?>
        </div>
        <div class="frame-footer">
            <?php if($sidemenu['search'] ?? false): ?>
                <?= $this->EntityInput->searchForm() ?>
            <?php endif; ?>
        </div>
        <?php $this->endTabsheet(); ?>
    <?php endif; ?>

    <?= $this->renderSidebar(
        'left',
        [
            'size'=> $sidebar_size['left'] ?? '2',
            'init' => $sidebar_options['left']['init'] ?? 'collapsed',
            'edit'=>$sidemenu['edit'] ?? false,
            'add'=> $sidemenu['add'] ?? false,
            'apply' => 'small'
        ]
    ) ?>

<?php endif; ?>

<?php if ($this->getShowBlock('content')): ?>

    <div class="content-flash">
        <?= $this->Flash->render() ?>
    </div>

    <div class="content-toolbar empty<?= $this->getConfig('showToolbar') ? ' active': '' ?>"></div>

    <?php
        $header = $this->fetch('header');

        $breadcrumbs = $this->Breadcrumbs->render(
            ['class' => 'breadcrumbs-trail'],
            ['separator' => '&nbsp;&raquo;&nbsp;']
        );
        if ($breadcrumbs) {
            $header = $this->Element->outputHtmlElement(
                    'nav', $breadcrumbs,
                    [
                        'class' => ['breadcrumbs', !empty($header) ? 'hidden' : null]
                    ]
                ). $header;
        }

    ?>
    <?= $header ?>

    <?php if ($this->Link->hasActions('top')): ?>
        <nav class="actions-top" data-snippet="actions-top">
            <?= $this->Link->renderActions('top'); ?>
        </nav>
    <?php endif; ?>

    <?= $this->fetch('content') ?>

    <div class="content-footer"><?= $this->fetch('footer') ?></div>

    <?php if ($this->Link->hasActions('content')): ?>
        <nav class="actions-content" data-snippet="actions-content">
            <?= $this->Link->renderActions('content'); ?>
        </nav>
    <?php endif; ?>

<?php endif; ?>

<?php if ($this->getShowBlock('rightsidebar')): ?>

    <!-- Right sidebar -->
    <?= $this->renderSidebar(
        'right',
        [
            'size'=> $sidebar_size['right'] ?? '5',
            'init' => $sidebar_options['right']['init'] ?? 'collapsed',
            'close'=>true
        ]
    ) ?>

<?php endif; ?>

<?php if ($this->getShowBlock('footer')): ?>
    <?= $this->renderAjaxFooter() ?>
<?php endif; ?>
