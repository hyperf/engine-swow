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
use Hyperf\Engine\Contract\CoroutineInterface;
use Hyperf\Engine\Coroutine;
use Hyperf\Engine\Exception\CoroutineDestroyedException;

/**
 * @internal
 * @coversNothing
 */
class CoroutineTest extends AbstractTestCase
{
    public function testCoroutineCreate()
    {
        $coroutine = new Coroutine(function () {
            $this->assertTrue(true);
        });

        $coroutine->execute();

        $this->assertInstanceOf(CoroutineInterface::class, $coroutine);
        $this->assertIsInt($coroutine->getId());

        $coroutine = Coroutine::create(function () {
            $this->assertTrue(true);
        });

        $this->assertInstanceOf(CoroutineInterface::class, $coroutine);
        $this->assertIsInt($coroutine->getId());
    }

    public function testCoroutineContext()
    {
        $coroutine = new Coroutine(function () {
        });
        $this->assertInstanceOf(\ArrayObject::class, $coroutine->getContext());
        $coroutine->getContext()['name'] = 'Hyperf';
        $this->assertSame('Hyperf', $coroutine->getContext()['name']);

        $id = uniqid();
        $coroutine = Coroutine::create(function () use ($id) {
            $this->assertInstanceOf(\ArrayObject::class, Coroutine::getContextFor());
            $this->assertFalse(isset(Coroutine::getContextFor()['name']));
            $this->assertSame(null, Coroutine::getContextFor()['name'] ?? null);
            Coroutine::getContextFor()['name'] = $id;
            $this->assertSame($id, Coroutine::getContextFor()['name']);
            usleep(1000);
        });

        $this->assertSame($id, Coroutine::getContextFor($coroutine->getId())['name']);

        usleep(1000);
        $this->assertNull(Coroutine::getContextFor($coroutine->getId()));
    }

    public function testCoroutinePid()
    {
        $pid = Coroutine::id();
        Coroutine::create(function () use ($pid) {
            $this->assertSame($pid, Coroutine::pid());
            $pid = Coroutine::id();
            $co = Coroutine::create(function () use ($pid) {
                $this->assertSame($pid, Coroutine::pid(Coroutine::id()));
                usleep(1000);
            });
            Coroutine::create(function () use ($pid) {
                $this->assertSame($pid, Coroutine::pid());
            });
            $this->assertSame($pid, Coroutine::pid($co->getId()));
        });
    }

    public function testCoroutinePidHasBeenDestroyed()
    {
        $co = Coroutine::create(function () {
        });

        try {
            Coroutine::pid($co->getId());
            $this->assertTrue(false);
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(CoroutineDestroyedException::class, $exception);
        }
    }

    public function testCoroutineInTopCoroutine()
    {
        $this->assertSame(0, Coroutine::pid());
    }

    public function testCoroutineDefer()
    {
        $channel = new Channel(2);
        Coroutine::create(function () use ($channel) {
            Coroutine::defer(function () use ($channel) {
                $channel->push(2);
            });

            $channel->push(1);
        });

        $this->assertSame(1, $channel->pop());
        $this->assertSame(2, $channel->pop());
    }

    public function testTheOrderForCoroutineDefer()
    {
        $channel = new Channel(3);
        Coroutine::create(function () use ($channel) {
            Coroutine::defer(function () use ($channel) {
                $channel->push(2);
            });
            Coroutine::defer(function () use ($channel) {
                $channel->push(3);
            });

            $channel->push(1);
        });

        $this->assertSame(1, $channel->pop());
        $this->assertSame(3, $channel->pop());
        $this->assertSame(2, $channel->pop());
    }
}
