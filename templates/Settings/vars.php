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
    use App\Utilities\Files\Files;
    use Cake\Core\Configure;
?>

<?php $this->Breadcrumbs->add(__('System variables')); ?>

<?php
$info = [
    'Data.root' => h(Configure::read('Data.root')),
    'Data.databases' => h(Configure::read('Data.databases')),
    'Data.shared' => h(Configure::read('Data.shared')),
    'Pages.contexthelp' => h(Configure::read('Pages.contexthelp')),
    'Commands.exiftool' => Files::commandAvailable('exiftool') ? 'yes' : 'no',
    'version.txt' => is_file(ROOT . DS . 'version.txt') ? h(file_get_contents(ROOT . DS . 'version.txt')) : '',
];
?>

<?= $this->Table->nestedTable($info) ?>
