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
 * @var \Epi\Model\Entity\Article $entity
 */
?>

<!-- Content area -->
<?php if (empty($entity->sections)): ?>

    <div id="sections-selector" data-snippet="widget-dropdown-pane">
        <ul>
            <?php foreach ($entity->getSectionTypes() as $sectionKey => $sectionOptions): ?>

                <?php $liAttributes = ['data-value ' => $sectionKey] ?>
                <?= $this->Element->openHtmlElement('li', $liAttributes); ?>
                <label class="text" title="<?= $sectionOptions['caption'] ?>">
                    <?= $sectionOptions['caption'] ?>
                </label>
                <?= $this->Element->closeHtmlElement('li') ?>

            <?php endforeach; ?>

        </ul>

    </div>

<?php else: ?>

    <?php
        // TODO: move to Controller (decide whether serialized or viewed)?
        // TODO: always set root?
        $entity->prepareRoot();

        $edit = $this->Link->getEdit();
        $mode = $this->Link->getMode();

        // Get template
        $templateArticle =  $entity->type['merged'] ?? [];

        // In the coding template, the default editing mode is false.
        // Coding has to be explicitly allowed for sections, items and fields.
        // TODO: implement function that merges default and mode specific templates
        if ($mode === 'code') {
            $templateArticle['edit'] = $templateArticle['edit'] ?? false;
        }

        $options = ['edit'=>$edit, 'mode'=>$mode,'template_article'=> $templateArticle, 'note' => true];
    ?>

    <?php foreach ($entity->sections as $section): ?>
        <?= $this->EntityInput->sectionContent($section, $options) ?>
    <?php endforeach; ?>

<?php endif;?>
