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

use App\Utilities\Files\Files;
use App\View\XmlView;

/**
 * Output options in the export pipeline
 */
class TaskOptions extends BaseTaskData
{

    /**
     * Execute task
     *
     * @return bool Return true if the task is finished
     */
    public function execute()
    {
        // Skip if no output format is defined
        if (empty($this->config['format'])) {
            return true;
        }

        // User defined options
        $userOptions = array_filter($this->job->config['options']['options'], function ($x) {
            return ($x['type'] !== 'radio') || !empty($x['output']);
        });

        $userOptions = array_map(function ($x) {
            $x['value'] = ($x['type'] === 'radio') ? $x['value'] : $x['output'];
            return $x;
        }, $userOptions);

        $userOptions = array_combine(
            array_map(fn($x) => $x['key'] ?? '_', $userOptions),
            array_map(fn($x) => $x['value'] ?? '', $userOptions)
        );

        // Data attributes
        /* @deprecated Remove data attributes.
         *             Instead, in the stylesheets,
         *             check existence of the respective elements
         *             (articletype=epi-book).
         */
        $textOption = 0;
        foreach ($this->job->config['pipeline_tasks'] ?? [] as $taskNo => $taskConfig) {
            // Look for tasks that exported "Bandartikel" (epi-book) by using the projects parameter
            // See TaskDataArticle::execute()
            if (
                !empty($taskConfig['matchprojects']) &&
                !empty($this->job->config['options']['enabled'][$taskNo]['enabled'] ?? true)
            ) {
                $textOption = 1;
                break;
            }
        }
        $dataOptions = [
            'objects' => 1,
            'index' => intval(!empty($this->job->config['options']['index'])),
            'text' => $textOption,
        ];

        // Output options element
        $options = array_merge($userOptions, $dataOptions);

        $view = $this->getView();
        if ($view instanceof XmlView) {
            $options['_xml_attributes'] = array_keys($options);
        }
        $content = $view->renderContent($options, ['tagname' => 'options'], 1);
        //$content = "\n  <options " . Attributes::toHtml($options) . "></options>";

        $outputfile = $this->getCurrentOutputFilePath();
        Files::appendToFile($outputfile, $content);

        return true;
    }


}
