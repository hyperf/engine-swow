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
use Hyperf\Engine\SocketServer;
use Psr\Log\LoggerInterface;
use Swow\Http\Buffer;
use Swow\Socket;

require_once __DIR__ . '/../vendor/autoload.php';

function to_buffer(string $body): Buffer
{
    return Buffer::create($body)->rewind();
}

$logger = Mockery::mock(LoggerInterface::class);
$server = new SocketServer($logger, Socket::TYPE_TCP);

$server->bind('0.0.0.0', 9501)->handle(function (Socket $socket) {
    $socket->recv($buffer = new Buffer());
    var_dump($buffer->rewind()->getContents());
    $socket->write([to_buffer('xxx')]);
});

$server->start();
