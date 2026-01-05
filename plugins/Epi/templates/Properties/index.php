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

    use App\Utilities\Converters\Attributes;
    use Cake\Routing\Router;

?>

<?php $scope = $this->getConfig('options')['scope'] ?? ''; ?>

<!-- Sidebar setup -->
<?php $this->setSidebarConfigByMode(); ?>

<!-- Search -->
<?php if ($this->getShowBlock('searchbar')): ?>

    <!-- Search -->
    <div class="content-searchbar">

        <?php $scope = $this->getConfig('options')['scope'] ?? ''; ?>
        <?= $this->Table->filterSearch(
            "epi_properties",
            "",
            "find",
            $this->getConfig('options')['params']['find'] ?? '',
            false,
            [
                "class" => "content-searchbar-item-main small-order-3",
                "label" => __('Jump'),
                'placeholder' => __('Jump to letter, e.g. type "me - b" ')
            ]
        ) ?>

        <?= $this->Table->filterSearch(
            "epi_properties",
            "",
            "term",
            $this->getConfig('options')['params']['term'] ?? '',
            [
                'field'=> $this->getConfig('options')['params']['field'] ?? '',
                'options' => $this->getConfig('options')['filter']['search'] ?? []
            ],
            [
                "class" => "content-searchbar-item-main small-order-4",
                "label" => __('Search'),
                'placeholder' => __('Search properties'),
                'form' => Router::url(['controller' => 'Properties', 'action' => 'index',''])
            ]
        ) ?>

        <?= $this->Table->filterSelector(
            "epi_properties",
            "articles.projects",
            $this->getConfig('options')['filter']['projects'] ?? [],
            $this->getConfig('options')['params']['articles']['projects'] ?? [],
            [
                "label" => __('Projects'),
                'reset' => true,
                'checkboxlist' => true,
                'searchable' => true,
                'class' => 'small-order-2'
            ]
        ) ?>

        <div class="content-searchbar-item content-searchbar-item-sub show-small small-order-1">
            <div class="input-group">
                <button  class="accordion-toggle" data-toggle-accordion="sidebar-left">
                    <?=  __('Navigation') ?>
                </button>
            </div>
        </div>

    </div>

<?php endif; ?>

<!-- Content area -->
<?php if ($this->request->getQuery('template') === 'select'): ?>
    <?= $this->element('../Properties/index_select') ?>
<?php elseif ($this->request->getQuery('template') === 'choose'): ?>
    <?= $this->element('../Properties/index_select') ?>
<?php elseif ($this->request->getQuery('template') === 'input'): ?>
    <?= $this->element('../Properties/index_input') ?>
<?php else: ?>
    <?= $this->element('../Properties/index_table') ?>
<?php endif; ?>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup('bottom');
    $this->Link->addCounter();
    $this->Link->addActionGroupLabel(__('Context Actions'));
    $this->Link->addCreateAction(
        __('Create property'),
        [
            'data-list-select' => 'epi_properties',
            'data-list-param' => 'reference'
        ],
        ['action' => 'add', $scope]
    );

    $this->Link->addAction(
        __('Import'),
        ['controller' => 'Properties', 'action' => 'import', $scope],
        [
            'class' => 'popup actions-set-default',
            'data-popup-modal' => true
        ]
    );

    //TODO what else to filter out?
    $queryparams = Attributes::paramsToQueryString(
        $this->getConfig('options')['params'],
        ['selected', 'template', 'action']
    );

    $this->Link->addAction(
        __('Transfer'),
        ['action' => 'transfer', $scope, '?' => $queryparams],
        [
            'data-list-select' => 'epi_properties',
            'data-list-param' => 'properties',
            'class' => 'popup actions-set-default',
            'data-popup-modal' => true
        ]
    );

    $this->Link->addAction(
        __('Mutate'),
        ['action' => 'mutate', $scope],
        [
            'data-list-select' => 'epi_properties',
            'data-list-param' => 'id',
            'class' => 'popup actions-set-default',
            'data-popup-modal' => true
        ]
    );

    $this->Link->addAction(
        __('Move'), null,
        [
            'linktype' => 'button',
            'class' => 'actions-set-default widget-switch hide-small',
            'data-role' => 'move',
            'data-switch-class' => 'hide',
            'data-switch-element' => '.actions-set-default, .actions-set-move',
            'data-target-model' => 'epi.properties',
            'roles' => ['admin','editor','author']
        ]
    );


    $this->Link->addAction(
        __('Save'),
        [],
        [
            'linktype' => 'button',
            'class' => 'actions-set-move hide',
            'data-switch-reverse'  => '1',
            'data-role' => 'save'
            // TODO: Add shortcuts without conflicting the entity save action in the sidebar
            //'shortcuts' => ['Ctrl+S', 'F10']
        ]
    );

    $this->Link->addAction(
        __('Cancel'),
        $this->request->getUri(),
        [
            'data-role' => 'cancel',
            'data-switch-reverse'  => '1',
            'class' => 'actions-set-move hide'
        ]
    );

?>


<?php
    $this->Link->beginActionGroup('bottom-right');
    $this->Link->toggleModes($queryparams, $queryparams['propertytype'] ?? '');
    $this->Link->exportButtons($queryparams, 'epi_properties');
?>
