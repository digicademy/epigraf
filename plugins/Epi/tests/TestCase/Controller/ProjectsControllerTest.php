<?php
namespace Epi\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Controller\ProjectsController Test Case
 *
 * @uses \Epi\Controller\ProjectsController
 */
class ProjectsControllerTest extends EpiTestCase
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
    ];

    public $dumps = [
        'test_projects' => 'test_projects.sql'
    ];

    public $Projects = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Projects = $this->fetchTable('Epi.Projects');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Projects);
        parent::tearDown();
    }

    /**
     * Test view method (HTML file)
     *
     * @return void
     */
    public function testViewHtml()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/projects/view/1');

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
        $this->get('/epi/projects/projects/view/1.xml');
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
        $this->get('/epi/projects/projects/view/1.json');
        $this->assertJsonResponseEqualsComparison();
    }

    /**
     * Test edit method (HTML file)
     *
     * //TODO: implement testEditPost (see PropertiesControllerTest.php)
     *
     * @return void
     */
    public function testEdit()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/projects/edit/1');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test edit method (HTML file)
     *
     * //TODO: implement testAddPost (see PropertiesControllerTest.php)
     *
     * @return void
     */
    public function testAdd()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/projects/add');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test index method (HTML file)
     *
     * @return void
     */
    public function testIndexHtml()
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/projects/');

        $this->assertHtmlEqualsComparison();
    }

}
