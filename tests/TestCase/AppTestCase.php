<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */

namespace App\Test\TestCase;

use App\Model\Entity\Databank;
use App\Model\Table\BaseTable;
use App\Test\Utilities\CompareHtmlTrait;
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;
use App\Cache\Cache;
use Cake\Chronos\Chronos;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\StringCompareTrait;
use Cake\TestSuite\TestCase;
use App\Test\Utilities\Constraint\JsonMatches;
use Epi\Model\Entity\BaseEntity;
use Epi\Model\Entity\Type;
use IvoPetkov\HTML5DOMDocument;
use Laminas\Diactoros\UploadedFile;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionClass;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

/**
 * App\Test\AppTestCase
 */
class AppTestCase extends TestCase
{
    use IntegrationTestTrait;
    use CompareHtmlTrait;
    use StringCompareTrait;

    /**
     * Create new comparison files
     *
     * Don't forget to set back to false, otherwise all tests will pass,
     * because comparison files are always regenerated.
     *
     * @var bool
     */
    public $overwriteComparison = false;

    /**
     * Save the current response content to a file with the same name as the test
     * and '.status' added to the extension
     *
     * @var bool
     */
    public $saveComparison = false;

    /**
     * File used as comparison
     *
     * @var string
     */
    public $comparisonFile;

    /**
     * File containing test data, for example, for upload tests
     *
     * @var string
     */
    public $testdataFile;

    /**
     * List of sql dumps that are loaded at startup (as alternative to fixtures).
     *
     * Put the sql dump into the folder Codeception/_data/dumps and
     * use the database name as key and the sql file name as value, e.g.:
     * ['test_epigraf' => 'epigraf.sql']
     *
     * @var array
     */
    public $dumps = [];
    /**
     * Empty databases before each run
     */
    static public function setUpBeforeClass(): void
    {
        static::truncateDatabase('test_epigraf');
        static::truncateDatabase('test_public');
        static::truncateDatabase('test_projects');

        parent::setUpBeforeClass();
    }

    /**
     * Setup test case
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // Disable caches and clear registry because switching databases will not update the cache
        BaseTable::$cacheMetadata = false;
        $this->getTableLocator()->clear();
        Cache::clear('_cake_model_');
        Cache::clear('default');
        //$this->dropTables = true;

        // Fix time
        Chronos::setTestNow('2020-10-29 13:00:58');
        Attributes::fixSeed();

        // Test security measures
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $this->disableErrorHandlerMiddleware();
        $this->configRequest(['environment' => ['HTTPS' => 'on']]);

        // Setup folders
        $reflect = new ReflectionClass($this);
        $this->_compareBasePath = Configure::read('Data.comparisons') . $reflect->getShortName() . DS;
        $this->comparisonFile = $this->_compareBasePath . $this->getName() . '.php';
        $this->testdataFile = Configure::read('Data.testdata') . $reflect->getShortName() . DS . $this->getName() . '.php';

        // Load dumps
        $dumpFolder = ROOT . DS . 'tests' . DS . 'Testdata' . DS . 'Databases' . DS;
        foreach ($this->dumps as $database => $filename) {
            $this->truncateDatabase($database);
            $this->loadSqlDump($dumpFolder . $filename, $database);
        }
    }

    /**
     * tearDown method
     *
     * Shut down any static object changes.
     *
     * @return void
     */
    public function tearDown(): void
    {
        // Reset projects database
        BaseTable::setDatabase('test_projects');
//        $this->fixtureManager->shutDown();

        foreach ($this->dumps as $database => $filename) {
            $this->truncateDatabase($database);
        }
        parent::tearDown();
    }

    /**
     * Restore error handler
     *
     * @return void
     */
    public function restoreErrorHandlerMiddleware()
    {
        if (isset($this->_configure['Error']['exceptionRenderer'])) {
            Configure::write('Error.exceptionRenderer', $this->_configure['Error']['exceptionRenderer']);
        }
    }


    protected function _generateArticle(): \App\Model\Entity\BaseEntity
    {
        // Create nested test data
        $article = new BaseEntity(
            [
                'id' => 1,
                'name' => 'Article with "Quotes"',
                'published' => PUBLICATION_PUBLISHED,
                'norm_data' => "wd:XXX\ngnd:YYY",
                'sections' => []
            ]
        );

        $article->_serialize_fields = ['id', 'name', 'norm_data', 'url' => 'internalUrl', 'sections'];
        $article->_children = 'sections';
        $article->setSource('Epi.Articles');

        for ($i = 1; $i <= 2; $i++) {
            $section = $this->_generateSection($article, $i);
            for ($j = 1; $j <= 2; $j++) {
                $this->_generateItem($section, $j);
            }
        }

        $article['type'] = $this->_generateArticleType();

        return $article;
    }


