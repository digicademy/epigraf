<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Utilities\XmlParser;

use Masterminds\HTML5\Parser\DOMTreeBuilder;

class HtmlHeader extends DOMTreeBuilder
{
    /**
     * Table of contents
     *
     * @var array
     */
    public $toc = [];

    /**
     * Capture
     *
     * @var bool
     */
    protected $capture = false;

    /**
     * Current ID
     *
     * @var null
     */
    protected $currentId = null;

    /**
     * Current text
     *
     * @var string
     */
    protected $currenttext = '';

    /**
     * Get start tag
     *
     * @param $name
     * @param $attributes
     * @param $selfClosing
     * @return int|void
     */
    public function startTag($name, $attributes = array(), $selfClosing = false)
    {
        if (in_array($name, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
            $this->capture = true;

            // Create ID
            $this->currentId = $attributes['id'] ?? 'toc_' . (count($this->toc) + 1);
            $attributes['id'] = $this->currentId;

            $classes = explode(' ', $attributes['class'] ?? '');
            if (!in_array('widget-scrollsync-section', $classes)) {
                $classes[] = 'widget-scrollsync-section';
            }
            $attributes['class'] = implode(' ', $classes);
        }

        parent::startTag($name, $attributes, $selfClosing);
    }

    /**
     * Get end tag
     *
     * @param $name
     * @return void
     */
    public function endTag($name)
    {
        parent::endTag($name);

        if (in_array($name, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])) {
            $level = (int)substr($name, 1, 1);
            $this->toc[] = [
                'name' => $name,
                'label' => $this->currenttext,
                'level' => $level,
                'url' => '#' . $this->currentId,
                'data' => [
                    'data-level' => $level,
                    'data-section-id' => $this->currentId
                ]
            ];

            $this->capture = false;
            $this->currentId = null;
            $this->currenttext = '';
        }
    }

    /**
     * Add data to current text
     *
     * @param $data
     * @return void
     */
    public function text($data)
    {
        parent::text($data);

        if ($this->capture) {
            $this->currenttext .= $data;
        }
    }
}
