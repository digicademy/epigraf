<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Traits;

use App\Model\Interfaces\ExportTableInterface;
use Cake\ORM\Entity;

/**
 * Adds the methods getExportCount() and getExportData() to tables.
 */
trait TransferTrait
{

    /**
     * Get number of records to be exported
     *
     * Called from JobExport.
     *
     * @implements ExportTableInterface
     * @param $params
     * @return int Number of rows for calculating the progress bar
     */
    public function getExportCount($params): int
    {
        $params = $this->parseRequestParameters($params);

        return $this
            ->find('hasParams', $params)
            ->count();
    }

    /**
     * Get data to be exported
     *
     * Called from JobExport.
     *
     * @implements ExportTableInterface
     * @param array $params
     * @param array $paging
     * @param string $indexkey
     * @return Entity[]
     */
    public function getExportData($params, $paging = [], $indexkey = ''): array
    {
        $offset = $paging['offset'] ?? 0;
        $limit = $paging['limit'] ?? $this->exportLimit;

        $params = $this->parseRequestParameters($params);

        $entities = $this
            ->find('hasParams', $params)
            ->limit($limit)
            ->offset($offset)
            ->toArray();

        return $entities;
    }

}
