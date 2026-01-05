<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Controller;

use App\Test\TestCase\AppTestCase;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Table;
use Epi\Model\Table\BaseTable;


/**
 * App\Controller\DatabanksController Test Case
 *
 * @uses \App\Controller\DatabanksController
 */
class DatabanksControllerTest extends AppTestCase
{
	/**
	 * Test subject
	 *
	 * @var Table
	 */
	public $Databanks;

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = [
		'app.Databanks',
		'app.Users',
		'app.Pipelines',
        'app.Permissions',
        'plugin.Epi.Meta',
        'plugin.Epi.Articles',
        'plugin.Epi.Projects',
        'plugin.Epi.Properties',
		'plugin.Epi.Token'
	];


	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp(): void
	{
	    parent::setUp();
		$this->Databanks = $this->fetchTable('Databanks');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown(): void
	{
        parent::tearDown();

        // Remove database and folders
        $this->removeDatabase('test_newprojects');

        // Remove file upload
        $this->tearDownFileUpload('import.sql');

        // Remove backup
        $backupfile = Configure::read('Data.databases') . 'test_projects' . DS . 'backup' . DS . 'backup_test_projects_2020-10-29_01_00_58_1603976458.sql';
        if (file_exists($backupfile)) {
            unlink($backupfile);
        }

        $backupfile = Configure::read('Data.databases') . 'test_projects' . DS . 'backup' . DS . 'backup_test_projects_2020-10-29_01_00_58_1603972858.sql';
        if (file_exists($backupfile)) {
            unlink($backupfile);
        }

        // Remove testtable
        $con = BaseTable::setDatabase('test_projects');
        $con->execute('DROP TABLE IF EXISTS testtable;');

        // Unset DatabanksTable
		unset($this->Databanks);
	}

    /**
     * Compare existing SQL dump file to currently created file
     *
     * @param $databank
     * @param $suffix
     *
     * @return void
     */
    public function assertDatabankEqualsDump($databank, $suffix='') {
        $expectedDump = $this->comparisonFile . $suffix. '.sql';

        // Create backup
        $backupfile = Configure::read('Data.databases') .
            $databank->name . DS .
            'backup' . DS .
            'backup_'.$databank->name.'_2020-10-29t13_00_58p01_001603972858';


        $this->assertFileDoesNotExist($backupfile. '.sql.gz');
        $this->assertFileDoesNotExist($backupfile. '.sql');

        $databank->backupDatabase();

        // Load backup
        $this->assertFileExists($backupfile. '.sql.gz');
        exec("gunzip ".$backupfile.'.sql.gz');

        $actual =  file_get_contents($backupfile . '.sql');

        // Save comparison
        if ($this->overwriteComparison) {
            file_put_contents($expectedDump, $actual);
        } else {
            file_put_contents($expectedDump.'.status', $actual);
        }

        $expected = file_get_contents($expectedDump );

        // Clean content
        $replacements = [
            '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9 ]{2}:[0-9]{2}:[0-9]{2}/' => '#TIME#',
            '/^--.*$/m'=>''
        ];
        $expected = preg_replace(array_keys($replacements),array_values($replacements),$expected);
        $actual = preg_replace(array_keys($replacements),array_values($replacements),$actual);
        unlink($backupfile.'.sql');

        // Compare
        $this->assertTextEquals($expected,$actual);
	}

	/**
	 * Test index method for admin role
	 *
	 * @return void
	 */
	public function testIndex()
	{
		$this->loginUser('admin');
		$this->get("databanks/index");
		$this->assertHtmlEqualsComparison();
	}

	/**
     * Test index method for author role
	 *
	 * @return void
	 */
	public function testIndexAuthor()
	{
		$this->loginUser('author');

	    $this->expectException(ForbiddenException::class);
		$this->get("databanks/index");
	}

    /**
     * Test open method for admin role
     *
     * @return void
     */
    public function testOpen()
    {
        $this->loginUser('admin');

        $this->get('databanks/open/1');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'epi',
            'database' => 'projects',
            'controller' => 'Articles',
            'action' => 'index'
        ]);
    }

    /**
     * Test view method for admin role
     *
     * @return void
     */
    public function testView()
    {
        $this->loginUser('admin');

        $this->get("databanks/view/1");
        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test admin methods for author role
     *
     * @return void
     */
    public function testAuthorRestricted()
    {
        $this->loginUser('author');

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/add");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/delete/1");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/create/1");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/init/1");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/drop/1");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/backup/1");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/select");
    }

    /**
     * Test admin methods for editor role
     *
     * @return void
     */
    public function testEditorRestricted()
    {
        $this->loginUser('editor');

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/add");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/delete/1");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/create/1");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/init/1");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/drop/1");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/backup/1");

        $this->expectException(ForbiddenException::class);
        $this->get("databanks/select");
    }


    /**
     * Test redirect to login for guest role
     *
     * @return void
     */
    public function testGuestRestricted()
    {
        $this->assertRedirectToLogin("databanks/add");
        $this->assertRedirectToLogin("databanks/delete/1");
        $this->assertRedirectToLogin("databanks/create/1");
        $this->assertRedirectToLogin("databanks/init/1");
        $this->assertRedirectToLogin("databanks/drop/1");
        $this->assertRedirectToLogin("databanks/backup/1");
        $this->assertRedirectToLogin("databanks/select");
    }

	/**
	 * Test add method for admin role
     *
     * Tests creation of record, database and folders.
	 *
	 * @return void
	 */
	public function testAdd()
	{
		$this->loginUser('admin');

		// Check that the folder does not exist yet
		$folder = Configure::read('Data.databases') . 'test_newprojects';
        $this->assertDirectoryDoesNotExist($folder);

        // Check that the database does not exist yet
        $databanks = $this->Databanks->getConnections();
        $this->assertNotContains('test_newprojects',$databanks);

		// Check that the databank record does not yet exists
		$count = $this->Databanks->find('all')->where(['id'=>2])->count();
		$this->assertEquals(0,$count);

		// Check add form
		$this->get("databanks/add");

		$this->assertHtmlEqualsComparison();

		// Check redirect after post
		$data = [
			'name' => 'test_newprojects',
			'version' => DATABASE_CURRENT_VERSION,
			'published' => 0
		];
		$this->post('databanks/add', $data);
		$this->assertResponseCode(302);

		// Check that the databank record exists
		$databank = $this->Databanks->get(2);
		$this->saveComparisonJson($databank);
		$this->assertJsonStringEqualsComparison($databank);

	    // Check the folders exist
        $subfolders = ['backup','articles','notes','properties'];
        foreach ($subfolders as $subfolder) {
            $this->assertDirectoryExists($folder . DS .$subfolder);
        }

        // Check that the database exists
        $databanks = $this->Databanks->getConnections();
        $this->assertContains('test_newprojects',$databanks);

        // Check database content
        $this->assertDatabankEqualsDump($databank);
	}

    /**
     * Test delete method for admin role
     *
     * @return void
     */
    public function testDelete()
    {
        $this->loginUser('admin');

        $entity = $this->Databanks->get(1);
        $this->assertNotNull($entity);

        // View page
        $this->get('databanks/delete/1');
        $this->assertHtmlEqualsComparison();

        // Execute delete
        $this->delete('databanks/delete/1');

        // Assert record is deleted
        $deleted = !$this->Databanks->exists(['id' => 1]);
        $this->assertEquals($deleted, true);

        // Assert the database still exists
        $databanks = $this->Databanks->getConnections();
        $this->assertContains('test_projects',$databanks);
    }

	/**
	 * Test create method for admin role
	 *
	 * @return void
	 */
	public function testCreateAndInit()
	{
		$this->loginUser('admin');

		// Create database record
        $data = [
            'name' => 'test_newprojects',
            'version' => DATABASE_CURRENT_VERSION,
            'published' => 0
        ];
        $databank = $this->Databanks->newEntity($data);
        $result =  $this->Databanks->save($databank);
        $this->assertEquals(true,(bool)$result);

        // Test create view
        $this->get("databanks/create/".$databank->id);
        $this->assertHtmlEqualsComparison(true,".content-wrapper");

        // Test database doesn't exist yet
        $databanks = $this->Databanks->getConnections();
        $this->assertNotContains('test_newprojects',$databanks);

		// Create and init database
		$this->post('databanks/create/'.$databank->id);
		$this->assertResponseCode(302);

        // Check database content
        $this->assertDatabankEqualsDump($databank);
        // Reset database because dumping changes the active database
        BaseTable::setDatabase('test_projects');
    }

	/**
	 * Test edit method for admin role
	 *
	 * @return void
	 */
	public function testEdit()
	{
		$this->loginUser('admin');

		$this->get("databanks/edit/1");

		$this->assertHtmlEqualsComparison();

		$this->post('databanks/edit/1',['name'=>'epi_renamed','version'=>DATABASE_CURRENT_VERSION,'published'=>true]);
		$this->assertResponseCode(302);

        $this->get("databanks/view/1");
        $this->assertHtmlEqualsComparison(true,".content-wrapper",'.aftersave');
	}

	/**
	 * Test backup method for admin role
	 *
	 * @return void
	 */
	public function testBackup()
	{
		$this->loginUser('admin');

		// Precheck
        $outputfile = Configure::read('Data.databases') . 'test_projects' . DS
            . 'backup' . DS . 'backup_test_projects_2020-10-29t13_00_58p01_001603972858';
        $this->assertFileDoesNotExist($outputfile).'.sql.gz';
        $this->assertFileDoesNotExist($outputfile).'.sql';

        // Backup page
		$this->get('databanks/backup/1');

		$this->assertHtmlEqualsComparison();

		// Execute backup
		$this->post('databanks/backup/1');
		$this->assertResponseCode(302);
		$this->assertFileExists($outputfile. '.sql.gz');

		// Unzip and compare content
        exec("gunzip ".$outputfile.'.sql.gz');

        $actual =  file_get_contents($outputfile . '.sql');

        if ($this->overwriteComparison) {
            file_put_contents($this->comparisonFile . '.sql', $actual);
        } else {
            file_put_contents($this->comparisonFile . '.sql.status', $actual);
        }

        $expected = file_get_contents($this->comparisonFile . '.sql');

        $replacements = [
            '/[0-9]{4}-[0-9]{2}-[0-9]{2} [ 0-9]{2}:[0-9]{2}:[0-9]{2}/' => '#TIME#',
            '/^--.*$/m' => ''
        ];
        $expected = preg_replace(array_keys($replacements),array_values($replacements),$expected);
        $actual = preg_replace(array_keys($replacements),array_values($replacements),$actual);

        $this->assertTextEquals($expected,$actual);
		unlink($outputfile.'.sql');
	}

	/**
	 * Test import method for admin role
     *
	 * @return void
	 */
	public function testImport()
	{
		$this->loginUser('admin');

		// Test import page
		$this->get('databanks/import/1');

		$this->assertHtmlEqualsComparison();

		// Import script
        $data = ['filename' => 'backup/import.sql'];
        $this->post('databanks/import/1', $data);
        $this->assertRedirect(['controller' => 'Databanks', 'action' => 'view',1]);

        // Check database content
        $databank = $this->Databanks->get(1);
        $this->assertDatabankEqualsDump($databank);

        // Tidy up
        $con = BaseTable::setDatabase('test_projects');
        $con->execute('DROP TABLE IF EXISTS testtable;');
	}

	/**
	 * Test select method for admin role
	 *
	 * @return void
	 */
	public function testSelect()
	{
        $this->loginUser('admin');

        $this->get('databanks/select');

        $compare = $this->saveBodyToComparisonHtml();
        $this->assertTextContains('test_epigraf',$compare);
        $this->assertTextContains('test_projects',$compare);
        $this->assertTextContains('test_public',$compare);
	}

}
