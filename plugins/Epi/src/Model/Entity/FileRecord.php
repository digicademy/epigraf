<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Entity;

use App\Model\Entity\Databank;
use Cake\Routing\Router;

/**
 * File Entity
 *
 * # Database fields (without inherited fields)
 * @property string $name
 * @property string $description
 * @property string $config
 * @property string $type
 * @property int $size
 * @property string $root
 * @property string $path
 * @property bool $isfolder
 *
 * # Virtual fields (without inherited fields)
 * @property string $downloadurl
 * @property string $displayurl
 * @property string $databaseName
 * @property string $database
 */
class FileRecord extends \Files\Model\Entity\FileRecord
{
    /**
     * Default limit
     *
     * @var int
     */
    public $limit = 15;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     * //TODO: make all internally handled fields unaccessible
     *
     * @var array
     */
    protected $_accessible = [
        'published' => true,
        'name' => true,
        'type' => true,
        'size' => true,
        'root' => true,
        'path' => true,
        'isfolder' => true,
        'description' => true
    ];


    /**
     * @return string Download url
     */
    protected function _getDownloadurl()
    {
        return Router::url([
            'plugin' => 'Epi',
            'controller' => 'Files',
            'action' => 'download',
            $this->id,
            'database' => Databank::removePrefix($this->getTable()->getDatabaseName())
        ]);
    }

    /**
     * @return string Display url
     */
    protected function _getDisplayurl()
    {
        return Router::url([
            'plugin' => 'Epi',
            'controller' => 'Files',
            'action' => 'display',
            $this->id,
            '?' => ['format' => 'thumb', 'size' => '600'],
            'database' => Databank::removePrefix($this->getTable()->getDatabaseName())
        ]);
    }

    /**
     * Get the items that use the file
     *
     * @return \Cake\ORM\Query|false
     */
    public function getItems()
    {

        $path = $this->path;
        $filename = $this->name;
        $prefix = 'articles/';

        if (!str_starts_with($path, $prefix) || empty($filename) || ($this->root !== 'root')) {
            return [];
        }

        $path = (strpos($path, $prefix) === 0) ? substr($path, strlen($prefix)) : $path;
        $path = trim($path, ' /\\');


        $items = $this->fetchTable('Epi.Items');

        $query = $items
            ->find('all')
            ->contain('Articles')
            ->where(['file_name' => $filename]);

        if (!empty($path)) {
            $query = $query->where(['file_path' => $path]);
        }
        else {
            $query = $query->where(['file_path IS' => null]);
        }

        return $query;
    }


    /**
     * Virtual field database name
     *
     * @return string
     */
    protected function _getDatabaseName()
    {
        return method_exists($this->table, 'getDatabaseName') ? $this->table->getDatabaseName() : '';
    }

    /**
     * Virtual field database as it is exposed in article and project entities
     *
     * TODO: can this be ommited in favor of _getDatabaseName() ? -> still needed for $_serialize_fields
     *
     * @return string
     */
    protected function _getDatabase()
    {
        return Databank::removePrefix($this->databaseName);
    }

}
