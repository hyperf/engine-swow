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
use Swow\Http\Http;
use Swow\Psr7\Server\ServerConnection;

class EventStream
{
    protected bool $isTransfer = false;

    public function __construct(protected Writable $connection)
    {
    }

    public function createStream(): self
    {
        if (! $this->isTransfer) {
            /** @var ServerConnection $socket */

            $socket = $this->connection->getSocket();
            $socket->write([
                Http::packResponse(
                    statusCode: 200,
                    headers: [
                        'Content-Type' => 'text/event-stream; charset=utf-8',
                        'Transfer-Encoding' => 'chunked',
                    ],
                    protocolVersion: '1.1'
                ),
            ]);
        }
        $this->isTransfer = true;
        return $this;
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