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
use Hyperf\Engine\Server;
use Psr\Log\LoggerInterface;
use Swow\Buffer;
use Swow\Socket;
use Swow\SocketException;

require_once __DIR__ . '/../vendor/autoload.php';

$logger = Mockery::mock(LoggerInterface::class);
$server = new Server($logger, Socket::TYPE_TCP);

$server->bind('0.0.0.0', 9502)->handle(function (Socket $socket) {
    while (true) {
        try {
            $ret = $socket->recv($buffer = new Buffer(Buffer::COMMON_SIZE));
            if ($ret === 0) {
                break;
            }
            $body = $buffer->rewind()->getContents();
            if ($body === 'ping') {
                $socket->write([(new Swow\Buffer(0))->write('pong')->rewind()]);
            } else {
                $socket->write([(new Swow\Buffer(0))->write('recv: ' . $body)->rewind()]);
            }
        } catch (SocketException|Throwable $exception) {
            echo (string) $exception;
            break;
        }
    }
});

$server->start();
