<?php

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverKeys;


/**
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */
    public $testClassName = '';

    public $shouldOverwriteSnapshots = false;

    public $shouldOverwriteScreenshots = false;

    /**
     * Log the tester in
     *
     * See https://codeception.com/docs/AcceptanceTests#Common-Cases
     *
     * @param $username
     * @param $password
     * @return void
     */
    public function login($username, $password)
    {
        $I = $this;

        // Skip login if snapshot exists
        if ($I->loadSessionSnapshot('login')) {
            return;
        }

        // Log in
        $I->amOnPage('/users/login');
        $I->fillField('username', $username);
        $I->fillField('password', $password);
        $I->click('Login','#content');
        $I->waitForText('Logout', 15, '.actions-main');

        // Save
        $I->saveSessionSnapshot('login');
    }

    /**
     * Wait until the ajax request started
     *
     * @return void
     */
    public function waitForTheAjaxRequest()
    {
        $this->waitForJS("return App.ajaxQueue.currentRequests.length > 0;", 5);
    }

    /**
     * Wait until all ajax requests are finished
     *
     * @param integer $timeout The timeout in seconds
     * @return void
     */
    public function waitForTheAjaxResponse($timeout=10, $wait=0.3)
    {
        $this->wait($wait);
        $this->waitForJS("return document.readyState == 'complete';", $timeout);
        $this->waitForJS("return 0 === App.ajaxQueue.currentRequests.length;", $timeout);
        $this->waitForElementNotVisible('#loader');
        $this->waitForElementNotVisible('.loader');
    }

    /**
     * Click on an element with the shift key hold down
     *
     * Simulates the event using Javascript.
     *
     * @param $selector
     * @return void
     */
    public function shiftClick($selector)
    {
        $js = 'let element = document.querySelector("' . addslashes($selector) . '");'
            . 'let clickEvent = new MouseEvent("click", {shiftKey: true,bubbles: true,detail:1,cancelable: true});'
            . 'element.dispatchEvent(clickEvent);';

        $this->executeJS($js);
    }

    /**
     * Click on an element with the control key hold down
     *
     * Simulates the event using Javascript.
     *
     * @param $selector
     * @return void
     */
    public function ctrlClick($selector)
    {
        $js = 'let element = document.querySelector("' . addslashes($selector) . '");'
            . 'let clickEvent = new MouseEvent("click", {ctrlKey: true,metaKey: true, bubbles: true,detail:1,view:window,cancelable: true});'
            . 'element.dispatchEvent(clickEvent);';

        $this->executeJS($js);
    }

    /**
     * Press a key by simulating a Javascript event
     *
     * @param string $key The key as character
     * @param integer $keyCode The numeric keycode
     * @return void
     */
    public function sendKey($key, $keyCode)
    {
        $this->executeJS(
            "document.dispatchEvent(new KeyboardEvent('keydown', {'key': '" . $key. "','keyCode': ". $keyCode . "}));"
        );
    }


    public function clearText()
    {
        $I = $this;
        $I->executeInSelenium(function(RemoteWebDriver $webDriver)use($I)
        {
            $action = $webDriver->action();
            $action->keyDown(null, WebDriverKeys::CONTROL)
                ->keyDown(null, 'a')
                ->keyUp(null, 'a')
                ->keyUp(null, WebDriverKeys::CONTROL)
                ->keyUp(null, WebDriverKeys::DELETE)
                ->keyDown(null, WebDriverKeys::DELETE)
                ->perform();
        });
    }

    public function pressCtrlHome()
    {
        $I = $this;
        $I->executeInSelenium(function(RemoteWebDriver $webDriver)use($I)
        {
            $action = $webDriver->action();
            $action->keyDown(null, WebDriverKeys::CONTROL)
                ->sendKeys(null, WebDriverKeys::HOME)
                ->keyUp(null, WebDriverKeys::CONTROL)
                ->perform();
        });
    }

    public function focus($selector)
    {
        $this->executeJS('document.querySelector("' . addslashes($selector) . '").focus();');
    }

    /**
     * Focus XML editor and wait for the toolbar
     *
     * @param string $selector CSS/XPath selector to find the xmleditor widget
     * @param boolean $cursor If true, the cursor will be placed at the beginning of the editor
     * @return void
     */
    public function focusXmlInput($selector, $cursor = false)
    {
        $this->scrollto($selector);
        $this->focus($selector);
        $this->click($selector);

        $this->waitForElementVisible('.content-toolbar.active');
        $this->wait(0.25);

        if ($cursor) {
            $this->pressCtrlHome();
            $this->wait(0.5);
        }
    }

    /**
     * Scroll to an element
     *
     * @param string $selector A CSS selector
     * @return void
     */
    public function scrollIntoView($selector)
    {
        $this->executeJS('document.querySelector("' . addslashes($selector) . '").scrollIntoView();');
    }

    /**
     * Click a toolbutton and select an item in the popup
     *
     * @param string $tooltip The tootip text of the button, e.g. "Buchstabenverbindung [alt+L]"
     * @param string $id If the toolbutton triggers a popup, the item with this ID will be selected, e.g. '66'
     * @return void
     */
    public function useToolbutton($tooltip, $id=false)
    {
        // Click
        $this->click('[data-cke-tooltip-text="' . $tooltip . '"]', '.ck-toolbar__items');
        $this->waitForTheAjaxResponse();

        // Change annotation settings
        if ($id) {
            $this->waitForElementVisible('.ui-dialog');
            $this->waitForElementVisible('[data-id="' . $id . '"]');
            $this->waitForTheAjaxResponse();
            $this->dontSeeElement('.node-cursor');
            $this->click('[data-id="' . $id . '"]');
        }

        $this->waitForElementNotVisible('.ui-dialog');
    }

    /**
     * Click on a column header to sort the table
     * in both directions.
     *
     * @param string $caption Caption to click on
     * @param string $fieldname The fieldname
     * @param string $direction The direction after the first click (asc|desc).
     * @return void
     */
    public function testSortTableByColumn($caption, $fieldname, $direction) {

        $this->click($caption,'.recordlist thead');

        // One direction
        $this->waitForElement(
            '.recordlist th[data-col="' . $fieldname . '"] '
            . 'a.' . $direction
        );

        $this->waitForTheAjaxResponse();
        $this->wait(0.5);
        $this->dontSeeVisualChanges('sorted_by_'.$fieldname.'_'.$direction, '.recordlist');

        // The other direction
        $this->click($caption,'.recordlist thead');
        $direction = $direction === 'asc' ? 'desc' : 'asc';
        $this->waitForElement(
            '.recordlist th[data-col="' . $fieldname . '"] '
            . 'a.' . $direction
        );

        $this->waitForTheAjaxResponse();
        $this->wait(0.5);
        $this->dontSeeVisualChanges('sorted_by_'.$fieldname.'_'.$direction, '.recordlist');
    }

    /**
     * Give a table, clicks a row and asserts
     * the record shows up in the right sidebar
     *
     * @param string $listName Name of the table
     * @param integer $id ID of the item
     * @return void
     */
    public function testOpensInSidebar($listName, $id) {

        $rowSelector = '[data-list-itemof="' . $listName. '"][data-id="' . $id. '"]';

        // Select the row
        $this->click($rowSelector);
        $this->waitForElement( '.row-selected.row-focused'. $rowSelector);

        // Check if the sidebar is expanded
        $this->waitForElementVisible('.sidebar-right .sidebar-content', 10);
        $this->waitForElementVisible('.sidebar-right [data-row-id="' . $id. '"]', 10);
    }

    /**
     * Give a table, clicks a row and asserts
     * a new page opens
     *
     * @param string $listName Name of the table
     * @param integer $id ID of the item
     * @param string $controller The controller name of the new page
     * @param string $action The action name of the new page
     * @return void
     */
    public function testOpensPage($listName, $id, $controller, $action) {

        $rowSelector = 'tr[data-list-itemof="' . $listName. '"][data-id="' . $id. '"]';

        //Click row
        $this->click($rowSelector);
        $this->doubleClick($rowSelector);
        $this->wait(0.5);
        $this->waitForElement('body.controller_' . $controller . '.action_'. $action);
    }

    public function getTestName()
    {
        return $this->testClassName . '.' . $this->getScenario()->current('name');
    }

    public function assertJsonContent($data, $identifier='')
    {
        // Convert data
        if (!is_string($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
        }

        // Generate filename
        $testName = $this->getTestName();
        $identifier = $identifier !== '' ? '.' . $identifier : '';
        $fileName = preg_replace('/\W/', '.', $testName . $identifier) . '.json';
        $fileName = codecept_data_dir('snapshots') . DS . $fileName;

        // Create comparison if not exists
        if (!file_exists($fileName) || $this->shouldOverwriteSnapshots) {
            file_put_contents($fileName, $data);
        }

        // Assert
        $this->assertJsonStringEqualsJsonFile($fileName, $data);
    }

    public function getScreenshotName($identifier = '')
    {
        $testName = $this->getTestName();
        $identifier = !empty($identifier) ? '.' . $identifier : '';
        $fileName = preg_replace('/\W/', '.', $testName . $identifier) . '.png';
        $fileName = codecept_data_dir('references') . DS . $fileName;

        return $fileName;
    }

    /**
     * Compare the reference image with a current screenshot, identified by their identifier name
     * and their element ID.
     *
     * @param string $identifier identifies your test object
     * @param string|null $elementID DOM ID of the element, which should be screenshotted
     * @param string|array $excludeElements string of Element name or array of Element names, which should not appear in the screenshot
     * @param float|null $deviation
     * @see \Codeception\Module\VisualCeption::dontSeeVisualChanges()
     */
    public function dontSeeVisualChanges(string $identifier, ?string $elementID = NULL, array|string $excludeElements = [], ?float $deviation = NULL): void {

        if ($this->shouldOverwriteScreenshots) {
            $filename = $this->getScreenshotName($identifier);
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
        $this->getScenario()->runStep(new \Codeception\Step\Action('dontSeeVisualChanges', func_get_args()));
    }

}
