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

use \DOMDocument;
use \DomXPath;

/*
 * XML Munge class
 *
 * Static functions for munging XML data (bubble, split, merge tags).
 */

class XmlMunge
{
    /*
     * Parse a XML string and call a handler on each element
     *
     * Die Werte können im Callback verändert werden und es können außerdem gesetzt werden:
     *   -disabled => true|false Element ausfiltern
     *   -customcontent => Inhalt des Elements überschreiben
     *   -customoutput => komplettes Element überschreiben
     *
     * @param string $input
     * @param callable $elementhandler
     *                  function (&$element,&$parser)
     *                  @element: array mit den keys:
     *                          - name => name
     *                          - empty  => isEmptyElement
     *                          - position =>'open' or 'close'
     *                          - attributes =>array()
     */
    static function parseXmlString($input, callable $elementhandler)
    {
        $parser = new XmlImport;
        $parser->elementhandler = $elementhandler;

        libxml_clear_errors();
        libxml_use_internal_errors(true);
        $output = $parser->importXMLString($input);
        $error = libxml_get_last_error();
        if (!empty($error)) {
            throw new \Exception($error->message);
        }
        libxml_use_internal_errors(false);

        return $output;
    }

    /**
     * Parse an XML file and call a handler on each element
     *
     * @param $fileName
     * @param callable $elementhandler
     * @return false|mixed
     */
    static function parseXmlFile($fileName, callable $elementhandler)
    {
        $parser = new XmlImport;
        $parser->elementhandler = $elementhandler;
        return $parser->importXMLFile($fileName);
    }

    /**
     * Extract all xml elements from a string and merge them by their id attribute
     *
     * Content of elements with the same id will be merged together.
     * Elements without an id attribute will be returned under a generated id "no-x" where x is a running number.
     *
     * ### Options
     * - content: Whether to extract the tag content.
     * - parents: Whether to add tag parent ids.
     *
     * @param string $value The XML string to extract elements from.
     * @param array $options Options for the extraction, e.g. ['content' => true, 'parents' => true].
     * @param callable|null $spawnCallback Optional callback function to be called for each tag, with signature function($tagId, $tagData).
     * @return array
     * @throws \Exception
     */
    static function getXmlElements($value, $options = ['content' => false, 'parents' => false], $spawnCallback = null)
    {
        $tags = [];
        if (empty($value)) {
            return $tags;
        }

        /**
         * @param array $element
         * @param XmlImport $parser
         * @return void
         */
        $callback_ids = static function (&$element, &$parser) use (&$tags, $options, $spawnCallback) {
            if ($element['position'] === 'open') {

                if (isset($element['attributes']['id'])) {
                    $tagId = $element['attributes']['id'];
                } else {
                    $tagId = 'no-' . (count($tags) + 1);
                    $element['attributes']['id'] = $tagId;
                }

                // TODO: Throw an error if the same id occurs more than once under different element names
                if (empty($tags[$tagId])) {
                    $tags[$tagId] = ['name' => $element['name']];
                }

                if (!empty($options['content']) && empty($element['empty'])) {
                    // TODO: Optionally, render styles
                    $element['customoutput'] = $parser->parseCurrentElement();
                    $tags[$tagId]['content'] = ($tags[$tagId]['content'] ?? '') . ' ' . trim($element['customoutput']);
                    $tags[$tagId]['content'] = trim($tags[$tagId]['content']);
                }

                // TODO: Handle the case where a tag with the same ID occurs under different parents
                if (!empty($options['parents']) && count($parser->stack) > 1) {
                    $parentId = end($parser->stack)['attributes']['id'] ?? null;
                    $tags[$tagId]['parent_id'] = $parentId;
                }

                if (!empty($spawnCallback)) {
                    $spawnCallback($tagId, $tags[$tagId]);
                }
            }
        };

        XmlMunge::parseXmlString($value, $callback_ids);
        return $tags;
    }


    /**
     * Build an XPath query for a given tag condition array
     *
     * The supported conditions are:
     * - name: The tag name (required)
     * - classes: An array of classes that the tag must have (optional)
     * - empty: A boolean indicating whether the tag must be empty (optional)
     *
     * @param array $tag
     * @return string
     */
    static protected function buildXpathQuery($tag)
    {
        $query = '//' . $tag['name'];
        $contains = array();
        if (!empty ($tag['classes'])) {
            foreach ($tag['classes'] as $class) {
                $contains[] = 'contains(concat(" ",@class," ")," ' . $class . ' ")';
            }
        }
        if (!empty($tag['empty'])) {
            $contains[] = 'not(node())';
        }
        if (!empty($contains)) {
            $query .= '[' . implode(' and ', $contains) . ']';
        }
        return $query;
    }

