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

    <?php $preview = $this->Files->outputPreview($entity) ?>
    <?php if (!empty($preview)): ?>
        <div class="content-box">
            <h2><?= __('Preview') ?></h2>
            <?= $preview ?>
        </div>
    <?php endif ?>

    <?php $xmp = $entity->xmp ?? []; ?>
    <?php if (!empty($xmp)): ?>
        <div class="content-box">
            <h2><?= __('XMP Metadata') ?></h2>
            <?=	$this->Table->nestedTable($xmp);?>
        </div>
    <?php endif; ?>

    <?php $items = $entity->getItems(); ?>
    <?php if (empty($entity['isfolder']) && !empty($items)): ?>
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

    if ($entity['isfolder']) {
        $this->Link->addAction(
            __('Open'),
            ['action' => 'index', $entity['id'], '?' => ['root' => $root]],
            ['data-target'=>'main','data-role' => 'open']
        );
    }

    $this->Link->addAction(__('Edit'),['action' => 'edit', $entity['id'],'?' => ['root'=>$root]],['shortcuts' => ['F2']]);
    $this->Link->addAction(__('Move'),['action' => 'move', $entity['id'],'item','?' => ['root'=>$root]]);

    if ($entity['isfolder']) {
        $this->Link->addAction(__('Move content'),
            ['action' => 'move', '?' => ['root' => $root, 'path' => $entity->relativePath]]);

        $this->Link->addAction(__('Download latest file'),
            ['action' => 'download', '?' => ['root' => $root, 'path' => $entity->relativePath, 'find' => 'latest']]);
    }

    if (($entity['type'] ?? '') == 'zip') {
        $this->Link->addAction(__('Unzip'),['action' => 'unzip', '?' => ['root' => $root, 'path' => $entity['path'], 'filename' => $entity['name']]],['roles'=>['devel']]);
    }

    if ($entity['isfolder'] && $this->User->hasRole(['devel']) && !empty($entity->relativePath)) {
            $this->Link->addAction(__('Clear thumbs'),['action' => 'clearthumbs', '?' => ['root' => $root, 'path' => $entity->relativePath]]);
            $this->Link->addAction(__('Clean names'),['action' => 'clean', '?' => ['root' => $root, 'path' => $entity->relativePath]]);
            $this->Link->addAction(__('Sync'),['action' => 'sync', '?' => ['root' => $root, 'path' => $entity->relativePath]],['confirm'=>__('This will recurse all files and folders and will stress the server. Are you ready  to proceed?')]);
    }

    if ($entity['isfolder'] && !empty($entity['config'])) {
        $this->Link->addAction(__('Pull from origin'),['action' => 'fetch', '?' => ['root' => $root, 'path' => $entity->relativePath]]);
    }
?>
