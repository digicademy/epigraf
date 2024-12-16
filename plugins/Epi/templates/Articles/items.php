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
 * @var \Epi\Model\Entity\Item[] $entities
 * @var array $items
 */

use App\Utilities\Converters\Attributes;
?>

<!-- Breadcrumbs -->
<?php $this->Breadcrumbs->add(__('Items')); ?>

<?= $this->Table->filterTable('epi.items', $items) ?>

<!-- Actions -->
<?php $this->Link->beginActionGroup('bottom'); ?>
<?php $this->Link->addCounter(); ?>

<?php $this->Link->downloadButtons(null, 'items', 'epi_items'); ?>
