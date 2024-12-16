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

use App\Utilities\Converters\Numbers;
use Cake\TestSuite\TestCase;

/**
 * Numbers Test Case
 */
class NumbersTest extends TestCase
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
     * Test range
     *
     * @return void
     */
    public function testRoman(): void
    {
        $testData = [-1, 0, 1, 2, 4, 5, 9, 10, 49, 50, 100, 101];

        $result = array_map(
            fn($x) => Numbers::numberToRoman($x,true),
            $testData
        );

        $this->assertEquals(
            ['','','i','ii','iv','v','ix','x','xlix','l','c','ci'],
            $result
        );

        $result = array_map(
            fn($x) => Numbers::numberToRoman($x,false),
            $testData
        );

        $this->assertEquals(
            ['','','I','II','IV','V','IX','X','XLIX','L','C','CI'],
            $result
        );

        $result = array_map(
            fn($x) => Numbers::numberToString($x,'roman'),
            $testData
        );

        $this->assertEquals(
            ['','','i','ii','iv','v','ix','x','xlix','l','c','ci'],
            $result
        );
    }

    /**
     * Test range
     *
     * @return void
     */
    public function testLatin(): void
    {
        $testData = [-1, 0, 1,5,26,27,28, 52,53,54];

        $latin = array_map(
            fn($x) => Numbers::numberToLetters($x,true),
            $testData
        );

        $this->assertEquals(
            ['','','a','e','z','aa','ab','az','ba','bb'],
            $latin
        );

        $latin = array_map(
            fn($x) => Numbers::numberToLetters($x,false),
            $testData
        );

        $this->assertEquals(
            ['','','A','E','Z','AA','AB','AZ','BA','BB'],
            $latin
        );

        $latin = array_map(
            fn($x) => Numbers::numberToString($x,'alphabetic'),
            $testData
        );

        $this->assertEquals(
            ['','','a','e','z','aa','ab','az','ba','bb'],
            $latin
        );
    }

    /**
     * Test range
     *
     * @return void
     */
    public function testGreek(): void
    {

        // TODO: What about the additional letter stigma ς (after ρ) ?
        // ["α", "β", "γ", "δ", "ε", "ζ", "η", "θ", "ι", "κ", "λ", "μ", "ν", "ξ", "ο", "π", "ρ", "΢", "σ", "τ", "υ", "φ", "χ", "ψ", "ω"]
        // ['α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ', 'ι', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ', 'ω'];
        // ['Α', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω'];

//        $testData = range(1,25);
//        $result = array_map(fn($x) => Numbers::numberToLetters($x,true,'Α', 25),$testData);

        $testData = [-1, 0, 1,5,24,25,26,27, 50,51,52];

        $result = array_map(
            fn($x) => Numbers::numberToLetters($x,true,'Α', 25),
            $testData
        );

        $this->assertEquals(
            ['','','α','ε','ψ','ω','αα','αβ','αω','βα','ββ'],
            $result
        );

        $result = array_map(
            fn($x) => Numbers::numberToLetters($x,false,'Α', 25),
            $testData
        );

        $this->assertEquals(
            ['','','Α','Ε','Ψ','Ω','ΑΑ','ΑΒ','ΑΩ','ΒΑ','ΒΒ'],
            $result
        );

        $result = array_map(
            fn($x) => Numbers::numberToString($x,'greek'),
            $testData
        );

        $this->assertEquals(
            ['','','α','ε','ψ','ω','αα','αβ','αω','βα','ββ'],
            $result
        );
    }
}
