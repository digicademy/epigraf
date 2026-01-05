<?php

use Facebook\WebDriver\WebDriverKeys;

/**
 * Tests on the Properties page
 *
 */
class PropertiesCest
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
     * Test the start page layout
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showStart(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/objecttypes?selected=35');

        //$I->waitForTheAjaxResponse();
        $I->waitForElement('.sidebar-right [data-row-table="properties"]');
        $I->waitForTheAjaxResponse();
        $I->wait(0.3);

        $I->dontSeeVisualChanges("body", "body");
    }

    /**
     * Scenario: Click a tree item
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function collapseRows(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/objecttypes');

        // Children are expanded
        $I->seeNumberOfElements('tr[data-tree-parent="36"]', 3);

        // Collapse children
        $I->click('tr[data-id="36"] .tree-indent-leaf');
        $I->seeNumberOfElements('tr[data-tree-parent="36"]', 0);
        $I->dontSeeVisualChanges('collapsed', '.recordlist');

        // Click on tree leaf symbol to expand children again
        $I->click('tr[data-id="36"] .tree-indent-leaf');
        $I->seeNumberOfElements('tr[data-tree-parent="36"]', 3);
        $I->dontSeeVisualChanges('expanded', '.recordlist');
    }

    /**
     * Scenario: Click a table row and, thus, show a property in the sidebar
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showProperty(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/objecttypes');

        $I->testOpensInSidebar('epi_properties', 36);
    }

    /**
     * Scenario: Edit a property in the sidebar
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editProperty(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/objecttypes');

        // Select a property
        $I->testOpensInSidebar('epi_properties', 34);
        $I->dontSee('New value');

        // Bearbeiten
        $I->click('Edit', '.sidebar-right');
        $I->waitForElementVisible('.widget-document-edit' );
        $I->waitForElement('.sidebar-right #form-edit-properties-34');

        $I->fillField('.sidebar-right input[name="lemma"]', 'New value');
        $I->click('Save', '.sidebar-right');
        $I->waitForTheAjaxResponse();

        // Check result in the sidebar
        $I->waitForElementNotVisible('.widget-document-edit' );
        $I->waitForElementVisible('.widget-document');

        $I->see('New value', '.sidebar-right');
        $I->dontSeeVisualChanges('sidebar', '.sidebar-right');

        // Check result in the table
        $I->waitForText('New value', 5, '.recordlist');
        $I->dontSeeVisualChanges('table', '.content-main');
    }

    /**
     * Scenario: Edit an annotation in a property in the sidebar
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editPropertyWithXmlField(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/heraldry');

        // Select a property
        $I->testOpensInSidebar('epi_properties', 44);
        $I->dontSee('hm001.1121', '.doc-fieldname-content');

        // Edit
        $I->click('Edit', '.sidebar-right');
        $I->waitForElementVisible('.widget-document-edit' );
        $I->waitForElement('.sidebar-right #form-edit-properties-44');

        // Add annotation to "Schild"
        $I->focusXmlInput('.sidebar-right .doc-fieldname-content .widget-xmleditor');
        $I->click('.ck-dropdown', '.ck-toolbar__items');
        $I->useToolbutton('Verweis auf Literatur [Alt+Shift+L]', '7');

        $I->click('.button-links-toggle');
        $I->see('Autor 1  (Einheit Autor 1) › Kurztitel Unterautor 2 mit \' (Unterautor2 Einheit)', '.doc-fieldname-content');
        $I->see('Autor 1', '.doc-section-links [data-from-field=content]');

        // Save
        $I->click('Save', '.sidebar-right');
        $I->waitForTheAjaxResponse();

        // Check result in the sidebar
        $I->waitForElementNotVisible('.widget-document-edit' );
        $I->waitForElementVisible('.widget-document');

        $I->click('.button-links-toggle');
        $I->see('Unterautor 2', '.doc-fieldname-content');
        $I->see('Unterautor 2', '.doc-section-links [data-from-field=content]');
        $I->dontSeeVisualChanges('sidebar', '.sidebar-right');
    }

    /**
     * Scenario: Add sibling property
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function addProperty(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/objecttypes');

        // Choose property
        $I->testOpensInSidebar('epi_properties', 36);

        // Check if 2 root properties exist
        $I->dontSee('Testträger');
        $I->seeNumberOfElements('.content-main tr[data-tree-parent=""]', 2);

        // Create new property
        $I->click("Create property");
        $I->waitForElement('.widget-entity form');
        $I->wait(2);
        $I->fillField('.widget-entity form input[name=lemma]', 'Testträger');
        $I->wait(1);
        $I->dontSeeVisualChanges('propertiesform', '.widget-entity form');

        // Save new property
        $I->click("Save", '.ui-dialog-buttonset');
        $I->waitForTheAjaxResponse();
        $I->waitForElementVisible('.recordlist [data-list-name="epi_properties"]');

        // Check if the new property exists
        $I->see('Testträger', '.recordlist [data-id="650"][data-tree-parent=""]');
        $I->seeNumberOfElements('.content-main tr[data-tree-parent=""]', 3);

        // Ensure visual appearance
        $I->wait(1);
        $I->dontSeeVisualChanges('propertieslist', '.recordlist [data-list-name="epi_properties"]');
    }

    /**
     * Scenario: Add a property with an XML field
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function addPropertyWithXmlField(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/heraldry');

        // Create new property
        $I->click("Create property");
        $I->waitForElement('.widget-entity form');
        $I->wait(2);
        $I->fillField('.widget-entity form input[name=name]', 'Mein Wappen');
        $I->wait(1);

        // Focus XML input - without the toolbar showing up because it's a popup
        $contentSelector = '.doc-fieldname-content .widget-xmleditor';
        $I->scrollto($contentSelector);
        $I->focus($contentSelector);
        $I->click($contentSelector);
        $I->wait(1);
        $I->pressKey($contentSelector, 'Mein Schild');
        $I->wait(1);

        // Focus XML input - without the toolbar showing up because it's a popup
        $contentSelector = '.doc-fieldname-elements .widget-xmleditor';
        $I->scrollto($contentSelector);
        $I->focus($contentSelector);
        $I->click($contentSelector);
        $I->wait(1);
        $I->pressKey(
            $contentSelector,
            'Some',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'lines',
            WebDriverKeys::ENTER,
            'to',
            WebDriverKeys::ENTER,
            'move',
            WebDriverKeys::ENTER,
            'modified and created time',
            WebDriverKeys::ENTER,
            'out of view.'
        );
        $I->wait(1);

        // Save new property
        $I->click("Save", '.ui-dialog-buttonset');
        $I->waitForTheAjaxResponse();
        $I->waitForElementVisible('.recordlist [data-list-name="epi_properties"]');

        // Check if the new property exists and has content in the XML field
        $I->testOpensInSidebar('epi_properties', 650);
        $I->see('Mein Wappen', '.sidebar-right');
        $I->see('Mein Schild', '.sidebar-right');

        // Ensure visual appearance
        $I->dontSeeVisualChanges('sidebar', '.sidebar-right');
    }

    /**
     * Scenario: Add child property
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function addChildProperty(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/objecttypes');

        // Choose property
        $I->testOpensInSidebar('epi_properties', 36);

        // Check if 2 root properties exist
        $I->dontSee('Testträger');
        $I->seeNumberOfElements('.content-main tr[data-tree-parent=""]', 2);

        // Create new property
        $I->click("Create property");
        $I->waitForElement('.widget-entity form');
        $I->wait(2);
        $I->selectOption('.widget-entity form select[name=reference_pos]', 'Subnode of ...');
        $I->fillField('.widget-entity form input[name=lemma]', 'Testträger');

        // Save new property
        $I->click("Save", '.ui-dialog-buttonset');
        $I->waitForTheAjaxResponse();
        $I->waitForElementVisible('.recordlist [data-list-name="epi_properties"]');

        // Check if the new property exists
        $I->see('Testträger', '.recordlist [data-id="650"][data-tree-parent="36"]');
        $I->seeNumberOfElements('.content-main tr[data-tree-parent=""]', 2);

        // Ensure visual appearance
        $I->wait(1);
        $I->dontSeeVisualChanges('propertieslist', '.recordlist [data-list-name="epi_properties"]');
    }

    /**
     * Merge two properties
     *
     * @group incomplete
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function mergeProperty(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/objecttypes');

        // Check if recordlist shows up
        $I->seeElement('.recordlist [data-list-name="epi_properties"]');

        // Choose property
        $I->testOpensInSidebar('epi_properties', 36);
    }

    /**
     * Scenario: Search a property
     * - Select a property type in the searchbar
     * - Type into the search field
     * - Wait until the results show up
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function searchProperty(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/objecttypes');
        $I->waitForElementVisible('.recordlist [data-id = "36"]');

        $I->click('.search-term[name=term]');
        $I->fillField('.search-term[name=term]', 'Träger 2');

        $I->waitForElementNotVisible('.recordlist [data-id = "36"]');
        $I->see('Träger 2', '.recordlist [data-id = "35"]');

        // Ensure visual appearance
        $I->dontSeeVisualChanges('propertiestree', '.recordlist [data-list-name="epi_properties"]');
    }

    /**
     * Scenario: Check the move buttons
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function moveProperty(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/objecttypes');

        $I->click('button[data-role="move"]');
        $I->waitForElementVisible('button[data-role="save"].actions-set-move');

        $I->dontSeeVisualChanges('switched', '.page-wrapper > footer');
    }

    /**
     * Scenario: Delete a property
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function deleteProperty(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/properties/index/objecttypes');

        // Select a property that cannot be deleted
        $I->testOpensInSidebar('epi_properties', 34);
        $I->seeElement('.content-main [data-list-name="epi_properties"] [data-id="34"]');

        // Click edit button
        $I->click('Edit', '.sidebar-right');
        $I->waitForElementVisible('.widget-document-edit' );
        $I->waitForElement('.sidebar-right #form-edit-properties-34');

        $I->dontSee('Delete', '.sidebar-right');

        // Select a property that can be deleted
        $I->testOpensInSidebar('epi_properties', 643);
        $I->seeElement('.content-main [data-list-name="epi_properties"] [data-id="643"]');

        // Click edit button
        $I->click('Edit', '.sidebar-right');
        $I->waitForElementVisible('.widget-document-edit' );
        $I->waitForElement('.sidebar-right #form-edit-properties-643');

        // Click delete
        $I->click('Delete', '.sidebar-right');
        $I->waitForElementVisible('.sidebar-right form#form-delete-properties-643' );

        // Confirm
        $I->click('Yes', '.sidebar-right');

        // Wait for result
        $I->waitForTheAjaxResponse();
//        $I->waitForText('The property has been deleted.');

        // Check result in the sidebar
        $I->waitForElementNotVisible('.sidebar-right .widget-document-edit' );
        $I->waitForElementVisible('.sidebar-right .widget-document');
        $I->dontSeeVisualChanges('sidebar', '.sidebar-right');

        // Check result in the table
        $I->click('.sidebar-right .widget-document');

        $I->dontSeeElement('.content-main [data-list-name="epi_properties"] [data-id="643"]');
        $I->waitForElementVisible('.content-main [data-list-name="epi_properties"] .row-focused');
        $I->wait(3);
        $I->dontSeeVisualChanges('table', '.content-main');
    }
}

