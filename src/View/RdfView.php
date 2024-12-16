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
 * A view class that is used for creating RDF+XML responses.
 *
 */
class RdfView extends XmlView
{

    /**
     * The name of the layout file to render the view inside of. The name
     * specified is the filename of the layout in /src/Template/Layout without
     * the .php extension. The file is only rendered, if the serialize option is not set.
     *
     * @var string
     */
    public $layout = 'rdf';


    static protected $_extension = 'rdf';

    /**
     * Init the header variables (resets static variables)
     *
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        static::$_header = [
            '_xml_declaration' => "<?xml version='1.0'?>",
            '_xml_tag' => 'rdf:RDF',
            '_xml_attributes' => [],
            '_xml_base' => '',
            '_xml_namespaces' => ['rdf' => SERIALIZE_NAMESPACES['rdf']]
        ];
    }

    public static function contentType(): string
    {
        return 'application/rdf+xml';
    }


    /**
     * Render a RDF subject or object value
     *
     * Prefixed names in literals are detected by namespace prefixes.
     * TODO: Support numeric values without quotes
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

        $baseIri = static::$_header['_xml_base'] ?? '';
        $namespaces = static::$_header['_xml_namespaces'] ?? [];
        list($value, $type) = Attributes::parseIriValue($value, $baseIri, $namespaces, 'expand');
        $value = htmlspecialchars($value, ENT_XML1, 'UTF-8');


        // Full IRIs
        if ($type === 'iri') {
            $value = [
                '_xml_attributes' => ['rdf:resource'],
                'rdf:resource' => $value
            ];
        }

        return $value;
    }


    /**
     * Prepare pagination for hydra collections
     *
     * Adds pagination data to $_header.
     *
     * @param array $data
     * @return array
     */
    protected function _prepareCollection($data, $options = [])
    {
        $pagination = $data['pagination'] ?? [];
        unset($data['pagination']);
        $data = array_values($data);

        // Hydra collection data
        $data['rdf:about'] = $this->Paginator->generateUrl(
            ['page' => null], null, ['_ext' => static::$_extension], ['fullBase' => true]
        );
        $data['_xml_attributes'] = array_merge($data['_xml_attributes'] ?? [], ['rdf:about']);

        $data = ['hydra:Collection' => $data];

        // Hydra pagination data
        if (!empty($pagination)) {
            $viewUrl = $this->Paginator->generateUrl(
                [], null, ['_ext' => static::$_extension], ['fullBase' => true]
            );
            $data['hydra:Collection']['hydra:view'] = [
                '_xml_attributes' => ['rdf:resource'],
                'rdf:resource' => $viewUrl
            ];
            $data['hydra:Collection']['hydra:totalItems'] = $pagination['count'] ?? '';

            $firstPage = $this->Paginator->generateUrl(
                ['page' => 1, 'escape' => false], null,
                ['_ext' => static::$_extension], ['fullBase' => true]
            );
            $lastPage = $this->Paginator->generateUrl(
                ['page' => ceil($pagination['count'] / $pagination['perpage']), 'escape' => false], null,
                ['_ext' => static::$_extension], ['fullBase' => true]
            );

            $data['hydra:PartialCollectionView'] = [
                '_xml_attributes' => ['rdf:about'],
                'rdf:about' => $viewUrl,
                'hydra:first' => [
                    '_xml_attributes' => ['rdf:resource'],
                    'rdf:resource' => $firstPage
                ],
                'hydra:last' => [
                    '_xml_attributes' => ['rdf:resource'],
                    'rdf:resource' => $lastPage
                ]
            ];

            if ($this->Paginator->hasPrev()) {
                $data['hydra:PartialCollectionView']['hydra:previous'] =
                    [
                        '_xml_attributes' => ['rdf:resource'],
                        'rdf:resource' => $this->Paginator->generateUrl([
                            'page' => $pagination['page'] - 1,
                            'escape' => false
                        ], null, ['_ext' => static::$_extension], ['fullBase' => true])
                    ];
            }
            if ($this->Paginator->hasNext()) {
                $data['hydra:PartialCollectionView']['hydra:next'] =
                    [
                        '_xml_attributes' => ['rdf:resource'],
                        'rdf:resource' => $this->Paginator->generateUrl([
                            'page' => $pagination['page'] + 1,
                            'escape' => false
                        ], null, ['_ext' => static::$_extension], ['fullBase' => true])
                    ];
            }
        }
        return $data;
    }

    /**
     * Prepare Hydra collections pagination data
     *
     * @param mixed $data
     * @param array $options
     * @param integer $level Nesting level of _prepareData calls
     */
    protected function _prepareViewData($data, $options = [], $level = 0)
    {
        if (($level === 0) && is_array($data)) {

            // Hydra collection data
            if (($options['params']['action'] ?? 'view') === 'index') {
                $data = $this->_prepareCollection($data, $options);
            }
            // Triple data without surrounding tag
            else {
                $data = array_values($data);
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
        if (!empty($data['namespaces'])) {
            static::$_header['_xml_namespaces'] = array_merge(static::$_header['_xml_namespaces'] ?? [],
                $data['namespaces'] ?? []);
        }

        // Set base (first base setting wins)
        if (!empty($data['base']) && empty(static::$_header['base'])) {
            static::$_header['_xml_base'] = $data['base'];

        }

        // Prepare triples
        if (is_array($data) && isset($data['triples'])) {
            // Group by subject
            $subjects = Arrays::array_group_values($data['triples'] ?? [], ['subject', 'predicate', 'object']);

            // Use $key as value for rdf:about
            foreach ($subjects as $subject => $predicate) {

                $subject = self::renderValue($subject, $options);
                $subject = is_array($subject) ? $subject['rdf:resource'] : '';

                $predicateList = [];
                foreach ($predicate as $predKey => $objects) {

                    // Handle arrays
                    if (is_array($objects)) {
                        foreach ($objects as $object) {
                            $object = self::renderValue($object, $options);
                            $object = is_array($object) ? $object : [$object];
                            $object['_xml_tag'] = $predKey;
                            $predicateList[] = $object;
                        }
                    }
                    else {
                        $predicateList[$predKey] = self::renderValue($objects, $options);
                    }
                }

                $out[] = [
                    'rdf:Description' => [
                        "_xml_attributes" => ["rdf:about"],
                        'rdf:about' => $subject,
                        $predicateList,
                    ]
                ];
            }
        }

        // Collect hydra members
        elseif (is_array($data) && isset($data['member'])) {
            $out['hydra:member'] = [
                '_xml_tag' => 'hydra:member',
                '_xml_attributes' => ['rdf:resource'],
                'rdf:resource' => $data['member']
            ];
        }

        $out['_xml_tag'] = null;
        return $out;
    }

    /**
     * Render the XML prolog and open the root tag
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    public function renderProlog($data, $options)
    {

        // Convert namespaces to attributes
        if (!empty(static::$_header['_xml_namespaces'])) {
            foreach (static::$_header['_xml_namespaces'] ?? [] as $key => $value) {
                static::$_header['xmlns:' . $key] = $value;
                static::$_header['_xml_attributes'][] = 'xmlns:' . $key;
            }
        }
        unset(static::$_header['_xml_namespaces']);

        // Convert base to attribute
        if (!empty(static::$_header['_xml_base'])) {
            static::$_header['xml:base'] = static::$_header['_xml_base'];
            static::$_header['_xml_attributes'][] = 'xml:base';
            unset(static::$_header['_xml_base']);
        }

        return parent::renderProlog($data, $options);
    }
}
