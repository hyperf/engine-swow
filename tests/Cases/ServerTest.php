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
namespace HyperfTest\Cases;

use Swow\Buffer;
use Swow\Http\Http;
use Swow\Socket;

/**
 * @internal
 * @coversNothing
 */
class ServerTest extends AbstractTestCase
{
    /**
     * @group Server
     */
    public function testHttpServer()
    {
        $socket = new Socket(Socket::TYPE_TCP);
        $socket->connect('127.0.0.1', 9501);
        $socket->write([Http::packRequest('GET', '/')]);
        $socket->recv($buffer = new Buffer(Buffer::COMMON_SIZE));
        $this->assertSame("HTTP/1.1 200 OK\r\nServer: Hyperf\r\nConnection: Keep-Alive\r\nContent-Length: 12\r\n\r\nHello World.", (string) $buffer);
    }

    /**
     * @group Server
     */
    public function testHttpServerRequestKeepalive()
    {
        $socket = new Socket(Socket::TYPE_TCP);
        $socket->connect('127.0.0.1', 9501);
        $socket->write([Http::packRequest('GET', '/')]);
        $socket->recv($buffer = new Buffer(Buffer::COMMON_SIZE));

        $socket->write([Http::packRequest('GET', '/')]);
        $socket->recv($buffer2 = new Buffer(Buffer::COMMON_SIZE));

        $this->assertEquals((string) $buffer, (string) $buffer2);
    }

    /**
     * @group Server
     */
    public function testTcpServer()
    {
        $socket = new Socket(Socket::TYPE_TCP);
        $socket->connect('127.0.0.1', 9502);
        $socket->send('ping');
        $body = $socket->recvString();
        $this->assertSame('pong', $body);
        usleep(1000);

        $socket->send('Hello World.');
        $body = $socket->recvString();
        $this->assertSame('recv: Hello World.', $body);
    }

    /**
     * @group Server
     */
    public function testUdpServer()
    {
        $socket = new Socket(Socket::TYPE_UDP);
        $socket->connect('127.0.0.1', 9504);
        $socket->send('ping');
        $body = $socket->recvString();
        $this->assertSame('pong', $body);
        usleep(1000);

        $socket->send('Hello World.');
        $body = $socket->recvString();
        $this->assertSame('recv: Hello World.', $body);
    }
}
