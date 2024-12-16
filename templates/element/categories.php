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
 * @var array $cats
 */
?>
<div class="widget-scrollbox">

    <ul class="side-nav">
        <li class="category-meta <?= (!isset($category) && empty($term) && ($this->request->getParam('action') == 'index')) ? 'active' : '' ?>">
            <?= $this->Html->link(__('List all'), ['action' => 'index']) ?>
        </li>

        <?php foreach ($cats as $cat): ?>
            <?php $catStr = empty($cat) ? __('Without Category') : $cat; ?>
            <li class="<?= empty($cat) ? 'category-meta' : '' ?> <?= isset($category) && (($category ?? '') == $cat) ? 'active' : '' ?>">
                <?php if (($parameter ?? 'query') === 'path'): ?>
                    <?= $this->Html->link($catStr, ['action' => 'index', $cat]) ?>
                <?php else: ?>
                    <?= $this->Html->link($catStr, ['action' => 'index', '?'=>['category' => $cat]]) ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php if ($search ?? true): ?>
<?= $this->Form->create(null, ['id' => 'search','url' => ['action' => 'index']]) ?>
    <?= $this->Form->control('query', ['type' => 'text', 'label' => false,'placeholder'=>__('Search')]); ?>
<?= $this->Form->end(); ?>
<?php endif; ?>
