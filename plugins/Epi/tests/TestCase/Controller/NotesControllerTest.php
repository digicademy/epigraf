<?php

namespace Epi\Test\TestCase\Controller;

use App\Utilities\Converters\Arrays;
use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;
use Epi\Controller\NotesController;

/**
 * Epi\Controller\NotesController Test Case
 *
 * @uses \Epi\Controller\NotesController
 */
class NotesControllerTest extends EpiTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
        'app.Pipelines',
        'app.Permissions',
        'app.TwoDatabanks',

        'plugin.Epi.Users',
        'plugin.Epi.Meta',
        'plugin.Epi.Token',
        'plugin.Epi.Locktable',
        'plugin.Epi.Notes',

        'plugin.Epi.PublicUsers',
        'plugin.Epi.PublicMeta',
        'plugin.Epi.PublicToken',
        'plugin.Epi.PublicLocktable',
        'plugin.Epi.PublicNotes'
    ];

    protected $Notes;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Notes = $this->fetchTable('Epi.Notes');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Notes);
        parent::tearDown();
    }

    /**
     * Test index method (HTML file)
     *
     * @return void
     */
    public function testIndex()
    {
        $this->loginUser('author');
        $this->get("/epi/projects/notes");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test index method (HTML file)
     *
     * @return void
     */
    public function testIndexPublic()
    {
        // Just check that the text_projects notes don't show up here
        $this->loginUser('devel');
        $this->get("/epi/public/notes");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test view method (HTML file)
     *
     * @return void
     */
    public function testView()
    {
        $this->loginUser('author');
        $this->get("/epi/projects/notes/view/2");
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->loginUser('author');
        $this->get('epi/projects/notes/add');

        $this->assertHtmlEqualsComparison();

        // Test post request
        $data = [
            'name' => 'Mainz',
            'category' => 'Tests',
            'content' => 'testcontent',
            'norm_iri' => ''
        ];
        $this->post('epi/projects/notes/add', $data);
        $this->assertResponseCode(302);

        //Extract id from redirect
        $id = $this->extractParamFromRedirect('#view/([0-9]+)#');

        // Check if the record was created
        $record = $this->Notes->get($id);
        $compare = $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test edit method
     *
     * //TODO: rename to testEditPost
     * @return void
     */
    public function testEdit()
    {
        $this->loginUser('author');
        $this->get('epi/projects/notes/edit/2');

        $this->assertHtmlEqualsComparison();

        // Test post request
        $id = 2;
        $data = [
            'id' => $id,
            'name' => 'Wismar',
            'category' => 'Tests',
            'content' => 'This is now testing playground'
        ];
        $this->post('epi/projects/notes/unlock/' . $id . '?force=1');
        $this->post('epi/projects/notes/edit/' . $id, $data);
        $this->assertResponseCode(302);

        // Check if the record was created
        $record = $this->Notes->get($id);
        $compare = $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test lock method
     *
     * @return void
     */
    public function testLock()
    {
        $this->loginUser('author');
        $this->get('epi/projects/notes/lock/2.json');


        $permissions = $this->fetchTable('Permissions');
        $data = $permissions
            ->find()
            ->where(['entity_id' => 2, 'entity_name' => 'test_projects.notes', 'entity_type' => 'record'])
            ->disableHydration()
            ->disableResultsCasting()
            ->firstOrFail();

//        $data = Arrays::array_remove_keys($data, ['created', 'modified']);
        $this->assertArrayEqualsComparison($data);
    }

    /**
     * Test unlock method
     *
     * @return void
     */
    public function testUnlock()
    {
        $this->loginUser('author');
        $this->get('epi/projects/notes/lock/2');

        $this->get('epi/projects/notes/unlock/2');
        $actual = json_decode($this->_response->getBody(), true);
        $expected = ['status' => ['success' => false, 'message' => 'Could not unlock the entity']];
        $this->assertEquals($expected, $actual);

        $this->get('epi/projects/notes/unlock/2?force=1');
        $actual = json_decode($this->_response->getBody(), true);
        $expected = ['status' => ['success' => true, 'message' => 'Unlocked', 'unlock' => true]];
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->loginUser('author');

        // Notes entry is in database?
        $entry = $this
            ->Notes
            ->find('all')
            ->where(['id' => 2])
            ->count();
        $this->assertEquals(1, $entry);

        // A get request shows the confirmation page
        $this->get('epi/projects/notes/delete/2');
        $this->assertHtmlEqualsComparison();

        // A delete request deletes the entry
        $this->delete('epi/projects/notes/delete/2');
        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison( 'after','.content-wrapper',);

        // Check if wiki entry not in database anymore
        $entry = $this
            ->Notes
            ->find('all')
            ->where(['id' => 2])
            ->count();
        $this->assertEquals(0, $entry);
    }

}