    protected function _generateArticleType() {
        $type = new Type(
            [
                'id' => 'ID epi-article',
                'name' => 'epi-article',
                'scope' => 'articles',
                'subtypes' => [],
                'published' => PUBLICATION_BINARY_PUBLISHED,
                'config' => [
                    'triples' => [
                        'base' => '',
                        'namespaces' => [
                            'epi' => '',
                            'schema' => 'http://schema.org/'
                        ],
                        'templates' => [
                            [
                                'subject' => 'epi:{iri}', 'predicate' => 'schema:title', 'object' => '{name}'
                            ],
                            [
                                'subject' => 'epi:{iri}', 'predicate' => 'schema:about', 'object' => 'epi:{sections.*.iri}'
                            ]
                        ]
                    ]
                ]
            ]
        );
        $type->setSource('Epi.Types');
        return $type;
    }

    protected function _generateSection($article, $i) {
        $section = new BaseEntity(
            [
                'id' => $i,
                'name' => 'Section ' . $i,
                'sectiontype' => 'geolocation',
                'published' => PUBLICATION_PUBLISHED,
                'items' => []
            ]
        );
        $section->setSource('Epi.Sections');
        $section->root = $article;
        $section->container = $article;
        $section->_serialize_fields = ['id', 'name', 'sectiontype' => 'type', 'items'];
        $section['type'] = $this->_generateSectionType();

        $article['sections'][] = $section;
        return $section;
    }
    protected function _generateSectionType() {
        $type = new Type(
            [
                'id' => 'ID epi-section',
                'name' => 'epi-section',
                'scope' => 'sections',
                'subtypes' => [],
                'published' => PUBLICATION_BINARY_PUBLISHED,
                'config' => [
                    'triples' => [
                        'templates' => [
                            [
                                'subject' => 'epi:{iri}', 'predicate' => 'schema:description', 'object' => '{items.*.xml}'
                            ],
                            [
                                'subject' => 'epi:{iri}', 'predicate' => 'schema:location', 'object' => '{items.*.prop.name}'
                            ]
                        ]
                    ]
                ]
            ]
        );
        $type->setSource('Epi.Types');
        return $type;
    }

    protected function _generateItem($section, $i) {
        $item = new BaseEntity(
            [
                'id' => $i,
                'published' => PUBLICATION_PUBLISHED,
                'name' => 'Item ' . $i,
                'geo' => ['lat' => 2.1 * $i, 'lng' => 1.2 * $i],
                'xml' => 'This -&gt; is an <anno id="a1">annotated</anno> &lt;- text.',
                'prop' => null,
                'itemtype' => 'geolocation'
            ]
        );
        $item['prop']  = $this->_generateProperty($item, 0);

        $item->setSource('Epi.Items');
        $item->root = $section->root;
        $item->container = $section;
        $item->setFieldFormat('geo', 'geodata');
        $item->setFieldFormat('xml', 'xml');
        $item->_serialize_fields = ['id', 'name', 'geo','xml', 'prop', 'itemtype' => 'type'];
        $item['type'] = $this->_generateItemType();
        $section['items'][] = $item;

        return $item;
    }

    protected function _generateItemType() {
        $type = new Type(
            [
                'id' => 'ID epi-item',
                'name' => 'epi-item',
                'scope' => 'items',
                'subtypes' => [],
                'published' => PUBLICATION_BINARY_PUBLISHED,
                'config' => [
                ]
            ]
        );
        $type->setSource('Epi.Types');
        return $type;
    }


    protected function _generateProperty($item, $id) {
        $property = new BaseEntity(
            [
                'id' => $id,
                'published' => PUBLICATION_PUBLISHED,
                'name' => 'Property ' . $id,
                'propertytype' => 'location',
                'norm_data' => "wd:WD1\ngnd:GND1",
            ]
        );
        $property->setSource('Epi.Properties');
        $property->root = $property;
        $property->container = $item;
        $property->_serialize_fields = ['id', 'name', 'propertytype' => 'type','norm_data'];
        $property['type'] = $this->_generatePropertyType();

        return $property;
    }


