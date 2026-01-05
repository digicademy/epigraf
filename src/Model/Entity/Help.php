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

use App\Utilities\Converters\Strings;
use Cake\ORM\Entity;
use Epi\Model\Behavior\PositionBehavior;
use Michelf\MarkdownExtra;
use Symfony\Component\Yaml\Yaml;

/**
 * Help file
 *
 */
class Help extends Entity
{

    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);
        $this->parseFile();
    }

    protected function parseFile() {
        if (empty($this->_fields['content'])) {
            $filename = $this->getFilename();

            $this->_fields['exists'] = file_exists($filename);

            if ($this->_fields['exists']) {
                $content = file_get_contents($filename);

                // Extract header
                $headerStart = strpos($content, '---');
                $headerEnd = $headerStart !== false ? strpos($content, '---', $headerStart + 3) : false;

                if (($headerStart !== false) && ($headerEnd !== false)) {
                    $headerContent = substr($content, $headerStart + 3, $headerEnd - $headerStart - 3);
                    $this->_fields['header'] = Yaml::parse($headerContent);
                    $content = substr($content, $headerEnd + 3);
                }

                // Parse Markdown
                $parser = new MarkdownExtra();

                // TODO: Include upper level headers in ID slug
                $parser->header_id_func = function ($text) {
                    return preg_replace('/[^a-z0-9]+/', '-', strtolower($text));
                };

                $baseUrl = $this->getBaseURL();
                $parser->url_filter_func = function ($url) use ($baseUrl) {

                    if (!str_starts_with($url, 'http') && !str_starts_with($url, '/')) {
                        $url = $baseUrl . $url;
                    }
                    return $url;
                };

                $this->_fields['html'] = $parser->transform($content);

                if ($this->_fields['header']['toc'] ?? true) {
                    $toc = Strings::getToc($this->_fields['html']);
                    $this->_fields['toc'] = $toc['toc'];
                    $this->_fields['html'] = $toc['html'];
                } else {
                    $this->_fields['toc'] = [];
                }
            }
        }
    }


    /**
     * Get the filename of the help file
     *
     * The filename is constructed from the path.
     * There are two cases:
     * 1. A simple folder name denotes the index file in that folder.
     * 2. A path with slashes denotes a file in that path.
     *
     * @return string
     */
    public function getFilename() : string
    {
        $path = array_filter(explode('/', $this->_fields['path'] ?? ''));

        if (count($path) > 1) {
            $filename = array_pop($path);
            $path = implode('/', $path) . DS . $filename . '.md';
        } else {
            $path = implode('/', $path) . DS . 'index.md';
        }

        return self::getHelpFolder() . $path;
    }

    /**
     * Get the base URL of the help file
     *
     * The base URL is the basis for relative URLs in the file.
     *
     * @return string
     */
    public function getBaseURL() : string
    {
        $path = array_filter(explode('/', $this->_fields['path'] ?? ''));

        if (count($path) > 1) {
            array_pop($path);
        }

        $path = array_merge(['help'], $path);
        $path = implode('/', $path);
        return '/' . $path . '/';
    }

    /**
     * Get the help file path
     *
     * @return string The path to the help folder
     */
    public static function getHelpFolder(): string
    {
        return ROOT . DS . 'help' . DS;
    }

    protected static function addMenuItems($items, $menu, $level = 0, $parentPath = '')
    {
        foreach ($items as $item) {
            $itemPath = trim($item['url'] ?? '', '/');
            if (!empty($parentPath)) {
                $itemPath = $parentPath . '/' . $itemPath;
            }


            $menu[] = [
                'label' => $item['title'] ?? '',
                'url' => ['controller' => 'Help', 'action' => 'show', $itemPath],
                'level' => $level,
                'tree-collapsed' => true,
                'tree-hidden' => false
            ];

            if (!empty($item['children']) && is_array($item['children'])) {
                $menu = self::addMenuItems($item['children'], $menu, $level + 1, $itemPath);
            }
        }
        return $menu;
    }

    public static function getMenu() : array
    {
        $folder = self::getHelpFolder();
        $nav = Yaml::parseFile($folder . 'navigation.yml');

        $menuItems = [
            [
                'label' => __('Preface'),
                'url' => ['controller' => 'Help', 'action' => 'show'],
                'level' => 0,
                'tree-collapsed' => true,
                'tree-hidden' => false
            ]
        ];

        $menuItems = self::addMenuItems($nav['docs'] ?? [], $menuItems);
        $menuItems = PositionBehavior::addTreePositions($menuItems, true);

        $menu = array_merge(
            [
                'caption' => __('Help'),
                'activate' => true,
                'scrollbox' => true,
                'search' => false,
                'tree' => 'foldable'
            ],
            $menuItems
        );


        return $menu;
    }
}
