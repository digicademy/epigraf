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

/**
 * Interface that tables must implement in order to be used in the export pipeline
 */
interface ExportTableInterface
{

    /**
     * Get record count (for progress bar)
     *
     * @param array $params Parameters from the query string
     * @return int Number of rows matching the conditions
     */
    public function getExportCount($params): int;

    /**
     * Fetch the data
     *
     * @param array $params
     * @param array $paging
     * @param string $indexkey
     * @return array
     */
    public function getExportData($params, $paging = [], $indexkey = ''): array;

}
