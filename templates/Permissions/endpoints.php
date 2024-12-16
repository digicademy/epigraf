<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

/**
 * @var array $rows
 * @var array $columns
 */
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Hardwired endpoint permissions by role')); ?>

<!-- Content area -->
<div class="content-main widget-scrollbox">
    <?= $this->Table->simpleTable($rows, $this->getConfig('options')['columns'] ?? []) ?>
</div>

<!-- Actions -->
<?php $this->Link->beginActionGroup ('bottom'); ?>
<?php $this->Link->addAction(__('Users'),	['controller'=>'users','action' => 'index']); ?>
<?php $this->Link->addAction(__('Permissions'),	['controller'=>'permissions','action' => 'index']); ?>

