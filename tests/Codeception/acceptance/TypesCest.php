<?php


/**
 * Tests on the Types page
 *
 */
class TypesCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function showStart(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/types');

        $I->waitForTheAjaxResponse();
        $I->dontSeeVisualChanges("body", "body");
    }

    /**
     * Scenario: Click a table row and by this show a type record
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showType(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/types');

        $I->testOpensInSidebar('epi_types',146);
        $I->dontSeeVisualChanges('sidebar','.sidebar-right');
    }

    /**
     * Scenario: Edit a type within the sidebar
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editType(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/types');

        // Show type
        $I->testOpensInSidebar('epi_types',895);

        // Open edit form
        $I->click('Edit', '.sidebar-right');

        // Change the name to an invalid value
        $I->waitForElement('.sidebar-right .widget-entity form');
        $I->fillField(['name' => 'name'], 'New name');
        $I->click('Save','.sidebar-right');
        $I->waitForText("The type could not be saved");

        // Use a valid name
        $I->fillField(['name' => 'name'], 'newname');
        $I->click('Save','.sidebar-right');
        $I->waitForText("The type has been saved");
        $I->see("newname",'.sidebar-right');
    }

    /**
     * Scenario: Edit a type with invalid JSON content within the sidebar
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editInvalidJson(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');

        // Open project in sidebar
        $I->amOnPage('/epi/projects/types');
        $I->testOpensInSidebar('epi_types',895);

        // Open edit form
        $I->click('Edit', '.sidebar-right');
        $I->waitForElement('.widget-entity');

        // Change the description to an invalid value
        $I->waitForElement('.sidebar-right .ace_editor');
        $I->focus('.widget-entity [data-row-field=config]');
        $I->pressKey(
            '.widget-entity [data-row-field=config] .ace_text-input',
            'This is not JSON',
        );

        $I->click('Save','.sidebar-right');
        $I->waitForText("The type could not be saved",2);

        $I->dontSeeVisualChanges('sidebar','.sidebar-right');
    }
}

