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

<div id="properties-selector" class="properties select columns content">
    <div class="results widget-scrollbox">
        <?=
            $this->Tree->selectTree(
                'epi.projects.tree',
                $entities,
                [
                    'fold' => 'fixed',
                    'paginate' => 'cursor'
                ]
            )
        ?>
    </div>
</div>
