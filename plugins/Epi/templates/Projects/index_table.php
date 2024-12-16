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

<div class="content-main widget-scrollbox">
    <?=
    $this->Table->filterTable(
        'epi.projects',
        $entities,
        [
            'select'=> true,
            'actions' => [
                'view' => true,
                'open' => [
                    'title' => __('Show articles'),
                    'plugin'=>'epi',
                    'controller' => 'Articles',
                    'action' => 'index',
                    '?' => ['projects' => '{id}', 'load'=>true]
                ]
            ]
        ]
    ) ?>
</div>
