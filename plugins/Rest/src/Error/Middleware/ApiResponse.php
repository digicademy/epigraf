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

use Cake\Http\Response;


/**
 * API response
 */
class ApiResponse extends Response
{

    /**
     * Constructor
     *
     * @param $data
     * @param $type
     */
    public function __construct($data, $type)
    {
        parent::__construct(['body' => json_encode($data), 'status'=> 200, 'type'=>$type]);
    }
}