    protected function _generatePropertyType() {
        $type = new Type(
            [
                'id' => 'ID epi-property',
                'name' => 'epi-property',
                'scope' => 'properties',
                'subtypes' => [],
                'published' => PUBLICATION_BINARY_PUBLISHED,
                'config' => [
                ]
            ]
        );
        $type->setSource('Epi.Types');
        return $type;
    }

    /**
     * Log in a user that has a specific role
     *
     * @param string $role
     * @return \App\Model\Entity\User
     */
    public function loginUser($role = 'reader')
    {

        $users = $this->fetchTable('App.Users');
        /** @var \App\Model\Entity\User $user */
        $user = $users->find('auth')->where(['role' => $role])->first();

        $this->session(['Auth' => ['User' => $user]]);

        return $user;
    }

    /**
     * Test redirect to login
     *
     * @param $url
     *
     * @return void
     */
    public function assertRedirectToLogin($url)
    {

        $this->get($url);
        $this->assertRedirect(['controller' => 'Users', 'action' => 'login', '?' => ['redirect' => '/' . $url]]);
    }

    /**
     * Create an empty project database
     *
     * @param $name
     *
     * @return void
     * @throws \Exception
     */
    protected function createDatabase($name)
    {
        $databanks = $this->fetchTable('Databanks');
        $databank = $databanks->newEntity(['name' => Databank::addPrefix($name), 'version' => DATABASE_CURRENT_VERSION]);

        $databanks->save($databank);
        $databank->createFolders();
        $databank->createDatabase();
        $databank->initDatabase();

        //$data = ['name' => $name];
        //$this->post('databanks/add', $data);
    }

    /**
     * Drop the database and remove files
     *
     * @param $name
     *
     * @return void
     * @throws \Exception
     */
    protected function removeDatabase($name)
    {
        // Remove database
        $databanks = $this->fetchTable('Databanks');
        $con = $databanks->getConnection();
        $con->execute('DROP DATABASE IF EXISTS ' . Databank::addPrefix($name));

        // Remove folders
        $folder = Configure::read('Data.databases') . Databank::addPrefix($name);
        if (is_dir($folder)) {
            Files::removeFolder($folder);
        }
    }

    /**
     * Load a SQL dump
     *
     * @param string $filename
     * @param string $database
     * @return bool
     */
    protected function loadSqlDump($filename, $database)
    {
        // Even though the connection is set to test_projects, this allows to switch the database
        // to epigraf since project and epigraf database have the same connection details.
        return BaseTable::loadSql($filename, Databank::addPrefix($database), 'test_projects');
    }

    /**
     * Recreate a SQL database
     *
     * @param string $database
     * @return void
     */
    static protected function truncateDatabase($database) {

        $oldDatabaseName = BaseTable::getDatabaseName('test_projects');

        /** @var \Cake\Database\Connection $connection */
        $connection = BaseTable::setDatabase($database,'test_projects' );

        $connection->getDriver()->connect();
        $collection = $connection->getSchemaCollection();
        $tables = $collection->listTablesWithoutViews();
        $schemas = array_map(function ($table) use ($collection) {
            return $collection->describe($table);
        }, $tables);

        $dialect = $connection->getDriver()->schemaDialect();
        /** @var \Cake\Database\Schema\TableSchema $schema */
        foreach ($schemas as $schema) {
            foreach ($dialect->truncateTableSql($schema) as $statement) {
                $connection->execute($statement)->closeCursor();
            }
        }

//        $connection->execute('DROP DATABASE IF EXISTS '. $database);
//        $connection->execute('CREATE DATABASE '. $database . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $connection->getDriver()->disconnect();
        BaseTable::setDatabase($oldDatabaseName,'test_projects' );
    }

    /**
     * Prepare data array for file upload tests
     *
     * Usage in tests:
     *
     * $uploadedFile = $this->>prepareFileUpload($testfilename,$filename,$mediatype);
     * $data = ['Import' => ['file' => $uploadedFile]];
     * $this->post('MYURL',$data);
     *
     * @param $testfilename
     * @param $filename
     * @param string $mediatype
     *
     * @return \Laminas\Diactoros\UploadedFile
     */
    public function prepareFileUpload($testfilename, $filename, $mediatype = 'text/csv')
    {
        $this->assertFileExists($testfilename);

        $this->tearDownFileUpload($filename);
        $this->assertFileDoesNotExist(TMP . $filename);
        copy($testfilename, TMP . $filename);
        $this->assertFileExists(TMP . $filename);

        $uploadedFile = new UploadedFile(
            TMP . $filename,
            filesize(TMP . $filename),
            UPLOAD_ERR_OK,
            $filename,
            $mediatype
        );

        return $uploadedFile;
    }

