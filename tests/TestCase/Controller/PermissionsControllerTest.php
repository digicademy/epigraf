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
use Cake\Chronos\Chronos;
use Cake\Http\Exception\ForbiddenException;

/**
 * App\Controller\PermissionsController Test Case
 *
 * @uses \App\Controller\PermissionsController
 */
class PermissionsControllerTest extends AppTestCase
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

        'plugin.Epi.Token',
        'plugin.Epi.Meta',

        'app.Docs'
    ];

    public $Permissions = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Permissions = $this->fetchTable('Permissions');
    }

    /**
     * Test index method for admin role
     *
     * @return void
     */
    public function testIndexAdmin()
    {
        $this->loginUser('admin');

        $this->get('permissions/index');
        $this->assertResponseCode(200);

        $this->assertHtmlEqualsComparison();

    }

    /**
     * Test index method for admin role with selected columns
     *
     * @return void
     */
    public function testFilterColumnsAdmin()
    {
        $this->loginUser('admin');

        $this->get('permissions/index?columns=username%2Cuser_role%2Centity_type%2Cpermission_type&entity_name=projects');
        $this->assertResponseCode(200);

        $this->assertHtmlEqualsComparison();

    }


    /**
     * Test index method for admin role
     *
     * @return void
     */
    public function testEndpoints()
    {
        $this->loginUser('admin');

        $this->get('permissions/endpoints');
        $this->assertResponseCode(200);

        $this->assertHtmlEqualsComparison();

    }

    /**
     * Test add method for admin role
     *
     * @return void
     */
    public function testAdd()
    {
        $this->loginUser('admin');

        $this->get('permissions/add');
        $this->assertResponseCode(200);

        $this->assertHtmlEqualsComparison();

    }

    /**
     * Test effect of adding a permission
     *
     * Given I am logged in as an author and
     * I access an endpoint where I don't have the permission to access it,
     * then I should not see the endpoint's page but be redirected to the login page
     * (even though I am logged in) which has the effect of logging me out.
     * After the permissions was added, I should see the endpoint's page.
     *
     * //TODO: model level test (are the permission records correctly populated in the database)?
     *
     * @return void
     */
    public function testAddAndDeletePermission()
    {
        // Usually authors can't see the permission page
        $this->loginUser('author');
        $this->expectException(ForbiddenException::class);
        $this->get("/permissions/index");
        $this->assertResponseCode(302);

        // Grant access to the current user with standard settings
        $this->Permissions->allowEndpoint('permissions/index');

        $this->loginUser('author');
        $this->get("/permissions/index");
        $this->assertResponseCode(200);

        // Revoke access
        $this->Permissions->denyEndpoint('permissions/index');

        $this->loginUser('author');
        $this->expectException(ForbiddenException::class);
        $this->get("/permissions/index");
        $this->assertResponseCode(302);
    }

    /**
     * Test a user can access pages that are permitted to the user's role
     *
     * @return void
     */
    public function testAccessByRoleSucceeds()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test a user can access pages that are explicitly permitted to the user
     *
     * @return void
     */
    public function testAccessByIdSucceeds()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }


    /**
     * Test a user cannot access pages where the user's role has no permissions.
     *
     * @return void
     */
    public function testAccessByRoleFails()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test a user cannot access pages where the user has no explicit permission
     *
     * @return void
     */
    public function testAccessByIdFails()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

}
