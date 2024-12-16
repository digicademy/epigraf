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
 * @var \Epi\Model\Entity\Property[] $propertySources
 */
?>

<?php $propertySource = $propertySources->first(); ?>
<?php $propertySourceIds = implode(',', $propertySources->all()->extract('id')->toArray()) ?>

<?php
    $options = [
        'id' => 'form-merge-properties',
        'type' => 'get',
        'data-cancel-url' => $this->Url->build(['action' => 'view', $propertySource->id])
    ];
?>
<?= $this->Form->create(null, $options) ?>

<fieldset>
    <div class="content-tight">

        <h2><?= __('Target property') ?></h2>
        <?php
            $options = [
                'caption' => false,
                'type' => 'reference',
                'url' => [
                    'controller' => 'Properties',
                    'action' => 'index',
                    $propertySource->propertytype,
                    '?' => ['template'=>'choose']
                ],
                'paneSnippet' => 'rows',
                'listValue' => 'id', //<- which attribute do the items carry? data-id (for trees) or data-value (everything else)

            ];
        ?>
        <?= $this->Form->input('target', $options); ?>

        <h2><?= __('Source properties') ?></h2>
        <?= $this->Form->hidden('source',['value'=>  $propertySourceIds]); ?>
        <?= $this->Table->filterTable(
            'sources',
            $propertySources,
            [
                'columns' => [
                    'path'=>['caption'=>__('Path'), 'default'=>true],
                    'norm_iri'=>['caption'=>__('IRI fragment'), 'default'=>true],
                    'modified'=>['caption'=>__('Modified'), 'default'=>true]
                ],
                'actions' => ['view' => false]
            ])
        ?>
    </div>

    <!-- Actions -->
    <?php
        $this->setShowBlock(['footer']);
        $this->Link->beginActionGroup ('bottom');
        $this->Link->addMergePreviewButtons($propertySource);
    ?>

</fieldset>
<?= $this->Form->end() ?>
