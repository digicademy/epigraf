<?php
declare(strict_types=1);

namespace App\View;

use App\Model\Interfaces\ExportEntityInterface;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetDecorator;
use Cake\ORM\Entity;
use Cake\ORM\ResultSet;
use Epi\Model\Entity\RootEntity;
use Exception;

/**
 * A view class that is used for CSV responses.
 *
 * By setting the '_serialize' key in your controller, you can specify a view variable
 * that should be serialized to CSV and used as the response for the request.
 * This allows you to omit views + layouts, if your just need to emit a single view
 * variable as the CSV response.
 *
 * In your controller, you could do the following:
 *
 * `$this->set(['posts' => $posts])->viewBuilder()->setOption('serialize', 'posts');`
 *
 * When the view is rendered, the `$posts` view variable will be serialized
 * into CSV.
 *
 * If the data contains nested arrays, each array item will result in a single row.
 *
 * Entities will be converted to an array and if the entity has a getDataForExport-method it will be called.
 *
 * ```
 *
 * You can also define `serialize` as an array. This will create a top level object containing
 * all the named view variables:
 *
 * ```
 * $this->set(compact('posts', 'users', 'stuff'));
 * $this->viewBuilder()->setOption('serialize', ['posts', 'users']);
 * ```
 *
 * Each of the viewVars in `serialize` would then be output into the csv
 *
 * If you don't use the `serialize` option, you will need a view. You can use extended
 * views to provide layout like functionality.
 *
 * When not using custom views, you may specify the following view options:
 *
 * - array `$header`: (default null)    A flat array of header column names
 * - array `$footer`: (default null)    A flat array of footer column names
 * - string `$delimiter`: (default ';') CSV Delimiter, defaults to semicolon
 * - string `$enclosure`: (default '"') CSV Enclosure for use with fputcsv()
 * - string `$eol`: (default '\n')      End-of-line character the csv
 * - string `$keycol`: (default '_key') A column that will contain the view variables name and
 *                                      keys of nested arrays
 *
 * @link https://github.com/friendsofcake/cakephp-csvview
 */
class CsvView extends ApiView
{

    /**
     * The name of the layout file to render the view inside of. The name
     * specified is the filename of the layout in /src/Template/Layout without
     * the .php extension. The file is only rendered, if the serialize option is not set.
     *
     * @var string
     */
    public $layout = 'csv';

    static protected $_extension = 'csv';

    /**
     * Iconv extension.
     *
     * @var string
     */
    public const EXTENSION_ICONV = 'iconv';

    /**
     * Mbstring extension.
     *
     * @var string
     */
    public const EXTENSION_MBSTRING = 'mbstring';

    /**
     * List of bom signs for encodings.
     *
     * @var array
     */
    protected $bomMap;

    /**
     * BOM first appearance
     *
     * @var bool
     */
    protected $isFirstBom = true;

    /**
     * @var array collect header while generating rows
     */
    protected $_csvHeader = [];

    /**
     * Default config.
     *
     * - 'header': (default null)  A flat array of header column names
     * - 'footer': (default null)  A flat array of footer column names
     * - 'delimiter': (default ';')      CSV Delimiter, defaults to semicolon
     * - 'enclosure': (default '"')      CSV Enclosure for use with fputcsv()
     * - 'newline': (default '\n')       CSV Newline replacement for use with fputcsv()
     * - 'eol': (default '\n')           End-of-line character the csv
     * - 'bom': (default false)          Adds BOM (byte order mark) header
     * - 'setSeparator': (default false) Adds sep=[_delimiter] in the first line
     * - 'csvEncoding': (default 'UTF-8') CSV file encoding
     * - 'dataEncoding': (default 'UTF-8') Encoding of data to be serialized
     * - 'transcodingExtension': (default 'iconv') PHP extension to use for character encoding conversion
     *
     * @var array
     */
    protected $_defaultConfig = [
        'footer' => null,
        'header' => null,
        'serialize' => null,
        'delimiter' => ';',
        'enclosure' => '"',
        'newline' => "\n",
        'eol' => PHP_EOL,
        'null' => '',
        'keycol' => '_key',
        'bom' => false,
        'setSeparator' => false,
        'csvEncoding' => 'UTF-8',
        'dataEncoding' => 'UTF-8',
        'transcodingExtension' => self::EXTENSION_ICONV,
    ];

