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
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Swow\Http\Buffer;
use Swow\Http\Response;
use Swow\Http\Server\Connection;
use Swow\WebSocket\Frame;

require_once __DIR__ . '/../vendor/autoload.php';

function to_buffer(string $body): Buffer
{
    return Buffer::for($body)->rewind();
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

$server->bind('0.0.0.0', 9503)->handle(function (RequestInterface $request, Connection $connection) use ($emitter) {
    switch ($request->getUri()->getPath()) {
        case '/':
            $socket = new WebSocket($connection, $request);
            $socket->on(WebSocket::ON_CLOSE, static function (Connection $connection, int $fd) {
                var_dump('closed: ' . $fd);
                $connection->close();
            });
            $socket->on(WebSocket::ON_MESSAGE, static function (Connection $connection, Frame $frame) {
                $received = (string) $frame->getPayloadData();
                $frame->getPayloadData()->rewind()->write("received: {$received}");
                $connection->sendWebSocketFrame($frame);
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
