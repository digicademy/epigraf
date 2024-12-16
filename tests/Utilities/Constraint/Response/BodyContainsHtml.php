<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @since         3.7.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\Utilities\Constraint\Response;

use Cake\TestSuite\Constraint\Response\ResponseBase;
use Psr\Http\Message\ResponseInterface;

/**
 * BodyContains
 *
 * @internal
 */
class BodyContainsHtml extends ResponseBase
{
    /**
     * @var bool
     */
    protected $ignoreCase;

    /**
     * Constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response A response instance.
     * @param bool $ignoreCase Ignore case
     */
    public function __construct(ResponseInterface $response, bool $ignoreCase = false)
    {
        parent::__construct($response);

        $this->ignoreCase = $ignoreCase;
    }

    /**
     * Checks assertion
     *
     * @param mixed $other Expected type
     * @return bool
     */
    public function matches($other): bool
    {
        $method = 'mb_strpos';
        if ($this->ignoreCase) {
            $method = 'mb_stripos';
        }

        $other = trim(preg_replace("/\s+/", '', $other));
        $body = trim(preg_replace("/\s+/", '', $this->_getBodyAsString()));

        return $method($body, $other) !== false;
    }

    /**
     * Assertion message
     *
     * @return string
     */
    public function toString(): string
    {
        return 'is a HTML snippet contained in response body';
    }
}