    /**
     * tearDown method for file uploads
     *
     * @param $filename
     *
     * @return void
     */
    public function tearDownFileUpload($filename)
    {
        if (file_exists(TMP . $filename)) {
            unlink(TMP . $filename);
        }
    }

    public function executeJob($url, $userRole, $timeOut = -1)
    {
        $this->loginUser($userRole);
        $this->post($url);
        $jobId = $this->extractParamFromRedirect('#jobs/execute/([0-9]+)#');

        $this->get('/jobs/execute/' . $jobId . '?timeout=' . $timeOut);

        $job = $this->fetchTable("Jobs")->get($jobId);
        //TODO: use AJAX requests, otherwise download is immediately triggered in JobsController
        for($i = (int)$job['progress']; $i < (int)$job['progressmax']; $i ++) {
            $this->configRequest(['headers' => ['Accept' => 'application/json']]);
            $this->loginUser($userRole);
            $this->post('/jobs/execute/' . $jobId . '?timeout=' . $timeOut);

            $response = $this->_getBodyAsString();
            $response = json_decode($response, true);
            $i = $response['job']['progress'] ?? $i;

            // Check errors
            $error = $response['job']['error'] ?? '';
            $this->assertEquals('',$error);
        }
    }

    /**
     * Extract data from the location header
     *
     * For example, after a record was added, usually, the user is redirected to the view action:
     * $id = $this->extractParamFromRedirect('#view/([0-9]+)#');
     *
     * @param string $pattern The first caption group in the pattern will be returned
     *
     * @return mixed
     */
    public function extractParamFromRedirect($pattern)
    {
        $location = $this->_response->getHeader('location')[0];

        $this->assertMatchesRegularExpression($pattern, $location);

        preg_match($pattern, $location, $matches);
        return $matches[1];
    }

    public function extractElementFromResponse($css)
    {
        $out = '';

        $doc = new HTML5DOMDocument();
        $html = $this->_getBodyAsString();
        $doc->loadHTML($html);
        $doc->formatOutput = true;

        $elements = $doc->querySelectorAll($css);
        foreach ($elements as $element) {
            $out .= $element->outerHTML;
        }

        return $out;
    }

    /**
     * Save the diff to a file
     *
     * @param ExpectationFailedException $e
     * @param string $filename
     * @param integer $truncate Truncate long strings
     *
     * @return void
     */
    public static function saveDiff(ExpectationFailedException $e, $filename, $truncate = 10000)
    {
        $expected = $e->getComparisonFailure()->getExpectedAsString();
        $actual = $e->getComparisonFailure()->getActualAsString();

        $expected = (strlen($expected) > $truncate) ? substr($expected, 0, $truncate) . '...' : $expected;
        $actual = (strlen($actual) > $truncate) ? substr($actual, 0, $truncate) . '...' : $actual;

        $differ = new Differ(new UnifiedDiffOutputBuilder("\n--- Expected\n+++ Actual\n"));
        $diff = $differ->diff($expected, $actual);
        file_put_contents($filename, $diff);
    }

    /**
     * Save comparison data as HTML file
     *
     * @param string $suffix
     * @param array $replacements Array of regex replacements to clean HTML
     *
     * @return array|string|string[]|null
     */
    public function saveBodyToComparisonHtml($suffix = '', $replacements = [])
    {
        $html = $this->_getBodyAsString();
        $html = $this->cleanComparisonHtml($html, $replacements);

        if ($this->saveComparison || $this->overwriteComparison) {
            $filename = $this->comparisonFile . $suffix . '.html';
            if (!$this->overwriteComparison) {
                $filename .= '.status';
            }
            file_put_contents($filename, $html);
        }
        return $html;
    }

    /**
     * Save comparison data as HTML file
     *
     * @param $suffix
     *
     * @return mixed
     */
    public function saveBodyToComparisonFile($suffix = '')
    {
        $data = $this->_getBodyAsString();

        if ($this->saveComparison || $this->overwriteComparison) {
            $filename = $this->comparisonFile . $suffix;
            if (!$this->overwriteComparison) {
                $filename .= '.status';
            }
            file_put_contents($filename, $data);
        }

        return $data;
    }

