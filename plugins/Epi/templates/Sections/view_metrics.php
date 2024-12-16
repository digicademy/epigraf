<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */
?>
<?php
/**
 * @var Epi\Model\Entity\Article $article
 *
 */
?>

<div class="art-metrics">

    <div class="art-metrics-value">
        <?= __('Annotations') ?>:
        <?= $article->amount_of_annotations ?>
    </div>

    <div class="art-metrics-value">
        <?= __('Full-text characters') ?>:
        <?= $article->amount_of_text ?>
    </div>

    <div class="art-metrics-value">
        <?= __('Mandatory content') ?>:
        <?= $article->content_state ?>
    </div>

    <div class="art-metrics-value">
        <?= __('Progress') ?>:
        <?= $article->published_label ?>
    </div>

</div>

