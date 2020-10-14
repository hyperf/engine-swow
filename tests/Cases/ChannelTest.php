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

    public function testChannelInCoroutine()
    {
        $id = uniqid();
        /** @var ChannelInterface $channel */
        $channel = new Channel(1);
        Coroutine::create(function () use ($channel, $id) {
            usleep(2000);
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
        Coroutine::create(function () use ($channel) {
            usleep(1000);
            $channel->close();
        });
        $this->assertFalse($channel->pop());
        $this->assertTrue($channel->isClosing());
    }

    public function testChannelIsAvailable()
    {
        /** @var ChannelInterface $channel */
        $channel = new Channel();
        $this->assertTrue($channel->isAvailable());
        $channel->close();
        $channel->pop();
        $this->assertFalse($channel->isAvailable());
    }
}
