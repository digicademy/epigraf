<?php


/**
 * Tests on the Pipeline page
 *
 */
class PipelinesCest
{

    public function _before(AcceptanceTester $I)
    {
    }

    public function showStart(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/pipelines');

        $I->waitForTheAjaxResponse();
        $I->dontSeeVisualChanges("body", "body");
    }

    /**
     * Scenario: Show pipeline
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function showPipeline(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/pipelines');

        $I->testOpensPage('pipelines',19,'pipelines', 'view');
    }

    /**
     * Scenario: Add a new section and insert content
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function addTask(AcceptanceTester $I)
    {
        // Open the article
        $I->login('devel', 'devel');
        $I->amOnPage('/pipelines/edit/19');

        // Add section
        $I->waitForElement('.doc-article.widget-document-edit');
        $I->click('.sidebar-left .node[data-section-id="sections-1"]');
        $I->click('.btn-edit-sidebar.doc-section-add', '.sidebar-left');

        $I->waitForTheAjaxResponse();
        $I->waitForText('Job data',3,'#sections-selector');
        $I->clickWithLeftButton('#sections-selector li[data-value=data_job]');
        $I->waitForTheAjaxResponse();

        $I->waitForText('Job data');
        $I->dontSeeVisualChanges('edit','.sidebar-left');

        // Save
        $I->click('Save');

        $I->waitForElement('.controller_pipelines.action_view');
        $I->waitForText("Job data");

        $I->wait(0.4);
        $I->click("//li[contains(@class, 'node')]/descendant::a[contains(., 'Job data')]");

        // Wait for transitions to finish
        $I->wait(0.4);
        $I->dontSeeVisualChanges('view','.sidebar-left');
    }

    /**
     * Scenario: Add new section
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function deleteTask(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/pipelines/edit/19');

        // Add section
        $I->waitForElement('.doc-article.widget-document-edit');

        $I->click('.sidebar-left .node[data-section-id="sections-1"]');

        $I->click('.btn-edit-sidebar.doc-section-remove', '.sidebar-left');
        $I->waitForText('Are you sure you want to delete the selected section?');
        $I->click('Confirm');
        $I->wait(3);
        $I->dontSee('Options', '.sidebar-left');

        // Save
        $I->click('Save');
        $I->waitForElement('.controller_pipelines.action_view');
        $I->dontSee('Options', '.sidebar-left');

        // Let the transition finish
        $I->wait(0.5);
        $I->dontSeeVisualChanges('sections','.sidebar-left');
    }
}
