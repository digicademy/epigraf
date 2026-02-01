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

use App\Utilities\Converters\Attributes;

/**
 * Remove XML tags from articles
 */
class TaskRemoveXml extends BaseTaskMutate
{

    /**
     * Get options for the configuration form
     *
     * @param array $fields
     * @return array[]
     */
    public function updateHtmlFields($fields)
    {

        $fields['config.params.tags'] =
            [
                'caption' => __('Tags'),
                'placeholder' => __('comma separated element names, e.g. "note,ref"')
            ];

        return $fields;
    }

    /**
     * Get parameters that are passed to the mutateEntities method
     *
     * @return array
     */
    public function getTaskParams()
    {

        $params = parent::getTaskParams();
        $params['tagnames'] = Attributes::commaListToStringArray($this->job->config['params']['tags'] ?? null);
        return $params;
    }
}
