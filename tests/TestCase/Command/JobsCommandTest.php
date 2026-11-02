<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Test\TestCase\Command;

use App\Model\Entity\Databank;
use App\Test\TestCase\AppTestCase;
use App\Utilities\Files\Files;
use Cake\Console\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;

class JobsCommandTest extends AppTestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Temporary import file
     *
     * @var string
     */
    public $testFile = '';

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Jobs',
        'app.Pipelines',
        'app.Users',
        'app.Permissions',
        'app.Databanks'
    ];

    /**
     * Remove temporary test file
     *
     * @return void
     */
    public function tearDown(): void
    {
        if (!empty($this->testFile) && file_exists($this->testFile)) {
            unlink($this->testFile);
        }
        parent::tearDown();
    }

    /**
     * Test adding an import job
     *
     * @return void
     */
    public function testAddImportJob()
    {
        $this->useCommandRunner();

        // Create a test import file
        $folder = Configure::read('Data.databases') . Databank::addPrefix('test_projects') . DS . 'import' . DS;
        Files::createFolder($folder, true);
        $this->testFile = $folder . 'test-import.csv';
        file_put_contents($this->testFile, "id,name\n1,Test article\n");

        // Run the command
        $jobConfig = '{"table":"articles","type":"import", "database":"test_projects", "inputpath":"' . $this->testFile . '"}';
        $this->exec("jobs add --type import --config='" . $jobConfig . "'");

        $this->assertOutputContains('Created job');
        $this->assertExitCode(Command::CODE_SUCCESS);

        $job = $this->fetchTable('Jobs')->find()->orderDesc('id')->first();
        $this->assertEquals('import', $job->jobtype);
        $this->assertEquals('test_projects', $job->config['database']);
        $this->assertEquals($this->testFile, $job->config['inputpath']);
        $this->assertEquals('articles', $job->config['table']);
        $this->assertEquals('admin', $job->config['user_role']);
    }

    /**
     * Test adding an export job by type and payload
     *
     * @return void
     */
    public function testAddExportJob()
    {
        $this->useCommandRunner();

        $jobConfig = '{"table":"articles","type":"export", "database":"test_projects", "selection":"filtered", "params": {"sort":"signature"}}';
        $this->exec("jobs add --type export --config='" . $jobConfig . "'");

        $this->assertOutputContains('Created job');
        $this->assertExitCode(Command::CODE_SUCCESS);

        $job = $this->fetchTable('Jobs')->find()->orderDesc('id')->first();
        $this->assertEquals('export', $job->jobtype);
        $this->assertEquals('test_projects', $job->config['database']);
        $this->assertEquals('filtered', $job->config['selection']);
        $this->assertEquals('signature', $job->config['params']['sort']);
        $this->assertEquals('articles', $job->config['table']);
    }
}
