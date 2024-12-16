<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace Widgets\View\Helper;

use App\Model\Table\PermissionsTable;
use App\Utilities\Converters\Attributes;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use Cake\Routing\Router;

/**
 * Element helper
 *
 * Renders elements in the frontend
 * TODO: merge ElementHelper and Attributes class
 *
 * @property Helper\HtmlHelper $Html
 */
class ElementHelper extends Helper
{

    /**
     * Load helpers
     *
     * @var string[]
     */
    public $helpers = ['Html', 'Url', 'Form', 'Paginator'];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];


    /**
     * Create an HTML element
     *
     * @param string $tagName
     * @param string $content
     * @param array $attributes
     * @return string
     */
    public function outputHtmlElement($tagName, $content, $attributes=[])
    {
        $out = $this->openHtmlElement($tagName, $attributes);

        if (!empty($content)) {
            $out .= $content;
        }

        $out .= $this->closeHtmlElement($tagName);

        return $out;
    }

    /**
     * Open a HTML element
     *
     * @param string $tagName
     * @param array $attributes
     * @return string
     */
    public function openHtmlElement($tagName, $attributes=[])
    {
        $out = '<' . $tagName;
        if (!empty($attributes)) {
            $out .= ' ' . Attributes::toHtml($attributes);
        }
        $out .= '>';
        return $out;
    }

    /**
     * Close a HTML element
     *
     * @param string $tagName
     * @return string
     */
    public function closeHtmlElement($tagName)
    {
        return '</' . $tagName . '>';
    }

    /**
     * Create a wrapper that loads a snippet using ajax
     *
     * @param string $snippet
     * @param string|array $show Elements to show
     * @param array|null $url If empty, the current URL including a snippets parameter is used
     * @return string
     */
    public function ajaxContent($snippet, $show, $url = [])
    {
        if (empty($url)) {
            $url = [];

            // Scope
            $scopeField = $this->_View->getConfig('options')['scopefield'] ?? null;
            $scope = isset($scopeField) ? $this->_View->getConfig('options')['params'][$scopeField] ?? null : null;
            if (isset($scope)) {
                $url[] = $scope;
            }

            // Entity ID
            $entity = $this->_View->get('entity');
            if (!empty($entity)) {
                $url[] = $entity->id;
            }

            // Query parameters
            $queryParams = $this->_View->getConfig('options')['params'] ?? [];
            $queryParams['snippets'] = $snippet;
            $url['?'] = Attributes::paramsToQueryString(
                $queryParams,
                ['collapsed', 'selected', 'template', 'action', $scopeField]
            );
        }

        if (!empty($show)) {
            $show = Attributes::commaListToStringArray($show);
            $url['?']['show'] = implode(',', $show);
        }

        return $this->outputHtmlElement(
            'div',
            '<div class="loader"></div>',
            [
                'class' => 'widget-loadcontent',
                'data-url' => Router::url($url, true),
                'data-snippet' => $snippet
            ]);
    }
}
