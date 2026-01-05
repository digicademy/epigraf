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
use Cake\Routing\Router;

/**
 * Choose widget class for generating a text field and a button to choose from suggestions
 *
 * This class is intended as an internal implementation detail
 * of Cake\View\Helper\FormHelper and is not intended for direct use.
 */
class ChooseWidget implements WidgetInterface
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
     * @param StringTemplate $templates Templates list
     */
    public function __construct($templates)
    {
        $templates->add([
            'datalist' => '<datalist id="{{id}}" {{attrs}}>{{content}}</datalist>'
        ]);
        $this->_templates = $templates;
    }

    /**
     * Render output
     *
     * @param array $data
     * @param ContextInterface $context
     *
     * @return string
     */
    public function render(array $data, ContextInterface $context): string
    {
        $data += [
            'name' => '',
            'val' => null,
            'type' => 'text',
            'escape' => true,
            'templateVars' => []
        ];

        $data['value'] = $data['val'];
        unset($data['val']);

        // Make more general? Choose is used for choosing databases and files, improve naming scheme
        $data['data-path'] = $data['path'] ?? '';
        unset($data['path']);

        $data['data-itemtype'] = $data['itemtype'] ?? '';
        unset($data['itemtype']);

        if (!empty($data['external'])) {
            $data['data-external'] = $data['external'] ?? '';
        }
        unset($data['external']);

        $out = '<div class="widget-choose">';

        // Hidden empty file path. Needed to replace the file_path field, see ItemTable::beforeSave().
        if (isset($data['pathField'])) {
            $out .= $this->_templates->format('input', [
                'type' => 'hidden',
                'name' => $data['pathField'],
                'value' => '',
                'templateVars' => $data['templateVars'],
                'attrs' => $this->_templates->formatAttributes([])
            ]);
        }

        //Text input
        $out .= $this->_templates->format('input', [
            'type' => 'text',
            'name' => $data['name'],
            'templateVars' => $data['templateVars'],
            'attrs' => $this->_templates->formatAttributes($data, ['type', 'name', 'options'])
        ]);

        //Button
        $button_attrs = [
            'id' => $data['id'] . '_button',
            'type' => 'button',
            'class' => '',
            'data-input' => $data['id'],
            'data-itemtype' => $data['data-itemtype'],
            'data-external' => $data['data-external'] ?? '',
            'data-url' => Router::url($data['options']),
        ];
        $button_attrs = array_filter($button_attrs);

        $out .= $this->_templates->format('button', [
            'text' => '...',
            'attrs' => $this->_templates->formatAttributes($button_attrs)
        ]);

        $out .= '</div>';

        return $out;
    }

    /**
     * Render the contents of the datalist element
     *
     * @param array $data The context for rendering a datalist.
     *
     * @return array
     */
    protected function _renderOptions($data)
    {
        $options = $data['options'];
        $out = [];
        foreach ($options as $val) {
            $out[] = $this->_templates->format('option', [
                'value' => $val
            ]);
        }

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
        return [$data['name']];
    }

}
