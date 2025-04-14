<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Behavior;

use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Locator\LocatorAwareTrait;
use App\Utilities\XmlParser\XmlMunge;
use App\Utilities\Converters\Numbers;
use Cake\ORM\RulesChecker;

/**
 * XmlStyles behavior
 */
class XmlStylesBehavior extends Behavior
{

    // To get Table instances
    use LocatorAwareTrait;

    /**
     * Default configuration.
     * - fields: Enable beforeFind and beforeSave for the given fields
     * @var array
     */
    protected $_defaultConfig = [
        'fields' => []
    ];

    /**
     * Current rendering status.
     *
     * Each call to disableRendering() increases the value,
     * each call to enableRendering() decreases the value.
     * The minimum value is 0.
     * Rendering is only performed when $_disableRendering is 0.
     *
     * @var integer
     */
    public $_disableRendering = 0;

    /**
     * Current styles
     *
     * @var array
     */
    public $styles = [];

    /**
     * Current tags
     *
     * @var array
     */
    public $tags = [];

    /**
     * Current counters
     *
     * @var array
     */
    public $counters = [];

    /**
     * Initialize hook
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {

    }


    /**
     * Unformat fields, e.g. from HTML to XML
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $data
     * @param ArrayObject $options
     */
    public function afterMarshal(
        EventInterface $event,
        EntityInterface $entity,
        ArrayObject $data,
        ArrayObject $options
    ) {
        if ($this->_disableRendering === 0) {
            $config = $this->getConfig();
            foreach ($config['fields'] as $fieldName) {
                if ($entity->isDirty($fieldName)) {

                    $entity->set(
                        $fieldName,
                        $entity->getValueUnformatted($fieldName),
                        ['format' => $entity->getFieldFormat($fieldName)]
                    );

                }
            }
        }
    }

    /**
     * Prevent saving entities that contain parsing errors
     *
     * @param EventInterface $event
     * @param RulesChecker $rules
     * @return void
     */
    public function buildRules(EventInterface $event, RulesChecker $rules)
    {
        $rules->add(function (EntityInterface $entity, array $options) {
            if (!empty($entity->_parsing_errors)) {
                $entity->setErrors($entity->_parsing_errors);
                return false;
            }
            return true;
        }, 'wellformed');

    }

    /**
     * Load the XML tag config
     *
     * @return array|mixed
     */
    public function loadStyles()
    {
        if (!empty($this->tags)) {
            return $this->tags;
        }

        $this->tags = array_merge(
            $this->table()->getDatabase()->types['links'] ?? [],
            $this->table()->getDatabase()->types['footnotes']  ?? []
        );

        return $this->tags;
    }


    /**
     * @param $name
     * @return array|mixed
     */
    public function getCounter($name)
    {
        return $this->counters[$name] ?? [];
    }

