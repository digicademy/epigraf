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
 * @var \Cake\ORM\Entity $folder The current selected folder
 */

use App\Utilities\Files\Files;
use Cake\Routing\Router;

?>

<?php $this->Breadcrumbs->add(__('Files')); ?>
<?php if (!empty($folder['root'])) $this->Breadcrumbs->add($folder['root']); ?>
<?php if (!empty($folder['path'])) $this->Breadcrumbs->add($folder['path']); ?>
<?php if (!empty($folder['name'])) $this->Breadcrumbs->add($folder['name']); ?>

<div class="content-main widget-scrollbox">
	<table class="recordlist">
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
                'root' => $folder['root'],
                'path' => $folder->relativeFolder,
                'basepath' => $folder->basepath
            ]
        ]);
        ?>
		<tbody
            data-list-name="files"
            data-value="<?= $folder->basedFolder ?>"
            data-url="<?= $data_url ?>"
        >
            <?php foreach ($folder->files as $file): ?>

               <!-- Folders -->
                <?php if (!empty($file['isfolder'])): ?>
                    <?php
                        $data_url =  Router::url([
                            'action' => 'select',
                            '?' => [
                                'root' => $folder['root'],
                                'path' => Files::prependPath($folder->basepath, $file['fullname']),
                                'basepath' => $folder->basepath
                            ]
                        ]);
                    ?>

                    <tr data-list-itemof="files"
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
                            'action' => 'download',
                            '?' => [
                                'root' => $folder['root'],
                                'path' => $folder['relative_path'],
                                'filename' => $file['name'],
                            ]
                        ]);
                    ?>

                    <tr data-list-itemof="files"
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

<?= $this->Files->dropzone() ?>
