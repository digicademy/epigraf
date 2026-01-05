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
<!DOCTYPE html>
<html lang="de">

<?php
/**
 * @var $this App\View\AppView
 * @var string $theme
 * @var string $pagetitle
 * @var string $pagehelp Path in the help pages (or null)
 * @var array $sidemenu
 * @var array $menu
 * @var string $user_role
 * @var array $user
 * @var App\Model\Entity\Databank $database
 */

use Cake\Core\Configure;
?>

<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?= $pagetitle; ?></title>

    <?= $this->Html->meta('icon',Configure::read('App.icon', 'img/favicon_petrol.png')) ?>
    <?= $this->Html->meta('description',__('A research platform for multimodal text data')); ?>
    <?= $this->Html->meta('author','Epigraf Team'); ?>
    <?= $this->fetch('meta') ?>

    <!-- CSS frameworks -->
    <?= $this->Html->css('/js/driver.js/driver.css') ?>
    <?= $this->Html->css('/js/jqueryui/jquery-ui.min.css') ?>

    <!-- CSS app -->
    <?= $this->Html->css('app.css') ?>

    <!-- CSS widgets and plugins -->
    <?php if (Configure::read('production',false)): ?>
        <?= $this->Html->css('Widgets.widgets.min.css'); ?>
    <?php else: ?>
        <?= $this->Html->css('Widgets.widgets.css'); ?>
    <?php endif; ?>

    <!-- Theme -->
    <?php if (!empty($theme)): ?>
        <?= $this->Html->css('theme_'. $theme . '.css'); ?>
    <?php endif; ?>

    <!-- JS -->

    <!-- JS frameworks -->
    <?= $this->Html->script('driver.js/driver.js.iife.js') ?>
    <?= $this->Html->script('jquery/jquery.min.js') ?>
    <?= $this->Html->script('jqueryui/jquery-ui.min.js') ?>

    <!-- JS app -->
    <?php if (Configure::read('production',false)): ?>
        <?= $this->Html->script('app.min.js', ['type'=>'module']) ?>
    <?php else: ?>
        <?= $this->Html->script('app.js', ['type'=>'module']) ?>
    <?php endif; ?>

    <?= $this->Html->scriptBlock($this->getAppJs(), ['type'=>'module']) ?>

    <!-- JS widgets -->
    <?php //TODO: Bundle and import in map.js on demand ?>
    <?= $this->Html->script('Widgets.leaflet/leaflet.js'); ?>
    <?= $this->Html->script('Widgets.leaflet/leaflet.markercluster.js'); ?>
    <?= $this->Html->script('Widgets.leaflet/leaflet.markercluster.layersupport.js'); ?>
    <?= $this->Html->script('Widgets.leaflet/easy-button.js'); ?>
    <?= $this->Html->script('Widgets.leaflet/leaflet-gesture-handling.min.js'); ?>
    <?= $this->Html->script('Widgets.leaflet/leaflet-control-geocoder/control.geocoder.js'); ?>
    <?= $this->Html->css('Widgets.leaflet/leaflet-control-geocoder/control.geocoder.css'); ?>
    <?= $this->Html->script('Widgets.mark/mark.min.js'); ?>
    <?= $this->Html->script('Widgets.d3/d3.v6.min.js'); ?>

    <?php if (Configure::read('production',false)): ?>
        <?= $this->Html->script('Widgets.epieditor.min.js') ?>
        <?= $this->Html->script('Widgets.widgets.min.js',['type'=>'module']); ?>
    <?php else: ?>
        <?= $this->Html->script('Widgets.epieditor.min.js') ?>
        <?= $this->Html->script('Widgets.widgets.js',['type'=>'module']); ?>
    <?php endif; ?>


    <!-- Additional CSS -->
    <?= $this->fetch('css') ?>

    <!-- Additional JS -->
    <?= $this->fetch('script') ?>
    <?php
        // Let it snow
        if (Configure::read('snow')) {
            echo $this->Html->css('/widgets/snowfall/snowfall.css');
            echo $this->Html->script('/widgets/snowfall/snowfall.js');
        }
    ?>
</head>

<?= $this->Element->openHtmlElement(
    'body',
    [
        'class' => [
            'plugin_' . h(strtolower($this->request->getParam('plugin') ?? '')),
            'controller_' . h(strtolower($this->request->getParam('controller') ?? '')),
            'action_' . h(strtolower($this->request->getParam('action') ?? '')),
            'template_' . h($this->request->getParam('template','default')),
            'theme_' . h($theme),
            'userrole_' . h($user_role)
        ]
    ]
);
?>

<?= $this->Link->getTrackingCode() ?>

