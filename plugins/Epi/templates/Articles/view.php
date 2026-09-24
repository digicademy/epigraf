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
use App\View\AppView;
?>

<?php
/**
 * @var AppView $this
 * @var Epi\Model\Entity\Article $entity
 */
?>

<?php
    // TODO: move to Controller (decide whether serialized or viewed)?
    // TODO: always set root?
    $entity->prepareRoot();

    // Get template
    $options = [
        'edit'=> $this->Link->getEdit(),
        'mode'=> $this->Link->getMode(),
        'template_article'=> $entity->type['merged'] ?? []
    ];
?>

<?php $template = $this->getContentTemplate('document', ['tree', 'document']); ?>
<?= $this->element('../Articles/view_' . $template, $options) ?>
