<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Entity\Tasks;

use App\Model\Entity\BaseTask;
use App\Utilities\Files\Files;
use Cake\Core\Configure;
use DOMDocument;
use Saxon\SaxonProcessor;
use XSLTProcessor;

/**
 * Copy files in the export pipeline
 */
class TaskTransformxsl extends BaseTask
{

    /**
     * Concatenate files
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        $inputFile = $this->job->getCurrentInputFilePath();
        $outputFile = $this->job->getCurrentOutputFilePath();

        if (($this->config['processor'] ?? 'php') == 'saxon') {
            return $this->task_xsl_saxon($inputFile, $outputFile);
        }
        else {
            return $this->task_xsl_php($inputFile, $outputFile);
        }
    }

    /**
     * Transform XML file using the default PHP parser
     *
     * @param string $inputFile The input file
     * @param string $outputFile The output file
     * @return bool true if the task is finished
     */
    public function task_xsl_php($inputFile, $outputFile)
    {

        // XML-Quellen laden
        $xml = new DOMDocument;

        $current = $this->config;

        $result = $xml->load($inputFile);
        libxml_use_internal_errors(true);
        if (!$result) {
            $errors = [];
            foreach (libxml_get_errors() as $error) {
                $errors[] = $error->message;
            }
            $errors = implode("\n", $errors);
            $errors = "Libxml error in file " . $inputFile . ": " . $errors;
            throw new \Cake\Core\Exception\CakeException($errors);
        }
        libxml_use_internal_errors(false);

        // XSL Document laden
        $xsl = new DOMDocument;
        $result = $xsl->load(Configure::read('Data.shared') . $current['xslfile']);

        libxml_use_internal_errors(true);
        if (!$result) {
            $errors = [];
            foreach (libxml_get_errors() as $error) {
                $errors[] = $error->message;
            }
            $errors = implode("\n", $errors);
            $errors = "Libxml error in file " . $current['xslfile'] . ": " . $errors;
            throw new \Cake\Core\Exception\CakeException($errors);
        }
        libxml_use_internal_errors(false);

        // Prozessor instanziieren und konfigurieren
        $proc = new XSLTProcessor;

        // XSL Document importieren
        //libxml_use_internal_errors(true);
        $result = $proc->importStyleSheet($xsl);
        if (!$result) {
            $errors = [];
            foreach (libxml_get_errors() as $error) {
                $errors[] = $error->message;
            }
            $errors = implode("\n", $errors);
            $errors = "Libxml error in file " . $current['xslfile'] . ": " . $errors;
            throw new \Cake\Core\Exception\CakeException($errors);
        }
        //libxml_use_internal_errors(false);

        $proc->registerPHPFunctions([
            'Images::base64Image',
            'Images::base64URI',
            'Images::fileExists',
            'Images::filePath'
        ]);
        $proc->registerPHPFunctions([
            'HistoricDates::encode',
            'HistoricDates::normalize',
            'HistoricDates::minyear',
            'HistoricDates::maxyear'
        ]);

        $newxml = $proc->transformToXML($xml);
        $newxml = str_replace("\r", "", $newxml);

        Files::replaceFile($outputFile, $newxml);

        return true;
    }

    /**
     * Transform XML file using Saxon
     *
     * @param string $inputFile The input file
     * @param string $outputFile The output file
     * @return bool True if the task is finished
     */
    public function task_xsl_saxon($inputFile, $outputFile)
    {
        $current = $this->config;

        // INITIALIZE PROCESSOR
        $saxonProc = new SaxonProcessor();

        // XSL Document laden
        $xslFile = Configure::read('Data.shared') . $current['xslfile'];
        $xsltProc = $saxonProc->newXslt30Processor();
//        $xsltProc->compileFromFile($xslFile);

        // Run transformation
        try {
            $newxml = $xsltProc->transformFileToString($inputFile, $xslFile);
            $newxml = str_replace("\r", "", $newxml);
        } catch (\Exception $e) {
            throw new \Cake\Core\Exception\CakeException("Error with file " . $current['xslfile'] . ": " . $e->getMessage());
        }

//        // Get errors
//        $errorcount = $xsltProc->getExceptionCount();
//        $errors = [];
//        if ($errorcount) {
//            for ($i = 0; $i < $errorcount; $i++) {
//                $errors[] = $xsltProc->getErrorMessage($i);
//            }
//
//            $errors = "Error with file " . $current['xslfile'] . ": " . implode("\n", $errors);
//            throw new \Cake\Core\Exception\CakeException($errors);
//        }

        // Save output
        Files::replaceFile($outputFile, $newxml);

        return true;
    }


}