    /**
     * Render attributes either to text or to element attributes
     *
     * @param array $element The element attributes will be updated
     * @param array $style The style definition determines which attributes will be processed
     * @param string $format Output format to get the correct style configuration (html|txt|md|rdf|ttl|jsonld)
     * @return array The result array contains the following values
     *               -  keys 'open' or 'close': the bracket content
     *               - 'content' key: the text content
     *               - 'attributes' key: the data-attributes.
     */
    protected static function renderAttributes(&$element, $style, $format = 'html')
    {

        // Unstyled tags
        if (empty($style)) {

            $style = [
                'merged' => [
                    'prefix' => $format === 'html' ? ('<' . $element['name'] . '>') : '',
                    'postfix' => $format === 'html' ? ('</' . $element['name'] . '>') : '',
                    'attributes' => array_diff_key(
                        $element['attributes'] ?? [],
                        ['id'=>false, 'data-type'=>false, 'data-tagid'=>false, 'class'=>false]
                    )
                ]
            ];
        }

        // Static content
        // TODO: Remove html_prefix, html_content, html_postfix
        $content = [
            'prefix' =>  $style['merged'][$format]['prefix'] ?? $style['merged']['prefix'] ?? $style['merged']['html_prefix'] ?? '',
            'text' =>  $style['merged'][$format]['content'] ?? $style['merged']['content'] ?? $style['merged']['html_content'] ?? '',
            'postfix' =>  $style['merged'][$format]['postfix'] ?? $style['merged']['postfix']  ?? $style['merged']['html_postfix'] ?? ''
        ];

        // Fields
        $tagContent = [];
        foreach (($style['merged']['fields'] ?? []) as $fieldName => $fieldConfig) {
            if (isset($fieldConfig['render'])) {
                $tagContent[$fieldConfig['render']] = $tagContent[$fieldConfig['render']] ?? '';

                // Counter
                if (($fieldConfig['format'] ?? '') === 'counter') {
                    $tagContent[$fieldConfig['render']] .=
                        //'<span class="xml_counter">'
                        Numbers::numberToString($element['number'] ?? 1, $fieldConfig['counter'] ?? 'numeric');
                    //. '</span>';
                }

                // Property or external record target value
                elseif (($fieldConfig['format'] ?? '') === 'record') {
                    $tagContent[$fieldConfig['render']] = $element['attributes']['data-link-value'] ?? '';
                }

                // Internal target value
                elseif (($fieldConfig['format'] ?? '') === 'relation') {
                    $tagContent[$fieldConfig['render']] = $element['attributes']['data-link-value'] ?? '';
                }
            }
        }
        $content['prefix'] .= $tagContent['prefix'] ?? '';
        $content['text'] .= $tagContent['text'] ?? '';
        $content['postfix'] = ($tagContent['postfix'] ?? '') . $content['postfix'];

        // Attributes
        $attrContent = [];
        foreach (($style['merged']['attributes'] ?? []) as $attrName => $attrConfig) {

            // Set data attributes
            $content['attributes']['data-attr-' . $attrName] = $element['attributes'][$attrName] ?? '';

            // Repeat renderer
            if (isset($attrConfig['render'])) {
                $attrValue = $element['attributes'][$attrName] ?? null;

                // Repeat renderer
                if (isset($attrConfig['repeat'])) {
                    $num = (int)$element['attributes'][$attrName] ?? 0;
                    if (!empty($num)) {
                        $attrValue = str_repeat($attrConfig['repeat'] , $num);
                    } else {
                        $attrValue = $attrConfig['default'] ?? $attrValue;
                    }
                }

                $attrValue = $attrValue ?? $attrConfig['default'] ?? '';

                if ($attrConfig['render'] !== 'attribute') {
                    $attrContent[$attrConfig['render']] =  ($attrContent[$attrConfig['render']]  ?? '') . $attrValue;
                    unset($element['attributes'][$attrName]);
                } else {
                    $element['attributes'][$attrName] = $attrValue;
                }
            } else {
                unset($element['attributes'][$attrName]);
            }
        }

        $content['prefix'] .= $attrContent['prefix'] ?? '';
        $content['text'] .= $attrContent['text'] ?? '';
        $content['postfix'] = ($attrContent['postfix'] ?? '') . $content['postfix'];

        return $content;
    }

