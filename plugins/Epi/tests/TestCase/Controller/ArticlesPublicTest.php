<?php

namespace Epi\Test\TestCase\Controller;

use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Controller\ArticlesController Test Case for guest users
 *
 * TODO: Add tests for guest role
 *
 * @uses \Epi\Controller\ArticlesController
 */
class ArticlesPublicTest extends EpiTestCase
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

    /**
     * Setup the test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Articles = $this->fetchTable('Epi.Articles');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Articles);
        parent::tearDown();
    }

    /**
     * Test public lanes
     *
     * @return void
     */
    public function testIndexPublicLanes()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
        //        $this->get('/epi/projects/articles/?properties.objecttypes=32&template=lanes&lanes=objecttypes');
        //        $this->assertHtmlEqualsComparison();
    }

}
