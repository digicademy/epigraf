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
    <div class="content-tight">
        <?= $this->EntityInput->entityForm($entity, 'merge') ?>
    </div>

    <!-- Actions -->
    <?php
        $this->setShowBlock(['footer']);
        $this->Link->beginActionGroup ('bottom');
        $this->Link->addMergeConfirmButtons($propertySource);
    ?>

</fieldset>
<?= $this->Form->end() ?>
