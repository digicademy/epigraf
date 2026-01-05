<?php
/**
 * Epigraf 5.0
 *
 * Originally developed for Lichtenberg Online by Jakob Jünger.
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Utilities\XmlParser;

use XMLReader;

/**
 * XML import class
 */
class XmlImport
{
    /**
     * Current XML reader
     *
     * @var null
     */
    var $reader = null;

    /**
     * Current element stack
     *
     * @var array
     */
    var $stack = array();

    /**
     * Anchors
     * @var array
     */
    var $anchors = array();

    /**
     * Current tracks
     *
     * @var array
     */
    var $tracks = array();

    /**
     * Current element
     *
     * @var bool
     */
    var $currentelement = false;

    /**
     * Current context
     *
     * @var bool
     */
    var $currentcontext = false;

    /**
     * Current list of contexts
     *
     * @var array
     */
    var $contexts = array();

    /**
     * Default prefix
     *
     * @var string
     */
    var $renamePrefix = 'tei_';

    /**
     * Current tags nummber
     *
     * @var int
     */
    var $tagcounter = 0;

//   var $wrapTabs = false;
//   var $tabCount = 0;

    /**
     * Default XML reader configuration
     *
     * @var array
     */
    var $rawoutput = array(
        XMLReader::TEXT,
        XMLReader::ENTITY,
        XMLReader::ENTITY_REF,
        XMLReader::CDATA,
        XMLReader::COMMENT,
        XMLReader::DOC_TYPE,
        XMLReader::WHITESPACE,
        XMLReader::SIGNIFICANT_WHITESPACE,
    );

    /* Optionen */

    /**
     * Current element handler
     *
     * @var null
     */
    var $elementhandler = null;

    /**
     * Current text handler
     *
     * @var bool
     */
    var $texthandler = false;

    /**
     * Default setting for renaming elements
     *
     * @var array
     */
    var $match = array();

    /**
     * Default setting for renaming attributes
     *
     * @var bool
     */
    var $renameattributes = false;

