<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

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
 * @var App\Model\Entity\Databank $database
 * @var Epi\Model\Entity\Article $article
 * @var Epi\Model\Entity\Section $section
 *
 * @var boolean $edit Passed from view.php
 * @var string $mode Passed from view.php
 *
 * @var array $template_article Passed from view.php
 * @var array $template_section Passed from view.php
 */

use App\Utilities\Converters\Arrays;

?>

<?php $entityHelper = $edit ? $this->EntityInput : $this->EntityHtml; ?>

<?php
    $itemtype = $template_section['view']['widgets']['grid']['itemtype'] ?? '';
    $tileFields = $template_section['view']['widgets']['grid']['fields'] ?? [];
    $items = collection($section->items)
        ->filter(fn($item) => $item->itemtype === $itemtype )
        ->filter(fn($item) => $item['pos_z'] > 0 )
        ->each(function($item) {
            $item->pos_x = max(1, $item->pos_x);
            $item->pos_y = max(1, $item->pos_y);
        })
        ->toArray();

    if (!empty($items)) {
        $section->layout_cols = max(1, $section->layout_cols);
        $section->layout_rows = max(1, $section->layout_rows);
    }

    $templates = [
        'mode' => $mode,
        'template_section' => $template_section,
        'template_article' => $template_article
    ];
?>
<?php if (!empty($items) || ($edit ?? false)): ?>
    <?= $this->Element->openHtmlElement(
        'div',
        ['class' => ['widget-grid', $edit ? 'widget-dragdrop' : null], 'data-itemtype'=>$itemtype]
    ) ?>
        <?php if ($edit && ($template_section['edit'] ?? $template_article['edit'] ?? true)): ?>
            <div class="doc-section-grid-size">

                <?= $this->Form->control(
                    'sections[' . $section->id . '][layout_cols]',
                    [
                        'value' => $section->layout_cols ?? 1,

                        'label' => __('Columns'),
                        'class' => 'doc-section-grid-cols',
                        'data-row-field' => 'layout_cols',
                        'type' => 'number',
                        'min' => 0,
                        'max' => 100
                    ]
                ) ?>

                <?= $this->Form->control(
                    'sections[' . $section->id . '][layout_rows]',
                    [
                        'value' => $section->layout_rows ?? 1,
                        'label' => __('Rows'),
                        'class' => 'doc-section-grid-rows',
                        'data-row-field' => 'layout_rows',
                        'type' => 'number',
                        'min' => 0,
                        'max' => 100
                    ]
                ) ?>

            </div>
        <?php endif; ?>

        <div class="doc-section-grid-container">
            <table class="doc-section-grid-table">
                <?php $items_byrow = Arrays::array_group($items,'pos_y'); ?>
                <?php for ($row = 1; $row <= max(1,$section['layout_rows']); $row++): ?>
                    <tr>

                        <?php $items_bycol = Arrays::array_group($items_byrow[$row] ?? [],'pos_x'); ?>
                        <?php for ($col = 1; $col <= max(1,$section['layout_cols']); $col++): ?>
                            <td>
                                <div class="doc-section-item-group">
                                    <?php
                                        $items_cell = $items_bycol[$col] ?? [];
                                        usort($items_cell, fn($a, $b) => $a->pos_z - $b->pos_z);

                                    ?>

                                    <?php foreach ($items_cell as $item): ?>
                                        <?php
                                            // TODO: move to BaseEntityHelper and get templates from the entities (or container entities)
                                            $template_item =  $item->type['merged'] ?? [];
                                            $templates = array_merge(
                                                $templates,
                                                ['template_item' => $template_item]
                                            );
                                        ?>

                                        <?=
                                            $entityHelper->itemTile(
                                                $item,
                                                [
                                                    'fields' => $tileFields,
                                                    'templates' => $templates,
                                                    'preview' => true,
                                                    'draggable' => $edit
                                                ]
                                            )
                                        ?>

                                    <?php endforeach; ?>
                                </div>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            </table>
        </div>

        <script type="text/template" class="template-doc-section-grid-item template">
            <?php
                //TODO: placeholders need to pass the validation rules
                $item = $section->createItem(['itemtype' => $itemtype], $tileFields);
            ?>
            <?=
                $entityHelper->itemTile(
                    $item,
                    [
                        'fields' => $tileFields,
                        'templates' => $templates,
                        'preview' => true,
                        'draggable' => $edit
                    ]
                )
            ?>
        </script>

    <?= $this->Element->closeHtmlElement('div') ?>
<?php endif; ?>

