<?php

/**
 * Tests on the User page
 *
 */
class UsersCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * Check the layout of the user management entry poge
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showStart(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/users');

        $I->waitForTheAjaxResponse();
        $I->waitForText('Other SQL connections');
        $I->waitForText('5 records');

        // Since the active flag is very much dependent on timing (and cache?), we can't test the table appearance
        // TODO: find a way to reliably test the table appearance
        //$I->dontSeeVisualChanges("content", ".content-main");

        $I->dontSeeVisualChanges("footer", "footer");

    }
    /**
     * Scenario: Click on a table row
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showUser(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/users');

        $I->testOpensPage('users',123,'users', 'view');
        $I->seeCurrentUrlMatches('~/users/view/123$~');
    }

    /**
     * Scenario: Sort Table Content
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function sortByUsername(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/users');

        // Wait for the active flag to be stored in the database
//        $I->wait(4);

        $I->testSortTableByColumn(
            'User name',
            'uname',
            'desc'
        );

    }

}
