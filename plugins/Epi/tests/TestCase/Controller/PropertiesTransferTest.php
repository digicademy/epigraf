<?php

namespace Epi\Test\TestCase\Controller;

use App\Utilities\Converters\Arrays;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Controller\PropertiesController Test Case
 *
 * @uses \Epi\Controller\PropertiesController
 */
class PropertiesTransferTest extends EpiTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Users',
        'app.Pipelines',
        'app.Jobs',
        'app.Permissions',
        'app.TwoDatabanks',

        'plugin.Epi.Users',
        'plugin.Epi.Token',
        'plugin.Epi.Locktable',
        'plugin.Epi.Articles',
		'plugin.Epi.Sections',
        'plugin.Epi.Items',
        'plugin.Epi.Links',
        'plugin.Epi.TransferPropertiesProjects',
        'plugin.Epi.Types',
        'plugin.Epi.Meta',

        'plugin.Epi.PublicUsers',
        'plugin.Epi.PublicMeta',
        'plugin.Epi.PublicToken',
        'plugin.Epi.PublicLocktable',
        'plugin.Epi.PublicTypes',

        'plugin.Epi.TransferPropertiesPublic'
    ];

	/**
     * Test transfer method
     *
     * @return void
     */
    public function testTransferToPublic()
    {

        // Target database: Get properties before transfer operation
        $this->fetchTable('Databanks')
            ->activateDatabase(DATABASE_PUBLIC);

        $properties = $this->fetchTable("Epi.Properties")
            ->find('all')
            ->where(['propertytype'=>'materials'])
            ->disableHydration()
            ->toArray();

        $this->assertArrayEqualsComparison($properties, ".1_properties_target.php");

        // Source database: Get properties before transfer operation
        $this->fetchTable('Databanks')
            ->activateDatabase('projects');

        $properties = $this->fetchTable("Epi.Properties")
            ->find('all')
            ->where(['propertytype'=>'materials'])
            ->disableHydration()
            ->toArray();

        $this->assertArrayEqualsComparison($properties, ".1_properties_source.php");

        // Step 1: Select target database
        $this->loginUser('admin');
		$this->get('epi/projects/properties/transfer/materials');

		$this->assertHtmlEqualsComparison(true,".content-wrapper",'.1_selecttarget');

		// Step 2: Preview
        $this->get('epi/projects/properties/transfer/materials?target=public');
        $this->assertRedirect([
            'plugin'=>'epi','database'=> DATABASE_PUBLIC,
            'controller'=>'properties','action'=>'transfer','materials',
            '?' => ['stage'=>'preview', 'source'=>'projects','close'=>'0']
            ]);

        $this->get(
            'epi/public/properties/transfer/materials?'
            .'source=projects&snippets=editors,published&stage=preview'
        );

		$this->assertHtmlEqualsComparison(true,".content-wrapper",'.2_preview');

        // Step 3: Transfer
        $this->post(
            'epi/public/properties/transfer/materials'
            .'?source=projects&tree=1&snippets=editors,published'
        );
        $this->assertRedirect([
            'plugin' => false,
            'controller' => 'Jobs',
            'action' => 'execute',
            3,
            '?' => ['database' => DATABASE_PUBLIC, 'close' => 0]
        ]);

        // Poll for 10 rounds at maximum
        $polling = 10;
        while ($polling) {
            $this->get('/jobs/execute/3');
            $polling = ($this->_response->getHeader('location')) ? false : $polling - 1;
        }

        $this->assertRedirect([
            'plugin' => 'epi',
            'database' => DATABASE_PUBLIC,
            'controller' => 'Properties',
            'action' => 'index',
            'materials'
        ]);

        // Target database: Get properties after transfer operation
        $this->fetchTable('Databanks')
            ->activateDatabase(DATABASE_PUBLIC);

        $properties = $this->fetchTable("Epi.Properties")
            ->find('all')
            ->where(['propertytype'=>'materials'])
            ->disableHydration()
            ->toArray();

        // The tree behavior will recover the tree and the database will update the modified field
        $properties = Arrays::array_remove_keys($properties, ['modified']);
        $this->assertArrayEqualsComparison($properties, ".2_properties_target.php");

        // Source database: Get properties after transfer operation
        $this->fetchTable('Databanks')
            ->activateDatabase('projects');

        $properties = $this->fetchTable("Epi.Properties")
            ->find('all')
            ->where(['propertytype'=>'materials'])
            ->disableHydration()
            ->toArray();

        $this->assertArrayEqualsComparison($properties, ".2_properties_source.php");
    }

    /**
     * Test transfer method
     *
     * @return void
     */
    public function testTransferFromPublic()
    {
        // Target database: Get properties before transfer operation
        $this->fetchTable('Databanks')
            ->activateDatabase('test_projects');

        $properties = $this->fetchTable("Epi.Properties")
            ->find('all')
            ->where(['propertytype'=>'materials'])
            ->disableHydration()
            ->toArray();

        $this->assertArrayEqualsComparison($properties, ".1_properties_target.php");

        // Source database: Get properties before transfer operation
        $this->fetchTable('Databanks')
            ->activateDatabase('test_public');

        $properties = $this->fetchTable("Epi.Properties")
            ->find('all')
            ->where(['propertytype'=>'materials'])
            ->disableHydration()
            ->toArray();

        $this->assertArrayEqualsComparison($properties, ".1_properties_source.php");

        // Step 1: Select target database
        $this->loginUser('admin');
        $this->get('epi/public/properties/transfer/materials');

		$this->assertHtmlEqualsComparison(true,".content-wrapper",'.1_selecttarget');

        // Step 2: Preview
        $this->get('epi/public/properties/transfer/materials?target=projects');
        $this->assertRedirect([
            'plugin' => 'epi',
            'database' => 'projects',
            'controller' => 'properties',
            'action' => 'transfer',
            'materials',
            '?' => ['stage' => 'preview', 'source' => DATABASE_PUBLIC, 'close' => '0']
        ]);

        $this->get(
            'epi/projects/properties/transfer/materials?'
            .'source=public&snippets=editors,published&stage=preview'
        );
		$this->assertHtmlEqualsComparison(true,".content-wrapper",'.2_preview');

        // Step 3: Transfer
        $this->post(
            'epi/projects/properties/transfer/materials'
                .'?source=public&tree=1&snippets=editors,published'
        );
        $this->assertRedirect([
            'plugin' => false,
            'controller' => 'Jobs',
            'action' => 'execute',
            3,
            '?' => ['database' => 'projects', 'close' => 0]
        ]);

        // Poll for 10 rounds at maximum
        $polling = 10;
        while ($polling) {
            $this->get('/jobs/execute/3');
            $polling = ($this->_response->getHeader('location')) ? false : $polling - 1;
        }

        $this->assertRedirect([
            'plugin' => 'epi',
            'database' => 'projects',
            'controller' => 'Properties',
            'action' => 'index',
            'materials'
        ]);

        // Source database: Get properties after transfer operation
        $this->fetchTable('Databanks')
            ->activateDatabase('test_public');

        $properties = $this->fetchTable("Epi.Properties")
            ->find('all')
            ->where(['propertytype'=>'materials'])
            ->disableHydration()
            ->toArray();

        $this->assertArrayEqualsComparison($properties, ".2_properties_source.php");

        // Target database: Get properties after transfer operation
        $this->fetchTable('Databanks')
            ->activateDatabase('test_projects');

        $properties = $this->fetchTable("Epi.Properties")
            ->find('all')
            ->where(['propertytype'=>'materials'])
            ->disableHydration()
            ->toArray();

        // The tree behavior will recover the tree and the database will update the modified field
        $properties = Arrays::array_remove_keys($properties, ['modified']);
        $this->assertArrayEqualsComparison($properties, ".2_properties_target.php");
    }

}