    /**
     * Check if an element is a stop element for bubbling
     *
     * @param $element
     * @param array $tags Array of tag conditions, e.g. [ ['name'=>'span','classes'=> ['line']] ]
     * @return bool
     */
    static protected function isStop($element, $tags = [])
    {
        if (empty($tags)) {
            return false;
        }

        $parentClasses = '';
        if ($element->parentNode->attributes->getNamedItem('class') !== null) {
            $parentClasses = $element->parentNode->attributes->getNamedItem('class')->value;
        };
        $parentClasses = explode(' ', $parentClasses);

        foreach ($tags as $tag) {
            if (($tag['name'] == $element->parentNode->nodeName) &&
                (count(array_intersect($tag['classes'], $parentClasses)) == count($tag['classes']))) {
                return true;
            }
        }
        return false;
    }

    static protected function hasValidParent($element) : bool
    {
        return ($element->parentNode != null) &&
               ($element->parentNode->parentNode != null) &&
               ($element->parentNode->parentNode->nodeName != '#document');
    }

    /**
     * Unnest tags
     *
     *
     * Move tags up if they are the last child of their parent
     * and the parent has the same tag name.
     *
     * Remove all empty tags.
     *
     * @param string $content xml string
     * @param array $tags An array of tag conditions, e.g.
     *                     [
     *                       ['name'=>'span', 'classes'=> ['wrapper']]
     *                     ]
     * @return string Cleaned xml string
     */
    static function unnestTags($content, $tags)
    {
        if ($content == '') {
            return $content;
        }

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;

        if ($doc->loadXML('<body>' . $content . '</body>')) {
            $xp = new DomXPath($doc);
            foreach ($tags as $tag) {

                $query = self::buildXpathQuery($tag);
                $elements = $xp->query($query);

                /** @var \DOMNode $element */
                foreach ($elements as $element) {
                    if (self::hasValidParent($element)
                    ) {
                        // Remove empty elements
                        if (!$element->hasChildNodes()) {
                            $element->parentNode->removeChild($element);
                        }

                        // Move nested elements up
                        elseif (($element->nextSibling == null) && ($element->parentNode->nodeName === $element->nodeName)) {
                            $element->parentNode->parentNode->insertBefore($element, $element->parentNode->nextSibling);
                            if (!$element->parentNode->hasChildNodes()) {
                                $element->parentNode->parentNode->removeChild($element->parentNode);
                            }
                        }
                    }
                }
            }
            $content = substr($doc->saveXML($doc->documentElement), 6, -7);
            // , LIBXML_NOEMPTYTAG
            //$content = substr($doc->saveXML($doc->documentElement),6,-7);
        }
        return $content;
    }

