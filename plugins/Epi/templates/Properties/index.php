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
    use Cake\Utility\Hash;

?>

<?php $scope = $this->getConfig('options')['scope'] ?? ''; ?>

<!-- Right sidebar -->
<?php $this->sidebarSize(['left' => 0, 'right' => 5]); ?>

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
                "class" => "content-searchbar-item-main",
                "label" => __('Jump'),
                'placeholder' => __('Jump to letter, e.g. type "Me > b" '),
                'form' => Router::url(['controller' => 'Properties', 'action' => 'index',''])
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
                "class" => "content-searchbar-item-main",
                "label" => __('Search'),
                'placeholder' => __('Search properties'),
                'form' => Router::url(['controller' => 'Properties', 'action' => 'index',''])
            ]
        ) ?>

        <?php // $this->Table->filterReset("epi_properties", [$scope]) ?>
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
        ['action' => 'import', $scope],
        ['class'=>'actions-set-default']
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
            'data-list-param' => 'properties',
            'class' => 'popup actions-set-default',
            'data-popup-modal' => true
        ]
    );

    $this->Link->addAction(
        __('Move'), null,
        [
            'linktype' => 'button',
            'class' => 'actions-set-default widget-switch',
            'data-role' => 'move',
            'data-switch-class' => 'hide',
            'data-switch-element' => '.actions-set-default, .actions-set-move',
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

<?php $this->Link->downloadButtons (null, 'properties', 'epi_properties'); ?>
