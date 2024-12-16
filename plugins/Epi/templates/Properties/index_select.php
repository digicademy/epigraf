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

<?php
    $template = $this->request->getQuery('template', 'select');
    $scope = $this->getConfig('options')['scope'] ?? '';
    $type = $database->types['properties'][$scope] ?? null;
    $tableModel = 'epi.properties.' . $template;
?>

<?php $this->Breadcrumbs->add(__('Categories'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($type['caption'] ??  $scope) ?>

<div id="properties-selector" class="properties select columns content">
    <div class="results widget-scrollbox">
        <?=
            $this->Tree->selectTree(
                $tableModel,
                $entities,
                [
                    'tree' => $type['merged']['type'] ?? 'tree',
                    'collapsed' => $this->getConfig('options')['params']['collapsed'] ?? (($type['merged']['type'] ?? 'tree') === 'collapsed'),
                    'scope' => $scope,
                    'paginate' => 'cursor'
                ]
            )
        ?>
    </div>
</div>
