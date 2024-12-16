<?php

/**
 * Tests on the notes page
 *
 */
class NotesCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * Test the layout of the start page
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showStart(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/notes');

        $I->waitForTheAjaxResponse();
        $I->dontSeeVisualChanges("body", "body");
    }

    /**
     * Scenario: Sort table content
     *
     * @param AcceptanceTester $I
     * @return void
     */

    public function sortByKey(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/notes');

        $I->testSortTableByColumn(
            'IRI-Fragment',
            'norm_iri',
            'asc'
        );
    }

    /**
     * Scenario: Edit a note in the sidebar, save and close it
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @param Snapshot\Notes $snapshot
     * @return void
     */
    public function editInSidebar(AcceptanceTester $I, \Snapshot\Notes $snapshot)
    {
        // Goto the notes list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/notes?selected=1');

        $I->waitForElement('.sidebar-right button.role-edit');
        $I->sendKey('F2',113);

        // Type into a content field (Beschreibung)
        $contentSelector = '.sidebar-right [data-row-field="content"] .ck-content';
        $I->waitForElement($contentSelector);
        $I->focus($contentSelector);
        $I->click($contentSelector);
        $I->wait(0.3);

        $I->pressKey(
            $contentSelector,
            \Facebook\WebDriver\WebDriverKeys::END,
            ' has been extended'
        );

        // Save and compare output
        $I->sendKey('F10',121);
        $I->waitForText('The note has been saved', 20);

        $I->see('Inhalt der Testnotiz has been extended', '.sidebar-right');

        // Compare snapshot
        $snapshot->shouldSaveAsJson(false);
        if ($I->shouldOverwriteSnapshots) {
            $snapshot->shouldRefreshSnapshot(true);
        }
        $snapshot->assert();

        $I->dontSeeVisualChanges('note', '.sidebar-right');
    }

}
