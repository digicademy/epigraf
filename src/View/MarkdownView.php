<?php
declare(strict_types=1);

/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         2.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\View;

use Cake\Datasource\EntityInterface;
use Cake\ORM\ResultSet;
use Epi\Model\Entity\RootEntity;

/**
 * A view class that is used for Markdown responses.
 *
 */
class MarkdownView extends ApiView
{

    /**
     * The name of the layout file to render the view inside of. The name
     * specified is the filename of the layout in /src/Template/Layout without
     * the .php extension. The file is only rendered, if the serialize option is not set.
     *
     * @var string
     */
    public $layout = 'md';

    /**
     * @var string The file extension for the response.
     */
    static protected $_extension = 'md';


    public function initialize(): void
    {
        // Map extension to mime types
        $this->getResponse()->setTypeMap('md', ['text/markdown']);

        parent::initialize();
        $this->loadHelper('Epi.Types');
        $this->loadHelper('Widgets.Link');
        $this->loadHelper('Widgets.EntityMarkdown');
    }

    public static function contentType(): string
    {
        return 'text/markdown';
    }

    /**
     * Render the entity content
     *
     * @param array|EntityInterface $data
     * @param array $options
     * @param int $level The level of indentation
     * @return string
     */
    public function renderContent($data, $options = [], $level = 0)
    {
        $value = '';

        // Single entities
        if (is_object($data) && ($data instanceof RootEntity)) {
            $value = $this->EntityMarkdown->render($data, $options);
        }

        // Set of entities
        elseif ($data instanceof ResultSet) {
            foreach ($data as $row) {
                if ((is_object($row) && ($row instanceof RootEntity))) {
                    $value .= $this->EntityMarkdown->render($row, $options);
                }
            }
        }

        else {
            // Objects with toArray method
            if (is_object($data) && method_exists($data, 'toArray') && is_callable([$data, 'toArray'])) {
                $data = $data->toArray();
            }

            // Arrays
            if (is_array($data)) {
                foreach ($data as $row) {
                    $value .= $this->renderContent($row, $options, $level + 1);
                }
            }
        }

        return $value;
    }

}
