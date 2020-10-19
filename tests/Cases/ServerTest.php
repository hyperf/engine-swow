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
use Swow\Socket;
use function Swow\Http\packRequest;

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
        $socket = new Socket();
        $socket->connect('127.0.0.1', 9501);
        $socket->write([packRequest('GET', '/')]);
        $socket->recv($buffer = new Buffer());
        $this->assertSame("HTTP/1.1 200 OK\r\nServer: Hyperf\r\nContent-Length: 12\r\n\r\nHello World.", $buffer->rewind()->getContents());
    }

    /**
     * @group Server
     */
    public function testTcpServer()
    {
        $socket = new Socket();
        $socket->connect('127.0.0.1', 9502);
        $socket->write([(new Buffer())->write('ping')->rewind()]);
        $socket->recv($buffer = new Buffer());
        $this->assertSame('pong', $buffer->rewind()->getContents());
        usleep(1000);
        $socket->write([(new Buffer())->write('Hello World.')->rewind()]);
        $socket->recv($buffer = new Buffer());
        $this->assertSame('recv: Hello World.', $buffer->rewind()->getContents());
    }
}
