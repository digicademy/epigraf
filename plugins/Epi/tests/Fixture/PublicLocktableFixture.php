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

namespace Epi\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LocktableFixture
 */
class PublicLocktableFixture extends TestFixture
{
    /**
     * Database connection
     *
     * @var string
     */
    public $connection = 'test_public';

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'locktable';
    /**
     * Fields
     *
     * @var array
     */
    // phpcs:disable
    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'lock_token' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'lock_mode' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'lock_table' => ['type' => 'string', 'length' => 1500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'lock_segment' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'lock_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'expires' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => true, 'default' => null, 'comment' => ''],
        '_indexes' => [
            'lockid' => ['type' => 'index', 'columns' => ['lock_token'], 'length' => []],
            'lockdatensatz' => ['type' => 'index', 'columns' => ['lock_id'], 'length' => []],
            'lockmode' => ['type' => 'index', 'columns' => ['lock_mode'], 'length' => []],
            'locksegment' => ['type' => 'index', 'columns' => ['lock_segment'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci'
        ],
    ];
    // phpcs:enable
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
                'lock_token' => 1,
                'lock_mode' => 1,
                'lock_table' => 'Lorem ipsum dolor sit amet',
                'lock_segment' => 'Lorem ipsum dolor sit amet',
                'lock_id' => 1,
            ],
        ];
        parent::init();
    }
}
