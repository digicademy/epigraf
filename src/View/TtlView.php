<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\View;

use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Entity;

/**
 * A view class that is used for creating TTL responses.
 *
 * The passed data should be an array of entities with the getDataForExport() method.
 *
 * The getDataForExport() method should return an array of triple objects.
 * Each triple object should contain the following keys:
 *
 * - triples An array with the keys 'subject', 'predicate', and 'object'.
 * - member The IRI of the entity, used to generate hydra collections
 * - namespaces
 * - base
 *
 * Reserved values in triples (in the subject, predicate and object keys) are:
 * - Values starting with an underscore '_' are treated as serialization options
 *   ('_data_type_' is used to identify IRIs)
 * - Values starting with a namespace prefix followed by a colon are treated as implicit IRIs.
 *
 * The triples are first grouped by subject and predicate,
 * then rendered using renderTriples().
 *
 * Within the grouped triples:
 * - the predicate 'rdf:type' is replaced by 'a'.
 * - IRIs are enclosed in '<>'.
 * - Literals are enclosed in quotes.
 *
 * IRIs are automatically identified:
 * - Each value starting with a namespace prefix followed by a colon is treated as an IRI.
 * - If the grouped triples contain a key '_data_type' with the value 'iri', the parent key is treated as an IRI.
 *
 */
class TtlView extends ApiView
{

    /**
     * The name of the layout file to render the view inside of. The name
     * specified is the filename of the layout in /src/Template/Layout without
     * the .php extension. The file is only rendered, if the serialize option is not set.
     *
     * Not used.
     *
     * @var string
     */
    public $layout = 'ttl';

    /**
     * Request file extension.
     *
     * @var string
     */
    static protected $_extension = 'ttl';

    public function initialize(): void
    {
        // Map extension to mime types
        $this->getResponse()->setTypeMap('ttl', ['text/turtle']);
        parent::initialize();
    }

    public static function contentType(): string
    {
        return 'text/turtle';
    }

    /**
     * Prepare data before renderContent() ist called
     *
     * Prepares hydra collection pagination data.
     *
     * @param mixed $data
     * @param array $options
     * @param integer $level Nesting level of _prepareViewData() calls
     */
    protected function _prepareViewData($data, $options = [], $level = 0)
    {
        if (($level === 0) && is_array($data)) {

            // Hydra collection data
            if (($options['params']['action'] ?? 'view') === 'index') {
                $data = $this->_prepareCollection($data, $options);
            }
        }
        return $data;
    }

    /**
     * Prepare entity data after calling getDataForExport()
     *
     * @param Entity|array $data
     * @param array $options
     * @return array
     */
    public function _prepareEntityData($data, $options = [])
    {
        $out = [];

        // Collect namespaces
        static::$_header['namespaces'] = array_merge(static::$_header['namespaces'], $data['namespaces'] ?? []);

        // Set base (first base setting wins)
        if (!empty($data['base']) && empty(static::$_header['base'])) {
            static::$_header['base'] = $data['base'];
        }

        // Group triples by subject
        if (is_array($data) && isset($data['triples'])) {
            $out['triples'] = Arrays::array_group_values($data['triples'] ?? [], ['subject', 'predicate', 'object']);
        }

        // Collect hydra members
        elseif (is_array($data) && isset($data['member'])) {
            static::$_header['member'][] = $data['member'] . '.' . self::$_extension;
        }

        return $out;
    }

