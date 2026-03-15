<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Utilities\Exceptions;

class CatchWarnings
{
    /**
     * Execute a callback while suppressing deprecation warnings.
     *
     * @param callable $callback
     * @return mixed
     */
    public static function mute(callable $callback)
    {
        $oldMask = error_reporting();
        error_reporting($oldMask & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        try {
            return $callback();
        } finally {
            error_reporting($oldMask);
        }
    }
}
