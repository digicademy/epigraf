<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\View;

use App\Test\TestCase\AppTestCase;
use App\View\CsvView;
use App\View\JsonldView;
use App\View\RdfView;
use App\View\TtlView;
use App\View\XmlView;

/**
 * Tests for XML serialization
 */
class ApiTest extends AppTestCase
{

    protected array $testData;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->testData = ['articles' => [$this->_generateArticle()]];
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
     * Test csv serialization
     *
     * TODO: Why only types for items, not for sections and articles?
     *
     * @return void
     */
    public function testCsv(): void {

        $view = new CsvView();
        \App\Model\Table\BaseTable::$userRole = 'author';

        $this->assertEquals(
         'id;name;norm_data;internalUrl;type;caption;config;geo;xml;prop' . "\n"
        . 'articles-1;"Article with ""Quotes""";"wd:XXX' ."\n" .'gnd:YYY";/epi/projects/articles/view/1' . "\n"
        . 'sections-1;"Section 1";;;"types-ID epi-section"' . "\n"
        . '"types-ID epi-section";epi-section;;;sections;"ID epi-section";"{""triples"":{""templates"":[{""subject"":""epi:{iri}"",""predicate"":""schema:description"",""object"":""{items.*.xml}""},{""subject"":""epi:{iri}"",""predicate"":""schema:location"",""object"":""{items.*.prop.name}""}]}}"' . "\n"
        . 'items-1;"Item 1";;;"types-ID epi-item";;;"{""lat"":2.1,""lng"":1.2}";"This -&gt; is an <anno id=""a1"">annotated</anno> &lt;- text.";properties-0' . "\n"
        . 'properties-0;"Property 0";"wd:WD1' . "\n" . 'gnd:GND1";;"types-ID epi-property";;;;;' . "\n"
        . '"types-ID epi-property";epi-property;;;properties;"ID epi-property";[];;;' . "\n"
        . '"types-ID epi-item";epi-item;;;items;"ID epi-item";[];;;' . "\n"
        . 'items-2;"Item 2";;;"types-ID epi-item";;;"{""lat"":4.2,""lng"":2.4}";"This -&gt; is an <anno id=""a1"">annotated</anno> &lt;- text.";properties-0' . "\n"
        . 'properties-0;"Property 0";"wd:WD1' . "\n" . 'gnd:GND1";;"types-ID epi-property";;;;;' . "\n"
        . '"types-ID epi-property";epi-property;;;properties;"ID epi-property";[];;;' . "\n"
        . '"types-ID epi-item";epi-item;;;items;"ID epi-item";[];;;' . "\n"
        . 'sections-2;"Section 2";;;"types-ID epi-section";;;;;' . "\n"
        . '"types-ID epi-section";epi-section;;;sections;"ID epi-section";"{""triples"":{""templates"":[{""subject"":""epi:{iri}"",""predicate"":""schema:description"",""object"":""{items.*.xml}""},{""subject"":""epi:{iri}"",""predicate"":""schema:location"",""object"":""{items.*.prop.name}""}]}}";;;' . "\n"
        . 'items-1;"Item 1";;;"types-ID epi-item";;;"{""lat"":2.1,""lng"":1.2}";"This -&gt; is an <anno id=""a1"">annotated</anno> &lt;- text.";properties-0' . "\n"
        . 'properties-0;"Property 0";"wd:WD1' . "\n" . 'gnd:GND1";;"types-ID epi-property";;;;;' . "\n"
        . '"types-ID epi-property";epi-property;;;properties;"ID epi-property";[];;;' . "\n"
        . '"types-ID epi-item";epi-item;;;items;"ID epi-item";[];;;' . "\n"
        . 'items-2;"Item 2";;;"types-ID epi-item";;;"{""lat"":4.2,""lng"":2.4}";"This -&gt; is an <anno id=""a1"">annotated</anno> &lt;- text.";properties-0' . "\n"
        . 'properties-0;"Property 0";"wd:WD1' . "\n" . 'gnd:GND1";;"types-ID epi-property";;;;;' . "\n"
        . '"types-ID epi-property";epi-property;;;properties;"ID epi-property";[];;;' . "\n"
        . '"types-ID epi-item";epi-item;;;items;"ID epi-item";[];;;' . "\n",

        $view->renderToString($this->testData)
        );
    }

