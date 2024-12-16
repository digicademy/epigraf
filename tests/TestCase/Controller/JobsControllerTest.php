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

use App\Model\Entity\Databank;
use App\Utilities\Files\Files;
use Cake\Core\Configure;
use Cake\Http\Exception\UnauthorizedException;
use App\Test\TestCase\AppTestCase;

/**
 * App\Controller\JobsController Test Case
 *
 * @uses \App\Controller\JobsController
 */
class JobsControllerTest extends AppTestCase
{

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

    /**
     * setup method
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex() {
        $this->loginUser('admin');
        $this->get("/jobs/index");
		$this->assertHtmlEqualsComparison();
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView() {

        // TODO: JSON wird im GitLab Runner in anderer Reihenfolge ausgegeben,
        //      Keine Ahnung warum. JSON decode / encode schon geprüft, alles in Ordnung
        //      Irgendwo im Model? Man müsste im Testsystem das Konstruieren des Entity debuggen
        //      Denn die Reihenfolge ist anscheinend im Entity falsch

        $this->loginUser('admin');
        $this->get("/jobs/view/1");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test download method
     *
     * @return void
     */
    public function testDownload()
    {
        $this->loginUser('admin');
        $this->get("/export?scope=article&database=projects&project=1&articles=1");
        $this->assertResponseEqualsComparison();
    }

    /**
     * Test article export method
     *
     * @return void
     */
    public function testExportArticle()
    {
        $this->loginUser('author');
        $this->get("/export?scope=article&database=projects&project=1&articles=1");
        $this->assertResponseEqualsComparison();
    }

    /**
     * Test book export method
     *
     * @return void
     */
    public function testExportBook()
    {
        $this->loginUser('author');
        $this->get("/export?scope=book&database=projects&project=1&articles=1");
        $this->assertResponseEqualsComparison();
    }

    /**
     * Test authentification for export
     *
     * @return void
     */
    public function testExportNoAuth()
    {
        $this->get("/export?scope=article&database=projects&project=1&articles=1");
        $this->assertRedirect(['controller' => 'Users', 'action' => 'login', '?' => ['redirect' => '/export?scope=article&database=projects&project=1&articles=1']]);
    }

    /**
     * Test add default
     *
     * @return void
     */
    public function testAddDefault()
    {
        $this->loginUser('admin');
        $this->get("export?database=projects");
        $this->assertResponseEqualsComparison();
    }


    /**
     * Test add article
     *
     * @return void
     */
    public function testAddArticle()
    {
        $this->loginUser('admin');
        $this->get("export?database=projects&scope=article");
        $this->assertResponseEqualsComparison();
    }

    /**
     * Test add book
     *
     * @return void
     */
    public function testAddBook()
    {
        $this->loginUser('admin');
        $this->get("export?database=projects&scope=book");
        $this->assertResponseEqualsComparison();
    }

    /**
     * Test token redirect
     *
     * @return void
     */
    public function testExportTokenRedirect()
    {
        $this->get("/export?token=TESTTOKENAUTHOR&scope=article&database=projects&project=1&articles=1");
        $this->assertRedirect([
            'controller' => 'Users',
            'action' => 'login',
            '?' => [
                'redirect' => '/export?scope=article&database=projects&project=1&articles=1&login=1',
                'token' => false
            ]
        ]);
    }

