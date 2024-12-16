<?php


/**
 * Tests the help pages
 *
 * This class is used as a base class for wiki an notes tests.
 * Set the properties in the derived classes and overwrite
 * methods as necessary (e.g. clickMenuItem()).
 *
 */
class HelpCest
{

    /**
     * @var string The segment (help, pages, wiki)
     */
    protected $segment = 'help';

    /**
     * @var int $docCount Total expected number of docs in the segment
     * @var int $publishedDocCount Number of published docs
     */
    protected $docCount = 13;
    protected $publishedDocCount = 12;

    /**
     * @var $docId int ID of the doc that is used for the test
     * @var $menuId int ID of the menu item that opens the test doc
     * @var $menuChildren int Child count of the test menu item
     */
    protected $docId = 472;
    protected $menuId = 5;
    protected $menuChildren = 8;
    protected $category = 'B.+Dateneingabe';
    protected $norm_iri = 'epiweb-editing';

    /**
     * @var string $sortByCaption Caption of the column that is used for the sort test
     * @var string $sortByField Database field of the column that is used for the sort test
     * @var string $§sortByDirection Direction to sort the table (asc|desc)
     */
    protected $sortByCaption = 'Name';
    protected $sortByField = 'name';
    protected $sortByDirection = 'asc';

    protected $viewAction = 'show';

    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * Test the start page
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showStart(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/' . $this->segment);
        $I->wait(0.3);

        $I->dontSeeVisualChanges("body", "body");
    }

    /**
     * Scenario: Click to Table Widget
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function showAll(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/' . $this->segment);

        $I->click('List all');
        $I->seeCurrentUrlEquals('/docs/index/' . $this->segment);
        $I->seeNumberOfElements('tr[data-id]', $this->docCount);
    }

    /**
     * Scenario: Non published docs are hidden for guest users
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function showPublished(AcceptanceTester $I)
    {
        $I->amOnPage('/docs/index/' . $this->segment);
        $I->waitForTheAjaxResponse();
        $I->seeNumberOfElements('tr[data-id]', $this->publishedDocCount);

        $I->login('devel', 'devel');
        $I->amOnPage('/docs/index/' . $this->segment);
        $I->waitForTheAjaxResponse();
        $I->seeNumberOfElements('tr[data-id]', $this->docCount);
    }

    /**
     * Scenario: Expand, collapse and click a menu item
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function clickMenuItem(AcceptanceTester $I)
    {
        if ($this->menuId !== null) {
            $I->login('devel', 'devel');
            $I->amOnPage('/' . $this->segment);

            // Expand Tree
            $I->click('.sidebar-left li[data-id="' . $this->menuId . '"] div.tree-indent-leaf');
            $I->seeNumberOfElements('[data-tree-parent="' . $this->menuId . '"]', $this->menuChildren);

            // Collapse Tree
            $I->click('.sidebar-left li[data-id="' . $this->menuId . '"] div.tree-indent-leaf');
            $I->seeNumberOfElements('[data-tree-parent="' . $this->menuId . '"]', 0);

            // Click Tree Item
            $I->click('.sidebar-left li[data-id="' . $this->menuId . '"]');
            $I->waitForElement('div#content');

            if (!empty($this->norm_iri)) {
                $I->seeCurrentUrlEquals('/' . $this->segment . '/' . $this->norm_iri);
            } else {
                $I->seeCurrentUrlEquals('/' . $this->segment . '?category=' . $this->category);
            }
        }
    }

    /**
     * Scenario: Click on Table
     *
     * @group incomplete
     * @param AcceptanceTester $I
     * @return void
     */
    public function clickRow(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/docs/index/' . $this->segment);

        $I->testOpensPage($this->segment, $this->docId, $this->segment, $this->viewAction);
        $I->seeCurrentUrlEquals('/docs/view/' . $this->segment . '/' . $this->docId);
    }

    /**
     * Scenario: Sort Table Content
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function sortTable(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/docs/index/' . $this->segment);

        $I->testSortTableByColumn(
            $this->sortByCaption,
            $this->sortByField,
            $this->sortByDirection
        );
    }

    /**
     * Scenario: Scroll down and test pagination
     *
     * //TODO: complete test
     *
     * @group incomplete
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function scrollTable(AcceptanceTester $I)
    {
        // Create test records
        $I->performInDatabase('test_epigraf', function ($I) {

            $countBefore = $I->grabNumRecords('docs', ['segment' => $this->segment, 'deleted' => 0]);
            $countTarget = 100;

            for ($i = $countBefore + 1; $i <= $countTarget; $i++) {
                $I->haveInDatabase(
                    'docs',
                    ['segment' => $this->segment, 'deleted' => 0, 'name' => 'Testdoc ' . $i]
                );
            }
        });


        // Open page
        $I->login('devel', 'devel');
        $I->amOnPage('/docs/index/' . $this->segment);
        $I->waitForTheAjaxResponse();
        $I->seeNumberOfElements('tr[data-id]', 25);

        // Scroll down
        //TODO: funktioniert leider alles nicht, es scrollt nicht :(
        $I->scrollTo('tr.item-last[data-id]');
        $I->executeJS('Utils.scrollIntoViewIfNeeded(document.querySelector("tr.item-last[data-id]"))');
        $I->wait(2);
        $I->waitForTheAjaxResponse();

        // Um mal was sichtbar zu machen
        //$I->executeJS('document.querySelector("tr.first-child[data-id]").classList.add("row-selected")');
        //$I->executeJS('document.querySelector("tr.item-last[data-id]").classList.add("row-selected")');

        $I->seeNumberOfElements('tr[data-id]', 50);
    }

    /**
     * Scenario: Edit help page
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editDoc(AcceptanceTester $I)
    {
        // Go to the help page
        $I->login('devel', 'devel');
        $I->amOnPage('/docs/view/' . $this->segment. '/' . $this->docId);

        // Open the editor
        $I->click('Edit');

        // Type in content field
        $contentSelector = '[data-row-field = "content"] .ck-editor .ck-content';
        $I->waitForElementVisible($contentSelector);
        $I->wait(0.5);
        $I->click($contentSelector);
        $I->wait(0.5);

        $I->type('Brand new help');
        $I->dontSeeVisualChanges('edit');

        // Save and see if content shows up
        $I->click('Save');
        $I->waitForTheAjaxResponse();

        if (!empty($this->norm_iri)) {
            $I->seeCurrentUrlEquals('/' . $this->segment . '/' . $this->norm_iri);
        } else {
            $I->seeCurrentUrlEquals('/' . $this->segment . '?category=' . $this->category);
        }

        $I->see('Brand new help');
    }
}


