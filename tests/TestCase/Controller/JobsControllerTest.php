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
use Authentication\Authenticator\UnauthenticatedException;
use Cake\Core\Configure;
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
     * Test article export method
     *
     * @return void
     */
    public function testExportArticle()
    {
        $this->loginUser('author');

        $this->get("/export?scope=article&database=projects&project=1&articles=1");
        $this->assertRedirect('/epi/projects/articles/export?scope=article&project=1&articles=1');

        $this->get("/epi/projects/articles/export?articles=1&scope=article&projects=1");
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
        $this->assertRedirect('/epi/projects/articles/export?scope=book&project=1&articles=1');

        $this->get("/epi/projects/articles/export?articles=1&scope=book&projects=1");
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
        $this->assertRedirectEquals(['controller' => 'Users', 'action' => 'login', '?' => ['redirect' => '/export?scope=article&database=projects&project=1&articles=1']]);
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
        $this->assertRedirect('/epi/projects/articles/export');
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
        $this->assertRedirect('/epi/projects/articles/export?scope=article');

        $this->get('/epi/projects/articles/export?scope=article');
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
        $this->assertRedirect('/epi/projects/articles/export?scope=book');

        $this->get('/epi/projects/articles/export?scope=book');
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
        $this->assertRedirectEquals([
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
        $this->expectException(UnauthenticatedException::class);
        $this->expectExceptionMessage('Authentication is required to continue');
        $this->get("/export?token=TESTWRONGAUTHOR&scope=article&database=projects&project=1&articles=1");
    }

    /**
     * Test pipeline
     *
     * @param string $params Query parameters as string
     * @param string $compareFile Name of the file to compare
     * @param string $downloadFile Download file name
     * @param string $downloadSize Size of the download file
     * @param string $contentType Content type of the download file
     *
     * @return void
     * @throws \Exception
     */
    protected function _testPipeline($database, $params, $compareFile, $downloadFile, $downloadSize, $contentType)
    {
        // TODO: über get request einsteigen, nicht über post request.
        //       dann post request in stufe 2
        //       dann beginnt bereits die AJAX-SChleife mit JSON requests
        //       geht aber auch erstmal so

        // Remove old export folder if present
        $outputfolder = Configure::read('Data.databases') . 'test_projects' . DS . 'jobs' . DS . 'job-3' . DS;
        if (is_dir($outputfolder)) {
            Files::delete($outputfolder);
        }

        // 1. CREATE JOB

        // Check only fixture jobs exist yet
        $this->assertEquals(2, $this->fetchTable("Jobs")->find()->count());

        // Create new job
        $data = include $this->testdataFile;
        $this->loginUser('author');

        //$this->post("/export?" . $params, $data);
        $this->post('/epi/' . $database . '/articles/export?' . $params, $data);
        $this->assertRedirect('/jobs/execute/3?database=' . $database . '&close=0');

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

            // Determine output file
            $outputfolder = 'job-3';
            $database =  $response['job']['config']['database'] ?? '';
            $this->assertEquals('test_projects', $database);
            $outputfile = Configure::read('Data.databases') . Databank::addPrefix($database) . DS;
            $outputfile .= 'jobs' . DS . $outputfolder . DS . $outputfolder . '.xml';

            copy($outputfile,$this->comparisonFile.'/progress_'.$i.'.status');

            // Check errors
            $error = $response['job']['error'] ?? '';
            if ($error) {
                echo "{$response['job']['config']['database']} {$response['job']['config']['table']} {$error}" . "\n";
            } else {
                echo "{$response['job']['config']['database']} {$response['job']['config']['table']}";
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
        $foldername =  'job-3';
        $outputfolder = Configure::read('Data.databases') . Databank::addPrefix($database) . DS . 'jobs' . DS . $foldername . DS;
        $outputfile = $outputfolder . $foldername . '.xml';

        // Clean for test
        Files::replaceContent($outputfile, ['/folder="[^"]+"/' => 'folder="job-3"']);

        $copyto = $this->comparisonFile. '/' . $compareFile;
        $copyto .= $this->overwriteComparison ? '' : '.status';
        copy($outputfile,$copyto);


        $this->assertFileEqualsCanonicalizing(
            $this->comparisonFile. '/' . $compareFile,
            $outputfile
        );

        // 3. DOWNLOAD
        $downloadUrl = '/jobs/execute/3?download=' . $downloadFile .'&force=1';

        $this->loginUser('author');
        $this->get('/jobs/execute/3?timeout=-1');
        $responseHeaders = $this->_response->getHeaders();
        $this->assertEquals('http://localhost' . $downloadUrl, $responseHeaders['location'][0]);

        $downloadHeaders = [
            'Content-Type' => [
                (int) 0 => $contentType
            ],
            'Content-Disposition' => [
                (int) 0 => 'attachment; filename="' . $downloadFile . '"'
            ],
            'Content-Transfer-Encoding' => [
                (int) 0 => 'binary'
            ],
            'Accept-Ranges' => [
                (int) 0 => 'bytes'
            ],
            'Content-Length' => [
                (int) 0 => $downloadSize
            ]
        ];

        $this->get($downloadUrl);
        $responseHeaders = $this->_response->getHeaders();
        unset($responseHeaders['x-frame-options']);
        $this->assertSame($downloadHeaders, $responseHeaders);

        $compare = file_get_contents($this->comparisonFile. '/' . $compareFile);
        $this->assertResponseEquals($compare);

        // 4. CLEAN UP
        Files::delete($outputfolder);
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
            'projects',
            'projects=1&articles=1&pipeline=19&sort=location',
            'result.doc',
            'job-3.doc',
            '67069',
            'application/msword'

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
            'projects',
            'projects=1&articles=1&pipeline=21',
            'result.doc',
            'job-3.doc',
            '33743',
            'application/msword'
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
            'projects',
            'projects=1&articles=1&pipeline=16',
            'result.xml',
            'job-3.xml',
            '451550',
            'application/xml; charset=UTF-8'
        );
    }
}
