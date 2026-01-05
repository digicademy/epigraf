<?php
declare(strict_types=1);

namespace App\View;

use App\Utilities\Converters\Csv;

/**
 * A view class that is used for XLSX responses.
 *
 * Adds a conversion step to the CSV view.
 *
 */
class XlsxView extends CsvView
{
    static protected $_extension = 'xlsx';

    public function initialize(): void
    {
        // Map extension to mime types
        $this->getResponse()->setTypeMap('xlsx', ['application/vnd.ms-excel']);
        parent::initialize();
    }

    public static function contentType(): string
    {
        return 'application/vnd.ms-excel';
    }

    /**
     * Post process the file when called from an export pipeline
     *
     * @param string $filename
     * @return boolean Return true if post-processing was successful
     */
    public function postProcess($filename)
    {
        Csv::csvToxlsx($filename, $filename, ';', '"');
        return true;
    }

    /**
     * Render the file as xlsx when called from a controller.
     *
     * @param string|null $template The template being rendered.
     * @param string|false|null $layout The layout being rendered.
     * @return string The rendered view.
     */
    public function render(?string $template = null, $layout = null): string
    {
        $content = parent::render($template, $layout);
        return Csv::csvTextToXlsx($content, ';', '"');
    }

}
