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
 * @var \App\Model\Entity\User $entity
 */
?>

<!-- Breadcrumbs -->
<?php
    $this->Breadcrumbs->add(__('Users'), ['action' => 'index']);
    $this->Breadcrumbs->add(__('Invite'));
?>

<!-- Content area -->
<?php $stage = $this->getConfig('options')['stage'] ?? 'confirm'; ?>
<?php if ($stage === 'confirm'): ?>

    <?= $this->EntityInput->entityForm(
        $entity,
        'invite',
        ['confirm' => __('Generate invitation for user "{0}" ({1})?', $entity->captionPath, $entity->email)]
    ) ?>

<?php elseif ($stage === 'show'): ?>
    <?php $email =  $entity->getInvitationMail(); ?>

    <p>Here comes a template including the activation link. You can send it to the new user.</p>

    <?php $this->Form->create(null, ['id' => 'form-mail']) ?>
    <?= $this->Form->control('receiver',['type'=>'email','value' => $email['receiver'], 'label'=>__('Receiver')]) ?>
    <?= $this->Form->control('subject',['value' => $email['subject'], 'label'=>__('Subject')]) ?>

    <?= $this->Form->textarea('body', [
        'label' => __('Invitation'),
        'value' => $email['body'],
        'rows' => 15,
    ]) ?>

    <?php
    $this->Link->addCancelAction(['controller' => 'Users', 'action' => 'view', $entity->id]);
    //$this->Link->addSubmitAction(__('Send'), ['form' => 'form-mail']);
    ?>

<?php $this->Form->end() ?>

<?php endif; ?>
