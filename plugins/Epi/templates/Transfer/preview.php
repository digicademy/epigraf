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
    use Cake\Utility\Hash;
?>
<?php
/**
 * @var App\View\AppView $this
 * @var string $scope
 * @var array $preview
 */
?>

<?php $this->Link->beginActionGroup ('bottom'); ?>
<?php $this->Link->addLabel(__('{0} records', $preview['count'])); ?>

<div class="content-main widget-scrollbox">
    <!-- Paginator -->
    <?php $nextUrl = $this->Link->nextPageUrl($scope, !empty($preview['rows'])) ?>

    <table class="recordlist widget-table">
        <thead>
        <tr>
            <th data-col="rownumber" data-width="40">#</th>
            <th data-col="table" data-width="100">table</th>
            <?php foreach ($preview['cols'] as $item => $value): ?>
                <th scope="col" data-col="<?= $value ?>" data-width=100><?= $value ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody data-list-name="import"
               data-list-action-next="<?= $nextUrl ?>">

        <?php foreach ($preview['rows'] as $no => $row): ?>
            <tr data-list-itemof="import" data-id="<?= ($row->import_table ?? '') . '-' .  $this->request->getQuery('page',1) . '-' . (($row->row_number ?? '') . '-' . ($row->id ?? 'new')) ?>">
                <td><?= $row->row_number ?? '' ?></td>
                <td><?= $row->import_table ?? '' ?></td>
                <?php foreach ($preview['cols'] as $col): ?>

                    <?php
                    $classes = [];
                    if (in_array($col,array_keys($row->_import_ids))) {
                        $classes = ['table-id-unsolved'];
                        $value = $row->_import_ids[$col] ?? '';
                    }

                    //TODO: show original ID values, not matched IDs
                    elseif (in_array($col,$row->getIdFields()) && ($row[$col])) {
                        $classes = ['table-id-solved'];
                        $value = $row[$col] ?? '';
                    }
                    elseif (($col === 'norm_iri') && $row->_import_irimatched)  {
                        $classes = ['table-id-solved'];
                        $value = $row[$col] ?? '';
                    } else {
                        $value = $row[$col] ?? '';
                    }

                    $value = is_array($value) ? json_encode($value) : $value;
                    $classes = empty($classes) ? '' : (' class="' . implode(" ", $classes) . '"');
                    ?>
                    <td<?= $classes ?>><?= h($value) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
</div>

<?= $this->Form->create(null, ['id' => 'form-import']) ?>

<?php if (!empty($source)): ?>
    <?= $this->Form->hidden('filename',['value'=>$source]) ?>
<?php endif; ?>

<?= $this->Form->end() ?>

<div class="confirm">
    <?php
      $cancelUrl = ['action' => 'index', $scope ?? null];
      if (!empty($job->source)) {
          $cancelUrl['database'] = $job->source;
      }
    ?>

    <?php
        $this->Link->beginActionGroup('content');
        $this->Link->addCancelAction($cancelUrl);
        $this->Link->addSubmitAction(__('Import'), ['autofocus' => 'true', 'form' => 'form-import']);
    ?>
</div>
