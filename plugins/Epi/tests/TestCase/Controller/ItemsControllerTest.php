<?php

namespace Epi\Test\TestCase\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Controller\ItemsController Test Case
 * //TODO: implement ItemsController instead of items action in articles
 *
 * @uses \Epi\Controller\ArticlesController
 */
class ItemsControllerTest extends EpiTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
        'app.Permissions',
        'app.Pipelines',
        'app.Databanks',
    ];


    public $dumps = [
        'test_projects' => 'test_projects.sql'
    ];

    public $Articles = null;
    public $Sections = null;
    public $Items = null;
    public $Footnotes = null;
    public $Links = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Articles = $this->fetchTable('Epi.Articles');
        $this->Sections = $this->fetchTable('Epi.Sections');
        $this->Items = $this->fetchTable('Epi.Items');
        $this->Footnotes = $this->fetchTable('Epi.Footnotes');
        $this->Links = $this->fetchTable('Epi.Links');

    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Articles);
        parent::tearDown();
    }

    /**
     * Test geo items (JSON file)
     *
     * @return void
     */
    public function testItemsGeoJson(): void
    {
        $this->loginUser('admin');
        $this->get('/epi/projects/articles/items.geojson?tile=11%2F676%2F1067&itemtypes=geolocations&snippets=article&page=1');
        $this->assertJsonResponseEqualsComparison('', 'application/geo+json');

        $this->loginUser('admin');
        $this->get('/epi/projects/articles/items.geojson?tile=11%2F676%2F1067&itemtypes=geolocations&snippets=article,properties&page=1');
        $this->assertJsonResponseEqualsComparison('.properties', 'application/geo+json');

    }

    /**
     * Test items (JSON file)
     *
     * @return void
     */
    public function testItemsJson(): void
    {
        // TODO: nested json should not be quoted
        $this->loginUser('admin');
        $this->get('/epi/projects/articles/items.json?itemtypes=geolocations&page=1');
        $this->assertJsonResponseEqualsComparison('.plain');

        $this->get('/epi/projects/articles/items.json?columns=value%2Ccontent%2Cid&page=1&itemtypes=geolocations&snippets=article');
        $this->assertJsonResponseEqualsComparison('.snippets');
    }

}
