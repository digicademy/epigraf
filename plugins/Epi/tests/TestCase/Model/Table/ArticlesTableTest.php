<?php
namespace Epi\Test\TestCase\Model\Table;

use App\Utilities\Converters\Arrays;
use Cake\Collection\CollectionInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Epi\Model\Table\ArticlesTable;
use Epi\Model\Table\BaseTable;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Model\Table\ArticlesTable Test Case
 */
class ArticlesTableTest extends EpiTestCase
{
    /**
     * Test subject
     *
     * @var \Epi\Model\Table\ArticlesTable
     */
    public $Articles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
        'app.Permissions',
        'app.Pipelines',
        'app.Databanks'
	];


    public $dumps = [
        'test_projects' => 'test_projects.sql'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Articles') ? [] : ['className' => ArticlesTable::class];
        $this->Articles = $this->fetchTable('Articles', $config);
        $this->Articles::$userRole = 'devel';
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
	 * Test findHasArticleIds method
	 *
	 * @return void
	 */
	public function testFindHasArticleIds()
	{
		$options = [
			'articles' => 3,
		];

		$articles = $this->Articles
			->find('hasIds', $options);

		$compare = $this->saveComparisonJson($articles);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * Test findHasTerm method
	 *
	 * @return void
	 */
	public function testFindHasTerm()
	{
		$options = [
			'term' => 'progress',
			'field' => 'status',
		];

		$articles = $this->Articles
			->find('hasTerm', $options);

		$compare = $this->saveComparisonJson($articles);
		$this->assertJsonStringEqualsComparison($compare);

	}


	/**
	 * Test findHasProject method
	 *
	 * @return void
	 */
	public function testFindHasProject()
	{
		$options = [
			'projects' => 1,
		];

		$articles = $this->Articles
			->find('hasProject', $options);

		$compare = $this->saveComparisonJson($articles);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * Test findHasArticleType method
	 *
	 * @return void
	 */
	public function testFindHasArticleType()
	{
        $options = [
			'articletypes' => 'epi-article',
		];

		$articles = $this->Articles
			->find('hasArticleType', $options);

		$compare = $this->saveComparisonJson($articles);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * Test findHasProperties method
	 *
	 * @return void
	 */
	public function testFindHasProperties()
	{
		$options = [
			'properties' => [92,128]
		];

		$articles = $this->Articles
			->find('hasProperties', $options);

		$compare = $this->saveComparisonJson($articles);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * Test findHasText method
	 *
	 * @return void
	 */
	public function testFindHasText()
	{
		$options = [
			'term' => 'maðr',
		];

		$articles = $this->Articles
			->find('hasText', $options);

		$compare = $this->saveComparisonJson($articles);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * Test findHasValue method
	 *
	 * @return void
	 */
	public function testFindHasValue()
	{
		$options = [
			'term' => 'signatur',
			'field' => 'signature',
		];

		$articles = $this->Articles->find('hasTerm', $options);

		$compare = $this->saveComparisonJson($articles);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * Test findHasParams method
	 *
	 * @return void
	 */
	public function testFindHasParams()
	{
		$options = [
			'articles' => 1,
			'term' => 'maðr',
			'scope' => 'content',
			'project' => 1,
			'articletypes' => ['epi-article'],
			'properties' => [36]
		];

		$articles = $this->Articles
			->find('hasParams', $options);

		$compare = $this->saveComparisonJson($articles);
		$this->assertJsonStringEqualsComparison($compare);
	}

	/**
	 * Test findPrepareXml method
	 *
	 * @return void
	 */
	public function testFindPrepareXml()
	{
		$options = [
			'regroup' => true
		];

        $id = 1;
        $data = $this->Articles
            ->find('containAll', $options)
            ->formatResults(
                function (CollectionInterface $results) use (&$query) {
                    return $results->map(
                        function ($row) {
                            return $row->getDataForExport(['params' => ['snippets'=>['comments']]],'xml');
                        }
                    );
                }
            )
            ->where(['Articles.id' => $id]);

        $data = Arrays::array_remove_null($data);
//        $data = Arrays::array_remove_keys($data,['_serialize_fields']);
        $data = Arrays::array_remove_keys($data,['_xml_attributes']);
        $data = Arrays::array_remove_keys($data,['modified']);

		$compare = $this->saveComparisonJson($data);
		$this->assertJsonStringEqualsComparison($compare);
	}


    /**
     * Test finprepareRoot method
     *
     * @return void
     */
    public function testFindPrepareRoot()
    {
        $options = [
            'regroup' => true
        ];

        $id = 1;
        $articlesPrepared = $this->Articles
            ->find('containAll', $options)
            ->formatResults(
                function (CollectionInterface $results) use (&$query) {
                    return $results->map(
                        function ($row) {
                            return $row->prepareRoot($row, $row);
                        }
                    );
                }
            )
            ->where(['Articles.id' => $id]);

        $compare = $this->saveComparisonJson($articlesPrepared);
        $this->assertJsonStringEqualsComparison($compare);
    }


    /**
	 * Test findContainAll method
	 *
	 * @return void
	 */
	public function testFindComplete()
	{
		$id = 1;

		$articles = $this->Articles
			->find('containAll',['snippets'=>['indexes','paths']])
			->where(['Articles.id'=>$id])
			->toArray();

		$index = $this->Articles->getIndexes();

        $articles = Arrays::array_remove_null($articles);
        $articles = Arrays::array_remove_keys($articles,['_serialize_fields']);
        $articles = Arrays::array_remove_keys($articles,['_xml_attributes']);
        $articles = Arrays::array_remove_keys($articles,['modified']);

		$articles_json = $this->saveComparisonJson($articles,'.articles');
		$index_json = $this->saveComparisonJson($index,'.index');

		$this->assertJsonStringEqualsComparison($articles_json,'.articles');
		$this->assertJsonStringEqualsComparison($index_json,'.index');
	}

	/**
	 * Test findContainAll method with two articles
	 *
	 * @return void
	 */
	public function testFindCompleteTwoArticles()
	{
		$article1 = $this->Articles
			->get(1,['finder'=>'containAll','snippets'=>['indexes']]);

		$article2 = $this->Articles
			->get(3,['finder'=>'containAll','snippets'=>['indexes']]);

		$index = $this->Articles->getIndexes();

        $article1 = Arrays::array_remove_null($article1);
        $article1 = Arrays::array_remove_keys($article1,['_serialize_fields']);
        $article1 = Arrays::array_remove_keys($article1,['_xml_attributes']);
        $article1 = Arrays::array_remove_keys($article1,['modified']);

        $article2 = Arrays::array_remove_null($article2);
        $article2 = Arrays::array_remove_keys($article2,['_serialize_fields']);
        $article2 = Arrays::array_remove_keys($article2,['_xml_attributes']);
        $article2 = Arrays::array_remove_keys($article2,['modified']);

		$article1_json = $this->saveComparisonJson($article1,'.article1');
		$article2_json = $this->saveComparisonJson($article2,'.article2');
		$index_json = $this->saveComparisonJson($index,'.index');

		$this->assertJsonStringEqualsComparison($article1_json,'.article1');
		$this->assertJsonStringEqualsComparison($article2_json,'.article2');
		$this->assertJsonStringEqualsComparison($index_json,'.index');
	}


	/**
	 * Test findContainAll method (check if exception is thrown after trying to access
	 * non-existing article id)
	 *
	 * @return void
	 */
	public function testFindCompleteException()
	{
		$this->expectException(RecordNotFoundException::class);
		$this->expectExceptionMessage('Record not found in table "articles"');

		$this->Articles
			->get(9999,['finder'=>'containAll','snippets'=>['indexes']]);
	}


	/**
	 * Test getExportCount method
	 *
	 * @return void
	 */
	public function testGetExportCount()
	{
		$options = [
			'articles' => 3
		];

		$exportCount = $this->Articles
			->getExportCount($options);

		$compare = $this->saveComparisonJson($exportCount);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * Test getExportData method with two articles and clearing index in between
	 * Will the index survive?
	 *
	 * @return void
	 */
	public function testGetExportData()
	{
		$joboptions = [
			'articletypes'=>'epi-article',
			'articles'=>'1,3',
            'snippets'=>'indexes,paths'
		];
		$indexkey = 'export-cache';

		// First article
		$article1 = $this->Articles
			->getExportData($joboptions, ['offset' => 0, 'limit' => 1], $indexkey);

		//Clear index array -> force loading from disk
		$this->Articles->clearIndex($indexkey,false);

		// Second article
		$article2 = $this->Articles
			->getExportData($joboptions,['offset' => 1, 'limit' => 1], $indexkey);

		// Get index
		$this->Articles->clearIndex($indexkey,false);
		$index = $this->Articles->getIndexes($indexkey);
		$this->Articles->clearIndex($indexkey,true);

		//Compare
        $index_json = $this->saveComparisonJson($index,'.index');
        $article1_json = $this->saveComparisonJson($article1,'.article1');
		$article2_json = $this->saveComparisonJson($article2,'.article2');

        $this->assertJsonStringEqualsComparison($index_json,'.index');
		$this->assertJsonStringEqualsComparison($article1_json,'.article1');
		$this->assertJsonStringEqualsComparison($article2_json,'.article2');

	}

    /**
     * Test getDataForTransfer method
     *
     * @return void
     */
    public function testGetDataForTransfer()
    {

        $id = 1;
        $options = [];
        $transferOptions = ['snippets'=>['comments','published', 'search', 'iris', 'editors']];
        $data = $this->Articles
            ->find('containAll', $options)
            ->formatResults(
                function (CollectionInterface $results) use (&$query, $transferOptions) {
                    return $results->map(
                        function ($row) use ($transferOptions) {
                            return $row->getDataForTransfer($transferOptions);
                        }
                    );
                }
            )
            ->where(['Articles.id' => $id]);


        $data = Arrays::array_remove_null($data);
        $data = Arrays::array_remove_keys($data,['_serialize_fields']);
        $data = Arrays::array_remove_keys($data,['_xml_attributes']);
        $data = Arrays::array_remove_keys($data,['modified']);

        $compare = $this->saveComparisonJson($data);
        $this->assertJsonStringEqualsComparison($compare);
    }

	/**
	 * Test getSearchFields method
	 *
	 * @return void
	 */
	public function testGetSearchFields()
	{

		$searchFields = $this->Articles->getFilter([])['search'] ?? [];
		$compare = $this->saveComparisonJson($searchFields);
		$this->assertJsonStringEqualsComparison($compare);
	}


	/**
	 * Test parseRequestParameters method
	 *
	 * @return void
	 */
	public function testParseRequestParameters()
	{
		// With non-empty parameters
		$queryparams1 = [
			'term' => 'neu angelegt',
			'field' => 'status',
			'projects' => 5,
			'articletypes' => 'epi-article',
			'properties' => [
				'id' => 341
			]
		];

		$articles1 = $this->Articles
			->parseRequestParameters($queryparams1);

		$compare = $this->saveComparisonJson($articles1);
		$this->assertJsonStringEqualsComparison($compare);

		// Fully qualified keys
		$queryparams2 = [
			'articles_term' => 'neu angelegt',
			'articles_field' => 'status',
			'articles_projects' => 5,
			'articles_articletypes' => 'epi-article',
			'properties' => [
				'id' => 341
			]
		];

		$articles2 = $this->Articles
			->parseRequestParameters($queryparams2);

		$this->assertEquals($articles1, $articles2);

		// With empty parameters
		$queryparamsEmpty1 = [
			'term' => '',
			'field' => '',
			'projects' => null,
			'articletypes' => '',
			'properties' => []
		];

		$articlesEmpty1 = $this->Articles
			->parseRequestParameters($queryparamsEmpty1);

		$compare2 = $this->saveComparisonJson($articlesEmpty1, '.empty');
		$this->assertJsonStringEqualsComparison($compare2, '.empty');

		// Fully qualified keys
		$queryparamsEmpty2 = [
			'articles_term' => '',
			'articles_field' => '',
			'articles_projects' => null,
			'articles_articletypes' => '',
			'properties' => []
		];

		$articlesEmpty2 = $this->Articles
			->parseRequestParameters($queryparamsEmpty2);

		$this->assertEquals($articlesEmpty1, $articlesEmpty2);
	}

    public function testGetColumns()
    {
        $columns = $this->Articles->getColumns(['id','signature']);
        $this->assertJsonStringEqualsComparison($columns, '.twocols');

        $columns = $this->Articles->getColumns(['id','signature','inscriptions_count']);
        $this->assertJsonStringEqualsComparison($columns, '.countcol');

        // Todo: New syntax is * not {*}
        $columns = $this->Articles->getColumns(['locations_count=items.{*}[itemtype=locations]|count']);
        $this->assertJsonStringEqualsComparison($columns, '.customcol');
    }


    /**
     * Test joining complex columns
     *
     * @return void
     */
    public function testJoinColumns()
    {
        $columns = [
            'dates' => [
                'caption' => 'Objektdatierung',
                'key' => 'items.*[itemtype=conditions,dio-conditions-raw].date_value|collapse',
                'sort' => 'items.*[itemtype=conditions,dio-conditions-raw].date_sort|min',
                'default' => true,
                'public' => true
            ],
            'locations' => [
                'caption' => 'Standorte',
                'key' => 'items.*[itemtype=locations].property.name|collapse',
                'default' => true,
                'public' => true
            ],
            'sortnos' => [
                'caption' => 'Sortiernummern',
                'key' => 'items.*[itemtype=locations,topics,conditions].sortno|max',
                'default' => true,
                'public' => true
            ],
            'topics_priority' => [
                'caption' => 'Themenpriorität',
                'key' => 'items.*[itemtype=topics].value|collapse',
                'sort' => [
                    'table' => 'items',
                    'conditions' => [
                        'itemtype' => 'topics'
                    ],
                    'field' => 'value',
                    'cast' => 'INTEGER',
                    'aggregate' => 'min'
                ],
                'selectable' => false,
                'public' => true
            ]
        ];

        $table = new BaseTable([
            'registryAlias' => 'Epi.BaseTable',
            'entityClass' => 'Epi.BaseEntity'
        ]);

        $table->hasMany('Items', [
            'className' => 'Epi.Items',
            'foreignKey' => 'articles_id',
            'sort' => ['Items.sortno'], //,'Items.itemtype'
            'propertyName' => 'items',
            'conditions' => ['Items.deleted' => 0],
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        // Check columns
        $columns = $table->getColumns(array_keys($columns),$columns);
        $this->assertJsonStringEqualsComparison($columns, 'columns');

        // Check SQL query
        $table->setTable('articles');
        $query = $table->find('joinColumns', ['columns' => $columns]);

        $sql = $query->sql($query->getValueBinder());
        $this->assertEquals(
            'SELECT '

            . '(MIN(dates.date_sort)) AS dates, '
            . 'GROUP_CONCAT(locations.name ORDER BY locations_0.sortno ASC SEPARATOR ", ") AS locations, '
            .'(MAX(sortnos.sortno)) AS sortnos, '
            . '(MIN(CAST(topics_priority.value AS INTEGER))) AS topics_priority '

            . 'FROM articles Base '
            .'LEFT JOIN items dates '
            . 'ON (dates.itemtype IN ("conditions","dio-conditions-raw") AND dates.deleted = 0 AND dates.articles_id = Base.id) '
            . 'LEFT JOIN items locations_0 '
            .'ON (locations_0.itemtype IN ("locations") AND locations_0.deleted = 0 AND locations_0.articles_id = Base.id) '
            . 'LEFT JOIN properties locations '
            .'ON (locations.deleted = 0 AND locations.id = locations_0.properties_id) '
            .'LEFT JOIN items sortnos '
            .'ON (sortnos.itemtype IN ("locations","topics","conditions") AND sortnos.deleted = 0 AND sortnos.articles_id = Base.id) '
            . 'LEFT JOIN items topics_priority '
            .'ON (topics_priority.itemtype IN ("topics") AND topics_priority.articles_id = Base.id) '
            . 'WHERE Base.deleted = :c0 GROUP BY Base.id ',
            $sql
        );

        // Check result
        // TODO: why empty and ugly, use better test data?
        $data = $query->where(['Base.deleted'=>0])->toArray();
        $this->assertJsonStringEqualsComparison($data, 'data');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $tables = ['projects' ,'articles', 'sections', 'items', 'footnotes', 'links'];
        $conn = $this->Articles->getConnection();

        // Status prior to delete
        $rows_before = [];
        foreach ($tables as $table) {
            $sql = 'SELECT CONCAT("' . $table. '-", id) AS id, version_id, deleted FROM ' . $table;
            $rows = $conn->execute($sql)->fetchAll();
            $rows_before = array_merge($rows_before, $rows);
        }

        // No need to test, whether the Article exists, get would throw an exception
        $entity = $this->Articles->get(3);
        $this->Articles->delete($entity);

        // Status after delete
        $rows_after = [];
        foreach ($tables as $table) {
            $sql = 'SELECT CONCAT("' . $table. '-", id) AS id, version_id, deleted FROM ' . $table;
            $rows = $conn->execute($sql)->fetchAll();
            $rows_after = array_merge($rows_after, $rows);
        }

        //TODO: refactor using Arrays::tablesCompare()

        // Compare rows
        $rows_before = array_combine(array_column($rows_before,0),$rows_before);
        $rows_after = array_combine(array_column($rows_after,0),$rows_after);

        $rows_before_diff = array_filter($rows_before, function ($row, $id) use ($rows_after) {
            return empty($rows_after[$id]) || ($rows_after[$id] !== $row);
        }, ARRAY_FILTER_USE_BOTH );

        $rows_after_diff = array_filter($rows_after, function ($row, $id) use ($rows_before) {
            return empty($rows_before[$id]) || ($rows_before[$id] !== $row);
        }, ARRAY_FILTER_USE_BOTH );

        $rows_before_diff_keys = array_map(function ($x) { return $x . '-before';} ,array_keys($rows_before_diff));
        $rows_before_diff = array_combine($rows_before_diff_keys,$rows_before_diff);

        $rows_after_diff_keys = array_map(function ($x) { return $x . '-after';} , array_keys($rows_after_diff));
        $rows_after_diff = array_combine($rows_after_diff_keys,$rows_after_diff);

        $rows_diff = array_merge($rows_before_diff,$rows_after_diff);
        ksort($rows_diff);

        $compare = $this->saveComparisonJson($rows_diff);
        $this->assertJsonStringEqualsComparison($compare);
    }

}
