<?php

use App\Utilities\Converters\Arrays;
use Facebook\WebDriver\WebDriverKeys;

/**
 * Tests on the article list page
 */
class ArticlesCest
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
     * Scenario: Test if the table shows up
     *
     * @group deploy
     * @group test
     * @param AcceptanceTester $I
     * @return void
     */
    public function showStart(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles?selected=1');

        $I->waitForElement('.sidebar-right .doc-article');
        $I->waitForElement('.sidebar-left .widget-tree');
        $I->waitForTheAjaxResponse();

        $I->dontSeeVisualChanges('body', 'body');
    }

    /**
     * Scenario: Select rows in the articles list
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function selectArticles(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');

        $I->click('[data-list-itemof="epi_articles"][data-id="1"]');
        $I->waitForTheAjaxResponse();
        $I->seeNumberOfElements('.row-selected', 1);

        // Check if sidebar is expanded
        $I->waitForElement('.sidebar-right .doc-article[data-row-id="1"]');
        $I->dontSeeVisualChanges('oneRowSelected', 'table.recordlist');

        $I->shiftClick('[data-list-itemof="epi_articles"][data-id="4"]');
        $I->seeNumberOfElements('.row-selected', 3);
        $I->dontSeeVisualChanges('threeRowsSelected', 'table.recordlist');

        //TODO: emulating the ctrl modifier is not working in Firefox
//        $I->ctrlClick('[data-id="3"]');
//        $I->seeNumberOfElements('.row-selected', 2);
//        $I->dontSeeVisualChanges('twoRowsSelected', 'table.recordlist');
    }

    /**
     * Scenario: Click an article and then use the keyboard
     *           - to go to the next and the previous articles
     *           - to select multiple articles using the shift key
     *
     * //TODO
     *
     * @group incomplete
     * @param AcceptanceTester $I
     * @return void
     */
    public function selectArticlesByKeyboard(AcceptanceTester $I)
    {

    }

    /**
     * Scenario: Check and uncheck, expand and collapse tree items
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function selectProperties(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles?properties.objecttypes=');
        $I->waitForTheAjaxResponse();

        // Check Tree Item
        $I->click('li[data-id="36"] label');
        $I->dontSeeVisualChanges('propertyChecked', '.sidebar-left');

        // Uncheck Tree Item
        $I->click('li[data-id="36"] label');
        $I->dontSeeVisualChanges('propertyUnchecked', '.sidebar-left');

        // Collapse Tree
        $I->clickWithLeftButton('li[data-id="35"] div.tree-indent');
        $I->dontSeeElement('li[data-id="32"]');
        $I->dontSeeVisualChanges('propertyCollapsed', ".sidebar-left");

        // Expand Tree
        $I->clickWithLeftButton('li[data-id="35"] div.tree-indent');
        $I->seeElement('li[data-id="32"]');
        $I->dontSeeVisualChanges('propertyUnchecked', ".sidebar-left");
    }

    /**
     * Scenario: Show an additional column in the article table
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function selectColumn(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');
        $I->seeElement('.recordlist [data-list-name="epi_articles"]');
        $I->dontSeeElement('table th [data-col="items_conditions"]');

        // Add table column
        $I->click('button.widget-filter-item');
        $I->click('.selector-columns [data-value="items_conditions"]');

        // Check if column is reloaded
        $I->seeElement('.recordlist [data-list-name="epi_articles"]');
        $I->waitForElementVisible('table th[data-col="items_conditions"]', 20);

        // Check if content shows up
        $I->see('1234', 'tr[data-id="1"]');
        $I->dontSeeVisualChanges('table', '.recordlist [data-list-name="epi_articles"]');
    }

    /**
     * Scenario: Change the size of a column in the articles table.
     *
     * @param AcceptanceTester $I
     * @return void
     */
    public function changeColumnSize(AcceptanceTester $I)
    {

        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');
        $I->seeElement('table th[data-col="name"]');

        $I->dontSeeVisualChanges('before', '.recordlist[data-model="epi.articles"]');

        // Mouse over the resizer, mouse down, move right 50px, mouse up
        $result = $I->executeJS("
            let resizer = document.querySelector('table th[data-col=\"name\"] div.resize-bar');
            if (!resizer) {
                return 'Resizer not found';
            }

            let rect = resizer.getBoundingClientRect();
            let initialX = rect.x + rect.width / 2;
            let initialY = rect.y + rect.height / 2;

            // Move 50px to the right
            let targetX = initialX + 50;

            var mouseDownEvent = new MouseEvent('mousedown', {
                clientX: initialX,
                clientY: initialY,
                pageX: initialX,
                pageY: initialY,
                bubbles: true
            });
            resizer.dispatchEvent(mouseDownEvent);

            var mouseMoveEvent = new MouseEvent('mousemove', {
                clientX: targetX,
                clientY: initialY,
                pageX: targetX,
                pageY: initialY,
                bubbles: true
            });
            document.dispatchEvent(mouseMoveEvent);

            var mouseUpEvent = new MouseEvent('mouseup', {
                clientX: targetX,
                clientY: initialY,
                pageX: targetX,
                pageY: initialY,
                bubbles: true
            });
            document.dispatchEvent(mouseUpEvent);

            return 'ok';
        ");

        $I->wait(1);
        $I->assertEquals('ok', $result);
        $I->dontSeeVisualChanges('after', '.recordlist[data-model="epi.articles"]');

    }

    /**
     * Scenario: Hover a row and check if the link wrapper is inserted
     *
     * At the first place, the table does not contain any links.
     * When hovering a row, the values are wrapped in link tags.
     * This way an article can be opened in a new tab by ctrl+click.
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function hoverRow(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');

        $I->seeElement('tr[data-id="1"]');
        $I->dontSeeElement('tr[data-id="1"] a[data-linkwrapper]');

        $I->moveMouseOver('tr[data-id="1"] > td:nth-child(2)');
        $I->seeElement(
            'tr[data-id="1"] a[data-linkwrapper]',
            ['href' => '/epi/projects/articles/edit/1']
        );

        //TODO: emulating the ctrl modifier is not working in Firefox
//        $I->ctrlClick('tr[data-id="1"] a[data-linkwrapper]');
//        $I->switchToNextTab();
//        $I->waitForElement("body");
//        $I->seeCurrentUrlMatches('~/epi/projects/articles/view/1$~');

    }

    /**
     * Scenario: Sort articles by the title column
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function sortByTitle(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');

        $I->testSortTableByColumn(
            'Titel',
            'name',
            'asc'
        );
    }


    /**
     * Scenario: Search articles by project
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function searchByProject(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');

        $I->fillField('input.widget-filter-facets-term', 'tp');

        $I->waitForText('Testprojekt',5, '.widget-filter-item-projects');

        $I->fillField('input.widget-filter-facets-term', 'nonexistingproject');
        $I->wait(5);
        $I->dontSee('Testprojekt','.widget-filter-item-projects');
    }

    /**
     * Scenario: Search articles by object types
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function searchByObjecttype(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles?properties.objecttypes=');
        $I->waitForTheAjaxResponse();

        $I->seeNumberOfElements('[data-list-itemof="epi_articles"]', 4);
        $I->see("Artikel 2 für das Testsystem");

        // Check Träger 2a (Träger 2a Einheit)
        $I->checkOption("[data-list-itemof='epi_properties_select'][data-id='32']");
        $I->waitForTheAjaxResponse(10,0.7);

        $I->seeNumberOfElements('[data-list-itemof="epi_articles"]', 1);
        $I->see("Artikel 1 für das Testsystem", 'tr[data-id="1"]');
        $I->dontSee("Artikel 2 für das Testsystem");
    }


    /**
     * Scenario: Open the sidebar content in a new tab
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function openNewTab(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');

        // New Tab
        $I->amOnPage('/epi/projects/articles');
        $I->click('[data-list-itemof="epi_articles"][data-id="1"]');
        $I->waitForElement('.sidebar-right .doc-article[data-row-id="1"]', 15);

        $I->click('.btn-open');

        $I->switchToNextTab();
        $I->waitForElement("div#content");
        $I->seeCurrentUrlMatches('~/epi/projects/articles/view/1$~');
    }

    /**
     * Scenario: Close the sidebar
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function closeSidebar(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');

        // Close Sidebar
        $I->amOnPage('/epi/projects/articles?selected=1');

        $I->click('[data-list-itemof="epi_articles"][data-id="1"]');
        $I->waitForElement('.sidebar-right .doc-article[data-row-id="1"]', 15);

        $I->click('.sidebar-right .frame-title .btn-close');
        $I->dontSeeElement('.sidebar-right');
    }

    /**
     * Scenario: Select a single article and export it to JSON.
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function jsonSingleArticle(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');

        $I->click('[data-list-itemof="epi_articles"][data-id="1"]');
        $I->waitForTheAjaxResponse();

        // Check that only one row is selected
        $I->seeNumberOfElements('.row-selected', 1);

        $I->click('footer button[data-toggle=actions-widget-sandwich-items-footer-sandwich-pane]');
        $I->wait(1);
        $I->seeLink('JSON', '/epi/projects/articles.json?articles=1');
        $articleSignature = $I->grabTextFrom('tr.row-selected td:nth-child(2)');
        $articleTitle = $I->grabTextFrom('tr.row-selected td:nth-child(3)');
//        $articleLocations = $I->grabTextFrom('tr.row-selected td:nth-child(4)');

        // Click the link and lookup if the JSON in the new tab contains the title

        $I->click('JSON', '#actions-widget-sandwich-items-footer-sandwich-pane');
//        $I->click('JSON', '.page-wrapper > footer');

        $I->switchToNextTab();
        $I->waitForElement("#rawdata-tab");
        $I->seeCurrentUrlMatches('~/epi/projects/articles.json\?articles=1$~');
        $I->click("#rawdata-tab");

        $I->see('"signature": ' . json_encode($articleSignature));
        $I->see('"name": ' . json_encode($articleTitle));
//        $I->see('"items_locations":' . json_encode($articleLocations));
    }

    /**
     * Scenario: Select two consecutive articles and export them to JSON.
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function jsonMultipleArticles(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');

        $I->click('[data-list-itemof="epi_articles"][data-id="1"]');
        $I->shiftClick('[data-list-itemof="epi_articles"][data-id="4"]');

        $I->waitForTheAjaxResponse();
        $I->seeNumberOfElements('.row-selected', 3);

        $I->click('footer button[data-toggle=actions-widget-sandwich-items-footer-sandwich-pane]');
        $I->wait(1);
        $I->seeLink('JSON', '/epi/projects/articles.json?articles=1%2C3%2C4');

        $selectedTitles = $I->grabMultiple('tr.row-selected td:nth-child(3)');
        $unSelectedTitles = $I->grabMultiple('tr:not(.row-selected) td:nth-child(3)');
        $I->assertCount(3, $selectedTitles);
        $I->assertCount(1, $unSelectedTitles);

        // Click the link and lookup if the JSON in the new tab contains the title
//        $I->click('JSON', '.page-wrapper > footer');
        $I->click('JSON', '#actions-widget-sandwich-items-footer-sandwich-pane');
        $I->switchToNextTab();
        $I->waitForElement("#rawdata-tab");
        $I->seeCurrentUrlMatches('~/epi/projects/articles.json\?articles=1%2C3%2C4$~');
        $I->click("#rawdata-tab");

        foreach ($selectedTitles as $title) {
            $I->seeInSource('"name": ' . json_encode($title));
        }
        foreach ($unSelectedTitles as $title) {
            $I->dontSee('"name": ' . json_encode($title));
        }
    }

    /**
     * Open an article in the sidebar
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function viewArticle(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');

        // Select one row
        $I->testOpensInSidebar('epi_articles', 1);
        $I->waitForTheAjaxResponse();

        // Check visual apperance
        $I->dontSeeVisualChanges('article', '.doc-article');
        $I->dontSeeVisualChanges('body', 'body');
    }

    /**
     * Scenario: See map in view
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function seeMap(AcceptanceTester $I)
    {
        // Login as author
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/view/4');
        $I->waitForTheAjaxResponse();

        // Check if map shows up
        $I->waitForElementVisible('.widget-map');
    }

    /**
     * Scenario: Add a new article from the articles table page
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function addArticle(AcceptanceTester $I)
    {
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');
        $I->waitForTheAjaxResponse();
        $I->dontSeeElement('[data-list-itemof="epi_articles"][data-id="10"]');

        // Add article
        $I->click('Create article');
        $I->waitForElement('.widget-entity form');
//        $I->seeCurrentUrlMatches('~/epi/projects/articles/add$~');

        $I->selectOption('select[name=articletype]', 'epi-article');

        $I->seeInFormFields('.widget-entity form', [
            'articletype' => 'EPI-Artikel',
            'projects_id' => 'Testprojekt 1'
        ]);
        $I->fillField('.widget-entity form input[name=name]', 'My fancy new article');

        // Save
        $I->click('Save', '.ui-dialog-buttonset');

        // Does the new article show up in the table?
        $I->waitForTheAjaxResponse();
        $I->waitForText('My fancy new article',10,'tbody[data-list-name="epi_articles"]');
        $I->seeElement('[data-list-itemof="epi_articles"][data-id="10"]');

        // What about the new tab?
        $I->wait(1);
        $I->switchToNextTab();
        $I->waitForElement('.controller_articles.action_edit');
        $I->seeCurrentUrlMatches('~/epi/projects/articles/edit/10$~');
    }

    /**
     * Scenario: Save article without editing
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function saveAndCloseArticle(AcceptanceTester $I)
    {
        // Goto the articles list
        //TODO: bitte einen author account nehmen
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/view/3');

        // Open the editor
        $I->click('Edit');
        $I->waitForElement('.doc-article.widget-document-edit');
        // Let the transition finish
        $I->wait(0.3);

        // Save without editing
        $I->click('Save');
        $I->waitForText("The article has been saved");
        $I->waitForTheAjaxResponse();

        // Open the editor again
        $I->click('Close');
        $I->waitForTheAjaxResponse();

        $I->click('Edit');
        $I->waitForElement('.doc-article.widget-document-edit');
        // Let the transition finish
        $I->wait(0.3);

        // Check visual apperance
        $I->dontSeeVisualChanges('edit', '.doc-article.widget-document-edit');

    }

    /**
     * Scenario: Save an article in the sidebar without changing the contents
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @param Snapshot\Articles $snapshot
     * @return void
     */
    public function saveAndCloseArticleInSidebar(AcceptanceTester $I, \Snapshot\Articles $snapshot)
    {
        // Get original data
        $snapshot->shouldSaveAsJson(true);
        $dataBefore = $snapshot->fetchData(1,['deleted'=>0]);

        // Go to the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');

        // Select one row
        $I->testOpensInSidebar('epi_articles', 1);

        // Open the editor in the sidebar
        $I->click('Edit', '.sidebar-right');
        $I->waitForTheAjaxResponse();
        $I->waitForElement('.doc-article.widget-document-edit');

        // Focus a content field (Beschreibung)
        $contentSelector = '[data-row-table="items"][data-row-id="4"] .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector);

        // Save and compare output
        $I->click('Save', '.sidebar-right');
        $I->waitForText('The article has been saved', 20);

        // Get new data
        $dataAfter = $snapshot->fetchData(1,['deleted'=>0]);

        // Remove search items
        $dataBefore['items'] = array_filter($dataBefore['items'], fn($row) => $row['itemtype'] !== 'search'); // ITEMTYPE_FULLTEXT
        $dataAfter['items'] = array_filter($dataAfter['items'], fn($row) => $row['itemtype'] !== 'search'); // ITEMTYPE_FULLTEXT

        // Remove value attributes
        $dataBefore['items'] = Arrays::rowsMutate(
            (array)$dataBefore['items'], 'content',
            fn($value) => is_string($value) ? preg_replace('/ value="[^"]*"/','', $value) : $value
        );
        $dataAfter['items'] = Arrays::rowsMutate(
            (array)$dataAfter['items'], 'content',
            fn($value) => is_string($value) ? preg_replace('/ value="[^"]*"/','', $value) : $value
        );

        // Open self-closing tags
        $dataBefore['items'] = Arrays::rowsMutate(
            (array)$dataBefore['items'], 'content',
            fn($value) => is_string($value) ? preg_replace("/<([^ >]+)([^<]*?) \/>/", '<$1$2></$1>', $value) : $value
        );
        $dataAfter['items'] = Arrays::rowsMutate(
            (array)$dataAfter['items'], 'content',
            fn($value) => is_string($value) ? preg_replace("/<([^ >]+)([^<]*?) \/>/", '<$1$2></$1>', $value) : $value
        );

        // Compare rows
        $rowsDiff = Arrays::tablesCompare($dataBefore, $dataAfter);
        $I->assertJsonContent($rowsDiff);
    }

    /**
     * Scenario: Edit an article, save and close it
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @param Snapshot\Articles $snapshot
     * @return void
     */
    public function editAndCloseArticle(AcceptanceTester $I, \Snapshot\Articles $snapshot)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/1');
        $I->waitForElement('.doc-article.widget-document-edit');

        // Type into a content field (Beschreibung)
        $I->click('.sidebar-left .node[data-section-id="sections-5"]');
        $contentSelector = '.doc-section-item[data-row-id="4"] .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector);

        $I->wait(1);
        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            'Fancynewtext'
        );
        $I->wait(0.5);

        // Edit the title
        $contentSelector = '.doc-content input[name="name"]';
        $I->click($contentSelector);

        $I->wait(1);
        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            'Fancynewtitle'
        );
        $I->wait(0.5);

        // Save and compare output
        $I->click('Save', 'nav.actions-bottom');
        $I->waitForText('The article has been saved', 20);
        $I->waitForTheAjaxResponse();

        $I->click('Close', 'footer');
        $I->waitForElement('.controller_articles.action_view');

        // TODO: Scroll to sections-5 (Beschreibung)
        $I->click('.sidebar-left .node[data-section-id="sections-5"]');
        $I->wait(1);
        $I->see('Fancynewtext');
        $I->see('Fancynewtitle');

        // Compare snapshot
        $snapshot->shouldSaveAsJson(false);
        if ($I->shouldOverwriteSnapshots) {
            $snapshot->shouldRefreshSnapshot(true);
        }
        $snapshot->assert();

        $I->dontSeeVisualChanges('article');
    }

    /**
     * Scenario: Edit an article in the sidebar of the article list, save and close it
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @param Snapshot\Articles $snapshot
     * @return void
     */
    public function editAndCloseArticleInSidebar(AcceptanceTester $I, \Snapshot\ArticlesInSidebar $snapshot)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles');

        // Select one row
        $I->testOpensInSidebar('epi_articles', 1);

        // Open the editor in the sidebar
        $I->click('Edit', '.sidebar-right');
        $I->waitForTheAjaxResponse();
        $I->waitForElement('.doc-article.widget-document-edit');

        // Type into a content field (Beschreibung)
        $contentSelector = '.doc-section-item[data-row-id="4"] .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector);

        $I->wait(1);
        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            'Fancynewtext'
        );

        // Because the field size changes, a new item comes into view
        // Depending on where the mouse cursor is, it gets focused or not.
        // Focus in any case to produce the annotations.
        $contentSelector = '.doc-section-item[data-row-id="3"] .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector);
        $I->dontSeeVisualChanges('description', '.sidebar-right');

        // Edit the title
        $contentSelector = '.doc-content input[name="name"]';
        $I->click($contentSelector);
        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            'Fancynewtitle'
        );

        // Save and compare output
        $I->click('Save', '.sidebar-right');
        $I->waitForText('The article has been saved', 20);
        $I->waitForTheAjaxResponse();

        $I->click('Close', '.sidebar-right');
        $I->waitForTheAjaxResponse();
        $I->waitForElement('.sidebar-right .doc-article.widget-document-view');

        $I->waitForText('Fancynewtext',10, '.sidebar-right');
        $I->see('Fancynewtitle', '.sidebar-right');

        // Compare snapshot
        $snapshot->shouldSaveAsJson(false);
        if ($I->shouldOverwriteSnapshots) {
            $snapshot->shouldRefreshSnapshot(true);
        }
        $snapshot->assert();

        $I->dontSeeVisualChanges('article', '.sidebar-right');
    }

    /**
     * Scenario: External references in notes
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function referenceInNote(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/1');

        // Open a note
        $I->waitForElement('.doc-article.widget-document-edit');
        $I->click('.sidebar-right .widget-tabsheets-button[data-tabsheet-id="notes"]');
        $I->click('.sidebar-left .node[data-section-id="sections-17"]');
        $I->waitForElementVisible('.doc-section-note[data-section-id="sections-17"]');

        // Focus the editor
        $contentSelector = '.doc-section-note.active .doc-field-content.widget-xmleditor';
        $I->focusXmlInput($contentSelector);
        $I->wait(1);
        $I->pressKey($contentSelector,WebDriverKeys::HOME);

        // Add external reference
        $I->click('.ck-dropdown__button');
        $I->click('[data-cke-tooltip-text="Externer Verweis [Alt+Shift+O]"]', '.ck-toolbar__items');

        // External reference settings
        $I->waitForElementVisible('.ui-dialog');
        $I->waitForElementVisible('[data-id="2"]');

        $I->waitForElementVisible('.search-term');
        $I->fillField('.search-term', 'Band');
        // TODO: the enter key should not close the dialog when nothing was selected
        // $I->pressKey('.search-term', \Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->wait(1);
        $I->waitForTheAjaxResponse();

        $I->see('Band', '.ui-dialog .widget-table');
        $I->dontSee('Signatur 1', '.ui-dialog .widget-table');

        $I->click('[data-id="2"]', '.ui-dialog [data-list-name="epi_articles_choose"]');
        $I->click('Select', '.ui-dialog');

        // Check if external reference appears and mode remains in edit
        $I->seeCurrentUrlMatches('~/epi/projects/articles/edit/1#sections-17$~');
        $I->see('Band', $contentSelector);

        $I->dontSeeVisualChanges('notes', '.sidebar-right');
    }

    /**
     * Scenario: Edit an article in the sidebar of the article list and save it
     *
     * @param AcceptanceTester $I
     * @param Snapshot\Articles $snapshot
     * @return void
     */
    public function editArticleInSidebar(AcceptanceTester $I, \Snapshot\ArticlesInSidebar $snapshot)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles?mode=preview');

        // Select one row
        $I->testOpensInSidebar('epi_articles', 1);

        // Open the editor in the sidebar
        $I->click('Edit', '.sidebar-right');
        $I->waitForTheAjaxResponse();
        $I->waitForElement('.doc-article.widget-document-edit');

        // Type into a content field (Beschreibung)
        $contentSelector = '.doc-section-item[data-row-id="4"] .doc-fieldname-content .widget-xmleditor';

        $I->focusXmlInput($contentSelector);

        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            'Fancynewtext'
        );

        // Edit the title
        $contentSelector = '.doc-content input[name="name"]';
        $I->click($contentSelector);
        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            'Fancynewtitle'
        );

        // Save and compare output
        $I->click('Save', '.sidebar-right');
        $I->waitForText('Saving document', 5, '.popup-message');
        $I->waitForElementNotVisible('.popup-message', 20);
        $I->seeElement('.doc-article.widget-document-edit');

        // Compare snapshot
        $snapshot->shouldSaveAsJson(false);
        if ($I->shouldOverwriteSnapshots) {
            $snapshot->shouldRefreshSnapshot(true);
        }
        $snapshot->assert();

        $I->dontSeeVisualChanges('article', '.sidebar-right');
    }


    /**
     * Scenario: Edit an article and save it without closing
     *
     * @param AcceptanceTester $I
     * @param Snapshot\Articles $snapshot
     * @return void
     */
    public function editArticle(AcceptanceTester $I, \Snapshot\Articles $snapshot)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/1?mode=preview');

        $I->waitForElement('.doc-article.widget-document-edit');

        // Type into a content field (Beschreibung)
        $contentSelector = '.doc-section-item[data-row-id="4"] .doc-fieldname-content .widget-xmleditor';

        $I->focusXmlInput($contentSelector);

        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            'Fancynewtext'
        );

        // Edit the title
        $contentSelector = '.doc-content input[name="name"]';
        $I->click($contentSelector);
        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            'Fancynewtitle'
        );

        // Save and compare output
        $I->click('Save', 'nav.actions-bottom');
        $I->waitForText('Saving document', 5, '.popup-message');
        $I->waitForElementNotVisible('.popup-message', 20);
        $I->seeElement('.doc-article.widget-document-edit');

        // Compare snapshot
        if ($I->shouldOverwriteSnapshots) {
            $snapshot->shouldRefreshSnapshot(true);
        }
        $snapshot->assert();

        $I->dontSeeVisualChanges('article');
    }

    /**
     * Scenario: Add and edit "Verlust"-Tag
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function addLossTag(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/3');

        // Select content field
        $contentSelector = '[data-row-table="items"][data-row-id="369"] [data-row-field="content"] .widget-xmleditor';
        $I->focusXmlInput($contentSelector);
        $I->pressCtrlHome();

        // Add "Verlust"-Tag
        $I->click('[data-cke-tooltip-text="Verlust [alt+D\]"]', '.content-toolbar');
        $I->waitForElementVisible('.ui-dialog input[name="num_sign"]');
        $I->pressKey(
            'input[name="num_sign"]',
            '5'
        );
        $I->click('Apply','.ui-dialog');
        $I->waitForElementNotVisible('.ui-dialog');

        // Check if Tag is present and has accurate number of signs
        $I->seeElement('.xml_text.xml_tag_del', ['data-attr-num_sign' => '5']);
        $I->dontSeeVisualChanges('tag_5', $contentSelector);

        // Change number of signs
        $I->click('.xml_text.xml_tag_del', $contentSelector);
        $I->waitForElementVisible('.ui-dialog input[name="num_sign"]');
        $I->pressKey(
            'input[name="num_sign"]',
            '0'
        );

        $I->click('Apply','.ui-dialog');
        $I->waitForElementNotVisible('.ui-dialog');

        // Check if Tag is present and has accurate number of signs again
        $I->seeElement('.xml_text.xml_tag_del', ['data-attr-num_sign' => '0']);
        $I->dontSeeVisualChanges('tag_0', $contentSelector);

        // Save
        $I->click('Save');
        $I->waitForText('The article has been saved');
        $I->waitForTheAjaxResponse();

        $I->seeCurrentUrlMatches('~/epi/projects/articles/edit/3#sections-147~');
        $I->seeElement('.xml_text.xml_tag_del', ['data-attr-num_sign' => '0']);
    }

    /**
     * Scenario: Edit an item, add a tag, save and close
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editBlock(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/3');

        // Select content field
        $contentSelector = '[data-row-table="items"][data-row-id="369"] [data-row-field="content"] .widget-xmleditor';
        $I->focusXmlInput($contentSelector, true);

        // Type in content field
        $I->useToolbutton('Bereich [alt+B]', '62');
        $I->click('#doc-section-content-147 .button-links-toggle');
        $I->see('Bereich 1', '.doc-section-links');

        // Save and compare output
        $I->click('Save' );
        $I->waitForText('The article has been saved', 15);
        $I->waitForTheAjaxResponse();

        $I->click('footer button[data-role="cancel"]');
        $I->waitForElement('.controller_articles.action_view');

        $I->click('#doc-section-content-147 .button-links-toggle');
        $I->see('Bereich 1', '.doc-section-links');
        $I->seeElement('[data-row-table="items"][data-row-id="369"] [data-row-field="content"] .xml_bracket.xml_tag_bl');

        // Only the old error should be present, no new errors
        $I->seeNumberOfElements('.art-problems .art-problems-value', 1);
        $I->see("Missing tag rec_lit#000004473044154935185185215539 in field items-369.content.");

        $I->dontSeeVisualChanges('body');
    }

    /**
     * Scenario: Edit an item, add a tag, save and close
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function restructureBlockWithFootnotes(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/1');

        // Select content field of Bearbeitung A.1.1
        $I->click(".sidebar-left [href='#sections-1']");
        $contentSelector = '[data-row-table="items"][data-row-id="1"] [data-row-field="content"] .widget-xmleditor';
        $I->focusXmlInput($contentSelector);
        $I->click('#doc-section-content-1 .button-links-toggle');

        // Remove blocks
//        $I->click("span.xml_bracket[data-tagid='000004436164974262731481588902'] .xml_bracket_open");
//        $I->waitForTheAjaxResponse();
//        $I->waitForElementVisible('.ui-dialog');
//        $I->click('button.role-remove', '.ui-dialog');
//        $I->wait(2);

//        $I->wait(1);
//        $I->click(".sidebar-left [href='#sections-1']");
//        $I->wait(1);

        // Scroll to last block
        $tagSelector = "span.xml_bracket[data-tagid='000004515581058186342592608309'] .xml_bracket_close";
        $I->scrollIntoView($tagSelector);
        $tagSelector = ".doc-section-link[data-from-tagid='000004515581058186342592608309']";
        $I->scrollIntoView($tagSelector);
        $I->wait(1);

        // Remove last block
        $I->click("[data-from-tagid='000004515581058186342592608309']");
        $I->waitForTheAjaxResponse();
        $I->waitForElementVisible('.ui-dialog');
        $I->click('button.role-remove', '.ui-dialog');
        $I->wait(1);

        // Save and compare output
        $I->click('Save' );
        $I->waitForText('The article has been saved', 15);
        $I->waitForTheAjaxResponse();

        $I->dontSeeVisualChanges('body');
    }

    /**
     * Scenario: Edit an item, remove it and save
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editItem(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/3');

        // Add item to "Artikel" section
        $I->click('.doc-item-add.tiny', '[data-row-table="sections"][data-row-id="159"]');
        $I->wait(1);
//        $I->click('[data-row-field="property"]', '[data-row-table="sections"][data-row-id="159"]');

        $I->waitForElementVisible('[data-row-table="sections"][data-row-id="159"] .widget-dropdown-selector.widget-focused.active');
        $I->waitForTheAjaxResponse();

        $I->click('[data-id="6"] .tree-content');
        $I->waitForElementVisible('[data-row-table="sections"][data-row-id="159"] .widget-dropdown-selector.widget-focused:not(.active)');

        $I->seeInField(
            'input[name="sections[159][items][items-int1][newproperty][name]"]',
            'Lemma A  (Lemma-Einheit)'
        );

        // Remove item
        $I->click('.doc-item-remove.tiny', '[data-row-table="sections"][data-row-id="159"]');
        $I->waitForElementVisible('.ui-dialog');
        $I->click('Confirm');

        // Check if item doesn't show up before saving
        $I->waitForElementNotVisible('input[name="sections[159][items][items-int1][newproperty][name]"]');

        // Check if item doesn't show up after saving
        $I->click('Save');
        $I->waitForText("The article has been saved");
        $I->waitForTheAjaxResponse();

        $I->click('Close');
        $I->waitForElement('.controller_articles.action_view');
        $I->dontSee("Lemma A");
    }

    /**
     * Scenario: Add and remove a wordseperator (tests the property selector window)
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editWordseparator(AcceptanceTester $I)
    {
        // Open article for editing
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/3');

        // Focus the field
        $contentSelector = '.doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector);

        // Add word seperator by toolbar
        $I->click('[data-cke-tooltip-text="Worttrenner \[alt+W\]"]', '.ck-toolbar__items');
        $I->waitForTheAjaxResponse();

        // Change word separator settings
        $I->waitForElementVisible('.ui-dialog');
        $I->waitForElementVisible('[data-id="92"]');
        $I->waitForTheAjaxResponse();
        $I->dontSeeElement('.node-cursor');

        $I->dontSeeVisualChanges('dialog', '.ui-dialog');
        $I->click('[data-id="92"]');
        $I->waitForElementNotVisible('.ui-dialog');
        $I->click('#doc-section-content-147 .button-links-toggle');
        $I->see('Worttrenner 1 (Worttrenner 1 Einheit)', '.doc-section-links');

        // Remove word seperator from doc section
        $I->click('.doc-section-link[data-row-type="wtr"]');
        // $I->click('.xml_tag_wtr', '.doc-fieldname-content .widget-xmleditor');
        $I->waitForElementVisible('.ui-dialog');
        $I->waitForTheAjaxResponse();
        $I->click('Remove','.ui-dialog');
        $I->waitForElementNotVisible('.ui-dialog');

        $I->dontSee('#doc-section-content-147 .button-links-toggle');
        $I->dontSee('Worttrenner 1', '.doc-section-links');
    }

    /**
     * Scenario: Add and remove a bracket (tests that partial removes remove the full tag and annotation
     *
     * @group incomplete
     * @param AcceptanceTester $I
     * @return void
     */
    public function addAndRemoveBracket(AcceptanceTester $I)
    {
        // Open article for editing
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/3');

        // Focus field
        $contentSelector = '#doc-section-content-147 .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector);
        $I->pressKey($contentSelector,WebDriverKeys::END);

        // Add bracket
        $I->click('[data-cke-tooltip-text="Abkürzung \[alt+A\]"]', '.ck-toolbar__items');
        $I->waitForElementVisible('#doc-section-content-147 .doc-section-link[data-row-type=abr]');
        $I->dontSeeVisualChanges('withbracket', '#doc-section-content-147');

        // Remove bracket
        // TODO: not working yet
//        $I->executeInSelenium(function(RemoteWebDriver $webDriver) use ($I,$contentSelector)
//        {
//            $action = $webDriver->action();
//            $action->sendKeys(null, WebDriverKeys::BACKSPACE)->perform();
//            $action->sendKeys(null, WebDriverKeys::BACKSPACE)->perform();
//            $action->sendKeys(null, WebDriverKeys::BACKSPACE)->perform();
//        });

        $I->pressKey($contentSelector, WebDriverKeys::BACKSPACE);
        $I->pressKey($contentSelector, WebDriverKeys::BACKSPACE);
        $I->pressKey($contentSelector, WebDriverKeys::BACKSPACE);

        $I->waitForElementNotVisible('#doc-section-content-147 .doc-section-link[data-row-type=abr]');
        $I->dontSeeVisualChanges('withoutbracket', '#doc-section-content-147');
    }


    /**
     * Scenario: Add and remove a link (tests the attribute window)
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editLink(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/3');

        // Type into the "Beschreibung"  content field
        $contentSelector = '[data-row-table="sections"][data-row-id="147"] .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector, true);

        // Add link by toolbar
        $I->click('[data-cke-tooltip-text="Link"]', '.ck-toolbar__items');
        $I->waitForTheAjaxResponse();

        // Link settings
        $I->waitForElementVisible('.ui-dialog input[name="value"]');

        $linkText = 'Example link';
        $linkUrl = 'https://example.org?with=param&ampersands';

        $I->pressKey('.ui-dialog input[name="value"]', $linkText);
        $I->pressKey('.ui-dialog input[name="href"]',$linkUrl);

        $I->dontSeeVisualChanges('dialog', '.ui-dialog');

        // Apply
        $I->click('Apply','.ui-dialog');
        $I->waitForElementNotVisible('.ui-dialog');

        $I->waitForText($linkText, 5, $contentSelector);

        $I->pressKey(
            $contentSelector,
            WebDriverKeys::ENTER
        );

        // Save
        $I->click('Save');
        $I->waitForText("The article has been saved");
        $I->waitForTheAjaxResponse();
        $I->seeCurrentUrlMatches('~/epi/projects/articles/edit/3#sections-147$~');

        // Check if link text shows up
        $I->click('Close', 'footer');
        $I->waitForElement('.controller_articles.action_view');
        $I->seeCurrentUrlMatches('~/epi/projects/articles/view/3#sections-147$~');


        $aSelector = '[data-row-table="sections"][data-row-id="147"] .doc-fieldname-content a[href="' . $linkUrl . '"]';
        $I->seeElement($aSelector);

        $I->click('.sidebar-left .node[data-section-id="sections-147"]');
        $I->wait(0.5);
        $I->dontSeeVisualChanges('content', '[data-row-table="sections"][data-row-id="147"]');
    }

    /**
     * Scenario: Add and remove footnotes in an article
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function addAndRemoveFootnote(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/3');

        // Focus a text field (Beschreibung)
        $contentSelector = '.doc-section-item[data-row-id="369"] .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector);
        $I->pressCtrlHome();

        $I->pressKey(
            $contentSelector,
            WebDriverKeys::END,
//            \Facebook\WebDriver\WebDriverKeys::ENTER,
            'New footnote:'
        );

        // Add footnote
        $I->click('[data-cke-tooltip-text="Numerische Fußnoten \[Alt+Shift+N\]"]', '.ck-toolbar__items');
        $I->waitForElementVisible('.sidebar-right');

        // Edit footnote
        $footnoteSelector = '.doc-footnote[data-row-id="links-int1"] .widget-xmleditor';
        $I->click($footnoteSelector, '.sidebar-right');
        $I->fillField($footnoteSelector, 'fancynewfootnote');

        // Save and compare
        $I->click('Save');
        $I->waitForText("The article has been saved");
        $I->waitForTheAjaxResponse();

        // Check visual apperance (Section Beschreibung)
        $I->click('.sidebar-left .node[data-section-id="sections-147"]');
        $I->wait(0.5);
        $I->dontSeeVisualChanges('sidebar', '.sidebar-right');
        $I->dontSeeVisualChanges('section', '.doc-section[data-row-id="147"]');

        // Remove footnote
         $I->see('fancynewfootnote');
        $I->click('.doc-item-remove.tiny', '.doc-footnote[data-row-id="9"]');
        $I->waitForTheAjaxResponse();
        $I->waitForElementVisible('.ui-dialog');
        $I->click('Confirm');
        $I->waitForTheAjaxResponse();

        // Save and compare
        $I->click('Save');
        $I->waitForText("The article has been saved");
        $I->waitForTheAjaxResponse();

        // Check visual apperance after removing
        $I->click('.sidebar-left .node[data-section-id="sections-147"]');
        $I->wait(0.5);

        $I->dontSeeVisualChanges('sidebar.remove', '.sidebar-right');
        $I->dontSeeVisualChanges('section.remove', '.doc-section[data-row-id="147"]');
    }

    /**
     * Scenario: Add location to article
     *
     * @group incomplete
     *
     * @param AcceptanceTester $I
     * @return void
     */

    public function addLocation(AcceptanceTester $I)
    {
        // Login
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/view/1');

        // Open the editor
        $I->click('Edit');
        $I->waitForTheAjaxResponse();
        $I->waitForElement('.doc-article.widget-document-edit');

        // Add location
        $I->waitForElementVisible('.widget-map');
        // TODO: Scrollen funktioniert nicht
        // $I->scrollTo('[data-row-id="381"]');
        $I->click('Standorte', '.sidebar-left');
        $I->click('.doc-item-add.tiny');
        $I->waitForElementVisible('.doc-section-item.doc-section-item-first');
        $I->click('.doc-field.doc-fieldname-sortno.doc-field-empty');
        $I->fillField('.doc-field.doc-fieldname-sortno', "4");
    }

    /**
     * Scenario: Add notes to section
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editNote(AcceptanceTester $I)
    {
        // Go to the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/1');

        // Add notes to section
        $I->waitForElement('.doc-article.widget-document-edit');
        $I->click('.sidebar-right [data-tabsheet-id="notes"]');
        $I->click('.sidebar-left .node[data-section-id="sections-17"]');
        $I->waitForElement('.doc-section-note[data-section-id="sections-17"]');

        // Fill in note
        $contentSelector = '.doc-section-note.active .doc-field-content.widget-xmleditor';
        $I->focusXmlInput($contentSelector);

        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            'Fancynewnote'
        );

        // Save
        $I->click('Save');
        $I->waitForText("The article has been saved", 20);
        $I->waitForTheAjaxResponse();
        $I->seeCurrentUrlMatches('~/epi/projects/articles/edit/1#sections-17$~');

        // Check
        $I->click('Close');
        $I->waitForElement('.controller_articles.action_view');
        $I->seeCurrentUrlMatches('~/epi/projects/articles/view/1#sections-17$~');
        $I->click('.sidebar-right [data-tabsheet-id="notes"]');
        $I->click('.sidebar-left .node[data-section-id="sections-17"]');
        $I->see("Fancynewnote");
    }

    /**
     * Scenario: Add a new section and insert content
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function addSection(AcceptanceTester $I)
    {
        // Open the article
        $I->login('devel', 'devel');
//        $I->login('author', 'author');
        $I->amOnPage('/epi/projects/articles/edit/3');
        $I->seeNumberOfElements('.art-problems-value', 1);

        // Add section
        $I->waitForElement('.doc-article.widget-document-edit');
        $I->click('.sidebar-left .node[data-section-id="sections-145"]');
        $I->click('.btn-edit-sidebar.doc-section-add', '.sidebar-left');

        $I->waitForTheAjaxResponse();
        $I->waitForText('Inschrift',3,'#sections-selector');
        $I->clickWithLeftButton('#sections-selector li[data-value=inscription]');
        $I->waitForTheAjaxResponse();

        $I->waitForText('Inschrift A');
        $I->waitForText('Inschrift B');

        // Wait for transitions to finish and select the last "Bearbeitung 1"
        $I->wait(1);
        $I->click('.sidebar-left .node:last-child');
        $I->wait(1);
        $I->dontSeeVisualChanges('edit','.sidebar-left');

        // Insert annotated text into the new item
        $contentSelector = '.doc-section-type-inscriptiontext.active .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector);
        $I->wait(2);
        $I->pressKey($contentSelector,'Fancynewtext');
        $I->wait(2);
        $I->pressKey($contentSelector,['ctrl', 'a']);
        $I->useToolbutton('Buchstabenverbindung [alt+L]', '66');
        $I->dontSeeVisualChanges('content', '.doc-article');

        // Save
        $I->click('Save');

        $I->waitForText("The article has been saved");
        $I->waitForText("{Fancynewtext}");

        $I->wait(0.4);
        $I->click("//li[contains(@class, 'node')]/descendant::a[contains(., 'Inschrift B')]/ancestor::li/following-sibling::li[2]");
        $I->wait(0.4);

        $I->dontSeeVisualChanges('transcription', '.doc-article');
        $I->seeNumberOfElements('.art-problems-value', 1);


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
    public function deleteSection(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/1');

        // Remove section "Inschrift B"
        $I->waitForElement('.doc-article.widget-document-edit');

        $I->click('.sidebar-left .node[data-section-id="sections-3"]');

        $I->click('.btn-edit-sidebar.doc-section-remove', '.sidebar-left');
        $I->waitForText('Are you sure you want to delete the selected section?');
        $I->click('Confirm');
        $I->wait(3);
        $I->dontSee('Inschrift B', '.sidebar-left');

        // Save
        $I->click('Save');
        $I->waitForText("The article has been saved", 20);
        $I->waitForTheAjaxResponse();

        $I->click('footer button[data-role="cancel"]');
        $I->waitForElement('.controller_articles.action_view');
        $I->dontSee('Inschrift B', '.sidebar-left');

        // Let the transition finish
        $I->wait(0.5);
        $I->dontSeeVisualChanges('sections','.sidebar-left');
    }

    /**
     * Scenario: Type a value into the dating  field
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function editDateField(AcceptanceTester $I)
    {
        // Go to the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/view/1');

        // Open the editor
        $I->click('Edit');
        $I->waitForTheAjaxResponse();
        $I->waitForElement('.doc-article.widget-document-edit');

        // Go to section "Beschaffenheit"
        $I->click("Beschaffenheit", '.sidebar-left');

        // change date
        $contentSelector = '[data-row-table="items"][data-row-id="8"] [data-row-field="date_value"]';
        $I->click($contentSelector);

        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            '3333'
        );

        // Save
        $I->click('Save');
        $I->waitForText("The article has been saved", 20);
        $I->waitForTheAjaxResponse();

        $I->wait(1);
        $I->click('Close');
        $I->waitForElement('.controller_articles.action_view');
        $I->see("3333", '.doc-header-4');
    }

    /**
     * Scenario: Check if the field size is retained when hovering
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function fieldsize(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/1');

        // Go to section "Maßangaben"
        $I->click("Maßangaben", '.sidebar-left');
        $I->waitForTheAjaxResponse();
        $I->waitForElement( '.doc-section-content#doc-section-content-10');

        // Hover over "Ergänzung"
        $I->moveMouseOver('[data-row-table="items"][data-row-type="measures"] [data-row-field="content"] .widget-xmleditor');

        // Visual regression to verify consistent content field size
        $I->dontSeeVisualChanges('ckcontentfield', '.doc-section-content#doc-section-content-10');

    }

    /**
     * Scenario: Check if line break shows up
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function addLinebreak(AcceptanceTester $I)
    {
        // Goto the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/3');

        // Go to section "Beschreibung"
        $I->click("Beschreibung", '.sidebar-left');
        $I->waitForElement('.doc-section-item[data-row-id="369"] .doc-fieldname-content .widget-xmleditor');

        // Check linebreaks before
        $textbefore = $I->grabTextFrom('.doc-section-item[data-row-id="369"] .doc-fieldname-content .widget-xmleditor');
        $I->seeNumberOfElements('.doc-section-item[data-row-id="369"] br', 6);

        // Type into a content field (Beschreibung)
        $I->click('.sidebar-left .node[data-section-id="sections-147"]');
        $I->wait(0.5);
        $contentSelector = '.doc-section-item[data-row-id="369"] .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector, true);

        $I->wait(0.5);
        $I->pressKey($contentSelector, WebDriverKeys::END);
        $I->wait(0.5);
        $I->pressKey($contentSelector, WebDriverKeys::ENTER);
        $I->wait(0.5);

        // Check linebreaks after
        $I->seeNumberOfElements('.doc-section-item[data-row-id="369"] br', 7);
        $textafter = $I->grabTextFrom('.doc-section-item[data-row-id="369"] .doc-fieldname-content .widget-xmleditor');
//        $I->assertNotEquals($textbefore, $textafter);

        $REGEX_WHITESPACE = '/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/';
        $I->assertEquals(
            preg_replace($REGEX_WHITESPACE, '', $textbefore),
            preg_replace($REGEX_WHITESPACE, '', $textafter),
        );

        // Check visuals
        $I->dontSeeVisualChanges('linebreak', '.doc-section-item[data-row-id="369"]');
    }

    /**
     * Scenario: Add heraldry item
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function addHeraldry(AcceptanceTester $I)
    {
        // Go to the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/1');

        // Go to section "Wappen"
        $I->click("Wappen", '.sidebar-left');
        $I->waitForElementVisible('#doc-section-content-9');

        // Check appearance before editing
        $I->dontSeeVisualChanges('heraldry_before', '#doc-section-content-9');

        // Add new item
        $I->click('Add item', '#doc-section-content-9 .doc-section-groups.doc-section-groups-one.doc-section-headers-multi');

        // Check element in drag and drop and item list
        $I->waitForElementVisible('#doc-section-content-9 .widget-grid [data-row-table="items"][data-row-id="items-int1"]');
        $I->waitForElementVisible('#doc-section-content-9 .doc-section-groups .doc-section-item[data-row-id="items-int1"]');

        // Check right x,y,z coordinates
        $I->seeInField('.doc-section-item[data-row-id="items-int1"] input[data-row-field="pos_x"]', '2');
        $I->seeInField('.doc-section-item[data-row-id="items-int1"] input[data-row-field="pos_y"]', '2');
        $I->seeInField('.doc-section-item[data-row-id="items-int1"] input[data-row-field="pos_z"]', '2');

        // Fill field property
        $I->click('.doc-section-item[data-row-id="items-int1"] .doc-field[data-row-field="property"] .widget-dropdown-selector');
        $I->waitForElementVisible('.doc-section-item[data-row-id="items-int1"] .widget-dropdown-pane.active [data-id="44"]');
        $I->pressKey('.widget-dropdown-selector.active input', WebDriverKeys::ARROW_DOWN, WebDriverKeys::ENTER);
        //$I->click('[data-id="44"] .tree-content');

        // Closing drop down
        $I->waitForElementVisible('#doc-section-content-9 .widget-dropdown-selector.widget-focused:not(.active)');

        // Check results
        $I->seeInField(
            'input[name="sections[9][items][items-int1][newproperty][name]"]',
            'Wappen 1'
        );

        // Fill in field notes
        $contentSelector = '.doc-section-item[data-row-id="items-int1"] .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($contentSelector);
        $I->click($contentSelector);
        $I->fillField($contentSelector, 'new note for new heraldry');

        // Save, close and check entries
        $I->click('Save');
        $I->waitForText('The article has been saved', 20);
        $I->waitForTheAjaxResponse();

        $I->dontSeeVisualChanges('heraldry_after', '.doc-section-content#doc-section-content-9');

    }

    /**
     * Scenario: Edit heraldry item
     *
     * @group incomplete
     * @param AcceptanceTester $I
     * @return void
     */
    public function editHeraldry(AcceptanceTester $I)
    {
        // Go to the articles list
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/1');

        // Go to section "Wappen"
        $I->click("Wappen", '.sidebar-left');
        $I->waitForElementVisible('.doc-section-content#doc-section-content-9');

        // Change grid-size (increasing one column and one row)
        $contentSelector = '.doc-section-grid-size .doc-section-grid-cols[name="sections[9][layout_cols]"]';
        $I->focus($contentSelector);
        $I->click($contentSelector);

        $I->pressKey(
            $contentSelector,
            ['ctrl', 'a'],
            WebDriverKeys::DELETE,
            '1'
        );

        $I->pressKey($contentSelector, WebDriverKeys::ENTER);
        $I->waitForText('Are you sure you want to delete the selected item?');
        $I->click('Confirm');

        $I->waitForElementVisible('.doc-section-grid-table tbody');

        // Visual regression
        $I->dontSeeVisualChanges('heraldry.grid', '.doc-section-content#doc-section-content-9');

        // Change coordinates and check grid inputs

        // Drag and drop heraldry
        // $I->dragAndDrop('');

    }

    /**
     * Scenario: Copy from the description field and paste into another field
     *
     * @group deploy
     * @param AcceptanceTester $I
     * @return void
     */
    public function copyPaste(AcceptanceTester $I)
    {
        // Open article for editing
        $I->login('devel', 'devel');
        $I->amOnPage('/epi/projects/articles/edit/3');

        // Focus the field
        $sourceSelector = '#sections-147 .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($sourceSelector);

        $I->pressKey($sourceSelector, [WebDriverKeys::CONTROL,WebDriverKeys::HOME]);
        $I->pressKey($sourceSelector, [WebDriverKeys::SHIFT, WebDriverKeys::END]);
        $I->pressKey($sourceSelector, [WebDriverKeys::CONTROL,'c']);
        $I->dontSeeVisualChanges('source', '#sections-147');

        $targetSelector = '#sections-146 .doc-fieldname-content .widget-xmleditor';
        $I->focusXmlInput($targetSelector);
        $I->pressKey($targetSelector, ['ctrl','v']);

        $I->dontSeeVisualChanges('target', '#sections-146');
    }

}



