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

use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use App\Test\TestCase\AppTestCase;

/**
 * App\Controller\SettingsController Test Case
 *
 * @uses \App\Controller\SettingsController
 */
class SettingsControllerTest extends AppTestCase
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
     * Test view static pages for admin role
     *
     * Test that admin users can view static pages.
     *
     * @return void
     */
    public function testShowAdmin()
    {
        $this->loginUser('admin');

        $this->get('/settings/show/vars');
        $this->assertResponseOk();
    }

    /**
     * Test that author users can't view static pages.
     *
     * @return void
     */
    public function testShowAuthor()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('You are not authorized to access that location.');

        $this->loginUser('author');
        $this->get('/settings/show/vars');
    }

    /**
     * Test view nonexisting static pages for admin role
     *
     * @return void
     */
    public function testMissingAdmin()
    {
        $this->loginUser('admin');

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Page not found');

        $this->get('/settings/show/nonexistingkey');
    }

    /**
     * Test that unauthenticated users are redirected to the log in page.
     *
     * @return void
     */
    public function testShowNoAuth()
    {
        $this->get('/settings/show/vars');
        $this->assertRedirect([
            'controller' => 'Users', 'action' => 'login',
            '?' => ['redirect' => '/settings/show/vars']
        ]);
    }

    /**
     * Test that missing pages redirect to log in for unauthenticated users.
     *
     * @return void
     */
    public function testShowMissingNoAuth()
    {
        $this->get('/settings/show/nonexistingkey');
        $this->assertRedirect([
            'controller' => 'Users', 'action' => 'login',
            '?' => ['redirect' => '/settings/show/nonexistingkey']
        ]);
    }
}
