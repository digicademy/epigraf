<?php /**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */ ?>

<?php
    use App\View\AppView;
?>

<?php
/**
 * @var AppView $this
 * @var Epi\Model\Entity\Article $entity
 * @var boolean $edit
 * @var string $mode
 * @var array $templateArticle
 */
?>

<div class="content-main widget-scrollbox" data-snippet="article-tree">
    <?= $this->Element->openHtmlElement(
        'table',
        [
            'class' => 'widget-tree widget-table recordlist'
        ]
    ) ?>

        <tbody data-list-name="targets">
            <?php foreach ($entity->tree as $node): ?>
                <?= $this->Table->getTableRow('node', $node, false, ['tree'=>true, 'actions'=>false, 'columns' => ['caption' => ['key'=>'caption']]]) ?>
            <?php endforeach; ?>

        </tbody>
    <?= $this->Element->closeHtmlElement('table') ?>
</div>
