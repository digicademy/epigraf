<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Auth\Storage;


/**
 * Session based persistent storage for authenticated user record.
 * Overwritten to disable automatic session renewal.
 *
 */
class SessionStorage extends \Cake\Auth\Storage\SessionStorage
{

    /**
     * Write a user record to the session
     *
     * Disable session renewal to persist parallel AJAX requests.
     *
     * @param array $user
     *
     * @return void
     */
    public function write($user): void
    {
        $this->_user = $user;

        //$this->_session->renew();
        $this->_session->write($this->_config['key'], $user);
    }

}
