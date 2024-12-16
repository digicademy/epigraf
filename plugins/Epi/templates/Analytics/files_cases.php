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
 * @var array $options
 * @var string $table
 * @var string $nexturl
 * @var array $cases
 * @var string $summary
 */
?>
<?php
    // Assemble URL for next page
    $paging = $this->Paginator->params();
    $nexturl = ($paging['nextPage']) ? $this->Paginator->generateUrl(['page' => $paging['page'] + 1]) : '';
?>

<?php $this->Breadcrumbs->add(ucfirst($table)); ?>

<div class="recordlist-filter">
    <?php foreach($options as $key => $value): ?>
    <?php if ($value !== null): ?>
        <?= $key ?>=<?= $value ?>.
    <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Create Table-->
<div class="content-main content-extratight widget-scrollbox">
	<table class="recordlist" data-list-name="items" data-list-action-next="<?= $nexturl ?>">

		<!-- Items -->
		<?php if ($table == 'items'): ?>

			<thead>
			<tr>
                <th>Projekt</th>
				<th>Artikelnummer</th>
				<th>Itemtyp</th>
				<th>Dateiname</th>
				<th>Dateipfad</th>
				<th scope="col" class="actions"><?= __('Actions') ?></th>
			</tr>
			</thead>

			<tbody>
				<?php foreach ($cases as $item): ?>
					<tr data-list-itemof="items" data-id="<?=$item['id']?>">
                        <td><?= $item['article']['project'][FIELD_PROJECTS_SIGNATURE] ?> </td>
                        <td><?= $item['article'][FIELD_ARTICLES_SIGNATURE] ?> </td>
						<td><?= $item['itemtype'] ?></td>
						<td><?= $item['file_name'] ?></td>
						<td><?= $item['file_path'] ?></td>
						<td class="actions">
							<?= $this->Html->link(__('View'), ['controller'=>'articles', 'action'=>'view', $item['articles_id']]) ?>
						</td>
					</tr>
				<?php endforeach; ?>
		<?php endif; ?>

		<!-- Files -->
		<?php if ($table == 'files'): ?>
			<thead>
			<tr>
				<th>Dateiname</th>
				<th>Dateityp</th>
				<th>Dateipfad</th>
				<th scope="col" class="actions"><?= __('Actions') ?></th>
			</tr>
			</thead>

			<?php foreach ($cases as $item): ?>
				<tr data-list-itemof="items" data-id="<?=$item['id']?>">
					<td><?= $item['name'] ?></td>
					<td><?= $item['type'] ?></td>
					<td><?= $item['path'] ?></td>
					<td class="actions">
                        <?= $this->Html->link(__('View'), ['controller'=>'files', 'action'=>'view', $item['id']]) ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
</div>

<?php $this->Link->beginActionGroup ('bottom'); ?>
<?php $this->Link->addCounter(); ?>
<?php $this->Link->addLabel($summary); ?>

<?php $params = $this->request->getQueryParams(); ?>
<?php $this->Link->downloadButtons ([$table,'?' => $params]); ?>
