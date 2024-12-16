<?php
declare(strict_types=1);

namespace Epi\Test\TestCase\Controller;

use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Controller\FilesController Test Case
 *
 * @uses \Epi\Controller\FilesController
 */
class FilesControllerTest extends EpiTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Databanks',

        'app.Users',
        'app.Permissions',
        'app.Jobs',
        'app.Pipelines',
        'app.Docs',

        'plugin.Epi.Users',
        'plugin.Epi.Files',
        'plugin.Epi.Meta',
        'plugin.Epi.Token',
    ];

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->tearDownFileUpload('uploadtest.txt');
        parent::tearDown();
    }

     /**
     * Test whether comparison file exists
     *
     * @return void
     */
    public function testFileExists(): void
    {
        $testfilename = $this->comparisonFile .'.doc';
        $this->assertFileExists($testfilename);
    }

    /**
     * Test index with id (HTML file)
     *
     * @return void
     */
    public function testIndexWithId(): void
    {
        $this->loginUser('author');
        $this->get("/epi/projects/files/index/1");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test index with path (HTML file)
     *
     * @return void
     */
    public function testIndexWithPath(): void
    {
        $this->loginUser('author');
        $this->get("/epi/projects/files/index/?path=downloads");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test download with id
     *
     * @return void
     */
    public function testDownloadWithId(): void
    {
        $this->get("/epi/projects/files/download/2?token=TESTTOKENAUTHOR");
        $this->assertResponseOk();

        $testfilename = $this->comparisonFile .'.doc';
        $this->assertResponseEquals(file_get_contents($testfilename));

       $expected =
            [
                'Content-Type' => [
                    0 => 'application/msword'
                ],
                'Content-Disposition' => [
                    0 => 'attachment; filename="test.doc"'
                ],
                'Content-Transfer-Encoding' => [
                    0 => 'binary'
                ],
                'Accept-Ranges' => [
                    0 => 'bytes'
                ],
                'Content-Length' => [
                    0 => '33379'
                ]
            ];
        $headers = array_intersect_key($this->_response->getHeaders(), $expected);
        $this->assertEquals($expected,$headers);
    }

    /**
     * Test download with path
     *
     * @return void
     */
    public function testDownloadWithPath(): void
    {
        $this->get("/epi/projects/files/download?token=TESTTOKENAUTHOR&path=downloads&filename=test.doc");
        $this->assertResponseOk();

//        $this->assertRedirect(['controller'=>'Files','action'=>'download',2,
//            '?'=>['token'=>'TESTTOKENAUTHOR']]);
//
//        $this->get("/epi/projects/files/download/2?token=TESTTOKENAUTHOR");

        $testfilename = $this->comparisonFile .'.doc';
        $this->assertResponseEquals(file_get_contents($testfilename));

        $expected = [
                'Content-Type' => [
                    0 => 'application/msword'
                ],
                'Content-Disposition' => [
                    0 => 'attachment; filename="test.doc"'
                ],
                'Content-Transfer-Encoding' => [
                    0 => 'binary'
                ],
                'Accept-Ranges' => [
                    0 => 'bytes'
                ],
                'Content-Length' => [
                    0 => '33379'
                ]
            ];

        $headers = array_intersect_key($this->_response->getHeaders(), $expected);
        $this->assertEquals($expected,$headers);

    }

    /**
     * Test view method (HTML file)
     *
     * @return void
     */
    public function testView()
    {
        $this->loginUser('author');
        $this->get('epi/projects/files/view/2');

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test display method (HTML file)
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->loginUser('author');
        $this->get('epi/projects/files/display/2');

        $this->assertHtmlEqualsComparison(true,'');
    }

    /**
     * Test upload method
     *
     * @return void
     */
    public function testUpload(): void
    {
        $this->loginUser('admin');

        $testfilename = $this->testdataFile.'.txt';
        $uploadedFile = $this->prepareFileUpload($testfilename,'uploadtest.txt','text/plain');
        $uploadedfilename1 = ROOT . DS . 'tests/Files/databases/test_projects/notes' . DS . 'uploadtest.txt';
        $uploadedfilename2 = ROOT . DS . 'tests/Files/databases/test_projects/notes' . DS . 'uploadtest_1.txt';

        // Check than non-ajax request is redirected
        $data = ['FileData' => ['file' => $uploadedFile]];
        $this->post('/epi/projects/files/upload?root=root&path=notes', $data);
        $this->assertResponseCode(302);
        $this->assertFileDoesNotExist($uploadedfilename1);
        //$this->assertRedirect('/files?root=root&path=shared');

        // Use ajax request
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]
        ]);
        $this->post('/epi/projects/files/upload?root=root&path=notes', $data);
        $this->assertFileExists($uploadedfilename1);
        $this->assertFileEquals($testfilename,$uploadedfilename1);

        // Check first file is not overwritten
        $this->tearDownFileUpload('uploadtest.txt');
        $uploadedFile = $this->prepareFileUpload($testfilename,'uploadtest.txt','text/plain');
        $data = ['FileData' => ['file' => $uploadedFile]];

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]
        ]);
        $this->post('/epi/projects/files/upload?root=root&path=notes', $data);

        $this->assertFileExists($uploadedfilename2);
        $this->assertFileEquals($testfilename,$uploadedfilename2);

        // Tidy up
        unlink($uploadedfilename1);
        unlink($uploadedfilename2);
    }

    /**
     * Test NewFolder method (HTML file)
     *
     * @return void
     */
    public function testNewFolder()
    {
        $this->loginUser('author');
        $this->get('epi/projects/files/newfolder');

        $this->assertHtmlEqualsComparison();

        //TODO: post request
    }

    /**
     * Test edit method (HTML file)
     *
     * @return void
     */
    public function testEdit()
    {
        $this->loginUser('author');
        $this->get('epi/projects/files/edit/1');

        $this->assertHtmlEqualsComparison();

        //TODO: post request
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->loginUser('author');
        $this->get('epi/projects/files/delete?root=root&path=downloads&filename=test.doc');

		$this->assertRedirect('epi/projects/files/delete/2?basepath=');
        //$this->assertHtmlEqualsComparison();

        //TODO: post request
    }
}

