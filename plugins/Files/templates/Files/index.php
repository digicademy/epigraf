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
 * @var string $user_role
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
<?php
    $this->setSidebarConfig(['left' => ['init' => 'expanded'], 'right' => ['init' => 'expanded', 'size' => 5]]);
?>

<?php $this->beginTabsheet(__('Mounts'), 'sidebar-menu', 'left') ?>
    <?php if (count($mounts) > 1) : ?>
        <div class="frame-title">
            <div class="frame-title-caption"><?= __('Mounts') ?></div>
            <div class="frame-title-manage">
                <button class="btn-close"
                        title="<?= __('Close') ?>"
                        aria-label="<?= __('Close') ?>"></button>
            </div>
        </div>
    <?php endif; ?>

    <div class="frame-content">
        <?php if (count($mounts) > 1) : ?>
            <div class="widget-scrollbox">
            <ul class="side-nav">
                <?php foreach ($mounts as $mount): ?>
                    <li class="<?= ($root == $mount) ? 'active' : '' ?>"><?= $this->Html->link($mount, ['action' => 'index', '?' => ['root' => $mount]]) ?> </li>
                <?php endforeach; ?>
            </ul>
            </div>
        <?php endif; ?>
    </div>

    <div class="frame-footer">
        <?= $this->Files->dropzone(['root'=>$root, 'path' => $entity->relativePath]) ?>
    </div>

<?php $this->endTabsheet() ?>

<!-- Paginator -->
<?php $nexturl = $this->Link->nextPageUrl() ?>
<?php $geturl = $this->Link->getRowUrl() ?>


<div class="content-main widget-scrollbox">
	<table class="recordlist widget-table actions-toframe"
           data-snippet="rows"
           data-model="files">
		<thead>
		<tr>
			<th data-col="name" scope="col"><?= $this->Link->sortLink('name', __('Name')) ?></th>
			<th data-col="thumb" scope="col">Thumbnail</th>
			<th data-col="modified" scope="col"><?= $this->Link->sortLink('modified', __('Modified')) ?></th>
			<th data-col="size" scope="col"><?= $this->Link->sortLink('size', __('Size')) ?></th>
			<?php if (in_array($user_role, ['devel'])): ?>
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
				<?php if (in_array($user_role, ['devel'])): ?>
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
                <tr class="files-file" data-list-itemof="files"  data-id="<?= $file->id ?>">
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
					<?php if (in_array($user_role, ['devel'])): ?>
					<td><?= h($file['filePermissions']) ?>  <?= h($file['fileOwner']) ?></td>
					<?php endif; ?>
					<td class="actions">
						<?= $this->Html->link(__('Details'), ['action' => 'view', $file->id, '?' => ['root' => $root]]) ?>
						<?= $this->Html->link(__('Download'), ['action' => 'download', '?' => ['root' => $root, 'path' => $entity->relativePath, 'filename' => $file['name']]]) ?>
					</td>
				</tr>

				<!-- Folders -->
			<?php else: ?>
                <tr class="files-folder actions-noframe" data-list-itemof="files"  data-id="<?= $file->id ?>">
					<td class="<?= empty($file['missing']) ? '' : 'missing' ?>"><span
							class="ui-icon ui-icon-folder-collapsed"></span><?= h($file['name']) ?></td>
					<td class="thumb"></td>
					<td><?= $file['modified']->i18nFormat(null, 'Europe/Paris') ?></td>
					<td></td>
					<?php if (in_array($user_role, ['devel'])): ?>
					<td><?= h($file['filePermissions']) ?> <?= h($file['fileOwner']) ?></td>
					<?php endif; ?>
					<td class="actions">
						<?= $this->Html->link(__('Open'), ['action' => 'index', '?' => ['root' => $root, 'path' => (empty($entity->relativePath) ? '' : ($entity->relativePath . '/')) . $file['name']]]) ?>
					</td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>

		</tbody>
	</table>
</div>


<?php if (!empty($entity['description']) || !empty($entity['config']['origin'])): ?>
	<div class="content-description">
		<p>
            <?php if (empty($entity['description'])): ?>
                <?= __('This folder is linked to an external resource: ')  ?>
                <?= $this->Html->link($entity['config']['origin'], $entity['config']['origin']) ?>

            <?php else: ?>
			    <?= h($entity['description']) ?>
            <?php endif; ?>
		</p>

        <?php if (!empty($entity['config']['origin'])): ?>
            <?= $this->Link->authLink(
                __('Pull from origin'),
                ['action' => 'pull', $entity->id],
                [
                    'linktype' => 'post',
                    'confirm' => __('This will replace all contents in this folder with the origin content. Are you ready to proceed?'),
                    'class' => 'button'
            ]) ?>
        <?php endif; ?>
	</div>
<?php endif; ?>


<!-- Actions -->
<?php
    $this->Link->beginActionGroup('bottom');
    $this->Link->addAction(__('Create folder'), ['action' => 'newfolder', '?' => ['root' => $root, 'path' => $entity->relativePath]], ['data-role'=>'add']);

    if (in_array($user_role, ['devel']) && !empty($entity->relativePath)) {
        $this->Link->addAction(__('Edit'),['action' => 'edit', '?' => ['root' => $root, 'path' => $entity->relativePath]],['shortcuts' => ['F2']]);
        $this->Link->addAction(__('Move folder'),['action' => 'move', $entity->id,'item', '?' => ['root' => $root, 'path' => $entity->relativePath]] );
        $this->Link->addAction(__('Move content'),['action' => 'move', '?' => ['root' => $root, 'path' => $entity->relativePath]] );
        $this->Link->addAction(__('Clear thumbs'),['action' => 'clearthumbs', '?' => ['root' => $root, 'path' => $entity->relativePath]]);
        $this->Link->addAction(__('Clean names'),['action' => 'clean', '?' => ['root' => $root, 'path' => $entity->relativePath]]);
        $this->Link->addAction(__('Sync'),['action' => 'sync', '?' => ['root' => $root, 'path' => $entity->relativePath]],['confirm'=>__('This will recurse all files and folders and will stress the server. Are you ready  to proceed?')]);
        $this->Link->addAction(__('Fetch file'),['action' => 'fetch', '?' => ['root' => $root, 'path' => $entity->relativePath]]);
    }

    $this->Link->beginActionGroup('bottom-right');
    $this->Link->addAction(__('Download latest file'),['action' => 'download', '?' => ['root' => $root, 'path' => $entity->relativePath, 'find' => 'latest']]);
    if (in_array($user_role, ['devel']) && !empty($entity->relativePath)) {
        $this->Link->addAction(__('Zip'), ['action' => 'download', $entity->id]);
    }
?>

