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

use Hyperf\Engine\Contract\WebSocket\WebSocketInterface;
use Hyperf\HttpMessage\Exception\BadRequestHttpException;
use Swow\Http\Exception as HttpException;
use Swow\Http\Server\Connection;
use Swow\Http\Server\Request;
use Swow\Http\Status;

class WebSocket implements WebSocketInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var array<string, callable>
     */
    protected $events = [];

    public function __construct(Connection $connection, Request $request)
    {
        $this->connection = $connection;
        if ($request->getUpgrade() !== $request::UPGRADE_WEBSOCKET) {
            $this->throwBadRequestException();
        }

        $this->connection->upgradeToWebSocket($request);
    }

    public function on(string $event, callable $callback): void
    {
        $this->events[$event] = $callback;
    }

    public function start(): void
    {
        while (true) {
            $frame = $this->connection->recvWebSocketFrame();
            $opcode = $frame->getOpcode();
            switch ($opcode) {
                case Opcode::PING:
                    $this->connection->sendString(Frame::PONG);
                    break;
                case Opcode::PONG:
                    break;
                case Opcode::CLOSE:
                    $callback = $this->events[static::ON_CLOSE];
                    $callback($this->connection, $this->connection->getFd());
                    break 2;
                default:
                    $callback = $this->events[static::ON_MESSAGE];
                    $callback($this->connection, $frame);
            }
        }

        $this->connection = null;
        $this->events = [];
    }

    private function throwBadRequestException()
    {
        if (class_exists(BadRequestHttpException::class)) {
            throw new BadRequestHttpException('Unsupported Upgrade Type');
        }
        throw new HttpException(Status::BAD_REQUEST, 'Unsupported Upgrade Type');
    }
}
