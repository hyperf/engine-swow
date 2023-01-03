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

use Hyperf\Engine\Listener\WatchDogListener;
use PHPUnit\Framework\TestCase;
use stdClass;
use Swow\WatchDog;

/**
 * @internal
 * @coversNothing
 */
class WatchDogTest extends TestCase
{
    protected function setUp(): void
    {
        if (WatchDog::isRunning()) {
            WatchDog::stop();
        }
    }

    protected function tearDown(): void
    {
        if (WatchDog::isRunning()) {
            WatchDog::stop();
        }
    }

    public function testWatchDogIsRunning()
    {
        $this->assertFalse(WatchDog::isRunning());
        (new WatchDogListener())->process(new stdClass());
        $this->assertTrue(WatchDog::isRunning());
    }
}
