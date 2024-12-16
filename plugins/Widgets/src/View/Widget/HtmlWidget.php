<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Widgets\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\StringTemplate;
use Cake\View\Widget\WidgetInterface;

/**
 * HTML editor, works in combination with the JS class HtmlEditor() (editors.js)
  */
class HtmlWidget implements WidgetInterface
{

    /**
     * String template instance
     *
     * @var \Cake\View\StringTemplate
     */
    protected $_templates;

    /**
     * Constructor
     *
     * @param \Cake\View\StringTemplate $templates Templates list
     */
    public function __construct(StringTemplate $templates)
    {
        $this->_templates = $templates;
    }

    /**
     * Output a rendered widget
     *
     * @param array $data The data to build an input with.
     * @param \Cake\View\Form\ContextInterface $context The current form context.
     *
     * @return string
     */
    public function render(array $data, ContextInterface $context): string
    {


        // Encode for ckeditor
        $value = str_replace('&', '&amp;', $data['val'] ?? '');

        $data += [
            'name' => '',
            'val'=>null,
            'escape' => true,
            'templateVars' => []
        ];

        $out = $this->_templates->format('textarea', [
            'name' =>$data['name'],
            'value' => $value,
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes(['class' => 'widget-htmleditor'])
        ]);

        return $out;
    }

    /**
     * Get secure fields
     *
     * @param array $data
     *
     * @return array
     */
    public function secureFields(array $data): array
    {
        return [];
    }
}
