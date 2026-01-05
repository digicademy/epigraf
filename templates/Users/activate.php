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
    $this->Breadcrumbs->add(__('Activate Your Account'));
?>

<!-- Content area -->
<div class="content-extratight">
    <?php $formId = 'form-password-users-' . $entity->id; ?>
    <?= $this->Form->create($entity, ['id'=> $formId]) ?>
        <fieldset>
            <p><?= __('Please, set a strong password and keep it safe.') ?></p>
            <p><?= __('It must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.') ?></p>
            <?= $this->Form->control('password',['type' => 'password', 'value'=>'']); ?>
            <?= $this->Form->control('repeat',['type' => 'password', 'value'=>'','label' => __('Repeat the password.')]); ?>

            <p>
                <?= __('Please, carefully read the <a href="/pages/privacy" target="_blank">usage terms and the privacy policy</a>. By activating your account, you accept those terms.') ?>
            </p>

            <div class="confirm">
                <?= $this->Link->cancelLink(['controller'=>'users','action' => 'login'], $formId) ?>
                <?= $this->Form->button(__('Submit')) ?>
            </div>
        </fieldset>
    <?= $this->Form->end() ?>
</div>
