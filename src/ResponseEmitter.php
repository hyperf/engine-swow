<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Engine;

use Exception;
use Hyperf\Collection\Arr;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Engine\Contract\ResponseEmitterInterface;
use Hyperf\Engine\Http\WritableConnection;
use Hyperf\HttpMessage\Server\Response;
use Hyperf\HttpServer\ResponseEmitter as Emitter;
use Psr\Http\Message\ResponseInterface;
use Stringable;
use Swow\Psr7\Message\ResponsePlusInterface;
use Swow\Psr7\Psr7;
use Swow\Psr7\Server\ServerConnection;

class ResponseEmitter extends Emitter implements ResponseEmitterInterface
{
    public function __construct(protected ?StdoutLoggerInterface $logger)
    {
    }

    /**
     * @param ResponseInterface|ResponsePlusInterface $response
     * @param ServerConnection $connection
     */
    public function emit(ResponseInterface $response, mixed $connection, bool $withContent = true): void
    {
        try {
            if ($connection->getProtocolType() === ServerConnection::PROTOCOL_TYPE_WEBSOCKET) {
                return;
            }

            if ($response instanceof Response && $writableConnection = $response->getConnection()) {
                if ($writableConnection instanceof WritableConnection && $writableConnection->isSent()) {
                    return;
                }
            }

            $response = $this->setCookies($response);
            $headers = $response->getHeaders();
            if ($response instanceof ResponsePlusInterface) {
                $headers = $response->getStandardHeaders();
            } else {
                $headers['Connection'] = $connection->shouldKeepAlive() ? 'keep-alive' : 'closed';
                if (! $response->hasHeader('Content-Length')) {
                    $body = (string) $response->getBody();
                    $headers['Content-Length'] = strlen($body);
                }
            }

            $response = Psr7::setHeaders($response, $headers);

            $connection->sendHttpResponse($response);
        } catch (Exception $exception) {
            $this->logger?->critical((string) $exception);
        }
    }

    protected function setCookies(ResponseInterface $response): ResponseInterface
    {
        if (method_exists($response, 'getCookies')) {
            foreach (Arr::flatten((array) $response->getCookies(), 3) as $cookie) {
                if ($cookie instanceof Stringable) {
                    $response = $response->withAddedHeader('Set-Cookie', (string) $cookie);
                }
            }
        }
        return $response;
    }
}
