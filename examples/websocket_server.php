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
use Hyperf\Engine\Http\Server;
use Hyperf\Engine\ResponseEmitter;
use Hyperf\Engine\WebSocket\WebSocket;
use Psr\Log\LoggerInterface;
use Swow\Buffer;
use Swow\Psr7\Message\RequestPlusInterface;
use Swow\Psr7\Message\Response;
use Swow\Psr7\Message\WebSocketFrame as Frame;
use Swow\Psr7\Psr7;
use Swow\Psr7\Server\ServerConnection;

require_once __DIR__ . '/../vendor/autoload.php';

function to_buffer(string $body): Buffer
{
    $buffer = new Buffer(0);
    $buffer->write(0, $body);
    return $buffer;
}

function parse_query(string $query): array
{
    $result = [];
    foreach (explode('&', $query) as $item) {
        [$key, $value] = explode('=', $item);
        $result[$key] = $value;
    }
    return $result;
}

$logger = Mockery::mock(LoggerInterface::class);
$logger->shouldReceive('error')->withAnyArgs()->andReturnUsing(static function ($args) {
    echo $args . PHP_EOL;
});

$emitter = new ResponseEmitter();
$server = new Server($logger);

$server->bind('0.0.0.0', 9503)->handle(function (RequestPlusInterface $request, ServerConnection $connection) use ($emitter) {
    switch ($request->getUri()->getPath()) {
        case '/':
            $socket = new WebSocket($connection, $request);
            $socket->on(WebSocket::ON_CLOSE, static function (ServerConnection $connection, int $fd) {
                var_dump('closed: ' . $fd);
                $connection->close();
            });
            $socket->on(WebSocket::ON_MESSAGE, static function (ServerConnection $connection, Frame $frame) {
                $received = (string) $frame->getPayloadData();
                $connection->sendWebSocketFrame(
                    Psr7::createWebSocketTextFrame(
                        payloadData: "received: {$received}"
                    )
                );
            });
            $socket->start();
            break;
        default:
            $response = new Response(404, [
                'Server' => 'Hyperf',
            ]);
            $emitter->emit($response, $connection);
            break;
    }
});

$server->start();
