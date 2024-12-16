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
 * @var \App\Model\Entity\User $entity
 */
?>
<!-- Breadcrumbs -->
<?php
    $this->Breadcrumbs->add(__('Users'), ['action' => 'index']);
    $this->Breadcrumbs->add('Grant access to database');
?>

<!-- Content area -->
<div class="content-extratight">
    <?php $formId = 'form-grant-users-' . $entity->id; ?>
    <?= $this->Form->create($entity, ['type' => 'post', 'id' => $formId]) ?>
            <?= $this->Form->control('databank_id',
                ['options' => $this->getConfig('options')['databanks'] ?? [], 'empty' => false, 'label' => __('Database')]);
            ?>
            <?= $this->Form->control('scope',
                ['options' =>  $this->getConfig('options')['scopes'] ?? [], 'empty' => false, 'label' => __('Scope')]);
            ?>
            <?= $this->Form->control('role',
                ['options' =>  $this->getConfig('options')['roles'] ?? [], 'empty' => true, 'required' => false, 'label' => __('Role')]);
            ?>
            <div class="confirm">
                <?= $this->Form->button(__('Grant access')) ?>
                <?= $this->Link->cancelLink(['controller'=>'users','action' => 'view', $entity->id], $formId) ?>
            </div>
    <?= $this->Form->end() ?>
</div>
