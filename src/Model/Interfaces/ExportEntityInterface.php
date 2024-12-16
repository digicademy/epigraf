<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Interfaces;

use Cake\ORM\Entity;
use Exception;

/**
 * Interface that entities must implement in order to export data
 */
interface ExportEntityInterface
{

    /**
     * Get fields that will be exported
     *
     * @param $options
     * @return array
     */
    public function getExportFields($options);

    /**
     * Get data for JSON, XML or CSV export
     *
     * @param array $options
     * @param string $format json|xml|csv
     * @return array
     * @throws Exception
     */
    public function getDataForExport($options = [], $format = 'json');

    /**
     * Prepare entity for import into another database
     *
     * @param array $options
     * @return Entity
     */
    public function getDataForTransfer($options = []);

}
