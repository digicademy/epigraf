<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Utilities\Files;

use App\Utilities\XmlParser\XmlMunge;
use App\View\CsvView;
use Cake\Filesystem\Folder;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenTime;
use Cake\Utility\Hash;
use Psr\Http\Message\UploadedFileInterface;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;
use SplFileInfo;
use SplFileObject;
use ZipArchive;

/**
 * Parse error logs
 */
class Logs
{
    /**
     * Parses the PHP error log to an array.
     *
     * @param string $filename
     * @return \Generator
     */
    static function parse($filename): \Generator
    {
        $currentEntry = [];
        $currentKey = '';
        $logFileHandle = fopen($filename, 'rb');

        while (!feof($logFileHandle)) {
            $currentLine = str_replace(PHP_EOL, '', fgets($logFileHandle));

            // A new log entry start with date and time, e.g. 2023-11-17 10:22:49
            if (preg_match(
                '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2} /',
                $currentLine,
                $currentMatches
            )) {

                if (!empty($currentEntry)) {
                    yield $currentEntry;
                }

                // Start entry with datetime
                $currentEntry = ['datetime' => trim($currentMatches[0])];
                $currentKey = '';
                $currentLine = trim(substr($currentLine, strlen($currentMatches[0])));

                // Entry type (e.g. "Error:")
                if (preg_match('/^[^ ]+/', $currentLine, $currentMatches)) {
                    $currentLine = trim(substr($currentLine, strlen($currentMatches[0])));
                    $currentEntry['type'] = trim($currentMatches[0], " :\t\n\r\0\x0B");
                }

                // Exception type (e.g. "[PDOException]")
                if (preg_match('/^\[[^\]]+]/', $currentLine, $currentMatches)) {
                    $currentLine = trim(substr($currentLine, strlen($currentMatches[0])));
                    $currentEntry['exception'] = trim($currentMatches[0], " :\t\n\r\0\x0B");
                }

                $currentEntry['message'] = $currentLine;
            }

            // Additional log data start with a keyword followed by a colon, e.g. "Stack trace:"
            elseif (preg_match(
                '/^[A-Za-z ]+: /',
                $currentLine,
                $currentMatches
            )) {
                $currentLine = trim(substr($currentLine, strlen($currentMatches[0])));
                $currentKey = trim($currentMatches[0], " :\t\n\r\0\x0B");
                $currentValue = trim($currentLine);
                $currentEntry[$currentKey] = $currentValue;
            }

            // Other lines belong to the last data key
            elseif (!empty($currentKey)) {
                if (!is_array($currentEntry[$currentKey])) {
                    $currentEntry[$currentKey] = [$currentEntry[$currentKey]];
                    $currentEntry[$currentKey] = array_filter($currentEntry[$currentKey]);
                }
                $currentEntry[$currentKey][] = $currentLine;
            }
        }

        if (!empty($currentEntry)) {
            yield $currentEntry;
        }
    }

}

