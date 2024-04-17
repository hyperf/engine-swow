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

namespace Cases;

use Hyperf\Engine\Http\Http;
use HyperfTest\Cases\AbstractTestCase;

/**
 * @internal
 * @coversNothing
 */
class HttpTest extends AbstractTestCase
{
    public function testHttpPackRequest()
    {
        $data = Http::packRequest('GET', '/', ['Content-Type' => 'application/json'], 'Hello World');

        $this->assertSame("GET / HTTP/1.1\r\nContent-Type: application/json\r\n\r\nHello World", $data);
    }

    public function testHttpPackResponse()
    {
        $data = Http::packResponse(200, 'OK', ['Content-Type' => 'application/json'], 'Hello World');

        $this->assertSame("HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\nHello World", $data);
    }
}
