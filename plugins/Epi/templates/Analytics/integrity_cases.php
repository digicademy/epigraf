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
 * @var App\Model\Entity\Databank $database
 * @var array $query
 * @var array $records
 */
?>
<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add($database['name']); ?>
<?php if (!empty($query)): ?>
    <?php $this->Breadcrumbs->add($query['type']); ?>
<?php endif; ?>

<!-- Content area -->
<div class="content-main widget-scrollbox">
    <table class="recordlist actions-show">
    <thead>
    <tr>
        <th scope="col"><?= __('Table') ?></th>
        <th scope="col"><?= __('Id') ?></th>

        <th scope="col"><?= __('Linked table') ?></th>
        <th scope="col"><?= __('Linked Id') ?></th>

        <th scope="col"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($records as $id => $row): ?>
        <tr>
            <td><?= ($row[0]) ?></td>
            <td><?= ($row[1]) ?></td>
            <td><?= ($row[2]) ?></td>
            <td><?= ($row[3]) ?></td>

            <td class="actions">
                <?php if ($row[2] == 'articles'): ?>
                    <?= $this->Html->link(__('Open'), ['controller' => 'Articles', 'action' => 'view', $row[3], 'database' => $this->request->getParam('database')], ['class' => 'button small']) ?>
                    <?= $this->Html->link(__('Desktop'), 'epigraf://' . $this->request->host() . '/database/' . $this->request->getParam('database') . '/article/' . $row[3], ['class' => 'button small']) ?>
                <?php endif; ?>
            </td>

        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php if (!empty($query)): ?>
    <div>
        <?= $query['sql_records'] . ' ' . $query['sql'] . ' LIMIT 100' ?>
    </div>
<?php endif; ?>
