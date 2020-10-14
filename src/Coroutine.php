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

use Hyperf\Engine\Contract\CoroutineInterface;
use Swow\Coroutine as SwowCo;

class Coroutine extends SwowCo implements CoroutineInterface
{
    public function execute(...$data)
    {
        return $this->resume(...$data);
    }

    public static function create(callable $callable, ...$data)
    {
        $coroutine = new static($callable);
        $coroutine->resume(...$data);
        return $coroutine;
    }

    public static function id()
    {
        return static::getCurrent()->getId();
    }
}
