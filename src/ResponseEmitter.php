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

use Hyperf\HttpServer\ResponseEmitter as Emitter;
use Psr\Http\Message\ResponseInterface;
use Swow\Psr7\Message\ResponsePlusInterface;
use Swow\Psr7\Server\ServerConnection;

class ResponseEmitter extends Emitter
{
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
            $headers = $response->getHeaders();
            $body = (string) $response->getBody();
            if ($connection->shouldKeepAlive() !== null) {
                $headers['Connection'] = $connection->shouldKeepAlive() ? 'Keep-Alive' : 'Closed';
            }
            if (! $response->hasHeader('Content-Length')) {
                $headers['Content-Length'] = strlen($body);
            }

            if ($response instanceof ResponsePlusInterface) {
                $response->setHeaders($headers);
            } else {
                $response = $response->withAddedHeader('Connection', $headers['Connection'])
                    ->withAddedHeader('Content-Length', $headers['Content-Length']);
            }

            $connection->sendHttpResponse($response);
        } catch (\Throwable) {
        }
    }
}
