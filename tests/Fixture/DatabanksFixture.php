<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * DatabanksFixture
 */
class DatabanksFixture extends TestFixture
{
    /**
     * Database connection
     *
     * @var string
     */
    public $connection = 'test';

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
			[
				'id' => 1,
				'created' => '2020-05-29 00:01:36',
				'modified' => '2020-05-29 00:01:36',
				'name' => 'test_projects',
				'version' => DATABASE_CURRENT_VERSION,
				'published' => 0,
			]
        ];
        parent::init();
    }
}
