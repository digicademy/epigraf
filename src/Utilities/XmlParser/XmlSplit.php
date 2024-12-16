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

/**
 * XML splitter class
 */
class XmlSplit
{

    /**
     * Current parser
     *
     * @var null
     */
    public $xmlparser = null;

    /**
     * Output
     *
     * @var array
     */
    public $output = array();

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Element handler
     *
     * @param $element
     * @param $parser
     *
     * @return void
     */
    protected function _handleElements(&$element, &$parser)
    {

//     if (($element['name'] == 'body' ) && ($element['position'] == 'open' ) ) {
//
//          $context = array('element'=> $element);
//          array_unshift($this->xmlimport->contexts,$context);
//          $this->_newPage();
//          $this->_newLine();
//
//          $this->xmlimport->parseCurrentElement($this->currentline['inhalt']);
//
//          //Letzte Seite speichern
//          $this->_saveCurrentPage();
//
//          array_shift($this->xmlimport->contexts);
//          return true;
//     }


        if ($element['name'] == 'cutpoint') {

            $parser->_closeTags($parser->contexts[0]['output']);

            $this->output[] = $parser->contexts[0]['output'];


            $this->xmlparser->parseCurrentElement();
            $element['customoutput'] = '';

            $parser->contexts[0]['output'] = '';
            $parser->_openTags($parser->contexts[0]['output']);
        }
    }

    /**
     * Split position method
     *
     * @param $input
     * @param $pos
     *
     * @return array
     */
    public function splitPos($input, $pos)
    {
        $this->xmlparser = new XmlImport();
        $this->xmlparser->elementhandler = array(&$this, '_handleElements');
        $this->output = array();

        $part1 = substr($input, 0, $pos);
        $part2 = substr($input, $pos);

        $this->xmlparser->importXMLString($part1 . '<cutpoint/>' . $part2);
        $this->output[] = $this->xmlparser->contexts[0]['output'];

        return array_pad($this->output, 2, '');
    }

}
