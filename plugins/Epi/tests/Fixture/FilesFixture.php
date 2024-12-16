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
 * FilesFixture
 */
class FilesFixture extends TestFixture
{
    /**
     * Database connection
     *
     * @var string
     */
    public $connection = 'test_projects';

    /**
     * Fields
     *
     * @var array
     */
    // phpcs:disable
    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'deleted' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => 0, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => true, 'default' => null, 'comment' => ''],
        'modified' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => true, 'default' => null, 'comment' => ''],
        'name' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'type' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'size' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'path' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'root' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'isfolder' => ['type' => 'tinyinteger', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'MyISAM',
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
                'deleted' => 0,
                'created' => '2020-11-11 14:17:34',
                'modified' => '2020-11-11 14:17:34',
                'name' => 'downloads',
                'type' => null,
                'size' => null,
                'path' => '',
                'root'=>'root',
                'isfolder' => 1,
            ],
            [
                'id' => 2,
                'deleted' => 0,
                'created' => '2020-11-11 14:17:34',
                'modified' => '2020-11-11 14:17:34',
                'name' => 'test.doc',
                'type' => 'doc',
                'size' => 33381,
                'path' => 'downloads',
                'root'=>'root',
                'isfolder' => 0,
            ],
            [
                'id' => 3,
                'deleted' => 0,
                'created' => '2020-11-11 14:17:34',
                'modified' => '2020-11-11 14:17:34',
                'name' => 'testfolder',
                'type' => null,
                'size' => null,
                'path' => 'downloads',
                'root'=>'root',
                'isfolder' => 1,
            ],
        ];
        parent::init();
    }
}
