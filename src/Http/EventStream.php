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
namespace Hyperf\Engine\Http;

use Hyperf\Engine\Contract\Http\Writable;
use Psr\Http\Message\ResponseInterface;
use Swow\Http\Http;
use Swow\Psr7\Server\ServerConnection;

class EventStream
{
    public function __construct(protected Writable $connection, ?ResponseInterface $response = null)
    {
        $headers = [
            'Content-Type' => 'text/event-stream; charset=utf-8',
            'Transfer-Encoding' => 'chunked',
            'Cache-Control' => 'no-cache',
        ];
        foreach ($response?->getHeaders() ?? [] as $name => $values) {
            $headers[$name] = implode(', ', $values);
        }

        /** @var ServerConnection $socket */
        $socket = $this->connection->getSocket();
        $socket->write([
            Http::packResponse(
                statusCode: 200,
                headers: $headers,
                protocolVersion: '1.1'
            ),
        ]);
    }

    public function write(string $data): self
    {
        $this->connection->write($data);
        return $this;
    }

    public function end(): void
    {
        $this->connection->end();
    }
}
