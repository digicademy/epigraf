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
                            $subject[$predicate][] = ($predicate === '@type') ? ($object['@id'] ?? '') : $object;
                        }
                    }
                    else {
                        $object = static::renderValue($objects, $options);
                        $subject[$predicate] = ($predicate === '@type') ? ($object['@id'] ?? '') : $object;
                    }
                }

                $data[] = $subject;
            }
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
            $value = [
                '@id' => $value . ($options['extension'] ?? '')
            ];
        }
        elseif ($type === 'prefixed name') {
            $value = [
                '@id' => $value . ($options['extension'] ?? '')
            ];
        }
        else {
            $value = [
                '@value' => $value
            ];
        }

        return $value;
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

        // Header
        $jsonld['@context'] = [];
        if (!empty(static::$_header['base'])) {
            $jsonld['@context']['@base'] = static::$_header['base'];
        }
        $jsonld['@context'] = array_merge($jsonld['@context'], static::$_header['namespaces']);

        if (!empty(static::$_header['member'])) {
            $jsonld = array_merge($jsonld, $this->_getCollection());
        }
        else {
            // Remove first nesting level
            if (!$isDocument) {
                $jsonld['@set'] = [];
                foreach ($data as $dataPart) {
                    $jsonld['@set'] = array_merge($jsonld['@set'], $dataPart);
                }
            }
            else {
                $jsonld['@set'] = $data;
            }
        }

        return $this->renderArray($jsonld, $options);
    }

}
