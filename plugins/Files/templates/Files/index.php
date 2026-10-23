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
    use App\Utilities\Files\Files;
?>
<?php
/**
 *
 * @var \App\View\AppView $this
 * @var \Files\Model\Entity\FileRecord $entity
 * @var \Files\Model\Entity\FileRecord[] $entities
 *
 * @var string $root
 * @var string $parent_path
 * @var array $mounts
 * @var array $thumbtypes
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Files'), ['action' => 'index']); ?>
<?php if (!empty($root)) $this->Breadcrumbs->add($root); ?>
<?php if (!empty($entity->relativePath)) $this->Breadcrumbs->add($entity->relativePath); ?>

<!-- Sidebars -->
<?php if (count($mounts) > 1) : ?>
    <?php $this->setSidebarConfig(['left' => ['init' => 'expanded'], 'right' => ['init' => 'expanded', 'size' => 5]]); ?>

    <?php $this->beginTabsheet(__('Mounts'), 'sidebar-menu', 'left') ?>

            <div class="frame-title">
                <div class="frame-title-caption"><?= __('Mounts') ?></div>
                <div class="frame-title-manage">
                    <button class="btn-close"
                            title="<?= __('Close') ?>"
                            aria-label="<?= __('Close') ?>"></button>
                </div>
            </div>

        <div class="frame-content">
                <div class="widget-scrollbox">
                <ul class="side-nav">
                    <?php foreach ($mounts as $mount): ?>
                        <li class="<?= ($root == $mount) ? 'active' : '' ?>"><?= $this->Html->link($mount, ['action' => 'index', '?' => ['root' => $mount]]) ?> </li>
                    <?php endforeach; ?>
                </ul>
                </div>

        </div>
    <?php $this->endTabsheet() ?>
<?php else: ?>
    <?php $this->setSidebarConfig(['left' => ['init' => 'collapsed'], 'right' => ['init' => 'expanded', 'size' => 5]]); ?>
<?php endif; ?>

<!-- Paginator -->
<?php $nexturl = $this->Link->nextPageUrl() ?>
<?php $geturl = $this->Link->getRowUrl() ?>


<div class="content-main">
    <div class="content-main-section widget-scrollbox">
        <table class="recordlist widget-table actions-toframe"
               data-snippet="rows"
               data-model="files">
            <thead>
            <tr>
                <th data-col="name" scope="col" data-width="300"><?= $this->Link->sortLink('name', __('Name')) ?></th>
                <th data-col="thumb" scope="col">Thumbnail</th>
                <th data-col="modified" scope="col"><?= $this->Link->sortLink('modified', __('Modified')) ?></th>
                <th data-col="size" scope="col"><?= $this->Link->sortLink('size', __('Size')) ?></th>
                <?php if ($this->User->hasRole(['devel'])): ?>
                <th data-col="permissions" scope="col"><?= $this->Link->sortLink('permissions', __('Permissions')) ?></th>
                <?php endif; ?>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody data-list-name="files"
                   data-list-action-next="<?= $nexturl ?>"
                   data-list-action-get="<?= $geturl ?>">

            <?php if (!empty($entity->relativePath)): ?>
                <tr class="files-folder actions-noframe">
                    <td><span class="ui-icon ui-icon-folder-collapsed"></span> ..</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?php if ($this->User->hasRole(['devel'])): ?>
                    <td></td>
                    <?php endif; ?>
                    <td class="actions">
                        <?= $this->Html->link(__('Open'), ['action' => 'index', '?' => ['root' => $root, 'path' => ($parent_path)]]) ?>
                    </td>
                </tr>

            <?php endif; ?>

            <?php foreach ($entities as $file): ?>
                <!-- Files -->
                <?php if (empty($file['isfolder'])): ?>
                    <tr class="files-file" data-list-itemof="files" data-id="<?= $file->id ?>">
                        <td class="<?= empty($file['missing']) ? '' : 'missing' ?>"><?= h($file['name']) ?></td>
                        <td class="thumb">
                            <?php if (in_array(strtolower($file['type']), $thumbtypes)): ?>
                                <img
                                    src="<?= $this->Url->build(['action' => 'download', $file['id'], '?' => ['format' => 'thumb']]) ?>"
                                    alt="Thumbnail">
                            <?php endif; ?>
                        </td>
                        <td><?= $file['modified']->i18nFormat(null, 'Europe/Paris') ?></td>
                        <td><?= Files::formatFileSize($file['size']) ?></td>
                        <?php if ($this->User->hasRole(['devel'])): ?>
                        <td><?= h($file['filePermissions']) ?>  <?= h($file['fileOwner']) ?></td>
                        <?php endif; ?>
                        <td class="actions">
                            <?= $this->Html->link(__('Open'), ['action' => 'view', $file->id, '?' => ['root' => $root]]) ?>
                        </td>
                    </tr>

                <!-- Folders -->
                <?php else: ?>
                    <tr class="files-folder" data-list-itemof="files" data-id="<?= $file->id ?>">
                        <td class="<?= empty($file['missing']) ? '' : 'missing' ?>"><span
                                class="ui-icon ui-icon-folder-collapsed"></span><?= h($file['name']) ?></td>
                        <td class="thumb"></td>
                        <td><?= $file['modified']->i18nFormat(null, 'Europe/Paris') ?></td>
                        <td></td>
                        <?php if ($this->User->hasRole(['devel'])): ?>
                        <td><?= h($file['filePermissions']) ?> <?= h($file['fileOwner']) ?></td>
                        <?php endif; ?>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $file->id]) ?>
                            <?= $this->Html->link(__('Open'), ['action' => 'index', $file->id], ['data-role' => 'open']) ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>

            </tbody>
        </table>
    </div>

    <div class="content-main-fixed">
        <?= $this->Files->dropzone(['root'=>$root, 'path' => $entity->relativePath]) ?>
    </div>
</div>

<!-- Actions -->
<?php
    $this->Link->beginActionGroup('bottom');
    $this->Link->addCreateAction(
        __('Create folder'),
        ['action'=>'add', $entity->id]
    );
?>

