<?php

namespace Epi\Test\TestCase\Controller;


use App\Utilities\Converters\Arrays;
use Cake\Controller\Exception\InvalidParameterException;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Controller\PropertiesController Test Case
 *
 * @uses \Epi\Controller\PropertiesController
 */
class PropertiesControllerTest extends EpiTestCase
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
        'app.Databanks',
        'app.Jobs',
    ];

    public $dumps = [
        'test_projects' => 'test_projects.sql'
    ];

    protected $Properties;

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->tearDownFileUpload('properties_import_personen.csv');
        parent::tearDown();
    }

    /**
     * Test index redirect
     *
     * @return void
     */
    public function testIndexRedirect()
    {
        $this->loginUser('author');

        $this->get('/epi/projects/properties/index?load=1');
        $this->assertRedirect([
            'controller' => 'Properties',
            'action' => 'index',
            'objecttypes',
            '?' => ['save' => true]
        ]);
    }

    /**
     * Test tree output
     *
     * @return void
     */
    public function testIndex()
    {
        $this->loginUser('author');

        $this->get('/epi/projects/properties/index/fonttypes');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test that forbidden sort fields are ignored
     *
     * TODO: Make a real problem scenarion.
     *       The problem occurs only when the sort field is not removed in Paginator->validateSort()
     *       and the limit is lower than the available rows
     *       and the provided sort field produces an order different from the lft order
     *
     * @return void
     */
    public function testForbiddenSortfield()
    {
        $this->loginUser('author');
        $this->get('/epi/projects/properties/index/fonttypes?sort=lemma&limit=1&direction=desc');
        $this->assertHtmlEqualsComparison();
    }

    public function testConflictedCursorCondition()
    {
        $this->loginUser('author');
        $this->get('/epi/projects/properties/index/personnames?find=p&id=99999');
        $this->assertResponseOk();

        $this->get('/epi/projects/properties/index/personnames?find=p');
        $this->assertResponseContains('Personenname 1');
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test output of flat list
     *
     * //TODO: configure brand type columns and update dump and comparisons, include images
     *
     * @return void
     */
    public function testIndexFlat()
    {
        $this->loginUser('author');

        $this->get('/epi/projects/properties/index/brands');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test output of collapsed tree
     *
     * //TODO: configure brand type columns and update dump and comparisons, include images
     *
     * @return void
     */
    public function testIndexCollapsed()
    {
        $this->loginUser('author');

        $this->get('/epi/projects/properties/index/literature?collapsed=1');

        $this->assertHtmlEqualsComparison();
    }


    /**
     * Test search by term
     *
     * @return void
     */
    public function testIndexSearch()
    {
        $this->loginUser('author');

        $this->get('/epi/projects/properties/index/fonttypes/?term=Versal');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test index method (JSON file)
     *
     * @return void
     */
    public function testIndexAsJson()
    {
        $this->loginUser('author');
        $this->get('/epi/projects/properties/index/fonttypes.json');
        $this->assertJsonResponseEqualsComparison();

        $this->configRequest(['headers' => ['Accept' => 'application/json']]);
        $this->get('/epi/projects/properties/index/fonttypes');
        $this->assertJsonResponseEqualsComparison();
    }

    /**
     * Test index method (XML file)
     *
     * @return void
     */
    public function testIndexAsXml()
    {
        $this->loginUser('author');

        // Extension set to XML: Should render the XML response
        $this->get('epi/projects/properties/index/materials.xml');
        $this->assertXmlResponseEqualsComparison();

        // Accept header set to XML: Should render the XML response
        $this->configRequest(['headers' => ['Accept' => 'application/xml']]);
        $this->get('epi/projects/properties/index/materials');
        $this->assertXmlResponseEqualsComparison();

        // Empty Accept header: Should render the HTML response
        $this->configRequest(['headers' => ['Accept' => '']]);
        $this->get('epi/projects/properties/index/materials');
        $this->assertFileResponseEqualsComparison('.html');
    }

    /**
     * Test index method (CSV file)
     *
     * @return void
     */
    public function testIndexAsCsv()
    {
        $this->loginUser('author');

        $this->get('/epi/projects/properties/index/materials.csv');

        file_put_contents($this->comparisonFile . ($this->overwriteComparison ? '.csv' : '.status'),
            $this->_getBodyAsString());
        $this->assertSameAsFile($this->comparisonFile . '.csv', $this->_getBodyAsString());
    }

    /**
     * Test view method (HTML file)
     *
     * @return void
     */
    public function testView()
    {
        $this->loginUser('author');
        $this->get('/epi/projects/properties/view/119');
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
        $this->get('epi/projects/properties/add/heraldry');

        $this->assertHtmlEqualsComparison();

        // Test post request
        $data = [
            'name' => 'New Coat of Arms',
            'scope' => 'heraldry',
            'iri' => 'newcoatofarms'
        ];

        $this->post('epi/projects/properties/add/heraldry', $data);
        $this->assertResponseCode(302);

        //Extract id from redirect
        $id = $this->extractParamFromRedirect('#properties/view/([0-9]+)#');

        // Check if the record was created
        $propertiesTable = $this->fetchTable('Epi.Properties');
        $record = $propertiesTable->get($id)->toArray();
        $record = Arrays::array_remove_keys($record, ['modified']);

        $compare = $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test index method (HTML file)
     *
     * @return void
     */
    public function testEdit()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/properties/edit/119');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEditPost()
    {
        $this->loginUser('admin');

        $this->post('/epi/projects/properties/edit/119', [
            'lemma' => 'FontLemma',
            'propertytype' => 'fonttypes',
            'name' => 'FontName',
            'comment' => 'FontComment'
        ]);

        $propertiesTable = $this->fetchTable('Epi.Properties');
        $data = $propertiesTable
            ->find()
            ->where(['id' => 119])
            ->disableHydration()
            ->disableResultsCasting()
            ->firstOrFail();

        $this->assertArrayEqualsComparison($data);
    }

    /**
     * Test import preview
     *
     * @return void
     */
    public function testImportPreview()
    {
        $this->loginUser('admin');

        $folder = Configure::read('Data.databases') . 'test_projects' . DS;
        $testfilename = $this->testdataFile . '.csv';
        $this->assertFileExists($testfilename);

        $importfilename = 'import/properties_import_uploaded.csv';
        copy($testfilename, $folder . $importfilename);
        $this->assertFileExists($folder . $importfilename);

        $this->get('/epi/projects/properties/import/personnames/?filename=' . $importfilename);

        $this->assertHtmlEqualsComparison();

        unlink($folder . $importfilename);
    }

    /**
     * Test import via post
     *
     * @return void
     */
    public function testImportPost()
    {
        $this->loginUser('admin');
        $this->markTestIncomplete('Not implemented yet.');

//        // Number of records before import
//        $properties = $this->getTableLocator()->get('Epi.Properties');
//        $count = $properties->find('all')->count();
//        $this->assertEquals(54,$count);
//
//        // Copy file to temp dir
//        $testfilename = $this->testdataFile.'.csv';
//        $this->assertFileExists($testfilename);
//        copy($testfilename, TMP . DS . 'properties_import_personen.csv');
//        $this->assertFileExists(TMP . DS . 'properties_import_personen.csv');
//
//        // Insert records
//        $postdata = include $this->testdataFile;
//        $this->post('/epi/projects/properties/import/personnames?filename=properties_import_personen.csv',$postdata);
//
//        // Number of records after import
//        $count = $properties->find('all')->count();
//        $this->assertEquals(58,$count);
//
//        // Contents of imported records
//        $data = $properties->find()->where(['propertytype' => 'personnames'])->disableHydration()->disableResultsCasting()->toArray();
//        file_put_contents($this->comparisonFile.'.status',var_export($data,true));
//        $compare = include $this->comparisonFile;
//        $this->assertEquals($compare,$data);
    }

    /**
     * Test import post
     *
     * @return void
     */
    public function testImportPostPathTraversal()
    {
        $this->expectException(BadRequestException::class);
        $this->loginUser('admin');
        $this->get('/epi/projects/properties/import/personnames?filename=../config/app.php');
    }

    /**
     * Test import upload
     *
     * @return void
     */
    public function testImportUpload()
    {
        $this->loginUser('admin');

        $testfilename = $this->testdataFile . '.csv';
        $uploadedFile = $this->prepareFileUpload($testfilename, 'properties_import_personen_upload.csv', 'text/csv');
        $data = ['file' => $uploadedFile];
        $this->post('/epi/projects/properties/import/personnames', $data);

        $folder = Configure::read('Data.databases') . 'test_projects' . DS;

        $this->assertFileExists($folder . 'import/properties_import_personen_upload.csv');
        $this->assertFileEquals(
            $testfilename,
            $folder . 'import/properties_import_personen_upload.csv'
        );

        // Remove upload
        if (is_file(TMP . 'properties_import_personen_upload.csv')) {
            unlink(TMP . 'properties_import_personen_upload.csv');
        }
        unlink($folder . 'import/properties_import_personen_upload.csv');

    }

    /**
     * Test select method (HTML file)
     *
     * @return void
     */
    public function testSelectEmpty()
    {
        $this->loginUser('author');
        $this->get('epi/projects/properties/index/nothing?template=select');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test select method (HTML file)
     *
     * @return void
     */
    public function testSelectFull()
    {
        $this->loginUser('author');
        $this->get('epi/projects/properties/index/wordseparators?template=select&references=0');

        $this->assertHtmlEqualsComparison();
    }


    /**
     * Test choose template
     *
     * @return void
     */
    public function testChoose()
    {
        $this->loginUser('author');
        $this->get('/epi/projects/properties/index/fonttypes?template=choose&show=content&references=0');
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test response for invalid property types (HTML)
     *
     * @return void
     */
    public function testInvalidType()
    {
        $this->loginUser('author');
        $this->get('epi/projects/properties/index/nonexistingtype');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test index method (JSON file)
     *
     * @return void
     */
    public function testInvalidTypeAsJson()
    {
        $this->loginUser('author');
        $this->get('epi/projects/properties/index/nonexistingtype.json');

        $this->assertResponseOk();
        $this->assertJsonResponseEqualsComparison();
    }


    /**
     * Test only devel is allowed to recover the tree
     *
     * @return void
     */
    public function testMutateNotAuthorized()
    {
        $this->loginUser('reader');
        $this->expectException(ForbiddenException::class);
        $this->get('epi/projects/properties/mutate/objecttypes');
    }

    /**
     * Test only devel is allowed to recover the tree
     *
     * @return void
     */
    public function testMutateMissingScope()
    {
        $this->loginUser('author');
        $this->expectException(BadRequestException::class);
        $this->get('epi/projects/properties/mutate');
    }

    /**
     * Test sort method
     *
     * @return void
     */
    public function testSortBySortNo()
    {
        $this->Properties = $this->fetchTable('Epi.Properties');

        $tree_before = $this->Properties
            ->find('all')
            ->select(['id', 'parent_id', 'lft', 'rght', 'level'])
            ->toArray();

        $compare = $this->saveComparisonJson($tree_before, '.before');
        $this->assertJsonStringEqualsComparison($compare, '.before');

        $scopes = $this->Properties->getScopes();
        foreach ($scopes as $scope) {
            $this->executeJob(
                'epi/projects/properties/mutate/' . $scope . '?task=batch_sort&sortby=sortno',
                'admin'
            );
        }

        $tree_after = $this->Properties
            ->find('all')
            ->select(['id', 'parent_id', 'lft', 'rght', 'level'])
            ->toArray();

        $compare = $this->saveComparisonJson($tree_after, '.after');
        $this->assertJsonStringEqualsComparison($compare, '.after');

        $this->assertNotEquals($tree_before, $tree_after);
    }

    /**
     * Test sort method
     *
     * //TODO: Create fixture (from epi_test) where sortkey and sortno differ
     *
     * @return void
     */
    public function testSortBySortField()
    {

        $this->Properties = $this->fetchTable('Epi.Properties');

        $tree_before = $this->Properties
            ->find('all')
            ->select(['id', 'parent_id', 'lft', 'rght', 'level'])
            ->toArray();

        $compare = $this->saveComparisonJson($tree_before, '.before');
        $this->assertJsonStringEqualsComparison($compare, '.before');

        $scopes = $this->Properties->getScopes();
        foreach ($scopes as $scope) {
            $this->executeJob(
                'epi/projects/properties/mutate/' . $scope . '?task=batch_sort&sortby=sortkey',
                'editor'
            );
        }

        $tree_after = $this->Properties
            ->find('all')
            ->select(['id', 'parent_id', 'lft', 'rght', 'level'])
            ->toArray();

        $compare = $this->saveComparisonJson($tree_after, '.after');
        $this->assertJsonStringEqualsComparison($compare, '.after');

        $this->assertNotEquals($tree_before, $tree_after);
    }

    /**
     * Test transfer method (authors are not allowed)
     *
     * @return void
     */
    public function testTransferAuthor()
    {
        $this->loginUser('author');

        $this->expectException(ForbiddenException::class);
        $this->get('epi/projects/properties/transfer/datakeys');
    }

    /**
     * Test transfer method (editors are not allowed)
     *
     * @return void
     */
    public function testTransferEditor()
    {
        $this->loginUser('editor');

        $this->expectException(ForbiddenException::class);
        $this->get('epi/projects/properties/transfer/datakeys');
    }


    /**
     * Test transfer method (HTML file)
     *
     * @return void
     */
    public function testTransferAdmin()
    {
        $this->loginUser('admin');
        $this->get('epi/projects/properties/transfer/datakeys');
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test merging two properties
     *
     * @return void
     */
    public function testMerge() {
        $this->loginUser('admin');

        // Get properties before merge operation
        $before = $this->fetchTable("Epi.Properties")
            ->find('deleted',['deleted'=>[0,1,2]])
            ->where(['propertytype'=>'objecttypes'])
            ->disableHydration()
            ->toArray();

        $before = Arrays::array_remove_keys($before, ['created','modified']);
        $compare = $this->saveComparisonJson($before,".1_before");
        $this->assertJsonStringEqualsComparison($compare, ".1_before");

        // Merge
        $this->post('epi/projects/properties/merge/?source=32&target=36', []);

        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison( '.after','.content-wrapper',);

        // Get properties after merge operation
        $after = $this->fetchTable("Epi.Properties")
            ->find('deleted',['deleted'=>[0,1,2]])
            ->where(['propertytype'=>'objecttypes'])
            ->disableHydration()
            ->toArray();

        $after = Arrays::array_remove_keys($after, ['created','modified']);
        $compare = $this->saveComparisonJson($after,".2_after");
        $this->assertJsonStringEqualsComparison($compare, ".2_after");

//        $deleted = $this->fetchTable("Epi.Properties")
//            ->find('deleted',['deleted'=>[1,2]])
//            ->where(['propertytype'=>'objecttypes'])
//            ->disableHydration()
//            ->toArray();
//
//        $deleted = Arrays::array_remove_keys($deleted, ['created','modified']);
//        $compare = $this->saveComparisonJson($deleted,".3_deleted");
//        $this->assertJsonStringEqualsComparison($compare, ".3_deleted");


        // Compare
        $before = array_column($before, null, 'id');
        $after = array_column($after, null, 'id');
        $diff = Arrays::array_recursive_diff($before, $after, 'both');
        $compare = $this->saveComparisonJson($diff);
        $this->assertJsonStringEqualsComparison($compare);
    }


    /**
     * Test merging two properties with concatenating the values
     *
     * @return void
     */
    public function testMergeConcat() {
        $this->loginUser('admin');

        // Set IRIs of both properties and check there are no IRIs in the merged property
        $propertiesTable = $this->fetchTable("Epi.Properties");
        $propertiesTable->updateAll(['norm_iri' => 'source~id32'], ['id' => 32]);
        $propertiesTable->updateAll(['norm_iri' => 'source~id36'], ['id' => 36]);

        // Get properties before merge operation
        $before = $this->fetchTable("Epi.Properties")
            ->find('deleted',['deleted'=>[0,1,2]])
            ->where(['propertytype'=>'objecttypes'])
            ->disableHydration()
            ->toArray();

        $before = Arrays::array_remove_keys($before, ['created','modified']);
        $compare = $this->saveComparisonJson($before,".1_before");
        $this->assertJsonStringEqualsComparison($compare, ".1_before");

        // Select
        $this->get('epi/projects/properties/merge/32?concat=1&preview=1');
        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison('.select');

        // Preview
        $this->get('epi/projects/properties/merge/?source=32&target=36&concat=1');
        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison('.preview');

        // Merge
        $this->post('epi/projects/properties/merge/?source=32&target=36&concat=1', []);

        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison( '.after');

        // Get properties after merge operation
        $after = $this->fetchTable("Epi.Properties")
            ->find('deleted',['deleted'=>[0,1,2]])
            ->where(['propertytype'=>'objecttypes'])
            ->disableHydration()
            ->toArray();

        $after = Arrays::array_remove_keys($after, ['created','modified']);
        $compare = $this->saveComparisonJson($after,".2_after");
        $this->assertJsonStringEqualsComparison($compare, ".2_after");

        // Compare
        $before = array_column($before, null, 'id');
        $after = array_column($after, null, 'id');
        $diff = Arrays::array_recursive_diff($before, $after, 'both');
        $compare = $this->saveComparisonJson($diff);
        $this->assertJsonStringEqualsComparison($compare);
    }

}
