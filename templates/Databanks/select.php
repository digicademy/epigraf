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
 * @var string[] $connections
 */
?>
<?php //TODO: add list markup, see file select template, ChooseWidget and buttons.js:ChooseButtons ?>
<table class="recordlist">
    <tbody data-list-name="databanks">
        <?php foreach ($connections as $connection): ?>
        <tr data-list-itemof="databanks" data-list-itemtype="databank" data-value="<?= h($connection) ?>">
            <td><?= h($connection) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
