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

namespace Files\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Files\Controller\Component\FilesRequestComponent;

/**
 * Files\Controller\Component\FilesRequestComponent Test Case
 */
class FilesRequestComponentTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var string[]
     */
    public $fixtures = [
        'app.Files',
    ];

    /**
     * Test subject
     *
     * @var \Files\Controller\Component\FilesRequestComponent
     */
    protected $FilesRequest;

    /**
     * Setup method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadPlugins(['Files']);

        $controller = new Controller();
//        $controller->loadModel('Files');
        $controller->mounts = ['shared'];

        $registry = new ComponentRegistry($controller);

        $mounts = [
            'shared' => Configure::read('Data.shared'),
            'root' => Configure::read('Data.root')
        ];
        $mounts = array_intersect_key($mounts, array_flip($controller->mounts));

        $this->FilesRequest = new FilesRequestComponent($registry, [
            'mounts' => $mounts,
            'root' => 'shared',
        ]);
    }

    /**
     * Teardown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FilesRequest);
        parent::tearDown();
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test select method
     *
     * @return void
     */
    public function testSelect()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test upload method
     *
     * @return void
     */
    public function testUpload()
    {
        // See FilesController->upload
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test download method
     *
     * @return void
     */
    public function testDownload()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test unzip method
     *
     * @return void
     */
    public function testUnzip()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test newfolder method
     *
     * @return void
     */
    public function testNewFolder()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test lowercase method
     *
     * @return void
     */
    public function testLowercase()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test move method
     *
     * @return void
     */
    public function testMove()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test startup method
     *
     * @return void
     */
    public function testStartUp()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getFileEntity method
     *
     * @return void
     */
    public function testGetFileEntity()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getFileFromQuery method
     *
     * @return void
     */
    public function testGetFileFromQuery()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test updatePropertiesRoot method
     *
     * @return void
     */
    public function testUpdatePropertiesRoot()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test updatePropertiesFolder method
     *
     * @return void
     */
    public function testUpdatePropertiesFolder()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test updatePropertiesFile method
     *
     * @return void
     */
    public function testUpdatePropertiesFile()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test setViewVars method
     *
     * @return void
     */
    public function testSetViewVars()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test redirectCreateFolder method
     *
     * @return void
     */
    public function testRedirectCreateFolder()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test redirectToCurrentFolder method
     *
     * @return void
     */
    public function testRedirectToCurrentFolder()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

}
