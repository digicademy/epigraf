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

use App\Utilities\Converters\Attributes;
use Cake\Datasource\EntityInterface;

/**
 * A view class that is used for creating XML responses.
 */
class XmlView extends ApiView
{
    /**
     * The name of the layout file to render the view inside of. The name
     * specified is the filename of the layout in /src/Template/Layout without
     * the .php extension. The file is only rendered, if the serialize option is not set.
     *
     * @var string
     */
    public $layout = 'xml';

    static protected $_extension = 'xml';

    public static function contentType(): string
    {
        return 'application/xml';
    }

    /**
     * Render a tag with opening and closing tag, attributes and content
     *
     * ### Options
     * - pretty
     * - level
     * - array
     *
     * @param string $tagname The tag name If empty, only the content will be rendered
     * @param string $attributes The tag attributes
     * @param mixed $content Content of the tag. If an empty string, an empty tag will be rendered.
     *                       If the content is not a string, it will be converted to a string using json_encode.
     * @param array $options
     * @return string
     */
    static public function renderTag($tagname, $attributes, $content, $options)
    {

        $pretty = $options['pretty'] ?? true;
        $level = $options['level'] ?? 0;

        $pretty_break = $pretty ? "\n" : '';
        $pretty_indent = $pretty ? str_repeat(' ', $level * 2) : '';

        if (is_bool($content)) {
            $content = $content ? '1' : '0';
        }
        elseif (!is_string($content)) {
            $content = json_encode($content);
        }

        // No tag
        if ($tagname === null) {
            $xml = $content;
        }

        // Empty tag
        elseif ($content === '') {
            $xml = $pretty_break . $pretty_indent;
            $xml .= "<{$tagname}{$attributes} />";
        }

        // Tag with content
        else {
            // Open tag
            $xml = $pretty_break . $pretty_indent;
            $xml .= "<{$tagname}{$attributes}>";

            // Element content
            $xml .= $content;

            // Close tag
            $xml .= ($options['array'] ?? false) ? ($pretty_break . $pretty_indent) : '';
            $xml .= "</{$tagname}>";
        }

        return $xml;
    }

    /**
     * Render attributes
     *
     * @param array $data Data containing fields to be rendered as attributes
     * @param array $attributes Data fields that should be rendered as attributes
     * @param bool $escape Whether to escape special characters.
     *                     If false, the values are directly passed to the attributes
     *                     and special characters, if necessary, need to be escaped before.
     * @return string
     */
    static public function renderAttributes($data, $attributes, $escape = false)
    {
        $out = '';
        if (!empty($attributes)) {
            $attributes_values = array_intersect_key($data, array_flip($attributes));
            $attributes_values = array_merge(array_fill_keys($attributes, null), $attributes_values);

            $out = array_map(function ($key, $value) use ($escape) {
                if ($escape) {
                    $value = htmlspecialchars($value ?? '', ENT_XML1 | ENT_COMPAT, 'UTF-8');
                }
                return ($key . '="' . $value . '"');
            }, $attributes, $attributes_values);
            $out = empty($out) ? '' : ' ' . implode(' ', $out);
        }

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
        $xmlDeclaration = $options['declaration'] ?? $data['_xml_declaration'] ?? static::$_header['_xml_declaration'] ?? '';
        $xml = empty($xmlDeclaration) ? '' : $xmlDeclaration . "\n";

        $rootTag = $options['rootnode'] ?? $data['_xml_tag'] ?? static::$_header['_xml_tag'] ?? 'response';
        $rootAttributes = static::renderAttributes(static::$_header, static::$_header['_xml_attributes'] ?? []);
        $xml .= "<{$rootTag}{$rootAttributes}>";

        return $xml;
    }

    /**
     * Close the root tag
     *
     * @param array $data
     * @param array $options
     * @return string
     */
    public function renderEpilog($data, $options)
    {
        $rootTag = $options['rootnode'] ?? $data['_xml_tag'] ?? static::$_header['_xml_tag'] ?? 'response';
        return "\n</{$rootTag}>";
    }

    /**
     * Convert array to xml
     *
     * Special keys in $data:
     * - _xml_attributes: A list of property keys that will be rendered as attributes instead of elements.
     * - _xml_tag: Rename the element.
     * - _serialize_fields: Filter elements.
     *
     * @param array|EntityInterface $data
     * @param array $options
     * @param int $level The level of indentation
     * @param bool $pretty Whether to pretty print the XML using indentation
     * @param bool $escape Whether to escape special characters
     *                     If false, the values are directly passed to the attributes.
     *                     and special characters, if necessary, need to be escaped before.
 * @return string
     */
    public function renderContent($data, $options = [], $level = 0, $escape = false)
    {
        // Prepare
        $data = $this->extractData($data, $options);

        // Prepare array
        $content = '';

        $attributes = $options['attributes'] ?? null;
        $tagname = $options['tagname'] ?? null;
        $tagname = $tagname !== null ? Attributes::cleanTag($tagname) : $tagname;

        if (is_array($data)) {
            $attributeKeys = $data['_xml_attributes'] ?? null;

            if (is_null($tagname) && !is_null($data['_xml_tag'] ?? null)) {
                $tagname = $data['_xml_tag'];
                $level += 1;
            }

            unset($data['_xml_attributes']);
            unset($data['_xml_tag']);

            // Remove not allowed fields
            if (isset($data['_serialize_fields'])) {
                $allowed = $data['_serialize_fields'];

                $data = array_intersect_key($data, array_flip($allowed));
                $data = array_merge(array_fill_keys($allowed, null), $data);
            }

            // Transform fields to attributes
            if (!empty($attributeKeys)) {
                $attributes = static::renderAttributes($data, $attributeKeys, $escape);
                $data = array_diff_key($data, array_flip($attributeKeys));
            }

            $isSimpleArray = false;
            foreach ($data as $key => $value) {
                if (is_numeric($key) && is_scalar($value)) {
                    $isSimpleArray = true;
                }
                $childtagname = $isSimpleArray ? $tagname : (is_numeric($key) ? null : $key);
                $nextLevel = is_null($childtagname) ? $level : ($level + 1);
                $contentOptions = ['tagname' => $childtagname];
                if ($isSimpleArray && !is_null($childtagname)) {
                    $contentOptions['attributes'] = $attributes;
                }
                $content .= $this->renderContent($value, $contentOptions + $options, $nextLevel);
            }

            if ($isSimpleArray) {
                $tagname = null;
            }

        }
        else {
            $content = $data;
            if ($escape) {
                $content = htmlspecialchars($content ?? '',  ENT_XML1 | ENT_COMPAT, 'UTF-8');
            }
        }

        // Output tag with content and attributes
        $options['array'] = is_array($data);
        $options['level'] = $level;
        return static::renderTag($tagname, $attributes, $content, $options);
    }

}
