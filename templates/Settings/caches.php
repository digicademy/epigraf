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
/** @var array $caches */
?>

<?php $this->Breadcrumbs->add(__('Caches')); ?>

<?= $this->Table->nestedTable($caches) ?>
