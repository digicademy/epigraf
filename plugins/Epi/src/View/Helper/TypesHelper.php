<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\View\Helper;

use App\Model\Entity\Databank;
use App\Utilities\Converters\Objects;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Sections helper
 */
class TypesHelper extends Helper
{

     /**
     * Get types in current database
     *
     * @return mixed
     */
    public function getTypes()
    {
        return $this->getView()->get('database')->types;
    }

    /**
     * Get the caption of a single type
     *
     * @param string $scope
     * @param $type
     * @param $default
     * @return mixed
     */
    public function getCaption($scope, $type, $default)
    {
        return $this->getTypes()[$scope][$type]['caption'] ?? $default;
    }


    /**
     * Display config (false, highlight, addendum)
     *
     * @param $scope
     * @param $type
     * @param string $default
     * @return mixed|string
     */
    public function getDisplay($scope, $type, $default = false)
    {
        return $this->getTypes()[$scope][$type]['merged']['display'] ?? $default;
    }

    /**
     * Get the fields of a type
     *
     * //TODO: move to model, implement _getHtmlFields in the Item entity class
     *
     * ### Options
     * - defaultFields Default field configuration (default: [])
     * - unnest Whether to unnest JSON fields from the keys key (default: false)
     * - edit = false
     * - mode = 'view'
     *
     * @param string $scope
     * @param string $type
     * @param array $options
     * @return mixed
     */
    public function getFields($scope, $type, $options = [])
    {
        $default = $options['defaultFields'] ?? [];
        $edit = $options['edit'] ?? false;
        $mode = $options['mode'] ?? 'view';
        $unnest = $options['unnest'] ?? false;

        $fields = $this->getTypes()[$scope][$type]['merged']['fields'] ?? $default;
        $result = [];

        if (($mode === 'stage') && empty($fields['published'])) {
            $fields['published'] = [
                'caption' => __('Progress'),
                'empty' => true,
                'format' => 'published'
            ];
        }

        foreach ($fields as $fieldName => $fieldConfig) {
            // TODO: find better solution, not specific to one field format
            if ($edit && (($fieldConfig['format'] ?? '') === 'unit')) {
                continue;
            }

            if (is_string($fieldConfig)) {
                $result[$fieldName] = ['caption' => $fieldConfig];
            }
            elseif (!empty($fieldConfig['keys']) && $unnest) {
                foreach ($fieldConfig['keys'] as $keyName => $keyConfig) {
                    if (is_string($keyConfig)) {
                        $result[$fieldName . '.' . $keyName] = ['caption' => $keyConfig];
                    }
                    else {
                        $result[$fieldName . '.' . $keyName] = $keyConfig;
                    }
                }
            }
            else {
                $result[$fieldName] = $fieldConfig;
            }
        }

        return $result;
    }

    /**
     * Output a css style element containing the styles in the links' config field
     *
     * @return string
     */
    public function getTagStyles()
    {
        $out = '<style data-snippet="article-css">';

        $types = $this->getTypes();
        $css = Objects::extract($types, "links.*.config.css_style");
        $out .= implode("\n", $css) . "\n";

        $css = Objects::extract($types, "footnotes.*.config.css_style");
        $out .= implode("\n", $css);

        $out .= '</style>';
        return $out;

    }

}
