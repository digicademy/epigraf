<?php

/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Utilities\XmlParser;

use App\Test\Utilities\CompareHtmlTrait;
use Cake\TestSuite\TestCase;
use App\Utilities\XmlParser\XmlMunge;

/**
 * XmlParser\XmlMunge Test Case
 */
class XmlMungeTest extends TestCase
{

    use CompareHtmlTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test parseXmlString method
     *
     * @return void
     */
    public function testParseXmlString(): void
    {
        $callback = static function (&$element,&$parser) {
            if ($element['position'] == 'open' ) {
                if (!empty($element['attributes']['id'])) {
                    $element['attributes']['testattribute'] = 'testvalue';
                }
            }
            return true;
        };

        $xml_input = 'Die vielen Bruchstücke lassen <rec id="changeit" />sich zu einem fast vollständigen Gefäß zusammensetzen. Auf der Wandung ist zwischen Blattwerk dreimal ein Pelikan dargestellt, der sich die Brust öffnet und dessen Blutstropfen von drei darunter im Nest sitzenden Jungen aufgenommen werden.';
        $this->assertTextNotContains('testvalue',$xml_input);

        $xml_parsed = XmlMunge::parseXmlString($xml_input,$callback);
        $this->assertContainsHtml('testvalue',$xml_parsed);
    }

}
