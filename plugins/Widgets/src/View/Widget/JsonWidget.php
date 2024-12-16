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
 * JSON editor, works in combination with the JS class JsonEditor() (editors.js)
  */
class JsonWidget implements WidgetInterface
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

        $data += [
            'name' => '',
            'val'=>null,
            'escape' => true,
            'templateVars' => []
        ];

        //Hidden input with encoded JSON
        $value = !is_null($data['val']) && !is_string($data['val']) ? json_encode($data['val'], JSON_PRETTY_PRINT) : ($data['val'] ?? '');
        $out = $this->_templates->format('input', [
            'type'=>'hidden',
            'name' =>$data['name'],
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes(['value' => $value])
        ]);

        // JSON editor widget container
        $widgetClasses = ['widget-jsoneditor'];
        if (!empty($data['class'])) {
            $widgetClasses[] = $data['class'];
        }
        $out .= '<div class="' . implode(' ', $widgetClasses) . '"></div>';

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