    public $errors = [];
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_initParser();
    }

    /**
     * Init XML reader
     *
     * @return void
     */
    protected function _initParser()
    {
        $this->stack = array();
        $this->tracks = array();
        $this->anchors = array();
        $this->currentelement = false;

        $this->contexts = array();
        $this->contexts[] = array('output' => '');

        $this->reader = new XMLReader();

        //$this->reader->setParserProperty(XMLReader::SUBST_ENTITIES,false);
    }

    /**
     * Get a unique id
     *
     * @return array|string|string[]
     */
    public function uniqueid()
    {
        return str_replace('.', '', uniqid('', true));
    }

    /**
     * Element handler
     *
     * @param $element
     *
     * @return bool
     */
    protected function _handleCurrentElement(&$element)
    {
        if (count($this->stack) == 0) {
            $element['newcontext'] = true;
        }

        if ($element['name'] == 'document') {
            $element['customoutput'] = '';
            return false;
        }

        if (!$this->elementhandler) {
            return false;
        }

        if (is_array($this->elementhandler)) {
            $this->elementhandler[0]->{$this->elementhandler[1]}($element, $this);
        }
        else {
            call_user_func_array($this->elementhandler, array(&$element, &$this));
        }
        //$this->elementhandler($element,$this);

        // Rename attributes and element name
        if (!isset($element['renameattributes']) || $element['renameattributes']) {
            $this->_renameAttributes($element);
        }
        $this->_renameElement($element);

        return true;
    }

    /**
     * Text handler
     *
     * @param $content
     *
     * @return bool
     */
    protected function _handleText(&$content)
    {
        if (!$this->texthandler) {
            return false;
        }

        $this->texthandler[0]->{$this->texthandler[1]}($content, $this);
        return true;
    }

    /**
     * Convert attributes to class names
     *
     * @param $key
     * @param $value
     *
     * @return string
     */
    protected function _attributeToClasses($key = null, $value = null)
    {
        //Replace forbidden chars
        $value = str_replace(array('#', ':'), array('', '-'), $value);

        //Split by whitespace
        if (($key == 'rend') || ($key == 'style')) {
            $value = preg_split("/;+/", $value, null, PREG_SPLIT_NO_EMPTY);
        }
        //else $value = preg_split("/[\s,;]+/",$value,null,PREG_SPLIT_NO_EMPTY);
        else {
            $value = array($value);
        }

        //Prefix with key
        $classes = array();
        for ($i = 0; $i < count($value); $i++) {
            $classes[] = $key . '_' . str_replace(array(' ', '.'), array('', '-'), $value[$i]);
        }

        //Output
        return implode(' ', $classes);
    }

    /**
     * Rename attributes
     *
     * @param $element
     *
     * @return bool
     */
    protected function _renameAttributes(&$element)
    {
        if (($element['position'] == 'close') || (!$this->renameattributes)) {
            return false;
        }

        //Name
        $classes = array($this->renamePrefix . $element['name']);

        //Klassen
        if (isset($element['attributes'])):

            if (isset($element['attributes']['class'])) {
                $classes[] = $element['attributes']['class'];
            }

            unset($element['attributes']['class']);

            //Attribute
            foreach ($element['attributes'] as $key => $value) {
                if (in_array($key, array('id'))) {
                    continue;
                }

                //Umwandeln
                $classes[] = $this->_attributeToClasses($key, $value);

                //Unset
                unset($element['attributes'][$key]);
            }

            //Unhandled Elements
            if (!isset($this->match[$element['name']])) {
                $classes[] = 'unhandled_element';
            }

            $element['attributes']['class'] = implode(' ', $classes);
        endif;

        return true;
    }

    /* Allgemeine Funktionen */

    /**
     * Import XML file
     *
     * @param $filename
     *
     * @return false|mixed
     */
    public function importXMLFile($filename = null)
    {
        libxml_use_internal_errors(true);
        $this->errors = array();

        if (!$this->reader->open($filename)) {
            return false;
        }

        $this->contexts[0]['output'] = '';
        $this->tagcounter = 0;
        $this->parseCurrentElement($this->contexts[0]['output']);

        foreach (libxml_get_errors() as $error) {
            $this->errors[] = $error->message;
        }
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        return $this->contexts[0]['output'];
    }

    /**
     * Import XML string
     *
     * @param $content
     *
     * @return false|mixed|string
     */
    public function importXMLString($content = null)
    {
        if ($content == '') {
            return '';
        }

        if (!$this->reader->xml('<document>' . $content . '</document>', 'UTF-8')) {
            return false;
        }

        //$this->reader->setParserProperty(XMLReader::VALIDATE, true);
        //if (!$this->reader->isValid()) return false;

        $this->contexts[0]['output'] = '';
        $this->tagcounter = 0;
        //$this->_openTags($this->contexts[0]['output']);

//        set_error_handler(function($errno, $errstr, $errfile, $errline) {
//            // error was suppressed with the @-operator
//            if (0 === error_reporting()) {
//                return false;
//            }
//
//            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
//        });

//        try {
        $this->parseCurrentElement($this->contexts[0]['output']);
//        } catch (Exception $e) {
//            $this->contexts[0]['output'] = 'ERROR: '.$e->getMessage();
//        }


        //$this->_closeTags($this->contexts[0]['output']);

        return $this->contexts[0]['output'];
    }


    /**
     * Convert current element
     *
     * Continue parsing of the current element
     * and output transformed content
     *
     * @param null $output
     * @param false $outputelement
     *
     * @return mixed|string|null
     */
    public function parseCurrentElement(&$output = null, $outputelement = false)
    {
        if ($output === null) {
            $output = '';
        }

        // Open parent element (if requested by $outputelement)
        if ($outputelement & ($this->currentelement !== false)) {
            $this->_outputElement($this->currentelement, $output);
        }

        // Nicht-leere Tags
        if (!(($this->currentelement !== false) && ($this->currentelement['empty']))) {
            $oldelement = $this->currentelement;
            $tagcounter = $this->tagcounter;

            if ($oldelement !== false) {
                $oldelement['newcontext'] = true;
                $this->_pushElement($oldelement);
            }

            while ($this->reader->read()) {
                //Text
                if (in_array($this->reader->nodeType, $this->rawoutput)) {
                    $this->_outputText($output);
                }

                //Processing instructions
                elseif (in_array($this->reader->nodeType, array(XMLReader::PI, XMLReader::XML_DECLARATION))) {
                    //         $pi = $this->_getCurrentElement();
                    //         $this->_outputElement($pi,$output);
                }

                //Empty element
                elseif (($this->reader->nodeType == XMLReader::ELEMENT) && $this->reader->isEmptyElement) {
                    $this->currentelement = $this->_getCurrentElement();
                    $this->_handleCurrentElement($this->currentelement);
                    $this->_outputElement($this->currentelement, $output);
                }

                //Element start
                elseif (($this->reader->nodeType == XMLReader::ELEMENT)) {
                    $this->tagcounter++;

                    //if (($oldelement !== false) && (!$this->reader->isEmptyElement) && ($this->reader->name == $oldelement['name'] ))
                    // $tagcounter++;

                    $this->currentelement = $this->_getCurrentElement();
                    $this->_handleCurrentElement($this->currentelement);
                    $this->_outputElement($this->currentelement, $output);

                    $this->_pushElement($this->currentelement);
                }

                //Element end
                elseif (($this->reader->nodeType == XMLReader::END_ELEMENT)) {
                    $this->tagcounter--;

                    // If all child elements of $oldelement are consumed, break
                    if (($oldelement !== false) && ($tagcounter > $this->tagcounter)) {
                        break;
                    }

                    //if (($oldelement !== false) && ($this->reader->name == $oldelement['name'] )) $tagcounter--;
                    $this->currentelement = $this->_getCurrentElement();
                    $this->_handleCurrentElement($this->currentelement);

                    // Close (inner?) wrappers
                    $this->_closeTags($output, $this->currentelement);

                    // Output element
                    $this->_outputElement($this->currentelement, $output);

                    // Reopen (inner?) wrappers
                    $this->_openTags($output, $this->currentelement);

                    $this->_popElement($this->currentelement);
                }
            }

            // Close parent element (if requested by $outputelement)
            $this->currentelement = $oldelement;
            if ($outputelement & ($this->currentelement !== false)) {
                $this->_outputElement($this->currentelement, $output, true);
            }

            //
            if ($this->currentelement !== false) {
                $this->_popElement($this->currentelement);
                $this->currentelement['empty'] = true;
            }
        }

        return $output;
    }

    /**
     * Current element handler
     *
     * Convert current element to array.
     *
     * @return array|false
     */
    protected function _getCurrentElement()
    {
        if ($this->reader->nodeType == XMLReader::END_ELEMENT) {
            $result = array(
                'name' => $this->reader->name,
                'position' => 'close'
            );
        }

        elseif ($this->reader->nodeType == XMLReader::ELEMENT) {

            $result = array(
                'name' => $this->reader->name,
                'empty' => $this->reader->isEmptyElement,
                'position' => 'open',
                'attributes' => array()
            );

            if ($this->reader->hasAttributes) {
                while ($this->reader->moveToNextAttribute()) {
                    $result['attributes'][$this->reader->name] = $this->reader->value;
                }
            }

            //ID ergänzen, wichtig für _openTags und _closeTags
            //if (!isset($result['attributes']['id'])) $result['attributes']['id'] = $this->_uniqueid();

        }

        else {
            $result = false;
        }

        return $result;
    }

    /**
     * Rename element
     *
     * @param $element
     *
     * @return void
     */
    protected function _renameElement(&$element)
    {
        if (isset($this->match[$element['name']])) {
            $element['rename'] = $this->match[$element['name']];
        }
    }

    /**
     * Create opening tag
     *
     * Empty elements are immediately closed.
     * The immediate closing can be overridden with $forceopen = true;
     *
     * @param $element
     * @param false $forceopen
     *
     * @return string
     */
    public function _openElement($element, $forceopen = false)
    {
        $name = isset($element['rename']) ? $element['rename'] : $element['name'];
        if ($name != '') {
            $result = '<' . $name;
            foreach ($element['attributes'] as $key => $value) {
                $result .= ' ' . $key . '="' . htmlspecialchars($value ?? '', ENT_XML1 | ENT_COMPAT, 'UTF-8') . '"';
            }
            if (!empty($element['empty']) && !$forceopen) {
                $result .= ' /';
            }
            $result .= '>';
            //if (!empty($element['empty']) && !$forceopen)  $result .= '</'.$name.'>';
        }
        else {
            $result = '';
        }
        return $result;
    }

    /**
     * Create closing tag
     *
     * @param $element
     *
     * @return string
     */
    public function _closeElement($element)
    {
        $name = isset($element['rename']) ? $element['rename'] : $element['name'];
        $result = ($name != '') ? '</' . $name . '>' : '';
        return $result;
    }

    /**
     * Create element
     *
     * @param $element
     * @param $output
     * @param $forceclose
     *
     * @return bool
     */
    protected function _outputElement($element, &$output, $forceclose = false)
    {
        if (($output === null) ||
            (isset($element['disabled']) && ($element['disabled'] == true))) {
            return false;
        }

        if (isset($element['customcontent'])) {
            $result = $this->_openElement($element, true);
            $result .= $element['customcontent'];
            $result .= $this->_closeElement($element);
        }

        elseif (isset($element['customoutput'])) {
            $result = $element['customoutput'];
        }
        elseif (($element['position'] == 'close') || $forceclose) {
            $result = $this->_closeElement($element);
        }

        elseif ($element['position'] == 'open') {
            $result = $this->_openElement($element);
        }

        else {
            $result = '';
        }

        $this->_output($output, $result);

        return true;
    }

    /**
     * Begin track
     *
     * @param $trackid
     *
     * @return void
     */
    public function beginTrack($trackid)
    {
        $this->tracks[$trackid] = '';
    }

    /**
     * End track
     *
     * @param $trackid
     *
     * @return false|mixed
     */
    public function endTrack($trackid)
    {
        if (isset($this->tracks[$trackid])) {
            $track = $this->tracks[$trackid];
            unset($this->tracks[$trackid]);
            return $track;
        }
        else {
            return false;
        }
    }

    /**
     * Check current track
     *
     * @param $trackid
     *
     * @return false|mixed
     */
    public function getTrack($trackid)
    {
        return isset($this->tracks[$trackid]) ? $this->tracks[$trackid] : false;
    }

    /**
     * Create output from current tracks
     *
     * @param $output
     * @param $append
     *
     * @return void
     */
    protected function _output(&$output, $append)
    {
        foreach ($this->tracks as $key => $value) {
            $this->tracks[$key] = $value . $append;
        }
        $output .= $append;
    }

    /**
     * Begin wrap
     *
     * @param $wrapperid
     * @param $wrap
     * @param $output
     *
     * @return void
     */
    protected function _beginWrap($wrapperid, $wrap, &$output)
    {
        //Wenn Anchor vorangegangen ist, alles hier ausgeben
        if (isset($this->anchors[$wrapperid])) {

            $this->_outputElement($wrap, $output);
            $this->_output($output, $wrap['inhalt']);
            $this->_outputElement($wrap, $output, true);

        }

        //Sonst Wrap starten
        else {
            $wrap['wrapperid'] = $wrapperid;

            $this->_outputElement($wrap, $output);
            $this->_pushElement($wrap);

        }
    }

    /**
     * Check wrap
     *
     * @param $wrapperid
     *
     * @return bool
     */
    protected function _isWrap($wrapperid)
    {
        foreach ($this->stack as $tag) {
            if (isset($tag['wrapperid']) && ($tag['wrapperid'] == $wrapperid)) {
                return true;
            }
        }
        return false;
    }

    /**
     * End wrap
     *
     * @param $wrapperid
     * @param $anchor
     * @param $output
     *
     * @return void
     */
    protected function _endWrap($wrapperid, $anchor, &$output)
    {
        //Anchor markieren
        if ($anchor) {
            $this->anchors[$wrapperid] = false;
        }

        //Wrap schließen
        while ($this->_isWrap($wrapperid)) {

            $close = '';
            $open = '';

            $this->stack = array_reverse($this->stack);
            foreach ($this->stack as $key => $tag) {
                $this->_outputElement($tag, $close, true);
                if (isset($tag['wrapperid']) && ($tag['wrapperid'] == $wrapperid)) {
                    unset($this->stack[$key]);
                    break;
                }

                $reopen = '';
                $this->_outputElement($tag, $reopen);
                $open = $reopen . $open;
            }

            $this->stack = array_reverse($this->stack);

            $this->_output($output, $close . $open);
        }
    }

    /**
     * Check element in context
     *
     * @param $elementname
     *
     * @return bool
     */
    protected function _isInContext($elementname)
    {
//   $context = array();
        $this->stack = array_reverse($this->stack);
        $result = false;
        foreach ($this->stack as $tag) {
            //if (isset($tag['newcontext']) && ($tag['newcontext'] == true) && ($tag['name']==$elementname)  ) return true;
//      if (isset($tag['newcontext']) && ($tag['newcontext'] == true)) $context[] = $tag['name'];
            if ($tag['name'] == $elementname) {
                $result = true;
                break;
            }
        }

        $this->stack = array_reverse($this->stack);
//    debug($context);
        return $result;

    }

    /**
     * Check output
     *
     * @param $output
     *
     * @return bool
     */
    protected function _outputText(&$output)
    {
        if ($output === null) {
            return false;
        }
        //$output .= str_replace('­','-',$this->reader->value);
        //debug($this->name);

        $value = htmlspecialchars($this->reader->value);
        $this->_handleText($value);

        $this->_output($output, $value);
        return true;
    }

    /**
     * Pop element from stack
     *
     * @param $element
     *
     * @return void
     */
    protected function _popElement($element)
    {
        $this->stack = array_reverse($this->stack);
        foreach ($this->stack as $key => $tag) {
            if (($tag['name'] == $element['name']) && (!isset($tag['wrapperid']) || isset($element['wrapperid']))) {
                unset($this->stack[$key]);
                break;
            }
        }
        $this->stack = array_reverse($this->stack);
    }

    /**
     * Push element into stack
     *
     * Add element to stack for (inner?) wrappers.
     *
     * @param $element
     *
     * @return void
     */
    protected function _pushElement($element)
    {
        if (empty($element['empty'])) {
            $element['attributes']['class'] = (isset($element['attributes']['class']) ? $element['attributes']['class'] : '') . ' reopen';
            array_push($this->stack, $element);
        }
    }

    /**
     * Check class
     *
     * @param $classname
     *
     * @return bool
     */
    protected function _hasClass($classname)
    {
        foreach ($this->stack as $element) {
            if (isset($element['attributes']['class']) && in_array($classname,
                    explode(' ', $element['attributes']['class']))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Close tags
     *
     * @param $output
     * @param $stop
     *
     * @return void
     */
    public function _closeTags(&$output, $stop = null)
    {
        $result = '';
        $this->stack = array_reverse($this->stack, true);
        foreach ($this->stack as $tag) {
            if (($stop !== null) && (is_array($stop)) && isset($stop['id']) && isset($tag['id']) && ($stop['id'] == $tag['id'])) {
                break;
            }
            if (($stop !== null) && (is_array($stop)) && ($stop['name'] == $tag['name']) && (!isset($tag['wrapperid']) || isset($stop['wrapperid']))) {
                break;
            }
            if (isset($tag['newcontext']) && $tag['newcontext']) {
                break;
            }
            //if (($stop !== false) && ($key == $stop) ) break;

            $this->_outputElement($tag, $result, true);
        }
        $this->stack = array_reverse($this->stack);
        $this->_output($output, $result);
    }

    /**
     * Open tags
     *
     * @param $output
     * @param $stop
     *
     * @return void
     */
    public function _openTags(&$output, $stop = null)
    {
        $result = '';
        $this->stack = array_reverse($this->stack, true);

        foreach ($this->stack as $tag) {
            if (($stop !== false) && (is_array($stop)) && isset($stop['id']) && isset($tag['id']) && ($stop['id'] == $tag['id'])) {
                break;
            }
            if (($stop !== false) && (is_array($stop)) && ($stop['name'] == $tag['name']) && (!isset($tag['wrapperid']) || isset($stop['wrapperid']))) {
                break;
            }
            if (isset($tag['newcontext']) && $tag['newcontext']) {
                break;
            }
            //if (($stop !== false) && ($key == $stop) ) break;

            $out = '';
            $this->_outputElement($tag, $out);
            $result = $out . $result;
        }

        $this->stack = array_reverse($this->stack);
        $this->_output($output, $result);
    }
}