    /**
     * Initialize hook
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->bomMap = [
            'UTF-32BE' => chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF),
            'UTF-32LE' => chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00),
            'UTF-16BE' => chr(0xFE) . chr(0xFF),
            'UTF-16LE' => chr(0xFF) . chr(0xFE),
            'UTF-8' => chr(0xEF) . chr(0xBB) . chr(0xBF),
        ];

        if (
            $this->getConfig('transcodingExtension') === static::EXTENSION_ICONV &&
            !extension_loaded(self::EXTENSION_ICONV)
        ) {
            $this->setConfig('transcodingExtension', static::EXTENSION_MBSTRING);
        }

        parent::initialize();
    }

    public static function contentType(): string
    {
        return 'text/csv';
    }

    /**
     * Get data from ExportEntityInterface
     *
     * ### Options
     * - isRows boolean: Is the input array an array of rows? Defaults to false
     *
     * @param Entity|array $data
     * @param array $options
     * @param integer $level
     * @return array
     */
    protected function _prepareViewData($data, $options = [], $level = 0)
    {
        $isRows = $options['isRows'] ?? false;
        $requestOptions = $this->getConfig('options');

        // TODO: set option for all exports, rename resulting field from type to rowtype
        //       to avoid naming conflicts with a potential type entity
        $requestOptions['types'] = 'merge';

        // Entities with the getDataForExport method
        if (is_object($data) && ($data instanceof ExportEntityInterface)) {
            return $data->getDataForExport($requestOptions, 'csv');
        }

        // ResultSets
        if (($data instanceof ResultSet) || ($data instanceof ResultSetDecorator) || (is_array($data) && ($level === 1))) {
            if ($data instanceof ResultSetDecorator) {
                $data = $data->toArray();
            }
            $rows = [];
            foreach ($data as $row) {
                if ((is_object($row) && ($row instanceof ExportEntityInterface))) {
                    $row = $row->getDataForExport($requestOptions, 'csv');
                    $rows = array_merge($rows, $row);
                }
                else {
                    $rows[] = $row;
                }
            }
            return $rows;
        }

        // Objects with toArray method
        elseif (is_object($data) && method_exists($data, 'toArray') && is_callable([$data, 'toArray'])) {
            $data = $data->toArray();
        }

        // Arrays
        if ($isRows && is_array($data)) {
            $rows = [];
            foreach ($data as $key => &$value) {
                $value = $this->_prepareViewData($value, $options, $level + 1);
                $rows = array_merge($rows, $value);
            }
            return $rows;
        }
        elseif ($isRows) {
            return is_array($data) ? $data : [$data];
        }
        else {
            // Because when called in a nested array, array_merge needs unnested rows
            return $level === 0 ? $data : [$data];
        }
    }

    /**
     * Renders the header line
     *
     * If the header was not set in the config, renders
     * the automatically collected headers
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    public function renderProlog($data, $options)
    {
        $header = $this->getConfig('header', $this->_csvHeader);
        return $this->_renderRow($header, true);
    }

    /**
     * Renders the footer
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    public function renderEpilog($data, $options)
    {
        return $this->_renderRow($this->getConfig('footer'));
    }

    /**
     * Renders the body of the data to the csv
     *
     * @param array|EntityInterface $data
     * @param array $options
     * @param int $level The level of indentation
     * @return string
     * @throws Exception When the data is not an array or an object
     */
    public function renderContent($data, $options = [], $level = 0) : string
    {
//        $data = $this->extractData($data, $options);

        $this->_csvHeader = [];
        $out = '';

        // Single entities
        if (is_object($data) && ($data instanceof RootEntity)) {
            $out .= $this->renderContent([[$data]], $options, $level);
        }

        // Set of entities
        elseif ($data instanceof ResultSet) {
            $out .= $this->renderContent([$data], $options, $level);
        }

        // Arrays
        else {
            // Objects with toArray method
            if (is_object($data) && method_exists($data, 'toArray') && is_callable([$data, 'toArray'])) {
                $data = $data->toArray();
            }

            // Arrays
            if (is_array($data)) {
                foreach ($data as $viewVar => $rows) {
                    if (is_scalar($rows)) {
                        throw new Exception("'" . $viewVar . "' is not an array or iteratable object.");
                    }

                    $rows = $this->_prepareViewData($rows, ['isRows' => true], $level + 1);
                    foreach ($rows as $row) {
                        if (is_object($row) && method_exists($row, 'toArray') && is_callable([$row, 'toArray'])) {
                            $row = $row->toArray();
                        }
                        if (is_array($row)) {
                            $out .= $this->_renderRow($row);
                        }
                    }
                }
            }
        }
        return $out;
    }

