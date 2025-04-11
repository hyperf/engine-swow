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

namespace Hyperf\Engine;

use Hyperf\Engine\Contract\BarrierInterface;
use Swow\Sync\WaitReference;

class Barrier implements BarrierInterface
{
    public static function wait(object &$barrier, int $timeout = -1): void
    {
        WaitReference::wait($barrier, $timeout);
    }

    public static function create(): object
    {
        return new WaitReference();
    }
}
