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

use App\Test\TestCase\AppTestCase;
use App\Utilities\Converters\Objects;

/**
 * Tests for extraction keys
 */
class ObjectsTest extends AppTestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        \App\Model\Table\BaseTable::$userRole = 'author';
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
     * Test parseFieldKey
     *
     * @return void
     */
    public function testParseFieldKey(): void
    {
        $this->assertEquals(
            [
                'name' => 'links',
                'caption' => 'links',
                'key' => 'links.*',
                'aggregate' => 'count'
            ],
            Objects::parseFieldKey('links=links.*|count')
        );

        $this->assertEquals(
            [
                'name' => 'items.*[itemtype="geolocations"]',
                'caption' => 'items.*[itemtype="geolocations"]',
                'key' => 'items.*[itemtype="geolocations"]',
                'aggregate' => false
            ],
            Objects::parseFieldKey('items.*[itemtype="geolocations"]')
        );

        $this->assertEquals(
            [
                'name' => 'Geo',
                'caption' => 'Geo',
                'key' => 'items.*[itemtype="geolocations"]',
                'aggregate' => 'first'
            ],
            Objects::parseFieldKey('Geo=items.*[itemtype="geolocations"]|first')
        );
    }

    /**
     * Test parsePlaceholder
     *
     * //TODO: test unequal array lengths
     *
     * @return void
     */
    public function testParsePlaceholder(): void
    {
        $testData = [
            'projects' => [
                ['name' => 'Superduper', 'status' => 'finished'],
                ['name' => 'Megafail', 'status' => ['first finished', 'then failed']]
            ],
            'status' => 'finished'
        ];

        // Without callback, returns a token array
        $this->assertEquals(
            [
                ['value' => 'Project ', 'type' => 'literal'],
                ['value' => 'projects.0.name', 'type' => 'path'],
                ['value' => ' is awesome', 'type' => 'literal']
            ],
            Objects::parsePlaceholder('Project {projects.0.name} is awesome')
        );

        $this->assertEquals(
            ['Project Superduper is awesome'],
            Objects::parsePlaceholder(
                'Project {projects.0.name} is awesome',
                fn($token) => Objects::extract($testData, $token)
            )
        );

        $this->assertEquals(
            [
                'Project Superduper is awesome',
                'Project Megafail is awesome'
            ],
            Objects::parsePlaceholder(
                'Project {projects.*.name} is awesome',
                fn($token) => Objects::extract($testData, $token)
            )
        );

        // Multiple items and one item
        $this->assertEquals(
            [
                'Project Superduper is finished',
                'Project Megafail is finished'
            ],
            Objects::parsePlaceholder(
                'Project {projects.*.name} is {status}',
                fn($token) => Objects::extract($testData, $token)
            )
        );

        // Multiple items and multiple items (with a nested array encoded as JSON)
        $this->assertEquals(
            [
                'Project Superduper is finished',
                'Project Megafail is ["first finished","then failed"]'
            ],
            Objects::parsePlaceholder(
                'Project {projects.*.name} is {projects.*.status}',
                fn($token) => Objects::extract($testData, $token)
            )
        );

    }


    /**
     * Test nested placeholders
     *
     * @return void
     */
    public function testNestedPlaceholders(): void
    {
        $testData = [
            'sections' => [
                [
                    'name' => 'Index 1',
                    'sectiontype' => 'index',
                    'items' => [
                        ['itemtype' => 'subjects', 'property' => 'Property 1'],
                        ['itemtype' => 'subjects', 'property' => 'Property 2'],
                        ['itemtype' => 'nosubjects', 'property' => 'Property 3']
                    ]
                ],
                [
                    'name' => 'Index 2',
                    'sectiontype' => 'index',
                    'items' => [
                        ['itemtype' => 'subjects', 'property' => 'Property 4'],
                        ['itemtype' => 'subjects', 'property' => 'Property 5'],
                        ['itemtype' => 'nosubjects', 'property' => 'Property 6']
                    ]
                ],
                [
                    'name' => 'Index 3',
                    'sectiontype' => 'index',
                    'items' => [
                        ['itemtype' => 'nosubjects', 'property' => 'Property 7'],
                    ]
                ],
                [
                    'name' => 'Noindex',
                    'sectiontype' => 'noindex',
                    'items' => [
                        ['itemtype' => 'subjects', 'property' => 'Property 8'],
                        ['itemtype' => 'subjects', 'property' => 'Property 9'],
                        ['itemtype' => 'nosubjects', 'property' => 'Property 10']
                    ]
                ],
            ]
        ];

        $placeholder = "{sections.*[sectiontype=index].items.*[itemtype=subjects].property}";


        $this->assertEquals(
            [
                'Property 1',
                'Property 2',
                'Property 4',
                'Property 5'
            ],
            Objects::parsePlaceholder($placeholder, fn($token) => Objects::extract($testData, $token))
        );
    }

    /**
     * Test processValues
     *
     * @return void
     */
    public function testProcessValues(): void
    {
        $testData = ['c', 'b', 'a', 'e', 'd <a href="http://example.com">link</a>'];

        // Aggregating
        $this->assertEquals(
            'c',
            Objects::processValues($testData, ['first'])
        );

        $this->assertEquals(
            'a',
            Objects::processValues($testData, ['min'])
        );

        $this->assertEquals(
            'e',
            Objects::processValues($testData, ['max'])
        );

        $this->assertEquals(
            implode(', ', $testData),
            Objects::processValues($testData, ['collapse'])
        );

        $this->assertEquals(
            5,
            Objects::processValues($testData, ['count'])
        );

        // Stripping
        $this->assertEquals(
            'd link',
            Objects::processValues('d <a href="http://example.com">link</a>', ['strip'])
        );

        $this->assertEquals(
            ['c', 'b', 'a', 'e', 'd link'],
            Objects::processValues($testData, ['strip'])
        );

        $this->assertEquals(
            'c, b, a, e, d link',
            Objects::processValues($testData, ['collapse', 'strip'], false)
        );

        $this->assertEquals(
            $testData,
            Objects::processValues($testData, ['strip'], false)
        );
    }

    /**
     * Test extraction keys
     *
     * Extraction keys are used in the following configurations:
     * fields, columns, header, preview tiles, triples
     *
     * Example database fields
     * published, signature, created, created_by...
     *
     * Example nested database columns (article entities):
     * project.signature, creator.name, project.type.caption
     * items.{*}[itemtype=topics].property.lemma (aggregate: collapse)
     * sections.{n}.items.{n}[itemtype=dio-locations-raw].content,
     * project.project.full_name,
     * items.{*}[itemtype=images] (aggregate: count)
     *
     * Virtual fields (project entity):
     * iri_path, articles, published_label
     *
     * Virtual fields (article summary):
     * project_signature, url
     *
     * @return void
     */
    public function testExtract(): void
    {
        $testArticle = $this->_generateArticle();

        // Simple value
        $this->assertEquals(
            ['Article with "Quotes"'],
            Objects::extract($testArticle, 'name')
        );

        $this->assertEquals(
            'Article with "Quotes"',
            Objects::extract($testArticle, 'name', false)
        );

        // Array
        $this->assertEquals(
            ['Section 1', 'Section 2'],
            Objects::extract($testArticle, 'sections.*.name')
        );

        $this->assertEquals(
            ['Section 1', 'Section 2'],
            Objects::extract($testArticle, 'sections.*.name', false)
        );
    }

    public function testGetValueFormatted(): void
    {
        $testArticle = $this->_generateArticle();

        // Simple value
        $this->assertEquals(
            'Article with &quot;Quotes&quot;',
            $testArticle->getValueFormatted('name', [])
        );

        // Array
        $this->assertEquals(
            ['Section 1', 'Section 2'],
            $testArticle->getValueFormatted('sections.*.name', [])
        );

        // Missing
        $this->assertEquals(
            null,
            $testArticle->getValueFormatted('notthere', [])
        );
    }

    public function testGetValueNested(): void
    {
        $testArticle = $this->_generateArticle();

        // Simple value
        $this->assertEquals(
            'Article with "Quotes"',
            $testArticle->getValueNested('name', [])
        );

        $this->assertEquals(
            'Article with &quot;Quotes&quot;',
            $testArticle->getValueNested('name', ['format'=>'html'])
        );

        // Array
        $this->assertEquals(
            ['Section 1', 'Section 2'],
            $testArticle->getValueNested('sections.*.name', [])
        );

        $this->assertEquals(
            ['Section 1', 'Section 2'],
            $testArticle->getValueNested('sections.*.name', ['format'=>'html'])
        );

        // Missing
        $this->assertEquals(
            null,
            $testArticle->getValueNested('notthere')
        );

        $this->assertEquals(
            null,
            $testArticle->getValueNested('notthere', ['format'=>'html'])
        );


        $this->assertEquals(
            null,
            $testArticle->getValueNested('notthere.name', ['format'=>'html'])
        );

        // TODO: should we stop collapsing it if no format is set? See next assertion.
        $this->assertEquals(
            [],
            $testArticle->getValueNested('sections.*.notthere')
        );

        $this->assertEquals(
            [null, null],
            $testArticle->getValueNested('sections.*.notthere', ['format'=>'html'])
        );
    }


    /**
     * Test whether 'parent' in an extraction key works
     *
     * @return void
     */
    public function testTraverseEntities(): void
    {
        $testArticle = $this->_generateArticle();

        $this->assertEquals(
            'Article with &quot;Quotes&quot;',
            $testArticle->sections[0]->getValueNested('container.name', ['format' => 'html'])
        );


        $this->assertEquals(
            'Article with &quot;Quotes&quot;',
            $testArticle->sections[0]->getValueNested('root.name', ['format' => 'html'])
        );
    }

        /**
     * Test GEOJson format
     *
     * @return void
     */
    public function testRenderGeojson(): void
    {
        $testArticle = $this->_generateArticle();

        // Rendered GSON
        $this->assertEquals(
            '{"type":"Feature","data":{"sortno":0,"id":1,"signature":"","name":"Article with \"Quotes\"","quality":3,"radius":0,"url":"\/mytesturl\/1"},"geometry":{"type":"Point","coordinates":[1.2,2.1]}}',
            $testArticle->sections[0]->items[0]->getValueFormatted('geo') // defaults to [format => 'html']
        );

        $this->assertEquals(
            '{"type":"Feature","data":{"sortno":0,"id":1,"signature":"","name":"Article with \"Quotes\"","quality":3,"radius":0,"url":"\/mytesturl\/1"},"geometry":{"type":"Point","coordinates":[1.2,2.1]}}',
            $testArticle->sections[0]->items[0]->getValueNested('geo', ['format' => 'html'])
        );

        $this->assertEquals(
            [
                '{"type":"Feature","data":{"sortno":0,"id":1,"signature":"","name":"Article with \"Quotes\"","quality":3,"radius":0,"url":"\/mytesturl\/1"},"geometry":{"type":"Point","coordinates":[1.2,2.1]}}',
                '{"type":"Feature","data":{"sortno":0,"id":2,"signature":"","name":"Article with \"Quotes\"","quality":3,"radius":0,"url":"\/mytesturl\/1"},"geometry":{"type":"Point","coordinates":[2.4,4.2]}}',
                '{"type":"Feature","data":{"sortno":0,"id":1,"signature":"","name":"Article with \"Quotes\"","quality":3,"radius":0,"url":"\/mytesturl\/1"},"geometry":{"type":"Point","coordinates":[1.2,2.1]}}',
                '{"type":"Feature","data":{"sortno":0,"id":2,"signature":"","name":"Article with \"Quotes\"","quality":3,"radius":0,"url":"\/mytesturl\/1"},"geometry":{"type":"Point","coordinates":[2.4,4.2]}}'
            ],
            $testArticle->getValueNested('sections.*.items.*.geo', ['format' => 'html'])
        );

        // Unrendered coordinates
        $this->assertEquals(
            ['lat' => 2.1, 'lng' => 1.2],
            $testArticle->sections[0]->items[0]->getValueNested('geo')
        );

        $this->assertEquals(
            [
                ['lat' => 2.1, 'lng' => 1.2],
                ['lat' => 4.2, 'lng' => 2.4],
                ['lat' => 2.1, 'lng' => 1.2],
                ['lat' => 4.2, 'lng' => 2.4]
            ],
            $testArticle->getValueNested('sections.*.items.*.geo')
        );

        $this->assertEquals(
            [
                ['lat' => 2.1, 'lng' => 1.2],
                ['lat' => 4.2, 'lng' => 2.4],
                ['lat' => 2.1, 'lng' => 1.2],
                ['lat' => 4.2, 'lng' => 2.4]
            ],
            $testArticle->getValueFormatted('sections.*.items.*.geo', ['format' => 'html'])
        );

        $this->assertEquals(
            ['lat' => 2.1, 'lng' => 1.2],
            $testArticle->sections[0]->items[0]->getValueFormatted('geo', ['format' => 'json'])
        );

        $this->assertEquals(
            [
                ['lat' => 2.1, 'lng' => 1.2],
                ['lat' => 4.2, 'lng' => 2.4],
                ['lat' => 2.1, 'lng' => 1.2],
                ['lat' => 4.2, 'lng' => 2.4]
            ],
            $testArticle->getValueFormatted('sections.*.items.*.geo', ['format' => 'json'])
        );

        // Single values
        $this->assertEquals(
            2.1,
            $testArticle->sections[0]->items[0]->getValueNested('geo.lat')
        );

        $this->assertEquals(
            2.1,
            $testArticle->sections[0]->items[0]->getValueNested('geo.lat', ['format' => 'html'])
        );

        $this->assertEquals(
            null,
            $testArticle->sections[0]->items[0]->getValueNested('geo.type', ['format' => 'html'])
        );

        $this->assertEquals(
            2.1,
            $testArticle->sections[0]->items[0]->getValueFormatted('geo.lat', ['format' => 'json'])
        );

    }

    /**
     * Test invalid geodata extraction
     *
     * getValueFormatted() will try to format the first component,
     * and then try to go deeper, which will return invalid coordinates
     * for nested fields.
     *
     * TODO: Should we, instead, travel into the nested data? Or throw a warning?
     *
     * @return void
     */
    public function testRenderInvalidGeojson(): void
    {
        $testArticle = $this->_generateArticle();

        $this->assertEquals(
            '{"type":"Feature","data":{"sortno":0,"id":1,"signature":"","name":"Article with \"Quotes\"","quality":3,"radius":0,"url":"\/mytesturl\/1"},"geometry":{"type":"Point","coordinates":[0,0]}}',
            $testArticle->sections[0]->items[0]->getValueFormatted('geo.lat', ['format' => 'html'])
        );

        $this->assertEquals(
            '{"type":"Feature","data":{"sortno":0,"id":1,"signature":"","name":"Article with \"Quotes\"","quality":3,"radius":0,"url":"\/mytesturl\/1"},"geometry":{"type":"Point","coordinates":[0,0]}}',
            $testArticle->sections[0]->items[0]->getValueFormatted('geo.type')
        );

    }

    /**
     * Test XML format
     *
     * @return void
     */
    public function testRenderXml(): void
    {
        $testArticle = $this->_generateArticle();

        // - rendered HTML output
        $this->assertEquals(
            'This -&gt; is an <span class="xml_tag xml_tag_anno xml_bracket xml_notstyled" data-tagid="a1" data-type="anno"><span class="xml_bracket_open">&lt;anno&gt;</span><span class="xml_bracket_content">annotated</span><span class="xml_bracket_close">&lt;/anno&gt;</span></span> &lt;- text.',
            $testArticle->sections[0]->items[0]->getValueFormatted('xml')
        );

        $this->assertEquals(
            'This -&gt; is an <span class="xml_tag xml_tag_anno xml_bracket xml_notstyled" data-tagid="a1" data-type="anno"><span class="xml_bracket_open">&lt;anno&gt;</span><span class="xml_bracket_content">annotated</span><span class="xml_bracket_close">&lt;/anno&gt;</span></span> &lt;- text.',
            $testArticle->sections[0]->items[0]->getValueFormatted('xml', ['format' => 'html'])
        );

        // - unrendered XML output
        $this->assertEquals(
            'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
            $testArticle->sections[0]->items[0]->getValueFormatted('xml', ['format' => 'xml'])
        );

        // - unrendered JSON output
        $this->assertEquals(
            'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
            $testArticle->sections[0]->items[0]->getValueFormatted('xml', ['format' => 'json'])
        );

        // - unrendered CSV output
        $this->assertEquals(
            'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
            $testArticle->sections[0]->items[0]->getValueFormatted('xml', ['format' => 'csv'])
        );

        // - rendered plain output
        $this->assertEquals(
            'This -> is an annotated <- text.',
            $testArticle->sections[0]->items[0]->getValueFormatted('xml', ['format' => 'md'])
        );

        // - rendered plain output
        $this->assertEquals(
            'This -> is an annotated <- text.',
            $testArticle->sections[0]->items[0]->getValueFormatted('xml', ['format' => 'txt'])
        );

        // - rendered plain output
        $this->assertEquals(
            'This -> is an annotated <- text.',
            $testArticle->sections[0]->items[0]->getValueFormatted('xml', ['format' => 'ttl'])
        );

        // - rendered plain output
        $this->assertEquals(
            'This -> is an annotated <- text.',
            $testArticle->sections[0]->items[0]->getValueFormatted('xml', ['format' => 'rdf'])
        );

        // - rendered plain output
        $this->assertEquals(
            'This -> is an annotated <- text.',
            $testArticle->sections[0]->items[0]->getValueFormatted('xml', ['format' => 'jsonld'])
        );


        // Nested keys should not render anythin (unless getValueNested() is used)
        $this->assertEquals(
            [
                htmlentities('This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'),
                htmlentities('This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'),
                htmlentities('This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'),
                htmlentities('This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.')
            ],
            $testArticle->getValueFormatted('sections.*.items.*.xml')
        );

        $this->assertEquals(
            [
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'
            ],
            $testArticle->getValueFormatted('sections.*.items.*.xml', ['format' => 'md'])
        );

        $this->assertEquals(
            [
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'
            ],
            $testArticle->getValueFormatted('sections.*.items.*.xml', ['format' => 'json'])
        );
    }

    /**
     * Test XML format
     *
     * @return void
     */
    public function testRenderNestedXml(): void
    {
        $testArticle = $this->_generateArticle();

        // Nested keys should not render anything (unless getValueNested() is used)
        $this->assertEquals(
            [
                htmlentities('This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'),
                htmlentities('This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'),
                htmlentities('This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'),
                htmlentities('This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.')
            ],
            $testArticle->getValueFormatted('sections.*.items.*.xml')
        );

        $this->assertEquals(
            [
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'
            ],
            $testArticle->getValueNested('sections.*.items.*.xml')
        );

        $this->assertEquals(
            [
              'This -&gt; is an <span class="xml_tag xml_tag_anno xml_bracket xml_notstyled" data-tagid="a1" data-type="anno"><span class="xml_bracket_open">&lt;anno&gt;</span><span class="xml_bracket_content">annotated</span><span class="xml_bracket_close">&lt;/anno&gt;</span></span> &lt;- text.',
                'This -&gt; is an <span class="xml_tag xml_tag_anno xml_bracket xml_notstyled" data-tagid="a1" data-type="anno"><span class="xml_bracket_open">&lt;anno&gt;</span><span class="xml_bracket_content">annotated</span><span class="xml_bracket_close">&lt;/anno&gt;</span></span> &lt;- text.',
                'This -&gt; is an <span class="xml_tag xml_tag_anno xml_bracket xml_notstyled" data-tagid="a1" data-type="anno"><span class="xml_bracket_open">&lt;anno&gt;</span><span class="xml_bracket_content">annotated</span><span class="xml_bracket_close">&lt;/anno&gt;</span></span> &lt;- text.',
                'This -&gt; is an <span class="xml_tag xml_tag_anno xml_bracket xml_notstyled" data-tagid="a1" data-type="anno"><span class="xml_bracket_open">&lt;anno&gt;</span><span class="xml_bracket_content">annotated</span><span class="xml_bracket_close">&lt;/anno&gt;</span></span> &lt;- text.'
            ],
            $testArticle->getValueNested('sections.*.items.*.xml', ['format' => 'html'])
        );

        $this->assertEquals(
            [
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'
            ],
            $testArticle->getValueFormatted('sections.*.items.*.xml', ['format' => 'md'])
        );

        $this->assertEquals(
            [
                'This -> is an annotated <- text.',
                'This -> is an annotated <- text.',
                'This -> is an annotated <- text.',
                'This -> is an annotated <- text.'
            ],
            $testArticle->getValueNested('sections.*.items.*.xml', ['format' => 'md'])
        );

        $this->assertEquals(
            [
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'
            ],
            $testArticle->getValueFormatted('sections.*.items.*.xml', ['format' => 'json'])
        );

        $this->assertEquals(
            [
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.'
            ],
            $testArticle->getValueNested('sections.*.items.*.xml', ['format' => 'json'])
        );
    }
}