    /**
     * Save comparison data as XML file
     *
     * @param $suffix
     *
     * @return mixed
     */
    public function saveBodyToComparisonXml($suffix = '')
    {
        $xml = $this->_getBodyAsString();

        if ($this->saveComparison || $this->overwriteComparison) {
            $filename = $this->comparisonFile . $suffix . '.xml';
            if (!$this->overwriteComparison) {
                $filename .= '.status';
            }
            file_put_contents($filename, $xml);
        }

        return $xml;
    }

    /**
     * Save comparison data as JSON file
     *
     * @param $suffix
     *
     * @return mixed
     */
    public function saveBodyToComparisonJson($suffix = '')
    {

        $json = $this->_getBodyAsString();

        if ($this->saveComparison || $this->overwriteComparison) {
            $filename = $this->comparisonFile . $suffix . '.json';
            if (!$this->overwriteComparison) {
                $filename .= '.status';
            }
            file_put_contents($filename, $json);
        }

        return $json;
    }

    /**
     * Save comparison data as XML file
     *
     * @param $data
     * @param $suffix
     *
     * @return string
     */
    public function saveComparisonXml($data, $suffix = '')
    {

        $filename = $this->comparisonFile . $suffix;
        if (!$this->overwriteComparison) {
            $filename .= '.xml.status';
        }
        else {
            $filename .= '.xml';
        }

        $data = '<response>' . $data . '</response>';
        file_put_contents($filename, $data);
        return $data;
    }

    /**
     * Save comparison data as JSON file
     *
     * @param $data
     * @param $suffix
     *
     * @return false|string
     */
    public function saveComparisonJson($data, $suffix = '')
    {

        $filename = $this->comparisonFile . $suffix;
        if (!$this->overwriteComparison) {
            $filename .= '.json.status';
        }
        else {
            $filename .= '.json';
        }

        $json = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents($filename, $json);

        return $json;
    }

    /**
     * Save comparison data as a file
     *
     * File type is defined by suffix.
     *
     * @param $data
     * @param $suffix
     * @param array $chronos Field names with timestamps to replace by the current test time
     *
     * @return mixed
     */
    public function saveComparisonArray($data, $suffix = '', $chronos = [])
    {

        $filename = $this->comparisonFile . $suffix;
        if (!$this->overwriteComparison) {
            $filename .= '.status';
        }

        // Replace datetime by Chronos placeholder
        $data_status = $data;
        foreach ($chronos as $item) {
            $data_status[$item] = '#Chronos#';
        }

        $datastring = "<?php \n return ";
        $datastring .= var_export($data_status, true);
        $datastring .= "; \n?>";

        // Replace Chronos placeholder by Chronos function
        $datastring = str_replace("'#Chronos#'", 'Cake\Chronos\Chronos::getTestNow()->toDateTimeString()', $datastring);

        file_put_contents($filename, $datastring);

        return $data;
    }

    /**
     * Compare existing JSON file to currently created file
     *
     * Asserts that the generated JSON encoded object and the content of the given file are equal.
     * This method overwrites the CakePHP method in order to shorten the output.
     *
     * @param string $actualJson
     * @param string $expectedFile
     * @param string $message
     *
     * @return void
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public static function assertJsonStringEqualsJsonFile(
        string $actualJson,
        string $expectedFile,
        string $message = ''
    ): void {
        static::assertFileExists($expectedFile, $message);
        $expectedJson = file_get_contents($expectedFile);

        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);

        try {
            static::assertThat($actualJson, new JsonMatches($expectedJson), $message);
        } catch (ExpectationFailedException $e) {
            static::saveDiff($e, $expectedFile . '.diff');
            throw $e;
        }
    }

    /**
     * Compare HTML content to the HTML in the comparison file.
     *
     * @param string|boolean $actualHtml Either the comparison HTML or true to save the current HTML to the comparison file
     * @param string $css CSS or XPath selector to extract the element to compare
     * @param string $suffix Suffix for the comparison file
     * @param array $replacements Array of regex replacements to clean HTML
     *
     * @return void
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function assertHtmlEqualsComparison(
        $actualHtml = true,
        string $css = ".content-wrapper, footer",
        string $suffix = '',
        array $replacements = []
    ): void {
        if (is_bool($actualHtml) && !empty($actualHtml)) {
            $actualHtml = $this->saveBodyToComparisonHtml($suffix, $replacements);
        }

        $expectedFile = $this->comparisonFile . $suffix . '.html';
        $this->assertHtmlElementEqualsHtmlFile($expectedFile, $actualHtml, $css, '', false, $replacements);
    }

    /**
     * Compare content to the comparison file
     *
     * @param string $actual
     * @param string $suffix
     *
     * @return void
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function assertContentEqualsComparison(string $actual, string $suffix = ''): void
    {
        $expectedFile = $this->comparisonFile . $suffix;
        if ($this->overwriteComparison) {
            file_put_contents($expectedFile, $actual);
        }

        if ($this->saveComparison) {
            file_put_contents($expectedFile.'.status', $actual);
        }

        $this->assertStringEqualsFile($expectedFile, $actual);
    }

    /**
     * Compare existing XML file to currently created file
     *
     * Asserts that the XML equals the comparison file.
     *
     * @param string $actualXml
     * @param string $suffix
     *
     * @return void
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function assertXmlEqualsComparison(string $actualXml, string $suffix = ''): void
    {
        $expectedFile = $this->comparisonFile . $suffix . '.xml';
        $this->assertXmlStringEqualsXmlFile($expectedFile, $actualXml);
    }

    /**
     * Asserts that the response content type is XML
     * and the content equals the test comparison file
     *
     * @param string $suffix A suffix to distinguish different comparison files in the same test
     * @return void
     */
    public function assertXmlResponseEqualsComparison($suffix = ''): void
    {
        $this->assertContentType('application/xml');
        $compare = $this->saveBodyToComparisonXml($suffix);
        $this->assertXmlEqualsComparison($compare, $suffix);
    }

