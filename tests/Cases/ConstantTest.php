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

use Hyperf\Engine\Constant;
use Hyperf\Engine\Http\Server as HttpServer;
use Hyperf\Engine\Server;
use Mockery;
use Psr\Log\LoggerInterface;

/**
 * @internal
 * @coversNothing
 */
class ConstantTest extends AbstractTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testEngine()
    {
        $this->assertSame('Swow', Constant::ENGINE);
    }

    public function testIsCoroutineServer()
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $this->assertTrue(Constant::isCoroutineServer(new HttpServer($logger)));
        $this->assertTrue(Constant::isCoroutineServer(new Server($logger)));
    }
}
