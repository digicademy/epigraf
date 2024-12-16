<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Traits;

/**
 * Adds the to magic fields path and parent_path to tree entitites.
 * You need to declare the properties $_path_field (e.g. lemma) and
 * $_path_separator (e.g. '.') in the entity class.
 */
trait TreeTrait
{
    /**
     * Get the path of the current nodes parent
     *
     * @return string
     */
    protected function _getParentPath()
    {
        $path = array_reverse($this->getValueNested('ancestors.{*}.' . $this->_path_field, ['aggregate' => false]));
        return implode($this->_path_separator, $path);
    }

    /**
     * Get the lemma path of the current property
     *
     * @return string
     */
    protected function _getPath()
    {
        $path = $this->getValueNested('ancestors.{*}.' . $this->_path_field, ['aggregate' => false]);
        $path = empty($path) ? [] : $path;
        $path = array_reverse($path);
        $path[] = $this->{$this->_path_field};
//        $level = $this->type['merged']['level'] ?? 0;
//        if (!empty($path) && $level > 0) {
//            $path = array_slice($path, $level);
//        }
//
//        $field = $this->type['merged']['displayfield'] ?? 'lemma';
//        $field = $field === 'path' ? 'lemma' : $field;
//
//        $path[] = $this[$field] ?? $this->lemma;
        return implode($this->_path_separator, $path);
    }

    protected function _getParentNode()
    {
        if (empty($this->_fields['parent_id'])) {
            return null;
        }
        if (!isset($this->_fields['parent'])) {
            $this->_fields['parent'] = $this->table->get($this->_fields['parent_id']);

        }
        return $this->_fields['parent'];
    }


}
