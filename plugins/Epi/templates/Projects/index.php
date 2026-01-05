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
    use App\Utilities\Converters\Attributes;
    use Cake\Routing\Router;
    use Cake\Utility\Hash;
?>

<?php
/**
 * @var \App\View\AppView $this
 * @var Cake\ORM\ResultSet $entities
 * @var \App\Model\Entity\Databank $database
 */
?>


<?php if ($this->getShowBlock('searchbar')): ?>
<!-- Search bar -->
    <div class="content-searchbar">

        <?php // TODO: replace by $this->Table->filterBar('epi.projects') ?>
        <?= $this->Table->filterSearch(
            "epi_projects",
            "",
            "term",
            $this->request->getQuery('term'),
            false,
            [
                "class" => "content-searchbar-item-main",
                "label" => __('Search'),
                'autofocus' => true,
                'placeholder' => __('Search projects'),
                'form' => Router::url(['controller' => 'Projects', 'action' => 'index'])
            ]
        ) ?>

        <?= $this->Table->filterSelector(
            "epi_projects",
            "projecttypes",
            Hash::combine($database->types, 'projects.{*}.name', 'projects.{*}.caption'),
            $this->getConfig('options')['params']['projecttypes'] ?? [],
            [
                "label" => __('Project types'),
                'reset' => true,
                'checkboxlist' => true
            ]
        ) ?>

        <?php // $this->Table->filterReset("epi_projects") ?>

    </div>
<?php endif; ?>

<!-- Content area -->
<!-- Content area -->
<?php if ($this->request->getQuery('template') === 'select'): ?>
    <?= $this->element('../Projects/index_select') ?>
<?php else: ?>
    <?= $this->element('../Projects/index_table') ?>
<?php endif; ?>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');
    $this->Link->addCounter();
    $this->Link->addActionGroupLabel(__('Context Actions'));
    $this->Link->addCreateAction(__('Create project'));

    $this->Link->addAction(__('Import'),['action' => 'import']);

    //TODO what else to filter out?
    $queryparams = Attributes::paramsToQueryString(
        $this->getConfig('options')['params'],
        ['selected', 'template', 'action']
    );

    $this->Link->addAction(
            __('Transfer'),
            ['action'=>'transfer','?' => $queryparams],
            [
                'data-list-select'=>'epi_projects',
                'data-list-param'=>'id',
                'class' => 'popup',
                'data-popup-modal' => true
            ]
        );

    $this->Link->downloadButtons (null, 'projects', 'epi_projects');
 ?>
