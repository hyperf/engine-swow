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

use Hyperf\Engine\Channel;
use Hyperf\Engine\Contract\ChannelInterface;
use Hyperf\Engine\Coroutine;

/**
 * @internal
 * @coversNothing
 */
class ChannelTest extends AbstractTestCase
{
    public function testChannelPushAndPop()
    {
        $result = [
            uniqid(),
            uniqid(),
            uniqid(),
        ];
        /** @var ChannelInterface $channel */
        $channel = new Channel(3);
        foreach ($result as $value) {
            $channel->push($value);
        }

        $actual[] = $channel->pop();
        $actual[] = $channel->pop();
        $actual[] = $channel->pop();

        $this->assertSame($result, $actual);
    }

    public function testPushClosedChannel()
    {
        /** @var ChannelInterface $channel */
        $channel = new Channel(10);
        $channel->push(111);
        $channel->close();
        $this->assertTrue($channel->isEmpty());
        $channel->push(123);
        $this->assertTrue($channel->isClosing());
        $this->assertSame(false, $channel->pop());
    }

    public function testChannelInCoroutine()
    {
        $id = uniqid();
        /** @var ChannelInterface $channel */
        $channel = new Channel(1);
        Coroutine::create(function () use ($channel, $id) {
            usleep(10000);
            $channel->push($id);
        });
        $t = microtime(true);
        $this->assertSame($id, $channel->pop());
        $this->assertTrue((microtime(true) - $t) > 0.001);
    }

    public function testChannelClose()
    {
        /** @var ChannelInterface $channel */
        $channel = new Channel();
        $this->assertFalse($channel->isClosing());
        Coroutine::create(function () use ($channel) {
            usleep(1000);
            $channel->close();
        });
        $this->assertFalse($channel->pop());
        $this->assertTrue($channel->isClosing());

        $channel = new Channel(1);
        Coroutine::create(function () use ($channel) {
            $channel->close();
        });
        $this->assertTrue($channel->isClosing());
    }

    public function testChannelIsAvailable()
    {
        /** @var ChannelInterface $channel */
        $channel = new Channel();
        $this->assertTrue($channel->isAvailable());
        Coroutine::create(function () use ($channel) {
            usleep(1000);
            $channel->close();
        });
        $channel->pop();
        $this->assertFalse($channel->isAvailable());
    }

    public function testChannelTimeout()
    {
        /** @var ChannelInterface $channel */
        $channel = new Channel(1);
        $channel->pop(0.001);
        $this->assertTrue($channel->isTimeout());

        $channel->push(true);
        $channel->pop(0.001);
        $this->assertFalse($channel->isTimeout());
    }

    public function testChannelPushTimeout()
    {
        /** @var ChannelInterface $channel */
        $channel = new Channel(1);
        $this->assertSame(true, $channel->push(1, 1));
        $this->assertSame(false, $channel->push(1, 1));
        $this->assertTrue($channel->isTimeout());

        /** @var ChannelInterface $channel */
        $channel = new Channel(1);
        $this->assertSame(true, $channel->push(1, 1.0));
        $this->assertSame(false, $channel->push(1, 1.0));
        $this->assertTrue($channel->isTimeout());
    }

    public function testChannelIsClosing()
    {
        /** @var ChannelInterface $channel */
        $channel = new Channel(1);
        $channel->push(true);
        $this->assertFalse($channel->isClosing());
        $this->assertFalse($channel->isTimeout());
        $this->assertTrue($channel->isAvailable());
        $channel->pop();
        $this->assertFalse($channel->isClosing());
        $this->assertFalse($channel->isTimeout());
        $this->assertTrue($channel->isAvailable());
        $channel->pop(0.001);
        $this->assertFalse($channel->isClosing());
        $this->assertTrue($channel->isTimeout());
        $this->assertTrue($channel->isAvailable());
        $this->assertNull($channel->close());
        $this->assertTrue($channel->isClosing());
        $this->assertFalse($channel->isTimeout());
        $this->assertFalse($channel->isAvailable());
        $channel->pop();
        $this->assertTrue($channel->isClosing());
        $this->assertFalse($channel->isTimeout());
        $this->assertFalse($channel->isAvailable());
        $channel->pop(0.001);
        $this->assertTrue($channel->isClosing());
        $this->assertFalse($channel->isTimeout());
        $this->assertFalse($channel->isAvailable());
    }
}
