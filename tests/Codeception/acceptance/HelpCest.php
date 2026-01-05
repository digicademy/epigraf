<?php


/**
 * Tests the help pages
 *
 */
class HelpCest
{

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
        $I->amOnPage('/help');
        $I->wait(0.3);
        $I->dontSeeVisualChanges("body", "body");
    }


}


