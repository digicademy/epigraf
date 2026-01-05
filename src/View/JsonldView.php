<?php
declare(strict_types=1);

/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\View;

use App\Model\Interfaces\ExportEntityInterface;
use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Entity;

/**
 * A view class that is used for JSON LD responses,
 * derived from the JsonView class.
 *
 */
class JsonldView extends JsonView
{

    /**
     * The name of the layout file to render the view inside of. The name
     * specified is the filename of the layout in /src/Template/Layout without
     * the .php extension. The file is only rendered, if the serialize option is not set.
     *
     * @var string
     */
    public $layout = 'jsonld';

    static protected $_extension = 'jsonld';

    public function initialize(): void
    {
        // Map extension to mime types
        $this->getResponse()->setTypeMap('jsonld', ['application/ld+json']);
        parent::initialize();
    }

    public static function contentType(): string
    {
        return 'application/ld+json';
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

        $subject = $this->Paginator->generateUrl(
            ['page' => null, 'limit' => null, 'offset' => null],
            null,
            ['_ext' => static::$_extension],
            ['fullBase' => true]
        );
        static::$_header['subject'] = $subject;

        if (!empty($pagination)) {

            $viewUrl = $this->Paginator->generateUrl(
                [], null,
                ['_ext' => static::$_extension],
                ['fullBase' => true]
            );
            static::$_header['hydra:view'] = ['@id' => $viewUrl];
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
                    '@id' => $viewUrl,
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => ['@id' => $firstPage],
                    'hydra:last' => ['@id' => $lastPage]
                ];

                if ($this->Paginator->hasPrev()) {
                    static::$_header['hydra:PartialCollectionView']['hydra:previous'] =
                        [
                            '@id' => $this->Paginator->generateUrl(
                                ['page' => $pagination['page'] - 1, 'escape' => false],
                                null,
                                ['_ext' => static::$_extension],
                                ['fullBase' => true]
                            )
                        ];
                }
                if ($this->Paginator->hasNext()) {
                    static::$_header['hydra:PartialCollectionView']['hydra:next'] =
                        [
                            '@id' => $this->Paginator->generateUrl(
                                ['page' => $pagination['page'] + 1, 'escape' => false],
                                null,
                                ['_ext' => static::$_extension],
                                ['fullBase' => true]
                            )
                        ];
                }
            }
        }

        return $data;
    }

    /**
     * Render the hydra collection
     * @return array
     */
    protected function _getCollection()
    {

        // Generate member collection
        $collection = [
            '@id' => static::$_header['subject'] ?? '',
            '@type' => 'hydra:Collection',
            'hydra:member' => static::$_header['member'] ?? [],
            'hydra:totalItems' => static::$_header['hydra:totalItems'] ?? '',
        ];

        // Add view data
        if (!empty(static::$_header['hydra:PartialCollectionView'])) {
            $collection['hydra:view'] = array_merge($collection['hydra:view'] ?? [],
                static::$_header['hydra:PartialCollectionView'] ?? []);
        }

        return $collection;
    }


    /**
     * Prepare entity data after calling getDataForExport()
     *
     * Overwrite in subclasses to change the data structure
     * and collect data on the way through the entities.
     *
     * @param Entity|array $data
     * @param array $options
     * @return array
     */
    public function _prepareEntityData($data, $options = [])
    {
        // Collect namespaces
        static::$_header['namespaces'] = array_merge(static::$_header['namespaces'], $data['namespaces'] ?? []);
        unset($data['namespaces']);

        // Set base
        if (!empty($data['base']) && empty(static::$_header['base'])) {
            static::$_header['base'] = $data['base'];
        }
        unset($data['base']);

        // Collect hydra members
        if (is_array($data) && isset($data['member'])) {
            $options['extension'] = '.' . static::$_extension;
            static::$_header['member'][] = static::renderValue($data['member'], $options);
        }
        unset($data['member']);

        // Prepare triples
        if (is_array($data) && isset($data['triples'])) {
            // Group by subject
            $subjects = Arrays::array_group_values($data['triples'], ['subject', 'predicate', 'object']);

            // Construct the set
            $data = [];
            foreach ($subjects as $subject => $predicates) {

                // Use the subject as @id
                $subject = static::renderValue($subject, $options);

                foreach ($predicates as $predicate => $objects) {
                    // Replace rdf:type with @type
                    if ($predicate === 'rdf:type') {
                        $predicate = '@type';
                    }

                    if (is_array($objects)) {
                        foreach ($objects as $object) {
                            $object = static::renderValue($object, $options);
                            $object = ($predicate === '@type') ? ($object['@id'] ?? '') : $object;
                            $subject[$predicate][] = $object;
                        }
                    }
                    else {
                        $object = static::renderValue($objects, $options);
                        $object = ($predicate === '@type') ? ($object['@id'] ?? '') : $object;
                        $subject[$predicate] = $object;
                    }
                }

                $data[] = $subject;
            }

            $data = static::expandSubjects($data);
        }

        return $data;
    }

    /**
     * Construct the JSONLD array from the triples
     *
     * // TODO: Handle values with reserved terms / characters
     *
     * @param $data
     * @param integer $level Nesting level of _prepareData calls
     */
    protected function _prepareViewData($data, $options = [], $level = 0)
    {
        // Prepare Hydra collection data
        if (($level === 0) && is_array($data)) {
            if (($options['params']['action'] ?? 'view') === 'index') {
                $this->_prepareCollection($data, $options);
                unset($data['pagination']);
            }
            // Triple data without surrounding tag
            else {
                $data = array_values($data);
            }
        }

        return $data;
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
     * @return array
     */
    static function renderValue($value, $options = [])
    {

        $baseIri = static::$_header['base'] ?? '';
        $namespaces = static::$_header['namespaces'] ?? [];
        list($value, $type) = Attributes::parseIriValue($value, $baseIri, $namespaces, true);

        // Full IRIs
        if ($type === 'iri') {
            $value = ['@id' => $value . ($options['extension'] ?? '')];
        }
        elseif ($type === 'prefixed name') {
            $value = ['@id' => $value . ($options['extension'] ?? '')];
        }
        else {
            $value = ['@value' => $value];

            if ($type !== 'literal') {
                $value["@type"]  = $type;
            }
        }

        return $value;
    }

    /**
     * Expand subjects by replacing references with the actual objects
     *
     * @param array $data
     * @return array
     */
    static function expandSubjects(array $data): array
    {
        // Index subjects by ID
        $subjectsById = [];
        foreach ($data as  $subject) {
            if (isset($subject['@id'])) {
                $subjectsById[$subject['@id']] = $subject;
            }
        }

        $consumedIds = [];

        // Funktion um eine node (Objekt in dritter Ebene) zu expandieren
        $expandNode = function ($node) use (&$expandNode, $subjectsById, &$consumedIds) {
            if (is_array($node)) {
                if (isset($node['@id']) && isset($subjectsById[$node['@id']])) {
                    $consumedIds[] = $node['@id'];
                    $expanded = $subjectsById[$node['@id']];

                    foreach ($expanded as $predicate => $object) {
                        if (is_array($object)) {
                            if (($predicate === '@id') || ($predicate === '@type')) {
                                continue;
                            }
                            $expanded[$predicate] = $expandNode($object);
                        }
                    }
                    return $expanded;
                }
                else {
                    foreach ($node as $k => $v) {
                        $node[$k] = $expandNode($v);
                    }
                    return $node;
                }
            }
            return $node;
        };

        // Expand all objects
        foreach ($data as &$subject) {
            foreach ($subject as $predicate => &$object) {
                if (($predicate === '@id') || ($predicate === '@type')) {
                    continue;
                }
                $object = $expandNode($object);
            }
        }
        unset($subject, $object);

        // Remove consumed subjects
        foreach ($data as $idx => $subject) {
            if (isset($subject['@id']) && in_array($subject['@id'], $consumedIds)) {
                unset($data[$idx]);
            }
        }
        unset($consumedIds);

        return array_values($data);
    }

    /**
     * Render the JSON-LD prolog
     *
     * Outputs @context and @set prolog.
     *
     * This is the first part of the wrapper, that's why the closing brackets are removed,
     * and added in renderEpilog().
     *
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    public function renderProlog($data, $options)
    {
        // Header (keep it open)
        $jsonld['@context'] = [];
        if (!empty(static::$_header['base'])) {
            $jsonld['@context']['@base'] = static::$_header['base'];
        }
        $jsonld['@context'] = array_merge($jsonld['@context'], static::$_header['namespaces']);
        $content = $this->renderArray($jsonld, $options);
        $content = preg_replace('/\s*}\s*$/', ",\n    ", $content);

        if (empty(static::$_header['member'])) {
            $content .= "\"@set\": [";
        }

        return $content;

    }

    /**
     * Render the epilog of the document: Close the list and object.
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    function renderEpilog($data, $options)
    {
        $content = '';

        // Entity view: Close list
        if (empty(static::$_header['member'])) {
            $content .= "    ]\n";
        }

        // Close object
        $content .= '}';
        return $content;
    }

    /**
     * Render the entity content
     *
     * @param array|EntityInterface $data
     * @param array $options
     * @param int $level The level of indentation
     * @return string
     */
    public function renderContent($data, $options = [], $level = 0)
    {
        $isDocument = (is_object($data) && ($data instanceof ExportEntityInterface));
        $data = $this->extractData($data, $options);

        // Collection view
        if (!empty(static::$_header['member'])) {
            $jsonld = $this->_getCollection();
            $content = $this->renderArray($jsonld, $options);

            // Remove brackets (they are rendered in the prolog and epilog)
            $content = preg_replace('/^\s*{\s*/', "", $content);
            $content = preg_replace('/}\s*$/', "", $content);
        }
        // Entity view
        else {
            // Remove first nesting level
            $jsonld = [];
            if (!$isDocument) {
                foreach ($data as $dataPart) {
                    $jsonld = array_merge($jsonld, $dataPart);
                }
            }
            else {
                $jsonld = $data;
            }
            $content = $this->renderArray($jsonld, $options);

            // Remove brackets (they are rendered in the prolog and epilog)
            $content = preg_replace('/^\s*\[/', "", $content);
            $content = preg_replace('/]\s*$/', "", $content);
        }

        $content = preg_replace('/^/m', '    ', $content);
        return $content;
    }
}