    /**
     * Prepare pagination for hydra collections
     *
     * Adds pagination data to $_header.
     *
     * @param array $data
     * @param array $options
     * @return array
     */
    protected function _prepareCollection($data, $options = [])
    {
        $pagination = $data['pagination'] ?? [];
        unset($data['pagination']);


        if (!empty($this->getRequest()->getParam('controller'))) {
            $subject = $this->Paginator->generateUrl(
                ['page' => null],
                null,
                ['_ext' => static::$_extension],
                ['fullBase' => true]
            );
        }
        else {
            $subject = $this->getRequest()->getRequestTarget();
        }

        static::$_header['subject'] = $subject;

        if (!empty($pagination)) {

            $viewUrl = $this->Paginator->generateUrl(
                [], null,
                ['_ext' => static::$_extension],
                ['fullBase' => true]
            );
            static::$_header['hydra:view'] = $viewUrl;
            static::$_header['hydra:totalItems'] = $pagination['count'] ?? '';

            if (!empty($pagination['page_prev']) || !empty($pagination['page_next'])) {

                $firstPage = $this->Paginator->generateUrl(
                    ['page' => 1, 'escape' => false], null,
                    ['_ext' => static::$_extension], ['fullBase' => true]
                );
                $lastPage = $this->Paginator->generateUrl(
                    [
                        'page' => ceil($pagination['count'] / $pagination['perpage']),
                        'escape' => false
                    ],
                    null,
                    ['_ext' => static::$_extension],
                    ['fullBase' => true]
                );

                static::$_header['hydra:PartialCollectionView'] = [
                    $viewUrl => [
                        '_data_type' => 'iri',
                        'rdf:type' => 'hydra:PartialCollectionView',
                        'hydra:first' => $firstPage,
                        'hydra:last' => $lastPage
                    ]
                ];

                if ($this->Paginator->hasPrev()) {
                    static::$_header['hydra:PartialCollectionView'][$viewUrl]['hydra:previous'] =
                        $this->Paginator->generateUrl(
                            ['page' => $pagination['page'] - 1, 'escape' => false],
                            null,
                            ['_ext' => static::$_extension],
                            ['fullBase' => true]
                        );
                }
                if ($this->Paginator->hasNext()) {
                    static::$_header['hydra:PartialCollectionView'][$viewUrl]['hydra:next'] =
                        $this->Paginator->generateUrl(
                            ['page' => $pagination['page'] + 1, 'escape' => false],
                            null,
                            ['_ext' => static::$_extension],
                            ['fullBase' => true]
                        );
                }
            }
        }


        return $data;
    }

    /**
     * Render nested triples
     *
     * ### Options
     * - objects: How to render object lists. 'comma' (default) or 'semicolon'.
     *
     * @param array $data
     * @param array $options
     * @param integer $level
     * @return string
     */
    static function renderTriples($data, $options = [], $level = 0)
    {
        $out = '';

        $collapseObjects = ($options['objects'] ?? 'semicolon') === 'comma';

        // Output rendered triples
        foreach (($data ?? []) as $subject => $predicates) {

            $valueOptions = [];
            if (is_array($predicates)) {
                $valueOptions['_data_type'] = $predicates['_data_type'] ?? null;
                unset($predicates['_data_type']);
            }
            $subject = static::renderValue($subject, $valueOptions);
            $out .= $subject;

            // Collect all predicates for the same subject
            $predicateArray = [];
            foreach ($predicates as $predicate => $objects) {

                // Collect all objects for the same predicate
                $objectArray = [];

                $valueOptions = [];
                if (is_array($objects)) {
                    $valueOptions['_data_type'] = $objects['_data_type'] ?? null;
                    unset($objects['_data_type']);
                }
                $predicate = static::renderValue($predicate, $valueOptions);

                // Replace rdf:type with a
                if ($predicate === 'rdf:type') {
                    $predicate = 'a';
                }

                $objects = is_array($objects) ? $objects : [$objects];
                foreach ($objects as $object) {
                    $valueOptions = [];
                    if (is_array($object)) {
                        $valueOptions['_data_type'] = $object['_data_type'] ?? null;
                        $object = $object['_data_value'] ?? '';
                    }

                    $object = static::renderValue($object, $valueOptions);

                    // Join the objects with a comma
                    if ($collapseObjects) {
                        $objectArray = array_merge($objectArray, [$object]);

                    }
                    else {
                        $predicateArray[] = $predicate . ' ' . $object;
                    }
                }

                // Join the objects with a comma
                if ($collapseObjects) {
                    $predicateArray[] = $predicate . ' ' . implode(", ", $objectArray);
                }
            }

            // Join the predicates with a semicolon
            $indent = 2; //strlen($subject) + 1;
            $out .= "\n  " . implode(" ;\n" . str_repeat(' ', $indent), $predicateArray) . " .\n\n";
        }

        return $out;
    }

