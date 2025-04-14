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

<?php if (!empty($article['norm_data'])) : ?>
    <div class="doc-section doc-section-normdata">
        <div class="doc-section-name"><?= __('Norm data') ?></div>
        <div class="doc-section-content">
            <?php foreach ($article->normDataParsed as $item): ?>
                <div class="doc-section-item">
                    <?php if (is_array($item)): ?>
                        <?= $this->Html->link($item['value'] ?? '', $item['url'] ?? '') ?>
                    <?php else: ?>
                        <?= $item ?>
                    <?php endif; ?>
                </div>
            <?php endforeach;  ?>
        </div>
    </div>
<?php endif; ?>