    /*
     *  Bubble tags up
     *
     *  @param  string  $content  XML-String
     *  @param  array $tags tags to bubble up and tags to stop bubbling at, array of array, e.g.
     *                     array(
     *                        'bubble'=>array(array('name'=>'span','classes'=> array('wrapper')),... ),
     *                        'stops'=>array(array('name'=>'span','classes'=> array('line')),... ),
     *                      )
     *
     *  @return string New XML-String
     */
    static function bubbleTags($content, $tags)
    {
        if ($content == '') {
            return $content;
        }

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;

        if ($doc->loadXML('<body>' . $content . '</body>')) {
            $xp = new DomXPath($doc);
            foreach ($tags['bubble'] as $tag) {

                $query = self::buildXpathQuery($tag);
                $elements = $xp->query($query);

                foreach ($elements as $element) {
                    if (
                        self::hasValidParent($element) &&
                        ($element->nextSibling == null)
                    ) {

                        if (self::isStop($element, $tags['stops'] ?? [])) {
                            continue;
                        }

                        $element->parentNode->parentNode->insertBefore($element, $element->parentNode->nextSibling);
                    }
                }
            }
            $content = substr($doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG), 6, -7);
            //$content = substr($doc->saveXML($doc->documentElement),6,-7);
        }
        return $content;
    }

    /*
     *  Bubble tags up
     *
     *  @param  string  $content  XML-String
     *  @param  array $tags tags to bubble up and tags to stop bubbling at, array of array, e.g.
     *                     array(
     *                        'bubble'=>array(array('name'=>'span','classes'=> array('wrapper')),... ),
     *                        'stops'=>array(array('name'=>'span','classes'=> array('line')),... ),
     *                      )
     *
     *  @return string New XML-String
     */
    static function bubbleWrapperTag($content, $tags)
    {
        if ($content == '') {
            return $content;
        }

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;

        if ($doc->loadXML('<body>' . $content . '</body>')) {
            $xp = new DomXPath($doc);

            foreach ($tags['bubble'] as $tag) {

                $query = self::buildXpathQuery($tag);
                $elements = $xp->query($query);

                foreach ($elements as $element) {
                    if (
                        self::hasValidParent($element) &&
                        ($element->nextSibling === null) && ($element->previousSibling === null)
                    ) {

                        if (self::isStop($element, $tags['stops'] ?? [])) {
                            continue;
                        }

                        $childnodes = $element->childNodes;
                        $outerwrap = $element->parentNode;

                        while ($childnodes->length > 0) {
                            $child = $childnodes->item(0);
                            $outerwrap->appendChild($child);
                        }

                        $element = $outerwrap->parentNode->insertBefore($element, $outerwrap);
                        $element->appendChild($outerwrap);

                        //Merge

                    }
                }
            }
            $content = substr($doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG), 6, -7);
            //$content = substr($doc->saveXML($doc->documentElement),6,-7);
        }
        return $content;
    }

    /**
     * Split tag
     *
     * @param $content
     * @param $tag
     *
     * @return mixed|string
     * @throws \DOMException
     */
    static function splitTag($content, $tag)
    {
        if ($content == '') {
            return $content;
        }

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;

        if ($doc->loadXML('<body>' . $content . '</body>')) {
            $xp = new DomXPath($doc);

            //Select
            $query = '//' . $tag['name'];
            $contains = array();
            foreach ($tag['classes']['select'] as $class) {
                $contains[] = 'contains(concat(" ",@class," ")," ' . $class . ' ")';
            }
            if (!empty($contains)) {
                $query .= '[' . implode(' and ', $contains) . ']';
            }

            $elements = $xp->query($query);
            foreach ($elements as $element) {
                $allowed = array_merge($tag['classes']['select'], $tag['classes']['move'], $tag['classes']['copy']);
                $current = ($element->attributes->getNamedItem('class') != null) ? $element->attributes->getNamedItem('class')->value : '';
                $current = explode(' ', $current);

                if (count(array_diff($current, $allowed)) > 0) {
                    $wrapclass = implode(' ', array_intersect($current, $allowed));
                    $remainclass = implode(' ',
                        array_diff($current, array_merge($tag['classes']['select'], $tag['classes']['move'])));

                    $element->setAttribute('class', $remainclass);
                    $wrap = $doc->createElement($tag['name']);
                    $wrap = $element->parentNode->insertBefore($wrap, $element);
                    $wrap->setAttribute('class', $wrapclass);
                    $wrap->appendChild($element);
                }
            }

            //$content = substr($doc->saveXML($doc->documentElement),6,-7);
            $content = substr($doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG), 6, -7);
        }
        else {
            debug($content);
        }

        return $content;
    }

    /**
     * Merge tags
     *
     * @param $content
     * @param $tag
     *
     * @return mixed|string
     */
    static function mergeTags($content, $tag)
    {
        if ($content == '') {
            return $content;
        }

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;

        if ($doc->loadXML('<body>' . $content . '</body>')) {
            $xp = new DomXPath($doc);

            //Select
            $query = '//' . $tag['name'];
            $contains = array();
            foreach ($tag['classes']['select'] as $class) {
                $contains[] = 'contains(concat(" ",@class," ")," ' . $class . ' ")';
            }
            if (!empty($contains)) {
                $query .= '[' . implode(' and ', $contains) . ']';
            }

            $elements = $xp->query($query);
            for ($i = $elements->length - 1; $i >= 0; $i--) {
                $element = $elements->item($i);

                //Check name
                if ((!$element->previousSibling) ||
                    ($element->previousSibling->nodeType != XML_ELEMENT_NODE) ||
                    ($element->previousSibling->nodeName != $element->nodeName) ||
                    ($element->previousSibling->attributes->getNamedItem('class') == null)) {
                    continue;
                }

                //Check class
                $current = ($element->attributes->getNamedItem('class') != null) ? $element->attributes->getNamedItem('class')->value : '';
                $current = explode(' ', $current);

                $previous = ($element->previousSibling->attributes->getNamedItem('class') != null) ? $element->previousSibling->attributes->getNamedItem('class')->value : '';
                $previous = explode(' ', $previous);

                if ((count(array_diff($previous, $current,
                            $tag['classes']['ignore'])) == 0) && (count(array_diff($current, $previous,
                            $tag['classes']['ignore'])) == 0)) {
                    $prev = $element->previousSibling;
                    $childnodes = $element->childNodes;
                    while ($childnodes->length > 0) {
                        $child = $childnodes->item(0);
                        $prev->appendChild($child);
                    }
                    $element->parentNode->removeChild($element);
                }
            }

            //$content = substr($doc->saveXML($doc->documentElement),6,-7);
            $content = substr($doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG), 6, -7);
        }
        return $content;
    }

}