    /**
     * Test wrong token redirect
     *
     * @return void
     */
    public function testExportWrongToken()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Unauthorized');
        $this->get("/export?token=TESTWRONGAUTHOR&scope=article&database=projects&project=1&articles=1");
    }

    /**
     * Test pipeline
     *
     * @param string $params Query parameters as string
     * @param string $downloadFile File name
     * @param array $downloadHeaders Expected response headers
     *
     * @return void
     * @throws \Exception
     */
    protected function _testPipeline($params, $downloadFile, $downloadHeaders)
    {
        // TODO: über get request einsteigen, nicht über post request.
        //       dann post request in stufe 2
        //       dann beginnt bereits die AJAX-SChleife mit JSON requests
        //       geht aber auch erstmal so

        // Remove old export folder if present
        $outputfolder = Configure::read('Data.databases') . 'test_projects' . DS . 'jobs' . DS . 'job_3' . DS;
        if (is_dir($outputfolder)) {
            Files::removeFolder($outputfolder);
        }

        // 1. CREATE JOB

        // Check only fixture jobs exist yet
        $this->assertEquals(2, $this->fetchTable("Jobs")->find()->count());

        // Create new job
        $data = include $this->testdataFile;
        $this->loginUser('author');

        // Pipeline #19 for article export
        $this->post("/export?" . $params, $data);
        $this->assertRedirect('/jobs/execute/3?database=projects&close=0');

        // Check if job exists
        $jobs = $this->fetchTable("Jobs")->find()->disableHydration()->toArray();
        $this->assertCount(3, $jobs);

        // Compare content of job
        $job = $jobs[2];
        $this->assertArrayEqualsComparison($job, "/export_job_1.php");

        // 2. START JOB
        $this->loginUser('author');
        $this->get('/jobs/execute/'.$job['id'].'?timeout=-1');


        // Compare response to HTML snippets
        $this->assertHtmlEqualsComparison(true,".content-wrapper","/export_job_2");

        $jobs = $this->fetchTable("Jobs")->find()->disableHydration()->toArray();
        $this->assertCount(3, $jobs);

        $job = $jobs[2];
        $this->assertArrayEqualsComparison($job, "/export_job_3.php");

        // 3. EXECUTE JOB
        //TODO: Use AJAX requests, otherwise download is immediately triggered in JobsController
        //      Or better implement a new endpoint for downloading results?
        for($i = (int)$job['progress']; $i < (int)$job['progressmax']; $i ++) {
            $this->configRequest(['headers' => ['Accept' => 'application/json']]);
            $this->loginUser('author');
            $this->post('/jobs/execute/3?timeout=-1');

            $response = $this->_getBodyAsString();
            $response = json_decode($response, true);
            $i = $response['job']['progress'] ?? $i;

            if (($response['job']['status'] ?? '') === 'download') {
                $i = $response['job']['progressmax'] ?? $i;
            }

            // Determine output file
            $outputfolder = 'job_3';
            $database =  $response['job']['config']['database'] ?? '';
            $this->assertEquals('projects',$database);
            $outputfile = Configure::read('Data.databases') . Databank::addPrefix($database) . DS;
            $outputfile .= 'jobs' . DS . $outputfolder . DS . $outputfolder . '.xml';

            copy($outputfile,$this->comparisonFile.'/progress_'.$i.'.status');

            // Check errors
            $error = $response['job']['error'] ?? '';
            if ($error) {
                echo "{$response['job']['config']['database']} {$response['job']['config']['model']} {$error}" . "\n";
            } else {
                echo "{$response['job']['config']['database']} {$response['job']['config']['model']}";
            }
            $this->assertEquals('',$error);
        }

        // Check progress
        $this->assertEquals(
            $response['job']['progressmax'] - 1,
            $response['job']['progress'],
            'Job not completed or progress calculation failed.'
        );

        $database =  $response['job']['config']['database'];
        $foldername =  'job_3';
        $outputfolder = Configure::read('Data.databases') . Databank::addPrefix($database) . DS . 'jobs' . DS . $foldername . DS;
        $outputfile = $outputfolder . $foldername . '.xml';

        // Clean for test
        Files::replaceContent($outputfile, ['/folder="[^"]+"/' => 'folder="job_3"']);

        $copyto = $this->comparisonFile. '/' . $downloadFile;
        $copyto .= $this->overwriteComparison ? '' : '.status';
        copy($outputfile,$copyto);


        $this->assertFileEqualsCanonicalizing(
            $this->comparisonFile. '/' . $downloadFile,
            $outputfile
        );

        // 3. DOWNLOAD
        $this->loginUser('author');
        $this->get('/jobs/execute/3?timeout=-1');

        $responseHeaders = $this->_response->getHeaders();
        unset($responseHeaders['x-frame-options']);
        $this->assertSame($downloadHeaders, $responseHeaders);

        $compare = file_get_contents($this->comparisonFile. '/' . $downloadFile);
        $this->assertResponseEquals($compare);

        // 4. CLEAN UP
        Files::removeFolder($outputfolder);
        $this->assertFileDoesNotExist($outputfolder);
    }

    /**
     * Test export article pipeline
     *
     * @return void
     * @throws \Exception
     */
    public function testExportExecuteArticle()
    {
        $this->_testPipeline(
            'database=projects&project=1&articles=1&pipeline=19',
            'result.doc',
            [
                'Content-Type' => [
                    (int) 0 => 'application/msword'
                ],
                'Content-Disposition' => [
                    (int) 0 => 'attachment; filename="job_3.doc"'
                ],
                'Content-Transfer-Encoding' => [
                    (int) 0 => 'binary'
                ],
                'Accept-Ranges' => [
                    (int) 0 => 'bytes'
                ],
                'Content-Length' => [
                    (int) 0 => '66823'
                ]
            ]
        );
    }

    /**
     * Test export book pipeline
     *
     * @return void
     * @throws \Exception
     */
    public function testExportExecuteBook()
    {
        $this->_testPipeline(
            'database=projects&project=1&articles=1&pipeline=21',
            'result.doc',
            [
                'Content-Type' => [
                    (int) 0 => 'application/msword'
                ],
                'Content-Disposition' => [
                    (int) 0 => 'attachment; filename="job_3.doc"'
                ],
                'Content-Transfer-Encoding' => [
                    (int) 0 => 'binary'
                ],
                'Accept-Ranges' => [
                    (int) 0 => 'bytes'
                ],
                'Content-Length' => [
                    (int) 0 => '38633'
                ]
            ]
        );
    }

    /**
     * Test export XML pipeline
     *
     * @return void
     * @throws \Exception
     */
    public function testExportExecuteXml()
    {
        $this->_testPipeline(
            'database=projects&project=1&articles=1&pipeline=16',
            'result.xml',
            [
                'Content-Type' => [
                    (int) 0 => 'application/xml; charset=UTF-8'
                ],
                'Content-Disposition' => [
                    (int) 0 => 'attachment; filename="job_3.xml"'
                ],
                'Content-Transfer-Encoding' => [
                    (int) 0 => 'binary'
                ],
                'Accept-Ranges' => [
                    (int) 0 => 'bytes'
                ],
                'Content-Length' => [
                    (int) 0 => '446196'
                ]
            ]
        );
    }
}
