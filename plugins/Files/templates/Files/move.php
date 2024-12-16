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
 * @var string $target
 * @var string $root
 */
?>
<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Files'), ['controller' => 'Files','action' => 'index']); ?>
<?php if (!empty($item['root'])) $this->Breadcrumbs->add($item['root']);?>
<?php if (!empty($item['path'])) $this->Breadcrumbs->add($item['path']); ?>
<?php if (!empty($item['name'])) $this->Breadcrumbs->add($item['name']); ?>

<!-- Content area -->
<div class="content-tight">
    <?= $this->Form->create($item,['id'=>'form-move-files-' . $item->id]) ?>

    <table class="vertical-table">
            <tr>
                <th scope="row"><?= __('Move to') ?></th>
                <td>
                    <?= $this->Form->control(
                        'target',
                        [
                            'type' => 'choose',
                            'options' => [
                                'controller' => 'Files',
                                'action' => 'select',
                                '?'=>['root' => $root, 'path'=>$target],
                            ],
                            'itemtype'=>'folder',
                            'label' => false,
                            'value' => empty($target) ? '' : $target
                        ]);
                    ?>
                </td>
            </tr>
        <tr>
            <th scope="row"><?= __('Options') ?></th>
            <td><?= $this->Form->control('overwrite', ['type' => 'checkbox']); ?></td>
        </tr>

    </table>


    <?= $this->Form->end() ?>
</div>
<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->beginActionGroup ('bottom');

        $this->Link->addAction(
        __('Cancel'),
        //TODO: index action for moving content
        ['controller' => 'Files', 'action' => 'view', $item->id, '?' => ['root' => $item['root'], 'path' => $item['path']]],
        ['class' => 'button button_cancel']
    );

    $this->Link->addAction(
        __('Move'),
        [],
        ['linktype' => 'submit', 'form' => 'form-move-files-' . $item->id]
    );
?>
