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
     * @return string
     */
    static public function renderAttributes($data, $attributes)
    {
        $out = '';
        if (!empty($attributes)) {
            $attributes_values = array_intersect_key($data, array_flip($attributes));
            $attributes_values = array_merge(array_fill_keys($attributes, null), $attributes_values);

            $out = array_map(function ($key, $value) {
                $value = htmlspecialchars($value ?? '', ENT_XML1 | ENT_COMPAT, 'UTF-8');
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
     * @return string
     */
    public function renderContent($data, $options = [], $level = 0)
    {
        // Prepare
        $data = $this->extractData($data, $options);

        // Prepare array
        $attributes = '';
        $content = '';

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
                $attributes = static::renderAttributes($data, $attributeKeys);
                $data = array_diff_key($data, array_flip($attributeKeys));
            }

            $isSimpleArray = false;
            foreach ($data as $key => $value) {
                if (is_numeric($key) && is_scalar($value)) {
                    $isSimpleArray = true;
                }
                $childtagname = $isSimpleArray ? $tagname : (is_numeric($key) ? null : $key);
                $nextLevel = is_null($childtagname) ? $level : ($level + 1);
                $content .= $this->renderContent($value, ['tagname' => $childtagname] + $options, $nextLevel);
            }

            if ($isSimpleArray) {
                $tagname = null;
            }

        }
        else {
            $content = $data;
            //$content = htmlspecialchars($data ?? '',  ENT_XML1 | ENT_COMPAT, 'UTF-8');
        }

        // Output tag with content and attributes
        $options['array'] = is_array($data);
        $options['level'] = $level;
        return static::renderTag($tagname, $attributes, $content, $options);
    }

}
