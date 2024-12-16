<?php

/**
 * Tests on the Databanks page
 *
 */

class DatabanksCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * Scenario: Show databases
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showStart(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/databanks');

        $I->waitForTheAjaxResponse();
        $I->wait(3);
        $I->dontSeeVisualChanges("body", "body");
    }

    /**
     * Scenario: Check if row values are wrapped in links when they are hovered
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function hoverRow(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/databanks');
        $I->seeElement('.recordlist');

        // Check if the link wrapper appears
        $I->moveMouseOver('.recordlist > tbody:nth-child(2) > tr:nth-child(1) > td:nth-child(2)');
        $I->seeElement('.recordlist > tbody:nth-child(2) > tr:nth-child(1) > td:nth-child(2) > a:nth-child(1)');
    }

    /**
     * Scenario: click a row in the database table and view the details
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function clickRow(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/databanks');

        $I->testOpensInSidebar('databanks',43);
    }

    /**
     * Scenario: Check if row values are wrapped in links when they are hovered
     *
     * //TODO: Does not work yet
     *
     * @group incomplete
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function dblClickRow(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/databanks');

        // Check if the link wrapper appears
        $I->doubleClick('tr[data-list-itemof="databanks"][data-id="43"] td');
        $I->waitForElement('body.controller_databanks.action_view');
        $I->seeCurrentUrlMatches('~/databanks/view/43$~');
    }

    /**
     * Scenario: Sort Table Content
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function sortbyName(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/databanks');

        $I->testSortTableByColumn(
            'Name',
            'name',
            'desc'
        );
    }

    /**
     * Scenario: Click on Sidebar Buttons.
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function openArticles(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/databanks');

        // Open in the sidebar
        $I->testOpensInSidebar('databanks',43);
        $I->waitForTheAjaxResponse();

        // Show articles
        $I->click('Show articles','.sidebar-right');

        $I->waitForElement('body.controller_articles.action_index');
        $I->seeCurrentUrlMatches('~/epi/projects/articles$~');
    }

}
