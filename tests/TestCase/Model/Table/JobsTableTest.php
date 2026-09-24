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
use Batch\Model\Jobs\JobExport;
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

        // Get job table
        $this->Jobs = $this->fetchTable('Jobs');
        $this->Jobs::$userRole = 'devel';

        // Init route used for job redirection
        $this->routeBuilder = Router::createRouteBuilder('/');
        Router::defaultRouteClass(DashedRoute::class);
        $this->routeBuilder->connect(
            '/jobs/execute/*',
            ['controller' => 'Jobs', 'action' => 'execute']
        );

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
     * Create and execute job
     *
     * @param array $jobData
     * @param array $jobConfig
     * @param int $limit Limit the record number for each step.
     * @return JobExport
     */
    public function processJob($jobData, $jobConfig, $limit = 1)
    {
        $typedJob = $this->Jobs->newEntity($jobData)->typedJob;

        $typedJob->config = $jobConfig;
        $this->Jobs->save($typedJob);

        // Determine output file
        $outputfile = Configure::read('Data.databases')
            . Databank::addPrefix($typedJob['config']['database'] ?? '') . DS
            . 'jobs' . DS . 'job-' . $typedJob->id . DS . 'job-' . $typedJob->id . '.xml';

        if (file_exists($outputfile)) {
            unlink($outputfile);
        }

        while (!in_array($typedJob->status,['finish','error'])) {

            $typedJob = $this->Jobs->get($typedJob->id)->typedJob;
            $typedJob->limit = $limit;
            $typedJob = $typedJob->execute(0);
            $result = $this->Jobs->save($typedJob);

            $this->assertEquals(true, (bool)$result);
        }
        $this->assertEquals('finish', $typedJob->status);

        return $typedJob;
    }

    /**
     * Test export job with a limit of 1
     *
     * @return void
     */
    public function testArticlesExportJob()
    {

        //Create and execute job
        $typedJob = $this->processJob(
            ['jobtype' => 'export'],
            [
                'database' => 'test_projects',
                'table' => 'articles',
                'selection' => 'filtered',
                'params' => [
                    'sort' => 'signature'
                ],
                'pipeline_tasks' => [
                    [
                        'number' => '1',
                        'type' => 'data_articles',
                        'snippets' => 'indexes,paths,editors,comments',
                        'format' => 'xml'
                    ]
                ]
            ]
        );

        // Check output
        $outputfile = $typedJob->getJobOutputFilePath();
        $comparisonFile = $this->comparisonFile . '.xml';
        if (!file_exists($comparisonFile) || $this->overwriteComparison) {
            copy($outputfile,$comparisonFile);
        }
        $this->assertFileEquals($comparisonFile, $outputfile);
    }

    /**
     * Test export job with a limit of 1
     *
     * @return void
     */
    public function testPropertiesExportJob()
    {
        //Create and execute job
        $typedJob = $this->processJob(
            ['jobtype' => 'export'],
            [
                'database' => 'test_projects',
                'table' => 'properties',
                'selection' => 'filtered',
                'pipeline_tasks' => [
                    [
                        'number' => '1',
                        'type' => 'data_properties',
                        'scope' => 'epithets',
                        'format' => 'xml',
                        'expand' => true
                    ]
                ]
            ]
        );

        // Check job config
        $this->assertJsonStringEqualsComparison($typedJob->config, '.config');

        // Check output
        $outputfile = $typedJob->getJobOutputFilePath();
        $comparisonFile = $this->comparisonFile . '.xml';
        if (!file_exists($comparisonFile) || $this->overwriteComparison) {
            copy($outputfile,$comparisonFile);
        }
        $this->assertFileEquals($comparisonFile, $outputfile);
    }
}
