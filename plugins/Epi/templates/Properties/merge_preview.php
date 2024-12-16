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
 * @var \Epi\Model\Entity\Property $entity The merged entity
 * @var \Epi\Model\Entity\Property[] $propertySources
 * @var \Epi\Model\Entity\Property $propertyTarget
 */
?>

<?php $entity->prepareRoot(); ?>
<?php $propertySource = $propertySources->first(); ?>

<?php
    $options = [
        'id' => 'form-merge-properties',
        'type' => 'post',
        'data-cancel-url' => $this->Url->build(['action' => 'view', $propertySource->id])
    ];
?>
<?= $this->Form->create(null, $options) ?>
<fieldset>
    <p class="merge">
        <?= __('Are you sure you want to merge the two properties? Data of the source property will be merged into the target and the source property will be deleted. This is a permanent operation that cannot be reverted!') ?>
    </p>

    <div class="content-tight">

        <h2><?= __('Preview') ?></h2>
        <?= $this->EntityInput->entityForm($entity, 'merge') ?>

        <!--h2><?= __('Target') ?></h2-->
        <?php // $this->EntityInput->entityForm($propertyTarget, 'view') ?>

        <!--h2><?= __('Sources') ?></h2-->
        <?php
        // $this->Table->filterTable(
//            'sources',
//            $propertySources,
//            [
//                'columns' => [
//                    'path'=>['caption'=>__('Path'), 'default'=>true],
//                    'norm_iri'=>['caption'=>__('IRI fragment'), 'default'=>true],
//                    'modified'=>['caption'=>__('Modified'), 'default'=>true]
//                ],
//                'actions' => ['view' => false]
//            ])
        ?>

    </div>

    <!-- Actions -->
    <?php
        $this->setShowBlock(['footer']);
        $this->Link->beginActionGroup ('bottom');
        $this->Link->addMergeConfirmButtons($propertySource);
    ?>

</fieldset>
<?= $this->Form->end() ?>
