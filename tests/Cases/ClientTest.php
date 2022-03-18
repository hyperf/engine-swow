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
use Swow\Errno;
use Swow\SocketException;

/**
 * @internal
 * @coversNothing
 */
class ClientTest extends AbstractTestCase
{
    /**
     * @group Server
     */
    public function testClientRequest()
    {
        $client = new Client('127.0.0.1', 9501);
        $response = $client->request('GET', '/');
        $this->assertSame(200, $response->statusCode);
        $this->assertSame(['Hyperf'], $response->headers['Server']);
        $this->assertSame('Hello World.', $response->body);
    }

    /**
     * @group Server
     */
    public function testClientJsonRequest()
    {
        $client = new Client('127.0.0.1', 9501);
        $response = $client->request(
            'POST',
            '/',
            ['Content-Type' => 'application/json charset=UTF-8'],
            json_encode(['name' => 'Hyperf'], JSON_UNESCAPED_UNICODE)
        );
        $this->assertSame(200, $response->statusCode);
        $this->assertSame(['Hyperf'], $response->headers['Server']);
        $this->assertSame('Hello World.', $response->body);
    }

    /**
     * @group Server
     */
    public function testClientCookies()
    {
        $client = new Client('127.0.0.1', 9501);
        $response = $client->request('GET', '/cookies');
        $this->assertSame(200, $response->statusCode);
        $this->assertSame(['Hyperf'], $response->headers['Server']);
        $this->assertSame([
            'X-Server-Id=' . $response->body,
            'X-Server-Name=Hyperf',
        ], $response->headers['Set-Cookie']);
    }

    public function testClientSocketConnectionRefused()
    {
        try {
            $client = new Client('127.0.0.1', 9601);
            $client->request('GET', '/');
            $this->assertTrue(false);
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(SocketException::class, $exception);
            $this->assertSame(Errno::ECONNREFUSED, $exception->getCode());
            $this->assertSame('Connection refused', $exception->getMessage());
        }
    }

    /**
     * @group Server
     */
    public function testClientSocketConnectionTimeout()
    {
        try {
            $client = new Client('127.0.0.1', 9501);
            $client->set(['timeout' => 0.1]);
            $client->request('GET', '/timeout?time=1');
            $this->assertTrue(false);
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(Exception::class, $exception);
            $this->assertSame(Errno\ETIMEDOUT, $exception->getCode());
            $this->assertStringContainsString('Timed out', $exception->getMessage());
        }
    }

    /**
     * @group Server
     */
    public function testClientNotFound()
    {
        $client = new Client('127.0.0.1', 9501);
        $response = $client->request('GET', '/not_found');
        $this->assertSame(404, $response->statusCode);
    }

    /**
     * @group Server
     */
    public function testClientResponseWithoutContentLength()
    {
        $this->markTestSkipped('Parse failed when the response does not has content-length.');
        $client = new Client('127.0.0.1', 9501);
        $response = $client->request('GET', '/without-content-length');
        $this->assertSame(400, $response->statusCode);
    }
}
