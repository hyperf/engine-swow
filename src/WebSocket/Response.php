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
use Hyperf\Engine\Exception\InvalidArgumentException;
use Swow\Psr7\Server\ServerConnection;

class Response implements ResponseInterface
{
    public function __construct(protected mixed $connection)
    {
    }

    public function push(FrameInterface $frame, int $fd = 0): bool
    {
        if (! $this->connection instanceof ServerConnection) {
            throw new InvalidArgumentException('The websocket connection is invalid.');
        }

        $this->connection->sendWebSocketFrame($frame);

        return true;
    }
}
