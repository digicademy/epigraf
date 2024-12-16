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

<?php $this->Breadcrumbs->add('Vollständigkeit der Dateidatensätze'); ?>

    <h2>Abgleich: Items- und Files-Tabelle </h2>

    <?php $itemsFiles = $data['items']; ?>
    <?=
        $this->Table->simpleTable($itemsFiles,
            [
                'itemtype' => __('Item type'),
                'file_type' => __('File type'),
                'wanted' => __('Wanted'),
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

    <p class="content-notice"> Wie viele der Einträge aus der Items-Tabelle sind in der Files-Tabelle vorhanden?</p>

    <h2> Abgleich: Files- und Items-Tabelle </h2>

    <?php $filesItems = $data['files']; ?>
    <?=
      $this->Table->simpleTable($filesItems,
          [
              'type' => __('File type'),
              'wanted' => __('Wanted'),
              'n_missing' => __('Missing')
          ],
          [
              'class' => 'content-extratight',
              'align-right' => ['n_available', 'n_missing']
          ]
      )
    ?>
    <p class="content-notice">Wie viele der Einträge aus der Files-Tabelle sind in der Items-Tabelle vorhanden?</p>


