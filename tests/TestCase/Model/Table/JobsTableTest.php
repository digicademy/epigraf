<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
namespace App\Test\TestCase\Model\Table;

use App\Model\Entity\Databank;
use App\Model\Entity\Job;
use App\Test\TestCase\AppTestCase;
use Cake\Core\Configure;
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

/**
 * App\Model\Table\JobsTable Test Case
 */
class JobsTableTest extends AppTestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\JobsTable
     */
    public $Jobs;

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

    public $dumps = [
        'test_projects' => 'test_projects.sql'
    ];


    public RouteBuilder $routeBuilder;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

//        $config = TableRegistry::getTableLocator()->exists('Jobs') ? [] : ['className' => JobsTable::class];
        $this->Jobs = $this->fetchTable('Jobs');
        $this->Jobs::$userRole = 'devel';

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
     * Test export job with a limit of 1
     *
     * @return void
     */
    public function testExportJob()
    {

        // Init route used for job redirection
        $this->routeBuilder = Router::createRouteBuilder('/');
        Router::defaultRouteClass(DashedRoute::class);

        $this->routeBuilder->connect(
            '/jobs/execute/*',
            ['controller' => 'Jobs', 'action' => 'execute']
        );

        //Create job
        /** @var Job $job */
        $params = [
            'database' => 'test_projects',
            'selection' => 'filtered',
            'sort' => 'signature'
        ];
        $typedJob = $this->Jobs->newEntity(['jobtype' => 'export'])->typedJob;

        $typedJob = $typedJob->patchExportOptions($params);
        $typedJob->config['pipeline_tasks'] = [
            [
                'number' => '1',
                'type' => 'data_articles',
                'snippets' => 'indexes,paths,editors,comments',
                'format' => 'xml'
            ]
        ];

        $this->Jobs->save($typedJob);

        // Determine output file
        $outputfile = Configure::read('Data.databases')
            . Databank::addPrefix($typedJob['config']['database'] ?? '') . DS
            . 'jobs' . DS . 'job_' . $typedJob->id . DS . 'job_' . $typedJob->id . '.xml';

        if (file_exists($outputfile)) {
            unlink($outputfile);
        }


        while (!in_array($typedJob->status,['finish','error'])) {

            $typedJob = $this->Jobs->get($typedJob->id)->typedJob;
            $typedJob->limit = 1;
            $typedJob = $typedJob->execute(0);
            $result = $this->Jobs->save($typedJob);

            $this->assertEquals(true, (bool)$result);
        }
        $this->assertEquals('finish', $typedJob->status);

        // Check output
        $comparisonFile = $this->comparisonFile . '.xml';
        if (!file_exists($comparisonFile) || $this->overwriteComparison) {
            copy($outputfile,$comparisonFile);
        }
        $this->assertFileEquals($comparisonFile, $outputfile);
    }
}