    /**
     * Test TTL serialization
     *
     * @return void
     */
    public function testTtl(): void {
        $view = new TtlView();
        $renderedData = $view->renderToString($this->testData);
        $this->assertStringContainsString('sections/geolocation/projects~1', $renderedData);

        $this->assertEquals(
            "@prefix epi: <> .\n"
            . "@prefix schema: <http://schema.org/> .\n\n"
            . "<articles/projects~1>\n"
            . "  schema:title \"Article with \\\"Quotes\\\"\" ;\n"
            . "  schema:about <sections/geolocation/projects~1> ;\n"
            . "  schema:about <sections/geolocation/projects~2> .\n\n"
            . "<sections/geolocation/projects~1>\n"
            . "  schema:description \"This -> is an annotated <- text.\" ;\n"
            . "  schema:description \"This -> is an annotated <- text.\" ;\n"
            . "  schema:location \"Property 0\" ;\n"
            . "  schema:location \"Property 0\" .\n\n"
            . "<sections/geolocation/projects~2>\n"
            . "  schema:description \"This -> is an annotated <- text.\" ;\n"
            . "  schema:description \"This -> is an annotated <- text.\" ;\n"
            . "  schema:location \"Property 0\" ;\n"
            . "  schema:location \"Property 0\" .\n\n",
            $renderedData
        );
    }

    /**
     * Test public TTL serialization
     *
     * @return void
     */
    public function testTtlPublished(): void {

        \App\Model\Table\BaseTable::$userRole = null;
        $publicData = ['articles' => [$this->_generateArticle()]];
        $publicData['articles'][0]['sections'][0]['published'] = 0;
        $publicData['articles'][0]['sections'][1]['items'][0]['published'] = PUBLICATION_DRAFTED;
        $publicData['articles'][0]['sections'][1]['items'][0]['prop']['published'] = PUBLICATION_PUBLISHED;

        $view = new TtlView();
        $renderedData = $view->renderToString($publicData);
        $this->assertStringNotContainsString('sections/geolocation/projects~1', $renderedData);

        $this->assertEquals(
            "@prefix epi: <> .\n"
            . "@prefix schema: <http://schema.org/> .\n\n"
            . "<articles/projects~1>\n"
            . "  schema:title \"Article with \\\"Quotes\\\"\" ;\n"
            . "  schema:about <sections/geolocation/projects~2> .\n\n"
            . "<sections/geolocation/projects~2>\n"
            . "  schema:description \"This -> is an annotated <- text.\" ;\n"
            . "  schema:location \"Property 0\" .\n\n",
            $renderedData
        );
    }

    /**
     * Test TTL hydra collection serialization
     *
     * @return void
     */
    public function testTtlCollection(): void {
        $view = new TtlView();
        $renderedData = $view->renderToString(
            $this->testData, true,
            ['params'=> ['controller' => 'articles', 'action' => 'index']]
        );

        $this->assertEquals(
            "@base <http://localhost/iri/> .\n" .
            "@prefix epi: <http://localhost/iri/> .\n" .
            "@prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .\n" .
            "@prefix hydra: <http://www.w3.org/ns/hydra/core#> .\n" .
            "\n" .
            "\"/\"\n" . // Empty subject because we are not in a request
            "  a hydra:Collection ;\n" .
            "  hydra:totalItems \"\" ;\n" .
            "  hydra:view \"\" ;\n" .
            "  hydra:member <articles/projects~1.ttl> .\n" .
            "\n",
            $renderedData
        );
    }

