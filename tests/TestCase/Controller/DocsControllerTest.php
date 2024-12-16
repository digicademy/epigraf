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

use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\MissingControllerException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use App\Test\TestCase\AppTestCase;
use Cake\ORM\TableRegistry;

/**
 * App\Controller\DocsController Test Case
 *
 * @uses \App\Controller\HelpController
 */
class DocsControllerTest extends AppTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Docs',
        'app.Users',
        'app.Permissions',
        'app.Databanks',
        'app.Pipelines',
        'plugin.Epi.Meta',
        'plugin.Epi.Token'
    ];

    /**
     * Docs property
     *
     * @var \App\Model\Table\DocsTable
     */
    protected $Docs;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Docs = $this->fetchTable('Docs');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Docs);
        parent::tearDown();
    }


	/**
	 * Test homepage
	 *
	 * @return void
	 */
	public function testHomepage()
	{
		$this->get('/');
		$this->assertResponseOk();

        $this->assertHtmlEqualsComparison(true,'');

		$page1 = $this->_getBodyAsString();

		$this->get('/pages/start');
		$this->assertResponseOk();
		$page2 = $this->_getBodyAsString();

        $this->get('/docs/show/pages/start');
        $this->assertResponseOk();
        $page3 = $this->_getBodyAsString();

		$this->assertTextEquals($page1,$page2);
        $this->assertTextEquals($page1,$page3);
	}

    /**
     * Test help start page for author role
     *
     * @return void
     */
    public function testHelpShowStart()
    {
        $this->loginUser('author');

        $this->get("/help");
        $this->assertHtmlEqualsComparison();

        $page1 = $this->_getBodyAsString();

        $this->get('/docs/show/help/start');
        $this->assertResponseOk();
        $page2 = $this->_getBodyAsString();

        $this->assertTextEquals($page1,$page2);
    }

    /**
     * Test help view for author role
     *
     * @return void
     */
    public function testHelpViewAuthor()
    {
        //TODO: use different doc, the chosen doc contains a broken header structure
        $this->loginUser('author');

        // This is the help landing page
        $this->get("/docs/view/help/73");
        $this->assertRedirect("/help");

        $this->get("/help");
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test show method for author role
     *
     * uses norm_iri.
     *
     * @return void
     */
    public function testHelpShowAuthor()
    {
        $this->loginUser('author');

        $this->get("/help/window-uebersicht");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test help redirect for author role
     *
     * Authors should be redirected to the add method for missing help pages.
     *
     * @return void
     */
    public function testHelpRedirectMissingAuthor()
    {
        $this->loginUser('author');

        $this->get("/wiki/nonexistingkey");
        $this->assertRedirect("/docs/add/wiki/nonexistingkey");
    }

    /**
     * Test help index method for author role
     *
     * @return void
     */
    public function testHelpIndex()
    {
        $this->loginUser('author');

        $this->get("docs/index/help");
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test help search method for author role
     *
     * @return void
     */
    public function testHelpSearch()
    {
        $this->loginUser('author');

        $this->get("docs/index/help?term=epigraf");
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test help empty method for author role
     *
     * @return void
     */
    public function testHelpEmptySearch()
    {
        $this->loginUser('author');

        $this->get("docs/index/help?term=thistermisnotthere");
        $this->assertHtmlEqualsComparison();
    }


    /**
     * Test redirect for unauthenticated users
     *
     * Test that unauthenticated users are redirected to login on nonpublic pages.
     *
     * @return void
     */
    public function testHelpViewRedirectNoAuth()
    {
        $this->get("/docs/view/help/73");
        $this->assertRedirect(['controller' => 'Users', 'action' => 'login','?'=>['redirect'=>'/docs/view/help/73?login=1']]);
    }

    /**
     * Test redirect for authenticated users
     *
     * Test that token authenticated users are redirected to login.
     *
     * @return void
     */
    public function testHelpViewRedirectTokenAuth()
    {
        $this->get("/docs/view/help/73?token=TESTTOKENAUTHOR");
        $this->assertRedirect(['controller' => 'Users', 'action' => 'login','?'=>['redirect'=>'/docs/view/help/73?login=1','token'=>false]]);
    }

    /**
     * Test redirect for users with a wrong token
     *
     * Test that users with a wrong token are redirected to login.
     *
     * @return void
     */
    public function testHelpViewWrongToken()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Unauthorized');

        $this->get("/docs/view/help/73?token=TESTTOKENWRONGAUTHOR");
        //$this->assertRedirect(['controller' => 'Users', 'action' => 'login','?'=>['redirect'=>'/docs/view/help/73']]);
    }

    /**
     * Test redirect from desktop for authenticated users
     *
     * Test that token authenticated users are redirected to login.
     *
     * @return void
     */
    public function testHelpOpenFromDesktop()
    {
        $this->get("/help?token=TESTTOKENAUTHOR");
        $this->assertRedirect(['controller' => 'Users', 'action' => 'login', '?' => ['redirect' => '/help?login=1']]);
    }

    /**
     * Test add method for author role
     *
     * @return void
     */
    public function testHelpAdd()
    {
        $this->loginUser('editor');

        // Test add page
        $this->get("docs/add/help");
        $this->assertHtmlEqualsComparison();

        // Test post request
        $data = [
            'name' => 'testpage',
            'content' => 'testcontent',
            'category' => 'testcategory'
        ];
        $this->post('docs/add/help', $data);
        $this->assertResponseCode(302);

        // Extract id from redirect
        $id = $this->extractParamFromRedirect('#docs/view/help/([0-9]+)#');

        // Check the record was created
        $record = $this->Docs->get($id);
        $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison($record);
    }

    /**
     * Test edit method for author role
     *
     * @return void
     */
    public function testHelpEdit()
    {
        // Table content before edit
        $docs = $this->fetchTable("Docs");
        $rows = $docs
            ->find()
            ->disableHydration()
            ->toArray();
        $this->assertCount(13, $rows);

        $rows = $this->saveComparisonJson($rows,'.beforesave');
        $this->assertJsonStringEqualsComparison($rows,'.beforesave');

        // Get edit page
        $this->loginUser('editor');

        $this->get("docs/edit/help/47");
        $this->assertHtmlEqualsComparison();

        $this->post("docs/unlock/help/47?force=1");

        // Post data
        $data = [
            'id' => 47,
            'content' => 'BLANK NEW CONTENT'
        ];
        $this->post('docs/edit/help/47', $data);

        // Redirect to the doc page
        $this->assertRedirect('/docs/view/help/47');
        $this->assertResponseCode(302);

        // Table content after edit (disable VersionBehavior to get unfiltered content)
        $docs = $this->fetchTable("Docs");
        $docs->removeBehavior('Version');
        $rows = $docs
            ->find()
            ->disableHydration()
            ->toArray();
        $this->assertCount(5, $rows);

        $rows = $this->saveComparisonJson($rows,'.aftersave');
        $this->assertJsonStringEqualsComparison($rows,'.aftersave');
    }


    /**
     * Test lock method for author role
     *
     * @return void
     */
    public function testHelpLock()
    {
        $this->loginUser('editor');

        $this->get('docs/lock/help/47.json');

        $permissions = $this->fetchTable('Permissions');
        $data = $permissions
            ->find()
            ->where(['entity_id' => 47,'entity_name'=>'test_epigraf.docs','entity_type'=>'record'])
            ->disableHydration()
            ->disableResultsCasting()
            ->firstOrFail();

        $this->assertArrayEqualsComparison($data);
    }

    /**
     * Test unlock method for author role
     *
     * @return void
     */
    public function testHelpUnlock()
    {
        $this->loginUser('editor');

        $this->get('docs/lock/help/47.json');
        $this->get('docs/unlock/help/47.json?force=1');
        $this->assertJsonResponseEqualsComparison();
    }

    /**
     * Test delete method for author role
     *
     * @return void
     */
    public function testHelpDelete()
    {
        $this->loginUser('editor');

        $this->get("docs/delete/help/47");
        $this->assertHtmlEqualsComparison();
    }


    /**
     * Test view static pages for admin role
     *
     * Test that admin users can view static pages.
     *
     * @return void
     */
    public function testPagesShowStaticAdmin()
    {
        $this->loginUser('admin');

        $this->get('/pages/empty');
        $this->assertResponseOk();
    }

    /**
     * Test that author users can't view static pages.
     *
     * @return void
     */
    public function testPagesShowStaticAuthor()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('You are not allowed to access this page, please login.');

        $this->loginUser('author');
        $this->get('/pages/empty');
    }

    /**
     * Test that unauthenticated users can't view static pages
     *
     * @return void
     */
    public function testPagesShowStaticNoAuth()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('You are not allowed to access this page, please login.');

        $this->get('/pages/empty');
    }

    /**
     * Test view missing pages for unauthenticated users
     *
     * Test that missing pages throw a 404 for unauthenticated users.
     *
     * @return void
     */
    public function testPagesShowMissingNoAuth()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('This page is not born yet.');

        $this->get('/pages/nonexistingkey');
    }

    /**
     * Test that admins are redirected to the add method for missing pages
     *
     * @return void
     */
    public function testPagesRedirectMissingAdmin()
    {
        $this->loginUser('admin');
        $this->get('/pages/nonexistingkey');
        $this->assertRedirect("/docs/add/pages/nonexistingkey");
    }

    /**
     * Test that authors can't change pages
     *
     * @return void
     */
    public function testPagesEditDeleteAddAuthor()
    {
        $this->loginUser('author');

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('You are not authorized to access that location.');
        $this->get('/docs/edit/pages/151');

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('You are not authorized to access that location.');
        $this->get('/docs/delete/pages/151');

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('You are not authorized to access that location.');
        $this->get('/docs/add/pages');

        // TODO: should be a not allowed message
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('This page is not born yet');
        $this->get('/docs/edit/151');
    }

    /**
     * Test wiki start page
     *
     * @return void
     */
    public function testWikiShowStart()
    {
        $this->loginUser('author');

        $this->get('/wiki');
        $this->assertHtmlEqualsComparison();

        $page1 = $this->_getBodyAsString();

        $this->get('/docs/show/wiki/start');
        $this->assertResponseOk();
        $page2 = $this->_getBodyAsString();

        $this->assertTextEquals($page1,$page2);
    }

    /**
     * Test wiki view method
     *
     * @return void
     */
    public function testWikiView()
    {
        $this->loginUser('author');

        // This is the wiki landing page
        $this->get("/docs/view/wiki/117");
        $this->assertRedirect('/wiki');

        $this->get("/wiki");
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test wiki index method
     *
     * @return void
     */
    public function testWikiIndex()
    {
        $this->loginUser('author');
        $this->get("/docs/index/wiki");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test the wiki index is not exposed to readers
     *
     * @return void
     */
    public function testWikiIndexReader()
    {
        $this->loginUser('reader');

        //$this->expectException(ForbiddenException::class);
        //$this->expectExceptionMessage('You are not authorized to access that location.');
        $this->get("/docs/index/wiki");
        $this->assertHtmlEqualsComparison();

    }

    /**
     * Test view wiki redirect
     *
     * @return void
     */
    public function testWikiViewRedirectTokenAuth()
    {
        $this->get("/docs/view/wiki/117?token=TESTTOKENAUTHOR");
        $this->assertRedirect(['controller' => 'Users', 'action' => 'login', '?' => ['redirect' => '/docs/view/wiki/117?login=1']]);
    }

    /**
     * Test open wiki from desktop
     *
     * @return void
     */
    public function testWikiOpenFromDesktop()
    {
        $this->get("/wiki?token=TESTTOKENAUTHOR");
        $this->assertRedirect(['controller' => 'Users', 'action' => 'login', '?' => ['redirect' => '/wiki?login=1']]);
    }

    /**
     * Test that authors are redirected to the add method for missing wiki pages
     *
     * @return void
     */
    public function testWikiRedirectMissingAuthor()
    {
        $this->loginUser('author');

        $this->get("/wiki/nonexistingkey");
        $this->assertRedirect("/docs/add/wiki/nonexistingkey");
    }

    /**
     * Test lock method
     *
     * @return void
     */
    public function testWikiLock()
    {
        $this->loginUser('author');
        $this->get('docs/lock/wiki/96.json');

        $permissions = $this->fetchTable('Permissions');
        $data = $permissions
            ->find()
            ->where(['entity_id' => 96, 'entity_name' => 'test_epigraf.docs', 'entity_type' => 'record'])
            ->disableHydration()
            ->disableResultsCasting()
            ->firstOrFail();

        $this->assertArrayEqualsComparison($data);
    }

    /**
     * Test unlock method
     *
     * @return void
     */
    public function testWikiUnlock()
    {
        $this->loginUser('author');
        $this->get('docs/lock/wiki/96');
        $this->get('docs/unlock/wiki/96?force=1');
        $this->assertJsonResponseEqualsComparison();
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testWikiAdd()
    {
        $this->loginUser('admin');
        $this->get("docs/add/wiki");

        $this->assertHtmlEqualsComparison();

        // Test post request
        $data = [
            'name' => 'testpage',
            'content' => 'testcontent',
            'category' => 'testcategory'
        ];
        $this->post('docs/add/wiki', $data);
        $this->assertResponseCode(302);

        //Extract id from redirect
        $id = $this->extractParamFromRedirect('#docs/view/wiki/([0-9]+)#');

        // Check if the record was created
        $record = $this->Docs->get($id);
        $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison($record);
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testWikiEdit()
    {
        $this->loginUser('admin');
        $this->get("docs/edit/wiki/96");

        $this->assertHtmlEqualsComparison();

        // Test post request
        $id = 120;
        $data = [
            'id' => $id,
            'name' => 'testpage',
            'content' => 'testcontent',
            'category' => 'testcategory'
        ];
        $this->post('docs/edit/wiki/' . $id, $data);
        $this->assertResponseCode(302);

        // Check if the record was created
        $record = $this->Docs->get($id);
        $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison($record);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testWikiDelete()
    {
        $this->loginUser('admin');

        // Wiki entry is in database?
        $entry = $this
            ->Docs
            ->find('all')
            ->where(['id' => 96])
            ->count();
        $this->assertEquals(1, $entry);

        // A get request shows the confirmation page
        $this->get("docs/delete/wiki/96");
        $this->assertHtmlEqualsComparison();

        // A delete request deletes the entry
        $this->delete('docs/delete/wiki/96');
        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison( 'after','.content-wrapper',);

        // Assert that wiki record  is not in the database anymore
        $entry = $this
            ->Docs
            ->find('all')
            ->where(['id' => 96])
            ->count();
        $this->assertEquals(0, $entry);
    }

    /**
     * Test devel layout
     *
     * @return void
     */
    public function testLayout()
    {
        $this->loginUser('devel');
        $this->get("/docs/index/wiki?database=projects");

        $this->assertHtmlEqualsComparison(true, "");
    }

    /**
     * Test admin menu method
     *
     * @return void
     */
    public function testMenuAdmin()
    {
        $this->loginUser('admin');
        $this->get("/docs/index/wiki?database=projects");


        $this->assertHtmlEqualsComparison(true, ".actions-main");
    }

    /**
     * Test author menu method
     *
     * @return void
     */
    public function testMenuAuthor()
    {
        $this->loginUser('author');
        $this->get("/docs/index/wiki?database=projects");


        $this->assertHtmlEqualsComparison(true, ".actions-main");
    }

    /**
     * Test editor menu method
     *
     * @return void
     */
    public function testMenuEditor()
    {
        // Grant access to editor user (ID 4, see fixture)
        $permissionTable = $this->fetchTable('Permissions');
        $permission = ['user_id' => 4, 'entity_type' => 'databank', 'entity_name' => 'text_projects', 'entity_id' => 1,'permission_type' => 'access'];
        $permissionTable->addPermission($permission);

        $this->loginUser('editor');
        $this->get("/docs/index/wiki?database=projects");


        $this->assertHtmlEqualsComparison(true, ".actions-main");
    }

    /**
     * Test index and view method with missing segment
     *
     * @return void
     */
    public function testMissingSegment()
    {
        $this->loginUser('author');

        $this->expectException(MissingControllerException::class);
        $this->expectExceptionMessage('Controller class Docs could not be found.');
        $this->get("docs/index");

        $this->expectException(MissingControllerException::class);
        $this->expectExceptionMessage('Controller class Docs could not be found.');
        $this->get("docs/view/73");
    }

//    /**
//     * Test directory traversal protection
//     *
//     * Tries to render Layout/ajax.ctp, make sure the file exists.
//     *
//     * @return void
//     */
//    public function testDirectoryTraversalProtection()
//    {
//        // To test response errors
//        $this->restoreErrorHandlerMiddleware();
//
//        $this->loginUser('devel');
//        $this->get('/pages/../Layout/ajax');
//        file_put_contents($this->comparisonFile.'.status',$this->_getBodyAsString());
//
//        $this->assertResponseCode(404);
//        $this->assertResponseContains('Not found');
//    }

}
