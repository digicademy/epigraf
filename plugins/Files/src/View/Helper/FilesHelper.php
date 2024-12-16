<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Files\View\Helper;

use App\Model\Entity\Databank;
use App\Utilities\Converters\Attributes;
use App\Utilities\Files\Files;
use Cake\View\Helper;
use Epi\Model\Entity\BaseEntity;
use Files\Model\Entity\FileRecord;
use Michelf\Markdown;

/**
 * XmlEditor helper
 */
class FilesHelper extends Helper
{
    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Load helpers
     *
     * @var string[]
     */
    public $helpers = ['Form', 'Html', 'Url'];

    /**
     * Initialize dropzone
     *
     * @return string
     */
    public function dropzone()
    {

        // Upload path variables
        $root = $this->getView()->get('root');
        $path = $this->getView()->get('path');
        $database = $this->getView()->get('database');

        // Form
        $url = [
            'controller' => 'Files',
            'action' => 'upload',
            '?' => ['root' => $root, 'path' => $path]

        ];
        if (!empty($database)) {
            $url['database'] = Databank::removePrefix($database['name']);
        }

        $out = $this->Form->create(null, [
                //'id' => 'FilesUploadDropzone',
                'type' => 'file',
                'url' => $url,
                'class' => 'widget-upload',
                'data-redirect' => $this->Url->build()
            ]
        );

        $out .= '<div class="dz-message">You can drop multiple files here or click here to upload.</div>';
        $out .= $this->Form->control('File.path', ['type' => 'hidden', 'value' => $path]);
        $out .= $this->Form->end();

        return $out;
    }

    /**
     * Create an image tag.
     *
     * @param BaseEntity $item Item
     * @param boolean $thumb Whether to show a thumnail (true) or the original image (false)
     * @param boolean|string $link The URL of the image, or true to automatically links the display URL.
     *
     * @return string
     */
    public function outputImage($item, $thumb = true, $link = true)
    {
        $file = $item['file_properties'];
        $exists = $thumb ? ($item['thumb']['exists'] ?? false) : $file['exists'];

        if (!$exists) {
            $element = '<span class="filenotfound">';
            $element .= __('The image {0} is not available on the server.',
                $file['filepath']);
            $element .= '</span>';
        }
        else {
            $url = is_string($link) ? $link : $file['url_display'];

            $attr = [
                'src' => $thumb ? $item['thumb']['thumburl'] : $file['url_display'],
                'alt' => $file['filepath'],
                'data-display' => $file['url_display'],
                'data-manage' => $file['url_view']
            ];

            $element = '<img ' . Attributes::toHtml($attr) . '>';
            if ($link) {
                $element = $this->Html->link(
                    $element,
                    $url,
                    [
                        'escape' => false,
                        'class' => 'link-image'
                    ]);
            }
        }

        return $element;
    }

    /**
     * Output image tag
     *
     * Used for example for brands.
     *
     * @param $filename
     * @param $path
     * @param $database
     * @param $format
     * @return string
     */
    public function outputThumb($filename, $path, $database, $format = 'thumb')
    {
        $path = dirname($filename) !== '.' ? ($path . dirname($filename)) : $path;
        $url = $this->Url->build([
            'controller' => 'Files',
            'action' => 'display',
            'database' => $database,
            '?' => [
                'path' => $path,
                'filename' => basename($filename),
                'format' => $format
            ]
        ]);

        $out = '<img src="' . $url . '" alt="' . $filename . '">';
        return $out;
    }

    /**
     * Preview for Markdown files, text files and images
     *
     * // TODO: paginate content (implement widget-filecontent)
     * //       When scrolling, the widget needs to call the current endpoint and count up the page query parameter,
     * //       starting with 1, until $file->content === false. The content must be extracted from
     * //       the blockquote element of the response and appended to the blockquote element.
     *
     * @param FileRecord $file The file entity
     *
     * @return string
     */
    public function outputPreview($file)
    {
        if (!empty($file->content)) {
            $out = '<div class="preview">';
            $out .= '<blockquote class="preview widget-filecontent" data-snippet="filecontent">';
            if ($file->type == 'md') {
                $out .= Markdown::defaultTransform($file->content);
            }
            else {
                $out .= h($file->content);
            }
            $out .= '</blockquote>';
            $out .= '</div>';
            return $out;
        }

        elseif (in_array(strtolower($file['type']), Files::$thumbtypes)) {
            $url = $this->Url->build([
                'action' => 'display',
                '?' => [
                    'root' => $file['root'],
                    'path' => $file['path'],
                    'filename' => $file['name'],
                    'format' => 'thumb',
                    'size' => '400'
                ]
            ]);

            $out = '<div class="preview">';
            $out .= '<img src="' . $url . '" alt="">';
            $out .= '</div>';
            return $out;
        }
        else {
            return '';
        }
    }

}
