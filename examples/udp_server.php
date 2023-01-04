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
use Swow\Socket\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

$logger = Mockery::mock(LoggerInterface::class);
$server = new Server($logger, Socket::TYPE_UDP);


$server->bind('0.0.0.0',9503)->handle(function (Socket $socket, $data, $clientInfo) {
        try {
            if ($data === 'ping') {
                $socket->sendTo("pong", 0, strlen("pong"),$clientInfo['address'], $clientInfo['port']);            
            } else {
                $socket->sendTo('recv: ' . $data, 0, strlen('recv: ' . $data),$clientInfo['address'], $clientInfo['port']);            
            }
        } catch (Exception $exception) {
            echo (string) $exception;

        } catch (Throwable $exception) {
            echo (string) $exception;

        }
});

$server->start();
