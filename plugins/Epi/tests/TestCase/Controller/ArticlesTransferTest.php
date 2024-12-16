<?php
namespace Epi\Test\TestCase\Controller;

use App\Model\Entity\Databank;
use App\Utilities\Converters\Arrays;
use Cake\Collection\CollectionInterface;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Controller\Component\TransferComponent Test Case
 *
 * @uses \Epi\Controller\ArticlesController
 */
class ArticlesTransferTest extends EpiTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
        'app.Jobs',
        'app.Permissions',
        'app.Pipelines',
        'app.Databanks',
    ];


    public $dumps = [
        'test_projects' => 'test_projects.sql'
    ];

    public $Articles = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->createDatabase('import');

        $this->fetchTable('Databanks')
            ->activateDatabase('import');

        $this->Articles = $this->fetchTable('Epi.Articles');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->removeDatabase('import');


        $this->fetchTable('Databanks')
            ->activateDatabase('test_projects');

        unset($this->Articles);
        parent::tearDown();
    }

    /**
     * Test import method
     * //TODO: test skip and clear actions
     *
     * @return void
     */
    public function testImport()
    {
        $this->loginUser('admin');

        // Get page
        $this->get('/epi/import/articles/import');
        $this->assertHtmlEqualsComparison();

        // Upload file
        $testfilename = $this->testdataFile.'.article.csv';
        $uploadedFile = $this->prepareFileUpload($testfilename,'article.csv','text/csv');
        $data = ['file' => $uploadedFile];
        $this->post('/epi/import/articles/import', $data);

        $folder = Configure::read('Data.databases') . Databank::addPrefix('import') . DS;
        $this->assertFileExists($folder . 'import/article.csv');

//        // Preview
//        $this->get('/epi/test_import/articles/import?filename=import/article.csv');
//        $this->assertHtmlEqualsComparison(true,"//*[@id='content-wrapper' or self::footer]",'.preview');
//
//        // Import
//        $this->post('/epi/test_import/articles/import?filename=import/article.csv');

        $this->assertRedirect('/jobs/execute/3?database=import');

        // Poll for 10 rounds at maximum
        $polling = 10;
        while ($polling) {
            $this->get('/jobs/execute/3?database=import&timeout=20');
            $polling = ($this->_response->getHeader('location')) ? false : $polling - 1;
        }

        $this->assertRedirect('/epi/import/articles/index');

        // Get result
        $article = $this->Articles->find('all')->orderDesc('created')->first();

        $this->get('/epi/import/articles/view/' . $article->id . '.xml?snippets=indexes,paths,comments');
        $this->assertXmlResponseEqualsComparison('.result');

        // Remove uploaded file
        unlink($folder . 'import/article.csv');
        $this->tearDownFileUpload('article.csv');
    }

    /**
     * Test transfer method
     *
     * @return void
     */
    public function testTransfer()
    {

        // Source database: Get article before transfer operation
        $this->fetchTable('Databanks')
            ->activateDatabase('projects');

        $article_source = $this->fetchTable('Epi.Articles')
            ->find('containAll')
            ->where(['Articles.id'=>1])
            ->toArray();

        // Those fields will not be compared
        $removeFields = [
            // Times will change
            'modified','created','modified_by','created_by',

            // IDs will change
            'database','id','job_id','root_id','from_id','parent_id',
            'projects_id','articles_id','properties_id','sections_id',
            'to_id','links_id',
            'related_id',

            // Structure will be merged
            'lft','rght',

            // Identifiers can change
            'import_db','import_id',//'keywords',
            'norm_iri', // TODO: write test for double import

            // Not transferred
            'type','level','ancestors',

            // Not used
            'to_field','to_tagid',
            'links_field','links_tagid',

            // Not used in EpiWeb (@deprecated)
            'fntype','sortno', // Only used in EpiDesktop footnotes
            'book_number','book_name', // TODO: remove from test data
            'lastopen_id', 'lastopen_tab','lastopen_field','lastopen_tagid',
        ];

        $removeNestedFields = [
            '*.project.description',
            '*.footnotes.*.name',
            '*.links.*.footnote.name','*.links.*.footnote.content',
            '*.links.*.footnote.article.project','*.links.*.footnote.article.norm_data','*.links.*.footnote.article.status',
            '*.links.*.section.status',
            '*.links.*.section.article.project','*.links.*.section.article.norm_data','*.links.*.section.article.status','*.links.*.section.article.project',
            '*.links.*.article.status','*.links.*.article.norm_data','*.links.*.article.project'
        ];

        $article_source = Arrays::array_remove_null($article_source);
        $article_source = Arrays::array_remove_keys($article_source,$removeFields);
        $article_source = Arrays::array_remove_keys($article_source,$removeNestedFields, true);

        $article_source_json = $this->saveComparisonJson($article_source,'.source');
//        $this->assertJsonStringEqualsComparison($article_source_json,'.source');

        // Transfer
        $this->loginUser('admin');
        $this->post(
            'epi/import/articles/transfer'
            .'?source=projects&articles=1&versions=1'
            .'&snippets=comments,editors,published&tree=1',
            []
        );
        $this->assertRedirect([
            'plugin' => false,
            'controller' => 'Jobs',
            'action' => 'execute',
            3,
            '?'=>['database' => 'import', 'close'=>0]
        ]);

        // Poll for 10 rounds at maximum
        $polling = 10;
        while ($polling) {
            $this->get('/jobs/execute/3');
            $polling = ($this->_response->getHeader('location')) ? false : $polling - 1;
        }

        $this->assertRedirect([
            'plugin' => 'epi',
            'database' => 'import',
            'controller' => 'Articles',
            'action' => 'index',
            null
        ]);

        // Target database
        $this->fetchTable('Databanks')
            ->activateDatabase('import');

        $article_target = $this->Articles
            ->find('containAll')
            ->where(['Articles.id'=>1])
            ->toArray();

        $article_target = Arrays::array_remove_null($article_target);
        $article_target = Arrays::array_remove_keys($article_target,$removeFields);
        $article_target = Arrays::array_remove_keys($article_target,$removeNestedFields, true);

        $article_target_json = $this->saveComparisonJson($article_target,'.target');
//        $this->assertJsonStringEqualsComparison($article_target_json,'.target');

        // Compare
        $this->assertGreaterThan(130000, mb_strlen($article_target_json));
        $this->assertTextEquals($article_source_json, $article_target_json);
    }

    /**
     * Test mutate method
     *
     * @return void
     */
    public function testMutateProject()
    {
        $this->fetchTable('Databanks')
            ->activateDatabase('test_projects');

        // Get article before mutate operation
        $article_source = $this->fetchTable('Epi.Articles')
            ->find('containAll')
            ->where(['Articles.id'=>1])
            ->first();

        $this->assertEquals(1, $article_source['projects_id']);

        // Mutate
        $this->loginUser('admin');
        $postData = ['config'=>['params'=>['target'=>2]]];
        $this->post('epi/projects/articles/mutate?articles=1&task=assign_project&selection=selected', $postData);
        $this->assertRedirect([
            'plugin' => false,
            'controller' => 'Jobs',
            'action' => 'execute',
            3,
            '?' => ['database' => 'import', 'close' => 0]
        ]);

        // Poll for 10 rounds at maximum
        $polling = 10;
        while ($polling) {
            $this->get('/jobs/execute/3');
            $polling = ($this->_response->getHeader('location')) ? false : $polling - 1;
        }

        $this->assertRedirect([
            'plugin' => 'epi',
            'database' => 'projects',
            'controller' => 'Articles',
            'action' => 'index',
            null,
            '?'=>['projects'=>2]
        ]);

        // Target article
        $article_target = $this->fetchTable('Epi.Articles')
            ->find('containAll')
            ->where(['Articles.id'=>1])
            ->first();

        $this->assertEquals(2, $article_target['projects_id']);

        // Compare
        $removeFields = [
            'modified','created','modified_by','created_by','projects_id','project'
        ];

        $article_source = Arrays::array_remove_null($article_source);
        $article_source = Arrays::array_remove_keys($article_source, $removeFields);
        $article_source_json = $this->saveComparisonJson($article_source,'.source');

        $article_target = Arrays::array_remove_null($article_target);
        $article_target = Arrays::array_remove_keys($article_target, $removeFields);
        $article_target_json = $this->saveComparisonJson($article_target,'.target');

        $this->assertGreaterThan(130000, mb_strlen($article_target_json));
        $this->assertTextEquals($article_source_json,$article_target_json);

    }
}