    /**
     * Asserts that the content equals the test comparison file
     *
     * @param string $suffix A suffix to distinguish different comparison files in the same test
     * @return void
     */
    public function assertFileResponseEqualsComparison($suffix = ''): void
    {
        $compare = $this->saveBodyToComparisonFile($suffix);
        $this->assertContentEqualsComparison($compare, $suffix);
    }

    /**
     * Compare existing file to currently created file
     *
     * File type is defined by suffix.
     * Asserts that the array equals the comparison file.
     *
     * @param array $actualData
     * @param string $suffix
     *
     * @return void
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function assertArrayEqualsComparison(array $actualData, string $suffix = ''): void
    {
        if ($this->saveComparison || $this->overwriteComparison) {
            $this->saveComparisonArray($actualData, $suffix);
        }
        $expectedData = include($this->comparisonFile . $suffix);
        static::assertEquals($expectedData, $actualData);
    }

    /**
     * Compare existing JSON file to currently created file
     *
     * Asserts that the generated JSON encoded object and the content of the comparison file are equal.
     *
     * @param string|array $actualJson Either a JSON string or an array to be converted to JSON.
     *                                 In case an array is provided, it is automatically saved to the comparison file.
     * @param string $suffix
     *
     * @return void
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function assertJsonStringEqualsComparison($actualJson, string $suffix = ''): void
    {
        if (!is_string($actualJson)) {
            $actualJson = $this->saveComparisonJson($actualJson, $suffix);
        }
        $expectedFile = $this->comparisonFile . $suffix . '.json';
        static::assertJsonStringEqualsJsonFile($actualJson, $expectedFile);
    }

    /**
     * Asserts that the response content type is JSON
     * and the content equals the test comparison file
     *
     * @param string $suffix A suffix to distinguish different comparison files in the same test
     * @param string $contentType The expected content type, default is 'application/json', for example 'application/geo+json'
     * @return void
     */
    public function assertJsonResponseEqualsComparison($suffix = '', $contentType = 'application/json'): void
    {
        $this->assertResponseOk();
        $this->assertContentType($contentType);
        $compare = $this->saveBodyToComparisonJson($suffix);
        $this->assertJsonStringEqualsComparison($compare, $suffix);
    }

    /**
     * Compares the current HTML response to the comparison file
     *
     * The response content is saved to a status file
     * (=name of the comparison file + ".status" extension)
     *
     * @param string $css
     * @param string $suffix
     * @param array $replacements
     *
     * @return void
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function assertResponseEqualsComparison(
        string $suffix = '',
        string $css = ".content-wrapper, footer",
        array $replacements = []
    ): void {
        $actualHtml = $this->saveBodyToComparisonHtml($suffix);
        $expectedFile = $this->comparisonFile . $suffix . '.html';
        $this->assertHtmlElementEqualsHtmlFile($expectedFile, $actualHtml, $css, '', false, $replacements);
    }

}
