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
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Swow\Http\Buffer;
use Swow\Http\Response;
use Swow\Http\Server\Session;

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
$emiter = new ResponseEmitter();
$server = new Server($logger);

$server->bind('0.0.0.0', 9501)->handle(function (RequestInterface $request, Session $session) use ($emiter) {
    switch ($request->getUri()->getPath()) {
        case '/':
            $response = new Response(200, [
                'Server' => 'Hyperf',
            ], to_buffer('Hello World.'));
            $emiter->emit($response, $session);
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
            $emiter->emit($response, $session);
            break;
        case '/timeout':
            $query = parse_query($request->getUri()->getQuery());
            sleep((int) $query['time']);
            $response = new Response(200);
            $emiter->emit($response, $session);
            break;
        default:
            $response = new Response(404, [
                'Server' => 'Hyperf',
            ]);
            $emiter->emit($response, $session);
            break;
    }
});

$server->start();