<div class="page-wrapper accordion">

    <?php if ($this->getShowBlock('mainmenu')): ?>
        <nav class="actions-main">

            <div class="topmenu-left">
                <div class="widget-sandwich-source widget-sandwich-items-topmenu"
                     data-sandwich-source="topmenu-left">
                    <?= $this->Menu->renderMenu(
                        $menu,
                        ['class'=>'horizontal', 'data'=> []]
                    ) ?>
                </div>

                <?= $this->Link->renderSandwichButton(
                    "widget-sandwich-items-topmenu",
                    ['dropdown' => 'bottomright', 'title' => __('Menu')]
                ) ?>
            </div>

            <div class="topmenu-right">
                <div id="loader" style="display:none;"></div>

                <?php if ($user_role === 'guest'): ?>
                    <?= $this->Html->link(
                        __('Login'),
                        ['plugin'=>false,'controller'=>'users','action'=>'login'],
                        ['class' => 'btn', 'title' => __('Login')]
                    ) ?>
                <?php else: ?>

                    <?= $this->Html->link(
                        __('Logout'),
                        ['plugin' => false, 'controller' => 'Users', 'action' => 'logout'],
                        ['class' => 'btn', 'title' => __('Logout')]
                    ) ?>
                    <?= $this->Html->link(
                        "\u{f007}",
                        ['plugin' => false, 'controller' => 'Users', 'action' => 'view', $user['id']],
                        ['class' => 'btn btn-icon', 'title' => $user['username']]
                    ) ?>
                <?php endif; ?>
                <?php if (in_array($user_role, ['admin', 'devel'])): ?>
                    <?= $this->Html->link(
                        "\u{f013}",
                        ['plugin' => false, 'controller' => 'Settings', 'action' => 'show', 'vars'],
                        ['class' => 'btn btn-icon', 'title' => __('System settings')]
                    ) ?>
                <?php endif; ?>
            </div>

        </nav>
    <?php endif; ?>

    <div class="content-wrapper">

        <!-- Left sidebar -->
        <?php if ($this->getShowBlock('leftsidebar')): ?>

            <?php if ($sidemenu): ?>
                <?php $this->activateSidebar(); ?>
                <?php $this->beginTabsheet($sidemenu['caption'] ?? __('Menu'), 'sidebar-menu', 'left') ?>

                <div class="frame-title">
                    <div class="frame-title-caption"><?= $sidemenu['caption'] ?? '' ?></div>
                    <div class="frame-title-manage"><?= $this->renderSidebarButtons($sidemenu) ?></div>
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
                    'edit'=>$sidemenu['edit'] ?? false,
                    'add'=> $sidemenu['add'] ?? false,
                    'apply' => 'small'
                ]
            ) ?>
        <?php endif; ?>

        <!-- Content -->
        <?php if ($this->getShowBlock('content')): ?>
            <div id="content" class="accordion-item accordion-main content" data-accordion-item="main">

                <div class="content-header">
                    <div class="content-header-title">

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
                    </div>

                    <?php if (!empty($header) && $sidemenu): ?>
                        <button class="accordion-toggle" data-toggle-accordion="sidebar-left"><?= $sidemenu['caption'] ?? __('Menu') ?></button>
                    <?php endif; ?>

                    <?php if ($this->Link->hasActions('top')): ?>
                        <div class="content-header-actions">
                            <nav class="actions-top" data-snippet="actions-top">
                                <?= $this->Link->renderActions('top'); ?>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="content-toolbar empty<?= $this->getConfig('showToolbar') ? ' active': '' ?>"></div>

                <div class="content-flash"><?= $this->Flash->render() ?></div>

                <div class="content-content widget-scrollsync-content">
                    <?= $this->fetch('content') ?>

                    <?php if ($this->Link->hasActions('content')): ?>
                        <nav class="actions-content" data-snippet="actions-content">
                            <?= $this->Link->renderActions('content'); ?>
                        </nav>
                    <?php endif; ?>
                </div>

                <div class="content-footer"><?= $this->fetch('footer') ?></div>

            </div>
        <?php endif; ?>

        <!-- Right sidebar -->
        <?php if ($this->getShowBlock('rightsidebar')): ?>
            <?= $this->renderSidebar('right', ['close' => true]) ?>
        <?php endif; ?>
    </div>

    <?php if ($this->getShowBlock('footer')): ?>
        <?php
            if ($user_role !== 'guest') {
                $this->Link->addHelpAction($pagehelp);
            }
        ?>
        <?= $this->renderFooter() ?>
    <?php endif; ?>

</div>
<?= $this->Element->closeHtmlElement('body') ?>
</html>
