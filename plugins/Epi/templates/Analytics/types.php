<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
?>

<!-- Embed plotly-->
<?php
    use Cake\I18n\I18n;
    $this->Html->script('Widgets.plotly/plotly-2.8.3.min.js', ['block' => true]);
?>

<?php $this->Html->script('Epi.analytics.js', ['block' => true]); ?>


<?php $this->Breadcrumbs->add('Übersicht über den Datenbestand des Projekts'); ?>

<!--Loop over every Table-->
<?php foreach ($data as $table): ?>
    <h2><?= $table['label'] ?></h2>

	<div class="content-row">

		<!--Create Table-->
		<div class="content-col">
			<?php $tableId =  $table['label']; ?>
			<table id="table-<?= $tableId ?>" class="simple-table">
				<thead>
				<tr>
					<th><?= __('Type')?></th>
                    <?php foreach ($table['cols'] as $col): ?>
					    <th class = "align-right"><?= I18n::getTranslator()->translate(ucfirst($col)) ?></th>
                    <?php endforeach; ?>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($table['rows'] as $row => $cols): ?>
					<tr class="analytics-row">
						<td><?= $row ?> </td>
                        <?php foreach ($table['cols'] as $col): ?>
						    <td class = "align-right"><?= $cols[$col] ?></td>
                        <?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				<tr>
					<td><b><?= __('total') ?> </b></td>
                    <?php foreach ($table['cols'] as $col): ?>
					    <td class = "align-right"><b><?= $table['total'][$col] ?? '' ?></b></td>
                    <?php endforeach; ?>
				</tr>
				</tbody>
			</table>
		</div>

		<!-- Create Plot-->
		<div class="content-col">
			<div id="canvas-<?= $tableId ?>" class = 'analytics-barchart' data-datasource="<?= $tableId ?>"></div>
		</div>
    </div>
    <span class="content-notice"><?= $table['notice'] ?></span>

<?php endforeach; ?>
