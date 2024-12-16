<?php

namespace Epi\Test\TestCase\Controller;

use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Controller\SectionsController Test Case
 *
 * @uses \Epi\Controller\ArticlesController
 */
class SectionsControllerTest extends EpiTestCase
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

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test add section
     *
     * @return void
     */
    public function testAdd(): void
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/sections/add/2/chapter');
        $this->assertResponseEqualsComparison();
    }

}
