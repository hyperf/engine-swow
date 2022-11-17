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
use Swow\Http\Protocol\ProtocolException as HttpProtocolException;
use Swow\Http\Status;
use Swow\Psr7\Message\RequestPlusInterface;
use Swow\Psr7\Message\UpgradeType;
use Swow\Psr7\Psr7;
use Swow\Psr7\Server\ServerConnection;
use Swow\WebSocket\Opcode;
use Swow\WebSocket\WebSocket as SwowWebSocket;

class WebSocket implements WebSocketInterface
{
    protected ?ServerConnection $connection;

    /**
     * @var array<string, callable>
     */
    protected array $events = [];

    public function __construct(ServerConnection $connection, RequestPlusInterface $request)
    {
        $upgradeType = Psr7::detectUpgradeType($request);

        if (($upgradeType & UpgradeType::UPGRADE_TYPE_WEBSOCKET) === 0) {
            $this->throwBadRequestException();
        }

        $this->connection = $connection;
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
                    $this->connection->send(SwowWebSocket::PONG_FRAME);
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
        throw new HttpProtocolException(Status::BAD_REQUEST, 'Unsupported Upgrade Type');
    }
}
