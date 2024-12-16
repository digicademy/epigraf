<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Test\TestCase\Model\Behavior;

use Cake\TestSuite\TestCase;
use Epi\Model\Behavior\XmlStylesBehavior;

/**
 * XmlStylesBehaviorTest Test Case
 */
class XmlStylesBehaviorTest extends TestCase
{
    /**
     * Test subject
     *
     * @var XmlStylesBehavior
     */
    public $XmlEditor;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        //see https://stackoverflow.com/questions/19833495/how-to-mock-a-cakephp-behavior-for-unit-testing
        //$this->XmlEditor = new XmlEditorBehavior();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->XmlEditor);

        parent::tearDown();
    }

}
