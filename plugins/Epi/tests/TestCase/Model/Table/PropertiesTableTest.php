<?php

namespace Epi\Test\TestCase\Model\Table;

use App\Utilities\Converters\Arrays;
use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;
use Epi\Model\Table\PropertiesTable;

/**
 * Epi\Model\Table\PropertiesTable Test Case
 */
class PropertiesTableTest extends EpiTestCase
{
    /**
     * Test subject
     *
     * @var \Epi\Model\Table\PropertiesTable
     */
    public $Properties;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
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
        $config = TableRegistry::getTableLocator()->exists('Properties') ? [] : ['className' => PropertiesTable::class];
        $this->Properties = $this->fetchTable('Properties', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Properties);

        parent::tearDown();
    }


    /**
     * Test getScopes method
     *
     * @return void
     */
    public function testGetScopes()
    {
        $scopes = $this->Properties->getScopes();
        $compare = $this->saveComparisonJson($scopes);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test setScope method
     *
     * @return void
     */
    public function testSetScope()
    {
        $this->Properties->setScope('metres');
        $testData = $this->Properties->find('treeList');

        $propertytype_json = $this->saveComparisonJson($testData);
        $this->assertJsonStringEqualsComparison($propertytype_json);
    }

    /**
     * Test removeScope method
     *
     * TODO: Da die removeScope-Methode aktuell nur das Behavior entfernt,
     * testen wir vorläufig das. Ich muss noch überlegen, was sinnvoller wäre.
     *
     * @return void
     */
    public function testRemoveScope()
    {
        // Get data with scope alignments
        $this->Properties->setScope('alignments');
        $data = $this->Properties->find('treeList');
        $compare = $this->saveComparisonJson($data, '.alignments');
        $this->assertJsonStringEqualsComparison($compare, '.alignments');


        $this->Properties->removeScope();
        $data = $this->Properties->find('treeList');
        $compare = $this->saveComparisonJson($data);
        $this->assertJsonStringEqualsComparison($compare);

        $this->Properties->setScope('conditions');
        $data = $this->Properties->find('treeList');
        $compare = $this->saveComparisonJson($data, '.conditions');
        $this->assertJsonStringEqualsComparison($compare, '.conditions');
    }

    /**
     * Test findHasArticleOptions method
     *
     * @return void
     */
    public function testFindHasArticleOptions()
    {
        $options = [
            'articles' => [
                'project' => 1,
                'articletypes' => 'epi-article',
                'properties' => ['objecttypes' => 36],
                'field' => 'content',
                'term' => 'Noregi'
            ]
        ];

        $properties = $this->Properties
            ->find('hasArticleOptions', $options)
            ->where([
                'Properties.propertytype' => 'objecttypes',
                'Properties.related_id IS' => null
            ]);

        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);

    }

    /**
     * Test findHasProject method
     *
     * @return void
     */
    public function testFindByProject()
    {
        $options = [
            'projects' => 1
        ];

        $properties = $this->Properties
            ->find('hasProject', $options)
            ->where([
                'Properties.propertytype' => 'objecttypes',
                'Properties.related_id IS' => null
            ]);

        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test findArticleCount method
     *
     * @return void
     */
    public function testFindArticleCount()
    {
        $options = [
            'articles' => [
                'project' => 1,
                'articletypes' => 'epi-article',
                'properties' => ['objecttypes' => 36],
                'field' => 'content',
                'term' => 'Noregi'
            ]
        ];

        $properties = $this->Properties
            ->find('articleCount', $options)
            ->where([
                'Properties.propertytype' => 'objecttypes',
                'Properties.related_id IS' => null
            ]);

        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test findWithAncestors method
     *
     * @return void
     */
    public function testFindWithAncestors()
    {
        $properties = $this->Properties
            ->find('withAncestors', ['ancestors' => true])
            ->where(['Properties.id' => 166]) // Versmaß 2a
            ->all()->toArray();

        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test findContainAncestors method
     *
     * @return void
     */
    public function testFindContainAncestors()
    {
        $properties = $this->Properties
            ->find('containAncestors')
            ->where(['Properties.id' => 166]) // Versmaß 2a
            ->toArray();

        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test findAncestorsFor method
     *
     * @return void
     */
    public function testFindAncestorsFor()
    {
        $options = [
            'nodes' => [
                [
                    'id' => 166,
                    'lft' => 9,
                    'rght' => 11,
                    'propertytype' => 'metres',
                ],
                [
                    'id' => 169,
                    'lft' => 2,
                    'rght' => 4,
                    'propertytype' => 'metres',
                ]
            ]
        ];

        $properties = $this->Properties
            ->find('ancestorsFor', $options)
            ->toArray();

        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test findWithout method
     *
     * @return void
     */
    public function testFindWithout()
    {
        $ids = [166, 169];

        // With
        $properties = $this->Properties
            ->find('list')
            ->toArray();

        $compare = array_intersect($ids, array_keys($properties));
        $this->assertEquals($ids, $compare);

        // Without
        $properties = $this->Properties
            ->find('list')
            ->where(['propertytype' => 'metres'])
            ->find('without', ['ids' => $ids])
            ->toArray();

        $compare = array_intersect($ids, array_keys($properties));
        $this->assertEmpty($compare);

        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test findReferencesFrom method
     *
     * @return void
     */
    public function testFindReferencesFrom()
    {
        $options = [
            'nodes' => [169]
        ];

        $properties = $this->Properties
            ->find('referencesFrom', $options)
            ->toArray();

        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test findReferencesTo method
     *
     * @return void
     */
    public function testFindReferencesTo()
    {
        $options = [
            'nodes' => [170]
        ];

        $properties = $this->Properties
            ->find('referencesTo', $options)
            ->all()->toArray();

        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test adding entity method
     *
     * @return void
     */
    public function testSaveAdd()
    {
        $data = [
            'deleted' => 0,
            'published' => 1,
            'created' => '2022-01-29 15:07:25',
            'modified' => '2022-01-29 15:07:37',
            'created_by' => 5,
            'modified_by' => 5,
            'sortno' => 9,
            'sortkey' => 'gehauen',
            'propertytype' => 'techniques',
            'signature' => 1,
            'file_name' => 'test_file.jpg',
            'properties_id' => 29555,
            'lemma' => 'gehauen',
            'name' => 'gehauen',
            'unit' => 'test',
            'comment' => 'Wirklich erfunden...',
            'content' => 'Hier ist eine erfundene Property zu sehen',
            'elements' => 'Teilbereiche davon',
            'keywords' => 'test',
            'source_from' => 'vgl. anderer Datensatz',
            'ishidden' => 0,
            'iscategory' => 1,
            'norm_type' => null,
            'norm_data' => 'gnd:4037124-4',
            'norm_iri' => 'gehauen',
            'import_db' => 'test_properties.csv',
            'import_id' => 77,
            'related_id' => 22915,
            'mergedto_id' => null,
            'splitfrom_id' => null,
            'parent_id' => null,
            'level' => 0,
            'lft' => 9,
            'rght' => 10
        ];

        // Create new entity
        $property = $this->Properties->newEntity($data);
        $result = $this->Properties->save($property);

        // Request the created entity
        $property = $this->Properties->get($result->id);
        $property = Arrays::array_remove_keys($property, ['modified']);
        $compare = $this->saveComparisonJson($property);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test the save method
     *
     * @return void
     */
    public function testSavePatch()
    {
        // Request and patch  entity
        $property = $this->Properties->get(170);
        $data = ['comment' => 'Hier wurde etwas geändert'];
        $this->Properties->patchEntity($property, $data);
        $this->Properties->save($property);

        // Get the changed entity
        $property = $this->Properties->get(170);
        $compare = $this->saveComparisonJson($property);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test that changing the parent updates the fields lft, rght and level
     *
     * @return void
     */
    public function testSaveNewParent()
    {
        $property = $this->Properties->get(166);

        // Save new parent
        $property->parent_id = 170;
        $this->Properties->setScope($property->propertytype);
        $this->Properties->save($property);

        // Compare parent
        $property = $this->Properties->get(166);
        $this->assertEquals(170, $property->parent_id);

        $properties = $this->Properties
            ->find('all')
            ->select(['id', 'parent_id', 'lft', 'rght', 'level'])
            ->where(['propertytype' => $property->propertytype]);


        $compare = $this->saveComparisonJson($properties);
        $this->assertJsonStringEqualsComparison($compare);
    }

    /**
     * Test that no self links occur
     *
     * @return void
     */
    public function testSaveSelfLink()
    {
        $property = $this->Properties->get(166);
        $parentId = $property->parent_id;


        // Saving should not be successful
        $property->parent_id = 166;
        $result = $this->Properties->save($property);
        $this->assertEquals(false, $result);
        $this->assertEquals("Self-links will collapse the universe. Don't.", $property->getError('parent_id')[0]);

        // Parent ID should remain unchanged
        $property = $this->Properties->get(166);
        $this->assertEquals($property->parent_id, $parentId);
    }

    /**
     * Test the integrity of parent_id
     *
     * @return void
     */
    public function testSaveMissingParent()
    {
        $property = $this->Properties->get(166);
        $parentId = $property->parent_id;

        // Saving should fail
        $property->parent_id = 99999;
        $result = $this->Properties->save($property);
        $this->assertEquals(false, $result);
        $this->assertEquals("This value does not exist", $property->getError('parent_id')['_existsIn']);

        // Parent ID should remain unchanged
        $property = $this->Properties->get(166);
        $this->assertEquals($parentId, $property->parent_id);
    }
}
