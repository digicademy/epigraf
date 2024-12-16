<?php

/**
 * Tests on the Repository page
 *
 */
class RepositoryCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * Check the layout of the repository entry page
     *
     * //TODO: When testing in GitLab, the folder is not found
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function showStart(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/files?root=shared');

        $I->waitForTheAjaxResponse();
        $I->dontSeeVisualChanges("body", "body");
    }

    /**
     * Scenario: Click on folder row
     *
     * @group incomplete
     * @param AcceptanceTester $I
     * @return void
     */
    public function clickFolder(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/files?root=shared');

        // Open folder
        $I->testOpensPage('files',75562,'files', 'index');

        $I->seeElement('tr span.ui-icon.ui-icon-folder-collapsed');
        $I->seeNumberOfElements('tr[data-list-itemof="files"]',2);
        $I->dontSeeVisualChanges('folder');
    }

    /**
     * Scenario: Click on a file
     *
     * @group incomplete
     * @param AcceptanceTester $I
     * @return void
     */
    public function clickFile(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/files?root=shared');

        // Open file
        $I->testOpensInSidebar('files',75564);
        $I->dontSeeVisualChanges('file');
    }

    /**
     * Scenario: Sort files descending by name
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function sortDescByName(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/files?root=shared');

        $I->testSortTableByColumn(
            'Name',
            'name',
            'asc'
        );
    }

}
