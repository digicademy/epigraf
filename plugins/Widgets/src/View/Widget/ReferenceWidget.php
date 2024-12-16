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

use App\Utilities\Converters\Attributes;
use Cake\View\Form\ContextInterface;
use Cake\View\StringTemplate;
use Cake\View\Widget\WidgetInterface;
use Cake\Routing\Router;

/**
 * Choose widget class for selecting records (e.g. parent_id in trees)
 *
 *  Works in combination with the JS class DropdownSelectorWidget (dropdowns.js).
 * // TODO: add documentation for options in $data, see render()
 */
class ReferenceWidget implements WidgetInterface
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
        // Default values
        $paneId = $data['paneId'] ?? '';
        $paneClasses = ['widget-dropdown-pane', 'widget-scrollbox'];

        if ($data['checkboxlist'] ?? false) {
            $paneClasses[] = 'widget-checkboxlist';
        }

        if ($data['frame'] ?? false) {
            $paneClasses[] = 'widget-dropdown-pane-frame';
        }

        $paneSnippet = $data['paneSnippet'] ?? 'widget-reference';
        $paneAttributes = [
            'id' => $paneId,
            'data-list-value' => $data['listValue'] ?? null,
            'class' => $paneClasses
        ];

        $pane = '<div '. Attributes::toHtml(array_filter($paneAttributes)) . '>'
            . '<ul class="widget-tree" '
            . 'data-snippet="' . $paneSnippet . '"></ul>'
            . '</div>';

        $data += [
            'name' => '',
            'url'=>'',
            'pane' => $pane,
            'paneId' => $paneId,
            'paneAlignTo' => '',
            'textLabel' => __('selected'),
            'search' => true,
            'val'=>null,
            'escape' => true,
            'templateVars' => []
        ];

        // $data['id'] = $data['id'] ?? $data['name'];

        $url = ($data['url'] ?? false) ? Router::url($data['url'],true) : '';

        $widgetClasses = ['widget-dropdown-selector'];
        if ($data['frame'] ?? false) {
            $widgetClasses[] = 'widget-dropdown-selector-frame';
        }

        if ($data['error'] ?? false) {
            $widgetClasses[] = 'field-problem';
        }


        $widgetAttributes = [
            'class' => implode(' ',$widgetClasses),
            'data-url' => $url,
            'data-url-param' => $data['param'] ?? 'term',
            'data-pane-id' => $data['paneId'],
            'data-pane-align-to' => $data['paneAlignTo']
        ];

        $out = '<div ' . Attributes::toHtml(array_filter($widgetAttributes)) . '>';

        //Text input
        if (!isset($data['button'])) {
            $data_text = [
                'value' => $data['text'] ?? '',
                'title' => $data['text'] ?? '',
                'autocomplete' => 'off',
                'class' => 'input-reference-text',
                'readonly' => $data['search'] ? 'false' : 'true',
                'data-label' => $data['textLabel']
                // 'id' => $data['id'] . '_text'
            ];

            $inputTextName = substr($data['name'],-1) === ']' ?
                substr($data['name'], 0, -1) . '-text]' :
                $data['name'] . '-text';

            $data['button'] = $this->_templates->format('input', [
                'type' => 'text',
                'name' => $inputTextName,
                'templateVars' => $data['templateVars'],
                'attrs' => $this->_templates->formatAttributes($data_text)
            ]);
        }

        $out .= $data['button'];

        //Hidden inputs for value and row type
        $data_hidden = [
            'value' => $data['val'],
            'class' => 'input-reference-value'
        ];

        $out .= $this->_templates->format('input', [
            'type'=>'hidden',
            'name' =>$data['name'],
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes($data_hidden)
        ]);

        if (isset($data['rowType'])) {
            $data_type = [
                'value'=> $data['rowType'],
                'class' => 'input-reference-type'
            ];
            $out .= $this->_templates->format('input', [
                'type' => 'hidden',
                'name' => '', // $data['name'] . '-text',
                'templateVars' => $data['templateVars'],
                'attrs' => $this->_templates->formatAttributes($data_type)
            ]);
        }

        // Output pane
        $out .= $data['pane'];

        $out .= '</div>';

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
