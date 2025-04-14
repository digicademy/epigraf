<?php

namespace Epi\Test\TestCase\Controller;

use App\Model\Table\BaseTable;
use App\Utilities\Converters\Arrays;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Controller\ArticlesController Test Case
 *
 * @uses \Epi\Controller\ArticlesController
 */
class ArticlesControllerTest extends EpiTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
        'app.Permissions',
        'app.Pipelines',
        'app.Databanks',
        'app.Jobs'
    ];

    public $dumps = [
        'test_projects' => 'test_projects.sql'
    ];

    public $Articles = null;
    public $Sections = null;
    public $Items = null;
    public $Footnotes = null;
    public $Links = null;
    /**
     * Setup the test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Articles = $this->fetchTable('Epi.Articles');
        $this->Sections = $this->fetchTable('Epi.Sections');
        $this->Items = $this->fetchTable('Epi.Items');
        $this->Footnotes = $this->fetchTable('Epi.Footnotes');
        $this->Links = $this->fetchTable('Epi.Links');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Articles);
        unset($this->Sections);
        unset($this->Items);
        unset($this->Footnotes);
        unset($this->Links);

        parent::tearDown();
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->loginUser('devel');

        // Check that the article exists and has an empty version_id field
        $id = 3;
        $articles = $this->Articles->get($id);
        $this->assertEquals(null, $articles->version_id);

        $records = [
            'sections' => 17,
            'items' => 5,
            'footnotes' => 1,
            'links' => 7
        ];

        // Check that sections, items, footnotes and links exist
        $count = $this->Sections
            ->find('all')->where(['articles_id' => $id])->count();
        $this->assertEquals($records['sections'], $count);

        $count = $this->Items
            ->find('all')->where(['articles_id' => $id])->count();
        $this->assertEquals($records['items'], $count);

        // TODO: create fixture with footnotes and links$options['deleted'] ?? 0 -> update: fixture contains footnote
        $count = $this->Footnotes
            ->find('all')->where(['root_id' => $id, 'root_tab' => 'articles'])->count();
        $this->assertEquals($records['footnotes'], $count);

        $count = $this->Links
            ->find('all')->where(['root_id' => $id, 'root_tab' => 'articles'])->count();
        $this->assertEquals($records['links'], $count);

        // Test confirmation page
        $this->get('epi/projects/articles/delete/' . $id);
        $this->assertHtmlEqualsComparison();

        // Delete the article
        $this->delete('epi/projects/articles/delete/' . $id);
        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison('after', '.content-wrapper');

        // Article must not exist any longer
        $count = $this->Articles
            ->find('all')->where(['id' => $id])->count();
        $this->assertEquals(0, $count);

        $count = $this->Sections
            ->find('all')->where(['articles_id' => $id])->count();
        $this->assertEquals(0, $count);

        $count = $this->Items
            ->find('all')->where(['articles_id' => $id])->count();
        $this->assertEquals(0, $count);

        $count = $this->Footnotes
            ->find('all')->where(['root_id' => $id, 'root_tab' => 'articles'])->count();
        $this->assertEquals(0, $count);

        $count = $this->Links
            ->find('all')->where(['root_id' => $id, 'root_tab' => 'articles'])->count();
        $this->assertEquals(0, $count);


        // Article must exist with deleted = 1
        $count = $this->Articles
            ->find('deleted', ['deleted' => true])->where(['id' => $id])->count();
        $this->assertEquals(1, $count);

        $count = $this->Sections
            ->find('deleted', ['deleted' => true])->where(['articles_id' => $id])->count();
        $this->assertEquals($records['sections'], $count);

        $count = $this->Items
            ->find('deleted', ['deleted' => true])->where(['articles_id' => $id])->count();
        $this->assertEquals($records['items'] + 7, $count); // Obviously, 7 deleted items existed before. Improve test data.

        $count = $this->Footnotes
            ->find('deleted', ['deleted' => true])->where(['root_id' => $id, 'root_tab' => 'articles'])->count();
        $this->assertEquals($records['footnotes'], $count);

        $count = $this->Links
            ->find('deleted', ['deleted' => true])->where(['root_id' => $id, 'root_tab' => 'articles'])->count();
        $this->assertEquals($records['links'], $count);

    }

    /**
     * Test view method (HTML file)
     *
     * @return void
     */
    public function testViewHtml()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/articles/view/1');
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test view method (XML file)
     *
     * @return void
     */
    public function testViewXml()
    {
        $this->loginUser('devel');
        $this->get('/epi/projects/articles/view/1.xml?snippets=indexes,paths,comments');
        $this->assertXmlResponseEqualsComparison();
    }

    /**
     * Test view method (JSON file)
     *
     * @return void
     */
    public function testViewJson()
    {
        $this->loginUser('devel');
        $this->get('/epi/projects/articles/view/1.json?snippets=indexes,paths,comments');
        $this->assertJsonResponseEqualsComparison();
    }


    /**
     * Test edit method (HTML file)
     *
     * @return void
     */
    public function testEdit()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/articles/edit/1');
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

        $before = $this->fetchTable('Epi.Articles')
            ->find('containAll',['snippets'=>[]])
            ->where(['Articles.id'=> 1])
            ->toArray();

        // TODO: Use more complex post data (including sections, items, footnotes, links)
        $this->post('/epi/projects/articles/edit/1', [
            'signature' => 'New signature',
        ]);

        $after = $this->fetchTable('Epi.Articles')
            ->find('containAll')
            ->where(['Articles.id'=> 1])
            ->toArray();

        // TODO: simplify, maybe in findContainAll, only add path&sectionpath if explicitly requested?
        $before = Arrays::array_remove_null($before);
        $before = Arrays::array_remove_keys($before, ['modified','path','sectionpath']);

        $after = Arrays::array_remove_null($after);
        $after = Arrays::array_remove_keys($after, ['modified','path','sectionpath']);

        $diff = Arrays::array_recursive_diff($before, $after, 'both');

        $compare = $this->saveComparisonJson($diff);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test index method (HTML file)
     *
     * @return void
     */
    public function testIndexHtml()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/articles/');
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test map template (HTML file)
     *
     * @return void
     */
    public function testIndexMap()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/articles/?template=map');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test map template (HTML file)
     *
     * @return void
     */
    public function testIndexMapSorted()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/articles/?template=map&sort=distance&direction=asc');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test lanes template
     *
     * @return void
     */
    public function testIndexLanes()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/articles/?properties.objecttypes.selected=32&template=lanes&lanes=objecttypes');
        $this->assertHtmlEqualsComparison();
    }

    public function testIndexSort()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/articles/?sort=items_date&direction=desc');
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test total parameter (HTML file)
     *
     * @return void
     */
    public function testIndexTotal()
    {
        $this->loginUser('admin');

        // From a total of 4 articles, 2 are shown on the first page...
        $this->get('/epi/projects/articles/?limit=2&page=1&total=3');
        $this->assertResponseContains('<span class="label actions-set-default">4 records</span>');
        $this->assertResponseContains('data-list-action-next="/epi/projects/articles?limit=2&amp;total=3&amp;page=2"');

        $this->assertResponseEqualsComparison('.page1', '.content-main' );

        //...and one is shown on the second page
        $this->get('/epi/projects/articles/?limit=2&page=2&total=3');
        $this->assertResponseContains('<span class="label actions-set-default">4 records</span>');
        $this->assertResponseContains('data-list-action-next=""');
        $this->assertResponseEqualsComparison('.page2', '.content-main' );

        //...and nothing is shown on the third page
        $this->expectException(NotFoundException::class);
        $this->get('/epi/projects/articles/?limit=2&page=3&total=3');
    }

    public function testIndexCustomColumn()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/articles/?columns=signature,items.*.value');
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test map template (HTML file)
     *
     * @return void
     */
    public function testIndexSaveSettings()
    {
        $this->loginUser('author');
        $this->get('/epi/projects/articles/index?load=1');
        $this->assertRedirect('/epi/projects/articles/index?save=1');

        $this->get('/epi/projects/articles/index?projects=1&save=1');
        $this->assertResponseCode(200);

        $this->get('/epi/projects/articles/index?load=1');
        $this->assertRedirect('/epi/projects/articles/index?projects=1&save=1');
    }

    /**
     * Test fulltext method
     *
     * @return void
     */
    public function testMutateFails()
    {

        $userRole = 'admin';
        $this->loginUser($userRole);

        $this->post('epi/projects/articles/mutate/?task=get_summary&projects=1&selection=filtered');
        $this->assertRedirect(['action'=>'mutate']);
    }

        /**
     * Test fulltext method
     *
     * @return void
     */
    public function testMutateDates()
    {
        $modifiedBefore = $this->Articles
            ->find('all')
            ->select(['id','created','modified'])
            ->disableHydration()
            ->toArray();

        $itemsBefore = $this->Items
            ->find('all')
            ->select(['id','deleted','job_id','date_value','date_start','date_end','date_sort','sections_id','articles_id'])
            ->where(['itemtype IN'=>['conditions', 'transcriptions']])
            ->disableHydration()
            ->toArray();

        $compare = $this->saveComparisonJson($itemsBefore, '.before');
        $this->assertJsonStringEqualsComparison($compare, '.before');

        $this->executeJob(
            'epi/projects/articles/mutate/?task=rebuild_dates&projects=1&selection=filtered',
            'admin'
        );

        $itemsAfter = $this->Items
            ->find('all')
            ->select(['id','deleted','job_id','date_value','date_start','date_end','date_sort','sections_id','articles_id'])
            ->where(['itemtype IN'=>['conditions', 'transcriptions']])
            ->disableHydration()
            ->toArray();

        $compare = $this->saveComparisonJson($itemsAfter, '.after');
        $this->assertJsonStringEqualsComparison($compare, '.after');

        $this->assertNotEquals($itemsBefore, $itemsAfter);

        $modifiedAfter = $this->Articles
            ->find('all')
            ->select(['id','created','modified'])
            ->disableHydration()
            ->toArray();

        $this->assertEquals($modifiedBefore, $modifiedAfter);
    }

    /**
     * Test fulltext method
     *
     * @return void
     */
    public function testMutateFulltext()
    {
        $modifiedBefore = $this->Articles
            ->find('all')
            ->select(['id','created','modified'])
            ->disableHydration()
            ->toArray();

        $itemsBefore = $this->Items
            ->find('all')
            ->select(['id','deleted','job_id','value','content','sections_id','articles_id'])
            ->where(['itemtype'=>'search'])
            ->disableHydration()
            ->toArray();

        $compare = $this->saveComparisonJson($itemsBefore, '.before');
        $this->assertJsonStringEqualsComparison($compare, '.before');

        $this->executeJob(
            'epi/projects/articles/mutate/?task=rebuild_fulltext&projects=1&selection=filtered',
            'admin'
        );

        $itemsAfter = $this->Items
            ->find('all')
            ->select(['id','deleted','job_id','value','content','sections_id','articles_id'])
            ->where(['itemtype'=>'search'])
            ->disableHydration()
            ->toArray();

        $compare = $this->saveComparisonJson($itemsAfter, '.after');
        $this->assertJsonStringEqualsComparison($compare, '.after');

        $this->assertNotEquals($itemsBefore, $itemsAfter);

        $modifiedAfter = $this->Articles
            ->find('all')
            ->select(['id','created','modified'])
            ->disableHydration()
            ->toArray();

        $this->assertEquals($modifiedBefore, $modifiedAfter);
    }
}
