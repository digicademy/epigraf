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
 * @var \Cake\ORM\Entity $entity The current selected folder
 */

use App\Utilities\Files\Files;
use Cake\Routing\Router;

?>

<?php $this->Breadcrumbs->add(__('Files')); ?>
<?php if (!empty($entity['root'])) $this->Breadcrumbs->add($entity['root']); ?>
<?php if (!empty($entity['path'])) $this->Breadcrumbs->add($entity['path']); ?>
<?php if (!empty($entity['name'])) $this->Breadcrumbs->add($entity['name']); ?>

<?php $listName =  $this->getConfig('options')['params']['list'] ?? 'files'; ?>
<?php $download = $this->getConfig('options')['params']['download'] ?? true; ?>

<div class="content-main widget-scrollbox">
	<table class="recordlist widget-table">
		<thead>
		<tr>
			<th scope="col">Name</th>
			<th scope="col">Modified</th>
			<th scope="col">Size</th>
		</tr>
		</thead>
        <?php
        $data_url =  Router::url([
            'action' => 'select',
            '?' => [
                'root' => $entity['root'],
                'path' => $entity->relativeFolder,
                'basepath' => $entity->basepath,
                'download' => $download ? '1' : '0'
            ]
        ]);
        ?>
		<tbody
            data-list-name="<?= $listName ?>"
            data-value="<?= $entity->basedFolder ?>"
            data-url="<?= $data_url ?>"
        >
            <?php foreach ($entity->files as $file): ?>

               <!-- Folders -->
                <?php if (!empty($file['isfolder'])): ?>
                    <?php
                        $data_url =  Router::url([
                            'action' => 'select',
                            '?' => [
                                'root' => $entity['root'],
                                'path' => Files::prependPath($entity->basepath, $file['fullname']),
                                'basepath' => $entity->basepath,
                                'download' => $download ? '1' : '0'
                            ]
                        ]);
                    ?>

                    <tr data-list-itemof="<?= $listName ?>"
                        data-list-itemtype="folder"
                        data-value="<?= $file['fullname'] ?>"
                        data-url="<?= $data_url ?>"
                    >
                        <td><span class="ui-icon ui-icon-folder-collapsed"></span><?= h($file['name']) ?></td>
                        <td><?php if (!empty($file['modified']))
                                echo $file['modified']->i18nFormat(null, 'Europe/Paris'); ?></td>
                        <td></td>
                    </tr>

                <!-- Files -->
                <?php else: ?>
                    <?php
                        $data_url =  Router::url([
                            'action' =>  $download ? 'download' : 'display',
                            '?' => [
                                'root' => $entity['root'],
                                'path' => $entity['relative_path'],
                                'filename' => $file['name'],
                            ]
                        ]);
                    ?>

                    <tr data-list-itemof="<?= $listName ?>"
                        data-list-itemtype="file"
                        data-value="<?= $file['fullname'] ?>"
                        data-url="<?= $data_url ?>"
                    >
                        <td><?= h($file['name']) ?></td>
                        <td><?= $file['modified']->i18nFormat(null, 'Europe/Paris') ?></td>
                        <td><?= h($file['size']) ?></td>
                    </tr>
                <?php endif; ?>

            <?php endforeach; ?>
		</tbody>
	</table>
</div>

<div data-position="fixed">
    <?= $this->Files->dropzone(['root'=>$entity->root, 'path' => $entity->relativePath, 'list' => $listName]) ?>
</div>

<!-- Actions -->
<?php if ($this->getRequest()->getQuery('template') === 'upload'): ?>
    <?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');

    $this->Link->addAction(
        __('Add all to article'),
        ['?' => ['root'=>$entity->root, 'path' => $entity->relativePath, 'basepath' => $entity->relativePath,'template'=>'upload']],
        [
            'class' => 'button button_import',
            'data-role' => 'import'
        ]
    );
    ?>
<?php endif; ?>
