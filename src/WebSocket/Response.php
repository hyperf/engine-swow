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

namespace Hyperf\Engine\WebSocket;

use Hyperf\Engine\Contract\WebSocket\FrameInterface;
use Hyperf\Engine\Contract\WebSocket\ResponseInterface;
use Swow\Psr7\Server\ServerConnection;

class Response implements ResponseInterface
{
    public function __construct(protected ServerConnection $connection)
    {
    }

    public function push(FrameInterface $frame): bool
    {
        $this->connection->sendWebSocketFrame($frame);

        return true;
    }

    public function init(mixed $frame): static
    {
        return $this;
    }

    public function getFd(): int
    {
        return $this->connection->getFd();
    }

    public function close(): bool
    {
        return $this->connection->close();
    }
}
