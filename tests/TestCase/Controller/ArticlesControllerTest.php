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


/**
 * App\Controller\ArticlesController Test Case
 *
 * @uses \App\Controller\ArticlesController
 */
class ArticlesControllerTest extends AppTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
        'app.Permissions',
        'app.Databanks',
        'app.Pipelines',

        'plugin.Epi.Meta',
        'plugin.Epi.Token'
    ];

    /**
     * Test search method for author role
     *
     * @return void
     */
    public function testSearchProject()
    {
        $this->loginUser('author');
        $this->get("/articles/search?database=projects&project=1");

        $this->assertRedirect("/epi/projects/articles?projects=1");
    }


    /**
     * Test search method
     *
     * @return void
     */
    public function testSearchNoAuth()
    {
        $this->get("/articles/search?database=projects&project=1");
        $this->assertRedirect(['controller' => 'Users', 'action' => 'login', '?' => ['redirect' => '/articles/search?database=projects&project=1']]);
    }


    /**
     * Test show method for author role
     *
     * @return void
     */
    public function testShow()
    {
        $this->loginUser('author');
        $this->get("/show?database=projects&article=1");
        $this->assertRedirect("/epi/projects/articles/view/1");
    }

    /**
     * Test show method
     *
     * @return void
     */
    public function testShowNoAuth()
    {
        $this->get("/show?database=projects&article=1");
        $this->assertRedirect(['controller' => 'Users', 'action' => 'login', '?' => ['redirect' => '/show?database=projects&article=1']]);
    }

}
