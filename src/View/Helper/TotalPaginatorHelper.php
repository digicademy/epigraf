<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

declare(strict_types=1);

namespace App\View\Helper;
use Cake\View\Helper;

/**
 * Extend the default helper for multiple sort field support
 *
 */
class TotalPaginatorHelper extends Helper\PaginatorHelper
{


    /**
     * Gets the current direction the recordset is sorted
     *
     * The parent method only supports a single sort field,
     * so we need to override it to support multiple sort fields.
     * The direction can be a comma separated list of directions, one for each sort field.
     *
     * @param string|null $model Optional model name. Uses the default if none is specified.
     * @param array<string, mixed> $options Options for pagination links.
     * @return string The direction by which the recordset is being sorted.
     */
    public function sortDir(?string $model = null, array $options = []): string
    {
        $dir = 'asc';

        if (empty($options)) {
            $options = $this->params($model);
        }

        if (!empty($options['direction'])) {
            $dir = $options['direction'];
            if (is_array($options['direction'])) {
                $dir = implode(',', $dir);
            }
            $dir = strtolower($dir);
        }

        return $dir;
    }

}
