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
use App\Utilities\Converters\Attributes;
use Cake\Chronos\Chronos;
use Cake\Http\Exception\ForbiddenException;

/**
 * App\Controller\UsersController Test Case
 *
 * @uses \App\Controller\UsersController
 */
class UsersControllerTest extends AppTestCase
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

    /**
     * Test login for admin role
     *
     * @return void
     */
    public function testLoginAdmin()
    {
        $this->post('users/login', ['username' => 'admin', 'password' => 'admin']);
        $this->assertResponseCode(302);
        $this->assertRedirect('users/start');
    }

    /**
     * Test login for devel role
     *
     * @return void
     */
    public function testLoginDevel()
    {
        $this->post('users/login', ['username' => 'devel', 'password' => 'devel']);
        $this->assertResponseCode(302);
        $this->assertRedirect('users/start');
    }

    /**
     * Test login for author role
     *
     * @return void
     */
    public function testLoginAuthor()
    {
        $this->post('users/login', ['username' => 'author', 'password' => 'author']);
        $this->assertResponseCode(302);
        $this->assertRedirect('users/start');
    }


    /**
     * Test the login fails with a wrong password (for author users)
     *
     * @return void
     */
    public function testWrongPasswordAuthor()
    {
        $this->post('users/login', ['username' => 'author', 'password' => 'wrongpassword']);
        $this->assertResponseContains('Invalid username or password, try again');
        $this->assertResponseEqualsComparison();
    }

    /**
     * Test login method after the user was logged in for author role
     *
     * @return void
     */
    public function testRenew()
    {
        $this->loginUser('author');

        $this->get('users/login.json');
        $response = json_decode($this->_getBodyAsString(), true);
        $this->assertEquals(['success' => true], $response);
    }

    /**
     * Test logout method for author role
     *
     * @return void
     */
    public function testLogout()
    {
        $this->loginUser('author');

        $this->get('users/logout');
        $this->assertRedirect('/');
    }

    /**
     * Test start method for admin role
     *
     * @return void
     */
    public function testStartAdmin()
    {
        $this->loginUser('admin');

        $this->get('users/start');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'Epi',
            'database' => 'projects',
            'controller' => 'Articles',
            'action' => 'index'
        ]);
    }

    /**
     * Test start method for devel role
     *
     * @return void
     */
    public function testStartDevel()
    {
        $this->loginUser('devel');

        $this->get('users/start');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'Epi',
            'database' => 'projects',
            'controller' => 'Articles',
            'action' => 'index'
        ]);
    }

    /**
     * Test start method for author role
     *
     * @return void
     */
    public function testStartAuthor()
    {
        $this->loginUser('author');

        $this->get('users/start');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'Epi',
            'database' => 'projects',
            'controller' => 'Articles',
            'action' => 'index'
        ]);
    }

    /**
     * Test no login
     *
     * @return void
     */
    public function testNoLogin()
    {
        $this->get('users/index');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'login',
            '?' => ['redirect' => '/users/index']
        ]);

        $this->get('users/view');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'login',
            '?' => ['redirect' => '/users/view']
        ]);

        $this->get('users/edit/1');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'login',
            '?' => ['redirect' => '/users/edit/1']
        ]);
    }

    /**
     * Test index method for admin role
     *
     * @return void
     */
    public function testIndexAdmin()
    {
        $this->loginUser('admin');

        $this->get('users/index');
        $this->assertResponseCode(200);

        $replacements = ['/<td>[0-9]+<\/td>/' => '<td>#NUMBER</td>'];
        $this->assertHtmlEqualsComparison(true, "table", '', $replacements);

    }

    /**
     * Test index method for devel role
     *
     * @return void
     */
    public function testIndexDevel()
    {
        $this->loginUser('devel');

        $this->get('users/index');
        $this->assertResponseCode(200);

        $replacements = ['/<td>[0-9]+<\/td>/' => '<td>#NUMBER</td>'];
        $this->assertHtmlEqualsComparison(true, "table", '', $replacements);
    }

    /**
     * Test index method for author role
     *
     * @return void
     */
    public function testIndexAuthor()
    {
        $this->loginUser('author');

        $this->expectException(ForbiddenException::class);
        $this->get('users/index');
        $this->assertResponseCode(403);
    }


    /**
     * Test view method for admin role
     *
     * @return void
     */
    public function testViewAdmin()
    {
        $this->loginUser('admin');

        $this->get('users/view/1');
        // Because the SQL users are shared between development and
        // test system, we can't expect the contents to be equal.
        // The view for a new user is tested in the testAddAddmin method.
        $this->assertResponseCode(200);
    }

    /**
     * Test index method for author role
     *
     * @return void
     */
    public function testViewAuthor()
    {
        $this->loginUser('author');

        $this->expectException(ForbiddenException::class);
        $this->get('users/view/2');
        $this->assertResponseCode(403);
    }

    /**
     * Test password method for author role
     *
     * @return void
     */
    public function testPasswordAuthor()
    {
        $this->loginUser('author');

        $this->expectException(ForbiddenException::class);
        $this->get('users/password/101');
    }

    /**
     * Test password method for admin role
     *
     * @return void
     */
    public function testPasswordAdmin()
    {
        $this->loginUser('admin');
        $this->get('users/password/1');
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test changing the password
     *
     * @return void
     */
    public function testPasswordAdminPost()
    {
        $this->loginUser('admin');
        $postData = [
            'password' => 'myextrasafepassword'
        ];
        $this->post('users/password/2', $postData);
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'view',
            2
        ]);
    }

    /**
     * Test grant select method for admin role
     *
     * @return void
     */
    public function testGrantSelect()
    {
        $this->loginUser('admin');

        $this->get('users/grant/1/select');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test grant revoke method for admin role
     *
     * @return void
     */
    public function testGrantRevoke()
    {
        $this->loginUser('admin');

        /** @var \App\Model\Entity\User $user */
        $user = $this->fetchTable('Users')
            ->find('all')
            ->where(['username' => 'author'])
            ->contain('Databanks')
            ->first();

        $grants = $user->databank->grants;
        $this->assertEquals(false, in_array('epi_author', $grants));

        $this->post('users/grant/1');
        $grants = $user->databank->grants;
        $this->assertEquals(true, in_array('epi_author', $grants));

        $this->delete('users/revoke/1');
        $grants = $user->databank->grants;
        $this->assertEquals(false, in_array('epi_author', $grants));
    }

    /**
     * Test add user for admin role
     *
     * @return void
     */
    public function testAddAdmin()
    {
        Attributes::fixSeed();

        $user = $this->fetchTable('Users')
            ->find('all')
            ->where(['id' => 6])
            ->count();
        $this->assertEquals(0, $user);

        // Get the add page
        $this->loginUser('admin');

        $this->get('users/add');
        $this->assertResponseCode(200);

        $replacements = ['/id="password" aria-required="true" value="[^"]*"/' => 'id="password" aria-required="true" value="MYPASSWORD"'];
        $this->assertHtmlEqualsComparison(true, ".content-wrapper", '.1_get', $replacements);

        // Post new data
        $data = [
            'role' => 'author',
            'username' => 'Test',
            // We leave password blank and expect it will be generated automatically
            // i.e. no empty passwords occur
            //'password' => 'Test',
            'accesstoken' => 'TESTTOKENAUTHOR'
        ];
        $this->post('users/add', $data);
        // Redirect to the new user's page
        $this->assertRedirect('users/view/6');
        $this->assertResponseCode(302);

        // Get the new user's page
        $this->get('users/view/6');
        $this->assertResponseCode(200);

        $this->assertHtmlEqualsComparison(true, ".content-wrapper", '.2_get');

        // Is the user in the database? We hope so!
        $userid = 6;
        $user = $this->fetchTable('Users')
            ->get($userid)
            ->setHidden([])
            ->toArray();

        // Convert DateTime objects and calculate password length
        // The actual password differs from run to run and, thus,
        // can't be compared (why though?).
        $user['created'] = $user['created']->toDateTimeString();
        $user['modified'] = $user['modified']->toDateTimeString();
        $user['password'] = strlen($user['password']);

        $expected = [
            'role' => 'author',
            'username' => 'Test',
            'password' => 60,
            'accesstoken' => 'TESTTOKENAUTHOR',
            'id' => $userid,
            'created' => Chronos::getTestNow()->toDateTimeString(),
            'modified' => Chronos::getTestNow()->toDateTimeString(),
            'lastaction' => null,
            'databank_id' => null,
            'pipeline_article_id' => null,
            'pipeline_book_id' => null,
            'contact' => null,
            'settings' => null,
            'norm_iri' => 'test',
            'created_by' => 2,
            'modified_by' => 2,
            'email' => null,
            'name' => null,
            'acronym' => null
        ];
        $this->assertEquals($expected, $user);
    }

    /**
     * Test add user for author role
     *
     * @return void
     */
    public function testAddAuthor()
    {
        $this->loginUser('author');
        $this->expectException(ForbiddenException::class);

        $this->get('users/add');
        $this->assertResponseCode(403);
    }

    /**
     * Test edit user for admin role
     *
     * @return void
     */
    public function testEditAdmin()
    {
        $this->loginUser('admin');

        // Show edit form
        $this->get('users/edit/1');
        $this->assertResponseCode(200);

        $this->assertHtmlEqualsComparison(true, ".content-wrapper", '.user-edit');

        // Post new data
        $data = [
            'role' => 'author',
            'username' => 'test',
            'password' => 'test'
        ];
        $this->post('/users/edit/1', $data);
        $this->assertResponseCode(302);
        $this->assertRedirect('users/view/1');

        // User page
        $this->get('users/view/1');
        $this->assertHtmlEqualsComparison(true, ".content-wrapper", '.user-page');

        // User is in database?
        $userid = 1;
        $user = $this->fetchTable('Users')
            ->get($userid)
            ->setHidden([])
            ->toArray();

        // Convert DateTime objects
        // The actual password differs from run to run and, thus,
        // can't be compared (why though?).
        $user['lastaction'] = $user['lastaction']->toDateTimeString();
        $user['created'] = $user['created']->toDateTimeString();
        $user['modified'] = $user['modified']->toDateTimeString();
        $user['password'] = strlen($user['password']);

        $expected = [
            'role' => 'author',
            'username' => 'test',
            'password' => 60,
            'accesstoken' => 'TESTTOKENAUTHOR',
            'id' => $userid,
            'created' => '2022-04-21 11:34:15',
            'modified' => Chronos::getTestNow()->toDateTimeString(),
            'lastaction' => '2022-04-30 11:12:35',
            'databank_id' => 1,
            'pipeline_article_id' => 19,
            'pipeline_book_id' => 21,
            'contact' => '',
            'settings' => null,
            'norm_iri' => 'author',
            'created_by' => null,
            'modified_by' => 2,
            'email' => null,
            'name' => null,
            'acronym' => null
        ];
        $this->assertEquals($expected, $user);
    }

    /**
     * Test edit user for author role
     *
     * @return void
     */
    public function testEditAuthor()
    {
        $this->loginUser('author');
        $this->expectException(ForbiddenException::class);

        $this->get('users/edit/2');
        $this->assertResponseCode(403);
    }

    /**
     * Test edit action for the own user record
     *
     * Allow changing the contact data, but not the username or role
     *
     * @return void
     */
    public function testEditSelf()
    {
        $this->loginUser('author');
        $userid = 1;

        // Show edit form
        $this->get('users/edit/' . $userid);
        $this->assertResponseCode(200);

        $this->assertHtmlEqualsComparison(true, ".content-wrapper", '.user-edit');

        // Post new data
        $data = [
            'role' => 'admin',
            'username' => 'notallowed',
            'accesstoken' => 'notallowed',
            'contact' => 'New contact information',
            'password' => 'test'
        ];
        $this->post('/users/edit/' . $userid, $data);
        $this->assertResponseCode(302);
        $this->assertRedirect('users/view/1');

        // User page
        $this->get('users/view/' . $userid);
        $this->assertHtmlEqualsComparison(true, ".content-wrapper", '.user-page');

        // User is in database?
        $user = $this->fetchTable('Users')
            ->get($userid)
            ->setHidden([])
            ->toArray();

        // Convert DateTime objects
        // The actual password differs from run to run and, thus,
        // can't be compared (why though?).
        $user['lastaction'] = $user['lastaction']->toDateTimeString();
        $user['created'] = $user['created']->toDateTimeString();
        $user['modified'] = $user['modified']->toDateTimeString();
        $user['password'] = strlen($user['password']);

        $expected = [
            'role' => 'author',
            'username' => 'author',
            'password' => 60,
            'accesstoken' => 'TESTTOKENAUTHOR',
            'id' => $userid,
            'created' => '2022-04-21 11:34:15',
            'modified' => Chronos::getTestNow()->toDateTimeString(),
            'lastaction' => '2020-10-29 13:00:58',
            'databank_id' => 1,
            'pipeline_article_id' => 19,
            'pipeline_book_id' => 21,
            'contact' => 'New contact information',
            'settings' => null,
            'norm_iri' => 'author',
            'created_by' => null,
            'modified_by' => $userid,
            'email' => null,
            'name' => null,
            'acronym' => null
        ];
        $this->assertEquals($expected, $user);
    }

    /**
     * Test delete user for admin role
     *
     * @return void
     */
    public function testDeleteAdmin()
    {
        $this->loginUser('admin');

        // User is in database?
        $user = $this->fetchTable('Users')
            ->find('all')
            ->where(['id' => 1])
            ->count();
        $this->assertEquals(1, $user);

        // A get request shows the confirmation page
        $this->get('users/delete/1');
        $this->assertHtmlEqualsComparison();

        // A post request deletes the user
        $this->post('users/delete/1');
        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison('after', '.content-wrapper',);


        // User still in database? Hopefully not!
        $user = $this->fetchTable('Users')
            ->find('all')
            ->where(['id' => 1])
            ->count();
        $this->assertEquals(0, $user);
    }

    /**
     * Test delete user for author role
     *
     * @return void
     */
    public function testDeleteAuthor()
    {
        $this->loginUser('author');
        $this->expectException(ForbiddenException::class);

        $this->get('users/delete/1');
        $this->assertResponseCode(403);
    }

    /**
     * Test delete user for author role
     *
     * @return void
     */
    public function testPostSettingsAuthor()
    {
        $this->loginUser('author');

        $postData = [
            'signature' => 200
        ];
        $this->patch('users/settings/columns/epi.articles.json', $postData);
        $this->assertJsonResponseEqualsComparison();
    }
}
