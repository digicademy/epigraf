<?php

namespace App\Error;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Log errors and unhandled exceptions to `Cake\Log\Log`
 */
class ErrorLogger extends \Cake\Error\ErrorLogger
{

    /**
     * Get the request context for an error/exception trace, including the user agent
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request to read from.
     * @return string
     */
    public function getRequestContext(ServerRequestInterface $request): string
    {
        $message = parent::getRequestContext($request);
        $message .= "\nUser Agent: " . $request->getHeaderLine('User-Agent');
        return $message;
    }
}