    /**
     * Render XML to HTML, Markdown or plain text, based on the types configuration
     *
     * TODO:refactor, move to entity or to trait
     *
     * @param array $data
     * @param string $format See RENDERED_FORMATS constant
     * @return array|false|mixed|string
     */
    public function renderXmlFields($data = [], $format='html')
    {

        $counters = &$this->counters;
        $xmlstyles = $this->loadStyles();

        $callback_tags = static function (&$element, &$parser) use (&$counters, $xmlstyles, $format) {

            $style = $xmlstyles[$element['name']] ?? [];
            $tagid = $element['attributes']['id'] ?? '';
            $tagname = $element['name'];
            // TODO: render unstyled empty tags as standalone tags
            $tagtype = $style['merged'][$format]['tag_type'] ?? $style['merged']['tag_type'] ?? 'bracket';

            $element['rename'] = $style['merged'][$format]['tag'] ?? $style['merged']['html_tag'] ?? $style['merged']['tag'] ?? 'span';
            $element['attributes']['data-type'] = $style['name'] ?? $tagname;
            $element['attributes']['data-tagid'] = $tagid;
            // TODO: more elegant class handling (use array)
            $element['attributes']['class'] = 'xml_tag xml_tag_' . $tagname;

            // Count tag order
            if ($element['position'] == 'open') {
                if (!isset($counters[$tagname][$tagid])) {
                    $counters[$tagname][$tagid] = empty($counters[$tagname]) ? 1 : count($counters[$tagname]) + 1;
                }

                if (!isset($counters['tags'][$tagid])) {
                    $counters['tags'][$tagid] = empty($counters['tags']) ? 1 : count($counters['tags']) + 1;
                }

                $element['number'] = $counters[$tagname][$tagid] ?? null;
            } else {
                $element['number'] = $counters[$tagname][$tagid] ?? null;
            }

            // Breaks
            if ($tagtype === 'break') {
                $element['customoutput'] = $format === 'html' ? '<br>' : "\n";

                // Ignore content
                $parser->parseCurrentElement();
            }

            // Empty
            elseif (($tagtype === 'empty') && ($element['position'] == 'open')) {
                if ($format === 'html') {
                    $element['customoutput'] = '<' . $element['rename'] . ' class="xml_tag xml_tag_' . $tagname . ' xml_empty" data-tagid="' . $tagid . '" data-type="' . $style['name'] . '">';
                } else {
                    $element['customoutput'] = '';
                }

                // Ignore content
                $parser->parseCurrentElement();
            }

            // Formats
            elseif ($tagtype === 'format') {

                // Render attributes
                $content = XmlStylesBehavior::renderAttributes($element, $style);
                $element['attributes'] = array_merge(
                    $element['attributes'],
                    $content['attributes'] ?? []
                );

                if ($format === 'html') {
                    $element['attributes']['class'] = $element['attributes']['class'] . ' xml_format';
                    $value = $parser->_openElement($element, true);
                    $value .= $parser->parseCurrentElement();
                    $value .= $parser->_closeElement($element);
                } else {
                    $value = $parser->parseCurrentElement();
                }

                $element['customoutput'] = $value;
            }

            // Text
            elseif (($tagtype === 'text') && ($element['position'] === 'open')) {
                // Attribute content
                $content = XmlStylesBehavior::renderAttributes($element, $style, $format);
                $elementContent = $parser->parseCurrentElement();
                if ($content['text'] === '') {
                    $content['text'] = $elementContent;
                }

                $element['customoutput'] = '';

                if ($format === 'html') {
                    $element['attributes'] = array_merge($element['attributes'], $content['attributes'] ?? []);
                    $element['attributes']['class'] = $element['attributes']['class'] .= ' xml_text';
                    $element['customoutput'] = $parser->_openElement($element, true);

                    $element['customoutput'] .= htmlentities($content['prefix'] . $content['text'] . $content['postfix']);

                    $element['customoutput'] .= $parser->_closeElement($element);
                    //TODO: hack for CKEditor whitespace after tag (inline widget) problem, add zws
                    $element['customoutput'] .= '&#8203;';
                } else {
                    $element['customoutput'] .= $content['prefix'] . $content['text'] . $content['postfix'];
                }

            }

            // Brackets
            elseif (($tagtype === 'bracket') && ($element['position'] == 'open')) {
                // Render attributes
                $content = XmlStylesBehavior::renderAttributes($element, $style, $format);

                // Common bracket attributes
                $bracketAttributes = array_merge(
                    [
                        'class' => 'xml_tag xml_tag_' . $tagname . ' xml_bracket',
                        'data-tagid' => $tagid,
                        'data-type' => $style['name'] ?? $tagname
                    ],
                    $content['attributes'] ?? []
                );

                if (empty($style)) {
                    $bracketAttributes['class'] .= ' xml_notstyled';
                }

                if ($format === 'html') {
                    $element['customoutput'] =
                        '<span ' . Attributes::toHtml($bracketAttributes) . '>' .
                        '<span class="xml_bracket_open">' . htmlentities($content['prefix']) . '</span>' .
                        '<span class="xml_bracket_content">' . $parser->parseCurrentElement() . '</span>' .
                        '<span class="xml_bracket_close">' . htmlentities($content['postfix']) . '</span>' .
                        '</span>';
                } else {
                    $element['customoutput'] = $content['prefix'] . $parser->parseCurrentElement() . $content['postfix'];
                }
            }

            else {
                return false;
            }

            return true;
        };

        if (is_string($data)) {
            $data = XmlMunge::parseXmlString($data, $callback_tags);
            if (in_array($format, PLAINTEXT_FORMATS)) {
                $data = html_entity_decode($data);
            }
        }
        elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->renderXmlFields($value, $format);
            }
        }

        return $data;
    }

    /**
     * Replace <anno> tags with <span> tags and match IRIs to IDs
     *
     * Used to post process the LLM annotations.
     *
     * @param string $value XML input text
     * @param string $annoType The annotation type (i.e. the link type as configured in the types table)
     * @param string $scope The scope of the linked data (i.e. the property type)
     * @return string XML text with replaced tags
     */
    public function renderAnnotations($value, $annoType, $scope) : string
    {
        $properties = $this->table()->getExportData(['scope' => $scope]);
        $properties = Arrays::array_group($properties,'norm_iri', true);

        // Replace opening tags
        $regexOpen = '/<anno\s+value="([^"]*)"\s*>/';
        $value = preg_replace_callback(
            $regexOpen,
            function ($matches) use ($annoType, $properties){
                $annoValue = $matches[1];

                $propertyId = $properties[$annoValue]['id'] ?? '';
                $propertyCaption = $properties[$annoValue]['path'] ?? $annoValue;

                $attributes = " data-link-id=\"{$propertyId}\"";
                $attributes .= " data-link-value=\"{$propertyCaption}\"";
                $attributes .= " data-target-tab=\"properties\"";
                $attributes .= " data-target-id=\"{$propertyId}\"";

                return "<span class=\"xml_format xml_tag_{$annoType}\" data-type=\"{$annoType}\"{$attributes}>";
            },
            $value
        );

        // Replace closing tags
        $value = str_replace('</anno>', '</span>', $value);

        return $value;
    }

    /**
     * Convert HTML to XML
     * (replace span ... elements)
     *
     * TODO: implement renaming on closing tags instead of hacky use of _openElement / _closeElement
     *
     * @param array $data
     * @return array|false|mixed|string
     */
    public function deRenderXmlFields($data = [])
    {

        $callback_tags = static function (&$element, &$parser) {

            $element['rename'] = $element['attributes']['data-type'] ?? $element['name'];

            // Get and remove ids
            $element['attributes']['id'] = $element['attributes']['data-tagid'] ?? $element['attributes']['id'] ?? '';
            unset($element['attributes']['data-tagid']);
            unset($element['attributes']['data-attr-id']);
            if ($element['attributes']['id'] === '') {
                unset($element['attributes']['id']);
            }

            // Get and remove classes
            $classes = explode(' ', $element['attributes']['class'] ?? '');
            unset($element['attributes']['class']);

            // Rename and remove all data attributes
            foreach (($element['attributes'] ?? []) as $attrName => $attrValue) {
                if (str_starts_with($attrName, 'data-attr-')) {
                    $attrNewName = substr($attrName, strlen('data-attr-'));
                    $element['attributes'][$attrNewName] = $attrValue;
                    unset($element['attributes'][$attrName]);
                } elseif (str_starts_with($attrName, 'data-')) {
                    unset($element['attributes'][$attrName]);
                }
            }

            // Cleanup (mark.js highlights)
            if (in_array($element['name'],['mark'])) {
                $element['customoutput'] .= $parser->parseCurrentElement();
            }

            // Formate
            elseif (in_array('xml_format', $classes) && ($element['position'] == 'open')) {
                $element['customoutput'] = $parser->_openElement($element);
                $element['customoutput'] .= $parser->parseCurrentElement();
                $element['customoutput'] .= $parser->_closeElement($element);
            }

            // Empty
            elseif (in_array('xml_empty', $classes) && ($element['position'] == 'open')) {
                $element['empty'] = true;
                $element['customoutput'] = $parser->_openElement($element);
                $element['empty'] = false;
                $parser->parseCurrentElement();
            }

            // Standalone, counter (app1, app2), text from database, attributes
            elseif (in_array('xml_text', $classes) && ($element['position'] == 'open')) {
                $element['empty'] = true;
                $element['customoutput'] = $parser->_openElement($element);
                $element['empty'] = false;
                $parser->parseCurrentElement();
            }

            // Klammern
            elseif (in_array('xml_bracket', $classes) && ($element['position'] == 'open')) {
                $element['customoutput'] = $parser->_openElement($element);
                $element['customoutput'] .= $parser->parseCurrentElement();
                $element['customoutput'] .= $parser->_closeElement($element);
            }

            elseif (in_array('xml_bracket_open', $classes) && ($element['position'] == 'open')) {
                $element['customoutput'] = '';
                $parser->parseCurrentElement();
            }

            elseif (in_array('xml_bracket_close', $classes) && ($element['position'] == 'open')) {
                $element['customoutput'] = '';
                $parser->parseCurrentElement();
            }

            elseif (in_array('xml_bracket_content', $classes) && ($element['position'] == 'open')) {
                $element['customoutput'] = $parser->parseCurrentElement();
            }

            // Default
            elseif (($element['position'] == 'open') && !$element['empty']) {
                $element['customoutput'] = $parser->_openElement($element);
                $element['customoutput'] .= $parser->parseCurrentElement();
                $element['customoutput'] .= $parser->_closeElement($element);
            }
            elseif ($element['empty'] ?? false) {
                $element['customoutput'] = $parser->_openElement($element);
                $parser->parseCurrentElement();
            }

            else {
                $element['customoutput'] = '';
            }

            return true;
        };

        if (is_string($data) && ($data !== "")) {
            // Replace HTML breaks. TODO: use configuration
            $data = str_replace('<br />', '<nl />', $data);
            $data = str_replace('<br/>', '<nl />', $data);

            // Just a safety procedure for buggy browsers
            $data = str_replace('&nbsp;', '&#160;', $data);

            // Ckeditor hack: replace zws
            $data = str_replace('&#8203;', '', $data);
            $data = preg_replace('/\x{200B}/u', '', $data);

            $data = XmlMunge::parseXmlString($data, $callback_tags);
        }
        elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->deRenderXmlFields($value);
            }
        }

        return $data;
    }

    /**
     * Disable rendering
     *
     * @return void
     */
    public function disableRendering()
    {
        $this->_disableRendering += 1;
    }

    /**
     * Activates the callbacks
     *
     * @return void
     */
    public function enableRendering()
    {
        $this->_disableRendering -= 1;
        if ($this->_disableRendering < 0) {
            $this->_disableRendering = 0;
        }
    }
}
