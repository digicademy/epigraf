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
 * @var \Cake\Collection\Collection $entities
 */

use App\Model\Table\PermissionsTable;
use App\Utilities\Converters\Attributes;
use Cake\Routing\Router;

?>

<!-- Right sidebar -->
<?php $this->setSidebarConfig(['right'=> ['size' =>5]]); ?>

<!-- Content area -->
<?= $this->Table->getProblems() ?>

<div class="content-searchbar">

    <?php // replace by $this->Table->filterBar('epi.types') ?>
    <?= $this->Table->filterSearch(
        "epi_types",
        "",
        "term",
        $this->getConfig('options')['params']['term'] ?? '',
        false,
        [
            'class' => 'content-searchbar-item-main',
            'label' => __('Search'),
            'autofocus' => true,
            'placeholder' => __('Search by name, caption or IRI fragment'),
            'form' => Router::url(['controller' => 'Types', 'action' => 'index'])
        ]
    ) ?>

    <?= $this->Table->filterSelector(
        "epi_types",
        "scopes",
        $this->getConfig('options')['filter']['scopes'] ?? [],
        $this->getConfig('options')['params']['scopes'] ?? [],
        [
            "class" => "content-searchbar-item",
            "label" => __('Scopes'),
            'reset' => true,
            'checkboxlist' => true
        ]
    ) ?>

    <?= $this->Table->filterSelector(
        "epi_types",
        "modes",
        PermissionsTable::$requestModes,
        $this->getConfig('options')['params']['modes'] ?? [],
        [
            "class" => "content-searchbar-item",
            "label" => __('Modes'),
            'reset' => true,
            'checkboxlist' => true
        ]
    ) ?>

    <?= $this->Table->filterSelector(
        "epi_types",
        "categories",
        $this->getConfig('options')['filter']['categories'] ?? [],
        $this->getConfig('options')['params']['categories'] ?? [],
        [
            "class" => "content-searchbar-item",
            "label" => __('Categories'),
            'reset' => true,
            'checkboxlist' => true
        ]
    ) ?>

    <?php // $this->Table->filterReset("epi_types") ?>
</div>


<div class="content-main widget-scrollbox">
    <?= $this->Table->filterTable('epi.types', $entities, ['select'=>true, 'actions'=>['view'=>true]]) ?>
</div>


<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');

    $this->Link->addCounter();
    $this->Link->addActionGroupLabel(__('Context Actions'));
    $this->Link->addCreateAction(__('Create type'));
    $this->Link->addAction(__('Import'), ['action' => 'import']);

    $queryparams = Attributes::paramsToQueryString($this->getConfig('options')['params'] ?? [], ['action']);
    $this->Link->addAction(
        __('Transfer'),
        ['action' => 'transfer' , '?' => $queryparams],
        [
            'data-list-select' => 'epi_types',
            'data-list-param' => 'id',
            'class' => 'popup',
            'data-popup-modal' => true
        ]
    );


    $this->Link->exportButtons($queryparams, 'epi_types');
?>
