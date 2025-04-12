<?php

namespace HyperfTest\Cases;

use Hyperf\Engine\Barrier;
use Hyperf\Engine\Coroutine;

class BarrierTest extends AbstractTestCase
{
    public function testBarrier()
    {
        $barrier = Barrier::create();
        $N = 10;
        $count = 0;
        for ($i = 0; $i < $N; $i++) {
            Coroutine::create(function () use ($barrier, &$count) {
                usleep(2000);
                $count++;
            });
        }
        Barrier::wait($barrier);
        $this->assertSame($N, $count);
    }
}
