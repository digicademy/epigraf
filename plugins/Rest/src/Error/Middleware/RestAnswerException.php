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

namespace Rest\Error\Middleware;

/**
 * Rest answer exception
 *
 */
class RestAnswerException extends \Cake\Core\Exception\CakeException
{

    /**
     * Success value
     *
     * @var bool|null
     */
    public $success = null;

    /**
     * Message
     *
     * @var string
     */
    public $message = '';

    /**
     * Redirect URL
     *
     * @var array|string
     */
    public $url = [];

    /**
     * Additional data
     *
     * @var array
     */
    public $data = [];

    /**
     * Add url
     *
     * @var bool
     */
    public $addUrl = false;

    /**
     * Constructor
     *
     * @param string|array $url
     * @param bool $success
     * @param string|boolean $message
     * @param array $data
     * @param bool $addUrl
     *
     * //todo addurl seems to be always false.
     */
    public function __construct($url, bool $success=true, $message=false, array $data = [], bool $addUrl = false)
    {
        parent::__construct();

        $this->url = $url;
        $this->data = $data;
        $this->message = $message !== false ? $message : '';
        $this->success = $success;
        $this->addUrl = $addUrl;
    }

}
