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

use Hyperf\Engine\WebSocket\Frame;
use Hyperf\Engine\WebSocket\Opcode;
use Hyperf\Engine\WebSocket\Response;
use Mockery;
use Swow\Psr7\Client\Client as HttpClient;
use Swow\Psr7\Message\Request;
use Swow\Psr7\Message\WebSocketFrame;
use Swow\Psr7\Psr7;
use Swow\Psr7\Server\ServerConnection;

/**
 * @internal
 * @coversNothing
 */
class WebSocketTest extends AbstractTestCase
{
    /**
     * @group Server
     */
    public function testWebSocket()
    {
        $client = new HttpClient();

        $request = new Request('GET', '/');
        $client->connect('127.0.0.1', 9503)
            ->upgradeToWebSocket($request);

        $message = new WebSocketFrame();
        $message->getPayloadData()->write('Hello World!');
        $reply = $client
            ->sendWebSocketFrame($message)
            ->recvWebSocketFrame();

        $this->assertSame('received: Hello World!', (string) $reply->getPayloadData());

        $message = new WebSocketFrame();
        $message->setOpcode(Opcode::PING);
        $reply = $client->sendWebSocketFrame($message)
            ->recvWebSocketFrame();

        $this->assertSame(Opcode::PONG, $reply->getOpcode());
    }

    public function testFrameToString()
    {
        $frame = Psr7::createWebSocketTextFrame(payloadData: 'Hello World.');

        $this->assertIsString($string = (string) $frame);

        $frame = Frame::from($frame);
        $this->assertSame($string, (string) $frame);
    }

    public function testResponseGetFd()
    {
        $conn = Mockery::mock(ServerConnection::class);
        $conn->shouldReceive('getFd')->andReturn(123);
        $response = new Response($conn);

        $this->assertSame(123, $response->getFd());
    }
}
