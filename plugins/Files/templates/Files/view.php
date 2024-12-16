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
 * @var \App\Model\Entity\Databank $database
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FileRecord $entity;
 * @var string $root
 */
?>

<!-- Breadcrumbs -->
<?php
    $this->Breadcrumbs->add(__('Files'), ['action' => 'index']);
    if (!empty($root)) {
        $this->Breadcrumbs->add($root);
    }
    if (!empty($path)) {
        $this->Breadcrumbs->add($path);
    }
    if (!empty($entity['name'])) {
        $this->Breadcrumbs->add($entity['name']);
    }
?>

<!-- Content area -->
<div class="content-boxes">
    <div class="content-box">
        <?= $this->EntityHtml->entityForm($entity, 'view'); ?>
    </div>

    <div class="content-box">
        <h2><?= __('Preview') ?></h2>
        <?= $this->Files->outputPreview($entity) ?>
    </div>

    <?php $xmp = $entity->xmp ?? []; ?>
    <?php if (!empty($xmp)): ?>
        <div class="content-box">
            <h2><?= __('XMP Metadata') ?></h2>
            <?=	$this->Table->nestedTable($xmp);?>
        </div>
    <?php endif; ?>

    <?php $items = $entity->getItems(); ?>
    <?php if (!empty($items)): ?>
        <div class="content-box">
            <h2><?= __('Used in articles') ?></h2>
            <ul>
            <?php foreach ($items as $item): ?>
                <li>
                    <?php $title = $item->article->captionPath; ?>
                    <?= $this->Html->link($title,['controller'=>'articles','action'=>'view',$item->articles_id,'#'=>'items-' . $item->id]) ?>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addAction(__('Edit'),['action' => 'edit', $entity['id'],'?' => ['root'=>$root]],['shortcuts' => ['F2']]);
    $this->Link->addAction(__('Move'),['action' => 'move', $entity['id'],'item','?' => ['root'=>$root]]);
    $this->Link->addAction(__('Download'),['action' => 'download', '?' => ['root' => $root, 'path' => $entity['path'], 'filename' => $entity['name']]],['data-target'=>'main']);
    if (($entity['type'] ?? '') == 'zip') {
        $this->Link->addAction(__('Unzip'),['action' => 'unzip', '?' => ['root' => $root, 'path' => $entity['path'], 'filename' => $entity['name']]],['roles'=>['devel']]);
    }
?>
