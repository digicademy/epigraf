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

use App\Model\Table\BaseTable;
use App\Test\TestCase\AppTestCase;
use Cake\Console\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Datasource\ConnectionManager;

class DatabaseCommandTest extends AppTestCase
{
    use ConsoleIntegrationTestTrait;

    public function testInitProjectDatabase()
    {
        $this->useCommandRunner();

        $articlesTable =  $this->fetchTable('Epi.Articles');
        $rows = $articlesTable->find('all')->where(['name LIKE' => '%pirates%'])->count();
        $this->assertEquals(0, $rows);


        // Run the command
        $this->exec('database init --database test_mytest --preset movies --connection test_projects --drop');

        $this->assertOutputContains('Project database test_mytest added');
        $this->assertOutputContains('The preset movies was imported into test_mytest');
        $this->assertExitCode(Command::CODE_SUCCESS);

        // Check if the database was created
        $oldTable = BaseTable::getDatabaseName('test_projects');
        $connection = BaseTable::setDatabase('test_mytest', 'test_projects');

        $connection->getDriver()->connect();
        $collection = $connection->getSchemaCollection();
        $tables = $collection->listTablesWithoutViews();
        $connection->getDriver()->disconnect();

        $this->assertEquals([
            'articles',
            'files',
            'footnotes',
            'grains',
            'items',
            'links',
            'locktable',
            'meta',
            'notes',
            'projects',
            'properties',
            'sections',
            'token',
            'types',
            'users'
        ], $tables);

        // Check content
        $articlesTable =  $this->fetchTable('Epi.Articles');
        $rows = $articlesTable->find('all')->where(['name LIKE' => '%pirates%'])->count();
        $this->assertEquals(1, $rows);

        // Reset
        BaseTable::setDatabase($oldTable);

    }
}
