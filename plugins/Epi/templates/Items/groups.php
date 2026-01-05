<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

use Epi\Model\Table\BaseTable;

?>

<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Databank $database
 * @var string $user_role
 * @var array $groups
 */
?>

<?php $template = $this->getConfig('options')['params']['template'] ?? 'table' ?>

<?php if ($template === 'table'): ?>
    <?php $columns =$this->getConfig('options')['columns'] ?? ['x','y','z', 'totals', 'grouptype']; ?>
    <?= $this->Table->simpleTable($groups, $columns) ?>
<?php else: ?>

    <?php $this->Html->script('Widgets.plotly/plotly-2.8.3.min.js', ['block' => true]); ?>

    <?php
      if ((BaseTable::$requestMode ?? MODE_DEFAULT) !== MODE_DEFAULT) {
            $url = ['?' => ['mode' => BaseTable::$requestMode]];
        } else {
            $url = [];
        }
    ?>
    <?= $this->Element->openHtmlElement(
        'div',
        [
            'class' => 'widget-plot',
            'data-view-url' => $this->Url->build(array_replace_recursive(['controller' => '{type}', 'action'=>'view', '{dbid}'], $url)),
            'data-scope' => $this->getConfig('options')['scope'] ?? ''
        ])
    ?>

        <script type="application/json" data-snippet="widget-data">
          <?php
            $data = [];
            foreach ($groups as $group) {
                $data[] = json_encode($group->getDataForExport([],'json'));
            }
          ?>
          [<?= implode(',',$data) ?>]
        </script>
    <?= $this->Element->closeHtmlElement('div') ?>

<?php endif; ?>

