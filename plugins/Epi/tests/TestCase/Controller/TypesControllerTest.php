<?php

declare(strict_types=1);

namespace Epi\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;

/**
 * App\Controller\TypesController Test Case
 *
 */
class TypesControllerTest extends EpiTestCase
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
        'app.Databanks'
    ];

    public $dumps = [
        'test_projects' => 'test_projects.sql'
    ];

    public $Types = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Types = $this->fetchTable('Epi.Types');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Types);
        parent::tearDown();
    }


    /**
     * Test view method  (HTML file)
     *
     * @return void
     */
    public function testView()
    {
        $this->loginUser('admin');
        $this->get('epi/projects/types/view/1');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->loginUser('admin');
        $this->get('epi/projects/types/add');

        $this->assertHtmlEqualsComparison();

        // Test post request
        $data = [
            'name' => 'library',
            'scope' => 'articles',
            'caption' => 'Bibliothek',
            'iri' => 'library'
        ];
        $this->post('epi/projects/types/add', $data);
        $this->assertResponseCode(302);

        //Extract id from redirect
        $id = $this->extractParamFromRedirect('#types/view/([0-9]+)#');

        // Check if the record was created
        $record = $this->Types->get($id);
        $compare = $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test edit method
     *
     * //TODO: rename to testEditPost
     *
     * @return void
     */
    public function testEdit()
    {
        $this->loginUser('admin');
        $this->get('epi/projects/types/edit/1');

        $this->assertHtmlEqualsComparison();

        // Test post request
        $id = 2;
        $data = [
            'id' => $id,
            'name' => 'anothermeasure',
            'caption' => 'Vermessung',
        ];
        $this->post('epi/projects/types/edit/' . $id, $data);
        $this->assertResponseCode(302);

        // Check if the record was updated
        $record = $this->Types->get($id);
        $compare = $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->loginUser('admin');

        // Record is in database?
        $entry = $this->Types
            ->find('all')
            ->where(['id' => 1])
            ->count();
        $this->assertEquals(1, $entry);

        // A get request shows the confirmation page
        $this->get('epi/projects/types/delete/1');
        $this->assertHtmlEqualsComparison();

        // A delete request deletes the entry
        $this->delete('epi/projects/types/delete/1');
        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison( 'after','.content-wrapper',);

        // Check if wiki entry not in database anymore
        $entry = $this
            ->Types
            ->find('all')
            ->where(['id' => 1])
            ->count();
        $this->assertEquals(0, $entry);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->loginUser('admin');

        $this->get('/epi/projects/types/index');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexAuthor()
    {
        $this->loginUser('author');

        $this->get('/epi/projects/types/index');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexItems()
    {
        $this->loginUser('admin');

        $this->get('/epi/projects/types/index?scopes=items');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test index method, necessary for the editor
     *
     * @return void
     */
    public function testIndexJson()
    {
        $this->loginUser('author');

        $this->get('/epi/projects/types/index.json');
        $this->assertJsonResponseEqualsComparison();
    }

    /**
     * Test index method (CSV file)
     *
     * @return void
     */
    public function testIndexCsv()
    {
        $this->loginUser('author');

        $this->get('/epi/projects/types/index.csv');

        file_put_contents($this->comparisonFile. ($this->overwriteComparison ? '.csv' : '.status') ,$this->_getBodyAsString());
        $this->assertSameAsFile($this->comparisonFile.'.csv', $this->_getBodyAsString());
    }

}
