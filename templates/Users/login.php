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
<?php $this->Breadcrumbs->add(__('Login')); ?>
<?= $this->Flash->render('auth') ?>

<div class="content-extratight">
    <?= $this->Form->create(null, ['id' => 'form-login','data-message'=>'']) ?>
    <fieldset>
        <?= $this->Form->control('username', ['autocomplete' => 'username', 'required' => true, 'autofocus' => true]) ?>
        <?= $this->Form->control('password', ['autocomplete' => 'current-password', 'required' => true]) ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<?php
    $this->Link->beginActionGroup('content');
    $this->Link->addAction(
        __('Login'),
        [],
        ['linktype' => 'submit', 'form' => 'form-login']
    );
?>
