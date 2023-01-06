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

use Hyperf\Engine\Http\Client;

/**
 * @internal
 * @coversNothing
 */
class HttpServerTest extends AbstractTestCase
{
    /**
     * @group Server
     */
    public function testHttpServerHelloWorld()
    {
        $client = new Client('127.0.0.1', 9505);
        $response = $client->request('GET', '/');
        $this->assertSame(200, $response->statusCode);
        $this->assertSame('Hello World.', $response->body);
    }

    /**
     * @group Server
     */
    public function testHttpServerReceived()
    {
        $client = new Client('127.0.0.1', 9505);
        $response = $client->request('POST', '/', contents: 'Hyperf');
        $this->assertSame(200, $response->statusCode);
        $this->assertSame('Received: Hyperf', $response->body);
    }
}
