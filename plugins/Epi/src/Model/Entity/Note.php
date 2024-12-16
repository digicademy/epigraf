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

use App\Model\Entity\Doc;

/**
 * Note Entity
 *
 * # Database fields (without inherited fields)
 * @property string $menu
 * @property string $notetype
 * @property string $name
 * @property string $category
 * @property string $sortkey
 * @property string $content
 * @property string $format
 *
 * # Virtual fields (without inherited fields)
 * @property string $scope
 * @property string $databaseName
 * @property string $database
 */
class Note extends Doc
{
    /**
     * Get the scope (note)
     *
     * @return string
     */
    protected function _getScope()
    {
        return 'notes';
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
        return $this->databaseName;
    }
}
