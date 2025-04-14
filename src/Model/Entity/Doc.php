<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity;

use App\Model\Table\DocsTable;
use App\Utilities\Files\Files;
use App\Utilities\XmlParser\HtmlHeader;
use Epi\Model\Behavior\PositionBehavior;
use Exception;
use Masterminds\HTML5;
use Masterminds\HTML5\Parser\Scanner;
use Masterminds\HTML5\Parser\Tokenizer;
use Rest\Entity\LockInterface;
use Rest\Entity\LockTrait;

/**
 * Doc Entity
 *
 * Handles the help and the pages segment of the docs table.
 *
 * # Database fields (without inherited fields)
 * @property string $menu
 * @property string $segment
 * @property string $sortkey
 * @property string $name
 * @property string $category
 * @property string $content
 * @property string $format
 *
 *
 * # Virtual fields (without inherited fields)
 * @property null|string $scope
 * @property array $toc
 * @property string $html
 * @property string $fileDefaultpath
 *
 * # Relations
 * @property DocsTable $table
 */
class Doc extends BaseEntity implements LockInterface
{

    use LockTrait;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'deleted' => false,
        'version_id' => false,
        'created' => false,
        'modified' => false,
    ];

    /**
     * Table of contents
     *
     * Holds the toc after parsing with _getToc().
     *
     * @var null
     */
    protected $_toc = null;

    /**
     * Get the scope (page, wiki, help)
     *
     * @return string|null
     */
    protected function _getScope()
    {
        return $this->table->scopeValue ?? null;
    }


    /**
     * Get the base folder for file uploads
     *
     * All doc related files are located in subfolders named after the scope: wiki, pages, help
     *
     * @return string
     */
    protected function _getFileBasepath()
    {
        return $this->scope . DS;
    }

    /**
     * Get the default file path, without the basepath
     *
     * All doc related files (i.e. images used in the text)
     * are located with in a base path named after the scope: wiki, help, pages.
     *
     * Preferably, the files are stored in subfolders named after the category.
     * The images' src attribute must contain a path using the image id or
     * contain the subfolders.
     *
     * @return string
     */
    protected function _getFileDefaultpath()
    {
        $path = Files::cleanPath($this->category ?? '');
        if (!empty($path)) {
            $path = $path . DS;
        }

        return $path;
    }

    /**
     * Get table of contents
     *
     * Extracts all h1, h2, h3, h4, h5, h6 elements from the content and created a toc array.
     * IDs and classes are injected into the HTML content and the result is assigned to the
     * content property. Thus, make sure to request the toc before the content.
     *
     * @return array
     */
    protected function _getToc()
    {

        if (!is_null($this->_toc)) {
            return $this->_toc;
        }

        try {
            $events = new HtmlHeader(false);
            $scanner = new Scanner($this->html, !empty($options['encoding']) ? $options['encoding'] : 'UTF-8');
            $parser = new Tokenizer(
                $scanner,
                $events,
                !empty($options['xmlNamespaces']) ? Tokenizer::CONFORMANT_XML : Tokenizer::CONFORMANT_HTML
            );

            $parser->parse();

            $html5 = new HTML5();
            $this->format = 'html';
            $value = $html5->saveHTML($events->document());
            $value = preg_replace('/^<!DOCTYPE html>\n/', '', $value);
            $value = preg_replace('/^<html>/', '', $value);
            $value = preg_replace('/<\/html>$/', '', $value);
            $this->content = $value;

            $this->_toc = $events->toc;

            // Add tree structure
            if (!empty($this->_toc)) {
                $this->_toc = PositionBehavior::addTreePositions($this->_toc);
            }


        } catch (Exception $e) {
            $this->_toc = [];
        }

        return $this->_toc;
    }

    /**
     * Transform doc to HTML format
     *
     * @return string
     */
    protected function _getHtml()
    {
        $this->transformToHtml();
        return $this->content;
    }

    /**
     * Return fields to be rendered in view/edit table
     *
     * @return array[]
     */
    protected function _getHtmlFields()
    {
        $fields = [
            'name' => [
                'caption' => __('Name'),
                'action' => ['edit', 'add']
            ],

            'format' => [
                'type' => 'hidden',
                'action' => ['edit', 'add']
            ],

            'content' =>
                [
                    'caption' => __('Content'),
                    'id' => 'textarea_content',
                    'rows' => 20,
                    'type' => $this->format === 'html' ? 'htmleditor' : 'text',
                    'escape' => false,
                    'action' => ['edit', 'add'],
                    'layout' => 'stacked'
                ],
            'category' => [
                'caption' => __('Category'),
                'action' => ['edit', 'add']
            ],
            'menu' => [
                'caption' => __('Add to menu'),
                'type' => 'checkbox',
                'action' => ['edit', 'add']
            ]
        ];

        if ($this->table->config['norm_iri'] ?? false) {
            $frontendfields = [
                'norm_iri' => [
                    'caption' => __('Pretty URL (iri)'),
                    'action' => ['edit', 'add']
                ],

                'sortkey' => [
                    'caption' => __('Sort key'),
                    'action' => ['edit', 'add']
                ],

                'published' => [
                    'caption' => __('Published'),
                    'type' => 'checkbox',
                    'action' => ['edit', 'add']
                ]
            ];

            $fields = array_merge($fields, $frontendfields);
        }

        return $fields;
    }


    /**
     * Transform markdown doc to HTML format
     * //TODO: make protected, call in afterFind instead of the Controllers
     *
     * @return string
     */
    public function transformToHtml()
    {
        if ($this->format == 'markdown') {
            $parser = new \Michelf\MarkdownExtra;
            $this->content = $parser->transform($this->content);
            $this->format = 'html';
        }
        return $this;
    }
}
