<?php

/**
 * Tests on the Projects page
 *
 */
class ProjectsCest
{
    /**
     * before method
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function _before(AcceptanceTester $I)
    {
        $I->testClassName = get_class($this);
    }

    /**
     * Scenario: Show project list
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showStart(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/projects?selected=1');

        $I->waitForElement('.sidebar-right .widget-entity');
        $I->waitForTheAjaxResponse();
        $I->dontSeeVisualChanges("body", "body");
    }

    /**
     * Scenario: Select a project in the table and open it in the sidebar
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showProject(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/projects');

        $I->testOpensInSidebar('epi_projects',1);
    }

    /**
     * Scenario: Sort projects by short name
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function sortByShortname(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/projects');

        $I->testSortTableByColumn(
            'Kurztitel',
            'signature',
            'asc'
        );

    }

    /**
     * Scenario: Select a project and open the articles of the project
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function openArticles(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/projects');

        // Select one row
        $I->testOpensInSidebar('epi_projects',1);

        // Show articles
        $I->click('Show articles', '.sidebar-right');
        $I->waitForElement('body.controller_articles.action_index');
        // TODO: Why %5B0%5D ?
        $I->seeCurrentUrlMatches('~/epi/projects/articles/index\?projects%5B0%5D=1&save=1$~');
    }


    /**
     * Scenario: Edit a type with invalid JSON content within the sidebar
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editInvalidInSidebar(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');

        // Open project in sidebar
        $I->amOnPage('/epi/projects/projects');
        $I->testOpensInSidebar('epi_projects',1);

        // Open edit form
        $I->click('Edit', '.sidebar-right');
        $I->waitForElement('.widget-entity');


        // Change the description to an invalid value
        $I->waitForElement('.sidebar-right .ace_editor');
        $I->focus('.widget-entity [data-row-field=description]');
        $I->pressKey(
            '.widget-entity [data-row-field=description] .ace_text-input',
            'This is not JSON',
        );

        // Change the IRI to an invalid value
        $I->focus('.widget-entity [data-row-field=norm_iri]');
        $I->pressKey(
            '.widget-entity [data-row-field=norm_iri] input',
            'br#?-ken iri',
        );

        $I->click('Save','.sidebar-right');
        $I->waitForText("The project could not be saved",2);
    }

    /**
     * Scenario: Check if x-button title is translated
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function checkTranslation(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');

        // Open project in sidebar
        $I->amOnPage('/epi/projects/projects');
        $I->testOpensInSidebar('epi_projects',1);
        $I->waitForElementVisible('.sidebar-right .frame-title .btn-close');

        // Check if title is correctly translated
        // TODO: Since the locale is english, the should not be translated. Change locale.
        $I->seeElement('.sidebar-right .frame-title .btn-close[title="Schließen"]');

    }

}
