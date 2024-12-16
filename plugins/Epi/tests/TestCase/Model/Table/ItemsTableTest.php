<?php
namespace Epi\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;
use Epi\Model\Table\ItemsTable;

/**
 * Epi\Model\Table\ItemsTable Test Case
 */
class ItemsTableTest extends EpiTestCase
{
    /**
     * Test subject
     *
     * @var \Epi\Model\Table\ItemsTable
     */
    public $Items;

    /**
     * Use auto fixtures
     *
     * @var bool
     */
    public $autoFixtures = true;

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
        $config = TableRegistry::getTableLocator()->exists('Items') ? [] : ['className' => ItemsTable::class];
        $this->Items = $this->fetchTable('Items', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Items);

        parent::tearDown();
    }


	/**
	 * test findTable method
	 *
	 * @return void
	 */
	public function testFindTable(): void
	{
		$itemList = $this->Items
			->find('table');

		$compare = $this->saveComparisonJson($itemList);
		$this->assertJsonStringEqualsComparison($compare);
	}

    /**
     * Test that nested JSON values are merged
     *
     * @return void
     */
    public function testMergeJson(): void
    {
        // Create new item
        $item = $this->Items->newEntity(['value'=>'{"field_one":1,"field_two":2}']);
        $item->type->config = [
            'fields' => ['value' => ['format' => 'json']]
        ];
        $this->Items->save($item);

        // 1. Merge nested value: patch and save item
        $item = $this->Items->patchEntity($item,['value'=>['field_one' => 3]]);
        $this->Items->save($item);

        $item = $this->Items->get($item->id);
        $this->assertEquals('{"field_one":3,"field_two":2}',$item['value']);

        // 2. Overwrite complete value: patch and save item
        $item = $this->Items->patchEntity($item,['value'=>'{"field_new":1}']);
        $this->Items->save($item);

        $item = $this->Items->get($item->id);
        $this->assertEquals('{"field_new":1}',$item['value']);

    }
}
