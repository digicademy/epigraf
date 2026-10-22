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

use Cake\Datasource\EntityInterface;

/**
 * Adds the magic fields path, parentPath, and indentation to tree entities.
 *
 */
trait TreeTrait
{


    /**
     * Default field for path elements
     *
     * @var string
     */
    protected $_path_field = 'lemma';

    /**
     * Default path separator
     *
     * @var string
     */
    protected $_path_separator = ' › ';

    protected function _getHasChildren()
    {
        return ($this->rght - $this->lft) > 1;
    }

    /**
     * Get the field to construct the display path
     *
     * By default, the lemma field is used. You can change the field
     * in the displayfield property of the types configuration.
     * To prevent recursion, the 'path' value is changed to 'lemma'.
     *
     * @return string
     */
    protected function _getPathField () {
        $value = $this->type['merged']['displayfield'] ?? $this->_path_field;
        if ($value === 'path') {
            $value = $this->_path_field;
        }
        return $value;
    }

    /**
     * Get the path of the current nodes parent
     *
     * @return string
     */
    protected function _getParentPath()
    {
        $path = $this->getValueNested('ancestors.{*}.' . $this->pathField, ['aggregate' => false]) ?? [];
        $path = array_reverse($path);
        return implode($this->_path_separator, $path);
    }

    /**
     * Get the lemma path of the current property
     *
     * @return string
     */
    protected function _getPath()
    {
        $path = $this->getValueNested('ancestors.{*}.' . $this->pathField, ['aggregate' => false]);
        $path = empty($path) ? [] : $path;
        $path = array_reverse($path);
        $path[] = $this->{$this->pathField};
        return implode($this->_path_separator, $path);
    }

    /**
     * Get indentation
     *
     * @return string
     */
    protected function _getIndentation()
    {
        return str_repeat($this->_path_separator, $this->level);
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

    /**
     * Get the reference ID
     *
     * @return null|int
     */
    protected function _getReferenceId()
    {
        if (!isset($this->_fields['reference_id'])) {
            $preceding = $this->preceding;
            if (empty($preceding)) {
                $this['reference_id'] = $this->parent_id;
                $this['reference_pos'] = 'parent';
            }
            else {
                $this['reference_id'] = $preceding->id;
                $this['reference_pos'] = 'preceding';
            }
        }
        return $this->_fields['reference_id'] ?? null;
    }

    /**
     * Get the reference position
     *
     * @return null|string 'parent' or 'preceding'
     */
    protected function _getReferencePos()
    {
        if (!isset($this->_fields['reference_pos'])) {
            $preceding = $this->preceding;
            if (empty($preceding)) {
                $this['reference_id'] = $this->parent_id;
                $this['reference_pos'] = 'parent';
            }
            else {
                $this['reference_id'] = $preceding->id;
                $this['reference_pos'] = 'preceding';
            }
        }
        return $this->_fields['reference_pos'] ?? null;
    }

    /**
     * Get the reference entity
     *
     * @return null|EntityInterface
     */
    protected function _getReference()
    {
        $referenceId = $this->referenceId;
        if (!empty($referenceId) && (($this->_fields['reference']['id'] ?? null) !== intval($referenceId))) {
            $referenceNode = $this->table
                ->find('containAncestors')
                ->find('all')
                ->where(['id' => intval($referenceId)])
                ->first();

            $this->_fields['reference'] = $referenceNode;

            //$property->ancestors = $referenceNode->ancestors;
        }

        return $this->_fields['reference'] ?? null;
    }

}
