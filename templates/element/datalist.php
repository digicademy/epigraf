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
<datalist id="<?= $id ?>">
   <?php foreach ($data as $item): ?>
      <option value="<?= $item ?>">
   <?php endforeach;?>
</datalist>
