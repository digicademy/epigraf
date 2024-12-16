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

<h1><?= __('Welcome to Epigraf!') ?></h1>

<!-- Actions -->
<?php $this->Link->beginActionGroup ('content'); ?>
<?php
$this->Link->addAction(
    __('Create the start page'),
    ['controller' => 'Pages', 'action' => 'add', 'start'],
    ['class' => 'button', 'data-role'=>'add']
);
?>
