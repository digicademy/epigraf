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
<div class="content">
    <?php $formId = 'form-grant-users-' . $entity->user_id; ?>
    <?= $this->Form->create($entity, ['type' => 'post', 'id' => $formId]) ?>

        <?= $this->EntityInput->entityTable($entity, 'grant') ?>

        <div class="confirm">
            <?= $this->Form->button(__('Grant access')) ?>
            <?= $this->Link->cancelLink(['controller'=>'users','action' => 'view', $entity->user_id], $formId) ?>
        </div>

    <?= $this->Form->end() ?>
</div>