    /**
     * Render the hydra collection
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    static function renderCollection($data, $options)
    {

        // Generate triples nested by subject, predicate, and object
        $collection = [];
        if (!empty(static::$_header['member']) && !empty(static::$_header['subject'])) {
            $collection = [
                static::$_header['subject'] => [
                    '_data_type' => 'iri',
                    'rdf:type' => 'hydra:Collection',
                    'hydra:totalItems' => static::$_header['hydra:totalItems'] ?? '',
                    'hydra:view' => static::$_header['hydra:view'] ?? '',
                    'hydra:member' => static::$_header['member'],
                ],
            ];
        }

        // Add view triple (which contains pagination data)
        if (!empty(static::$_header['hydra:PartialCollectionView'])) {
            $collection = array_merge_recursive($collection, static::$_header['hydra:PartialCollectionView'] ?? []);
        }

        if (!empty($collection)) {
            return static::renderTriples($collection, ['objects' => 'semicolon']);
        }
        else {
            return '';
        }
    }

    /**
     * Render a TTL value
     *
     * Enclose URIs with '<>' and literals with '""'.
     *
     * Implicit IRIs in literals are detected by namespace prefixes.
     * TODO: Support numeric values without quotes
     * TODO: Merge with JsonldView::renderValue() and RdfView::renderValue()
     *
     * ### Options
     * - _data_type: 'iri' to treat the value as an IRI,
     *               'literal' to treat it as a literal.
     *                The default (if not set) is 'literal'.
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    static function renderValue($value, $options = [])
    {

        $baseIri = static::$_header['base'] ?? '';
        $namespaces = static::$_header['namespaces'] ?? [];
        list($value, $type) = Attributes::parseIriValue($value, $baseIri, $namespaces, 'ncname');

        // Full IRIs
        if ($type === 'iri') {
            $value = "<" . $value . ">";
        }

        // Literals
        elseif ($type === 'literal') {

            // Escape backslashes and double quotes
            $value = str_replace(
                ['\\', '"'],
                ['\\\\', '\"'],
                $value
            );

            // Use triple quotes for multiline literals
            // and double quotes for single-line literals
            if (strpos($value, "\n") !== false) {
                $value = '"""' . $value . '"""';
            }
            else {
                $value = '"' . $value . '"';
            }
        }

        return $value;
    }

    /**
     * Open the document
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    function renderProlog($data, $options)
    {
        $out = "";

        $base = static::$_header['base'] ?? null;
        if (!is_null($base)) {
            $out .= "@base <" . $base . "> .\n";
        }
        foreach ((static::$_header['namespaces'] ?? []) as $nspKey => $nspValue) {
            $out .= "@prefix " . $nspKey . ": <" . $nspValue . "> .\n";
        }
        $out .= "\n";

        return $out;
    }


    /**
     * Render the epilog of the document
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    function renderEpilog($data, $options)
    {
        return static::renderCollection($data, $options);
    }

    /**
     * Output TTL triples
     *
     * Collects hydra members, namespaces, and the base on its way through the data
     *
     * @param array|EntityInterface $data
     * @param array $options
     * @param integer $level
     * @return string
     */
    public function renderContent($data, $options = [], $level = 0)
    {

        // Get triples
        $data = $this->extractData($data, $options);

        $out = '';

        if (is_array($data)) {
            $out .= static::renderTriples($data['triples'] ?? [], $options, $level);
            unset($data['base']);
            unset($data['triples']);
            unset($data['namespaces']);

            // Recurse into nested data
            foreach ($data as $value) {
                $out .= $this->renderContent($value, $options, $level + 1);
            }
        }
        return $out;
    }
}
