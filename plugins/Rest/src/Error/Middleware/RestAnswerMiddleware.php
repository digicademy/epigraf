<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.3.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Rest\Error\Middleware;

use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Routing\Router;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Error handling middleware.
 *
 * Traps exceptions and converts them into HTML or content-type appropriate
 * error pages using the CakePHP WebExceptionRenderer.
 */
class RestAnswerMiddleware extends ErrorHandlerMiddleware
{

    /**
     * Wrap the remaining middleware with error handling.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     *
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (RestAnswerException $exception) {
            return $this->handleRestAnswer($request, $exception);
        }
    }

    /**
     * Convert a redirect exception into a response.
     *
     * @param ServerRequestInterface $request The request
     * @param \Cake\Http\Exception\RedirectException $exception The exception to handle
     *
     * @return \Psr\Http\Message\ResponseInterface Response created from the redirect.
     */
    public function handleRestAnswer(ServerRequestInterface $request, RestAnswerException $exception): ResponseInterface
    {

        // API answer
        if ($request->is('api')) {

            $data = [
                'success' => $exception->success,
                'message' => $exception->message,
            ];

            if ($exception->addUrl) {
                $url = $exception->url;
                if (is_string($url)) {
                    $url = Router::parseRoutePath($url);
                }
                $url['_ext'] = $request->getParam('_ext');
                $data['nexturl'] = Router::url($url, true);
            }
            $data = array_merge($data, $exception->data);

            return new ApiResponse($data, $request->getParam('_ext'));
        }

        // Redirect
        else {
            return new RedirectResponse(
                Router::url($exception->url, true)
            );
        }
    }
}
