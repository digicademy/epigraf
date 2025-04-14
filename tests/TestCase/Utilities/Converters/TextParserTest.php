<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Utilities\Converters;

use App\Utilities\Text\TextParser;
use Cake\TestSuite\TestCase;

/**
 * Tests for array manipulation functions
 */
class TextParserTest extends TestCase
{

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
     * Test tokenize
     *
     * @return void
     */
    public function testTokenize() {
        // standard parameters
        // '\', '"', '"', ' '

        // simple list of words
        $term = 'roter gebrannter Ton';
        $this->assertEquals(
            ['roter', 'gebrannter', 'Ton'],
            TextParser::tokenize($term)
        );

        // several groups
        $term = 'roter "gebrannter Ton" eingebettet in feinen\ weißen\ Sand';
        $this->assertEquals(
            ['roter', 'gebrannter Ton', 'eingebettet', 'in', 'feinen weißen Sand'],
            TextParser::tokenize($term)
        );

        // different special characters
        $term = 'roter-(Kies-Ton)-eingebettet- in- feinen\-weißen\-Sand';
        $this->assertEquals(
            ['roter', 'Kies-Ton', 'eingebettet', ' in', ' feinen-weißen-Sand'],
            TextParser::tokenize($term, '\\', '(', ')', '-')
        );


    }

}
