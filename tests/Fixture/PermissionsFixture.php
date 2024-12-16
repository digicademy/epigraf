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
 * PermissionsFixture
 */
class PermissionsFixture extends TestFixture
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
                'created' => '2019-07-23 09:48:21',
                'modified' => '2019-07-23 09:48:21',
                'user_id' => 1,
                'user_session' => null,
                'user_request' => null,
                'entity_type' => 'databank',
                'entity_name' => 'test_projects',
                'entity_id' => 1,
                'permission_type' => 'access',
                'permission_name' => null,
                'permission_expires' => null,
            ],
            [
                'id' => 2,
                'created' => '2019-07-23 09:48:21',
                'modified' => '2019-07-23 09:48:21',
                'user_id' => 4,
                'user_session' => null,
                'user_request' => null,
                'entity_type' => 'databank',
                'entity_name' => 'test_projects',
                'entity_id' => 1,
                'permission_type' => 'access',
                'permission_name' => null,
                'permission_expires' => null,
            ],
            [
                'id' => 3,
                'created' => '2019-07-23 09:48:21',
                'modified' => '2019-07-23 09:48:21',
                'user_id' => 2,
                'user_session' => null,
                'user_request' => null,
                'entity_type' => 'databank',
                'entity_name' => 'test_public',
                'entity_id' => 2,
                'permission_type' => 'access',
                'permission_name' => null,
                'permission_expires' => null,
            ],
            [
                'id' => 4,
                'created' => '2019-07-23 09:48:21',
                'modified' => '2019-07-23 09:48:21',
                'user_id' => 2,
                'user_session' => null,
                'user_request' => null,
                'entity_type' => 'databank',
                'entity_name' => 'test_projects',
                'entity_id' => 1,
                'permission_type' => 'access',
                'permission_name' => null,
                'permission_expires' => null,
            ],
            [
                'id' => 5,
                'created' => '2019-07-23 09:48:21',
                'modified' => '2019-07-23 09:48:21',
                'user_id' => 2,
                'user_session' => null,
                'user_request' => 'web',
                'entity_type' => 'databank',
                'entity_name' => 'test_projects',
                'entity_id' => 1,
                'permission_type' => 'access',
                'permission_name' => 'articles/items',
                'permission_expires' => null,
            ],
            [
                'id' => 6,
                'created' => '2019-07-23 09:48:21',
                'modified' => '2019-07-23 09:48:21',
                'user_id' => 2,
                'user_session' => null,
                'user_request' => null,
                'entity_type' => 'databank',
                'entity_name' => 'test_public',
                'entity_id' => 2,
                'permission_type' => 'access',
                'permission_name' => null,
                'permission_expires' => null,
            ],
        ];
        parent::init();
    }
}
