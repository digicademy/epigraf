<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Utilities\Converters;

use OpenSpout\Writer\XLSX\Entity\SheetView;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Style\Style;

class Csv
{

    /**
     * Convert a csv file to a xlsx file
     *
     * @param string $csvPath CSV file name.
     * @param string $xlsxPath The output filename.
     * @param string $csvDelim The delimiter used in the csv file.
     * @param string $csvEnclosure The enclosure used in the csv file.
     * @return false|string
     */
    static public function csvToXlsx($csvPath , $xlsxPath, $csvDelim = ',', $csvEnclosure = '"')
    {
        $tmpPath = tempnam(dirname($xlsxPath), 'tmp_xlsx_');

        $options = new \OpenSpout\Reader\CSV\Options();
        $options->FIELD_DELIMITER = $csvDelim;
        $options->FIELD_ENCLOSURE = $csvEnclosure;

        $reader = new \OpenSpout\Reader\CSV\Reader($options);
        $reader->open($csvPath);
        try {
            $writer = new \OpenSpout\Writer\XLSX\Writer();
            $writer->openToFile($tmpPath);

            $sheetView = new SheetView();
            $sheetView->setFreezeRow(2);
            $sheetView->setFreezeColumn('B');
            $writer->getCurrentSheet()->setSheetView($sheetView);

            $style = new Style();
            $style->setShouldWrapText(false);

            try {
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        $row->setStyle($style);

                        $cells = $row->getCells();

                        foreach ($cells as $i => $cell) {
                            $cells[$i] = \OpenSpout\Common\Entity\Cell::fromValue(
                                self::shortenExcelCell($cell->getValue())
                            );
                        }
                        $writer->addRow(new \OpenSpout\Common\Entity\Row($cells));
                    }
                }
            } finally {
                $writer->close();
            }
        }
        finally {
            $reader->close();
        }

        $result = rename($tmpPath, $xlsxPath);
        return $result;
    }

    /**
     * Convert CSV content (string) to XLSX content (string).
     *
     * @param string $csvContent CSV formatted string.
     * @param string $csvDelim CSV delimiter.
     * @param string $csvEnclosure CSV enclosure.
     * @return false|string XLSX binary content, or false on failure
     */
    static public function csvTextToXlsx(string $csvContent, string $csvDelim = ',', string $csvEnclosure = '"')
    {
        $tempCsvFile = tempnam(sys_get_temp_dir(), 'csv_');
        $tempXlsxFile = tempnam(sys_get_temp_dir(), 'xlsx_');

        if ($tempCsvFile === false || $tempXlsxFile === false) {
            return false;
        }

        if (file_put_contents($tempCsvFile, $csvContent) === false) {
            @unlink($tempCsvFile);
            @unlink($tempXlsxFile);
            return false;
        }

        $result = self::csvToXlsx($tempCsvFile, $tempXlsxFile, $csvDelim, $csvEnclosure);

        if (!$result) {
            @unlink($tempCsvFile);
            @unlink($tempXlsxFile);
            return false;
        }

        // Read the XLSX file content
        $xlsxContent = file_get_contents($tempXlsxFile);

        // Cleanup temp files
        @unlink($tempCsvFile);
        @unlink($tempXlsxFile);

        return $xlsxContent !== false ? $xlsxContent : false;
    }

    /**
     * Convert an array to an HTML table
     *
     * @param string|array $data A string containing csv data or an already parsed array
     * @param boolean $hasHeader Whether the first row is treated as a header
     * @return string
     */
    static public function csvToHtml($data, $hasHeader = true) {
        if (is_string($data)) {
            $data = str_getcsv($data);
        }

        $body = '';
        $header = '';

        foreach ($data as $idx => $row) {
            if (!is_array($row)) {
                $row = [$row];
            }

            $row_html = '<tr>';
            foreach ($row as $cell) {
                if (($idx === 0) && $hasHeader) {
                    $row_html .= '<th>' . htmlspecialchars($cell) . '</th>';
                } else {
                    $row_html .= '<td>' . htmlspecialchars($cell) . '</td>';
                }
            }
            $row_html .= '</tr>';

            if (($idx === 0) && $hasHeader) {
                $header .= $row_html;
            } else {
                $body .= $row_html;
            }
        }

        return '<table class="csv-table"><thead>' . $header . '</thead><tbody>' . $body . '</tbody></table>';

    }

    private static function shortenExcelCell($value, $max = 32760)
    {
        if (!is_string($value)) return $value;

        if (strlen($value) > $max) {
            return substr($value, 0, $max) . '…';
        }

        return $value;
    }
}
