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
    use Epi\Model\Analytics\Analytics;

    /**
     * @var AppView $this
     * @var Analytics $data
     * @var array $itemsFiles
     * @var array $filesItems
     */
?>

<?php $this->Breadcrumbs->add(__('Missing files')); ?>

    <h2><?= __('Expected files by item type') ?></h2>

    <?php $itemsFiles = $data['items']; ?>
    <?=
        $this->Table->simpleTable($itemsFiles,
            [
                'itemtype' => __('Item type'),
                'file_type' => __('File type'),
                'wanted' => __('Expected'),
                'file_online' => __('Online'),
                'n_available' => __('Available'),
                'n_missing' => __('Missing')
            ],
            [
                'class' => 'content-extratight',
                'align-right' => ['n_available', 'n_missing']
            ]
        )
    ?>

    <h2><?= __('Expected items by file type') ?></h2>

    <?php $filesItems = $data['files']; ?>
    <?=
      $this->Table->simpleTable($filesItems,
          [
              'type' => __('File type'),
              'wanted' => __('Expected'),
              'n_missing' => __('Missing')
          ],
          [
              'class' => 'content-extratight',
              'align-right' => ['n_available', 'n_missing']
          ]
      )
    ?>