    /**
     * Test Rdf serialization
     *
     * // TODO: Reduce indent on first level
     * // TODO: Output IRIs as resources
     *
     * @return void
     */
    public function testRdf(): void {
        $view = new RdfView();
        $this->assertEquals(
            "<?xml version='1.0'?>\n"
            . "<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns:epi=\"\" xmlns:schema=\"http://schema.org/\">\n"
            . "  <rdf:Description rdf:about=\"articles/projects~1\">\n"
            . "    <schema:title>Article with \"Quotes\"</schema:title>\n"
            . "    <schema:about rdf:resource=\"sections/geolocation/projects~1\" />\n"
            . "    <schema:about rdf:resource=\"sections/geolocation/projects~2\" />\n"
            . "  </rdf:Description>\n"
            . "  <rdf:Description rdf:about=\"sections/geolocation/projects~1\">\n"
            . "      <schema:description>This -&gt; is an annotated &lt;- text.</schema:description>\n"
            . "      <schema:description>This -&gt; is an annotated &lt;- text.</schema:description>\n"
            . "      <schema:location>Property 0</schema:location>\n"
            . "      <schema:location>Property 0</schema:location>\n"
            . "  </rdf:Description>\n"
            . "  <rdf:Description rdf:about=\"sections/geolocation/projects~2\">\n"
            . "      <schema:description>This -&gt; is an annotated &lt;- text.</schema:description>\n"
            . "      <schema:description>This -&gt; is an annotated &lt;- text.</schema:description>\n"
            . "      <schema:location>Property 0</schema:location>\n"
            . "      <schema:location>Property 0</schema:location>\n"
            . "  </rdf:Description>\n"
            . "</rdf:RDF>"
            ,
            $view->renderToString($this->testData)
        );
    }

    /**
     * Test JSON-LD serialization
     *
     * @return void
     */
    public function testJsonld(): void {
        $view = new JsonLdView();
        $this->assertContentEqualsComparison($view->renderToString($this->testData));
    }

    /**
     * Test output of multiple tags
     *
     * @return void
     */
    public function testDuplicateTags(): void {

        $view = new XmlView();

        // set tag names explicitly
        $testData = [
            'rdf:Description' => [
                ["_xml_tag" => "rdf:about",
                    "a"],
                ["_xml_tag" => "rdf:about",
                    "b"],
                ["_xml_tag" => "rdf:about",
                    "c"]
            ]
        ];

        $expected = "<rdf:Description>"
            . "<rdf:about>a</rdf:about>"
            . "<rdf:about>b</rdf:about>"
            . "<rdf:about>c</rdf:about>"
            . "</rdf:Description>";

        $this->assertEquals(
            $expected,
            $view->renderContent($testData, ['pretty' => false], 0)
        );

        // repeat parent tag for simple array
        $testData = [
            'rdf:Description' => [
                "schema:Description" => "d",
                "rdf:about" => ["a", "b", "c"]
            ]
        ];

        $expected =
            "<rdf:Description>"
            . "<schema:Description>d</schema:Description>"
            . "<rdf:about>a</rdf:about>"
            . "<rdf:about>b</rdf:about>"
            . "<rdf:about>c</rdf:about>"
            . "</rdf:Description>";

        $this->assertEquals(
            $expected,
            $view->renderContent($testData, ['pretty' => false], 0)
        );

        // repeat parent tag for array with arrays
        $testData = [
            'rdf:Description' => [
                "schema:Description" => "d",
                "rdf:about" => [
                    ["name" => "a"],
                    ["name" => "b"],
                    ["name" => "c"]
                ]
            ]
        ];

        $expected = "\n"
            ."  <rdf:Description>\n"
            . "    <schema:Description>d</schema:Description>\n"
            . "    <rdf:about>\n"
            . "      <name>a</name>\n"
            . "      <name>b</name>\n"
            . "      <name>c</name>\n"
            . "    </rdf:about>\n"
            . "  </rdf:Description>";

        $this->assertEquals(
            $expected,
            $view->renderContent($testData, [], 0)
        );


        // repeat parent tag for array with arrays
        $testData = [
            'rdf:Description' => [
                "schema:Description" => "d",
                "rdf:about" => [
                    ["a"],
                    ["b"],
                    ["c"]
                ]
            ]
        ];

        $expected = "<rdf:Description>"
            ."<schema:Description>d</schema:Description>"
            ."<rdf:about>"
            ."abc"
            ."</rdf:about>"
            ."</rdf:Description>";

        $this->assertEquals(
            $expected,
            $view->renderContent($testData, ['pretty' => false], 0)
        );

    }

}
