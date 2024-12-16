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
 * @var \Epi\Model\Entity\Property $entity The merged entity
 */
?>

<?php $entity->prepareRoot(); ?>
<div class="content-tight">
    <?= $this->EntityInput->entityForm($entity, 'merge') ?>
</div>

