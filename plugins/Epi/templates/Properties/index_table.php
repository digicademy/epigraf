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
 * @var \App\View\AppView $this
 * @var Cake\ORM\ResultSet $entities
 * @var \App\Model\Entity\Databank $database
 */
?>

<!-- Structure -->
<?php
    $scope = $this->getConfig('options')['scope'] ?? '';
    $type = $database->types['properties'][$scope] ?? null;
    $tableModel = 'epi.properties';
 ?>

<!-- Items -->
<div class="content-main widget-scrollbox" data-snippet="rows">

    <?=
        $this->Table->filterTable(
            $tableModel,
            $entities,
            [
                'select'=> true,    // Column selector
                'snippet' => false, // Prevents the column selector to vanish

                'tree' => $type['merged']['type'] ?? 'tree',
                'collapsed' => $this->getConfig('options')['params']['collapsed'] ?? (($type['merged']['type'] ?? 'tree') === 'collapsed'),
                'sort' => false,
                'fold' => 'foldable',
                'drag' => true,
                'paginate' => 'cursor',
                'scope' => $scope,
                'actions' => ['view' => true],
                'class' => 'widget-filter-item widget-filter-item-fixed',
                'data' => ['data-filter-path' => $scope]
            ]
        )
    ?>

</div>
