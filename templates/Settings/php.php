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

<?php $this->Breadcrumbs->add(__('PHP info')); ?>

<?php
    ob_start();
    phpinfo();
    $content = ob_get_contents();
    ob_end_clean();

    $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $content);
?>

<?= $content ?>
