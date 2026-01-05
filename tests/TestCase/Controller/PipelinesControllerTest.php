<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Controller;

use App\Test\TestCase\AppTestCase;
use Cake\ORM\TableRegistry;

/**
 * App\Controller\PipelinesController Test Case
 *
 * @uses \App\Controller\PipelinesController
 */
class PipelinesControllerTest extends AppTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Databanks',
        'app.Users',
        'app.Permissions',
        'app.Pipelines',
        'plugin.Epi.Token'
    ];

    public $Pipelines = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();
        $this->Pipelines = $this->fetchTable('Pipelines');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Pipelines);
        parent::tearDown();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->loginUser('admin');
        $this->get("pipelines/index");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->loginUser('editor');

        $this->get("/pipelines/view/21");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test add element method
     *
     * @return void
     */
    public function testAddElement()
    {
        $this->loginUser('editor');

        $this->configRequest(['headers' => ['X-Requested-With' => 'XMLHttpRequest']]);
        $this->get("/pipelines/add_task/21/transformxsl?number=3");


        $this->assertHtmlEqualsComparison(true,'/');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->loginUser('admin');

        $this->get("pipelines/add");

        $this->assertHtmlEqualsComparison();

        // Test post request
        $data = [
            'name' => 'testpipeline',
            'type' => 'export',
            'norm_iri' => 'testpipeline',
            'description' => 'do something'
        ];
        $this->post('pipelines/add', $data);
        $this->assertResponseCode(302);

        // Get id of last created entry
        $id = $this->fetchTable('Pipelines')
            ->find('all', [
                'order' => ['Pipelines.id' => 'DESC']
            ])
            ->first()
            ->get('id');

        // Check if the record was created
         $record = $this->Pipelines->get($id);
         $this->saveComparisonJson($record);
         $this->assertJsonStringEqualsComparison($record);
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $id = 21;

        $this->loginUser('admin');
        $this->get("pipelines/edit/".$id);

        $this->assertHtmlEqualsComparison();

        // Test post request
        $data = [
            'id' => $id,
            'name' => 'new name',
            'type' => 'export',
            'description' => 'do something else'
        ];
        $this->post('pipelines/edit/' . $id, $data);
        $this->assertResponseCode(302);

        // Check if the record was created
        $record = $this->Pipelines->get($id);
        $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison($record);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $id = 21;
        $this->loginUser('admin');

        // Pipeline is in database?
        $pipeline = $this
            ->Pipelines
            ->find('all')
            ->where(['id' => $id])
            ->count();
        $this->assertEquals(1, $pipeline);

        // A get request shows the confirmation page
        $this->get("pipelines/delete/" . $id);
        $this->assertHtmlEqualsComparison();

        // A delete request deletes the entry
        $this->delete('pipelines/delete/' . $id);
        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison('after', '.content-wrapper');

        // Check if the pipeline record is not in database anymore
        $pipeline = $this
            ->Pipelines
            ->find('all')
            ->where(['id' => $id])
            ->count();
        $this->assertEquals(0, $pipeline);
    }

}
