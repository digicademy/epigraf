<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
declare(strict_types=1);

namespace App\Test\Utilities;

use App\Test\Utilities\Constraint\Response\BodyContainsHtml;
use App\Test\Utilities\Constraint\Response\BodyContainsHtmlBody;
use App\Test\Utilities\Constraint\Response\HtmlContainsHtmlElement;

/**
 * Assert method, comparing to HTML snippets
 */
trait CompareHtmlTrait
{

    /**
     * Asserts content exists in the value (ignore whitespace)
     *
     * @param string $content The content to check for.
     * @param string $message The failure message that will be appended to the generated message.
     * @param bool $ignoreCase A flag to check whether we should ignore case or not.
     * @return void
     */
    public function assertContainsHtml(string $expected, string $compare): void
    {
        $this->assertTextContains($expected, $compare);

    }


    /**
     * Asserts content exists in the response body (ignore whitespace)
     * using a text variable
     *
     * @param string $content The content to check
     * @param string $message The failure message that will be appended to the generated message.
     * @param bool $ignoreCase A flag to check whether we should ignore case or not.
     * @return void
     */
    public function assertResponseContainsHtml(string $content, string $message = '', bool $ignoreCase = false): void
    {
        $verboseMessage = $this->extractVerboseMessage($message);
        $this->assertThat($content, new BodyContainsHtml($this->_response, $ignoreCase), $verboseMessage);
    }

    /**
     * Asserts content exists in the response body (ignore whitespace)
     * using a comparison file
     *
     * @param string $expectedFile
     * @param string $message The failure message that will be appended to the generated message.
     * @param bool $ignoreCase A flag to check whether we should ignore case or not.
     * @return void
     */
    public function assertResponseContainsHtmlFile(
        string $expectedFile,
        string $message = '',
        bool $ignoreCase = false
    ): void {
        $verboseMessage = $this->extractVerboseMessage($message);

        static::assertFileExists($expectedFile, $message);
        $content = file_get_contents($expectedFile);

        $this->assertThat($content, new BodyContainsHtml($this->_response, $ignoreCase), $verboseMessage);
    }

    /**
     * Asserts content exists in the response body (ignore whitespace)
     * using an array of HTML snippets
     *
     * @param string $snippets An array of HTML snippets to check for.
     * @param string $message The failure message that will be appended to the generated message.
     * @param bool $ignoreCase A flag to check whether we should ignore case or not.
     * @return void
     */
    public function assertResponseContainsHtmlArray(
        array $snippets,
        string $message = '',
        bool $ignoreCase = false
    ): void {
        $verboseMessage = $this->extractVerboseMessage($message);
        foreach ($snippets as $content) {
            $this->assertThat($content, new BodyContainsHtml($this->_response, $ignoreCase), $verboseMessage);
        }
    }

    /**
     * Asserts the HTML body in a file exists in the HTML body of the response (ignore head tag)
     *
     * @param string $expectedFile
     * @param string $message The failure message that will be appended to the generated message.
     * @param bool $ignoreCase A flag to check whether we should ignore case or not.
     * @return void
     */
    public function assertResponseContainsHtmlBodyFile(
        string $expectedFile,
        string $message = '',
        bool $ignoreCase = false
    ): void {
        $verboseMessage = $this->extractVerboseMessage($message);

        static::assertFileExists($expectedFile, $message);
        $expected = file_get_contents($expectedFile);
        $actual = $this->_response->_getBodyAsString();

        $this->assertThat($expected, new BodyContainsHtmlBody($actual, $ignoreCase), $verboseMessage);
    }

    /**
     * Asserts the HTML body in a file exists in the HTML body of the comparison string(ignore head tag)
     *
     * @param string $expectedFile File name containing HTML
     * @param string $compare HTML string
     * @param string $message The failure message that will be appended to the generated message.
     * @param bool $ignoreCase A flag to check whether we should ignore case or not.
     * @return void
     */
    public function assertHtmlBodyEqualsHtmlFile(
        string $expectedFile,
        string $compare,
        string $message = '',
        bool $ignoreCase = false,
        $replacements = []
    ): void {
        $verboseMessage = $this->extractVerboseMessage($message);

        static::assertFileExists($expectedFile, $message);
        $expected = file_get_contents($expectedFile);
        $expected = $this->cleanComparisonHtml($expected, $replacements);

        $this->assertThat($compare, new BodyContainsHtmlBody($expected, $ignoreCase), $verboseMessage);
    }

    /**
     * Asserts the selected HTML elements exist in the comparison file
     *
     * @param string $expectedFile File name containing HTML
     * @param string $compare HTML string
     * @param string $css A css selector or an XPath expression
     * @param string $message The failure message that will be appended to the generated message.
     * @param bool $ignoreCase A flag to check whether we should ignore case or not.
     * @param array $replacements An array of replacements to clean the HTML content
     * @return void
     */
    public function assertHtmlElementEqualsHtmlFile(
        string $expectedFile,
        string $compare,
        $css = '',
        string $message = '',
        bool $ignoreCase = false,
        $replacements = []
    ): void {
        $verboseMessage = $this->extractVerboseMessage($message);

        static::assertFileExists($expectedFile, $message);
        $expected = file_get_contents($expectedFile);
        $expected = $this->cleanComparisonHtml($expected, $replacements);

        $this->assertThat($compare, new HtmlContainsHtmlElement($expected, $css, $ignoreCase), $verboseMessage);
    }

    /**
     * Clean HTML comparison file
     *
     * @param $html
     * @param $replacements
     *
     * @return array|string|string[]|null
     */
    protected function cleanComparisonHtml($html, $replacements = [])
    {
        $default = [
            '/form name="post_[0-9a-z]+"/' => 'form name="post_000"',
            '/document.post_[0-9a-z]+/' => 'document.post_000',
            '/<input type="hidden" name="_Token[^>]+>/' => '',
            '/[0-9]{2}\.[0-9]{2}\.[0-9]{2}, [0-9]{2}:[0-9]{2}/' => '#TIME#',
            '/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2}, [0-9]{1,2}:[0-9]{1,2}( [AP]M)?/' => '#TIME#',
            '/[rf][rwx-]{9}  (root|www-data)/' => '#PERMISSIONS'
        ];
        $replacements = array_merge($default, $replacements);

        $html = preg_replace(array_keys($replacements), array_values($replacements), $html);

        return $html;
    }

}
