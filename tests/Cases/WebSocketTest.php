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

use Hyperf\Engine\WebSocket\Opcode;
use Swow\Http\Client as HttpClient;
use Swow\Http\Request;
use Swow\Http\WebSocketFrame;

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
}
