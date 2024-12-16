<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Core\Configure;
use App\Test\TestCase\AppTestCase;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\RedirectException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * App\Controller\FilesController Test Case
 *
 * @uses \App\Controller\FilesController
 */
class FilesControllerTest extends AppTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'app.Files',
        'app.Users',
        'app.Permissions',
        'app.Pipelines',
        'app.Docs',
        'app.Databanks',
        'plugin.Epi.Token'
    ];

    public $Files = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Files = $this->fetchTable('Files');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->tearDownFileUpload('uploadtest.txt');

        // Rename file backup
        $rootfolder = Configure::read('Data.shared');
        if (file_exists($rootfolder . 'downloads/test_private_backup.doc')) {
            rename(
                $rootfolder . 'downloads/test_private_backup.doc',
                $rootfolder . 'downloads/test_private.doc'
            );
        }

        parent::tearDown();
    }

    /**
     * Test files exists method
     *
     * @return void
     */
    public function testFileExists(): void
    {
        $testfilename = Configure::read('Data.shared'). 'downloadtest.md';
        $this->assertFileExists($testfilename);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexWithId(): void
    {
        $this->loginUser('devel');
        $this->get("/files/index/1");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test index method with path
     *
     * @return void
     */
    public function testIndexWithPath(): void
    {
        $this->loginUser('devel');
        $this->get("/files/index/?path=downloads");

//        $this->assertRedirect(['controller'=>'files','action'=>'index',1]);
//        $this->get("/files/index/1");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test index method create record
     *
     * @return void
     */
    public function testIndexCreateRecord(): void
    {
        $this->markTestIncomplete('Not implemented yet.');

//        $this->loginUser('admin');
//
//        // Create file record
//        $this->get("/files/index?root=shared&path=images");
//
//
//        $this->assertHtmlEqualsComparison(true);
    }

    /**
     * Test index method sort by default
     *
     * @return void
     */
    public function testIndexSortDefault(): void
    {
        $this->loginUser('devel');
        $this->get("/files/index/?path=downloads");


		$this->assertHtmlEqualsComparison(true,"#content");

        $response = $this->_getBodyAsString();
        $start = mb_stripos($response, '<td><span class="ui-icon ui-icon-folder-collapsed"></span> ..</td>');
        $first = mb_stripos($response, '<td class="">test_private.doc</td>');
        $second = mb_stripos($response, '<td class="">test_published.doc</td>');

        $this->assertTrue($first > $start);
        $this->assertTrue($second > $first);
    }


    /**
     * Test index method sort ascending
     *
     * @return void
     */
    public function testIndexSortAsc(): void
    {
        $this->loginUser('devel');
        $this->get("/files/index/?path=downloads&sort=size&direction=asc");


		$this->assertHtmlEqualsComparison(true,"#content");

        $response = $this->_getBodyAsString();
        $start = mb_stripos($response, '<td><span class="ui-icon ui-icon-folder-collapsed"></span> ..</td>');
        $first = mb_stripos($response, '<td class="">test_private.doc</td>');
        $second = mb_stripos($response, '<td class="">test_published.doc</td>');

        $this->assertTrue($first > $start);
        $this->assertTrue($second > $first);
    }

    /**
     * Test index method sort descending
     *
     * @return void
     */
    public function testIndexSortDesc(): void
    {
        $this->loginUser('devel');
        $this->get("/files/index/?path=downloads&sort=size&direction=desc");


		$this->assertHtmlEqualsComparison(true,"#content");

        $response = $this->_getBodyAsString();
        $start = mb_stripos($response, '<td><span class="ui-icon ui-icon-folder-collapsed"></span> ..</td>');
        $first = mb_stripos($response, '<td class="">test_published.doc</td>');
        $second = mb_stripos($response, '<td class="">test_private.doc</td>');

        $this->assertTrue($first > $start);
        $this->assertTrue($second > $first);
    }

    /**
     * Test select method
     *
     * @return void
     */
    public function testSelect(): void
    {
        $this->loginUser('devel');
        $this->get("files/select?path=images&root=shared");

        $this->assertRedirect(['controller'=>'files','action'=>'select',7,'?'=>['basepath'=>'']]);
        $this->get("/files/select/7");


		$this->assertHtmlEqualsComparison();
    }

    /**
     * Test select method
     *
     * @return void
     */
    public function testSelectBase(): void
    {
        $this->loginUser('devel');
        $this->get("files/select?path=images&root=shared&basepath=images");

        $this->assertRedirect(['controller'=>'files','action'=>'select',7,'?'=>['basepath'=>'images']]);
        $this->get("/files/select/7?basepath=images");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test download method for author role
     *
     * @return void
     */
    public function testDownloadWithIdAuthor(): void
    {
        $this->loginUser('author');

        // Private file
        $this->get("/files/download/2");

        $testfilename = $this->comparisonFile .'.doc';
        $this->assertResponseEquals(file_get_contents($testfilename));

        $expected = [
            'Content-Type' => [
                (int) 0 => 'application/msword'
            ],
            'Content-Disposition' => [
                (int) 0 => 'attachment; filename="test_private.doc"'
            ],
            'Content-Transfer-Encoding' => [
                (int) 0 => 'binary'
            ],
            'Accept-Ranges' => [
                (int) 0 => 'bytes'
            ],
            'Content-Length' => [
                (int) 0 => '33379'
            ]
        ];

        $headers = array_intersect_key($this->_response->getHeaders(), $expected);
        $this->assertEquals($expected,$headers);

        // Public file
        $this->get("/files/download/3");
        $this->assertResponseEquals(file_get_contents($testfilename));

        $expected = [
                'Content-Type' => [
                    (int) 0 => 'application/msword'
                ],
                'Content-Disposition' => [
                    (int) 0 => 'attachment; filename="test_published.doc"'
                ],
                'Content-Transfer-Encoding' => [
                    (int) 0 => 'binary'
                ],
                'Accept-Ranges' => [
                    (int) 0 => 'bytes'
                ],
                'Content-Length' => [
                    (int) 0 => '33379'
                ]
            ];
        $headers = array_intersect_key($this->_response->getHeaders(), $expected);
        $this->assertEquals($expected,$headers);

    }

    /**
     * Test download method for guest role
     *
     * @return void
     */
    public function testDownloadWithIdGuest(): void
    {
        //Private
        //$this->expectException(ForbiddenException::class);
        //$this->expectExceptionMessage('Forbidden');
        $this->get("/files/download/2");
        $this->assertRedirect(['controller'=>'Users','action'=>'login',
            '?'=>['redirect'=>'/files/download/2?login=1']]
        );

        // Published
        $this->get("/files/download/3");

        $testfilename = $this->comparisonFile .'.doc';
        $this->assertResponseEquals(file_get_contents($testfilename));

        $expected = [
                'Content-Type' => [
                    (int) 0 => 'application/msword'
                ],
                'Content-Disposition' => [
                    (int) 0 => 'attachment; filename="test_published.doc"'
                ],
                'Content-Transfer-Encoding' => [
                    (int) 0 => 'binary'
                ],
                'Accept-Ranges' => [
                    (int) 0 => 'bytes'
                ],
                'Content-Length' => [
                    (int) 0 => '33379'
                ]
            ];

        $headers = array_intersect_key($this->_response->getHeaders(), $expected);
        $this->assertEquals($expected,$headers);

    }

    /**
     * Test download with path method for author role
     *
     * @return void
     */
    public function testDownloadWithPathAuthor(): void
    {
        $this->loginUser('author');

        // Private file
        $this->get("/files/download?root=shared&path=downloads&filename=test_private.doc");
//        $this->assertRedirect(['controller'=>'Files','action'=>'download',2]);

//        $this->get("/files/download/2");
        $testfilename = $this->comparisonFile .'.doc';
        $this->assertResponseEquals(file_get_contents($testfilename));

        $expected =
            [
                'Content-Type' => [
                    (int) 0 => 'application/msword'
                ],
                'Content-Disposition' => [
                    (int) 0 => 'attachment; filename="test_private.doc"'
                ],
                'Content-Transfer-Encoding' => [
                    (int) 0 => 'binary'
                ],
                'Accept-Ranges' => [
                    (int) 0 => 'bytes'
                ],
                'Content-Length' => [
                    (int) 0 => '33379'
                ]
            ];
        $headers = array_intersect_key($this->_response->getHeaders(), $expected);
        $this->assertEquals($expected,$headers);

    }

    /**
     * Test download with path method for guest role
     *
     * @return void
     */
    public function testDownloadWithPathGuestPrivate(): void
    {
        //Private
        //$this->expectException(ForbiddenException::class);
        //$this->expectExceptionMessage('Forbidden');
        $this->get("/files/download?root=shared&path=downloads&filename=test_private.doc");
//        $this->assertRedirect(['controller'=>'Files','action'=>'download',2]);
//        $this->get('/files/download/2');

        $this->assertRedirect([
            'controller'=>'Users','action'=>'login',
            '?' => ['redirect'=>'/files/download?root=shared&path=downloads&filename=test_private.doc&login=1']
            //'?'=>['redirect'=>'/files/download/2']
            ]
        );
    }

    /**
     * Test download with path method for guest role
     *
     * @return void
     */
    public function testDownloadWithPathGuestPublished(): void
    {
        // Published
        $this->get("/files/download?root=shared&path=downloads&filename=test_published.doc");
        //$this->assertRedirect(['controller'=>'Files','action'=>'download',3]);


        $testfilename = $this->comparisonFile .'.doc';
        $this->assertResponseEquals(file_get_contents($testfilename));

        $expected = [
            'Content-Type' => [
                (int) 0 => 'application/msword'
            ],
            'Content-Disposition' => [
                (int) 0 => 'attachment; filename="test_published.doc"'
            ],
            'Content-Transfer-Encoding' => [
                (int) 0 => 'binary'
            ],
            'Accept-Ranges' => [
                (int) 0 => 'bytes'
            ],
            'Content-Length' => [
                (int) 0 => '33379'
            ]
        ];

        $headers = array_intersect_key($this->_response->getHeaders(), $expected);
        $this->assertEquals($expected,$headers);
    }


    /**
     * Test view method
     *
     * @return void
     */
    public function testView(): void
    {
        $this->loginUser('admin');
        $this->get("files/view/2");

        $this->assertHtmlEqualsComparison();
    }

    /**
     * Test move method
     *
     * @return void
     */
    public function testMove()
    {
        $this->loginUser('devel');
        $this->get("files/move/1");

        $this->assertHtmlEqualsComparison();

        //TODO: post request
    }

    /**
     * Test editfolder method
     *
     * Note: if the test fails, the test data may be in an undefined state.
     * Revert changes in tests/Files/shared to tidy up.
     *
     * @return void
     */
    public function testEditFolder(): void
    {
        $this->loginUser('admin');
        $this->get("files/edit/1");

        $this->assertHtmlEqualsComparison();

        // Test folder exists
        $rootfolder = Configure::read('Data.shared');
        $this->assertFileExists($rootfolder . 'downloads/test_private.doc');

        // Test post request
        $id = 1;
        $data = [
            'id' => $id,
            'name' => 'downloads_renamed'
        ];
        $this->post('files/edit/' . $id, $data);
        $this->assertResponseCode(302);

        // Check if the record was edited
        $record = $this->Files->get($id);
        $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison(json_encode($record));

        $this->assertDirectoryDoesNotExist($rootfolder . 'downloads');
        $this->assertFileExists($rootfolder . 'downloads_renamed/test_private.doc');

        //Revert
        $data = [
            'id' => $id,
            'name' => 'downloads'
        ];
        $this->post('files/edit/' . $id, $data);
        $this->assertDirectoryDoesNotExist($rootfolder . 'downloads_renamed');
        $this->assertFileExists($rootfolder . 'downloads/test_private.doc');
    }

    /**
     * Test editfolder method
     *
     * Renaming a folder should fail, if the directory already exists.
     * Note: if the test fails, the test data may be in an undefined state.
     * Revert changes in tests/Files/shared to tidy up.
     *
     * @return void
     */
    public function testEditFolderFails(): void
    {
        $this->loginUser('admin');

        // Test folder exists
        $rootfolder = Configure::read('Data.shared');
        $this->assertFileExists($rootfolder . 'downloads/test_private.doc');
        $this->assertFileExists($rootfolder . 'images/testbild.png');

        // Test post request
        $id = 1;
        $data = [
            'id' => $id,
            'name' => 'images'
        ];
        $this->post('files/edit/' . $id, $data);

        $this->assertHtmlEqualsComparison();

        // Check if the record was not edited
        $record = $this->Files->get($id);
        $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison(json_encode($record));

        $this->assertFileExists($rootfolder . 'downloads/test_private.doc');
        $this->assertFileExists($rootfolder . 'images/testbild.png');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEditFile(): void
    {
        $this->loginUser('admin');
        $this->get("files/edit/2");

        $this->assertHtmlEqualsComparison();

        // Test file exists
        $rootfolder = Configure::read('Data.shared');
        $this->assertFileExists($rootfolder . 'downloads/test_private.doc');
        $this->assertFileDoesNotExist($rootfolder . 'downloads/test_private_renamed.doc');

        // Test post request
        $id = 2;
        $data = [
            'id' => $id,
            'name' => 'test_private_renamed.doc',
            'description' => 'New world.'
        ];
        $this->post('files/edit/' . $id, $data);
        $this->assertResponseCode(302);

        // Check if the record was edited
        $record = $this->Files->get($id);
        $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison(json_encode($record));

        // Check the file was renamed
        $this->assertFileDoesNotExist($rootfolder . 'downloads/test_private.doc');
        $this->assertFileExists($rootfolder . 'downloads/test_private_renamed.doc');

        // Revert
        $data = [
            'id' => $id,
            'name' => 'test_private.doc'
        ];
        $this->post('files/edit/' . $id, $data);
        $this->assertFileExists($rootfolder . 'downloads/test_private.doc');
        $this->assertFileDoesNotExist($rootfolder . 'downloads/test_private_renamed.doc');

    }

    /**
     * Test display with path method for admin role
     *
     * @return void
     */
    public function testDisplayWithPath(): void {

        $this->loginUser('admin');

        // Create file record
        $this->get("/files/index?root=shared&path=images");

        // Get image file
        $this->get("/files/display?root=shared&path=images&filename=testbild.png");
//        $this->assertRedirect(['controller'=>'Files','action'=>'display',8]);
//        $this->get('/files/display/8');

        $this->assertFileResponseEqualsComparison('.png');
    }

    /**
     * Test clearthumbs method
     *
     * @return void
     */
    public function testClearthumbs(): void
    {
        $this->markTestIncomplete('Not implemented yet.');

//        $this->loginUser('admin');
//
//        // Create file record and thumb
//        $this->get("/files/index?root=shared&path=images");
//
//        $thumbname = TMP . 'thumbs' . DS . $imagefile . '_'  . $size . '_notfound.png';
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete(): void
    {
        $this->loginUser('admin');

        // Backup file
        $rootfolder = Configure::read('Data.shared');
        copy($rootfolder . 'downloads/test_private.doc', $rootfolder . 'downloads/test_private_backup.doc');
        $this->assertFileExists($rootfolder . 'downloads/test_private_backup.doc');
        $this->assertFileExists($rootfolder . 'downloads/test_private.doc');

        // File is in database?
        $record = $this->Files->get(2);
        $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison(json_encode($record));

        $this->get("files/delete/2");
		$this->assertHtmlEqualsComparison();

        // A delete request deletes the entry
        $this->delete('files/delete/2');
        $this->assertResponseCode(200);
        $this->assertResponseEqualsComparison( 'after','.content-wrapper',);


        // Check whether the file is not in the database anymore
        $this->expectException(RecordNotFoundException::class);
        $this->Files->get(2);

        // Check whether the files was actually deleted, but not the backup
        $this->assertFileExists($rootfolder . 'downloads/test_private_backup.doc');
        $this->assertFileDoesNotExist($rootfolder . 'downloads/test_private.doc');

        //Revert
        rename(
            $rootfolder . 'downloads/test_private_backup.doc',
            $rootfolder . 'downloads/test_private.doc'
        );
        $this->assertFileDoesNotExist($rootfolder . 'downloads/test_private_backup.doc');
        $this->assertFileExists($rootfolder . 'downloads/test_private.doc');
    }

    /**
     * Deleting root folders should fail.
     *
     * @return void
     */
    public function testDeleteFails(): void
    {
        $this->loginUser('admin');

        // Folder exists?
        $rootfolder = Configure::read('Data.shared');
        $this->assertDirectoryExists($rootfolder);

        // Folder is in database?
        $record = $this->Files->get(4);
        $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison(json_encode($record));

        // Try to delete the root folder
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('You must not delete the root.');
        $this->delete('files/delete/4');

        // Folder is in database?
        $record = $this->Files->get(4);
        $this->saveComparisonJson($record);
        $this->assertJsonStringEqualsComparison(json_encode($record));

        // Check whether the folder still exists
		$this->assertDirectoryExists($rootfolder);
    }

    /**
     * Test newfolder method
     *
     * @return void
     */
    public function testNewfolder(): void
    {
        $this->loginUser('admin');
        $this->get("files/newfolder");

        $this->assertHtmlEqualsComparison();

        // Assert the folder does not exist yet
        $rootfolder = Configure::read('Data.shared');
        $this->assertDirectoryDoesNotExist($rootfolder . 'newfolder');

        // Test post request
        $data =['name'=>'newfolder'];

        $this->post('files/newfolder', $data);
        $this->assertResponseCode(302);

        // Assert the folder exists
        $this->assertDirectoryExists($rootfolder . 'newfolder');

        // Assert the folder record exists
        $records = $this->Files
            ->find('all')
            ->where(['root'=>'shared','path'=>'','name'=>'newfolder','isfolder'=>1])
            ->toArray();
        $this->saveComparisonJson($records);
        $this->assertJsonStringEqualsComparison(json_encode($records));

        $folder = $this->Files->get($records[0]['id']);
        $folder->delete();
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
        $uploadedfilename1 = ROOT . DS . 'tests' . DS . 'Files' . DS . 'shared' . DS . 'uploadtest.txt';
        $uploadedfilename2 = ROOT . DS . 'tests' . DS . 'Files' . DS . 'shared' . DS . 'uploadtest_1.txt';

        // Check than non-ajax request is redirected
        $data = ['FileData' => ['file' => $uploadedFile]];
        $this->post('/files/upload?root=root&path=shared', $data);
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
        $this->post('/files/upload?root=root&path=shared', $data);
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
        $this->post('/files/upload?root=root&path=shared', $data);

        $this->assertFileExists($uploadedfilename2);
        $this->assertFileEquals($testfilename,$uploadedfilename2);

        // Tidy up
        unlink($uploadedfilename1);
        unlink($uploadedfilename2);
    }

}
