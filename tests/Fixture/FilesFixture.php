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

namespace App\Test\Fixture;

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
                'state' => null,
                'published'=>null,
                'created' => '2020-11-11 14:17:34',
                'modified' => '2020-11-11 14:17:34',
                'name' => 'downloads',
                'type' => null,
                'size' => null,
                'path' => '',
                'root'=>'shared',
                'isfolder' => 1,
                'description' =>'Download folder'
            ],
            [
                'id' => 2,
                'state' => null,
                'published'=>0,
                'created' => '2020-11-11 14:17:34',
                'modified' => '2020-11-11 14:17:34',
                'name' => 'test_private.doc',
                'type' => 'doc',
                'size' => 33381,
                'path' => 'downloads',
                'root'=>'shared',
                'isfolder' => 0,
                'description' =>'Private test file'
            ],
            [
                'id' => 3,
                'state' => null,
                'published'=>1,
                'created' => '2020-11-11 14:17:34',
                'modified' => '2020-11-11 14:17:34',
                'name' => 'test_published.doc',
                'type' => 'doc',
                'size' => 43381,
                'path' => 'downloads',
                'root'=>'shared',
                'isfolder' => 0,
                'description' =>'Published test file'
            ],
			[
				'id' => 4,
				'state' => null,
				'published'=>1,
				'created' => '2020-11-11 14:17:34',
				'modified' => '2020-11-11 14:17:34',
				'name' => '',
				'type' => '',
				'size' => null,
				'path' => '',
				'root'=>'shared',
				'isfolder' => 1,
                'description' =>'Shared root folder'
			],
			[
				'id' => 5,
				'state' => null,
				'published'=>1,
				'created' => '2020-11-11 14:17:34',
				'modified' => '2020-11-11 14:17:34',
				'name' => '',
				'type' => '',
				'size' => null,
				'path' => 'downloads',
				'root'=>'shared',
				'isfolder' => 1,
                'description' =>'Downloads root folder'
			],
			[
				'id' => 6,
				'state' => null,
				'published'=>1,
				'created' => '2020-11-11 14:17:34',
				'modified' => '2020-11-11 14:17:34',
				'name' => '',
				'type' => '',
				'size' => null,
				'path' => 'images',
				'root'=>'shared',
				'isfolder' => 1,
                'description' =>'Image root folder'
			],
			[
				'id' => 7,
				'state' => null,
				'published'=>null,
				'created' => '2020-11-11 14:17:34',
				'modified' => '2020-11-11 14:17:34',
				'name' => 'images',
				'type' => null,
				'size' => null,
				'path' => '',
				'root'=>'shared',
				'isfolder' => 1,
                'description' =>'Image folder'
			],
        ];
        parent::init();
    }
}
