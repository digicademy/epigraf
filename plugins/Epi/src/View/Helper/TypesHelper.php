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

use App\Utilities\Converters\Objects;
use Cake\Utility\Text;
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
        $mode = $options['mode'] ?? MODE_PREVIEW;
        $unnest = $options['unnest'] ?? false;

        $fields = $this->getTypes()[$scope][$type]['merged']['fields'] ?? $default;
        $result = [];

        if (($mode === MODE_STAGE) && empty($fields['published'])) {
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

            $fieldConfig = is_string($fieldConfig) ? ['caption' => $fieldConfig] : $fieldConfig;

            if (!empty($fieldConfig['keys']) && $unnest) {
                foreach ($fieldConfig['keys'] as $keyName => $keyConfig) {
                    $keyConfig = is_string($keyConfig) ? ['caption' => $keyConfig] : $keyConfig;
                    $result[$fieldName . '.' . $keyName] = $keyConfig;
                }
            }
            else {
                $result[$fieldName] = $fieldConfig;
            }
        }

        return $result;
    }


    /**
     * Output a css style element containing the styles in the tags' (links and footnotes) config field
     *
     * The css_style field in the config of a type contains an object with the following structure:
     *
     * "css_style": {
     *    "default": ".xml_tag_k { color: #1ccaaa; font-size: 1em; font-variant: small-caps }",
     *    "unadorned": ".xml_group_text_unadorned .xml_tag_k { color: inherit; font-size: inherit; font-variant: inherit; }"
     * }
     *
     * The field may contain a single string instead of an object,
     * in which case it is interpreted as equivalent to the default style.
     *
     * @return string
     */
    public function getTagStyles()
    {
        $types = $this->getTypes();
        $css = [];
        $groups = [];

        foreach (['links', 'footnotes'] as $scope) {
            foreach ($types[$scope] ?? [] as $typeName => $typeEntity) {
                $group = $typeEntity['merged']['group'] ?? null;
                if (!empty($group)) {
                    $groups[$group] = [];
                    $tagType = $typeEntity['merged']['tag_type'] ?? null;
                    $tagType = empty($tagType) ? 'group' : $tagType;

                    if ($tagType === 'group') {
                        $groups[$group]['caption'] = $typeEntity['caption'] ?? $typeName;
                        $groups[$group]['color'] = $typeEntity['merged']['color'] ?? null;
                    };
                }

                $cssStyle = Objects::extract($typeEntity, "merged.css_style");
                if (!empty($cssStyle)) {
                    if (is_array($cssStyle)) {
                        $css = array_merge($css, array_values($cssStyle));
                    }
                    else {
                        $css[] = $cssStyle;
                    }
                }
            }
        }

        // Group colors and display states
        foreach ($groups as $groupName => $groupConfig) {
            $group = Text::slug($groupName);
            $css[] = ".xml_group_{$group}_unadorned .doc-section-link[data-group='{$group}'] {display: none;}";

            $color = $groupConfig['color'] ?? '';
            if (!empty($color)) {
                $css[] = ".doc-section-link[data-group='{$group}'] { background-color: {$color};}";
                $css[] = "span.anno-selector-group-{$group} {background-color: {$color};}";
            }
        }

        $out = '<style data-snippet="article-css">';
        $out .= implode("\n", $css) . "\n";
        $out .= '</style>';
        return $out;
    }

}
