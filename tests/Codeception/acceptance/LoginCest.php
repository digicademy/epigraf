<?php

/**
 * Login tests
 *
 * This class demonstrates how to write tests using PHP.
 *
 */
class LoginCest
{
    /**
     * before method
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * Scenario: Go to the login page
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function testLoginPage(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Login');
        $I->see('Username');
        $I->see('Password');
    }

    /**
     * Scenario: Use the wrong password
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function testWrongPassword(AcceptanceTester $I)
    {
        $I->amOnPage('/users/login');

        $I->fillField('username', 'devel');
        $I->fillField('password', 'wrongpassword');

        // Provide the #content css selector as context so that it is not
        // mixed up with the login link
        $I->click('Login','#content');
        $I->see('Invalid username or password, try again');
        $I->seeCurrentUrlMatches('~/users/login$~');
    }

    /**
     * Scenario: The devel user can log into the app
     * and see the project databases
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function testDevelLogin(AcceptanceTester $I)
    {
        $I->amOnPage('/users/login');
        $I->fillField('username', 'devel');
        $I->fillField('password', 'devel');
        $I->click('Login','#content');
        $I->waitForText('Logout', 15, '.actions-main');

        $I->seeCurrentUrlMatches('~/databanks$~');
        $I->see('Project databases');
    }

    /**
     * Scenario: User list
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function testUserList(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/users');

        $I->see('User');
        $I->seeElement('table th[data-col=uname]');
    }

}