    /**
     * Aggregates the rows into a single csv
     *
     * @param array|null $row Row data
     * @param bool $header If true, the row is considered a header and prefixed to the output
     * @return string CSV with all data to date
     */
    protected function _renderRow(?array $row = null, bool $header = false): string
    {
        if (empty($row)) {
            return '';
        }

        if (!$header) {
            $this->_csvHeader = array_unique(array_merge($this->_csvHeader, array_keys($row)));
            $row = array_merge(array_fill_keys($this->_csvHeader, null), $row);
        }

        return (string)$this->_generateRow($row);
    }

    /**
     * Generates a single row in a csv from an array of
     * data by writing the array to a temporary file and
     * returning it's contents
     *
     * @param array|null $row Row data
     * @return string|false String with the row in csv-syntax, false on fputscv failure
     */
    protected function _generateRow(?array $row = null)
    {
        static $fp = false;

        if (empty($row)) {
            return '';
        }

        if ($fp === false) {
            $fp = fopen('php://temp', 'r+');

            $setSeparator = $this->getConfig('setSeparator');
            if ($setSeparator) {
                fwrite($fp, 'sep=' . $setSeparator . "\n");
            }
        }
        else {
            ftruncate($fp, 0);
        }

        $null = $this->getConfig('null');
        if ($null) {
            foreach ($row as &$field) {
                if ($field === null) {
                    $field = $null;
                }
            }
        }

        $delimiter = $this->getConfig('delimiter');
        $enclosure = $this->getConfig('enclosure');
        $newline = $this->getConfig('newline');

        // Convert arrays to JSON
        $row = array_map(function ($field) use ($newline) {
            if (is_array($field)) {
                $field = json_encode($field);
            }
            if (is_string($field)) {
                $field = str_replace(["\r\n", "\n", "\r"], $newline, $field);
            }
            return $field;
        }, $row);
        //$row = array_values($row);

        if ($enclosure === '') {
            // fputcsv does not supports empty enclosure
            if (fputs($fp, implode($delimiter, $row) . $newline) === false) {
                return false;
            }
        }
        else {
            if (fputcsv($fp, $row, $delimiter, $enclosure, "") === false) {
                return false;
            }
        }

        rewind($fp);

        $csv = '';
        while (($buffer = fgets($fp, 4096)) !== false) {
            $csv .= $buffer;
        }

        $eol = $this->getConfig('eol');
        if ($eol !== "\n") {
            $csv = str_replace("\n", $eol, $csv);
        }

        $dataEncoding = $this->getConfig('dataEncoding');
        $csvEncoding = $this->getConfig('csvEncoding');
        if ($dataEncoding !== $csvEncoding) {
            $extension = $this->getConfig('transcodingExtension');
            if ($extension === static::EXTENSION_ICONV) {
                $csv = iconv($dataEncoding, $csvEncoding, $csv);
            }
            elseif ($extension === static::EXTENSION_MBSTRING) {
                $csv = mb_convert_encoding($csv, $csvEncoding, $dataEncoding);
            }
        }

        // BOM must be added after encoding
        $bom = $this->getConfig('bom');
        if ($bom && $this->isFirstBom) {
            $csv = $this->_getBom($csvEncoding) . $csv;
            $this->isFirstBom = false;
        }

        return $csv;
    }

    /**
     * Returns the BOM for the encoding given.
     *
     * @param string $csvEncoding The encoding you want the BOM for
     * @return string
     */
    protected function _getBom(string $csvEncoding): string
    {
        $csvEncoding = strtoupper($csvEncoding);

        return $this->bomMap[$csvEncoding] ?? '';
    }
}
