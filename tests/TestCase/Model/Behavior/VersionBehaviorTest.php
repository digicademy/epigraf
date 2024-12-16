<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Model\Behavior;

use App\Test\TestCase\AppTestCase;
use Cake\ORM\Table;

/**
 * App\Model\Behavior\VersionBehavior Test Case
 */
class VersionBehaviorTest extends AppTestCase
{

    public $dumps = [
        'test_epigraf' => 'test_epigraf_versions.sql'
    ];

	/**
	 * Test subject
	 *
	 * @var Table
	 */
	public $Versions;

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp(): void
	{
        parent::setUp();
		$this->Versions = $this->fetchTable('Versions');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown(): void
	{
		unset($this->Versions);
		parent::tearDown();
	}


	/**
	 * Test softDelete method
	 *
	 * @return void
	 */
	public function testSoftDelete()
	{
		// Delete entity
		$this->Versions->addBehavior('Version');
		$entity = $this->Versions->get(14);
		$this->Versions->delete($entity);

		// Assert the entity is filtered in find-calls
		$rows = $this->Versions->find()->where(['id' => 14]);
		$this->assertEquals(0, $rows->count());

		// Assert the entity is still present in the database
		$this->Versions->removeBehavior('Version');
		$rows = $this->Versions->find()->where(['id' => 14, 'deleted' => 1]);
		$this->assertNotEmpty($rows);
	}

	/**
	 * Test createVersion method: edit existing entity
	 *
	 * @return void
	 */
	public function testCreateVersion()
	{
		// Edit entity
		$this->Versions->addBehavior('Version');

		$entity = $this->Versions->get(24);

		$data = ['content' => 'mens sana in corpore sano ...'];
		$entity = $this->Versions->patchEntity($entity, $data);
		$this->Versions->save($entity);

		// Assert that the entity was changed
		$entity = $this->Versions->get(24);
		$this->assertEquals($entity['content'], $data['content']);

		// Assert that a new version was created
		$this->Versions->removeBehavior('Version');
		$rows = $this->Versions->find('all');
		$this->assertJsonStringEqualsComparison($rows);
	}

    /**
     * Test createVersion method: edit existing entity
     *
     * @return void
     */
    public function testCreateCopy()
    {
        // Edit entity
        $this->Versions->addBehavior('Version');

        $entity = $this->Versions->get(24);

        $this->assertEquals($entity->id, 24);
        $entity = $this->Versions->createCopy($entity);
        $this->assertNotEquals($entity->id, 24);

        // Assert that a copy was created
        $this->Versions->removeBehavior('Version');
        $rows = $this->Versions->find('all');
        $this->assertJsonStringEqualsComparison($rows);
    }

    /**
     * Versioning should fail when necessary fields are missing.
     *
     * //TODO: test that the versioning failed exception is thrown for
     * // entities with errors (e.g. missing mandatory data)
     * @return void
     */
    public function testFailedVersioning()
    {

        $this->markTestIncomplete('Not implemented yet.');

        // Edit entity
        //$this->Versions->addBehavior('Version');

        // Remove mandatory value
//        $connection = $this->Versions->getConnection();
//        $connection->execute("ALTER TABLE `versions` CHANGE COLUMN `type` `type` VARCHAR(50) NULL COLLATE 'utf8mb4_unicode_ci';");
//        $connection->execute("UPDATE versions SET type = NULL WHERE id =24;");
//        $connection->execute("ALTER TABLE `versions` CHANGE COLUMN `type` `type` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci';");
//
//        $entity = $this->Versions->get(24);
//
//        $data = ['content' => 'mens sana in corpore sano ...'];
//        $entity = $this->Versions->patchEntity($entity, $data);
//        $this->Versions->save($entity);

    }


    /**
	 * Test createVersion method: add new entity
	 *
	 * @return void
	 */
	public function testAddEntity()
	{
		// Edit entity
		$this->Versions->addBehavior('Version');


		$data = ['content' => 'mens sana in corpore sano ...','type'=>'record'];
		$entity = $this->Versions->newEntity($data);
		$this->Versions->save($entity);

		// Assert that the entity was saved a version was created
		$this->Versions->removeBehavior('Version');
		$rows = $this->Versions->find('all');
		$this->assertJsonStringEqualsComparison($rows);
	}
}
