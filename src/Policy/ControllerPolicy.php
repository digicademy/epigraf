<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */


namespace App\Policy;

class ControllerPolicy
{
    protected $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Magic call method to handle any `can*()` calls via isAuthorized
     */
    public function __call($method, $args)
    {
        if (method_exists($this->controller, 'isAuthorized')) {
            // First argument is the user/identity, second is the authorization service
            $user = $args[0] ?? null;
            $result = $this->controller->isAuthorized($user);

            if ($result) {
                return true;
            }
        }
        return false;
    }
}
