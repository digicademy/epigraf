<?php

namespace Epi\Test\TestCase\Controller;

use Authorization\Exception\ForbiddenException;
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
     * Test index method as guest user
     *
     * @return void
     */
    public function testIndexLanes()
    {
        // No access without permission
        $this->expectException(ForbiddenException::class);
        $this->get('/epi/public/articles/');

        $this->expectException(ForbiddenException::class);
        $this->get('/epi/public/articles/?properties.objecttypes=32&template=lanes&lanes=objecttypes');

        // Add permission
        $data = [
            'user_role' => 'guest',
            'user_request' => 'web',
            'entity_type' => 'databank',
            'entity_name' => 'epi_public',
            'permission_type' => 'access',
            'permission_name' => 'epi/types/index'
        ];
        $this->post('permissions/add', $data);
        $this->assertResponseCode(302);

        // Access granted
        $this->get('/epi/public/articles/?properties.objecttypes=32&template=lanes&lanes=objecttypes');
        $this->assertResponseOk();
        $this->assertHtmlEqualsComparison();

    }


}
