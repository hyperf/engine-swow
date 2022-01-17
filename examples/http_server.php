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
use Hyperf\Engine\Coroutine;
use Hyperf\Engine\Http\Server;
use Hyperf\Engine\ResponseEmitter;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Swow\Http\Buffer;
use Swow\Http\Response;
use Swow\Http\Server\Connection;

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

$server->bind('0.0.0.0', 9501)->handle(function (RequestInterface $request, Connection $session) use ($emitter) {
    switch ($request->getUri()->getPath()) {
        case '/':
            $response = new Response(200, [
                'Server' => 'Hyperf',
            ], to_buffer('Hello World.'));
            $emitter->emit($response, $session);
            break;
        case '/cookies':
            $id = uniqid();
            $response = new Response(200, [
                'Server' => ['Hyperf'],
                'Set-Cookie' => [
                    'X-Server-Id=' . $id,
                    'X-Server-Name=Hyperf',
                ],
            ], to_buffer($id));
            $emitter->emit($response, $session);
            break;
        case '/timeout':
            $query = parse_query($request->getUri()->getQuery());
            sleep((int) $query['time']);
            $response = new Response(200);
            $emitter->emit($response, $session);
            break;
        case '/without-content-length':
            $body = 'HTTP/1.1 400 Bad Request: missing required Host header
Content-Type: text/plain; charset=utf-8
Connection: close

400 Bad Request: missing required Host header';
            $session->write([$body]);
            return;
        case '/coroutine_id':
            Coroutine::create(function () use ($emitter, $session) {
                $id = Coroutine::id();
                $response = new Response(200, [
                    'Server' => 'Hyperf',
                ], to_buffer((string) $id));
                $emitter->emit($response, $session);
            });
            break;
        default:
            $response = new Response(404, [
                'Server' => 'Hyperf',
            ]);
            $emitter->emit($response, $session);
            break;
    }
});

$server->start();
