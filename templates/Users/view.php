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
 * @var App\Model\Entity\User $entity
 */

use App\Utilities\Converters\Attributes;

?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Users'), ['action' => 'index']); ?>
<?php $this->Breadcrumbs->add($entity->captionPath); ?>

<!-- Content area -->
<div class="content-section">
    <?= $this->EntityHtml->docProblems($entity) ?>
    <h2 id="toc-overview" class="widget-scrollsync-section"><?= __('Profile') ?></h2>

    <?= $this->EntityHtml->entityForm($entity, 'view') ?>

    <?= $this->Link->authLink(
        __('Generate invitation'),
        ['action' => 'invite', $entity->id],
        [
            'class' => 'doc-item-add button tiny frame',
            // TODO: open post links in frame
            //'linktype' => 'post',
        ]
    );
    ?>

    <?= $this->Link->authLink(
        __('Regenerate access token'),
        ['action' => 'token', $entity->id],
        [
            'class' => 'doc-item-add button tiny',
            'linktype' => 'post',
            'confirm' => __('This will regenerate the access token. Are you ready to proceed?')
        ]
    );
    ?>
</div>

<?php if($entity->hasSqlAccess): ?>
<div class="content-section">
<h2 id="toc-epidesktop" class="widget-scrollsync-section"><?= __('Epigraf Desktop Settings') ?></h2>
<table class="vertical-table">
    <tr>
        <th scope="row"><?= __('Host') ?></th>
        <td><?= $_SERVER['HTTP_HOST'] ?? '' ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Port') ?></th>
        <td>3307 (lokal: 3306)</td>
    </tr>
    <tr>
        <th scope="row"><?= __('Username') ?></th>
        <td><?= 'epi_' . $entity->username; ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Password') ?></th>
        <td>*********</td>
    </tr>
    <tr>
        <th scope="row"><?= __('Database') ?></th>
        <td><?= h($entity->databank['name'] ?? '') ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('SSL CA') ?></th>
        <td>ssl\epigraf-ca-cert.pem</td>
    </tr>
    <tr class="row-has-token">
        <th scope="row"><?= __('Online URL') ?></th>
        <td>
            <?php
                $fieldId = Attributes::uuid('field-');
                $url = $this->Url->build('/', ['fullBase' => true]) . '?token=' . $entity->accesstoken;
            ?>
            <button
                class="widget-switch widget-switch-icon widget-switch-token" data-switch-element="#<?= $fieldId ?>"
                data-switch-content="<?= $url ?>"
            >
            </button>
            <span id="<?= $fieldId ?>" class="field-token"><?= str_repeat("*", 20) ?></span>
        </td>
    </tr>
</table>
</div>
<?php endif; ?>

<?php if (in_array($user_role, ['admin', 'devel'])) : ?>
<div class="content-section">
    <h2 id="toc-access" class="widget-scrollsync-section"><?= __('Databases') ?></h2>
    <?=
        $this->Table->simpleTable(
            $entity->permissionsByRole,
            $this->getConfig('options')['columns'] ?? [],
            [
                'class' => 'compact-table'
            ]
    ) ?>

</div>

<div class="content-section">
    <h2 id="toc-permissions" class="widget-scrollsync-section"><?= __('Permissions') ?></h2>
    <?= $this->Table->simpleTable(
            $entity->permissionsWithActions,
            [
                'entity_type' => __('Entity Type'),
                'entity_name' => __('Entity Name'),
                'entity_id' => __('Entity Id'),
                'permission_name' => __('Endpoint'),
                'user_role' => __('User role'),
                'user_request' => __('Request'),
                'permission_type' => __('Permission'),
                'permission_expires' => __('Expires')
            ],
            [
                'class' => 'compact-table', 'actions' => true
            ]
    ) ?>
    <?= $this->Link->authLink(
        __('Grant'),
        ['action' => 'grant', $entity->id],
        ['class' => 'doc-item-add button tiny popup']
        );
    ?>
</div>


<div class="content-section">
	<h2 id="toc-usersettings" class="widget-scrollsync-section"><?= __('User settings') ?></h2>

    <?php if (empty($entity->settings)): ?>
        <i>No settings</i>
    <?php else: ?>
        <?=	$this->Table->nestedTable($entity['settings'], ['tree' => true]);?>
    <?php endif;?>
    <br><br>
    <?= $this->Link->authLink(
        __('Clear user settings'),
        ['action' => 'clearsettings', $entity->id],
        [
            'class' => 'doc-item-remove button tiny',
            'confirm' => __("This will clear the user's settings. Are you ready to proceed?")
        ]
    );
    ?>
</div>

<?php endif; ?>

<!-- Actions -->
<?php
    $this->setShowBlock(['footer']);
    $this->Link->addEditButtons($entity);
?>
