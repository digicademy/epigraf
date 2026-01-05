<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace App\Model\Entity\Jobs;

use App\Cache\Cache;
use App\Model\Entity\Job;

/**
 * Import data from an uploaded file using TaskImport
 *
 * The preview task performs the following steps:
 * 1. _loadEntities
 *    a) _loadData
 *    b) _toEntities
 *
 * The import task performs the following steps
 * 1. _loadEntities
 *    a) _loadData
 *    b) _toEntities
 * 2. _saveEntities
 *    a) clearEntities (model method)
 *    b) saveMany (model method)
 *    c) _solveLinks
 *      (collectIds / _addToIndex -> getSolvedIds / canSolve -> saveMany
 *
 * 3. _fillEntities
 *
 */
class JobImport extends Job
{

    /**
     * Default limit value
     *
     * @var int
     */
    public int $limit = 1000;

    /**
     * Default job name
     *
     * @var string
     */
    public $jobName = 'Import';

    /**
     * Clear index specified by current index key
     *
     * @return void
     */
    protected function initIndex()
    {
        Cache::delete($this->index_key, 'index');
        $this->_index =  ['sources' => [], 'targets' => []];
    }

}
