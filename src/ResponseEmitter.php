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
use Swow\Http\Server\Connection;
use function Swow\Http\packResponse;

class ResponseEmitter extends Emitter
{
    /**
     * @param Connection $connection
     */
    public function emit(ResponseInterface $response, $connection, bool $withContent = true)
    {
        $headers = $response->getHeaders();
        $body = $response->getBody()->getContents();
        if ($connection->getKeepAlive() !== null) {
            $headers['Connection'] = $connection->getKeepAlive() ? 'Keep-Alive' : 'Closed';
        }
        if (! $response->hasHeader('Content-Length')) {
            $headers['Content-Length'] = strlen($body);
        }
        $connection->write([
            packResponse($response->getStatusCode(), $headers),
            $body,
        ]);
    }
}
