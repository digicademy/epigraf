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
 * XML filter class
 */
class XmlFilter
{
    /**
     * Current parser
     *
     * @var null
     */
    public $xmlparser = null;

    /**
     * Current tags
     *
     * @var array
     */
    public $tags = array();

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Remove tags handler
     *
     * @param $element
     * @param $parser
     *
     * @return void
     */
    protected function _handle_removetags(&$element, &$parser)
    {
        if ($element['position'] == 'open') {

            foreach ($this->tags as $tag) {
                $checks = 0;

                //Tagname
                if (!empty($tag['tagname']) && ($tag['tagname'] == $element['name'])) {
                    $checks++;
                }


                //Attributes
                if (!empty($tag['attribute']) && !empty($element['attributes'])) {
                    foreach ($tag['attribute'] as $a_name => $a_value) {
                        if (isset($element['attributes'][$a_name]) && ($element['attributes'][$a_name] == $a_value)) {
                            $checks++;
                            break;
                        }
                    }
                }

                //Class
                if (!empty($tag['class']) && !empty($element['attributes']['class'])) {
                    $classes = explode(' ', $element['attributes']['class']);
                    if (!is_array($tag['class'])) {
                        $tag['class'] = explode(' ', $tag['class']);
                    }
                    if (count(array_intersect($tag['class'], $classes))) {
                        $checks++;
                    }
                }

                //Check
                if (!empty($tag['tagname'])) {
                    $checks--;
                }
                if (!empty($tag['attribute'])) {
                    $checks--;
                }
                if (!empty($tag['class'])) {
                    $checks--;
                }

                if ($checks == 0) {
                    if (!empty($tag['removechildren'])) {
                        $element['customoutput'] = '';
                        $this->xmlparser->parseCurrentElement();
                    }
                    if (isset($tag['replace'])) {
                        $element['customoutput'] = $tag['replace'];
                        $this->xmlparser->parseCurrentElement();
                    }
                    else {
                        $element['customoutput'] = $this->xmlparser->parseCurrentElement();
                    }
                    break;
                }

            }
        }
    }

    /**
     *  Remove tags method from XML-String
     *
     * @param string $input XML-String
     * @param boolean|array $tags Either true to remove all tags or a list of tag conditions to remove.
     *                            Array of array, e.g.
     *                            [
     *                              0=>('tagname'=>'span','attribute'=> array('id'=>'123123')),
     *                              1=>('tagname'=>'span','class'=> 'xxx','removechildren'=>true)
     *                            ]
     *
     * @return string New XML-String
     *
     **/
    public function removeTags($input, $tags = true)
    {
        if ($tags === true) {
            return preg_replace('/<[^>]*>/', '', $input);
        }

        try {
            $this->tags = $tags;
            $this->xmlparser = new XmlImport();
            $this->xmlparser->elementhandler = array(&$this, '_handle_removetags');
            return $this->xmlparser->importXMLString($input);
        } catch (\Cake\Core\Exception\CakeException $e) {
            return false;
        }
    }

}

