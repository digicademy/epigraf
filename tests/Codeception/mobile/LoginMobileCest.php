<?php

/**
 * Login tests for mobile page
 *
 */
class LoginMobileCest
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
     * @param AcceptanceTester $I
     * @return void
     */
    public function testLoginPage(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Login');
        $I->see('Username');
        $I->see('Password');

        $I->dontSeeVisualChanges("body", "body");
    }


}
