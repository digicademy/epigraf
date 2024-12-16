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

use Cake\Routing\Router;

?>

<?php $entityHelper = $edit ? $this->EntityInput : $this->EntityHtml; ?>

<?php
    $itemtype = $template_section['view']['widgets']['thumbs']['itemtype'] ?? '';
    $tileFields = $template_section['view']['widgets']['thumbs']['fields'] ?? [];
    $itemFields = $this->Types->getFields('items', $itemtype);

    $tileSize = $template_section['view']['widgets']['thumbs']['size'] ?? 'medium';
    $propertyImage = $template_section['view']['widgets']['thumbs']['property'] ?? false;
    $linkImage = $template_section['view']['widgets']['thumbs']['link'] ?? false;

    $items = collection($section->items)
        ->filter(fn($item) => $item->itemtype === $itemtype )
        ->filter(
            fn($item) => $propertyImage ?
                (!empty($item->property) && ($item->property->file_properties['preview'] ?? false)):
                $item->thumb['exists'] ?? false
        )
        ->toArray();


    $templates = [
        'mode' => $mode,
        'template_section' => $template_section,
        'template_article' => $template_article
    ];
?>

<?php if (!empty($items) || ($edit ?? false)): ?>
    <?= $this->Element->openHtmlElement('div', [
        'class' => [
            'widget-image-viewer',
            empty($items) ? 'widget-image-viewer-empty' : null,
            'doc-imagelist',
            'doc-imagelist-' . $tileSize
        ]
    ]) ?>
        <?php foreach ($items as $item): ?>

            <?php
                //TODO: where does $selected come from? Not used anymore?
                $divAttributes = [
                    'class' => ($selected ?? false) ? 'doc-image selected' : 'doc-image',
                    'data-row-table' => 'items',
                    'data-row-id' => $item->id,
                    'data-row-type' => $item->itemtype,
                ];

                if ($linkImage) {
                    $url = Router::url(['action'=>'view', $article->id,'#'=>'items-' .  $item->id]);
                    $divAttributes['data-item-url'] = $url;
                } else {
                    $url = false;
                }

                $imageItem = $propertyImage ? $item->property ?? [] : $item;
            ?>

            <?= $this->Element->openHtmlElement('div', $divAttributes) ?>

                <div class="doc-image-frame doc-image-frame-clip">
                    <?= $this->Files->outputImage($imageItem, true, $url); ?>
                </div>

                <div class="doc-image-heading">
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
                            'preview' => true
                        ]
                    )
                    ?>
                </div>

                <div class="doc-image-content">
                    <table class="doc-image-metadata">
                        <tbody>
                        <?php foreach ($itemFields as $fieldName => $fieldConfig): ?>
                            <tr>
                                <td>
                                    <?= $fieldConfig['caption'] ?? $fieldName ?>
                                </td>
                                <td>
                                    <?=
                                    $entityHelper->itemField(
                                        $item,
                                        $fieldName,
                                        [
                                            'edit'=> false,
                                            'caption'=> false
                                        ]
                                    )
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?= $this->Element->closeHtmlElement('div') ?>
        <?php endforeach; ?>
    <?= $this->Element->closeHtmlElement('div') ?>
<?php endif; ?>
