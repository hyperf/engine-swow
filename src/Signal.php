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

use Hyperf\Engine\Contract\SignalInterface;
use Swow\Signal as SwowSignal;
use Swow\SignalException;

class Signal implements SignalInterface
{
    public static function wait(int $signo, float $timeout = -1): bool
    {
        try {
            SwowSignal::wait($signo, $timeout * 1000);
        } catch (SignalException $e) {
            return false;
        }
        return true;
    }
}
