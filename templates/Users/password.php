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
 * @var \App\Model\Entity\User $user
 */
?>

<!-- Breadcrumbs -->
<?php
$this->Breadcrumbs->add(__('Users'), ['action' => 'index']);
$this->Breadcrumbs->add(__('Set mySQL-password'));
?>

<!-- Content area -->
<div class="content-extratight">
    <?php $formId = 'form-password-users-' . $user->id; ?>
    <?= $this->Form->create($user, ['id'=> $formId]) ?>
        <fieldset>
            <?= $this->Form->control('password',['value'=>'']); ?>

            <div class="confirm">
                <?= $this->Link->cancelLink(['controller'=>'users','action' => 'view', $user->id], $formId) ?>
                <?= $this->Form->button(__('Submit')) ?>
            </div>
        </fieldset>
    <?= $this->Form->end() ?>
</div>
