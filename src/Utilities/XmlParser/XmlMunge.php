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

    static function getXmlElements($value, $content = false)
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
        $callback_ids = static function (&$element, &$parser) use (&$tags, $content) {
            if (($element['position'] === 'open') && (!empty($element['attributes']['id']))) {

                if ($content) {
                    if (empty($tags[$element['attributes']['id']])) {
                        $tags[$element['attributes']['id']] = ['name' => $element['name'], 'content' => ''];
                    }

                    if (empty($element['empty'])) {
//                        $parser->beginTrack($element['attributes']['id']);
                        // TODO: Optionally, render styles
                        $element['customoutput'] = $parser->parseCurrentElement();
                        $tags[$element['attributes']['id']]['content'] .= ' ' . trim($element['customoutput']);
                        $tags[$element['attributes']['id']]['content'] = trim($tags[$element['attributes']['id']]['content']);
//                        $parser->endTrack($element['attributes']['id']);
                    }
                }
                else {
                    $tags[$element['attributes']['id']] = $element['name'];
                }

            }
            elseif (($element['position'] === 'open')) {
                $tags['no-' . (count($tags) + 1)] = $element['name'];
            }
        };
        XmlMunge::parseXmlString($value, $callback_ids);

        return $tags;
    }

    /*
     *  bubbleTags
     *
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
                $elements = $xp->query($query);
                foreach ($elements as $element) {
                    if (($element->parentNode != null) && ($element->parentNode->parentNode != null) && ($element->parentNode->parentNode->nodeName != '#document') && ($element->nextSibling == null)) {
                        $parentclasses = ($element->parentNode->attributes->getNamedItem('class') != null) ? $element->parentNode->attributes->getNamedItem('class')->value : '';
                        $parentclasses = explode(' ', $parentclasses);
                        foreach ($tags['stops'] as $stop) {
                            if (($stop['name'] == $element->parentNode->nodeName) &&
                                (count(array_intersect($stop['classes'], $parentclasses)) == count($stop['classes']))) {
                                continue 2;
                            }
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
     *  bubbleWrapperTag
     *
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
                $elements = $xp->query($query);
                foreach ($elements as $element) {
                    if (($element->parentNode != null) && ($element->parentNode->parentNode != null) && ($element->parentNode->parentNode->nodeName != '#document') && ($element->nextSibling == null) && ($element->previousSibling == null)) {

                        //Move
                        $parentclasses = ($element->parentNode->attributes->getNamedItem('class') != null) ? $element->parentNode->attributes->getNamedItem('class')->value : '';
                        $parentclasses = explode(' ', $parentclasses);
                        foreach ($tags['stops'] as $stop) {
                            if (($stop['name'] == $element->parentNode->nodeName) &&
                                (count(array_intersect($stop['classes'], $parentclasses)) == count($stop['classes']))) {
                                continue 2;
                            }
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
