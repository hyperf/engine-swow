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
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Engine\Coroutine;
use Hyperf\Engine\Http\ServerFactory;
use Hyperf\Engine\Http\Stream;
use Hyperf\Engine\ResponseEmitter;
use Hyperf\HttpMessage\Server\Response;
use Psr\Http\Message\RequestInterface;
use Swow\Psr7\Server\ServerConnection;

require_once __DIR__ . '/../vendor/autoload.php';
Coroutine::set([
    'hook_flags' => SWOOLE_HOOK_ALL,
]);

$callback = function () {
    $logger = Mockery::mock(StdoutLoggerInterface::class);
    $logger->shouldReceive('error', 'critical')->withAnyArgs()->andReturnUsing(static function ($args) {
        echo $args . PHP_EOL;
    });

    $emitter = new ResponseEmitter($logger);
    $server = (new ServerFactory($logger))->make('0.0.0.0', 9505);

    $server->handle(static function (RequestInterface $request, ServerConnection $response) use ($emitter) {
        $body = (string) $request->getBody();
        $ret = 'Hello World.';
        if ($body) {
            $ret = 'Received: ' . $body;
        }
        $emitter->emit((new Response())->withBody(new Stream($ret)), $response);
    });

    $server->start();
};

$callback();
