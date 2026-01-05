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
 * @var array $email
 */
?>

<!-- Breadcrumbs -->
<?php
    $this->Breadcrumbs->add(__('Users'), ['action' => 'index']);
    $this->Breadcrumbs->add(__('Mail'));
?>

<!-- Content area -->
<?= $this->Form->create(null, ['id' => 'form-mail']) ?>
<?= $this->Form->control('receiver',['value' => $email['receiver'] ?? '', 'type'=>'email', 'label'=>__('Receiver')]) ?>
<?= $this->Form->control('subject',['value' => $email['subject'] ?? '', 'label'=>__('Subject')]) ?>

<?= $this->Form->textarea('body', [
    'label' => __('Message'),
    'value' => $email['body'] ?? '',
    'rows' => 15,
]) ?>

<?= $this->Form->end() ?>

<?php
    $this->Link->addCancelAction(['controller' => 'Users', 'action' => 'index']);
    $this->Link->addSubmitAction(__('Send'), ['form' => 'form-mail']);
?>
