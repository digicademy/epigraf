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

/**
 * Transfer data from one database into another using TaskTransfer
 */
class JobTransfer extends JobImport
{

    /**
     * Job name
     *
     * @var string
     */
    public $jobName = 'Transfer data between databases';

    /**
     * Number of records to transfer in each iteration
     *
     * @var int
     */
    public int $limit = 1000;

    protected function _getOptions()
    {
        return [
            'versions' => __('Init versioning'),
            'tree' => __('Recover trees'),
            //'fulltext' => __('Recreate fulltext index'),
            //'dates' => __('Reindex dates'),
            'files' => __('Copy files'),
            'snippets' => [
                'caption' => __('Snippets'),
                'type' => 'multi',
                'key' => 'params',
                'options' => [
                    'iris' => 'iris',
                    'published' => 'published',
                    'search' => 'search',
                    'editors' => 'editors',
                    'comments' => 'comments'
                ]
            ],
            'published' => [
                'caption' => __('Publication states'),
                'type' => 'multi',
                'key' => 'params',
                'prefix' => 'val_',
                'options' => $this->_getPublishedOptions()
            ]
        ];
    }

}
