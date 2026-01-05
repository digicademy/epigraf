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

namespace Rest\Middleware;

use App\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Rate limit middleware
 *
 * Limits the number of requests from a single IP address
 * for unauthenticated users. It uses a sliding window
 * to track the number of requests.
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    protected $limit = 600;   // Max requests
    protected $interval = 60; // Time window in seconds

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->limit = Configure::read('RateLimit.limit', $this->limit);
        $this->interval = Configure::read('RateLimit.interval', $this->interval);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Only limit unauthenticated users
        $identity = $request->getAttribute('identity');
        if ($identity !== null) {
            return $handler->handle($request);
        }

        // TODO: Migrate to authentication / authorization middleware
        // TODO: Handle token authenticated users
        $session = $request->getSession();
        $isAuthenticated = $session->read('Auth.User') !== null;
        if ($isAuthenticated) {
            return $handler->handle($request);
        }

        // Identify user by IP address
        $ip = $request->clientIp();
        $key = "rate_limit_" . md5($ip);

        $now = time();
        $data = Cache::read($key, 'default') ?? ['count' => 0, 'start' => $now];
        if (($now - $data['start']) > $this->interval) {
            $data = ['count' => 1, 'start' => $now];
        } else {
            $data['count']++;
        }

        $remaining = max(0, $this->limit - $data['count']);
        $reset = $data['start'] + $this->interval;
        Cache::write($key, $data, 'default');

        if ($data['count'] > $this->limit) {

            // TODO: Render a custom error page, use AnswerComponent / RestAnwerException
            $response = new Response();
            return $response
                ->withStatus(429)
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('X-RateLimit-Limit', $this->limit)
                ->withHeader('X-RateLimit-Remaining', 0)
                ->withHeader('X-RateLimit-Reset', $reset)
                ->withStringBody(json_encode([
                    'error' => 'Rate limit exceeded. Try again later.'
                ]));
        }

        // Normal request: pass to next and add headers
        $response = $handler->handle($request);
        return $response
            ->withHeader('X-RateLimit-Limit', $this->limit)
            ->withHeader('X-RateLimit-Remaining', $remaining)
            ->withHeader('X-RateLimit-Reset', $reset);
    }
}
