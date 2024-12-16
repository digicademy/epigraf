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
 * @var string $segment
 * @var \App\Model\Entity\Doc[] $entities
 */
?>

<!-- Breadcrumbs -->
<?php // TODO: can `'segment'=>$this->request->getParam('segment')` be removed? ?>
<?php $this->Breadcrumbs->add($title ?? __('Pages'), ['segment' => $this->request->getParam('segment'), 'action' => 'index']); ?>
<?php
    $category = $this->getConfig('options')['params']['category'] ?? null;
    if (isset($category) && ($category !== '')) {
        $this->Breadcrumbs->add($category);
    }
?>

<!-- Sidebars -->
<?php
    $this->sidebarInit(['left' => 'expanded','right'=>'collapsed']);
    $this->sidebarSize(['right'=>5]);
?>

<!-- Content -->
<?php
    $term = $this->getConfig('options')['params']['term'] ?? null;

    $plugin = $this->request->getParam('plugin');
    $controller = $this->request->getParam('controller');
    $model = strtolower(implode('.', array_filter([$plugin, $controller])));
?>

<div class="content-main widget-scrollbox">
    <?= $this->Table->filterTable(
        $model, $entities,
        [
            'select'=> false,     // Column selector
            'tree' => !empty($term), // Tree rendering: true|false|collapsed
            'fold' => 'fixed',    // Foldable: fixed|foldable
            'actions'=>['view'=>true]
        ]
    ) ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup('bottom');
    $this->Link->addCounter();
    $this->Link->addCreateAction(__('Create page'));
    $this->Link->downloadButtons(null, 'id', $model);
//    $this->Link->addAction(
//    __('Move'),
//        null,
//        ['linktype'=>'button', 'class'=>'widget-switch','data-role'=>'move','roles'=>['admin', 'devel']]
//    );
?>
